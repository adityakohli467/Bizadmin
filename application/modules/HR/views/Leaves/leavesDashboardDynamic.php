<?php
// $summary, $recent_requests, $csrf_name, $csrf_hash passed from controller
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Leave Dashboard</title>
  <link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
  <style>body{font-family:Inter, sans-serif}::-webkit-scrollbar{display:none}</style>
</head>
<body class="bg-gray-50">

<main class="pt-16 p-8">
    <div id="stats-section" class="mb-8">
        <div class="grid grid-cols-4 gap-6">
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-clock text-orange-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded-full">+3 Today</span>
                </div>
                <h3 id="pending_count" class="text-3xl font-bold text-gray-900 mb-1"><?php echo isset($summary['pending']) ? $summary['pending'] : 0; ?></h3>
                <p class="text-sm text-gray-600">Pending Requests</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">This Month</span>
                </div>
                <h3 id="approved_count" class="text-3xl font-bold text-gray-900 mb-1"><?php echo isset($summary['approved']) ? $summary['approved'] : 0; ?></h3>
                <p class="text-sm text-gray-600">Approved Leaves</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-times-circle text-red-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded-full">This Month</span>
                </div>
                <h3 id="rejected_count" class="text-3xl font-bold text-gray-900 mb-1"><?php echo isset($summary['rejected']) ? $summary['rejected'] : 0; ?></h3>
                <p class="text-sm text-gray-600">Rejected Leaves</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-calendar-day text-indigo-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full">Next 30 Days</span>
                </div>
                <h3 id="upcoming_count" class="text-3xl font-bold text-gray-900 mb-1"><?php echo isset($summary['upcoming']) ? $summary['upcoming'] : 0; ?></h3>
                <p class="text-sm text-gray-600">Upcoming Leaves</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6 mb-8">
        <div class="col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Recent Leave Requests</h3>
                <a href="#" class="text-sm font-medium text-indigo-600">View All</a>
            </div>

            <div id="requests_list" class="divide-y divide-gray-200">
                <?php foreach ($recent_requests as $r): ?>
                    <?php
                        $employee_name = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));
                        $status_label = ($r['leave_status'] == 1) ? 'Pending' : (($r['leave_status'] == 2) ? 'Approved' : 'Rejected');
                        $badge_class = ($r['leave_status'] == 1) ? 'bg-orange-100 text-orange-700' : (($r['leave_status'] == 2) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700');
                    ?>
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h4 class="font-semibold text-gray-900"><?php echo $employee_name; ?></h4>
                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($r['department'] ?? ''); ?></p>
                                </div>
                                <span class="px-3 py-1 <?php echo $badge_class; ?> text-xs font-semibold rounded-full"><?php echo $status_label; ?></span>
                            </div>
                            <div class="flex items-center gap-6 text-sm text-gray-600 mb-3">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-umbrella-beach text-indigo-600"></i>
                                    <span><?php echo htmlspecialchars($r['leaveTypeName'] ?? ''); ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-calendar"></i>
                                    <span><?php echo date('M d, Y', strtotime($r['start_date'])) . ' - ' . date('M d, Y', strtotime($r['end_date'])); ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-clock"></i>
                                    <span><?php echo ( (strtotime($r['end_date']) - strtotime($r['start_date'])) / 86400 ) + 1; ?> days</span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars($r['leaveComments'] ?? ''); ?></p>
                            <div class="flex items-center gap-2">
                                <?php if ($r['leave_status'] == 1): ?>
                                <button data-id="<?php echo $r['id']; ?>" class="approve-btn px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg">
                                    <i class="fa-solid fa-check mr-2"></i>Approve
                                </button>
                                <button data-id="<?php echo $r['id']; ?>" class="reject-btn px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm rounded-lg">
                                    <i class="fa-solid fa-times mr-2"></i>Reject
                                </button>
                                <?php endif; ?>
                                <button data-id="<?php echo $r['id']; ?>" class="details-btn px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm rounded-lg">
                                    <i class="fa-solid fa-eye mr-2"></i>View Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-bold mb-4">Notifications & Alerts</h3>
            <div id="alerts">
                <div class="p-4 border-b">
                    <p class="text-sm font-semibold">Overlapping Leave Conflict</p>
                    <p class="text-xs text-gray-600">3 employees from Engineering team have overlapping leaves Dec 20-27</p>
                </div>
                <div class="p-4 border-b">
                    <p class="text-sm font-semibold">New Leave Request</p>
                    <p class="text-xs text-gray-600">Aditya Singh submitted annual leave request for 8 days</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Modal (hidden) -->
    <div id="leaveModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg w-11/12 max-w-3xl p-6">
            <div id="modalContent"></div>
            <div class="mt-4 text-right">
                <button id="modalClose" class="px-4 py-2 bg-gray-200 rounded">Close</button>
            </div>
        </div>
    </div>

</main>

<script>
const baseUrl = '<?php echo site_url('leave'); ?>'; // module route
const csrfName = '<?php echo $csrf_name;?>';
const csrfHash = '<?php echo $csrf_hash;?>';

document.addEventListener('click', function(e){
    if (e.target.closest('.approve-btn')) {
        const id = e.target.closest('.approve-btn').dataset.id;
        if (!confirm('Approve this leave?')) return;
        fetch(baseUrl + '/approve', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `${encodeURIComponent('id')}=${id}&${encodeURIComponent(csrfName)}=${encodeURIComponent(csrfHash)}`
        }).then(r=>r.json()).then(res=>{
            if (res.success) location.reload();
            else alert(res.message || 'Failed');
        });
    }

    if (e.target.closest('.reject-btn')) {
        const id = e.target.closest('.reject-btn').dataset.id;
        const comment = prompt('Please provide reason for rejection (required):');
        if (comment === null) return;
        if (!comment.trim()) { alert('Reject comment required'); return; }
        const body = `id=${id}&comment=${encodeURIComponent(comment)}&${encodeURIComponent(csrfName)}=${encodeURIComponent(csrfHash)}`;
        fetch(baseUrl + '/reject', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body
        }).then(r=>r.json()).then(res=>{
            if (res.success) location.reload();
            else alert(res.message || 'Failed');
        });
    }

    if (e.target.closest('.details-btn')) {
        const id = e.target.closest('.details-btn').dataset.id;
        fetch(baseUrl + '/details/' + id, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
            .then(r=>r.json()).then(res=>{
                if (!res.success) { alert(res.message || 'No data'); return; }
                const d = res.data;
                const html = `
                    <h3 class="text-xl font-semibold mb-2">${(d.first_name||'') + ' ' + (d.last_name||'')}</h3>
                    <p class="text-sm text-gray-600 mb-2">Type: ${d.leaveTypeName || ''}</p>
                    <p class="text-sm text-gray-600 mb-2">Dates: ${d.start_date} - ${d.end_date}</p>
                    <p class="text-sm text-gray-600 mb-2">Comments: ${d.leaveComments || ''}</p>
                    <p class="text-sm text-gray-600 mb-2">Status: ${d.leave_status}</p>
                `;
                document.getElementById('modalContent').innerHTML = html;
                document.getElementById('leaveModal').classList.remove('hidden');
            });
    }
});

// modal close
document.getElementById('modalClose').addEventListener('click', function(){
    document.getElementById('leaveModal').classList.add('hidden');
});
</script>
</body>
</html>
