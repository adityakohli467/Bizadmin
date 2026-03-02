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
                            $site_name = $site['site_name'] ?? 'Unknown Site';
                            $selected = $count === 0 ? 'selected' : '';
                    ?>
                        <option <?php echo $selected; ?> value="<?php echo htmlspecialchars($site_id); ?>">
                            <?php echo htmlspecialchars($site_name); ?>
                        </option>
                    <?php $count++; } } else { ?>
                        <option value="">No sites available</option>
                    <?php } ?>
                </select>
                <!-- Exceed Temp Alert -->
                <button class="btn btn-warning d-flex align-items-center gap-1" data-bs-toggle="tooltip" title="View Exceeded Temperatures" onclick="showExceedTemp()">
                    <i class="fas fa-exclamation-triangle"></i> Exceeded Temps
                </button>
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
                        <i class="fas fa-thermometer-half"></i> Record Equipment Temperature/Calibration
                        <span class="text-black ms-2">🗓 <?php echo date('d-m-Y'); ?></span>
                    </h4>
                </div>
                                
                                <div class="d-none alert alert-success alert-dismissible alert-label-icon rounded-label shadow fade show tempSuccessRecorded" role="alert">
                                  <i class="ri-notification-off-line label-icon"></i><strong>Success</strong>
                                   Temperature recorded succesfully.
                                   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive table-card">
                                        <table class="table table-borderless table-hover table-nowrap align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="text-muted">
                                                    <th scope="col" style="width: 25%;">Equipment</th>
                                                    <th scope="col" style="width: 10%;">Record By</th>
                                                    <th scope="col">Record At </th>
                                                    <th scope="col" style="width: 5%;">Degree/Secs</th>
                                                    <th scope="col" style="width: 20%;">Entered By </th>
                                                    <th scope="col" style="width: 25%;"> Comments</th>
                                                   
                                                    <th scope="col" style="width: 5%;">Add Photo</th>
                                                    <th scope="col" style="width: 5%;">Action</th>
                                                </tr>
                                            </thead>
                                         <?php    
                                         
                                       
                                         
                                         if(isset($site_detail) && !empty($site_detail)){  ?>
                                       
                                          <?php  foreach($site_detail as $AllSites) { ?>
                                           <?php  $staffComments = unserialize($AllSites['staff_comments']);?>

                                           <?php  foreach($EquipListForDash as $EquipList) {    ?>
                                           
                                            <?php  if(isset($EquipList) && !empty($EquipList) && $EquipList['site_id'] == $AllSites['id']) { ?>
                                            <tbody class="site_<?php echo $AllSites['id'] ?> d-none tbodySite" >
                                               
                                        <th colspan="8" class="text-black w-100 " style="background-color: #dff0fa;"> <b><?php echo $EquipList['prep_name']; ?></b></th> 
                                        
                                              <?php  foreach($EquipList['equipments'] as $Equip) {  ?> 
                                              
                                              <?php $eqipID = $Equip['id']; ?>
                                              <?php $is_completed = (isset($todaysTempData[$eqipID]['equip_temp']) ? 'disabled' : '');  ?>
                                              <?php $completeText = (isset($todaysTempData[$eqipID]['equip_temp']) ? '⏱ Completed' : '⏰ Complete');  ?>  
                                              <?php $classComplete = (isset($todaysTempData[$eqipID]['equip_temp']) ? '⏱ btn-success' : '⏰ btn-orange');  ?>    
                                                 <tr>
                                               
                                                  <input type="hidden" name="equip_id" value="<?php echo $eqipID; ?>">     
                                                    <td><?php echo (isset($Equip['equip_name']) ? $Equip['equip_name'] : ''); ?></td>
                                                    <td><?php echo (isset($Equip['equip_time']) ? date('h:i a',strtotime($Equip['equip_time'])) : ''); ?></td>
                                                     <td class="capturedTime_<?php echo $eqipID; ?>">
                                                    <?php echo (isset($todaysTempData[$eqipID]['entered_time']) ? date('h:i a',strtotime($todaysTempData[$eqipID]['entered_time'])) : '') ?> 
                                                </td>
                                                    <td><input type="hidden" class="temp_min_<?php echo $Equip['id']; ?>" value="<?php echo $Equip['temp_min']; ?>"><input type="hidden" class="temp_max_<?php echo $Equip['id']; ?>" value="<?php echo $Equip['temp_max']; ?>"><input type="text" <?php echo $is_completed ?> value="<?php echo (isset($todaysTempData[$eqipID]['equip_temp']) ? $todaysTempData[$eqipID]['equip_temp'] : '') ?>" name="equip_temp_<?php echo $Equip['id']; ?>" class="form-control"/></td>
                                                    <td><input type="text" <?php echo $is_completed ?> value="<?php echo (isset($todaysTempData[$eqipID]['entered_by']) ? $todaysTempData[$eqipID]['entered_by'] : '') ?>" name="entered_by_<?php echo $Equip['id']; ?>" class="form-control"/></td>
                                                   
                                                    
                                                    <td>
                                                     <select class="form-select staffComments" <?php echo $is_completed ?>  name="staff_comments_<?php echo $Equip['id']; ?>" >
                                                         <option value=""> Select Comment</option>
                                                     <?php if(!empty($staffComments)) { foreach($staffComments as $staffComment)  { ?>
                                                     <?php if(isset($todaysTempData[$eqipID]['staff_comments']) && $todaysTempData[$eqipID]['staff_comments'] == $staffComment) {  ?>
                                                     <option value="<?php echo $staffComment; ?>" selected="selected"><?php echo $staffComment; ?></option>
                                                     <?php } else { ?>
                                                     <option value="<?php echo $staffComment; ?>"><?php echo $staffComment; ?></option>
                                                     <?php } ?>
                                                     <?php } } ?>
                                                     </select>   
                                                    </td>
                                                   
                                                    <?php if(!$is_completed && $Equip['is_attchmentRequired']==1) { ?>
                                                     <td class="attchmentN_<?php echo $Equip['id']; ?>">
                                                     <i class="ri-attachment-2 align-bottom me-1 mx-3 fs-16 " style="color: red;" onclick="showTempAttchmentModal(<?php echo $Equip['id'] ?>)"></i>
                                                     </td>
                                                     <?php }else if($is_completed && $Equip['is_attchmentRequired']==1) {   ?>
                                                     <td> <i class="mdi mdi-file-eye align-bottom me-1 mx-3 fs-16" style="color: red;" onclick="fetchAttachment(<?php echo $Equip['id'] ?>)"></i></td>
                                                      <?php }else { ?>
                                                      <td></td>
                                                      <?php } ?>
                                                    
                                                    <td><button name="complete_<?php echo $Equip['id']; ?>" class="btn btn-sm <?php echo $classComplete; ?>" <?php echo $is_completed ?> onclick="conmpleteThisRow(<?php echo $Equip['id']; ?>,<?php echo $AllSites['id'] ?>,<?php echo $EquipList['prep_id'] ?>)"><?php echo $completeText; ?></button></td>
                                                </tr>
                                                
                                            <?php } ?>
                                           </tbody>
                                            <?php } } ?>
                                           
                                            <?php }   ?>
                                              <?php }   ?>
                                        </table><!-- end table -->
                                    </div><!-- end table responsive -->
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div><!-- end col -->

                       <!-- end col -->
                       <div class="col-12 d-none exceededDiv">
                            <div class="card  h-96">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1 text-faded"><i class="ri-close-fill fs-22" title="Close" onclick="showExceedTemp()"></i> Exceeded Range Equipments</h4>
                                    <div class="flex-shrink-0">
                                     <?php $currentDate = date("d-m-Y"); $date7DaysAgo = date("d-m-Y", strtotime("-7 days")); ?>  
                                     <?php echo "🗓 ".$date7DaysAgo." To ".$currentDate; ?>
                                    </div>
                                </div><!-- end card header -->
                                <div class="card-body pt-0">
                                    <ul class="list-group list-group-flush border-dashed d-flex">
                                        <li class="list-group-item ps-0 liHeader">
                                        <div class="row align-items-center px-3">
                                           
                                             <div class="col-4 fw-bold">Equip Name</div>
                                             <div class="col-3 fw-bold">Comments</div>
                                             <div class="col-2 fw-bold">Old Temp</div>
                                             <div class="col-2 fw-bold">New Temp</div>
                                             <div class="col-1 fw-bold"></div>
                                            </div>
                                            </li>
                                    </ul>        
                                    <ul class="list-group list-group-flush border-dashed">
                                        <?php if(!empty($exceededTempData)) { foreach($exceededTempData as $exceededData) {  ?>
                                        <?php $managerComments = (isset($exceededData['manager_comments']) ? unserialize($exceededData['manager_comments']) : '') ?>
                                        <li class="list-group-item ps-0">
                                            <div class="row align-items-center g-3">
                                               
                                                <div class="col-4">
                                                    <h4 class="text-faded mt-0 mb-1 fs-13 fw-bold"><?php echo $exceededData['site_name'] ?></h4>
                                                    <a href="#" class="text-reset fs-14 mb-0"><?php echo $exceededData['equip_name'] ?></a>
                                                </div>
                                                <div class="col-3">
                                                <select class="form-select" id="manager_comments_<?php echo $exceededData['id']; ?>" name="manager_comments_<?php echo $exceededData['id']; ?>">
                                                    <option value="">Select Comment</option>
                                                     <?php if(!empty($managerComments)) { foreach($managerComments as $managerComment)  { ?>
                                                     <?php if(isset($todaysTempData[$exceededData['id']]['manager_comments']) && $todaysTempData[$exceededData['id']]['manager_comments'] == $managerComment) {  ?>
                                                     <option value="<?php echo $managerComment; ?>" selected="selected"><?php echo $managerComment; ?></option>
                                                     <?php } else { ?>
                                                     <option value="<?php echo $managerComment; ?>"><?php echo $managerComment; ?></option>
                                                     <?php } ?>
                                                     <?php } } ?>
                                                     </select> 
                                                </div>
                                                <div class="col-2">
                                                <input type="text" class="form-control" readonly value="<?php echo $exceededData['equip_temp'] ?>" style="padding: 0.5rem 0.6rem;" />
                                                </div>
                                                <div class="col-2">
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
                                                            <label> Select multiple images and upload</label></br>
                                                            <small>Max upload size 2 Mb</small>
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
                                            
          <div id="attachmentEquipModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
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
                                            <div class="swiper-wrapper appendSwiperImages">
                                                
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
                            
                            
                                            <script>
function conmpleteThisRow(equipId,siteId,prepId){
   
    let currentTime = getCurrentTime();
    let equipTemp = document.querySelector('[name="equip_temp_' + equipId + '"]').value;
 
    if(equipTemp === ''){
        alert("Please enter temperature/calibration before completing");
        return false;
    }
    if ($('.attchmentN_'+equipId).length > 0) {
     alert("Please attach a photo before completing this record");
     return false;
     } 
    
    
let tempMin = +$(".temp_min_" + equipId).val();
let tempMax = +$(".temp_max_" + equipId).val();
let equip_IsTempok = 'notOk';
// console.log("equipTemp",equipTemp);console.log("tempMin",tempMin);console.log("tempMax",tempMax); return false;
if (
    (equipTemp >= Math.min(tempMin, tempMax) && equipTemp <= Math.max(tempMin, tempMax))
) {
   equip_IsTempok = 'ok';
} else if ($('[name="staff_comments_' + equipId + '"]').val() === '') {
    alert("The temperature/calibration entered is outside the acceptable range. Please select the action taken from the dropdown of comments.");
    $('[name="staff_comments_' + equipId + '"]').removeAttr("disabled");
    return false;
}

    $(".capturedTime_"+ equipId).html(currentTime)
     
        let data = [
        {
        equip_id: equipId,
        equip_IsTempok: equip_IsTempok,
        site_id: siteId,
        prep_id: prepId,
        equip_temp: equipTemp,
        entered_by: $('[name="entered_by_' + equipId + '"]').val(),
        staff_comments: $('[name="staff_comments_' + equipId + '"]').val(),
    },
];

$('[name="equip_temp_' + equipId + '"], [name="entered_by_' + equipId + '"],[name="complete_' + equipId + '"], [name="staff_comments_' + equipId + '"]').prop('disabled', true);
    $('[name="complete_' + equipId + '"]').html('Completed');
     $('[name="complete_' + equipId + '"]').removeClass('btn-orange'); $('[name="complete_' + equipId + '"]').addClass('btn-success');
    
formData = JSON.stringify(data);
       
           $.ajax({
            type: 'POST',
            url: 'home/saveTempDashboardData',
            data: formData,
            success: function (response) {
              $(".tempSuccessRecorded").removeClass("d-none");
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        }); 
    
}
function saveExceedTemp(obj,id){
    let correctedTemp = $("#correctedTemp_"+id).val();
    let manager_comments = $("#manager_comments_"+id).val();
    let buttonHtml = $(obj).parents(".exceedSaveBtn");
      $.ajax({
            type: 'POST',
            url: 'home/updateExceededTemp',
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
function showTempAttchmentModal(id){
 $("#equipId").val(id);
 $("#tempAttachmentModal").modal('show');
}
$(".siteDropdown").on('change',function(){
    let siteId = $(this).val();
    localStorage.setItem('selectedEquipSiteDashBoard',siteId);
$(".tbodySite").each(function(index, element) {
    if (!$(element).hasClass("d-none")) {
        console.log($(element).val());
        $(element).addClass("d-none");
    }
});
$(".site_"+siteId).removeClass("d-none");
})
function showExceedTemp(){
    $(".exceededDiv").toggleClass("d-none");
    $(".tempDiv").toggleClass("col-lg-5");
     $(".exceededDiv").toggleClass("col-lg-7");
     
     let isMobileOrTablet = $(window).width() <= 768; console.log("isMobileOrTabletWW",$(window).width())
    if (isMobileOrTablet) {
            $("html, body").animate({ scrollTop: $(document).height() }, "slow");
        }
}
$(document).ready(function(){
    
    let siteId = localStorage.getItem('selectedEquipSiteDashBoard');
    console.log("siteId",siteId)
    if(siteId =='' || siteId == undefined){
      siteId = $(".siteDropdown").val();   
    }
    $(".siteDropdown").val(siteId);
    
  $(".site_"+siteId).removeClass("d-none");  
  
  $(".uploadAttachmentButton").on("click", function () {
    var formData = new FormData($("#attachmentUploadForm")[0]);

    $(".uploadAttachmentButton").html("Loading...");

    $.ajax({
        type: "POST",
        url: "/Temp/home/uploadTemperatureAttachment",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (res) {

            $(".uploadAttachmentButton").html("Save");

            if (res.status === false) {
                alert("Upload failed: " + res.message);
                return;
            }

            // SUCCESS
            alert("Files uploaded successfully!");

            $("#tempAttachmentModal").modal('hide');

            let className = 'attchmentN_' + $("#equipId").val();
            $('.' + className).removeClass(className);
        },
        error: function (xhr, status, error) {
            $(".uploadAttachmentButton").html("Save");
            alert("Something went wrong: " + error);
        }
    });
});

    

    
    
})

  function fetchAttachment(equipId){
      let orzName =  "<?php echo $this->session->userdata('tenantIdentifier'); ?>";
      $('.appendSwiperImages').html('');     
       $.ajax({
            type: "POST",
            url: "/Temp/home/fetchAttachmentUploadedToday", // Replace with your controller's URL
            data: 'equipId='+equipId,
            success: function (response) {
                let result= JSON.parse(response);
                console.log(result)
           result.forEach(function (filename) {
           let imageUrl = "/uploaded_files/"+orzName+"/Temp/TemperatureAttachments/" + filename;
           let slide = '<div class="swiper-slide">' +
                    '<img src="' + imageUrl + '" alt="" class="img-fluid" style="width: 100%;" />' +
                    '</div>';
            
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
            // You can handle saving here (AJAX, local storage, etc.)
            alert('Manager Signature saved: ' + signature);
            $('#managerSignatureBox').slideUp(); // Optional: hide after save
        }
    


      $.ajax({
            url: '/Temp/home/save_signature', // Update with your actual controller/method
            type: 'POST',
            data: { signature: signature },
            dataType: 'json',
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