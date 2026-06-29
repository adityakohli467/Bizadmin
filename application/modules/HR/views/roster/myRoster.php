<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// ---- helpers (view-scoped) ----
if (!function_exists('mr_fmt_time')) {
    function mr_fmt_time($t) { return ($t !== '' && $t !== null) ? date('g:i A', strtotime($t)) : ''; }
}
if (!function_exists('mr_shift_type')) {
    // Classify a shift by start hour: Morning < 12, Afternoon 12-16, Evening >= 16.
    function mr_shift_type($start) {
        if ($start === '' || $start === null) return ['Not Scheduled', '#94a3b8', 'rgba(148,163,184,.15)'];
        $h = (int) date('G', strtotime($start));
        if ($h < 12)  return ['Morning',   '#25A69A', 'rgba(37,166,154,.15)'];
        if ($h < 16)  return ['Afternoon', '#f59e0b', 'rgba(245,158,11,.15)'];
        return ['Evening', '#3b82f6', 'rgba(59,130,246,.15)'];
    }
}
$totalHours = $totalSeconds / 3600;
$avgShift   = $shiftCount > 0 ? $totalHours / $shiftCount : 0;
$daysScheduled = 0;
foreach ($days as $list) { if (!empty($list)) $daysScheduled++; }
// Next shift label
$nextShift = '—';
if (!empty($upcoming)) {
    $u = $upcoming[0];
    $nextShift = date('D, j M', strtotime($u['date'])) . ', ' . mr_fmt_time($u['start']);
}
?>
<div id="myRoster" class="mr-wrap">
<style>
.mr-wrap{--mr-teal:#25A69A;--mr-navy:#1a2f52;padding:90px 24px 48px;max-width:none;width:100%;margin:0;color:#1a1a1a;}
.mr-loader{position:fixed;inset:0;background:rgba(255,255,255,.6);display:none;align-items:center;justify-content:center;z-index:9999;}
.mr-loader.show{display:flex;}
.mr-spin{width:46px;height:46px;border:4px solid #d7eee9;border-top-color:var(--mr-teal);border-radius:50%;animation:mrspin .8s linear infinite;}
@keyframes mrspin{to{transform:rotate(360deg)}}
.mr-head{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:12px;margin-bottom:6px;}
.mr-wrap h3.mr-title{font-size:1.6rem;font-weight:700;color:#1a2f52!important;margin:0;}
.mr-sub{color:#475569;font-size:.92rem;margin:0 0 14px;}
.mr-toolbar{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:10px;margin-bottom:16px;}
.mr-week{display:flex;align-items:center;gap:6px;}
.mr-week .btn-nav{width:38px;height:38px;border:1px solid #e2e8f0;background:#fff;border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#1a1a1a;}
.mr-week .btn-nav:hover{border-color:var(--mr-teal);color:var(--mr-teal);}
.mr-week .label{padding:8px 14px;border:1px solid #e2e8f0;border-radius:8px;font-weight:600;color:#1a1a1a;background:#fff;}
.mr-actions{display:flex;gap:8px;}
.mr-actions .btn{border:1px solid #25A69A;background:#25A69A;color:#fff;border-radius:8px;padding:7px 16px;font-size:.85rem;cursor:pointer;display:inline-flex;align-items:center;gap:6px;}
.mr-actions .btn:hover{background:#1f8c82;border-color:#1f8c82;color:#fff;}
.mr-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:0;margin-bottom:16px;border:1px solid #eef2f6;border-radius:10px;background:#fff;overflow:hidden;}
.mr-stat{padding:16px 20px;border-right:1px solid #eef2f6;}
.mr-stat:last-child{border-right:0;}
.mr-stat .lbl{font-size:.82rem;color:#475569;margin-bottom:4px;}
.mr-stat .val{font-size:1.2rem;font-weight:700;color:var(--mr-teal);}
.mr-stat .val.navy{color:#1a1a1a;font-size:1rem;}
.mr-tablecard{border:1px solid #eef2f6;border-radius:10px;overflow:hidden;background:#fff;margin-bottom:16px;}
.mr-table{width:100%;border-collapse:collapse;font-size:.8rem;color:#1a1a1a;}
.mr-table th,.mr-table td{padding:10px 12px;border:1px solid #f0f3f7;text-align:left;vertical-align:middle;}
.mr-table thead th:first-child{width:110px;color:#1a1a1a;font-weight:700;}
.mr-table thead th{font-weight:700;color:#1a1a1a;}
.mr-table .rowlbl{color:#475569;font-weight:600;background:#fbfcfe;}
.mr-table th .dnum{color:var(--mr-teal);font-weight:600;font-size:.82rem;margin-top:2px;}
.mr-badge{display:inline-block;padding:3px 9px;border-radius:6px;font-size:.72rem;font-weight:600;}
.mr-grid2{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;}
.mr-panel{border:1px solid #eef2f6;border-radius:10px;padding:16px 18px;background:#fff;}
.mr-panel h6{font-weight:700;color:#1a1a1a;margin:0 0 12px;font-size:1rem;}
.mr-up{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f3f5f9;}
.mr-up:last-child{border-bottom:0;}
.mr-up .dt{text-align:center;border:1px solid #cdd9ef;background:#eef2fb;border-radius:8px;padding:5px 10px;min-width:48px;}
.mr-up .dt .d{font-weight:700;color:#1a2f52;font-size:1.05rem;line-height:1;}
.mr-up .dt .m{font-size:.68rem;color:#3b82f6;text-transform:uppercase;font-weight:600;}
.mr-up .meta{flex:1;}
.mr-up .meta b{color:#1a2f52;font-size:.9rem;}
.mr-up .meta small{color:#94a3b8;display:block;}
.mr-up .rel{font-size:.72rem;font-weight:600;padding:4px 10px;border-radius:6px;white-space:nowrap;}
.mr-srow{display:flex;justify-content:space-between;padding:8px 0;font-size:.9rem;border-bottom:1px solid #f3f5f9;color:#1a1a1a;}
.mr-srow:last-child{border:0;}
.mr-srow .v{font-weight:700;color:var(--mr-teal);}
.mr-leg{display:flex;align-items:center;gap:8px;padding:7px 0;font-size:.85rem;color:#1a1a1a;}
.mr-dot{width:11px;height:11px;border-radius:50%;}
.mr-empty{color:#cbd5e1;}
/* Mobile card view */
.mr-cards{display:none;margin-bottom:16px;}
.mr-cards-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;}
.mr-cards-head h6{margin:0;font-weight:700;color:#1a2f52;font-size:1.05rem;}
.mr-cards-nav{display:flex;gap:8px;}
.mr-arrow{width:34px;height:34px;border:1px solid #e2e8f0;background:#fff;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#1a2f52;cursor:pointer;}
.mr-arrow:active{background:#eef2fb;}
.mr-cards-track{display:flex;gap:12px;overflow-x:auto;scroll-behavior:smooth;padding-bottom:6px;-webkit-overflow-scrolling:touch;}
.mr-cards-track::-webkit-scrollbar{display:none;}
.mr-card{flex:0 0 auto;width:240px;aspect-ratio:1;border:1px solid #eef2f6;border-top:3px solid #25A69A;border-radius:0;padding:14px 16px;background:#fff;box-shadow:0 2px 8px rgba(16,40,80,.05);}
.mr-card.off{border-top-color:#cbd5e1;background:#f8fafc;}
.mr-card-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;}
.mr-card .dw{font-size:.72rem;color:#94a3b8;font-weight:600;}
.mr-card .dn{font-size:1.5rem;font-weight:700;color:#1a2f52;line-height:1;}
.mr-card .hh{color:#25A69A;font-weight:700;font-size:.9rem;}
.mr-card .tm{margin-top:8px;font-weight:600;color:#1a1a1a;font-size:.9rem;}
.mr-card .rl{color:#475569;font-size:.85rem;margin-top:4px;}
.mr-card .lc{display:flex;align-items:center;gap:6px;color:#25A69A;font-size:.82rem;margin-top:4px;}
@media(max-width:991px){.mr-stats{grid-template-columns:repeat(2,1fr);}.mr-stat{border-bottom:1px solid #eef2f6;}.mr-grid2{grid-template-columns:1fr;}.mr-tablecard{display:none;}.mr-cards{display:block;}}
@media(max-width:575px){.mr-wrap{padding-top:70px;}.mr-sub{font-size:.78rem;white-space:nowrap;}.mr-stats{grid-template-columns:1fr 1fr;}.mr-stat-hide{display:none;}.mr-stat{border-bottom:0;}.mr-title{font-size:1.25rem;}.mr-toolbar{flex-direction:column;align-items:stretch;}.mr-actions{display:none;}}
</style>

<div class="mr-loader" id="mrLoader"><div class="mr-spin"></div></div>

<div class="mr-head">
    <h3 class="mr-title">My Roster</h3>
</div>
<p class="mr-sub">View your shifts, hours and upcoming schedule.</p>

<div class="mr-toolbar">
    <div class="mr-week">
        <button class="btn-nav" type="button" data-start="<?php echo $prevStart; ?>" onclick="mrLoadWeek('<?php echo $prevStart; ?>')"><i class="bx bx-chevron-left"></i></button>
        <span class="label"><?php echo htmlspecialchars($weekRange); ?><i class="bx bx-calendar" style="margin-left:8px;color:#64748b;"></i></span>
        <button class="btn-nav" type="button" data-start="<?php echo $nextStart; ?>" onclick="mrLoadWeek('<?php echo $nextStart; ?>')"><i class="bx bx-chevron-right"></i></button>
    </div>
    <div class="mr-actions">
        <button class="btn" type="button" onclick="window.print()"><i class="bx bx-printer"></i> Print</button>
    </div>
</div>

<div class="mr-stats">
    <div class="mr-stat"><div class="lbl">Total Hours</div><div class="val"><?php echo number_format($totalHours,2); ?> hrs</div></div>
    <div class="mr-stat mr-stat-hide"><div class="lbl">Scheduled Shifts</div><div class="val navy"><?php echo (int)$shiftCount; ?></div></div>
    <div class="mr-stat mr-stat-hide"><div class="lbl">Average Shift</div><div class="val"><?php echo number_format($avgShift,2); ?> hrs</div></div>
    <div class="mr-stat"><div class="lbl">Next Shift</div><div class="val navy"><?php echo htmlspecialchars($nextShift); ?></div></div>
</div>

<div class="mr-tablecard mr-table-wrap">
    <table class="mr-table">
        <thead><tr>
            <th>Day</th>
            <?php foreach ($days as $date => $list): ?>
                <th><div><?php echo date('D, j M', strtotime($date)); ?></div><div class="dnum"><?php $f=$list[0]['hours']??0; echo $f? number_format(array_sum(array_column($list,'hours')),2).' hrs':''; ?></div></th>
            <?php endforeach; ?>
        </tr></thead>
        <tbody>
            <tr><td class="rowlbl">Shift</td>
                <?php foreach ($days as $list): $t=mr_shift_type($list[0]['start']??''); ?>
                    <td><?php if(!empty($list)): ?><span class="mr-badge" style="color:<?php echo $t[1];?>;background:<?php echo $t[2];?>"><?php echo $t[0];?></span><?php else: ?><span class="mr-badge" style="color:#94a3b8;background:rgba(148,163,184,.15)">Not Scheduled</span><?php endif; ?></td>
                <?php endforeach; ?>
            </tr>
            <tr><td class="rowlbl">Time</td>
                <?php foreach ($days as $list): ?><td><?php echo !empty($list)?mr_fmt_time($list[0]['start']).' - '.mr_fmt_time($list[0]['end']):'<span class="mr-empty">—</span>'; ?></td><?php endforeach; ?>
            </tr>
            <tr><td class="rowlbl">Role</td>
                <?php foreach ($days as $list): ?><td><?php echo !empty($list)&&$list[0]['role']?htmlspecialchars($list[0]['role']):'<span class="mr-empty">—</span>'; ?></td><?php endforeach; ?>
            </tr>
            <tr><td class="rowlbl">Location</td>
                <?php foreach ($days as $list): ?><td><?php echo !empty($list)?htmlspecialchars($list[0]['area']?:($locationName?:'')):'<span class="mr-empty">—</span>'; ?></td><?php endforeach; ?>
            </tr>
            <tr><td class="rowlbl">Break</td>
                <?php foreach ($days as $list): ?><td><?php echo !empty($list)&&$list[0]['break']?htmlspecialchars($list[0]['break']).' min':'<span class="mr-empty">—</span>'; ?></td><?php endforeach; ?>
            </tr>
            <tr><td class="rowlbl">Notes</td>
                <?php foreach ($days as $list): ?><td><?php echo !empty($list)&&$list[0]['notes']?htmlspecialchars($list[0]['notes']):'<span class="mr-empty">—</span>'; ?></td><?php endforeach; ?>
            </tr>
        </tbody>
    </table>
</div>

<!-- Mobile card view (replaces table on small screens) -->
<div class="mr-cards">
    <div class="mr-cards-head">
        <h6>This Week</h6>
        <div class="mr-cards-nav">
            <button type="button" class="mr-arrow" onclick="mrScroll(-1)"><i class="bx bx-chevron-left"></i></button>
            <button type="button" class="mr-arrow" onclick="mrScroll(1)"><i class="bx bx-chevron-right"></i></button>
        </div>
    </div>
    <div class="mr-cards-track" id="mrCardsTrack">
        <?php foreach ($days as $date => $list): $t = mr_shift_type($list[0]['start'] ?? ''); $isOff = empty($list); ?>
            <div class="mr-card<?php echo $isOff?' off':'';?>">
                <div class="mr-card-top">
                    <div><div class="dw"><?php echo strtoupper(date('D',strtotime($date)));?></div><div class="dn"><?php echo date('j',strtotime($date));?></div></div>
                    <div class="hh"><?php echo $isOff?'—':number_format(array_sum(array_column($list,'hours')),2).'h';?></div>
                </div>
                <span class="mr-badge" style="<?php echo $isOff?'color:#94a3b8;background:rgba(148,163,184,.15)':'color:'.$t[1].';background:'.$t[2];?>"><?php echo $isOff?'Off':$t[0];?></span>
                <div class="tm"><?php echo $isOff?'Not Scheduled':mr_fmt_time($list[0]['start']).' - '.mr_fmt_time($list[0]['end']);?></div>
                <?php if(!$isOff): ?>
                    <div class="rl"><?php echo htmlspecialchars($list[0]['role']?:'—');?></div>
                    <div class="lc"><span class="mr-dot" style="background:<?php echo $t[1];?>"></span><?php echo htmlspecialchars($list[0]['area']?:($locationName?:''));?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="mr-grid2">
    <div class="mr-panel">
        <h6>Upcoming Shifts</h6>
        <?php if (empty($upcoming)): ?><div class="mr-empty">No upcoming shifts.</div><?php endif; ?>
        <?php foreach ($upcoming as $u): ?>
            <?php
                $daysAway = (int) floor((strtotime($u['date']) - strtotime(date('Y-m-d'))) / 86400);
                if ($daysAway <= 0) { $rel = 'Today'; $relC = 'color:#25A69A;background:rgba(37,166,154,.15)'; }
                elseif ($daysAway === 1) { $rel = 'Tomorrow'; $relC = 'color:#f59e0b;background:rgba(245,158,11,.15)'; }
                else { $rel = 'In '.$daysAway.' days'; $relC = 'color:#3b82f6;background:rgba(59,130,246,.15)'; }
            ?>
            <div class="mr-up">
                <div class="dt"><div class="d"><?php echo date('j',strtotime($u['date']));?></div><div class="m"><?php echo date('D',strtotime($u['date']));?></div></div>
                <div class="meta"><b><?php echo mr_shift_type($u['start'])[0];?> Shift</b><small><?php echo mr_fmt_time($u['start']).' - '.mr_fmt_time($u['end']).' ('.number_format($u['hours'],2).' hrs)';?></small><small><?php echo htmlspecialchars($u['area']?:($locationName?:''));?></small></div>
                <span class="rel" style="<?php echo $relC;?>"><?php echo $rel;?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="mr-panel">
        <h6>Roster Period Summary</h6>
        <div class="mr-srow"><span>Total Scheduled Hours</span><span class="v"><?php echo number_format($totalHours,2);?> hrs</span></div>
        <div class="mr-srow"><span>Total Shifts</span><span class="v"><?php echo (int)$shiftCount;?></span></div>
        <div class="mr-srow"><span>Average Shift Length</span><span class="v"><?php echo number_format($avgShift,2);?> hrs</span></div>
        <div class="mr-srow"><span>Days Scheduled</span><span class="v"><?php echo (int)$daysScheduled;?> / 7</span></div>
    </div>
    <div class="mr-panel">
        <h6>Legend</h6>
        <div class="mr-leg"><span class="mr-dot" style="background:#25A69A"></span> Morning Shift <span style="margin-left:auto;color:#94a3b8">Before 12:00 PM</span></div>
        <div class="mr-leg"><span class="mr-dot" style="background:#f59e0b"></span> Afternoon Shift <span style="margin-left:auto;color:#94a3b8">12:00 - 4:00 PM</span></div>
        <div class="mr-leg"><span class="mr-dot" style="background:#3b82f6"></span> Evening Shift <span style="margin-left:auto;color:#94a3b8">4:00 PM - Close</span></div>
        <div class="mr-leg"><span class="mr-dot" style="background:#94a3b8"></span> Not Scheduled <span style="margin-left:auto;color:#94a3b8">No shifts assigned</span></div>
    </div>
</div>

<script>
function mrLoadWeek(start){
    var l=document.getElementById('mrLoader'); if(l)l.classList.add('show');
    fetch('<?php echo site_url('HR/myRoster'); ?>?ajax=1&start_date='+encodeURIComponent(start))
        .then(function(r){return r.text();})
        .then(function(html){
            var wrap=document.getElementById('myRoster');
            wrap.outerHTML=html;
        })
        .catch(function(){ if(l)l.classList.remove('show'); });
}
function mrScroll(dir){
    var t=document.getElementById('mrCardsTrack');
    if(!t)return;
    var card=t.querySelector('.mr-card');
    var w=card?card.offsetWidth+12:260;
    t.scrollBy({left:dir*w,behavior:'smooth'});
}
</script>
</div>
