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
                            <div class="card-header d-flex justify-content-between align-items-center">
    <div>
        <h4 class="card-title mb-0 text-black">
            <i class="fas fa-thermometer-half"></i> Record Slicing Temperature
            <span class="text-black ms-2">🗓 <?php echo date('d-m-Y'); ?></span>
        </h4>
        <small>
            <b><i>Click on clock icon to auto populate all "time" values</i></b>
        </small>
    </div>

    <button type="button" class="btn btn-success" onclick="handleSaveClick(this)">
        <i class="fas fa-save"></i> Save
    </button>
</div>


                                <div class="card-body">
                                 
                                    <div class="table-responsive table-card">
                                        <table class="table table-borderless table-hover table-nowrap align-middle mb-0 foodTempTable">
                                            <thead class="table-light">
                                                <tr class="text-muted">
                                                 <th scope="col">Product Name</th>
            <th scope="col">Internal Batch Code</th>
            <th scope="col">Start Slicing</th>
            <th scope="col">Finish Slicing</th>
            <th scope="col">Start Slice Temp</th>
            <th scope="col">End Slice Temp</th>
            <th scope="col">Comments</th>
            <th scope="col">Entered By</th>
            <th scope="col">Signature</th>
                                                  
                                                </tr>
                                            </thead>
                                        <?php
                                       
                                        if(!empty($site_detail)) {      
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

                $matched = array_filter($todaysSlicingTempData, function($item) use ($productId, $prep_area, $sites) {
                    return isset($item['product_id'], $item['prep_id'], $item['site_id']) &&
                           $item['product_id'] == $productId &&
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
                            <input type="text" data-prepid="<?php echo $prep_area->id ?>" data-field="internal_batch_code_allocated"
                                   name="internal_batch_code_allocated"
                                   class="auto-save form-control internalBatchCode"
                                   value="<?php echo htmlspecialchars($matchedData['internal_batch_code_allocated'] ?? ''); ?>"
                                   data-product-id="<?php echo $productId; ?>" >
                        </td>
       <td>
                            <div class="input-group align-items-center">
                                <input type="text" name="start_slicing" class="auto-save form-control startSlicing"
                                       data-prepid="<?php echo $prep_area->id ?>" data-field="start_slicing"
                                       data-product-id="<?php echo $productId; ?>"
                                       value="<?php echo htmlspecialchars($matchedData['start_slicing'] ?? ''); ?>"
                                       >
                                <button class="btn btn-outline-secondary" type="button" onclick="populateCurrentTime(this)">⏰</button>
                                
                            </div>
                        </td>
        <td>
                            <div class="input-group align-items-center">
                                <input type="text" name="time_finished_slicing" class="auto-save form-control finishSlicing"
                                       data-prepid="<?php echo $prep_area->id ?>" data-field="time_finished_slicing"
                                       data-product-id="<?php echo $productId; ?>"
                                       value="<?php echo htmlspecialchars($matchedData['time_finished_slicing'] ?? ''); ?>"
                                       >
                                <button class="btn btn-outline-secondary" type="button" onclick="populateCurrentTime(this)">⏰</button>
                            </div>
                        </td>
                        <td>
                            <div class="input-group align-items-center">
                                <input type="text" name="temp_of_product_at_start_of_slicing" class="auto-save form-control"
                                       data-prepid="<?php echo $prep_area->id ?>" data-field="temp_of_product_at_start_of_slicing"
                                       data-product-id="<?php echo $productId; ?>"
                                       value="<?php echo htmlspecialchars($matchedData['temp_of_product_at_start_of_slicing'] ?? ''); ?>"
                                       >
                               
                            </div>
                        </td>
                        
                         <td>
                            <div class="input-group align-items-center">
                                <input type="text" name="temp_of_product_at_end_of_slicing" class="auto-save form-control"
                                       data-prepid="<?php echo $prep_area->id ?>" data-field="temp_of_product_at_end_of_slicing"
                                       data-product-id="<?php echo $productId; ?>"
                                       value="<?php echo htmlspecialchars($matchedData['temp_of_product_at_end_of_slicing'] ?? ''); ?>"
                                       >
                               
                            </div>
                        </td>
       <td>
                            <textarea name="comments" class="auto-save form-control comments"
                                      data-prepid="<?php echo $prep_area->id ?>" data-field="comments"
                                      data-product-id="<?php echo $productId; ?>" rows="2" >
                                <?php echo htmlspecialchars($matchedData['comments'] ?? ''); ?>
                            </textarea>
                        </td>
        <td>
            <input type="text" name="enteredBy" 
                   value="<?php echo $matchedData['entered_by'] ?? ''; ?>" 
                   class="auto-save form-control enteredBy" 
                   data-prepid="<?php echo $prep_area->id ?>" 
                   data-field="entered_by"
                   data-product-id="<?php echo $productId; ?>">
        </td>
        <td>
                            <input type="text" name="signature" class="auto-save form-control signature"
                                   data-prepid="<?php echo $prep_area->id ?>" data-field="signature"
                                   data-product-id="<?php echo $productId; ?>"
                                   value="<?php echo htmlspecialchars($matchedData['signature'] ?? ''); ?>"
                                   >
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
 

$(document).ready(function () {
   
    
   let selectedSite = localStorage.getItem('selectedSiteSlicingTempDashBoard');
   console.log("selectedSite",selectedSite)
    if(selectedSite =='' || selectedSite == undefined){
      selectedSite = $(".siteDropdown").val();   
    }
    $(".siteDropdown").val(selectedSite);
   
    $(".tbodySite").addClass('d-none');
    $(".siteId_"+selectedSite).removeClass('d-none');
    $(".siteDropdown").on('change',function(){
      let selectedSite = $(this).val();  
      localStorage.setItem('selectedSiteSlicingTempDashBoard',selectedSite);
      $(".tbodySite").addClass('d-none');
      $(".siteId_"+selectedSite).removeClass('d-none');
    });
    
     document.getElementById('userfile').addEventListener('change', function () {
               this.value = '';
             });
    
 
    
    
  });
  

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
           url: "<?= base_url('Temp/SliceTemp/Slicinghome/save_signature') ?>",
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

$(document).on('blur', '.auto-save', function() {
    const productId = $(this).data('product-id');
    const field = $(this).data('field');
    const value = $(this).val();
    let siteId = $(".siteDropdown").val();
    const prepId = $(this).data('prepid');

    $.ajax({
        url: "<?= base_url('Temp/SliceTemp/Slicinghome/saveRecord') ?>",
        method: "POST",
        data: {
            product_id: productId,
            field: field,
            siteId: siteId,
            prepId: prepId,
            value: value
        },
        success: function(response) {
            console.log("Saved:", response);
        },
        error: function(err) {
            console.error("Error saving data", err);
        }
    });
});


 function handleSaveClick(obj) {
     
      const $button = $(obj);
    $button.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
    
    setTimeout(() => {
      $button.html('<i class="fas fa-save"></i> Save').prop('disabled', false);
    }, 1000);
  }
  
  function populateCurrentTime(button) {
    let parentDiv = button.parentNode;
    let inputField = parentDiv.querySelector('input[type="text"]');
    let currentTime = new Date();
    let hours = currentTime.getHours().toString().padStart(2, '0');
    let minutes = currentTime.getMinutes().toString().padStart(2, '0');
    inputField.value = `${hours}:${minutes}`;

    // Trigger blur so auto-save AJAX runs
    $(inputField).trigger('blur');
}
</script>

                                   