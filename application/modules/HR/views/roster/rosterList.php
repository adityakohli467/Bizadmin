 <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rosters</title>
    <link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
    <?php $this->load->view('general/tailwind_common_assets'); ?>
</head>
<body class="bg-gray-100">

<main id="main-content" class="px-6 py-10">
    <div id="roster-container" class="max-w-7xl mx-auto bg-white rounded-lg shadow-md p-6">
        
        <?php if ($this->session->flashdata('success_message')): ?>
            <div class="alert bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $this->session->flashdata('success_message'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error_message')): ?>
            <div class="alert bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $this->session->flashdata('error_message'); ?>
            </div>
        <?php endif; ?>

        <div id="page-header" class="mb-6">
            <h1 class="text-2xl font-bold text-black">ROSTERS</h1>
        </div>
        
        <div id="toolbar-section" class="flex items-center justify-end space-x-4 mb-6">
            <?php if(!isset($roleId) || $roleId != 4) { ?>
            <a href="/HR/rosterForm" class="bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded-md text-white font-medium text-xs transition">
                <i class="fa-solid fa-plus"></i>
                <span class="font-medium">Add Roster</span>
            </a>
            <?php } ?>
            
            
        </div>
        
        <div id="table-section" class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden" id="rosterListDatatable">
                <thead>
                    <tr class="bg-navy text-white">
                        <th class="px-6 py-4 text-left text-sm font-semibold">
                            <div class="flex items-center space-x-2">
                                <span>Roster Id</span>
                                <i class="fa-solid fa-sort text-xs"></i>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">
                            <div class="flex items-center space-x-2">
                                <span>Roster Name</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">
                            <div class="flex items-center space-x-2">
                                <span>Roster Week</span>
                                <i class="fa-solid fa-sort text-xs"></i>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-semibold no-sort">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if(!empty($rosterList)) { ?>
                        <?php foreach($rosterList as $roster){ ?>
                            <?php
                            $start_datetime = new DateTime($roster['start_date']);
                            $end_datetime = new DateTime($roster['end_date']);
                            
                            $start_formatted = $start_datetime->format('jS F');
                            $end_formatted = $end_datetime->format('jS F');
                            $week_range = $start_formatted . ' - ' . $end_formatted;
                            
                            $dates = explode(' to ', $roster['rosterName']);
                            $from = date('d-m-Y', strtotime($dates[0]));
                            $to = date('d-m-Y', strtotime($dates[1]));
                            ?>

                            <tr id="row_<?php echo $roster['roster_id']; ?>" class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <?php echo $roster['roster_id']; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <?php echo $from . ' to ' . $to; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <?php echo $week_range; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="/HR/rosterView/<?php echo $roster['roster_id']; ?>" class="px-3 py-1.5 rounded-md text-white font-medium text-xs transition bg-green-500 hover:bg-green-600">
                                            <i class="fa-solid fa-eye"></i>
                                            <span>View</span>
                                        </a>
                                        
                                        <?php if(!isset($roleId) || $roleId != 4) { ?>
                                        <button class="px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-md font-medium text-xs transition shadow-sm" onclick="showRosterRecreateModal(<?php echo $roster['roster_id'] ?>)">
                                            <i class="fa-solid fa-copy mr-1"></i>
                                            Recreate
                                        </button>
                                        
                                        <button class="px-3 py-1.5 rounded-md text-white font-medium text-xs transition bg-red-500 hover:bg-red-600 remove-item-btn" data-rel-id="<?php echo $roster['roster_id']; ?>">
                                            <i class="fa-solid fa-trash"></i>
                                            <span>Delete</span>
                                        </button>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                No rosters found.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div class="modal fade" id="recreateRosterModal" tabindex="-1" aria-labelledby="recreateRoster" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recreateRoster">Select date for roster</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo base_url('/HR/recreateRoster') ?>" method="post" id="recreateRosterForm">
                <div class="modal-body">
                    <input type="hidden" name="roster_id" class="recreate_roster_id">
                    <div class="mb-3">
                        <label for="startDate" class="col-form-label">Roster Start Date:</label>
                        <input type="text" name="start_date" id="startdatepicker" class="form-control flatpickr-input border-gray-300 rounded-lg p-2 text-sm" data-provider="flatpickr" data-date-format="d M, Y" readonly="readonly">
                    </div>
                    <div class="mb-3">
                        <label for="endDate" class="col-form-label">Roster End Date:</label>
                        <input type="text" name="end_date" id="enddatepicker" class="form-control flatpickr-input border-gray-300 rounded-lg p-2 text-sm" data-provider="flatpickr" data-date-format="d M, Y" readonly="readonly">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light px-3 py-1.5 text-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success px-3 py-1.5 text-sm">Recreate</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>

<script>
// Remove roster localstorage that we save while creating
window.onload = function() {
    const keysToRemove = Object.keys(localStorage).filter(key => key.startsWith('emp_'));
    keysToRemove.forEach(key => localStorage.removeItem(key));
};

$(document).ready(function(){
    // Automatically close flash messages after 5 seconds
    setTimeout(function() {
        $(".alert").fadeOut("slow");
    }, 4000);
  
    flatpickr("#startdatepicker", {
        dateFormat: "d M, Y",
        disable: [
            function(date) {
                return (date.getDay() !== 1);
            }
        ]
    });
    
    flatpickr("#enddatepicker", {
        dateFormat: "d M, Y",
        disable: [
            function(date) {
                return (date.getDay() !== 0);
            }
        ]
    });

    $('#recreateRosterForm').on('submit', function() {
        $('#loaderContainer').show();
    });
});

function showRosterRecreateModal(roster_id){
    $(".recreate_roster_id").val(roster_id);
    $("#recreateRosterModal").modal("show");
}
</script>

<script>
$('#rosterListDatatable').DataTable({
    pageLength: 100,
    bPaginate: false,
    bInfo: false,
    lengthMenu: [0, 5, 10, 20, 50, 100, 200, 500],
    columnDefs: [{
        targets: 'no-sort',
        orderable: false
    }],
    order: [[0, 'desc']]
});

$(document).on("click", ".remove-item-btn", function() {
    let rosterId = $(this).attr('data-rel-id');
    Swal.fire({
        title: "Are you sure?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn btn-primary w-xs me-2 mt-2",
        cancelButtonClass: "btn btn-danger w-xs mt-2",
        confirmButtonText: "Yes, delete it!",
        buttonsStyling: false,
        showCloseButton: true,
    }).then(function (e) {
        if (e.value) {
            $.ajax({
                type: "POST",
                url: "/HR/Roster/deleteRoster",
                data: 'rosterId=' + rosterId,
                success: function(data){
                    $('#row_' + rosterId).remove();
                }
            });
        }
    });
});
</script>
 