<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timesheets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script> window.FontAwesomeConfig = { autoReplaceSvg: 'nest'};</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>::-webkit-scrollbar { display: none;}</style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'navy': '#1F3A61',
                        'magenta': '#B01271'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">



<main id="main-content" class="px-6 py-10">
    <div id="timesheet-container" class="max-w-7xl mx-auto bg-white rounded-lg shadow-md p-6">
        
        <div id="page-header" class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">TIMESHEETS</h1>
        </div>
        
        <div id="toolbar-section" class="flex items-center justify-end space-x-4 mb-6">
            <a href="/HR/addTimesheetWithoutRoster" class="bg-magenta hover:bg-[#8f0f5c] px-4 py-2 rounded-lg text-white font-medium text-sm transition">
                <i class="fa-solid fa-plus"></i>
                <span class="font-medium">Add Timesheet</span>
            </a>
            
            <div class="relative">
                <input 
                    type="text" 
                    placeholder="Search:" 
                    class="border border-gray-300 rounded-md px-4 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
            </div>
        </div>
        
        <div id="table-section" class="overflow-x-auto">
    <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
        <thead>
            <tr class="bg-navy text-white">
               
                <th class="px-6 py-4 text-left text-sm font-semibold">
                    <div class="flex items-center space-x-2">
                        <span>Timesheet Week</span>
                        <i class="fa-solid fa-sort text-xs"></i>
                    </div>
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                    Action
                </th>
            </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">

            <?php if (isset($timesheets) && is_array($timesheets) && count($timesheets) > 0): ?>
                <?php foreach ($timesheets as $t): ?>

                    <?php
                        // Safe values
                        $date_from = isset($t['date_from']) ? $t['date_from'] : '';
                        $date_to   = isset($t['date_to']) ? $t['date_to'] : '';

                        // For readable date format (17th November – 23rd November)
                        $fromFormatted = !empty($date_from) ? date("jS F", strtotime($date_from)) : '';
                        $toFormatted   = !empty($date_to) ? date("jS F", strtotime($date_to)) : '';
                    ?>

                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <?= $fromFormatted . ' – ' . $toFormatted ?>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <?php if(isset($generalConfigData['timesheetWORoster_toggle']) && $generalConfigData['timesheetWORoster_toggle'] == 1)  { ?>
                                <a href="/HR/viewTimesheetWithoutRoster/<?php echo  $t['id']; ?>" class="px-4 py-2 rounded-lg text-white font-medium text-sm transition bg-green-500 hover:bg-green-600">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>View</span>
                                </a>
                                
                                  <a href="/HR/addTimesheetWithoutRoster/<?php echo  $t['id']; ?>" class="px-4 py-2 rounded-lg text-white font-medium text-sm transition bg-red-500 hover:bg-red-600">
                                    <i class="fa-solid fa-pencil"></i>
                                    <span>Edit</span>
                                </a>
                                
                    <button class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-medium text-sm transition shadow-sm" onclick="showTimesheetRecreateModal(<?php echo  $t['id']; ?>)">
                        <i class="fa-solid fa-copy mr-2"></i>
                        Recreate
                     </button>
                    
                                <?php }  ?>

                                <a href="/HR/viewWeeklyTimesheet/<?php echo  $date_from; ?>/<?php echo  $date_to; ?>" class=" px-4 py-2 rounded-lg text-white font-medium text-sm transition bg-blue-500 hover:bg-blue-600">
                                    <span>Manage Timesheets</span>
                                </a>
                                
                       

                            <!-- Add this button in the Action column -->
<a href="/HR/Timesheet/payrollCalculation/<?php echo $t['id']; ?>" 
   class="px-4 py-2 rounded-lg text-white font-medium text-sm transition bg-purple-500 hover:bg-purple-600">
    <i class="fa-solid fa-calculator"></i>
    <span>Payroll</span>
</a>  

<a href="/HR/Timesheet/downloadWeeklyReport/<?php echo $date_from; ?>/<?php echo $date_to; ?>" 
   class="px-4 py-2 rounded-lg text-white font-medium text-sm transition bg-indigo-500 hover:bg-indigo-600">
    <i class="fa-solid fa-download"></i>
    <span>Download Report</span>
</a>

                            </div>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php else: ?>

                <tr>
                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                        No timesheets found.
                    </td>
                </tr>

            <?php endif; ?>

        </tbody>
    </table>
</div>

        
    </div>
</main>
<div class="modal fade" id="recreateTimesheetModal" tabindex="-1" aria-labelledby="recreateRoster" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recreateRoster">Select date for timesheet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form  method="post" id="recreateTimesheetForm">
                    <div class="modal-body">
                        <input type="hidden" name="recreate_timesheet_id" class="recreate_timesheet_id">
                        <div class="mb-3">
                            <label for="startDate" class="col-form-label"> Start Date:</label>
                            <input type="text" name="start_date" id="startdatepicker" class="form-control flatpickr-input border-gray-300 rounded-lg p-2 text-sm" data-provider="flatpickr" data-date-format="d M, Y" readonly="readonly">
                        </div>
                        <div class="mb-3">
                            <label for="endDate" class="col-form-label"> End Date:</label>
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

function showTimesheetRecreateModal(roster_id) {
            $(".recreate_timesheet_id").val(roster_id);
            $("#recreateTimesheetModal").modal("show");
        }
        
        
 $(document).ready(function() {
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

            $('#recreateTimesheetForm').on('submit', function() {
                $('#loaderContainer').show();
            });
        });
        
</script>
<script>
// Recreate timesheet 
$(document).ready(function() {
    $('#recreateTimesheetForm').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: '<?php echo base_url("HR/Timesheet/recreateTimesheet"); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(res) {
                if (res.status === 'error') {
                    alert(res.message);
                } else if (res.status === 'success') {
                    alert('Timesheet recreated successfully');
                    setTimeout(() => {
                     location.reload(true);   
                    }, 1000);
                }
            },
            error: function() {
                 alert('Something went wrong. Please try again.');
               
            }
        });
    });

  
});
</script>
