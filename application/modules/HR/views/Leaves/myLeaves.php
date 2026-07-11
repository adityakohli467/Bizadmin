<?php
// $empId, $empName, $leaves, $balances, $csrf_name, $csrf_hash passed from controller
$leaves   = isset($leaves) && is_array($leaves) ? $leaves : [];
$balances = isset($balances) && is_array($balances) ? $balances : [];

$total_leaves   = count($leaves);
$pending_leaves = 0;
$approved_leaves = 0;
$rejected_leaves = 0;
foreach ($leaves as $lv) {
    $st = (int)($lv['leave_status'] ?? 0);
    if ($st === 1) { $pending_leaves++; }
    elseif ($st === 2) { $approved_leaves++; }
    elseif ($st === 3) { $rejected_leaves++; }
}
?>
<?php /* My Leaves: rendered inside the shared Velzon layout (header > content > footer). Page-specific assets only. */ ?>
<link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
<?php $this->load->view('general/tailwind_common_assets'); ?>
<style>
    .my-leaves .text-gray-900 { color:#111827 !important; }
    .my-leaves .text-gray-800 { color:#1f2937 !important; }
    .my-leaves .text-gray-700 { color:#374151 !important; }
    .my-leaves .text-gray-600 { color:#4b5563 !important; }
    .my-leaves .text-gray-500 { color:#6b7280 !important; }
    .my-leaves { font-family:'Inter', sans-serif; }
</style>

<main class="my-leaves w-full pb-8" style="padding-top:90px;">
  <div class="px-8">

    <!-- Page header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-calendar-check text-xl" style="color:#111827;"></i>
            <h1 class="text-2xl font-bold" style="color:#111827;">My Leaves</h1>
        </div>
    </div>

    <!-- Summary stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
            <h3 class="text-3xl font-bold mb-1" style="color:#111827;"><?php echo $total_leaves; ?></h3>
            <p class="text-sm text-gray-600">Total Requests</p>
        </div>
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
            <h3 class="text-3xl font-bold mb-1" style="color:#111827;"><?php echo $pending_leaves; ?></h3>
            <p class="text-sm text-gray-600">Pending</p>
        </div>
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
            <h3 class="text-3xl font-bold mb-1" style="color:#111827;"><?php echo $approved_leaves; ?></h3>
            <p class="text-sm text-gray-600">Approved</p>
        </div>
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
            <h3 class="text-3xl font-bold mb-1" style="color:#111827;"><?php echo $rejected_leaves; ?></h3>
            <p class="text-sm text-gray-600">Rejected</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Leave history -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Leave History</h3>
            </div>

            <?php if (empty($leaves)): ?>
                <div class="p-10 text-center text-gray-500">
                    <i class="fa-solid fa-inbox text-4xl mb-3 text-gray-300"></i>
                    <p class="text-sm">You haven't applied for any leaves yet.</p>
                </div>
            <?php else: ?>
                <div class="divide-y divide-gray-200">
                    <?php foreach ($leaves as $lv): ?>
                        <?php
                            $st = (int)($lv['leave_status'] ?? 0);
                            $status_label = ($st == 1) ? 'Pending' : (($st == 2) ? 'Approved' : 'Rejected');
                            $badge_class  = ($st == 1) ? 'bg-orange-100 text-orange-700' : (($st == 2) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700');
                            $days = 0;
                            if (!empty($lv['start_date']) && !empty($lv['end_date'])) {
                                $days = (int)round((strtotime($lv['end_date']) - strtotime($lv['start_date'])) / 86400) + 1;
                            }
                        ?>
                    <div class="p-6 hover:bg-gray-50">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-umbrella-beach text-indigo-600"></i>
                                <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($lv['leaveTypeName'] ?? 'N/A'); ?></h4>
                            </div>
                            <span class="px-3 py-1 <?php echo $badge_class; ?> text-xs font-semibold rounded-full"><?php echo $status_label; ?></span>
                        </div>
                        <div class="flex flex-wrap items-center gap-6 text-sm text-gray-600 mb-2">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-calendar"></i>
                                <span><?php echo (!empty($lv['start_date']) ? date('M d, Y', strtotime($lv['start_date'])) : '') . ' - ' . (!empty($lv['end_date']) ? date('M d, Y', strtotime($lv['end_date'])) : ''); ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-clock"></i>
                                <span><?php echo $days; ?> day<?php echo $days == 1 ? '' : 's'; ?></span>
                            </div>
                            <?php if (!empty($lv['date_added'])): ?>
                            <div class="flex items-center gap-2 text-gray-400">
                                <i class="fa-solid fa-paper-plane"></i>
                                <span>Applied <?php echo date('M d, Y', strtotime($lv['date_added'])); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($lv['leaveComments'])): ?>
                        <p class="text-sm text-gray-600 mb-2"><span class="font-medium">Reason:</span> <?php echo htmlspecialchars($lv['leaveComments']); ?></p>
                        <?php endif; ?>
                        <?php if ($st == 3 && !empty($lv['approver_comment'])): ?>
                        <p class="text-sm text-red-600 mb-2"><span class="font-medium">Manager note:</span> <?php echo htmlspecialchars($lv['approver_comment']); ?></p>
                        <?php elseif ($st == 2 && !empty($lv['approver_comment'])): ?>
                        <p class="text-sm text-green-700 mb-2"><span class="font-medium">Manager note:</span> <?php echo htmlspecialchars($lv['approver_comment']); ?></p>
                        <?php endif; ?>
                        <?php if ($st == 1): ?>
                        <button data-id="<?php echo $lv['id']; ?>" class="cancel-leave-btn mt-1 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">
                            <i class="fa-solid fa-ban mr-2"></i>Cancel Request
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Leave balances -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Leave Balance</h3>
            </div>
            <div class="p-6 space-y-5">
                <?php if (empty($balances)): ?>
                    <p class="text-sm text-gray-500">No leave types configured.</p>
                <?php else: ?>
                    <?php foreach ($balances as $b): ?>
                        <?php
                            $entitlement = (float)($b['entitlements'] ?? 0);
                            $used        = (float)($b['used_days'] ?? 0);
                            $remaining   = isset($b['remaining']) ? (float)$b['remaining'] : max(0, $entitlement - $used);
                            $percent     = $entitlement > 0 ? min(100, round(($used / $entitlement) * 100)) : 0;
                        ?>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($b['leaveTypeName'] ?? ''); ?></span>
                            <span class="text-sm text-gray-600"><?php echo $used; ?> / <?php echo $entitlement; ?> days</span>
                        </div>
                        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-2 bg-indigo-500 rounded-full" style="width:<?php echo $percent; ?>%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1"><?php echo $remaining; ?> day<?php echo $remaining == 1 ? '' : 's'; ?> remaining</p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

  </div>
</main>

<script>
const leaveBaseUrl = '<?php echo site_url('HR/Leave'); ?>';
const leaveCsrfName = '<?php echo $csrf_name; ?>';
const leaveCsrfHash = '<?php echo $csrf_hash; ?>';

document.addEventListener('click', function (e) {
    const cancelEl = e.target.closest('.cancel-leave-btn');
    if (!cancelEl) return;
    const id = cancelEl.dataset.id;
    if (!confirm('Cancel this leave request?')) return;
    fetch(leaveBaseUrl + '/cancelLeave', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `id=${encodeURIComponent(id)}&${encodeURIComponent(leaveCsrfName)}=${encodeURIComponent(leaveCsrfHash)}`
    }).then(function () {
        location.reload();
    }).catch(function () {
        alert('Could not cancel the request. Please try again.');
    });
});
</script>
