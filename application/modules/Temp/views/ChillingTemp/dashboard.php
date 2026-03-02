 <style>
 @media (min-width: 992px) {
        .foodTempTable {
            table-layout: fixed;
        }
    }
    .foodTempTable .input-group-text{
    font-size:10px !important;
    padding:9px !important;
     }
   .foodTempTable .form-control{
     font-size:12px !important;
    } 
@media (max-width: 768px) {
    .foodTempTable .input-group {
     width : 112px !important;   
    }
}
</style>
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
                            <?php echo trim(htmlspecialchars($site_name)); ?>
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
                        <i class="fas fa-thermometer-half"></i> Record  Chilling Temperature
                        <span class="text-black ms-2">🗓 <?php echo date('d-m-Y'); ?></span>
                    </h4>
                    <small><b><i> Click on clock icon to auto populate all "time" values</b></i></small>
                </div>
                          
                                <div class="card-body">
                                  <input type="hidden" class="minTempAtFinish" value="<?php echo $minTempAtFinish ?>">
                                   <input type="hidden" class="minTempAfterTwoHrs" value="<?php echo $minTempAfterTwoHrs ?>">
                                    <input type="hidden" class="minTempAfterFourHrs" value="<?php echo $minTempAfterFourHrs ?>">
                                    <div class="table-responsive table-card">
            <table class="table table-borderless table-hover table-nowrap align-middle mb-0 foodTempTable">
               <thead class="table-light">
                                                <tr class="text-muted">
                                                    <th scope="col">Product Name</th>
                                                    <th scope="col">Start Time</th>
                                                    <th scope="col">Finish Time </th>
                                                    <th scope="col">Temp at Finish</th>
                                                    <th scope="col">Chilling Start Time </th>
                                                    <th scope="col">Time after 2 Hrs </th>
                                                    <th scope="col">Temp after 2 Hrs</th>
                                                    <th scope="col">Time after 4 Hrs</th>
                                                    <th scope="col">Temp after 4 Hrs</th>
                                                    <th scope="col">Entered by</th>
                                                    <th scope="col"></th>
                                                </tr>
                                            </thead>
     <?php if(!empty($site_detail)) {      
    foreach($site_detail as $sites) { ?>
    <?php  $staffComments = unserialize($sites['staff_comments']);?>
    <?php  $prep_areas = json_decode($sites['prep_areas']); ?>
      <?php 
      foreach($prep_areas as $prep_area) {  ?>
       <tbody class="prep_<?php echo $prep_area->id ?>  tbodySite <?php echo 'siteId_'.$sites['id'] ?>" > 
       <th colspan="11" class="text-black w-100 " style="background-color: #dff0fa;"> <b><?php echo $prep_area->prep_name; ?></b></th> 

         <?php if(isset($todaysChillingTempData) && !empty($todaysChillingTempData)) {  ?>
         <?php foreach($todaysChillingTempData as $chillingTempData) { 
          if($chillingTempData['prep_id'] == $prep_area->id && $chillingTempData['site_id'] == $sites['id']){ ?>
          <?php
            if(isset($chillingTempData['finishTime']) && $chillingTempData['finishTime'] !=''){
             $finishTime = $chillingTempData['finishTime'];
             $timeAfterTwoHours = date("g:i A", strtotime($finishTime . " +2 hours"));
             $timeAfterFourHours = date("g:i A", strtotime($finishTime . " +4 hours"));
            }else{
            $finishTime =''; $timeAfterTwoHours = '';  $timeAfterFourHours = '';
            }
            ?>
                
                     <tr class="parentRow">
                    <input type="hidden" class="tempcID" value="<?php echo isset($chillingTempData['id']) ? $chillingTempData['id'] : ''; ?>">     
                    <td> <input type="text" name="productName" class="form-control productName" disabled value="<?php echo $chillingTempData['foodName'] ?>"> </td> 
                   
                    <td> <input type="text" name="startTime" class="form-control startTime" readonly value="<?php echo $chillingTempData['startTime'] ?? '' ?>"> </td>
                    <td> 
        <div class="input-group">
            <input type="text" name="finishTime" class="form-control finishTime" readonly value="<?php echo $finishTime ?? '' ?>">
            <span class="input-group-text" onclick="populateCurrentTime(this)">⏰</span>
        </div>
    </td>
                <td> <input type="text" name="tempFinish" class="form-control tempFinish w-75" value="<?php echo $chillingTempData['tempAtFinish'] ?? '' ?>"> </td>
                    <td> <input type="text" name="chillingStartTime" class="form-control chillingStartTime" readonly value="<?php echo $finishTime ?? '' ?>"> </td>
                   <td> <input type="text" name="timeAfterTwoHr" class="form-control afterTwoHr timeAfterTwoHr" readonly value="<?php echo $timeAfterTwoHours ?? '' ?>"> </td> 
                   <td> <input type="text" name="tempAfterTwoHr" class="form-control tempAfterTwoHr w-75" value="<?php echo $chillingTempData['tempAfterTwohours'] ?? '' ?>"> </td>
                   <td> <input type="text" name="timeAfterFourHr" class="form-control afterFourHr timeAfterFourHr" readonly value="<?php echo $timeAfterFourHours ?? '' ?>"> </td>
                   <td> <input type="text" name="tempAfterFourHr" class="form-control tempAfterFourHr w-75" value="<?php echo $chillingTempData['tempAfterFourhours'] ?? '' ?>"> </td>
                  <td> <input type="text" name="entered_by" class="form-control enteredBy" value="<?php echo $chillingTempData['entered_by'] ?? '' ?>"> </td>
                  <td>
                  <?php if($chillingTempData['is_completed'] == 1){ ?>
                  <button name="complete" class="btn btn-sm btn-success" readonly>⏰ <?php echo 'Completed'; ?></button>
                  <?php  }else { ?>
                 <button name="save" class="btn btn-sm btn-success" onclick="completeThisRow(this,<?php echo $prep_area->id; ?>,<?php echo $sites['id'] ?>,'save')">⏰ <?php echo 'Save'; ?></button> 
                 <button name="complete" class="btn btn-sm btn-success" onclick="completeThisRow(this,<?php echo $prep_area->id; ?>,<?php echo $sites['id'] ?>,'complete')">⏰ Complete</button>
                   <?php  } ?> 
                  </td></tr>  
                   <?php } }  ?>
                 <?php  } ?>
                   <tr class="parentRow">
     <input type="hidden" class="tempcID" value="">                     
    <td> <input type="text" name="productName" class="form-control productName"> </td> 
    
    <td> 
        <div class="input-group">
            <input type="text" name="startTime" class="form-control startTime">
            <span class="input-group-text" onclick="populateCurrentTime(this)">⏰</span>
        </div>
    </td>
    <td> 
        <div class="input-group">
            <input type="text" name="finishTime" class="form-control finishTime">
            <span class="input-group-text" onclick="populateCurrentTime(this)">⏰</span>
        </div>
    </td>
    <td> <input type="text" name="tempFinish" class="form-control tempFinish"> </td>
    <td> 
        <div class="input-group">
            <input type="text" name="chillingStartTime" class="form-control chillingStartTime">
            <span class="input-group-text" onclick="populateChillingStartTime(this)">⏰</span>
        </div>
    </td>
    <td> <input type="text" name="timeAfterTwoHr" class="form-control afterTwoHr timeAfterTwoHr"> </td> 
    <td> <input type="text" name="tempAfterTwoHr" class="form-control tempAfterTwoHr"> </td>
    <td> <input type="text" name="timeAfterFourHr" class="form-control afterFourHr timeAfterFourHr"> </td>
    <td> <input type="text" name="tempAfterFourHr" class="form-control tempAfterFourHr"> </td>
     <td> <input type="text" name="entered_by" class="form-control enteredBy"> </td>
    <td>
        <button name="save" class="btn btn-sm btn-success" onclick="completeThisRow(this,<?php echo $prep_area->id; ?>,<?php echo $sites['id'] ?>,'save')">⏰ <?php echo 'Save'; ?></button>
        <button name="complete" class="btn btn-sm btn-orange" onclick="completeThisRow(this,<?php echo $prep_area->id; ?>,<?php echo $sites['id'] ?>,'complete')">⏰ <?php echo 'Complete'; ?></button>
        <button class="btn btn-success btn-sm plus-btn clone-btn first-row" type="button">+</button>
        <button class="btn btn-danger btn-sm minus-btn d-none mx-2" type="button">-</button>
    </td>
</tr>

              </tbody>
                 <?php } ?>
                <?php } ?>
                <?php } ?>
                </table>
               </div>
              </div>
             </div>
           </div>
           </div>
            </div>
<script>
  

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
    
   let selectedSite = localStorage.getItem('selectedSiteChillingTempDashBoard');
   console.log("selectedSite",selectedSite)
    if(selectedSite !='' ){
     $(".siteDropdown").val(selectedSite);
    }
    
   
    $(".tbodySite").addClass('d-none');
    $(".siteId_"+selectedSite).removeClass('d-none');
    $(".siteDropdown").on('change',function(){
      let selectedSite = $(this).val();  
      localStorage.setItem('selectedSiteChillingTempDashBoard',selectedSite);
      $(".tbodySite").addClass('d-none');
      $(".siteId_"+selectedSite).removeClass('d-none');
    });
    
   });
  
 function completeThisRow(obj,prepId,siteId,type){
  let currentTime = getCurrentTime();     
  let foodName = $(obj).parents(".parentRow").find(".productName").val();
  let OrderName = $(obj).parents(".parentRow").find(".OrderName").val()
  let minTempAfterFourHrs = $(".minTempAfterFourHrs").val();
  let minTempAfterTwoHrs = $(".minTempAfterTwoHrs").val();
  let minTempAtFinish = $(".minTempAtFinish").val();
  
  let startTime = $(obj).parents(".parentRow").find(".startTime").val();
  let finishTime = $(obj).parents(".parentRow").find(".finishTime").val();
  let tempAtFinish = $(obj).parents(".parentRow").find(".tempFinish").val();
  let chillingStartTime = $(obj).parents(".parentRow").find(".chillingStartTime").val();
  let timeAfterTwohours = $(obj).parents(".parentRow").find(".timeAfterTwoHr").val();
  let tempAfterTwohours = $(obj).parents(".parentRow").find(".tempAfterTwoHr").val()
  let timeAfterFourhours = $(obj).parents(".parentRow").find(".timeAfterFourHr").val();
  let tempAfterFourhours =  $(obj).parents(".parentRow").find(".tempAfterFourHr").val();
  let enteredBy = $(obj).parents(".parentRow").find(".enteredBy").val()
   let tempcID = $(obj).parents(".parentRow").find(".tempcID").val();

  let isTempok = 'ok';
  if((parseFloat(tempAtFinish) < parseFloat(minTempAtFinish)) || (parseFloat(tempAfterFourhours) > parseFloat(minTempAfterFourHrs)) || (parseFloat(tempAfterTwohours) > parseFloat(minTempAfterTwoHrs))){
   isTempok = 'notOk';
   }
  
//   if(tempAtFinish==''){
//     alert("Please enter Temperature at finish"); 
//     return false;
//   }
  
  if(type=='save'){
  $(obj).html('⏰ Saved');
  $(obj).removeClass('btn-orange'); $(obj).addClass('btn-success completSuccessBtn');    
  }else{
  $(obj).html('⏰ Completed');
  $(obj).removeClass('btn-orange'); $(obj).addClass('btn-success completSuccessBtn');    
  }
  
 
  $(obj).parents(".parentRow").find('button:not(.minus-btn):not(.plus-btn)').prop('disabled', true);
 
   let data = [
        {
        foodName: foodName,
        OrderName: OrderName,
        startTime: startTime,
        finishTime: finishTime,
        tempAtFinish: tempAtFinish,
        chillingStartTime: chillingStartTime,
        timeAfterTwohours: timeAfterTwohours,
        tempAfterTwohours: tempAfterTwohours,
        timeAfterFourhours: timeAfterFourhours,
        tempAfterFourhours: tempAfterFourhours,
        is_completed : type =='save' ? 0 : 1,
        id : tempcID,
        minTempAtFinish: minTempAtFinish,
        minTempAfterFourHrs : minTempAfterFourHrs,
        minTempAfterTwoHrs : minTempAfterTwoHrs,
        isTempok: isTempok,
        site_id: siteId,
        prep_id: prepId,
        entered_by: enteredBy,
       
    },
];

      formData = JSON.stringify(data);
       
           $.ajax({
            type: 'POST',
            url: '/Temp/home/saveChillingDashboardData',
            data: formData,
            success: function (response) {
              $(".tempSuccessRecorded").removeClass("d-none");
              location.reload();
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        }); 
  
  console.log("currentTime",currentTime);
 } 
 


function populateCurrentTime(button) {
    let parentDiv = button.parentNode;
    let inputField = parentDiv.querySelector('input[type="text"]');
    inputField.value = getCurrentTime();
}

function populateChillingStartTime(button) {
    var parentDiv = button.parentNode;
    var currentTime = new Date();
    let inputField = parentDiv.querySelector('input[type="text"]');
   
    inputField.value = getCurrentTime();
    
    let timeAfterTwoHours = new Date(currentTime.getTime() + 2 * 60 * 60 * 1000);
    let formattedTimeAfterTwoHours = formatTime(timeAfterTwoHours);
    
    let timeAfterFourHours = new Date(currentTime.getTime() + 4 * 60 * 60 * 1000);
    let formattedTimeAfterFourHours = formatTime(timeAfterFourHours);
    
    // Populate input fields
    if(formattedTimeAfterTwoHours){
    $(button).parents(".parentRow").find(".afterTwoHr").val(formattedTimeAfterTwoHours) 
    }
    if(formattedTimeAfterFourHours){
    $(button).parents(".parentRow").find(".afterFourHr").val(formattedTimeAfterFourHours) 
    }
}

function formatTime(date) {
    let hours = date.getHours();
    let minutes = date.getMinutes();
    let ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // Handle midnight (0 hours)
    minutes = minutes < 10 ? '0' + minutes : minutes; // Add leading zero if minutes are less than 10
    return hours + ':' + minutes + ' ' + ampm;
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
            url: '/Temp/ChillingTemp/Chillinghome/save_signature', // Update with your actual controller/method
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
                                   