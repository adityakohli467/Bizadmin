<style>
.table td {
    width: 10%; 
}
/* Fixed header for table */
.fixed-table-header {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    z-index: 999;
    background-color: #fff;
}
</style>

<div class="container-fluid mb-5" style="margin-top: 130px !important;">
   <div class="row">
       <div class="col-12 tempDiv">
           <div class="card">
               <div class="card-header align-items-center d-flex">
                   
                   <a href="/Temp/home/fryerTempHistory" class="btn bg-orange waves-effect btn-label waves-light">
                       <i class="ri-reply-fill label-icon align-middle fs-16 me-2"></i><span>Back</span>
                   </a>
               </div><!-- end card header -->

               <div class="card-body">
                   <?php if(isset($weeklyTempData) && !empty($weeklyTempData)) { ?>
                   <div class="table-responsive table-card">
                       <?php $dateCount = 0; foreach($uniqueDates as $dateToFind) { ?>
                       <?php 
                           $isDateExist = array_filter($weeklyTempData, function ($dayData) use ($dateToFind) {
                               return $dayData['date_entered'] == $dateToFind;
                           });
                       ?>
                       <?php if(!empty($isDateExist)) { ?>        
                       <h4 class="card-title mb-0 flex-grow-1 text-faded mt-4 mb-4 px-4 text-center allViewT">
                           <?php echo date('d-m-Y',strtotime($dateToFind)) ?>
                       </h4>

                       <table class="table table-borderless table-hover table-nowrap align-middle mb-0 table-bordered allViewT">
                           <?php if($dateCount == 0 || $dateCount == 1){ $dateCount++; ?>  
                           <thead class="table-light fixed-table-header">
                               <tr class="text-muted">
                                   <th>Product</th>
                                   <th>Prep ID</th>
                                   <th>Record Time</th>
                                   
                                   <th>Start Cooking</th>
                                   <th>Finish Cooking</th>
                                   <th>Temp End Cooking</th>
                                   <th>Chill Start</th>
                                   <th>Chill Finish</th>
                                   <th>Temp End Chill</th>
                                   <th>Recorded By</th>
                                   <th>Entered By</th>
                                   <th>Is Completed</th>
                                   <th>Manager Sign</th>
                                   <th>Action</th>
                               </tr>
                           </thead>
                           <?php } ?>

                           <?php if(isset($site_detail) && !empty($site_detail)){ ?>
                               <?php foreach($site_detail as $AllSites) { ?>
                                   <?php $prep_areas = json_decode($AllSites['prep_areas']); ?>
                                   <?php foreach($prep_areas as $prep_area) { ?>   
                                       <?php if (isset($weeklyTempData) && !empty($weeklyTempData)) { ?>
                                       <tbody class="site_<?php echo $AllSites['id'] ?> tbodySite">
                                           <th colspan="16" class="text-black w-100" style="background-color: #07070b2e;">
                                               <b><?php echo $prep_area->prep_name; ?></b>
                                           </th>
                                           <?php foreach ($weeklyTempData as $tempData) { ?>
                                               <?php if ($tempData['prep_id'] == $prep_area->id && $tempData['site_id'] == $AllSites['id'] && $tempData['date_entered'] == $dateToFind) { ?>
                                              <tr class="parentRow">
    <td>
        <input type="text" value="<?php echo htmlspecialchars($tempData['product_name'] ?? ''); ?>" 
               class="form-control productName">
    </td>
    <td>
        <input type="text" value="<?php echo $tempData['prep_id']; ?>" 
               class="form-control prep_id">
    </td>
    <td>
        <input type="time" value="<?php echo isset($tempData['entered_time']) ? date('H:i', strtotime($tempData['entered_time'])) : ''; ?>" 
               class="form-control entered_time">
    </td>
   
    <td>
        <input type="time" value="<?php echo isset($tempData['start_cooking_time']) ? date('H:i', strtotime($tempData['start_cooking_time'])) : ''; ?>" 
               class="form-control start_cooking_time">
    </td>
    <td>
        <input type="time" value="<?php echo isset($tempData['finish_cooking_time']) ? date('H:i', strtotime($tempData['finish_cooking_time'])) : ''; ?>" 
               class="form-control finish_cooking_time">
    </td>
    <td>
        <input type="text" value="<?php echo $tempData['temp_end_cooking']; ?>" 
               class="form-control temp_end_cooking">
    </td>
    <td>
        <input type="time" value="<?php echo isset($tempData['time_chilling_start']) ? date('H:i', strtotime($tempData['time_chilling_start'])) : ''; ?>" 
               class="form-control time_chilling_start">
    </td>
    <td>
        <input type="time" value="<?php echo isset($tempData['time_chilling_finished']) ? date('H:i', strtotime($tempData['time_chilling_finished'])) : ''; ?>" 
               class="form-control time_chilling_finished">
    </td>
    <td>
        <input type="text" value="<?php echo $tempData['temp_end_chilling']; ?>" 
               class="form-control temp_end_chilling">
    </td>
    <td>
        <input type="text" value="<?php echo $tempData['recorded_by']; ?>" 
               class="form-control recorded_by">
    </td>
    <td>
        <input type="text" value="<?php echo $tempData['entered_by']; ?>" 
               class="form-control enteredBy">
    </td>
    <td>
        <?php if($tempData['is_completed'] == 1) { ?>
            <span class="badge bg-success">Yes</span>
        <?php } else { ?>
            <span class="badge bg-danger">No</span>
        <?php } ?>
    </td>
    <td>
        <input type="text" value="<?php echo $tempData['manager_sign'] ?? ''; ?>" 
               class="form-control manager_sign">
    </td>
    
    <td>
        <button class="btn btn-sm btn-primary" onclick="addNewRow(this)">+</button>
        <button class="btn btn-sm btn-secondary" 
                onclick="completeThisRow(this,<?php echo $prep_area->id; ?>,<?php echo $AllSites['id']; ?>,'<?php echo $tempData['date_entered']; ?>')">
            Save new row
        </button>
        <button class="btn btn-sm btn-success" 
                onclick="updateThisRow(this,<?php echo $tempData['id']; ?>)">
            Update
        </button>
    </td>
</tr>

                                               <?php } ?>
                                           <?php } ?>
                                       </tbody>
                                       <?php } ?>
                                   <?php } ?>
                               <?php } ?>
                           <?php } ?>
                       </table>
                       <?php }  ?>
                       <?php } ?>
                   </div><!-- end table responsive -->
                   <?php } else { ?>
                       <h3 class="text-black">No result found for this date range/site</h3>
                   <?php } ?>
               </div><!-- end card body -->
           </div><!-- end card -->
       </div>
   </div>
</div>

<script>
function addNewRow(button) {
    const row = button.closest('tr');
    const newRow = row.cloneNode(true);
    newRow.querySelectorAll('input').forEach(input => input.value = '');
    row.parentNode.appendChild(newRow);
}



function showAttachment(foodID) {
    $("#attachmentEquipModal_" + foodID).modal('show');
}

function updateTempHistoryForm() {
    $("#tempHistoryForm").submit();
}

function completeThisRow(obj, prepId, siteId, date_entered) {
    let row = $(obj).closest('.parentRow');

    // Collect all fryer data fields from the row
    let data = [{
        site_id: siteId,
        prep_id: prepId,
        productId: row.find('.productName').val() || '',

        entered_time: row.find('.entered_time').val() || getCurrentTime(),
        currentFoodMinTempAllowed: row.find('.currentFoodMinTempAllowed').val() || '',
        currentFoodMaxTempAllowed: row.find('.currentFoodMaxTempAllowed').val() || '',
        start_cooking_time: row.find('.start_cooking_time').val() || '',
        finish_cooking_time: row.find('.finish_cooking_time').val() || '',
        temp_end_cooking: row.find('.temp_end_cooking').val() || '',
        time_chilling_start: row.find('.time_chilling_start').val() || '',
        time_chilling_finished: row.find('.time_chilling_finished').val() || '',
        temp_end_chilling: row.find('.temp_end_chilling').val() || '',
        recorded_by: row.find('.recorded_by').val() || '',
        entered_by: row.find('.enteredBy').val() || '',
        manager_sign: row.find('.manager_sign').val() || '',
        staff_comments: row.find('.staff_comments').val() || '',
        manager_comments: row.find('.manager_comments').val() || '',

        date_entered: date_entered,
        is_completed: 1
    }];

    // Disable button temporarily while saving
    $(obj).prop('disabled', true).html('Saving...');

    $.ajax({
        type: 'POST',
        url: '/Temp/FryerTemp/Fryerhome/saveTempDashboardData',
        data: JSON.stringify(data),
        success: function (response) {
            $(obj).prop('disabled', false).html('Saved');
            console.log('Saved:', response);
        },
        error: function (xhr, status, error) {
            $(obj).prop('disabled', false).html('Error');
            console.error('Save Error:', error);
        }
    });
}

function updateThisRow(obj, rowid) {
    let row = $(obj).closest('.parentRow');

    // Collect updated fryer fields
    let data = [{
        id: rowid,
        entered_time: row.find('.entered_time').val() || '',
        currentFoodMinTempAllowed: row.find('.currentFoodMinTempAllowed').val() || '',
        currentFoodMaxTempAllowed: row.find('.currentFoodMaxTempAllowed').val() || '',
        start_cooking_time: row.find('.start_cooking_time').val() || '',
        finish_cooking_time: row.find('.finish_cooking_time').val() || '',
        temp_end_cooking: row.find('.temp_end_cooking').val() || '',
        time_chilling_start: row.find('.time_chilling_start').val() || '',
        time_chilling_finished: row.find('.time_chilling_finished').val() || '',
        temp_end_chilling: row.find('.temp_end_chilling').val() || '',
        recorded_by: row.find('.recorded_by').val() || '',
        entered_by: row.find('.enteredBy').val() || '',
        manager_sign: row.find('.manager_sign').val() || '',
        staff_comments: row.find('.staff_comments').val() || '',
        manager_comments: row.find('.manager_comments').val() || '',

        is_completed: 1
    }];

    $(obj).prop('disabled', true).html('Saving...');

    $.ajax({
        type: 'POST',
        url: '/Temp/FryerTemp/Fryerhome/tempHistoryUpdatePastrecords',
        data: JSON.stringify(data),
        success: function (response) {
            $(obj).prop('disabled', false).html('Saved');
            console.log('Updated:', response);
        },
        error: function (xhr, status, error) {
            $(obj).prop('disabled', false).html('Error');
            console.error('Update Error:', error);
        }
    });
}

/* Utility: Return current time in HH:mm */
function getCurrentTime() {
    let now = new Date();
    let hours = now.getHours().toString().padStart(2, '0');
    let minutes = now.getMinutes().toString().padStart(2, '0');
    return `${hours}:${minutes}`;
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
