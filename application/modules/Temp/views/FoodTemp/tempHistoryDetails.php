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
                                         <button type="button" class="btn btn-success waves-effect waves-light shadow-none custom-toggle" data-bs-toggle="button" aria-pressed="false">
                                                        <span class="icon-on"><i class="ri-subtract-line align-bottom me-1"></i> View All</span> 
                                                        <span class="icon-off"><i class="ri-add-line align-bottom me-1"></i>View Temp</span>
                                                    </button></h4>
                                                  <a href="/Temp/home/foodTempHistory" class="btn bg-orange waves-effect btn-label waves-light">
                                                      <i class="ri-reply-fill label-icon align-middle fs-16 me-2">
                                                      
                                                  </i><span>Back</span></a>  
                                                  
                                                  <!--<a href="#" class="btn btn-success waves-effect btn-label waves-light mx-2" onclick="updateTempHistoryForm()">-->
                                                  <!--    <i class="ri-save-fill label-icon align-middle fs-16 me-2">-->
                                                      
                                                  <!--</i><span>Update</span></a>   -->
                                </div><!-- end card header -->
                                
                               
                                <div class="card-body">
                                    <?php if(isset($weeklyTempData) && !empty($weeklyTempData)) {  ?>
                                    <div class="table-responsive table-card">
                                        <?php $dateCount = 0;  foreach($uniqueDates as $dateToFind) {   ?>
                                        <?php 
                                          
                                             $isDateExist = array_filter($weeklyTempData, function ($dayData) use ($dateToFind) {
                                                 return $dayData['date_entered'] == $dateToFind;
                                               });
                                               
                                            //   echo "<pre>"; print_r($isDateExist); exit;
                                                ?>
                                        <?php
                                        if(!empty($isDateExist)) {  ?>        
                                        <h4 class="card-title mb-0 flex-grow-1 text-faded mt-4 mb-4 px-4 text-center allViewT"><?php echo date('d-m-Y',strtotime($dateToFind)) ?></h4>
                                    
                                    
                            <?php if(isset($site_detail) && !empty($site_detail)){  ?>       
                             <?php  foreach($site_detail as $AllSites) { ?>
                              <?php  $prep_areas = json_decode($AllSites['prep_areas']); ?>
                             <?php  foreach($prep_areas as $prep_area) {    ?>   
                             
                             <?php  if(isset($weeklyTempData) && !empty($weeklyTempData)) { ?>
                             <?php  foreach($weeklyTempData as $foodTempData) {  ?> 
                            <?php if($foodTempData['prep_id']==$prep_area->id && $foodTempData['site_id'] == $AllSites['id'] && $foodTempData['date_entered'] == $dateToFind) { ?> 
                           <?php 
                             $foodId = $foodTempData['id'];
                             if(!empty($foodTempData['attachment'])){
                         $attachments =  (isset($foodTempData['attachment']) && $foodTempData['attachment'] !='' ? unserialize($foodTempData['attachment']) : '');        
                             ?>                   
                                                   
                             <div id="attachmentEquipModal_<?php echo $foodId; ?>" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                               <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title" id="myModalLabel">Attachments</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                         </div>
                      <div class="modal-body appendAttachment">
                       <div class="card">
                          <div class="card-body">
                                        <div class="swiper pagination-progress-swiper rounded">
                                            <div class="swiper-wrapper">
                                                <?php if(!empty($attachments)) {  ?>
                                                <?php foreach($attachments as $attachment) {  ?>
                                                <div class="swiper-slide">
                                                    <img src="/uploaded_files/<?php echo $this->session->userdata('tenantIdentifier') ?>/Temp/FoodTemperatureAttachments/<?php echo $attachment ?>" alt="" class="img-fluid" style="width: 100%;"/>
                                                </div>
                                              <?php } } ?>
                                            </div>
                                            <div class="swiper-button-next bg-white shadow"></div>
                                            <div class="swiper-button-prev bg-white shadow"></div>
                                            <div class="swiper-pagination"></div>
                                        </div>
                                    </div><!-- end card-body -->
                            </div><!-- end card --> 
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
                            </div>
       
                                <?php } ?>  
                                <?php } ?> 
                                 <?php } ?> 
                                <?php } ?>
                                 <?php } ?>
                                  <?php } ?>
                                  <?php } ?>       
                                        
                                        
                                        <table class="table table-borderless table-hover table-nowrap align-middle mb-0 table-bordered allViewT">
                                            <?php if($dateCount ==0 || $dateCount ==1){ $dateCount++; ?>  
                                            <thead class="table-light fixed-table-header">
                                                <tr class="text-muted">
                                                    <th scope="col">Food Name</th>
                                                    <th scope="col" >Record At </th>
                                                    <th scope="col">Old Degree</th>
                                                    <th scope="col">New Degree</th>
                                                    <th scope="col">Entered By </th>
                                                     <th scope="col">Staff Comments</th>
                                                    <th scope="col">Manager Comments</th>
                                                    <th scope="col">Attachments</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <?php } ?>
                                         <?php  
                                         
                                         
                                          
                                         if(isset($site_detail) && !empty($site_detail)){  ?>
                                       
                                          <?php  foreach($site_detail as $AllSites) { ?>
                                           <?php  $prep_areas = json_decode($AllSites['prep_areas']); ?>
                                           <?php  foreach($prep_areas as $prep_area) {    ?>   
                                           
     <?php if (isset($weeklyTempData) && !empty($weeklyTempData)) { ?>
    <tbody class="site_<?php echo $AllSites['id'] ?>  tbodySite">
        <th colspan="9" class="text-black w-100" style="background-color: #07070b2e;">
            <b><?php echo $prep_area->prep_name; ?></b>
        </th>
        <?php foreach ($weeklyTempData as $foodTempData) { ?>
            <?php if ($foodTempData['prep_id'] == $prep_area->id && $foodTempData['site_id'] == $AllSites['id'] && $foodTempData['date_entered'] == $dateToFind) { ?>
                <tr class="parentRow" data-date-entered="<?php echo $foodTempData['date_entered']; ?>">
                    <td>
                        <input type="text" name="foodName[]" value="<?php echo htmlspecialchars($foodTempData['foodName'] ?? ''); ?>" class="form-control productName">
                    </td>
                    <td>
                        <input type="time" name="entered_time[]" value="<?php echo isset($foodTempData['entered_time']) ? date('H:i', strtotime($foodTempData['entered_time'])) : ''; ?>" class="form-control entered_time">
                    </td>
                    <td>
                        <input type="text" name="food_temp[]" value="<?php echo $foodTempData['food_temp'] ?? ''; ?>" class="form-control currentTemp">
                    </td>
                    <td>
                        <?php if(isset($foodTempData['correctedTemp']) && $foodTempData['correctedTemp'] !='') { ?>
                        <input type="text" name="correctedTemp[]" value="<?php echo $foodTempData['correctedTemp'] ?? ''; ?>" class="form-control">
                        <?php } ?>
                    </td>
                    <td>
                        <input type="text" name="entered_by[]" value="<?php echo $foodTempData['entered_by'] ?? ''; ?>" class="form-control enteredBy">
                    </td>
                    <td>
                        <input type="text" name="staff_comments[]" value="<?php echo $foodTempData['staff_comments'] ?? ''; ?>" class="form-control">
                    </td>
                    <td>
                        <input type="text" name="manager_comments[]" value="<?php echo $foodTempData['manager_comments'] ?? ''; ?>" class="form-control">
                    </td>
                    <td>
                        <?php if (!empty($foodTempData['attachment'])) { ?>
                            <a onclick="showAttachment('<?php echo $foodTempData['id']; ?>')" class="btn btn-sm btn-green">View</a>
                        <?php } ?>
                    </td>
                    
                     <td>
                <button class="btn btn-sm btn-primary" onclick="addNewRow(this)">+</button>
                <button class="btn btn-sm btn-success" onclick="updateThisRow(this,<?php echo $foodTempData['id']; ?>)">Update</button>
                
            </td>
                </tr>
            <?php } ?>
        <?php } ?>
        
        <!-- Add empty row for new entries at the end -->
        <tr class="emptyRow parentRow" data-date-entered="<?php echo $dateToFind; ?>">
            <td><input type="text" name="productName" value="" class="form-control productName"><input type="hidden" class="foodType" value="1"></td>
            <td><input type="time" name="entered_time" value="" class="form-control entered_time"></td>
            <td><input type="text" name="currentTemp" value="" class="form-control currentTemp"></td>
            <td></td>
            <td><input type="text" name="enteredBy" value="" class="form-control enteredBy"></td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="addNewRow(this)">+</button>
                <button class="btn btn-sm btn-success" onclick="completeThisRow(this,<?php echo $prep_area->id; ?>,<?php echo $AllSites['id'] ?>,'<?php echo $dateToFind ?>')">Save</button>
            </td>
        </tr>
    </tbody>
<?php }  ?>
    <?php  } ?>
                                           
                                            <?php }   ?>
                                              <?php }   ?>
                                        </table>
                                        <?php }else{ ?>
                                         <h4 class="card-title mb-0 flex-grow-1 text-faded mt-4 mb-4 px-4 text-center allViewT"><?php echo date('d-m-Y',strtotime($dateToFind)) ?></h4>
                                      <table class="table table-borderless table-hover table-nowrap align-middle mb-0 table-bordered allViewT">
                                            <?php if($dateCount ==0){ $dateCount++; ?>  
                                            <thead class="table-light fixed-table-header">
                                                <tr class="text-muted">
                                                    <th scope="col">Food Name</th>
                                                    <th scope="col">Food Type</th>
                                                    <th scope="col" >Record At </th>
                                                    <th scope="col">Temperature</th>
                                                    <th scope="col">Entered By </th>
                                                     <th scope="col">Action </th>
                                                   </tr>
                                            </thead>
                                            <?php } ?> 
                                            
                                 <?php  if(isset($site_detail) && !empty($site_detail)){  ?>
                                       
                                          <?php  foreach($site_detail as $AllSites) { ?>
                                           <?php  $prep_areas = json_decode($AllSites['prep_areas']); ?>
                                           <?php  foreach($prep_areas as $prep_area) {    ?>            
                                            
      <tbody class="site_<?php echo $AllSites['id'] ?> tbodySite">
     <th colspan="6" class="text-black w-100" style="background-color: #07070b2e;">
            <b><?php echo $prep_area->prep_name; ?></b>
        </th>
        <tr class="emptyRow parentRow">
            <td><input type="text" name="productName" value="" class="form-control productName"></td>
            <td><select class="form-select foodType" name="foodType" ><option value="1">Hot Food </option>  <option value="2">Cold Food </option>   </select>   </td> 
             <td><input type="time" name="entered_time" value="" class="form-control entered_time"></td>
            <td><input type="text" name="currentTemp" value="" class="form-control currentTemp"></td>
            <td><input type="text" name="enteredBy" value="" class="form-control enteredBy"></td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="addNewRow(this)">+</button>
                <button class="btn btn-sm btn-success" onclick="completeThisRow(this,<?php echo $prep_area->id; ?>,<?php echo $AllSites['id'] ?>,'<?php echo $dateToFind ?>')">Save</button>
                
            </td>
        </tr>
    </tbody>
     <?php     }  }  } ?>
    </table>
                                    <?php     }   } ?>
                                        
                                        
                                        
                                        
                                        
                                        
                                        
     <!------------------------------------------------TABLE FOR TEMP VIEW --------------------------------------------------------------------------- -->
                                        <form id="tempHistoryForm" action="/Temp/home/tempHistoryUpdate" method="post">
                                            <input type="hidden" name="dateRange" value="<?php echo $dateRange ?>">
                                             <input type="hidden" name="site_id" value="<?php echo $site_id ?>">
                                        <table class="table table-borderless table-hover table-nowrap align-middle mb-0 table-bordered tempViewT d-none">
                                            <thead class="table-light">
                                                <tr class="text-muted">
                                                <th scope="col">Equipment</th>    
                                                 <?php  foreach($uniqueDates as $dateToFind) {  ?>    
                                                 <th scope="col" ><?php echo date('d-m-Y',strtotime($dateToFind)) ?></th>
                                                   <?php }   ?> 
                                                    </tr>
                                                    </thead>
            
                            
                             
                                                                     
                                                    
                                                    
                                          <?php if(isset($site_detail) && !empty($site_detail)){  ?>
                                           <?php  foreach($site_detail as $AllSites) { ?>
                                            <?php  $prep_areas = json_decode($AllSites['prep_areas']); ?>
                                             <?php if(!empty($prep_areas)){  ?>
                                            <?php  foreach($prep_areas as $prep_area) {    ?> 
         <?php  if(isset($weeklyTempData) && !empty($weeklyTempData)) { ?>    
           <tbody class="tbodySite" >
   <th colspan="8" class="text-black w-100 " style="background-color: #07070b2e;"> <b><?php echo $prep_area->prep_name; ?></b></th> 
     <?php  foreach($weeklyTempData as $foodTempData) {  ?> 
        <?php if($foodTempData['prep_id']==$prep_area->id && $foodTempData['site_id'] == $AllSites['id']) { ?>    
    <tr>
            <td><?php echo (isset($foodTempData['foodName']) ? $foodTempData['foodName'] : ''); ?></td>
          <?php foreach($uniqueDates as $dateToFind) {  ?> 
         <?php if($foodTempData['date_entered'] == $dateToFind){ ?> 
         
          <td><input type="text" name="foodTemp_<?php echo $foodTempData['site_id'] ?>_<?php echo $prep_area->id ?>_<?php echo $foodTempData['id'] ?>_<?php echo $dateToFind ?>" value="<?php echo $foodTempData['food_temp']; ?>"></td>
          
          <?php }else{    ?> 
          <td><input type="text" name="foodTemp_<?php echo $foodTempData['site_id'] ?>_<?php echo $prep_area->id ?>_<?php echo $foodTempData['id'] ?>_<?php echo $dateToFind ?>"></td>
          <?php }   ?>
           <?php }   ?>
          </tr>
           <?php }  } ?> 
             </tbody>   
             <?php }   ?>
              <?php }   ?>
             <?php }   ?>
               <?php }   ?> 
                <?php }   ?> 
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
                            
function getCurrentTime() {
    let now = new Date();
    let hours = String(now.getHours()).padStart(2, '0');
    let minutes = String(now.getMinutes()).padStart(2, '0');
    return hours + ':' + minutes;
}

function addNewRow(button) {
    const row = button.closest('tr');
    const newRow = row.cloneNode(true);

    // Clear input fields in the new row
    newRow.querySelectorAll('input').forEach(input => input.value = '');
    
    // Clear select fields in the new row
    newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

    // Append the new row after the current one
    row.parentNode.appendChild(newRow);
}

function saveRow(button) {
    // Logic to save the data via AJAX or form submission
    alert('Save functionality not implemented');
}

$(document).ready(function() {
    
    $('.custom-toggle').on('click', function() {
      
        $('.allViewT').toggleClass('d-none');
        $('.tempViewT').toggleClass('d-none');
    });
});

function showAttachment(foodID){
    
   $("#attachmentEquipModal_"+foodID).modal('show');
   
}

function updateTempHistoryForm(){
    $("#tempHistoryForm").submit();
}


function completeThisRow(obj,prepId,siteId,date_entered){
  let entered_time = $(obj).parents(".parentRow").find(".entered_time").val();
  // If no time entered, use current time
  if(!entered_time) {
    entered_time = getCurrentTime();
  }
  let foodName = $(obj).parents(".parentRow").find(".productName").val()
  let food_temp = $(obj).parents(".parentRow").find(".currentTemp").val();
  
  // Try to find foodType - might be in same row or in sibling hidden input
  let foodTypeElement = $(obj).parents(".parentRow").find(".foodType");
  if(foodTypeElement.length === 0) {
    // Check if there's a hidden input after this row
    foodTypeElement = $(obj).parents(".parentRow").siblings("input.foodType");
  }
  let foodType = foodTypeElement.val() || '1'; // Default to 1 (Hot Food)
 
  let enteredBy = $(obj).parents(".parentRow").find(".enteredBy").val()
  let food_IsTempok = 'ok';
 
  // Validate required fields
  if(!foodName || !food_temp || !enteredBy) {
    alert('Please fill in all required fields: Food Name, Temperature, and Entered By');
    return;
  }
  
  $(obj).html('Saving...');
  $(obj).prop('disabled', true);
  
   let data = [
        {
        foodName: foodName,
        foodType: foodType,
        food_IsTempok: food_IsTempok,
        entered_time : entered_time,
        site_id: siteId,
        prep_id: prepId,
        food_temp: food_temp,
        entered_by: enteredBy,
        date_entered: date_entered,
        
    },
];

      formData = JSON.stringify(data);
       
           $.ajax({
            type: 'POST',
            url: '/Temp/FoodTemp/Foodtemphome/saveTempDashboardData',
            data: formData,
            contentType: 'application/json',
            dataType: 'json',
            success: function (response) {
              console.log('Response:', response);
              if(response.status === 'success') {
                // Don't clear the fields - keep them visible so user can see what was saved
                // Disable all inputs in this row to show it's saved
                $(obj).parents(".parentRow").find("input").prop('readonly', true);
                $(obj).parents(".parentRow").find("select").prop('disabled', true);
                
                // Change button to indicate saved status
                $(obj).html('✓ Saved');
                $(obj).removeClass('btn-success').addClass('btn-info');
                $(obj).prop('disabled', true);
                
                // Hide the + button since this row is now saved
                $(obj).siblings('.btn-primary').hide();
                
              } else {
                $(obj).html('Save');
                $(obj).prop('disabled', false);
                alert('Error saving data');
              }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                $(obj).html('Save');
                $(obj).prop('disabled', false);
                alert('Error saving data: ' + error);
            }
        });
 } 
 
 function updateThisRow(obj,rowid){
 
  let entered_time = $(obj).parents(".parentRow").find(".entered_time").val()
  let foodName = $(obj).parents(".parentRow").find(".productName").val()
  let food_temp = $(obj).parents(".parentRow").find(".currentTemp").val();
  let foodType = $(obj).parents(".parentRow").find(".foodType").val()
  let enteredBy = $(obj).parents(".parentRow").find(".enteredBy").val()
  let date_entered = $(obj).parents(".parentRow").attr("data-date-entered")
  let food_IsTempok = 'ok';
  // 1 = hot food 2 = cold food 

  
  $(obj).html('Saving...');
  
   let data = [
        {
        id: rowid,
        foodName: foodName,
        foodType: foodType,
        food_IsTempok: food_IsTempok,
        entered_time : entered_time,
        food_temp: food_temp,
        entered_by: enteredBy,
        date_entered: date_entered,
        
    },
];

      formData = JSON.stringify(data);
       
           $.ajax({
            type: 'POST',
            url: '/Temp/FoodTemp/Foodtemphome/tempHistoryUpdatePastrecords',
            data: formData,
            contentType: 'application/json',
            dataType: 'json',
            success: function (response) {
              console.log('Response:', response);
              if(response.status === 'success') {
                $(obj).html('✓ Updated');
                $(obj).removeClass('btn-success').addClass('btn-success');
              } else {
                $(obj).html('Error');
                alert('Error updating data');
              }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                $(obj).html('Error');
                alert('Error updating data: ' + error);
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
