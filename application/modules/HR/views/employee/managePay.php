<?php
/**
 * Manage Pay — master/detail page.
 * Left: scrollable active-employee list (no pagination, first selected by default).
 * Right: editable pay-rate panel. All data is preloaded as JSON for instant switching.
 */

// Build a compact JSON payload the client uses to render everything.
$mpEmpTypeMap = ['1' => 'Full Time', '2' => 'Part Time', '3' => 'Casual'];
$mpEmployees = [];
foreach (($empLists ?? []) as $e) {
    $eid = $e['emp_id'];
    $empTypeKey = isset($e['employee_type']) ? (string) $e['employee_type'] : '';
    $mpEmployees[] = [
        'emp_id'          => $eid,
        'name'            => trim($e['name'] ?? ''),
        'email'           => $e['email'] ?? '',
        'phone'           => $e['phone'] ?? '',
        'position_name'   => $e['primary_position_name'] ?? '',
        'employment_type' => isset($mpEmpTypeMap[$empTypeKey]) ? $mpEmpTypeMap[$empTypeKey] : '—',
        'prep_area'       => $e['prep_name'] ?? '',
        'positions'       => isset($payRatesMap[$eid]) ? array_values($payRatesMap[$eid]) : [],
    ];
}

$mpPositions = [];
foreach (($positions ?? []) as $p) {
    $mpPositions[] = ['id' => $p['position_id'], 'name' => $p['position_name']];
}

$mpPayrollTypes = [];
foreach (($payrollTypes ?? []) as $pt) {
    $mpPayrollTypes[] = ['id' => $pt['id'], 'name' => $pt['name']];
}

$mpData = [
    'employees'    => $mpEmployees,
    'positions'    => $mpPositions,
    'payrollTypes' => $mpPayrollTypes,
    'saveUrl'      => base_url('HR/Employee/savePayRates'),
    'editBase'     => base_url('HR/Employee/edit/'),
];
?>

<style>
    #managePay {
        --mp-navy: #1a2f52;
        --mp-green: #1D9E75;
        --mp-green-dark: #0F6E56;
        --mp-green-soft: #E1F5EE;
        --mp-red: #ef4444;
        --mp-border: #e5e7eb;
        --mp-text: #1f2937;
        --mp-muted: #6b7280;
        --mp-bg: #f7f8fa;
    }

    #managePay {
        color: var(--mp-text);
    }

    /* A little breathing room between the top nav/header and the page content. */
    .page-content:has(#managePay) {
        padding-top: 28px;
    }
    #managePay {
        margin-top: 0;
    }

    #managePay .mp-shell {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    /* ---------- Left: employee list ---------- */
    #managePay .mp-aside {
        width: 340px;
        flex: 0 0 340px;
        background: #fff;
        border: 1px solid var(--mp-border);
        border-radius: 14px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        /* Stay in view and scroll the list internally instead of the whole page. */
        position: sticky;
        top: 90px;
        max-height: calc(100vh - 110px);
    }

    #managePay .mp-aside-head {
        padding: 18px 18px 12px;
        border-bottom: 1px solid var(--mp-border);
    }

    #managePay .mp-aside-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--mp-navy);
        margin: 0 0 12px;
    }

    #managePay .mp-search {
        position: relative;
    }

    #managePay .mp-search i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--mp-muted);
        font-size: 16px;
    }

    #managePay .mp-search input {
        width: 100%;
        padding: 10px 12px 10px 36px;
        border: 1px solid var(--mp-border);
        border-radius: 10px;
        font-size: 14px;
        outline: none;
        transition: border-color .15s ease;
    }

    #managePay .mp-search input:focus {
        border-color: var(--mp-green);
    }

    #managePay .mp-list {
        flex: 1 1 auto;
        overflow-y: auto;
        padding: 10px;
        min-height: 0;
    }

    #managePay .mp-list::-webkit-scrollbar { width: 8px; }
    #managePay .mp-list::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 8px; }

    #managePay .mp-emp {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: 12px;
        cursor: pointer;
        border: 1px solid transparent;
        transition: background .12s ease, border-color .12s ease;
    }

    #managePay .mp-emp:hover { background: #f3f4f6; }

    #managePay .mp-emp.active {
        background: var(--mp-green-soft);
        border-color: #bfe6d7;
    }

    #managePay .mp-emp.active::before {
        content: "";
        position: absolute;
    }

    #managePay .mp-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--mp-navy);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
        flex: 0 0 40px;
        text-transform: uppercase;
    }

    #managePay .mp-emp-meta { flex: 1 1 auto; min-width: 0; }

    #managePay .mp-emp-name {
        font-weight: 600;
        font-size: 14px;
        color: var(--mp-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #managePay .mp-emp-role {
        font-size: 12px;
        color: var(--mp-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #managePay .mp-badge {
        font-size: 11px;
        font-weight: 600;
        color: var(--mp-green-dark);
        background: var(--mp-green-soft);
        padding: 3px 9px;
        border-radius: 999px;
        white-space: nowrap;
    }

    #managePay .mp-aside-foot {
        padding: 12px 18px;
        border-top: 1px solid var(--mp-border);
        font-size: 12px;
        color: var(--mp-muted);
    }

    /* ---------- Right: detail panel ---------- */
    #managePay .mp-main {
        flex: 1 1 auto;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    #managePay .mp-card {
        background: #fff;
        border: 1px solid var(--mp-border);
        border-radius: 14px;
        padding: 22px;
    }

    #managePay .mp-emp-header {
        display: flex;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
    }

    #managePay .mp-emp-header .mp-avatar {
        width: 64px;
        height: 64px;
        flex: 0 0 64px;
        font-size: 20px;
    }

    #managePay .mp-emp-header h4 {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
        color: var(--mp-navy);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #managePay .mp-profile-link {
        margin-left: auto;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        border: 1px solid var(--mp-border);
        border-radius: 10px;
        color: var(--mp-navy);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: background .12s ease;
    }

    #managePay .mp-profile-link:hover { background: #f3f4f6; }

    #managePay .mp-meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 18px 24px;
        margin-top: 20px;
        padding-top: 18px;
        border-top: 1px solid var(--mp-border);
    }

    #managePay .mp-meta-item .mp-meta-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--mp-muted);
        margin-bottom: 3px;
    }

    #managePay .mp-meta-item .mp-meta-value {
        font-size: 14px;
        font-weight: 600;
        color: var(--mp-text);
        word-break: break-word;
    }

    #managePay .mp-section-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--mp-navy);
        margin: 0;
    }

    #managePay .mp-section-sub {
        font-size: 13px;
        color: var(--mp-muted);
        margin: 2px 0 0;
    }

    #managePay .mp-table-wrap { overflow-x: auto; margin-top: 18px; }

    #managePay table.mp-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 940px;
    }

    #managePay table.mp-table thead th {
        background: #f9fafb;
        font-size: 12px;
        font-weight: 600;
        color: #374151;
        text-align: left;
        padding: 12px 10px;
        white-space: nowrap;
        border-bottom: 1px solid var(--mp-border);
    }

    #managePay table.mp-table thead th:first-child { border-top-left-radius: 10px; }
    #managePay table.mp-table thead th:last-child { border-top-right-radius: 10px; }

    #managePay table.mp-table td {
        padding: 12px 10px;
        vertical-align: top;
        border-bottom: 1px solid #f1f3f5;
    }

    #managePay .mp-req { color: var(--mp-red); }

    #managePay .mp-field {
        width: 100%;
        padding: 9px 10px;
        border: 1px solid var(--mp-border);
        border-radius: 8px;
        font-size: 13px;
        outline: none;
        transition: border-color .12s ease;
        background: #fff;
    }

    #managePay .mp-field:focus { border-color: var(--mp-green); }
    #managePay input.mp-field { text-align: right; }
    #managePay select.mp-field { text-align: left; min-width: 130px; }

    #managePay .mp-row-actions {
        display: flex;
        gap: 8px;
        padding-top: 2px;
    }

    #managePay .mp-icon-btn {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 8px;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    #managePay .mp-add-row { background: var(--mp-green); }
    #managePay .mp-add-row:hover { background: var(--mp-green-dark); }
    #managePay .mp-remove-row { background: var(--mp-red); }
    #managePay .mp-remove-row:hover { background: #dc2626; }

    #managePay .mp-add-another {
        width: 100%;
        margin-top: 14px;
        padding: 12px;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        background: #fff;
        color: var(--mp-green-dark);
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: background .12s ease;
    }

    #managePay .mp-add-another:hover { background: #f0fdf9; }

    #managePay .mp-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 18px;
    }

    #managePay .mp-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid transparent;
    }

    #managePay .mp-btn-light {
        background: #fff;
        border-color: var(--mp-border);
        color: var(--mp-text);
    }

    #managePay .mp-btn-light:hover { background: #f3f4f6; }

    #managePay .mp-btn-save {
        background: var(--mp-green);
        color: #fff;
    }

    #managePay .mp-btn-save:hover { background: var(--mp-green-dark); }
    #managePay .mp-btn-save:disabled { opacity: .6; cursor: not-allowed; }

    #managePay .mp-note {
        margin-top: 18px;
        background: #eff6ff;
        border: 1px solid #dbeafe;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 13px;
        color: #1e40af;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    #managePay .mp-empty {
        padding: 60px 20px;
        text-align: center;
        color: var(--mp-muted);
    }

    /* Toast */
    #managePay .mp-toast {
        position: fixed;
        right: 24px;
        bottom: 24px;
        background: var(--mp-navy);
        color: #fff;
        padding: 12px 18px;
        border-radius: 10px;
        font-size: 14px;
        box-shadow: 0 10px 30px rgba(0,0,0,.2);
        opacity: 0;
        transform: translateY(12px);
        transition: opacity .2s ease, transform .2s ease;
        z-index: 1080;
        pointer-events: none;
    }

    #managePay .mp-toast.show { opacity: 1; transform: translateY(0); }
    #managePay .mp-toast.error { background: var(--mp-red); }

    @media (max-width: 991px) {
        #managePay .mp-shell { flex-direction: column; }
        #managePay .mp-aside { width: 100%; flex: 1 1 auto; position: static; max-height: none; }
        #managePay .mp-list { max-height: 320px; }
    }
</style>

<div class="main-content">
  <div class="page-content">
    <div class="container-fluid">
      <div id="managePay">

        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="mb-0" style="color:#1a2f52;font-weight:700;">Manage Pay</h5>
            <small class="text-muted">Set employee pay rates for different days and conditions.</small>
          </div>
          <a href="/HR/employees" class="mp-btn mp-btn-light"><i class="ri-arrow-left-line"></i> Back to Employees</a>
        </div>

        <div class="mp-shell">

          <!-- Left: employees -->
          <aside class="mp-aside">
            <div class="mp-aside-head">
              <h3 class="mp-aside-title">Employees</h3>
              <div class="mp-search">
                <i class="ri-search-line"></i>
                <input type="text" id="mpSearch" placeholder="Search employees..." autocomplete="off">
              </div>
            </div>
            <div class="mp-list" id="mpList"><!-- rendered by JS --></div>
            <div class="mp-aside-foot" id="mpCount"></div>
          </aside>

          <!-- Right: detail -->
          <section class="mp-main" id="mpDetail"><!-- rendered by JS --></section>

        </div>

        <div class="mp-toast" id="mpToast"></div>
      </div>
    </div>
  </div>
</div>

<script>
window.__MP = <?php echo json_encode($mpData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

(function () {
    "use strict";

    var MP = window.__MP;
    var employees = MP.employees || [];
    var positions = MP.positions || [];
    var payrollTypes = MP.payrollTypes || [];

    var selectedId = employees.length ? String(employees[0].emp_id) : null;
    // Per-employee working state (clone of positions) + removed row ids.
    var workState = {}; // emp_id -> { rows: [...], removed: [ids] }
    var tempSeq = 0;

    var $list = document.getElementById('mpList');
    var $detail = document.getElementById('mpDetail');
    var $count = document.getElementById('mpCount');
    var $search = document.getElementById('mpSearch');
    var $toast = document.getElementById('mpToast');

    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function initials(name) {
        var parts = String(name || '').trim().split(/\s+/);
        if (!parts.length || !parts[0]) return '?';
        var a = parts[0].charAt(0);
        var b = parts.length > 1 ? parts[parts.length - 1].charAt(0) : '';
        return (a + b).toUpperCase();
    }

    function money(v) {
        var n = parseFloat(v);
        return isNaN(n) ? '0.00' : n.toFixed(2);
    }

    function getEmp(id) {
        for (var i = 0; i < employees.length; i++) {
            if (String(employees[i].emp_id) === String(id)) return employees[i];
        }
        return null;
    }

    // Build (once) the editable working state for an employee from its saved rows.
    function ensureState(emp) {
        var id = String(emp.emp_id);
        if (workState[id]) return workState[id];
        var rows = (emp.positions || []).map(function (p) {
            return {
                key: 'k' + (tempSeq++),
                id: p.id || '',
                position_id: p.position_id || '',
                payroll_type_id: p.payroll_type_id || '',
                rate: money(p.rate),
                Saturday_rate: money(p.Saturday_rate),
                Sunday_rate: money(p.Sunday_rate),
                holiday_rate: money(p.holiday_rate),
                early_start: money(p.early_start),
                late_night: money(p.late_night),
                uniform_allowance: money(p.uniform_allowance)
            };
        });
        if (!rows.length) rows.push(blankRow());
        workState[id] = { rows: rows, removed: [] };
        return workState[id];
    }

    function blankRow() {
        return {
            key: 'k' + (tempSeq++), id: '', position_id: '', payroll_type_id: '',
            rate: '0.00', Saturday_rate: '0.00', Sunday_rate: '0.00', holiday_rate: '0.00',
            early_start: '0.00', late_night: '0.00', uniform_allowance: '0.00'
        };
    }

    /* ---------------- Left list ---------------- */
    function renderList() {
        var q = ($search.value || '').toLowerCase().trim();
        var shown = 0;
        var html = '';
        employees.forEach(function (e) {
            var hay = (e.name + ' ' + (e.position_name || '') + ' ' + (e.email || '')).toLowerCase();
            if (q && hay.indexOf(q) === -1) return;
            shown++;
            var active = String(e.emp_id) === String(selectedId);
            html += '' +
                '<div class="mp-emp' + (active ? ' active' : '') + '" data-id="' + esc(e.emp_id) + '">' +
                    '<div class="mp-avatar">' + esc(initials(e.name)) + '</div>' +
                    '<div class="mp-emp-meta">' +
                        '<div class="mp-emp-name">' + esc(e.name || 'Unnamed') + '</div>' +
                        '<div class="mp-emp-role">' + esc(e.position_name || '—') + '</div>' +
                    '</div>' +
                    '<span class="mp-badge">Active</span>' +
                '</div>';
        });
        $list.innerHTML = html || '<div class="mp-empty">No employees found.</div>';
        $count.textContent = 'Showing ' + shown + ' of ' + employees.length + ' employees';
    }

    /* ---------------- Right detail ---------------- */
    function optionList(items, selVal, placeholder) {
        var out = '<option value="">' + esc(placeholder) + '</option>';
        items.forEach(function (it) {
            var sel = String(it.id) === String(selVal) ? ' selected' : '';
            out += '<option value="' + esc(it.id) + '"' + sel + '>' + esc(it.name) + '</option>';
        });
        return out;
    }

    var NUM_FIELDS = [
        ['rate', 'Weekday', true],
        ['Saturday_rate', 'Saturday', true],
        ['Sunday_rate', 'Sunday', true],
        ['holiday_rate', 'Public Holiday', true],
        ['early_start', 'Early Start', false],
        ['late_night', 'Late Night', false],
        ['uniform_allowance', 'Uniform', false]
    ];

    function renderRow(row) {
        var tds = '';
        tds += '<td><select class="mp-field mp-in" data-f="position_id">' +
                    optionList(positions, row.position_id, 'Select Position') + '</select></td>';
        tds += '<td><select class="mp-field mp-in" data-f="payroll_type_id">' +
                    optionList(payrollTypes, row.payroll_type_id, 'Select Type') + '</select></td>';
        NUM_FIELDS.forEach(function (f) {
            tds += '<td><input type="number" step="0.01" min="0" class="mp-field mp-in" data-f="' + f[0] + '" value="' + esc(row[f[0]]) + '"></td>';
        });
        tds += '<td><div class="mp-row-actions">' +
                    '<button type="button" class="mp-icon-btn mp-add-row" title="Add row">+</button>' +
                    '<button type="button" class="mp-icon-btn mp-remove-row" title="Remove row">&minus;</button>' +
                '</div></td>';
        return '<tr class="mp-prow" data-key="' + esc(row.key) + '">' + tds + '</tr>';
    }

    function renderDetail() {
        if (!selectedId) {
            $detail.innerHTML = '<div class="mp-card"><div class="mp-empty">No active employees to display.</div></div>';
            return;
        }
        var emp = getEmp(selectedId);
        if (!emp) return;
        var st = ensureState(emp);

        var head = '' +
            '<div class="mp-card">' +
                '<div class="mp-emp-header">' +
                    '<div class="mp-avatar">' + esc(initials(emp.name)) + '</div>' +
                    '<div>' +
                        '<h4>' + esc(emp.name || 'Unnamed') + ' <span class="mp-badge">Active</span></h4>' +
                    '</div>' +
                    '<a class="mp-profile-link" href="' + esc(MP.editBase + emp.emp_id) + '">' +
                        '<i class="ri-user-line"></i> View Employee Profile</a>' +
                '</div>' +
                '<div class="mp-meta-grid">' +
                    metaItem('Position', emp.position_name || '—') +
                    metaItem('Employment Type', emp.employment_type || '—') +
                    metaItem('Prep Area', emp.prep_area || '—') +
                    metaItem('Email', emp.email || '—') +
                    metaItem('Phone', emp.phone || '—') +
                '</div>' +
            '</div>';

        var thNums = NUM_FIELDS.map(function (f) {
            return '<th>' + esc(f[1]) + (f[2] ? '<span class="mp-req">*</span>' : '') + '</th>';
        }).join('');

        var rowsHtml = st.rows.map(renderRow).join('');

        var table = '' +
            '<div class="mp-card">' +
                '<div class="d-flex justify-content-between align-items-start flex-wrap gap-2">' +
                    '<div>' +
                        '<h3 class="mp-section-title">Position Details and Pay Rates</h3>' +
                        '<p class="mp-section-sub">Set employee pay rates for different days and conditions.</p>' +
                    '</div>' +
                '</div>' +
                '<div class="mp-table-wrap"><table class="mp-table">' +
                    '<thead><tr>' +
                        '<th>Position and Payroll<span class="mp-req">*</span></th>' +
                        '<th>Payroll Type</th>' +
                        thNums +
                        '<th>Actions</th>' +
                    '</tr></thead>' +
                    '<tbody id="mpRows">' + rowsHtml + '</tbody>' +
                '</table></div>' +
                '<button type="button" class="mp-add-another" id="mpAddAnother"><i class="ri-add-line"></i> Add another position</button>' +
                '<div class="mp-actions">' +
                    '<button type="button" class="mp-btn mp-btn-light" id="mpReset"><i class="ri-refresh-line"></i> Reset</button>' +
                    '<button type="button" class="mp-btn mp-btn-save" id="mpSave"><i class="ri-check-line"></i> Save Pay Rates</button>' +
                '</div>' +
            '</div>';

        $detail.innerHTML = head + table;
    }

    function metaItem(label, value) {
        return '<div class="mp-meta-item">' +
            '<div class="mp-meta-label">' + esc(label) + '</div>' +
            '<div class="mp-meta-value">' + esc(value) + '</div></div>';
    }

    /* ---------------- State sync from DOM ---------------- */
    function syncStateFromDom() {
        var st = workState[String(selectedId)];
        if (!st) return;
        var byKey = {};
        st.rows.forEach(function (r) { byKey[r.key] = r; });
        var trs = document.querySelectorAll('#mpRows .mp-prow');
        Array.prototype.forEach.call(trs, function (tr) {
            var row = byKey[tr.getAttribute('data-key')];
            if (!row) return;
            Array.prototype.forEach.call(tr.querySelectorAll('.mp-in'), function (inp) {
                row[inp.getAttribute('data-f')] = inp.value;
            });
        });
    }

    /* ---------------- Events ---------------- */
    $search.addEventListener('input', renderList);

    $list.addEventListener('click', function (e) {
        var card = e.target.closest('.mp-emp');
        if (!card) return;
        var id = card.getAttribute('data-id');
        if (String(id) === String(selectedId)) return;
        syncStateFromDom();
        selectedId = id;
        renderList();
        renderDetail();
    });

    $detail.addEventListener('click', function (e) {
        var addAll = e.target.closest('#mpAddAnother');
        var addRow = e.target.closest('.mp-add-row');
        var rmRow = e.target.closest('.mp-remove-row');
        var reset = e.target.closest('#mpReset');
        var save = e.target.closest('#mpSave');

        if (addAll || addRow) {
            syncStateFromDom();
            var st = workState[String(selectedId)];
            st.rows.push(blankRow());
            renderDetail();
            return;
        }

        if (rmRow) {
            syncStateFromDom();
            var st2 = workState[String(selectedId)];
            var tr = rmRow.closest('.mp-prow');
            var key = tr.getAttribute('data-key');
            var idx = -1;
            for (var i = 0; i < st2.rows.length; i++) { if (st2.rows[i].key === key) { idx = i; break; } }
            if (idx > -1) {
                var removed = st2.rows.splice(idx, 1)[0];
                if (removed.id) st2.removed.push(removed.id);
            }
            if (!st2.rows.length) st2.rows.push(blankRow());
            renderDetail();
            return;
        }

        if (reset) {
            delete workState[String(selectedId)];
            renderDetail();
            showToast('Changes reset');
            return;
        }

        if (save) {
            doSave(save);
            return;
        }
    });

    function doSave(btn) {
        syncStateFromDom();
        var emp = getEmp(selectedId);
        var st = workState[String(selectedId)];
        if (!emp || !st) return;

        // A row "has data" if a payroll type is picked or any rate is non-zero.
        function rowHasData(r) {
            if (r.payroll_type_id) return true;
            var numKeys = ['rate', 'Saturday_rate', 'Sunday_rate', 'holiday_rate', 'early_start', 'late_night', 'uniform_allowance'];
            return numKeys.some(function (k) {
                var n = parseFloat(r[k]);
                return !isNaN(n) && n !== 0;
            });
        }

        // Validate: data entered without a Position must not be silently dropped.
        var missingPosition = false;
        var missingRate = false;
        st.rows.forEach(function (r) {
            if (!r.position_id) {
                if (rowHasData(r)) missingPosition = true;
                return;
            }
            if (r.rate === '' || isNaN(parseFloat(r.rate))) missingRate = true;
        });

        if (missingPosition) { showToast('Please select a Position for each pay rate you entered', true); return; }
        if (missingRate) { showToast('Enter a Weekday rate for each position', true); return; }

        // Rows that will actually be persisted (a Position is required).
        var payloadRows = st.rows.filter(function (r) { return r.position_id; });
        if (!payloadRows.length) { showToast('Add at least one position with a pay rate', true); return; }

        var payload = { emp_id: emp.emp_id, rows: [], removeIds: st.removed.slice() };
        payloadRows.forEach(function (r) {
            payload.rows.push({
                tempKey: r.key, id: r.id, position_id: r.position_id,
                payroll_type_id: r.payroll_type_id,
                rate: r.rate, Saturday_rate: r.Saturday_rate, Sunday_rate: r.Sunday_rate,
                holiday_rate: r.holiday_rate, early_start: r.early_start,
                late_night: r.late_night, uniform_allowance: r.uniform_allowance
            });
        });

        btn.disabled = true;
        var original = btn.innerHTML;
        btn.innerHTML = '<i class="ri-loader-4-line"></i> Saving...';

        jQuery.ajax({
            type: 'POST',
            url: MP.saveUrl,
            data: payload,
            dataType: 'json'
        }).done(function (res) {
            if (res && res.status === 'success') {
                // Map new DB ids back onto the working rows so re-saving updates.
                (res.saved || []).forEach(function (s) {
                    if (!s.tempKey) return;
                    st.rows.forEach(function (r) { if (r.key === s.tempKey) r.id = s.id; });
                });
                st.removed = [];
                // Update the preloaded snapshot so switching away/back is consistent.
                emp.positions = st.rows.filter(function (r) { return r.position_id; }).map(function (r) {
                    return {
                        id: r.id, position_id: r.position_id, payroll_type_id: r.payroll_type_id,
                        rate: r.rate, Saturday_rate: r.Saturday_rate, Sunday_rate: r.Sunday_rate,
                        holiday_rate: r.holiday_rate, early_start: r.early_start,
                        late_night: r.late_night, uniform_allowance: r.uniform_allowance
                    };
                });
                showToast('Pay rates saved');
            } else {
                showToast((res && res.message) || 'Could not save', true);
            }
        }).fail(function () {
            showToast('Network error, please try again', true);
        }).always(function () {
            btn.disabled = false;
            btn.innerHTML = original;
        });
    }

    var toastTimer;
    function showToast(msg, isError) {
        $toast.textContent = msg;
        $toast.className = 'mp-toast show' + (isError ? ' error' : '');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () { $toast.className = 'mp-toast' + (isError ? ' error' : ''); }, 2600);
    }

    /* ---------------- Init ---------------- */
    renderList();
    renderDetail();
})();
</script>
