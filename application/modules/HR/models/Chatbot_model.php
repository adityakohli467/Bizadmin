<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Chatbot_model
 * -------------------------------------------------------------------------
 * Natural-language query engine for the HR admin dashboard.
 *
 * Pipeline:
 *   1. A free LLM (Groq by default) reads the question and returns a small
 *      JSON "query spec" (intent + date range + threshold + employee +
 *      aggregation). The LLM only *plans* — it never writes SQL.
 *   2. PHP validates the spec and runs deterministic, parameterised,
 *      location-scoped queries via the CI query builder.
 *   3. If the LLM is unavailable (no key / network / bad output), it falls
 *      back to a built-in rule-based parser so the assistant keeps working.
 *
 * This keeps answers accurate and safe (no LLM-generated SQL touches the DB)
 * while still understanding free-form natural language.
 */
class Chatbot_model extends CI_Model {

    /** @var int|null current location scope */
    private $location_id;

    /** @var array llm config */
    private $llm = [];

    public function __construct()
    {
        parent::__construct();
        $this->location_id = $this->session->userdata('location_id')
            ? $this->session->userdata('location_id')
            : ($this->session->userdata('User_location_ids')
                ? $this->session->userdata('User_location_ids')[0]
                : null);

        // Load LLM configuration (safe if the file is missing).
        @$this->config->load('chatbot', TRUE, TRUE);
        $cfg = (array) $this->config->item('chatbot');
        $this->llm = [
            'provider' => isset($cfg['chatbot_llm_provider']) ? $cfg['chatbot_llm_provider'] : 'groq',
            'api_key'  => getenv('GROQ_API_KEY') ?: (isset($cfg['chatbot_groq_api_key']) ? $cfg['chatbot_groq_api_key'] : ''),
            'model'    => isset($cfg['chatbot_groq_model']) ? $cfg['chatbot_groq_model'] : 'llama-3.3-70b-versatile',
            'endpoint' => isset($cfg['chatbot_groq_endpoint']) ? $cfg['chatbot_groq_endpoint'] : 'https://api.groq.com/openai/v1/chat/completions',
            'timeout'  => (int) (isset($cfg['chatbot_llm_timeout']) ? $cfg['chatbot_llm_timeout'] : 20),
        ];
    }

    /* =====================================================================
     *  PUBLIC ENTRY POINT
     * ===================================================================== */

    /**
     * Answer a natural-language question.
     * Tries the LLM planner first; falls back to rule-based parsing.
     *
     * @param  string $question
     * @return array  ['answer','columns','rows','intent','range','engine']
     */
    public function answer($question)
    {
        $qRaw = trim((string) $question);
        if ($qRaw === '') {
            return $this->reply('Please type a question, e.g. "Who worked more than 45 hours last week?"', 'empty');
        }

        // 0) STRICT SAFETY GUARD — this assistant is read-only.
        // Refuse anything that looks like a request to modify, delete, or
        // execute data/commands. The engine never runs write SQL, but we
        // reject such requests up front so they are never processed at all.
        if ($this->isHazardous($qRaw)) {
            return $this->reply(
                "I can only answer read-only questions about your team's data (hours, leave, shifts, attendance, headcount, birthdays). "
                . "I can't create, edit, update, delete, or run any changes. Please rephrase as a question, e.g. \"Who worked more than 45 hours last week?\"",
                'blocked'
            );
        }

        // 1) Try the LLM planner.
        $spec = $this->planWithLlm($qRaw);
        if (is_array($spec) && !empty($spec['intent']) && $spec['intent'] !== 'unknown') {
            $result = $this->answerFromSpec($spec);
            if ($result !== null) {
                $result['engine'] = 'llm';
                return $result;
            }
        }

        // 2) Fall back to rule-based parsing.
        $result = $this->answerRuleBased(strtolower($qRaw));
        $result['engine'] = 'rules';
        return $result;
    }

    /* =====================================================================
     *  LLM PLANNER (Groq / OpenAI-compatible)
     * ===================================================================== */

    /**
     * Ask the LLM to convert the question into a structured query spec.
     * Returns an associative array, or null on any failure.
     */
    private function planWithLlm($question)
    {
        if (empty($this->llm['api_key'])) {
            return null; // no key configured -> use rule-based fallback
        }

        $system = $this->plannerPrompt();
        $body = [
            'model' => $this->llm['model'],
            'temperature' => 0,
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user',   'content' => $question],
            ],
        ];

        $raw = $this->httpPostJson($this->llm['endpoint'], $body, $this->llm['api_key']);
        if ($raw === null) { return null; }

        $decoded = json_decode($raw, true);
        $content = isset($decoded['choices'][0]['message']['content'])
            ? $decoded['choices'][0]['message']['content'] : null;
        if (!$content) { return null; }

        // Strip any accidental code fences and parse the JSON spec.
        $content = trim(preg_replace('/^```(?:json)?|```$/m', '', $content));
        $spec = json_decode($content, true);
        return is_array($spec) ? $spec : null;
    }

    /** The system prompt describing the JSON spec the LLM must produce. */
    private function plannerPrompt()
    {
        $today = date('Y-m-d');
        $dayName = date('l');
        return <<<PROMPT
You are a query planner for an HR staff-management dashboard. Convert the user's
question into a single JSON object describing what data to fetch. Respond with
JSON ONLY — no prose, no markdown.

Today's date is {$today} ({$dayName}). Weeks run Monday to Sunday.

JSON fields:
- "intent" (required): one of
    "hours"       (hours worked / overtime / total hours),
    "leave"       (who/how many on leave, time off, holiday, sick),
    "attendance"  (present/absent/clocked-in today),
    "headcount"   (number of employees / staff count),
    "shifts"      (who is working / rostered / scheduled shifts),
    "birthday"    (employee birthdays),
    "on_break"    (employees currently on break),
    "unknown"     (anything else).
- "date_range": object { "type", "n", "start", "end" }
    "type" one of: "today","yesterday","tomorrow","this_week","last_week",
      "next_week","this_month","last_month","next_month","this_year",
      "last_n_days","last_n_weeks","last_n_months","next_n_days","next_n_weeks","custom".
    "n": integer, only for the *_n_* types (e.g. "last 2 weeks" => type "last_n_weeks", n 2).
    "start","end": "YYYY-MM-DD", only for type "custom".
- "threshold": object { "op", "hours" }. "op" one of ">",">=","<","<=","=".
    "more than X hours" => ">"; "at least" => ">="; "less than"/"under"/"below" => "<";
    "at most"/"no more than" => "<="; "exactly" => "=". Omit if not asked.
- "employee": full name string, only if the question targets one specific person. Omit otherwise.
- "aggregation": one of "list","count","total".
    "how many" => "count"; "total"/"sum"/"combined"/"how much" => "total";
    "who"/"which"/"list"/"show" => "list". Default "list".
- "attendance_type": "present" or "absent" (only for intent "attendance"; default "present").

If the question does not map to any intent, return {"intent":"unknown"}.

Examples:
Q: Who worked more than 45 hours last week?
{"intent":"hours","date_range":{"type":"last_week"},"threshold":{"op":">","hours":45},"aggregation":"list"}
Q: how many employees are on leave next week
{"intent":"leave","date_range":{"type":"next_week"},"aggregation":"count"}
Q: total hours worked by all employees in the last 2 weeks
{"intent":"hours","date_range":{"type":"last_n_weeks","n":2},"aggregation":"total"}
Q: who is present today
{"intent":"attendance","date_range":{"type":"today"},"attendance_type":"present"}
Q: how many staff do we have
{"intent":"headcount"}
Q: whose birthday is this month
{"intent":"birthday","date_range":{"type":"this_month"}}
Q: how many hours did John Smith work this month
{"intent":"hours","date_range":{"type":"this_month"},"employee":"John Smith","aggregation":"total"}
PROMPT;
    }

    /** Minimal cURL JSON POST. Returns the raw response body or null on failure. */
    private function httpPostJson($url, array $body, $apiKey)
    {
        if (!function_exists('curl_init')) {
            log_message('error', 'Chatbot: cURL not available.');
            return null;
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->llm['timeout'],
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS     => json_encode($body),
        ]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($resp === false || $code < 200 || $code >= 300) {
            log_message('error', 'Chatbot LLM call failed (HTTP ' . $code . '): ' . ($err ?: substr((string) $resp, 0, 300)));
            return null;
        }
        return $resp;
    }

    /* =====================================================================
     *  SPEC DISPATCH
     * ===================================================================== */

    /**
     * Execute a validated query spec. Returns the reply array, or null if the
     * spec cannot be handled (so the caller can fall back to rules).
     */
    private function answerFromSpec(array $spec)
    {
        $intent = isset($spec['intent']) ? $spec['intent'] : 'unknown';
        $range  = $this->resolveRange(isset($spec['date_range']) ? $spec['date_range'] : null);
        $agg    = (isset($spec['aggregation']) && in_array($spec['aggregation'], ['list', 'count', 'total'], true))
            ? $spec['aggregation'] : null;

        // Threshold.
        $threshold = null;
        if (!empty($spec['threshold']) && isset($spec['threshold']['op'], $spec['threshold']['hours'])
            && in_array($spec['threshold']['op'], ['>', '>=', '<', '<=', '='], true)) {
            $threshold = ['op' => $spec['threshold']['op'], 'hours' => (float) $spec['threshold']['hours']];
        }

        // Employee (resolve name -> record).
        $employee = null;
        if (!empty($spec['employee']) && is_string($spec['employee'])) {
            $employee = $this->findEmployeeByName($spec['employee']);
        }

        switch ($intent) {
            case 'hours':
                return $this->answerHours($range, $threshold, $employee, $agg);
            case 'leave':
                return $this->answerLeave($range, $agg);
            case 'attendance':
                $type = ((isset($spec['attendance_type']) ? $spec['attendance_type'] : 'present') === 'absent') ? 'absent' : 'present';
                return $this->answerAttendanceToday($type);
            case 'headcount':
                return $this->answerHeadcount();
            case 'shifts':
                return $this->answerShifts($range, $employee, $agg);
            case 'birthday':
                return $this->answerBirthdays($range);
            case 'on_break':
                return $this->answerOnBreak();
        }
        return null;
    }

    /** Resolve a structured date_range spec into ['start','end','label']. */
    private function resolveRange($dr)
    {
        $type = is_array($dr) && isset($dr['type']) ? $dr['type'] : '';
        $n    = is_array($dr) && isset($dr['n']) ? max(1, (int) $dr['n']) : 1;

        switch ($type) {
            case 'today':
                $d = new DateTime(); return $this->range($d, $d, 'today');
            case 'yesterday':
                $d = (new DateTime())->modify('-1 day'); return $this->range($d, $d, 'yesterday');
            case 'tomorrow':
                $d = (new DateTime())->modify('+1 day'); return $this->range($d, $d, 'tomorrow');
            case 'this_week':
                $s = new DateTime('monday this week'); return $this->range($s, (clone $s)->modify('+6 days'), 'this week');
            case 'last_week':
                $s = new DateTime('monday last week'); return $this->range($s, (clone $s)->modify('+6 days'), 'last week');
            case 'next_week':
                $s = new DateTime('monday next week'); return $this->range($s, (clone $s)->modify('+6 days'), 'next week');
            case 'this_month':
                return $this->range(new DateTime('first day of this month'), new DateTime('last day of this month'), 'this month');
            case 'last_month':
                return $this->range(new DateTime('first day of last month'), new DateTime('last day of last month'), 'last month');
            case 'next_month':
                return $this->range(new DateTime('first day of next month'), new DateTime('last day of next month'), 'next month');
            case 'this_year':
                return $this->range(new DateTime(date('Y') . '-01-01'), new DateTime(date('Y') . '-12-31'), 'this year');
            case 'last_n_days':
                $s = (new DateTime())->modify('-' . ($n - 1) . ' day');
                return $this->range($s, new DateTime(), "in the last {$n} days");
            case 'last_n_weeks':
                $mon = new DateTime('monday this week');
                return $this->range((clone $mon)->modify('-' . $n . ' week'), (clone $mon)->modify('-1 day'), "in the last {$n} weeks");
            case 'last_n_months':
                $first = new DateTime('first day of this month');
                return $this->range((clone $first)->modify('-' . $n . ' month'), (clone $first)->modify('-1 day'), "in the last {$n} months");
            case 'next_n_days':
                return $this->range((new DateTime())->modify('+1 day'), (new DateTime())->modify('+' . $n . ' day'), "in the next {$n} days");
            case 'next_n_weeks':
                $s = new DateTime('monday next week');
                return $this->range($s, (clone $s)->modify('+' . ($n * 7 - 1) . ' day'), "in the next {$n} weeks");
            case 'custom':
                try {
                    $s = new DateTime($dr['start']); $e = new DateTime($dr['end']);
                    return $this->range($s, $e, $s->format('d M Y') . ' – ' . $e->format('d M Y'));
                } catch (Throwable $ex) { /* fall through to default */ }
        }
        // Default: current week.
        $s = new DateTime('monday this week');
        return $this->range($s, (clone $s)->modify('+6 days'), 'this week');
    }

    /* =====================================================================
     *  INTENT HANDLERS  (shared by LLM + rule-based paths)
     * ===================================================================== */

    /**
     * Hours worked.
     * @param array       $range
     * @param array|null  $threshold ['op','hours']
     * @param array|null  $employee  ['emp_id','name']
     * @param string|null $agg       'list'|'count'|'total'
     */
    private function answerHours($range, $threshold, $employee, $agg)
    {
        $isFuture   = ($range['start'] > date('Y-m-d'));
        $basisLabel = $isFuture ? 'rostered (scheduled)' : 'clocked';

        $rows = $isFuture
            ? $this->scheduledHoursByEmployee($range['start'], $range['end'], $employee)
            : $this->workedHoursByEmployee($range['start'], $range['end'], $employee);

        // --- Single employee ---------------------------------------------
        if ($employee) {
            $secs = 0;
            foreach ($rows as $r) { $secs += $r['seconds']; }
            $ans = sprintf('%s worked %s %s (%s).',
                $employee['name'], $this->fmtHours($secs), $range['label'], $basisLabel);
            return $this->reply($ans, 'hours_employee', [], [], $range['label']);
        }

        // --- Total only ---------------------------------------------------
        if ($agg === 'total' && !$threshold) {
            $secs = 0; $emps = [];
            foreach ($rows as $r) { $secs += $r['seconds']; $emps[$r['employee_id']] = true; }
            $ans = sprintf('The team worked a total of %s %s across %d employee%s (%s).',
                $this->fmtHours($secs), $range['label'], count($emps),
                count($emps) === 1 ? '' : 's', $basisLabel);
            return $this->reply($ans, 'hours_total', [], [], $range['label']);
        }

        // --- Per-employee aggregation ------------------------------------
        $byEmp = [];
        foreach ($rows as $r) {
            $id = $r['employee_id'];
            if (!isset($byEmp[$id])) { $byEmp[$id] = ['name' => $r['name'], 'seconds' => 0]; }
            $byEmp[$id]['seconds'] += $r['seconds'];
        }

        if ($threshold) {
            $limitSecs = $threshold['hours'] * 3600;
            $op = $threshold['op'];
            $byEmp = array_filter($byEmp, function ($e) use ($op, $limitSecs, $threshold) {
                switch ($op) {
                    case '>':  return $e['seconds'] >  $limitSecs;
                    case '>=': return $e['seconds'] >= $limitSecs;
                    case '<':  return $e['seconds'] <  $limitSecs;
                    case '<=': return $e['seconds'] <= $limitSecs;
                    case '=':  return round($e['seconds'] / 3600, 2) == $threshold['hours'];
                }
                return true;
            });
        }

        uasort($byEmp, function ($a, $b) { return $b['seconds'] <=> $a['seconds']; });

        $tableRows = []; $grandSecs = 0;
        foreach ($byEmp as $e) { $tableRows[] = [$e['name'], $this->fmtHours($e['seconds'])]; $grandSecs += $e['seconds']; }

        if ($threshold) {
            $opWord = $this->opWord($threshold['op']);
            $ans = empty($tableRows)
                ? sprintf('No employees worked %s %g hours %s (%s).', $opWord, $threshold['hours'], $range['label'], $basisLabel)
                : sprintf('%d employee%s worked %s %g hours %s (%s):', count($tableRows), count($tableRows) === 1 ? '' : 's', $opWord, $threshold['hours'], $range['label'], $basisLabel);
        } else {
            $ans = empty($tableRows)
                ? sprintf('No %s hours found %s.', $basisLabel, $range['label'])
                : sprintf('Hours worked %s (%s) — total %s across %d employee%s:', $range['label'], $basisLabel, $this->fmtHours($grandSecs), count($tableRows), count($tableRows) === 1 ? '' : 's');
        }

        return $this->reply($ans, 'hours_list', ['Employee', 'Hours'], $tableRows, $range['label']);
    }

    /** Leave in a range. @param string|null $agg */
    private function answerLeave($range, $agg)
    {
        $leaves = $this->leavesInRange($range['start'], $range['end']);

        if (empty($leaves)) {
            return $this->reply(sprintf('No employees are on approved leave %s.', $range['label']), 'leave', [], [], $range['label']);
        }

        $empSet = []; $tableRows = [];
        foreach ($leaves as $l) {
            $empSet[$l['emp_id']] = true;
            $days = (int) (floor((strtotime($l['end_date']) - strtotime($l['start_date'])) / 86400) + 1);
            $tableRows[] = [
                trim($l['first_name'] . ' ' . $l['last_name']),
                $l['leaveTypeName'] ?: 'Leave',
                date('d M Y', strtotime($l['start_date'])) . ' – ' . date('d M Y', strtotime($l['end_date'])),
                $days . ' day' . ($days === 1 ? '' : 's'),
            ];
        }

        $count = count($empSet);
        if ($agg === 'count') {
            $ans = sprintf('%d employee%s %s on approved leave %s.',
                $count, $count === 1 ? '' : 's', $count === 1 ? 'is' : 'are', $range['label']);
        } else {
            $ans = sprintf('%d employee%s on approved leave %s:',
                $count, $count === 1 ? '' : 's', $range['label']);
        }
        return $this->reply($ans, 'leave', ['Employee', 'Type', 'Dates', 'Days'], $tableRows, $range['label']);
    }

    /** Present / absent today. @param string $type 'present'|'absent' */
    private function answerAttendanceToday($type)
    {
        $today = date('Y-m-d');
        $rows  = $this->attendanceForDate($today);

        $present = []; $absent = [];
        foreach ($rows as $r) {
            $name = trim($r['first_name'] . ' ' . $r['last_name']);
            if (!empty($r['clock_in_time'])) {
                $present[$r['employee_id']] = [$name, date('h:i A', strtotime($r['clock_in_time']))];
            } else {
                $absent[$r['employee_id']] = [$name, '—'];
            }
        }
        foreach (array_keys($present) as $id) { unset($absent[$id]); }

        if ($type === 'absent') {
            $tableRows = array_map(function ($r) { return [$r[0]]; }, array_values($absent));
            $ans = empty($tableRows)
                ? 'Everyone scheduled today has clocked in.'
                : sprintf('%d employee%s scheduled today %s not clocked in:', count($absent), count($absent) === 1 ? '' : 's', count($absent) === 1 ? 'is' : 'are');
            return $this->reply($ans, 'attendance_absent', ['Employee'], $tableRows, 'today');
        }

        $tableRows = array_values($present);
        $ans = empty($tableRows)
            ? 'No employees have clocked in today yet.'
            : sprintf('%d employee%s present today (clocked in):', count($present), count($present) === 1 ? '' : 's');
        return $this->reply($ans, 'attendance_present', ['Employee', 'Clock In'], $tableRows, 'today');
    }

    /** Headcount of active employees at this location. */
    private function answerHeadcount()
    {
        $count = $this->tenantDb
            ->distinct()->select('e.emp_id')
            ->from('HR_employee e')
            ->join('HR_empIdToLocationId el', 'e.emp_id = el.empId', 'left')
            ->where('el.location_id', $this->location_id)
            ->where('e.is_deleted', 0)
            ->count_all_results();

        $ans = sprintf('There %s %d active employee%s at this location.',
            $count === 1 ? 'is' : 'are', $count, $count === 1 ? '' : 's');
        return $this->reply($ans, 'headcount');
    }

    /** Shifts in a range. @param array|null $employee @param string|null $agg */
    private function answerShifts($range, $employee, $agg)
    {
        $shifts = $this->shiftsInRange($range['start'], $range['end'], $employee);

        if (empty($shifts)) {
            return $this->reply(sprintf('No shifts are scheduled %s.', $range['label']), 'shifts', [], [], $range['label']);
        }

        if ($agg === 'count') {
            $emps = [];
            foreach ($shifts as $s) { $emps[$s['employee_id']] = true; }
            $ans = sprintf('%d employee%s %s scheduled to work %s (%d shift%s total).',
                count($emps), count($emps) === 1 ? '' : 's', count($emps) === 1 ? 'is' : 'are',
                $range['label'], count($shifts), count($shifts) === 1 ? '' : 's');
            return $this->reply($ans, 'shifts_count', [], [], $range['label']);
        }

        $tableRows = [];
        foreach ($shifts as $s) {
            $tableRows[] = [
                trim($s['first_name'] . ' ' . $s['last_name']),
                date('D d M', strtotime($s['roster_date'])),
                ($s['shift_start_time'] ? date('h:i A', strtotime($s['shift_start_time'])) : '—')
                    . ' – ' .
                ($s['shift_end_time'] ? date('h:i A', strtotime($s['shift_end_time'])) : '—'),
                $s['prep_name'] ?: '—',
            ];
        }
        $ans = sprintf('%d shift%s scheduled %s:', count($shifts), count($shifts) === 1 ? '' : 's', $range['label']);
        return $this->reply($ans, 'shifts', ['Employee', 'Date', 'Shift', 'Area'], $tableRows, $range['label']);
    }

    /** Birthdays in a range. */
    private function answerBirthdays($range)
    {
        $rows = $this->birthdaysInRange($range['start'], $range['end']);
        if (empty($rows)) {
            return $this->reply(sprintf('No employee birthdays %s.', $range['label']), 'birthday', [], [], $range['label']);
        }
        $tableRows = [];
        foreach ($rows as $r) {
            $tableRows[] = [trim($r['first_name'] . ' ' . $r['last_name']), $r['dob'] ? date('d M', strtotime($r['dob'])) : '—'];
        }
        $ans = sprintf('%d birthday%s %s:', count($rows), count($rows) === 1 ? '' : 's', $range['label']);
        return $this->reply($ans, 'birthday', ['Employee', 'Birthday'], $tableRows, $range['label']);
    }

    /** Employees currently on an open break. */
    private function answerOnBreak()
    {
        $today = date('Y-m-d');
        $rows = $this->tenantDb
            ->select('e.first_name, e.last_name, tb.break_start_time')
            ->from('HR_timesheet_breaks tb')
            ->join('HR_timesheet_details ht', 'ht.timesheet_id = tb.timesheet_id', 'left')
            ->join('HR_employee e', 'e.emp_id = ht.employee_id', 'left')
            ->where('ht.roster_date', $today)
            ->where('ht.location_id', $this->location_id)
            ->where('tb.break_start_time IS NOT NULL', null, false)
            ->where("tb.break_start_time !=", '')
            ->group_start()
                ->where('tb.break_end_time IS NULL', null, false)
                ->or_where('tb.break_end_time', '')
            ->group_end()
            ->get()->result_array();

        if (empty($rows)) {
            return $this->reply('No employees are currently on break.', 'on_break', [], [], 'now');
        }
        $tableRows = [];
        foreach ($rows as $r) {
            $tableRows[] = [trim($r['first_name'] . ' ' . $r['last_name']), $r['break_start_time'] ? date('h:i A', strtotime($r['break_start_time'])) : '—'];
        }
        $ans = sprintf('%d employee%s currently on break:', count($rows), count($rows) === 1 ? '' : 's');
        return $this->reply($ans, 'on_break', ['Employee', 'Break Started'], $tableRows, 'now');
    }

    /* =====================================================================
     *  DATA QUERIES  (all scoped to $this->location_id)
     * ===================================================================== */

    /** Actual worked hours from timesheet clock data. Rows: [employee_id,name,seconds]. */
    private function workedHoursByEmployee($start, $end, $employee = null)
    {
        $this->tenantDb
            ->select('td.employee_id, e.first_name, e.last_name, td.clock_in_time, td.clock_out_time,
                      td.actual_break_duration, td.roster_break_duration')
            ->from('HR_timesheet_details td')
            ->join('HR_employee e', 'e.emp_id = td.employee_id', 'left')
            ->where('td.location_id', $this->location_id)
            ->where('td.is_deleted', 0)
            ->where('td.roster_date >=', $start)
            ->where('td.roster_date <=', $end)
            ->where('td.clock_in_time IS NOT NULL', null, false)
            ->where("td.clock_in_time !=", '');
        if ($employee) { $this->tenantDb->where('td.employee_id', $employee['emp_id']); }
        $raw = $this->tenantDb->get()->result_array();

        $out = [];
        foreach ($raw as $r) {
            if (empty($r['clock_in_time']) || empty($r['clock_out_time'])) { continue; }
            $in  = strtotime($r['clock_in_time']);
            $outT = strtotime($r['clock_out_time']);
            if ($outT <= $in) { $outT += 86400; } // overnight
            $worked = max(0, $outT - $in);
            $breakMin = !empty($r['actual_break_duration']) ? (int) $r['actual_break_duration']
                       : (!empty($r['roster_break_duration']) ? (int) $r['roster_break_duration'] : 0);
            $worked = max(0, $worked - ($breakMin * 60));
            $out[] = ['employee_id' => $r['employee_id'], 'name' => trim($r['first_name'] . ' ' . $r['last_name']), 'seconds' => $worked];
        }
        return $out;
    }

    /** Scheduled (rostered) hours for future periods. Rows: [employee_id,name,seconds]. */
    private function scheduledHoursByEmployee($start, $end, $employee = null)
    {
        $this->tenantDb
            ->select('rd.employee_id, e.first_name, e.last_name, rd.shift_start_time, rd.shift_end_time, rd.break_duration')
            ->from('HR_roster_details rd')
            ->join('HR_roster r', 'r.roster_id = rd.roster_id', 'inner')
            ->join('HR_employee e', 'e.emp_id = rd.employee_id', 'left')
            ->where('r.location_id', $this->location_id)
            ->where('rd.is_deleted', 0)
            ->where('r.is_deleted', 0)
            ->where('rd.roster_date >=', $start)
            ->where('rd.roster_date <=', $end);
        if ($employee) { $this->tenantDb->where('rd.employee_id', $employee['emp_id']); }
        $raw = $this->tenantDb->get()->result_array();

        $out = [];
        foreach ($raw as $r) {
            if (empty($r['shift_start_time']) || empty($r['shift_end_time'])) { continue; }
            $in  = strtotime($r['shift_start_time']);
            $outT = strtotime($r['shift_end_time']);
            if ($outT <= $in) { $outT += 86400; }
            $worked = max(0, $outT - $in);
            $breakMin = !empty($r['break_duration']) ? (int) $r['break_duration'] : 0;
            $worked = max(0, $worked - ($breakMin * 60));
            $out[] = ['employee_id' => $r['employee_id'], 'name' => trim($r['first_name'] . ' ' . $r['last_name']), 'seconds' => $worked];
        }
        return $out;
    }

    /** Approved leaves overlapping [start,end]. */
    private function leavesInRange($start, $end)
    {
        return $this->tenantDb
            ->select('lm.emp_id, lm.start_date, lm.end_date, e.first_name, e.last_name, l.leaveTypeName')
            ->from('HR_leave_management lm')
            ->join('HR_employee e', 'e.emp_id = lm.emp_id', 'left')
            ->join('HR_leaves l', 'l.id = lm.leave_type', 'left')
            ->where('lm.location_id', $this->location_id)
            ->where('lm.leave_status', 2) // approved
            ->where('lm.start_date <=', $end)
            ->where('lm.end_date >=', $start)
            ->order_by('lm.start_date', 'ASC')
            ->get()->result_array();
    }

    /** All scheduled timesheet rows for a date. */
    private function attendanceForDate($date)
    {
        return $this->tenantDb
            ->select('td.employee_id, td.clock_in_time, e.first_name, e.last_name')
            ->from('HR_timesheet_details td')
            ->join('HR_employee e', 'e.emp_id = td.employee_id', 'left')
            ->where('td.location_id', $this->location_id)
            ->where('td.is_deleted', 0)
            ->where('td.roster_date', $date)
            ->get()->result_array();
    }

    /** Shifts (roster) within a range. */
    private function shiftsInRange($start, $end, $employee = null)
    {
        $this->tenantDb
            ->select('rd.employee_id, rd.roster_date, rd.shift_start_time, rd.shift_end_time,
                      e.first_name, e.last_name, p.prep_name')
            ->from('HR_roster_details rd')
            ->join('HR_roster r', 'r.roster_id = rd.roster_id', 'inner')
            ->join('HR_employee e', 'e.emp_id = rd.employee_id', 'left')
            ->join('HR_prepArea p', 'p.id = rd.prep_area_id', 'left')
            ->where('r.location_id', $this->location_id)
            ->where('rd.is_deleted', 0)
            ->where('r.is_deleted', 0)
            ->where('rd.roster_date >=', $start)
            ->where('rd.roster_date <=', $end);
        if ($employee) { $this->tenantDb->where('rd.employee_id', $employee['emp_id']); }
        return $this->tenantDb
            ->order_by('rd.roster_date', 'ASC')
            ->order_by('rd.shift_start_time', 'ASC')
            ->get()->result_array();
    }

    /** Birthdays whose month-day falls inside [start,end]. */
    private function birthdaysInRange($start, $end)
    {
        $rows = $this->tenantDb
            ->select('e.emp_id, e.first_name, e.last_name, e.dob')
            ->from('HR_employee e')
            ->join('HR_empIdToLocationId el', 'e.emp_id = el.empId', 'left')
            ->where('el.location_id', $this->location_id)
            ->where('e.is_deleted', 0)
            ->where('e.dob IS NOT NULL', null, false)
            ->where("e.dob !=", '0000-00-00')
            ->get()->result_array();

        $wanted = [];
        $cursor = strtotime($start); $endTs = strtotime($end);
        while ($cursor <= $endTs) { $wanted[date('m-d', $cursor)] = true; $cursor = strtotime('+1 day', $cursor); }

        $out = [];
        foreach ($rows as $r) {
            if (empty($r['dob'])) { continue; }
            if (isset($wanted[date('m-d', strtotime($r['dob']))])) { $out[] = $r; }
        }
        return $out;
    }

    /** Resolve an employee name to a record at this location. */
    private function findEmployeeByName($name)
    {
        $name = strtolower(trim($name));
        if ($name === '') { return null; }

        $employees = $this->tenantDb
            ->select('e.emp_id, e.first_name, e.last_name')
            ->from('HR_employee e')
            ->join('HR_empIdToLocationId el', 'e.emp_id = el.empId', 'left')
            ->where('el.location_id', $this->location_id)
            ->where('e.is_deleted', 0)
            ->get()->result_array();

        $best = null; $bestScore = 0;
        foreach ($employees as $e) {
            $first = strtolower(trim($e['first_name']));
            $last  = strtolower(trim($e['last_name']));
            $full  = trim($first . ' ' . $last);
            $score = 0;
            if ($name === $full)            { $score = 3; }
            elseif ($name === $first)       { $score = 2; }
            elseif ($first && $last && strpos($name, $first) !== false && strpos($name, $last) !== false) { $score = 3; }
            elseif ($first && strpos($name, $first) !== false) { $score = 1; }
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = ['emp_id' => $e['emp_id'], 'name' => trim($e['first_name'] . ' ' . $e['last_name'])];
            }
        }
        return $best;
    }

    /* =====================================================================
     *  RULE-BASED FALLBACK  (used when the LLM is unavailable)
     * ===================================================================== */

    private function answerRuleBased($q)
    {
        $range     = $this->parseDateRangeText($q);
        $threshold = $this->parseThresholdText($q);
        $employee  = $this->detectEmployeeText($q);
        $agg       = $this->detectAggregation($q);

        if ($this->has($q, ['birthday', 'birth day', 'bday'])) {
            if (!$this->has($q, ['today', 'week', 'month', 'tomorrow', 'year'])) {
                $range = ['start' => date('Y-m-d'), 'end' => date('Y-m-d'), 'label' => 'today'];
            }
            return $this->answerBirthdays($range);
        }
        if ($this->has($q, ['on break', 'in break', 'break now', 'currently on break', 'taking break'])) {
            return $this->answerOnBreak();
        }
        if ($this->has($q, ['leave', 'off work', 'day off', 'days off', 'holiday', 'annual leave', 'sick', 'vacation'])) {
            return $this->answerLeave($range, $agg);
        }
        if ($this->has($q, ['present', 'absent', 'attendance', 'clocked in', 'clock in', 'clocked-in', 'who is in', "who's in"])
            && $this->has($q, ['today', 'now', 'currently'])) {
            $type = $this->has($q, ['absent', 'not in', 'missing', 'not present', 'no show', 'no-show']) ? 'absent' : 'present';
            return $this->answerAttendanceToday($type);
        }
        if ($this->has($q, ['how many employee', 'how many staff', 'number of employee', 'total employee',
                             'total staff', 'headcount', 'head count', 'how many people work', 'staff count'])
            && !$this->has($q, ['hour', 'leave', 'roster', 'shift', 'break', 'present', 'absent'])) {
            return $this->answerHeadcount();
        }
        if ($this->has($q, ['hour', 'hrs', 'worked', 'work time', 'working time', 'overtime', 'hours worked'])) {
            return $this->answerHours($range, $threshold, $employee, $agg);
        }
        if ($this->has($q, ['shift', 'roster', 'scheduled', 'working', 'who is working', "who's working", 'rostered'])) {
            return $this->answerShifts($range, $employee, $agg);
        }

        return $this->reply(
            "I couldn't understand that yet. Try questions like:\n" .
            "• Who worked more than 45 hours last week?\n" .
            "• How many employees are on leave next week?\n" .
            "• Total hours worked by all employees in the last 2 weeks\n" .
            "• Who is present today?\n" .
            "• How many employees do we have?\n" .
            "• Whose birthday is this month?",
            'unknown'
        );
    }

    /** Aggregation from keywords. */
    private function detectAggregation($q)
    {
        if ($this->has($q, ['how many', 'number of', 'count'])) { return 'count'; }
        if ($this->has($q, ['total', 'sum', 'combined', 'altogether', 'in total', 'how much'])) { return 'total'; }
        if ($this->has($q, ['who', 'which', 'list', 'name', 'each', 'per employee', 'breakdown', 'everyone', 'show'])) { return 'list'; }
        return null;
    }

    private function parseDateRangeText($q)
    {
        if (preg_match('/(?:last|past|previous)\s+(\d+)\s+week/', $q, $m)) {
            $n = max(1, (int) $m[1]); $mon = new DateTime('monday this week');
            return $this->range((clone $mon)->modify('-' . $n . ' week'), (clone $mon)->modify('-1 day'), "in the last {$n} weeks");
        }
        if (preg_match('/(?:last|past|previous)\s+(\d+)\s+day/', $q, $m)) {
            $n = max(1, (int) $m[1]);
            return $this->range((new DateTime())->modify('-' . ($n - 1) . ' day'), new DateTime(), "in the last {$n} days");
        }
        if (preg_match('/(?:last|past|previous)\s+(\d+)\s+month/', $q, $m)) {
            $n = max(1, (int) $m[1]); $first = new DateTime('first day of this month');
            return $this->range((clone $first)->modify('-' . $n . ' month'), (clone $first)->modify('-1 day'), "in the last {$n} months");
        }
        if (preg_match('/next\s+(\d+)\s+week/', $q, $m)) {
            $n = max(1, (int) $m[1]); $s = new DateTime('monday next week');
            return $this->range($s, (clone $s)->modify('+' . ($n * 7 - 1) . ' day'), "in the next {$n} weeks");
        }
        if (preg_match('/next\s+(\d+)\s+day/', $q, $m)) {
            $n = max(1, (int) $m[1]);
            return $this->range((new DateTime())->modify('+1 day'), (new DateTime())->modify('+' . $n . ' day'), "in the next {$n} days");
        }
        if ($this->has($q, ['last week', 'previous week', 'past week'])) { $s = new DateTime('monday last week'); return $this->range($s, (clone $s)->modify('+6 days'), 'last week'); }
        if ($this->has($q, ['next week'])) { $s = new DateTime('monday next week'); return $this->range($s, (clone $s)->modify('+6 days'), 'next week'); }
        if ($this->has($q, ['this week', 'current week'])) { $s = new DateTime('monday this week'); return $this->range($s, (clone $s)->modify('+6 days'), 'this week'); }
        if ($this->has($q, ['last month', 'previous month'])) { return $this->range(new DateTime('first day of last month'), new DateTime('last day of last month'), 'last month'); }
        if ($this->has($q, ['next month'])) { return $this->range(new DateTime('first day of next month'), new DateTime('last day of next month'), 'next month'); }
        if ($this->has($q, ['this month', 'current month'])) { return $this->range(new DateTime('first day of this month'), new DateTime('last day of this month'), 'this month'); }
        if ($this->has($q, ['this year', 'current year'])) { return $this->range(new DateTime(date('Y') . '-01-01'), new DateTime(date('Y') . '-12-31'), 'this year'); }
        if ($this->has($q, ['yesterday'])) { $d = (new DateTime())->modify('-1 day'); return $this->range($d, $d, 'yesterday'); }
        if ($this->has($q, ['tomorrow'])) { $d = (new DateTime())->modify('+1 day'); return $this->range($d, $d, 'tomorrow'); }
        if ($this->has($q, ['today'])) { $d = new DateTime(); return $this->range($d, $d, 'today'); }
        $s = new DateTime('monday this week');
        return $this->range($s, (clone $s)->modify('+6 days'), 'this week');
    }

    private function parseThresholdText($q)
    {
        if (!preg_match('/(\d+(?:\.\d+)?)\s*(?:hours?|hrs?|h)\b/', $q, $m)) {
            if (!preg_match('/(?:more than|greater than|over|above|less than|under|below|at least|at most)\s+(\d+(?:\.\d+)?)/', $q, $m)) {
                return null;
            }
        }
        $hours = (float) $m[1];
        $op = null;
        if ($this->has($q, ['more than', 'greater than', 'over', 'above', 'exceed', 'exceeds', 'longer than', 'higher than'])) { $op = '>'; }
        elseif ($this->has($q, ['at least', 'minimum', 'no less than', 'or more'])) { $op = '>='; }
        elseif ($this->has($q, ['less than', 'under', 'below', 'fewer than', 'lower than', 'shorter than'])) { $op = '<'; }
        elseif ($this->has($q, ['at most', 'maximum', 'no more than', 'or less'])) { $op = '<='; }
        elseif ($this->has($q, ['exactly', 'equal to', 'equals'])) { $op = '='; }
        if ($op === null) { return null; }
        return ['op' => $op, 'hours' => $hours];
    }

    private function detectEmployeeText($q)
    {
        $employees = $this->tenantDb
            ->select('e.emp_id, e.first_name, e.last_name')
            ->from('HR_employee e')
            ->join('HR_empIdToLocationId el', 'e.emp_id = el.empId', 'left')
            ->where('el.location_id', $this->location_id)
            ->where('e.is_deleted', 0)
            ->get()->result_array();

        $best = null; $bestLen = 0;
        foreach ($employees as $e) {
            $first = strtolower(trim($e['first_name']));
            $last  = strtolower(trim($e['last_name']));
            $full  = trim($first . ' ' . $last);
            foreach (array_filter([$full, $first]) as $candidate) {
                if (strlen($candidate) < 3) { continue; }
                if (preg_match('/\b' . preg_quote($candidate, '/') . '\b/', $q) && strlen($candidate) > $bestLen) {
                    $best = ['emp_id' => $e['emp_id'], 'name' => trim($e['first_name'] . ' ' . $e['last_name'])];
                    $bestLen = strlen($candidate);
                }
            }
        }
        return $best;
    }

    /* =====================================================================
     *  SHARED HELPERS
     * ===================================================================== */

    /**
     * Strict safety guard. Returns true if the question appears to request a
     * mutating / destructive / command action rather than a read-only query.
     * The assistant is answer-only and must never process such requests.
     */
    private function isHazardous($question)
    {
        $q = strtolower($question);

        // Whole-word hazardous verbs/keywords (SQL DML/DDL, admin actions,
        // command execution, and common phrasings of "change this data").
        $patterns = [
            // SQL / data mutation
            '\bdelete\b', '\bremove\b', '\bdrop\b', '\btruncate\b', '\bupdate\b',
            '\bedit\b', '\bmodify\b', '\balter\b', '\binsert\b', '\breplace\b',
            '\brename\b', '\bmerge\b', '\bupsert\b', '\bwipe\b', '\breset\b',
            '\bpurge\b', '\brevoke\b', '\bgrant\b', '\bexecute\b', '\bexec\b',
            // record/action verbs (things that would change state)
            '\bapprove\b', '\breject\b', '\bassign\b', '\bunassign\b',
            '\bfire\b', '\bhire\b', '\bterminate\b', '\bdeactivate\b',
            '\bactivate\b', '\bdisable\b', '\benable\b', '\bpromote\b',
            '\bdemote\b', '\bsuspend\b',
            // injection / command attempts
            ';\s*(?:drop|delete|update|insert|truncate|alter)\b',
            '--', '/\*', '\bunion\s+select\b', '\bxp_cmdshell\b',
            '\bshutdown\b', '\brm\s+-rf\b', '\bsudo\b', '<script',
        ];

        foreach ($patterns as $p) {
            if (preg_match('/' . $p . '/i', $q)) {
                return true;
            }
        }
        return false;
    }

    private function has($haystack, array $needles)
    {
        foreach ($needles as $n) { if (strpos($haystack, $n) !== false) { return true; } }
        return false;
    }

    private function range(DateTime $start, DateTime $end, $label)
    {
        return ['start' => $start->format('Y-m-d'), 'end' => $end->format('Y-m-d'), 'label' => $label];
    }

    private function opWord($op)
    {
        switch ($op) {
            case '>':  return 'more than';
            case '>=': return 'at least';
            case '<':  return 'less than';
            case '<=': return 'at most';
            case '=':  return 'exactly';
        }
        return '';
    }

    private function fmtHours($seconds)
    {
        $seconds = max(0, (int) $seconds);
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        if ($h > 0 && $m > 0) { return $h . 'h ' . $m . 'm'; }
        if ($h > 0)           { return $h . 'h'; }
        return $m . 'm';
    }

    private function reply($answer, $intent = 'unknown', $columns = [], $rows = [], $range = '')
    {
        return ['answer' => $answer, 'intent' => $intent, 'columns' => $columns, 'rows' => $rows, 'range' => $range];
    }
}
