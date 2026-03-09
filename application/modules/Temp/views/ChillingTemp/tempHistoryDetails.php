<style>
.table td {
    width: 10%; 
}
/* CSS to make the table header fixed */
.fixed-table-header {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    z-index: 999;
    background-color: #fff; /* Background color of the fixed header */
}

</style>

<div class="container-fluid mb-5" style="margin-top: 130px !important;">
   <div class="row">
       
                        <div class="col-12 tempDiv">
                             <div class="card">
                                <div class="card-header align-items-center d-flex">
                                   
                                    
                                    <div class="flex-shrink-0">
                                        
                                        
                                    </div>
                                    <!--<i class='ri-file-download-fill mx-2' style='font-size:32px;' onclick="showExceedTemp()"></i>-->
                                    
                                     <h4 class="card-title mb-0 flex-grow-1 text-faded ">
                                         <button type="button" class="btn btn-success waves-effect waves-light shadow-none custom-toggle d-none" data-bs-toggle="button" aria-pressed="false">
                                                        <span class="icon-on"><i class="ri-subtract-line align-bottom me-1"></i> View All</span> 
                                                        <span class="icon-off"><i class="ri-add-line align-bottom me-1"></i>View Temp</span>
                                                    </button></h4>
                                                  <a href="/Temp/home/chillingTempHistory" class="btn bg-orange waves-effect btn-label waves-light">
                                                      <i class="ri-reply-fill label-icon align-middle fs-16 me-2">
                                                      
                                                  </i><span>Back</span></a>  
                                                  
                                                  <!--<a href="#" class="btn btn-success waves-effect btn-label waves-light mx-2" onclick="updateTempHistoryForm()">-->
                                                  <!--    <i class="ri-save-fill label-icon align-middle fs-16 me-2">-->
                                                      
                                                  <!--</i><span>Update</span></a>   -->
                                </div><!-- end card header -->
                                
                               
                                <div class="card-body">
                                      <?php if(isset($weeklyTempData) && !empty($weeklyTempData)) {  ?>
                                    <div class="table-responsive table-card">
                                       <?php $dateCount = 0; foreach($uniqueDates as $dateToFind) { ?>
    <?php 
    // Filter weeklyTempData for the current date
    $isDateExist = array_filter($weeklyTempData, function ($dayData) use ($dateToFind) {
        return $dayData['date_entered'] === $dateToFind;
    });
    ?>
    <?php if (!empty($isDateExist)) { ?>
        <h4 class="card-title mb-0 flex-grow-1 text-faded mt-4 mb-4 px-4 text-center allViewT">
            <?php echo date('d-m-Y', strtotime($dateToFind)); ?>
        </h4>
        <table class="table table-borderless table-hover table-nowrap align-middle mb-0 table-bordered allViewT ">
            <?php if ($dateCount === 0) { $dateCount++; ?>
                <thead class="table-light fixed-table-header">
                    <tr class="text-muted">
                        <th scope="col">Food Name</th>
                        <th scope="col">Start Time</th>
                        <th scope="col">Finish Time</th>
                        <th scope="col">Temp at Finish</th>
                        <th scope="col">Chilling Start Time</th>
                        <th scope="col">Time after 2 Hrs</th>
                        <th scope="col">Temp after 2 Hrs</th>
                        <th scope="col">Time after 4 Hrs</th>
                        <th scope="col">Temp after 4 Hrs</th>
                        <th scope="col">Entered by</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
            <?php } ?>
            <tbody>
                <?php 
                if (isset($site_detail) && !empty($site_detail)) { 
                    foreach ($site_detail as $AllSites) { 
                        $prep_areas = json_decode($AllSites['prep_areas']);
                        foreach ($prep_areas as $prep_area) { ?>
                            <tr>
                                <th colspan="11" class="text-black w-100" style="background-color: #07070b2e;">
                                    <b><?php echo $prep_area->prep_name; ?></b>
                                </th>
                            </tr>
                            <?php 
                            foreach ($isDateExist as $chillingTempData) { 
                                if ($chillingTempData['prep_id'] == $prep_area->id && $chillingTempData['site_id'] == $AllSites['id']) {
                                    $foodName = $chillingTempData['foodName'] ?? '';
                                    $finishTime = $chillingTempData['finishTime'] ?? '';
                                    $timeAfterTwoHours = $finishTime ? date("g:i A", strtotime($finishTime . " +2 hours")) : '';
                                    $timeAfterFourHours = $finishTime ? date("g:i A", strtotime($finishTime . " +4 hours")) : '';
                                    ?>
                                    <tr class="rowData">
                                        <td><input type="text" name="foodName[]" value="<?php echo htmlspecialchars($foodName); ?>" class="form-control"></td>
                                        <td><input type="time" name="startTime[]" value="<?php echo isset($chillingTempData['startTime']) ? date('H:i', strtotime($chillingTempData['startTime'])) : ''; ?>" class="form-control"></td>
                                        <td><input type="time" name="finishTime[]" value="<?php echo isset($chillingTempData['finishTime']) ? date('H:i', strtotime($chillingTempData['finishTime'])) : ''; ?>" class="form-control"></td>
                                        <td><input type="text" name="tempAtFinish[]" value="<?php echo $chillingTempData['tempAtFinish'] ?? ''; ?>" class="form-control"></td>
                                        <td><input type="time" name="chillingStartTime[]" value="<?php echo isset($chillingTempData['chillingStartTime']) ? date('H:i', strtotime($chillingTempData['chillingStartTime'])) : ''; ?>" class="form-control"></td>
                                        <td><input type="text" name="timeAfterTwoHours[]" value="<?php echo $timeAfterTwoHours; ?>" class="form-control" readonly></td>
                                        <td><input type="text" name="tempAfterTwohours[]" value="<?php echo $chillingTempData['tempAfterTwohours'] ?? ''; ?>" class="form-control"></td>
                                        <td><input type="text" name="timeAfterFourHours[]" value="<?php echo $timeAfterFourHours; ?>" class="form-control" readonly></td>
                                        <td><input type="text" name="tempAfterFourhours[]" value="<?php echo $chillingTempData['tempAfterFourhours'] ?? ''; ?>" class="form-control"></td>
                                        <td><input type="text" name="entered_by[]" value="<?php echo $chillingTempData['entered_by'] ?? ''; ?>" class="form-control"></td>
                                        <td><button class="btn btn-sm btn-success" onclick="updateTempChillingHisRow(this,<?php echo $chillingTempData['id']; ?>)">Update</button></td>
                                    </tr>
                                <?php }
                            } ?>
                        <?php }
                    }
                } ?>
            </tbody>
        </table>
    <?php } ?>
<?php } ?>

                                        
                                        
     <!------------------------------------------------TABLE FOR TEMP VIEW --------------------------------------------------------------------------- -->
                                        <form id="tempHistoryForm" action="/Temp/home/tempHistoryUpdatec" method="post">
                                            <input type="hidden" name="dateRange" value="<?php echo $dateRange ?>">
                                             <input type="hidden" name="site_id" value="<?php echo $site_id ?>">
                                        <table class="table table-borderless table-hover table-nowrap align-middle mb-0 table-bordered tempViewT d-none">
                                            <thead class="table-light">
                                                <tr class="text-muted">
                                                <th scope="col">Food Name</th>    
                                                 <?php  foreach($uniqueDates as $dateToFind) {  ?>    
                                                 <th scope="col" ><?php echo date('d-m-Y',strtotime($dateToFind)) ?></th>
                                                   <?php }   ?> 
                                                    </tr>
                                                    </thead>
                  <?php if (isset($site_detail) && !empty($site_detail)) { ?>
    <?php foreach ($site_detail as $AllSites) { ?>
        <?php $prep_areas = json_decode($AllSites['prep_areas']); ?>
        <?php if (!empty($prep_areas)) { ?>
            <?php foreach ($prep_areas as $prep_area) { ?>
                <?php if (isset($weeklyTempData) && !empty($weeklyTempData)) { ?>
                    <tbody class="tbodySite">
                        <th colspan="10" class="text-black w-100" style="background-color: #07070b2e;">
                            <b><?php echo $prep_area->prep_name; ?></b>
                        </th>
                        <?php 
                        $rowsByFoodName = []; 
                        foreach ($weeklyTempData as $chillingTempData) {
                            // Group data by unique foodName and prep_id/site_id match
                            if (
                                $chillingTempData['prep_id'] == $prep_area->id &&
                                $chillingTempData['site_id'] == $AllSites['id']
                            ) {
                                $foodName = $chillingTempData['foodName'];
                                if (!isset($rowsByFoodName[$foodName])) {
                                    $rowsByFoodName[$foodName] = [
                                        'foodName' => $foodName,
                                        'temps' => array_fill_keys($uniqueDates, ''), // Initialize empty temps by date
                                    ];
                                }
                                $rowsByFoodName[$foodName]['temps'][$chillingTempData['date_entered']] = $chillingTempData['tempAtFinish'];
                            }
                        }
                        ?>

                        <?php foreach ($rowsByFoodName as $foodData) { ?>
                            <tr>
                                <td><?php echo $foodData['foodName']; ?></td>
                                <?php foreach ($uniqueDates as $dateToFind) { ?>
                                    <td>
                                        <input type="text"
                                               name="tempAtFinish_<?php echo $AllSites['id'] ?>_<?php echo $prep_area->id ?>_<?php echo $dateToFind ?>"
                                               value="<?php echo $foodData['temps'][$dateToFind]; ?>" readonly class="form-control">
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    <?php } ?>
<?php } ?>

               </table>
               </form>
                                        <!-- end table -->
                                    </div><!-- end table responsive -->
                                     <?php } else {  ?>
                    <h3 class="text-black">No result found for this date range/site</h3>
                     <?php }   ?>
                                </div><!-- end card body -->
                            </div><!-- end card -->
                             </div>
                            </div>
                            </div>


                            <script>
                           $(document).ready(function() {
    
    $('.custom-toggle').on('click', function() {
      
        $('.allViewT').toggleClass('d-none');
        $('.tempViewT').toggleClass('d-none');
    });
});



function updateTempHistoryForm(){
    $("#tempHistoryForm").submit();
}


function updateTempChillingHisRow(obj,rowId) {
    $(obj).html("Saving...");
    const $row = $(obj).closest('.rowData');

    // Collect data from the inputs in the row
    const data = {
        id: rowId, // The ID passed to the function
        foodName: $row.find('input[name="foodName[]"]').val(),
        startTime: $row.find('input[name="startTime[]"]').val(),
        finishTime: $row.find('input[name="finishTime[]"]').val(),
        tempAtFinish: $row.find('input[name="tempAtFinish[]"]').val(),
        chillingStartTime: $row.find('input[name="chillingStartTime[]"]').val(),
        timeAfterTwoHours: $row.find('input[name="timeAfterTwoHours[]"]').val(),
        tempAfterTwohours: $row.find('input[name="tempAfterTwohours[]"]').val(),
        timeAfterFourHours: $row.find('input[name="timeAfterFourHours[]"]').val(),
        tempAfterFourhours: $row.find('input[name="tempAfterFourhours[]"]').val(),
        entered_by: $row.find('input[name="entered_by[]"]').val()
    };
 console.log("data",data);
 console.log("row",$row)
    // Send the AJAX request
    $.ajax({
        url: '/Temp/ChillingTemp/Chillinghome/tempHistoryUpdateAlldata',
        type: 'POST', // HTTP method
        data: data, // Data to be sent
        success: function(response) {
            let res = JSON.parse(response);
            if (res.status === 'success') {
                $(obj).html("Save");
            } else {
                alert('Error: ' + res.message);
            }
        },
        error: function(xhr, status, error) {
            // Handle the error response
            console.error('AJAX Error:', status, error);
            alert('An error occurred while updating the row. Please try again.');
        }
    });
}

 
                            </script>

<?php if (isset($can_edit_history) && !$can_edit_history): ?>
<style>
    .history-readonly input:not([type="hidden"]),
    .history-readonly select,
    .history-readonly textarea {
        background-color: #e9ecef !important;
        pointer-events: none !important;
    }
</style>
<script>
$(document).ready(function() {
    $('.card-body').addClass('history-readonly');
    $('.card-body input:not([type="hidden"]), .card-body select, .card-body textarea').prop('disabled', true);
    $('button, .btn').each(function() {
        var text = $(this).text().toLowerCase().trim();
        if (text.includes('update') || text.includes('save') || text.includes('add new') || text.includes('complete')) {
            $(this).hide();
        }
    });
});
</script>
<?php endif; ?>
