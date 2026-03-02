<div class="container-fluid mb-5" style="margin-top: 130px !important;">
    
    
     <nav class="navbar navbar-light bg-light  mb-3 p-2">
        <div class="container-fluid">
            <div class="d-flex flex-wrap gap-2 w-100">
                <!-- Manager Signature -->
                

                <a class="btn btn-secondary d-flex align-items-center gap-1" href="<?php echo base_url('/Temp/' . ($this->session->userdata('system_id') ?? '')); ?>" data-bs-toggle="tooltip" title="Record Equipment Temperature">
                    <i class="fas fa-thermometer-half"></i> Record Temp/Calib
                </a>
                <?php if (!empty($showFoodTemp) && $showFoodTemp) { ?>
                    <a class="btn btn-success d-flex align-items-center gap-1" href="<?php echo base_url('/Temp/FoodTemp/Foodtemphome'); ?>" data-bs-toggle="tooltip" title="Record Food Temperature">
                        <i class="fas fa-thermometer-half"></i> Food Temp
                    </a>
                <?php } ?>
                <?php if (!empty($showChillingTemp) && $showChillingTemp) { ?>
                    <a class="btn btn-danger d-flex align-items-center gap-1" href="<?php echo base_url('/Temp/ChillingTemp/Chillinghome'); ?>" data-bs-toggle="tooltip" title="Record Chilling Temperature">
                        <i class="fas fa-snowflake"></i> Chilling Temp 
                    </a>
                <?php } ?>
                
                <a class="btn btn-warning d-flex align-items-center gap-1" href="<?php echo base_url('/Temp/SliceTemp/Slicinghome'); ?>" data-bs-toggle="tooltip" title="Record Slicing Temperature">
                        <i class="fas fa-snowflake"></i> Slicing Temp
                    </a>
                    
                    <a class="btn btn-blue d-flex align-items-center gap-1" href="<?php echo base_url('/Temp/FryerTemp/Fryerhome'); ?>" data-bs-toggle="tooltip" title="Record Fryer Temperature">
                        <i class="fas fa-fire"></i> Fryer Temp
                    </a>

                <a class="btn btn-info d-flex align-items-center gap-1" href="<?php echo base_url('/Temp/CalibrationTemp/Calibrationhome'); ?>" data-bs-toggle="tooltip" title="Thermometer Calibration Record">
                    <i class="fas fa-tools"></i> Calibration
                </a>
                
                <button id="managerSignatureBtn" class="btn btn-primary d-flex align-items-center gap-1" data-bs-toggle="tooltip" title="Add Manager Signature">
                  <i class="fas fa-signature"></i> Manager Signature
                  </button>
                 <div id="managerSignatureBox" class="mt-2" style="display: none; max-width: 300px;">
    <div class="input-group">
        <input type="text" class="form-control" placeholder="Enter signature..." id="managerSignatureInput">
        <button class="btn btn-success" id="saveManagerSignature">Save</button>
    </div>
</div>
                <!-- Site Dropdown -->
                <select class="form-select ms-auto w-auto siteDropdown" aria-label="Select Site">
                    <option value="">Select Site</option>
                    <?php if (!empty($site_detail) && is_array($site_detail)) {
                        $count = 0;
                        foreach ($site_detail as $site) {
                            $site_id = $site['id'] ?? '';
                            $site_name = $site['site_name'] ?? '';
                            $selected = $count == 0 ? 'selected' : '';
                    ?>
                        <option <?php echo $selected; ?> value="<?php echo htmlspecialchars($site_id); ?>">
                            <?php echo htmlspecialchars($site_name); ?>
                        </option>
                    <?php $count++; } } else { ?>
                        <option value="">No sites available</option>
                    <?php } ?>
                </select>
                <!-- Exceed Temp Alert -->
                 <a class="btn btn-primary d-flex align-items-center gap-1" data-bs-toggle="tooltip" href="/Temp/FoodTemp/Foodtemphome/listProduct">
                    Add  product
                </a>
                
                <!--<button class="btn btn-warning d-flex align-items-center gap-1" data-bs-toggle="tooltip" title="View Exceeded Temperatures" onclick="showExceedTemp()">-->
                <!--    <i class="fas fa-exclamation-triangle"></i> Exceeded Temps-->
                <!--</button>-->
            </div>
        </div>
    </nav>
  <div class="alert alert-success alert-dismissible alert-label-icon rounded-label shadow fade tempSuccessRecorded d-none" role="alert">
        <i class="ri-notification-off-line label-icon"></i>
        <strong>Success</strong> Action completed successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div> 
    
   <div class="row">
                        <div class="col-12 tempDiv">
                            
                            
  
                            
                            <div class="card">
                                <div class="card-header  ">
                    <h4 class="card-title mb-0 text-black">
                        <i class="fas fa-thermometer-half"></i> Record  Food Temperature
                        <span class="text-black ms-2">🗓 <?php echo date('d-m-Y'); ?></span>
                    </h4>
                </div>

                                <div class="card-body">
                                  <input type="hidden" class="foodMaxTemp" value="<?php echo $foodMaxTemp ?>">
                                   <input type="hidden" class="foodMinTemp" value="<?php echo $foodMinTemp ?>">
                                    <div class="table-responsive table-card">
                                        <table class="table table-borderless table-hover table-nowrap align-middle mb-0 foodTempTable">
                                            <thead class="table-light">
                                                <tr class="text-muted">
                                                    <th scope="col">Product Name</th>
                                                    <th scope="col">Food Type</th>
                                                    <th scope="col">Record At </th>
                                                    <th scope="col">Temperature</th>
                                                    <th scope="col">Entered By </th>
                                                    <th scope="col">Action Taken</th>
                                                    <th scope="col">Attachments</th>
                                                    <th scope="col">Action</th>
                                                  
                                                </tr>
                                            </thead>
                                        <?php if(!empty($site_detail)) {      
                                        foreach($site_detail as $sites) { ?>
                                        <?php  $staffComments = unserialize($sites['staff_comments']);?>
                                        <?php  $prep_areas = json_decode($sites['prep_areas']); ?>
                                        <?php 
                                        foreach($prep_areas as $prep_area) {  ?>
                                        <tbody class="prep_<?php echo $prep_area->id ?>  tbodySite <?php echo 'siteId_'.$sites['id'] ?>" > 
    <th colspan="9" class="text-black w-100" style="background-color: #dff0fa;">
        <b><?php echo $prep_area->prep_name; ?></b>
    </th> 

    <?php 
    if (isset($products) && !empty($products)) {
        foreach ($products as $product) { 
            $productId = $product['id'];

            if ($product['prep_id'] == $prep_area->id) {

              
                $matched = array_filter($todaysFoodTempData, function($item) use ($productId, $prep_area, $sites) {
                    return isset($item['productId'], $item['prep_id'], $item['site_id']) &&
                           $item['productId'] == $productId &&
                           $item['prep_id'] == $prep_area->id &&
                           $item['site_id'] == $sites['id'];
                });

             
                $matchedData = !empty($matched) ? reset($matched) : null;
    ?>
    <tr class="parentRow">
        <td>
            <input type="text" name="productName" 
                   value="<?php echo $product['product_name']; ?>" 
                   readonly 
                   class="auto-save form-control productName" 
                   data-prepid="<?php echo $prep_area->id ?>" 
                   data-product-id="<?php echo $productId; ?>">
        </td> 
        <td>
            <?php echo (isset($product['foodType']) && $product['foodType'] == '1' ? 'Hot Food' : 'Cold Food'); ?>
        </td>
        <td class="recordedAt">
            <?php echo $matchedData['entered_time'] ?? ''; ?>
        </td>
        <td>
            <input type="text" name="currentTemp"
                   value="<?php echo $matchedData['food_temp'] ?? ''; ?>" 
                   class="auto-save form-control currentTemp" 
                   data-prepid="<?php echo $prep_area->id ?>" 
                   data-product-id="<?php echo $productId; ?>">
        </td>
        <td>
            <input type="text" name="enteredBy" 
                   value="<?php echo $matchedData['entered_by'] ?? ''; ?>" 
                   class="auto-save form-control enteredBy" 
                   data-prepid="<?php echo $prep_area->id ?>" 
                   data-product-id="<?php echo $productId; ?>">
        </td>
        <td>
            <select class="auto-save form-select staff_comments" 
                    disabled 
                    name="staff_comments" 
                    data-prepid="<?php echo $prep_area->id ?>" 
                    data-product-id="<?php echo $productId; ?>">
                <option value=""> Action Taken</option>     
                <?php 
                if (!empty($staffComments)) {
                    foreach ($staffComments as $staffComment) {
                        $selected = ($matchedData && $matchedData['staff_comments'] == $staffComment) ? 'selected' : '';
                        echo "<option value=\"$staffComment\" $selected>$staffComment</option>";
                    }
                }
                ?>
            </select>     
        </td>
        <td class="attchmentN_<?php echo $prep_area->id; ?>">
            <i class="ri-attachment-2 align-bottom me-1 mx-3 fs-16" 
               style="color: red;" 
               onclick="showTempAttchmentModal()"></i>
        </td> 
        <td>
            <button name="complete" 
                    class="btn btn-sm btn-<?php echo $matchedData ? 'success' : 'orange'; ?>" 
                    onclick="completeThisRow(this, <?php echo $product['id']; ?>, <?php echo $prep_area->id; ?>, <?php echo $sites['id']; ?>)">
                ⏰ <?php echo $matchedData ? 'Completed' : 'Complete'; ?>
            </button>
        </td>
      
    </tr>
    <?php } // if prep_id matches ?>
<?php } // foreach products ?>
<?php } // if products ?>
</tbody>

                                         <?php } ?>
                                         <?php } ?>
                                         <?php } ?>
                                            
                                            </table>
                                  
                                  
                                  
                                  
                                    
                                   
                                      </div>
                                       </div>
                                    
                                    </div>
                                    </div>
                         <div class="col-12 d-none exceededDiv">
                            <div class="card  h-96">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1 text-faded"><i class="ri-close-fill fs-22" title="Close" onclick="showExceedTemp()"></i> Food not within acceptable range</h4>
                                    <div class="flex-shrink-0">
                                     <?php $currentDate = date("d-m-Y"); $date7DaysAgo = date("d-m-Y", strtotime("-7 days")); ?>  
                                     <?php echo "🗓 ".$date7DaysAgo." To ".$currentDate; ?>
                                    </div>
                                </div><!-- end card header -->
                                <div class="card-body pt-0">
                                    <ul class="list-group list-group-flush border-dashed d-flex">
                                        <li class="list-group-item ps-0 liHeader">
                                        <div class="row align-items-center px-3">
                                            <div class="col-2 fw-bold">Date</div>
                                             <div class="col-3 fw-bold">Equip Name</div>
                                             <div class="col-2 fw-bold">Comments</div>
                                             <div class="col-2 fw-bold">Old Temp</div>
                                             <div class="col-2 fw-bold">New Temp</div>
                                            </div>
                                            </li>
                                    </ul>        
                                    <ul class="list-group list-group-flush border-dashed">
                                        <?php if(!empty($exceededTempData)) { foreach($exceededTempData as $exceededData) {  ?>
                                        <?php $managerComments = (isset($exceededData['manager_comments']) ? unserialize($exceededData['manager_comments']) : '') ?>
                                        <li class="list-group-item ps-0">
                                            <div class="row align-items-center g-3">
                                                <div class="col-2">
                                                    <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3 shadow">
                                                        <div class="text-center">
                                                            <?php $dateEntered = date("d", strtotime($exceededData['date_entered'])); $monthName = date("M", strtotime($exceededData['date_entered'])); ?>
                                                            <h5 class="mb-0 text-faded"><?php echo $dateEntered; ?></h5>
                                                            <div class="text-faded"><?php echo $monthName; ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <h4 class="text-faded mt-0 mb-1 fs-13 fw-bold"><?php echo $exceededData['site_name'] ?></h4>
                                                    <a href="#" class="text-reset fs-14 mb-0"><?php echo $exceededData['foodName'] ?></a>
                                                </div>
                                                <div class="col-4">
                                                <select class="form-select" id="manager_comments_<?php echo $exceededData['id']; ?>" name="manager_comments_<?php echo $exceededData['id']; ?>">
                                                    <option value="">Action Taken</option>
                                                     <?php if(!empty($managerComments)) { foreach($managerComments as $managerComment)  { ?>
                                                     <?php if(isset($todaysTempData[$exceededData['id']]['manager_comments']) && $todaysTempData[$exceededData['id']]['manager_comments'] == $managerComment) {  ?>
                                                     <option value="<?php echo $managerComment; ?>" selected="selected"><?php echo $managerComment; ?></option>
                                                     <?php } else { ?>
                                                     <option value="<?php echo $managerComment; ?>"><?php echo $managerComment; ?></option>
                                                     <?php } ?>
                                                     <?php } } ?>
                                                     </select> 
                                                </div>
                                                <div class="col-1">
                                                <input type="text" class="form-control" readonly value="<?php echo $exceededData['food_temp'] ?>" style="padding: 0.5rem 0.6rem;" />
                                                </div>
                                                <div class="col-1">
                                                <input type="text" style="padding: 0.5rem 0.6rem;" class="form-control" id="correctedTemp_<?php echo $exceededData['id']; ?>" value="<?php echo $exceededData['correctedTemp'] ?>" />
                                                </div>
                                                 <div class="col-1">
                                                <button class="btn btn-success exceedSaveBtn">
                                        <i class=" ri-save-2-fill" onclick="saveExceedTemp(this,<?php echo $exceededData['id']; ?>)"></i>            
                                                </button>
                                                </div>
                                            </div>
                                            <!-- end row -->
                                        </li>
                                        <?php } } ?>
                                       
                                    </ul><!-- end -->
                                
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div>            
                                    </div>
                                    </div>
  <div id="tempAttachmentModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="myModalLabel">Attachments</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form id="attachmentUploadForm" enctype="multipart/form-data">
                                                        <div class="modal-body">
                                                            <label> Select multiple images and upload</label>
                                                     <div class="file-input-container">
                                                             <input type="file" id="userfile" name="userfile[]" class="form-control-file" multiple>
                                                        </div>
                                                        <!--<input type="text" class="form-control mt-2" name="checklistComments" placeholder="Comments (Examples: details on why a task couldn’t be completed)" />-->
                                                       
                                                        <input type="hidden" id="equipId" name="equipId" value="">
                                                        </div>
                                                        </form>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                            <button type="button" class="btn btn-success uploadAttachmentButton">Upload</button>
                                                        </div>

                                                    </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div>                                  
 <script>
  function showTempAttchmentModal(id){
 $("#equipId").val(id);
 $("#tempAttachmentModal").modal('show');
}

$(document).ready(function () {
    $('.foodTempTable').on('click', '.clone-btn, .minus-btn', function () {
      if ($(this).hasClass('clone-btn')) {
        let clonedRow = $(this).closest('tr').clone();
        clonedRow.find('.minus-btn').removeClass('d-none');   
       clonedRow.find('input, button, select').prop('disabled', false);
       clonedRow.find('input').val(''); 
       clonedRow.find('.recordedAt').html('');
       clonedRow.find('.completSuccessBtn').html('⏰ Complete');
      clonedRow.insertAfter($(this).closest('tr'));
       clonedRow.find('.completSuccessBtn').removeClass('btn-success'); clonedRow.find('.completSuccessBtn').addClass('btn-orange');
      } else if ($(this).hasClass('minus-btn')) {
        $(this).closest('tr').remove();
      }
    });
    
   let selectedSite = localStorage.getItem('selectedSiteFoodTempDashBoard');
   console.log("selectedSite",selectedSite)
    if(selectedSite =='' || selectedSite == undefined){
      selectedSite = $(".siteDropdown").val();   
    }
    $(".siteDropdown").val(selectedSite);
   
    $(".tbodySite").addClass('d-none');
    $(".siteId_"+selectedSite).removeClass('d-none');
    $(".siteDropdown").on('change',function(){
      let selectedSite = $(this).val();  
      localStorage.setItem('selectedSiteFoodTempDashBoard',selectedSite);
      $(".tbodySite").addClass('d-none');
      $(".siteId_"+selectedSite).removeClass('d-none');
    });
    
     document.getElementById('userfile').addEventListener('change', function () {
               this.value = '';
             });
    
  $(".uploadAttachmentButton").on("click", function () {
        var formData = new FormData($("#attachmentUploadForm")[0]);
        $(".uploadAttachmentButton").html("Loading...");
        // Debugging: Output FormData object to console
        console.log(formData);

        $.ajax({
            type: "POST",
            url: "/Temp/home/uploadTemperatureAttachment", // Replace with your controller's URL
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
              $("#tempAttachmentModal").modal('hide');
              $(".uploadAttachmentButton").html("Save");
              let className = 'attchmentN_' + $("#equipId").val();
              $('.' + className).removeClass(className);
             
              
              
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });  
    
    
  });
  
 function completeThisRow(obj,productId,prepId,siteId){
  let currentTime = getCurrentTime();     
  let foodName = $(obj).parents(".parentRow").find(".productName").val()
  let foodMinTemp = $(".foodMinTemp").val();
  let foodMaxTemp = $(".foodMaxTemp").val();
  let food_temp = $(obj).parents(".parentRow").find(".currentTemp").val();
  let foodType = $(obj).parents(".parentRow").find(".foodType").val()
  let staff_comments =  $(obj).parents(".parentRow").find(".staff_comments").val();
  let enteredBy = $(obj).parents(".parentRow").find(".enteredBy").val()
  let food_IsTempok = 'ok';
  // 1 = hot food 2 = cold food 
  
  if((foodType == '1' && parseFloat(food_temp) < parseFloat(foodMaxTemp) && staff_comments =='') || (foodType=='2' && parseFloat(food_temp) > parseFloat(foodMinTemp) && staff_comments =='')){
    alert("The temperature entered is outside the acceptable range. Please select the action taken from the dropdown of comments.") ;
     food_IsTempok = 'notOk';
      $(obj).parents(".parentRow").find(".staff_comments").removeAttr("disabled");
    return false;
   }else if((foodType == '1' && parseFloat(food_temp) < parseFloat(foodMaxTemp)) || (foodType=='2' && parseFloat(food_temp) > parseFloat(foodMinTemp))){
     food_IsTempok = 'notOk';
     $(obj).parents(".parentRow").find(".staff_comments").removeAttr("disabled");
  }
  
  if(foodName==''){
    alert("Please enter food name to complete"); 
    return false;
  }
  if(food_temp==''){
    alert("Please enter food temperature to complete"); 
    return false;
  }
  
  if(enteredBy==''){
    alert("Please enter your name  in Entered By field to complete"); 
    return false;
  }
  $(obj).html('⏰ Completed');
  $(obj).removeClass('btn-orange'); $(obj).addClass('btn-success completSuccessBtn');
  $(obj).parents(".parentRow").find('input, select').prop('disabled', true);
  $(obj).parents(".parentRow").find('button:not(.minus-btn):not(.plus-btn)').prop('disabled', true);
  $(obj).parents(".parentRow").find(".recordedAt").html(currentTime);
   let data = [
        {
        foodName: foodName,
        productId: productId,
        foodType: foodType,
        currentFoodMinTempAllowed : foodMinTemp,
        currentFoodMaxTempAllowed : foodMaxTemp,
        food_IsTempok: food_IsTempok,
        entered_time : currentTime,
        site_id: siteId,
        prep_id: prepId,
        food_temp: food_temp,
        entered_by: enteredBy,
        staff_comments: staff_comments,
    },
];

      formData = JSON.stringify(data);
       
           $.ajax({
            type: 'POST',
            url: '/Temp/FoodTemp/Foodtemphome/saveTempDashboardData',
            data: formData,
            success: function (response) {
              $(".tempSuccessRecorded").removeClass("d-none");
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        }); 
  
  console.log("currentTime",currentTime);
 } 
 function showExceedTemp(){
    $(".exceededDiv").toggleClass("d-none");
    $(".tempDiv").toggleClass("col-lg-5");
     $(".exceededDiv").toggleClass("col-lg-7");
     
     let isMobileOrTablet = $(window).width() <= 768; console.log("isMobileOrTabletWW",$(window).width())
    if (isMobileOrTablet) {
            $("html, body").animate({ scrollTop: $(document).height() }, "slow");
        }
}
function saveExceedTemp(obj,id){
    let correctedTemp = $("#correctedTemp_"+id).val();
    let manager_comments = $("#manager_comments_"+id).val();
    let buttonHtml = $(obj).parents(".exceedSaveBtn");
    buttonHtml.html('...')
      $.ajax({
            type: 'POST',
            url: '/Temp/FoodTemp/Foodtemphome/updateExceededTemp',
            data: 'id='+id+'&correctedTemp='+correctedTemp+'&manager_comments='+manager_comments,
            success: function (response) {
              $(".tempSuccessRecorded").removeClass("d-none");
              buttonHtml.html('<i class="ri-checkbox-circle-line"></i>')
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        }); 
    
}
function fetchAttachment(id){
       $.ajax({
            type: "POST",
            url: "/Temp/home/fetchFoodAttachmentUploadedToday", // Replace with your controller's URL
            data: 'id='+id,
            success: function (response) {
                let result= JSON.parse(response);
                console.log(result)
           result.forEach(function (filename) {
           let imageUrl = "/uploaded_files/TemperatureAttachments/" + filename;
           let slide = '<div class="swiper-slide">' +
                    '<img src="' + imageUrl + '" alt="" class="img-fluid" style="width: 100%;" />' +
                    '</div>';
            console.log("slide",slide)        
             $('.appendSwiperImages').append(slide);
           });
          },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
         $("#attachmentEquipModal").modal('show');
    
}


</script>

<script>
$(document).ready(function () {
    $('#managerSignatureBtn').on('click', function () {
        $('#managerSignatureBox').slideToggle(); // Toggle show/hide with animation
    });

    $('#saveManagerSignature').on('click', function () {
        const signature = $('#managerSignatureInput').val().trim();
        if (signature === '') {
            alert('Please enter a signature.');
        } else {
            $('#managerSignatureBox').slideUp(); // Optional: hide after save
        }
   


      $.ajax({
            url: '/Temp/FoodTemp/Foodtemphome/save_signature', // Update with your actual controller/method
            type: 'POST',
            data: { signature: signature },
            success: function (response) {
                if (response.status === 'success') {
                    alert('Signature saved successfully!');
                    $('#managerSignatureBox').slideUp();
                    $('#managerSignatureInput').val('');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function (xhr) {
                alert('AJAX Error: ' + xhr.statusText);
            }
        });
    });
});
</script>

                                   