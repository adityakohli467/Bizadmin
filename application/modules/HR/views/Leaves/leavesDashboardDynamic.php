<?php
// $summary, $recent_requests, $csrf_name, $csrf_hash passed from controller
$summary = isset($summary) && is_array($summary) ? $summary : [];
$recent_requests = isset($recent_requests) && is_array($recent_requests) ? $recent_requests : [];

$pending_count  = isset($summary['pending'])  ? (int)$summary['pending']  : 0;
$approved_count = isset($summary['approved']) ? (int)$summary['approved'] : 0;
$rejected_count = isset($summary['rejected']) ? (int)$summary['rejected'] : 0;
$upcoming_count = isset($summary['upcoming']) ? (int)$summary['upcoming'] : 0;

// Build dynamic notifications from the most recent pending requests
$pending_requests = array_values(array_filter($recent_requests, function ($r) {
    return isset($r['leave_status']) && (int)$r['leave_status'] === 1;
}));
?>
<?php /* Leave dashboard: rendered inside the shared Velzon layout (header > content > footer). Page-specific assets only. */ ?>
<link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
<?php $this->load->view('general/tailwind_common_assets'); ?>
<style>
    /* Keep text visible regardless of theme overrides */
    .leave-dash .text-gray-900 { color:#111827 !important; }
    .leave-dash .text-gray-800 { color:#1f2937 !important; }
    .leave-dash .text-gray-700 { color:#374151 !important; }
    .leave-dash .text-gray-600 { color:#4b5563 !important; }
    .leave-dash .text-gray-500 { color:#6b7280 !important; }
    .leave-dash { font-family:'Inter', sans-serif; }
    /* Details modal: keep table text dark (theme makes th white) */
    #modalContent th { color:#374151 !important; background:transparent !important; }
    #modalContent td { color:#111827 !important; }
    #modalContent p, #modalContent h3 { color:#111827 !important; }
</style>

<main class="leave-dash w-full pb-8" style="padding-top:90px; padding-bottom:60px;">
  <div class="px-8">

    <!-- Page header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-plane-departure text-xl" style="color:#111827;"></i>
            <h1 class="text-2xl font-bold" style="color:#111827;">Leave Management</h1>
        </div>
    </div>
    <div id="stats-section" class="mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-1">
                    <h3 id="pending_count" class="text-3xl font-bold" style="color:#ea580c;"><?php echo $pending_count; ?></h3>
                    <span class="text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded-full">Awaiting</span>
                </div>
                <p class="text-sm text-gray-600">Pending Requests</p>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-1">
                    <h3 id="approved_count" class="text-3xl font-bold" style="color:#16a34a;"><?php echo $approved_count; ?></h3>
                    <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">Total</span>
                </div>
                <p class="text-sm text-gray-600">Approved Leaves</p>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-1">
                    <h3 id="rejected_count" class="text-3xl font-bold" style="color:#dc2626;"><?php echo $rejected_count; ?></h3>
                    <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded-full">Total</span>
                </div>
                <p class="text-sm text-gray-600">Rejected Leaves</p>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-1">
                    <h3 id="upcoming_count" class="text-3xl font-bold" style="color:#4f46e5;"><?php echo $upcoming_count; ?></h3>
                    <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full">Next 30 Days</span>
                </div>
                <p class="text-sm text-gray-600">Upcoming Leaves</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Recent Leave Requests</h3>
                <span class="text-sm font-medium text-gray-500"><?php echo count($recent_requests); ?> total</span>
            </div>

            <div id="requests_list" class="divide-y divide-gray-200">
                <?php if (empty($recent_requests)): ?>
                    <div class="p-10 text-center text-gray-500">
                        <i class="fa-solid fa-inbox text-4xl mb-3 text-gray-300"></i>
                        <p class="text-sm">No leave requests yet. New employee requests will appear here.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($recent_requests as $r): ?>
                        <?php
                            $employee_name = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));
                            if ($employee_name === '') { $employee_name = 'Employee #' . ($r['emp_id'] ?? ''); }
                            $status_int   = (int)($r['leave_status'] ?? 0);
                            $status_label = ($status_int == 1) ? 'Pending' : (($status_int == 2) ? 'Approved' : 'Rejected');
                            $badge_class  = ($status_int == 1) ? 'bg-orange-100 text-orange-700' : (($status_int == 2) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700');
                            $days = 0;
                            if (!empty($r['start_date']) && !empty($r['end_date'])) {
                                $days = (int)round((strtotime($r['end_date']) - strtotime($r['start_date'])) / 86400) + 1;
                            }
                        ?>
                    <div class="p-6 hover:bg-gray-50">
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($employee_name); ?></h4>
                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($r['preferred_name'] ?? ($r['email'] ?? '')); ?></p>
                                </div>
                                <span class="px-3 py-1 <?php echo $badge_class; ?> text-xs font-semibold rounded-full"><?php echo $status_label; ?></span>
                            </div>
                            <div class="flex flex-wrap items-center gap-6 text-sm text-gray-600 mb-3">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-umbrella-beach text-indigo-600"></i>
                                    <span><?php echo htmlspecialchars($r['leaveTypeName'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-calendar"></i>
                                    <span><?php echo (!empty($r['start_date']) ? date('M d, Y', strtotime($r['start_date'])) : '') . ' - ' . (!empty($r['end_date']) ? date('M d, Y', strtotime($r['end_date'])) : ''); ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-clock"></i>
                                    <span><?php echo $days; ?> day<?php echo $days == 1 ? '' : 's'; ?></span>
                                </div>
                            </div>
                            <?php if (!empty($r['leaveComments'])): ?>
                            <p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars($r['leaveComments']); ?></p>
                            <?php endif; ?>
                            <div class="flex items-center gap-2">
                                <?php if ($status_int == 1): ?>
                                <button data-id="<?php echo $r['id']; ?>" class="approve-btn px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                                    <i class="fa-solid fa-check mr-2"></i>Approve
                                </button>
                                <button data-id="<?php echo $r['id']; ?>" class="reject-btn px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">
                                    <i class="fa-solid fa-times mr-2"></i>Reject
                                </button>
                                <?php endif; ?>
                                <button data-id="<?php echo $r['id']; ?>" class="details-btn px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">
                                    <i class="fa-solid fa-eye mr-2"></i>View Details
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Notifications &amp; Alerts</h3>
            </div>
            <div id="alerts" class="divide-y divide-gray-200">
                <?php if (empty($pending_requests)): ?>
                    <div class="p-6 text-center text-gray-500">
                        <i class="fa-solid fa-bell-slash text-3xl mb-2 text-gray-300"></i>
                        <p class="text-sm">No pending notifications.</p>
                    </div>
                <?php else: ?>
                    <?php foreach (array_slice($pending_requests, 0, 6) as $pr): ?>
                        <?php
                            $pname = trim(($pr['first_name'] ?? '') . ' ' . ($pr['last_name'] ?? ''));
                            if ($pname === '') { $pname = 'An employee'; }
                            $pdays = 0;
                            if (!empty($pr['start_date']) && !empty($pr['end_date'])) {
                                $pdays = (int)round((strtotime($pr['end_date']) - strtotime($pr['start_date'])) / 86400) + 1;
                            }
                        ?>
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-bell text-orange-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 mb-1">New Leave Request</p>
                                <p class="text-xs text-gray-600"><?php echo htmlspecialchars($pname); ?> requested <?php echo htmlspecialchars($pr['leaveTypeName'] ?? 'leave'); ?> for <?php echo $pdays; ?> day<?php echo $pdays == 1 ? '' : 's'; ?>.</p>
                                <?php if (!empty($pr['date_added'])): ?>
                                <p class="text-xs text-gray-400 mt-1"><?php echo date('M d, Y H:i', strtotime($pr['date_added'])); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Details Modal (hidden) -->
    <div id="leaveModal" class="fixed inset-0 z-50 hidden items-start justify-center bg-black bg-opacity-50" style="padding-top:80px; padding-bottom:40px;">
        <div class="bg-white rounded-lg w-11/12 max-w-2xl p-6 overflow-y-auto" style="max-height:85vh;">
            <div id="modalContent"></div>
            <div class="mt-4 text-right">
                <button id="modalClose" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Close</button>
            </div>
        </div>
    </div>

  </div>
</main>

<script>
const baseUrl  = '<?php echo site_url('HR/Leave'); ?>';
const csrfName = '<?php echo $csrf_name; ?>';
const csrfHash = '<?php echo $csrf_hash; ?>';

const ajaxHeaders = {
    'Content-Type': 'application/x-www-form-urlencoded',
    'X-Requested-With': 'XMLHttpRequest'
};

document.addEventListener('click', function (e) {
    const approveEl = e.target.closest('.approve-btn');
    const rejectEl  = e.target.closest('.reject-btn');
    const detailsEl = e.target.closest('.details-btn');

    if (approveEl) {
        const id = approveEl.dataset.id;
        if (!confirm('Approve this leave request?')) return;
        fetch(baseUrl + '/approve', {
            method: 'POST',
            headers: ajaxHeaders,
            body: `id=${encodeURIComponent(id)}&${encodeURIComponent(csrfName)}=${encodeURIComponent(csrfHash)}`
        }).then(r => r.json()).then(res => {
            if (res.success) location.reload();
            else alert(res.message || 'Failed to approve');
        }).catch(() => alert('Request failed'));
    }

    if (rejectEl) {
        const id = rejectEl.dataset.id;
        const comment = prompt('Please provide a reason for rejection (required):');
        if (comment === null) return;
        if (!comment.trim()) { alert('A rejection reason is required'); return; }
        fetch(baseUrl + '/reject', {
            method: 'POST',
            headers: ajaxHeaders,
            body: `id=${encodeURIComponent(id)}&comment=${encodeURIComponent(comment)}&${encodeURIComponent(csrfName)}=${encodeURIComponent(csrfHash)}`
        }).then(r => r.json()).then(res => {
            if (res.success) location.reload();
            else alert(res.message || 'Failed to reject');
        }).catch(() => alert('Request failed'));
    }

    if (detailsEl) {
        const id = detailsEl.dataset.id;
        fetch(baseUrl + '/details/' + id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json()).then(res => {
                if (!res.success) { alert(res.message || 'No data'); return; }
                const d = res.data || {};
                const balances = res.balances || [];
                const hist = res.history || {};
                const name = ((d.first_name || '') + ' ' + (d.last_name || '')).trim() || 'Employee';
                const statusMap = { 1: 'Pending', 2: 'Approved', 3: 'Rejected' };

                let balanceRows = '';
                if (balances.length) {
                    balanceRows = balances.map(b => {
                        const ent = parseFloat(b.entitlements || 0);
                        const used = parseFloat(b.used_days || 0);
                        const rem = (b.remaining !== undefined) ? parseFloat(b.remaining) : Math.max(0, ent - used);
                        return `<tr class="border-b border-gray-100">
                                    <td class="py-1 pr-4">${b.leaveTypeName || ''}</td>
                                    <td class="py-1 pr-4 text-center">${used}</td>
                                    <td class="py-1 pr-4 text-center">${ent}</td>
                                    <td class="py-1 text-center font-medium">${rem}</td>
                                </tr>`;
                    }).join('');
                } else {
                    balanceRows = `<tr><td colspan="4" class="py-2 text-gray-500">No balance data.</td></tr>`;
                }

                const html = `
                    <h3 class="text-xl font-semibold text-gray-900 mb-1">${name}</h3>
                    <p class="text-sm text-gray-500 mb-4">${d.email || ''}</p>

                    <div class="grid grid-cols-4 gap-3 mb-4">
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-lg font-bold text-gray-900">${hist.total || 0}</div>
                            <div class="text-xs text-gray-500">Total</div>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-3 text-center">
                            <div class="text-lg font-bold text-orange-600">${hist.pending || 0}</div>
                            <div class="text-xs text-gray-500">Pending</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3 text-center">
                            <div class="text-lg font-bold text-green-600">${hist.approved || 0}</div>
                            <div class="text-xs text-gray-500">Approved</div>
                        </div>
                        <div class="bg-indigo-50 rounded-lg p-3 text-center">
                            <div class="text-lg font-bold text-indigo-600">${hist.approved_days || 0}</div>
                            <div class="text-xs text-gray-500">Days taken</div>
                        </div>
                    </div>

                    <div class="space-y-2 text-sm text-gray-700 mb-4">
                        <p><span class="font-medium">Leave Type:</span> ${d.leaveTypeName || 'N/A'}</p>
                        <p><span class="font-medium">Dates:</span> ${d.start_date || ''} to ${d.end_date || ''}</p>
                        <p><span class="font-medium">Reason:</span> ${d.leaveComments || '-'}</p>
                        <p><span class="font-medium">Status:</span> ${statusMap[d.leave_status] || 'Unknown'}</p>
                        ${d.approver_comment ? `<p><span class="font-medium">Manager Comment:</span> ${d.approver_comment}</p>` : ''}
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-900 mb-2">Leave Balance</p>
                        <table class="w-full text-sm text-gray-700">
                            <thead>
                                <tr class="text-xs text-gray-500 border-b border-gray-200">
                                    <th class="text-left py-1 pr-4">Type</th>
                                    <th class="py-1 pr-4">Used</th>
                                    <th class="py-1 pr-4">Entitled</th>
                                    <th class="py-1">Remaining</th>
                                </tr>
                            </thead>
                            <tbody>${balanceRows}</tbody>
                        </table>
                    </div>`;
                document.getElementById('modalContent').innerHTML = html;
                const modal = document.getElementById('leaveModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }).catch(() => alert('Request failed'));
    }
});

document.getElementById('modalClose').addEventListener('click', function () {
    const modal = document.getElementById('leaveModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
});
</script>
