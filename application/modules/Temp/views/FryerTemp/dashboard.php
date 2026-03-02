<div class="container-fluid mb-5" style="margin-top: 130px !important;">
    
    <nav class="navbar navbar-light bg-light mb-3 p-2">
        <div class="container-fluid">
            <div class="d-flex flex-wrap gap-2 w-100">
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
                
                <a class="btn btn-primary d-flex align-items-center gap-1" data-bs-toggle="tooltip" href="/Temp/FryerTemp/Fryerhome/listProduct">
                    Add product
                </a>
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
                <div class="card-header">
                    <h4 class="card-title mb-0 text-black">
                        <i class="fas fa-thermometer-half"></i> Record Fryer Temperature
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
                                   
                                    <th scope="col">Start Cooking Time</th>
                                    <th scope="col">Finish Cooking Time</th>
                                    <th scope="col">Temp at End of Cooking</th>
                                    <th scope="col">Chilling Start Time</th>
                                    <th scope="col">Chilling Finish Time</th>
                                    <th scope="col">Temp at End of Chilling</th>
                                    <th scope="col">Recorded By</th>
                                    <th scope="col">Action Taken</th>
                                    <!--<th scope="col">Attachments</th>-->
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            
                            <?php if(!empty($site_detail)) {      
                                foreach($site_detail as $sites) { ?>
                                    <?php $staffComments = unserialize($sites['staff_comments']);?>
                                    <?php $prep_areas = json_decode($sites['prep_areas']); ?>
                                    <?php foreach($prep_areas as $prep_area) { ?>
                                        <tbody class="prep_<?php echo $prep_area->id ?> tbodySite <?php echo 'siteId_'.$sites['id'] ?>"> 
                                            <tr>
                                                <th colspan="12" class="text-black w-100" style="background-color: #dff0fa;">
                                                    <b><?php echo $prep_area->prep_name; ?></b>
                                                </th>
                                            </tr>

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
                                                    <input type="time" name="start_cooking_time"
                                                           value="<?php echo $matchedData['start_cooking_time'] ?? ''; ?>" 
                                                           class="auto-save form-control start_cooking_time" 
                                                           data-prepid="<?php echo $prep_area->id ?>" 
                                                           data-product-id="<?php echo $productId; ?>">
                                                </td>
                                                <td>
                                                    <input type="time" name="finish_cooking_time"
                                                           value="<?php echo $matchedData['finish_cooking_time'] ?? ''; ?>" 
                                                           class="auto-save form-control finish_cooking_time" 
                                                           data-prepid="<?php echo $prep_area->id ?>" 
                                                           data-product-id="<?php echo $productId; ?>">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="temp_end_cooking"
                                                           value="<?php echo $matchedData['temp_end_cooking'] ?? ''; ?>" 
                                                           class="auto-save form-control temp_end_cooking" 
                                                           data-prepid="<?php echo $prep_area->id ?>" 
                                                           data-product-id="<?php echo $productId; ?>"
                                                           placeholder="°C">
                                                </td>
                                                <td>
                                                    <input type="time" name="time_chilling_start"
                                                           value="<?php echo $matchedData['time_chilling_start'] ?? ''; ?>" 
                                                           class="auto-save form-control time_chilling_start" 
                                                           data-prepid="<?php echo $prep_area->id ?>" 
                                                           data-product-id="<?php echo $productId; ?>">
                                                </td>
                                                <td>
                                                    <input type="time" name="time_chilling_finished"
                                                           value="<?php echo $matchedData['time_chilling_finished'] ?? ''; ?>" 
                                                           class="auto-save form-control time_chilling_finished" 
                                                           data-prepid="<?php echo $prep_area->id ?>" 
                                                           data-product-id="<?php echo $productId; ?>">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="temp_end_chilling"
                                                           value="<?php echo $matchedData['temp_end_chilling'] ?? ''; ?>" 
                                                           class="auto-save form-control temp_end_chilling" 
                                                           data-prepid="<?php echo $prep_area->id ?>" 
                                                           data-product-id="<?php echo $productId; ?>"
                                                           placeholder="°C">
                                                </td>
                                                <td>
                                                    <input type="text" name="recorded_by" 
                                                           value="<?php echo $matchedData['recorded_by'] ?? ''; ?>" 
                                                           class="auto-save form-control recorded_by" 
                                                           data-prepid="<?php echo $prep_area->id ?>" 
                                                           data-product-id="<?php echo $productId; ?>">
                                                </td>
                                                <td>
                                                    <select class="auto-save form-select staff_comments" 
                                                            disabled 
                                                            name="staff_comments" 
                                                            data-prepid="<?php echo $prep_area->id ?>" 
                                                            data-product-id="<?php echo $productId; ?>">
                                                        <option value="">Action Taken</option>     
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
                                                <!--<td class="attchmentN_<?php echo $prep_area->id; ?>">-->
                                                <!--    <i class="ri-attachment-2 align-bottom me-1 mx-3 fs-16" -->
                                                <!--       style="color: red; cursor: pointer;" -->
                                                <!--       onclick="showTempAttchmentModal()"></i>-->
                                                <!--</td> -->
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

<!-- Exceeded Temperature Section -->
<div class="col-12 d-none exceededDiv">
    <div class="card h-96">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1 text-faded">
                <i class="ri-close-fill fs-22" title="Close" onclick="showExceedTemp()" style="cursor: pointer;"></i> 
                Food not within acceptable range
            </h4>
            <div class="flex-shrink-0">
                <?php $currentDate = date("d-m-Y"); $date7DaysAgo = date("d-m-Y", strtotime("-7 days")); ?>  
                <?php echo "🗓 ".$date7DaysAgo." To ".$currentDate; ?>
            </div>
        </div>
        <div class="card-body pt-0">
            <ul class="list-group list-group-flush border-dashed d-flex">
                <li class="list-group-item ps-0 liHeader">
                    <div class="row align-items-center px-3">
                        <div class="col-2 fw-bold">Date</div>
                        <div class="col-3 fw-bold">Product Name</div>
                        <div class="col-2 fw-bold">Comments</div>
                        <div class="col-2 fw-bold">Old Temp</div>
                        <div class="col-2 fw-bold">New Temp</div>
                    </div>
                </li>
            </ul>        
            <ul class="list-group list-group-flush border-dashed">
                <?php if(!empty($exceededTempData)) { foreach($exceededTempData as $exceededData) { ?>
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
                                    <?php if(!empty($managerComments)) { foreach($managerComments as $managerComment) { ?>
                                        <?php if(isset($todaysTempData[$exceededData['id']]['manager_comments']) && $todaysTempData[$exceededData['id']]['manager_comments'] == $managerComment) { ?>
                                            <option value="<?php echo $managerComment; ?>" selected="selected"><?php echo $managerComment; ?></option>
                                        <?php } else { ?>
                                            <option value="<?php echo $managerComment; ?>"><?php echo $managerComment; ?></option>
                                        <?php } ?>
                                    <?php } } ?>
                                </select> 
                            </div>
                            <div class="col-1">
                                <input type="text" class="form-control" readonly value="<?php echo $exceededData['temp_end_chilling'] ?>" style="padding: 0.5rem 0.6rem;" />
                            </div>
                            <div class="col-1">
                                <input type="text" style="padding: 0.5rem 0.6rem;" class="form-control" id="correctedTemp_<?php echo $exceededData['id']; ?>" value="<?php echo $exceededData['correctedTemp'] ?>" />
                            </div>
                            <div class="col-1">
                                <button class="btn btn-success exceedSaveBtn">
                                    <i class="ri-save-2-fill" onclick="saveExceedTemp(this,<?php echo $exceededData['id']; ?>)"></i>            
                                </button>
                            </div>
                        </div>
                    </li>
                <?php } } ?>
            </ul>
        </div>
    </div>
</div>            
</div>
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
            clonedRow.find('.completSuccessBtn').removeClass('btn-success'); 
            clonedRow.find('.completSuccessBtn').addClass('btn-orange');
        } else if ($(this).hasClass('minus-btn')) {
            $(this).closest('tr').remove();
        }
    });
    
    let selectedSite = localStorage.getItem('selectedSiteFoodTempDashBoard');
    console.log("selectedSite", selectedSite)
    if(selectedSite == '' || selectedSite == undefined){
        selectedSite = $(".siteDropdown").val();   
    }
    $(".siteDropdown").val(selectedSite);
   
    $(".tbodySite").addClass('d-none');
    $(".siteId_"+selectedSite).removeClass('d-none');
    
    $(".siteDropdown").on('change', function(){
        let selectedSite = $(this).val();  
        localStorage.setItem('selectedSiteFoodTempDashBoard', selectedSite);
        $(".tbodySite").addClass('d-none');
        $(".siteId_"+selectedSite).removeClass('d-none');
    });
    
    document.getElementById('userfile').addEventListener('change', function () {
        this.value = '';
    });
});

function completeThisRow(obj, productId, prepId, siteId){
    let currentTime = getCurrentTime();     
    let foodName = $(obj).parents(".parentRow").find(".productName").val();
    let foodMinTemp = $(".foodMinTemp").val();
    let foodMaxTemp = $(".foodMaxTemp").val();
    
    let staff_comments = $(obj).parents(".parentRow").find(".staff_comments").val();
    
    // Get all new fields
    let start_cooking_time = $(obj).parents(".parentRow").find(".start_cooking_time").val();
    let finish_cooking_time = $(obj).parents(".parentRow").find(".finish_cooking_time").val();
    let temp_end_cooking = $(obj).parents(".parentRow").find(".temp_end_cooking").val();
    let time_chilling_start = $(obj).parents(".parentRow").find(".time_chilling_start").val();
    let time_chilling_finished = $(obj).parents(".parentRow").find(".time_chilling_finished").val();
    let temp_end_chilling = $(obj).parents(".parentRow").find(".temp_end_chilling").val();
    let recorded_by = $(obj).parents(".parentRow").find(".recorded_by").val();
    
    let food_IsTempok = 'ok';
    
   
    
    if(start_cooking_time == ''){
        alert("Please enter start cooking time to complete"); 
        return false;
    }
    
    if(finish_cooking_time == ''){
        alert("Please enter finish cooking time to complete"); 
        return false;
    }
    
    if(temp_end_cooking == ''){
        alert("Please enter temperature at end of cooking to complete"); 
        return false;
    }
    
    if(time_chilling_start == ''){
        alert("Please enter chilling start time to complete"); 
        return false;
    }
    
    if(time_chilling_finished == ''){
        alert("Please enter chilling finish time to complete"); 
        return false;
    }
    
    if(temp_end_chilling == ''){
        alert("Please enter temperature at end of chilling to complete"); 
        return false;
    }
    
    if(recorded_by == ''){
        alert("Please enter your name in Recorded By field to complete"); 
        return false;
    }
    
   
    
  
    
    $(obj).html('⏰ Completed');
    $(obj).removeClass('btn-orange'); 
    $(obj).addClass('btn-success completSuccessBtn');
    $(obj).parents(".parentRow").find('input, select').prop('disabled', true);
    $(obj).parents(".parentRow").find('button:not(.minus-btn):not(.plus-btn)').prop('disabled', true);
    
    let data = [{
        productId: productId,
        currentFoodMinTempAllowed: foodMinTemp,
        currentFoodMaxTempAllowed: foodMaxTemp,
        entered_time: currentTime,
        site_id: siteId,
        prep_id: prepId,
        staff_comments: staff_comments,
        start_cooking_time: start_cooking_time,
        finish_cooking_time: finish_cooking_time,
        temp_end_cooking: temp_end_cooking,
        time_chilling_start: time_chilling_start,
        time_chilling_finished: time_chilling_finished,
        temp_end_chilling: temp_end_chilling,
        recorded_by: recorded_by,
       
        entered_by: recorded_by // Keep for backwards compatibility
    }];

    formData = JSON.stringify(data);
       
    $.ajax({
        type: 'POST',
        url: '/Temp/FryerTemp/Fryerhome/saveTempDashboardData',
        data: formData,
        success: function (response) {
            $(".tempSuccessRecorded").removeClass("d-none");
            setTimeout(function(){
                $(".tempSuccessRecorded").addClass("d-none");
            }, 3000);
        },
        error: function (xhr, status, error) {
            console.error(error);
            alert("Error saving data. Please try again.");
        }
    }); 
    
    console.log("currentTime", currentTime);
}

function showExceedTemp(){
    $(".exceededDiv").toggleClass("d-none");
    $(".tempDiv").toggleClass("col-lg-5");
    $(".exceededDiv").toggleClass("col-lg-7");
     
    let isMobileOrTablet = $(window).width() <= 768;
    if (isMobileOrTablet) {
        $("html, body").animate({ scrollTop: $(document).height() }, "slow");
    }
}

function saveExceedTemp(obj, id){
    let correctedTemp = $("#correctedTemp_"+id).val();
    let manager_comments = $("#manager_comments_"+id).val();
    let buttonHtml = $(obj).parents(".exceedSaveBtn");
    buttonHtml.html('...')
    
    $.ajax({
        type: 'POST',
        url: '/Temp/FryerTemp/Fryerhome/updateExceededTemp',
        data: 'id='+id+'&correctedTemp='+correctedTemp+'&manager_comments='+manager_comments,
        success: function (response) {
            $(".tempSuccessRecorded").removeClass("d-none");
            buttonHtml.html('<i class="ri-checkbox-circle-line"></i>');
            setTimeout(function(){
                $(".tempSuccessRecorded").addClass("d-none");
            }, 3000);
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    }); 
}

function fetchAttachment(id){
    $.ajax({
        type: "POST",
        url: "/Temp/home/fetchFoodAttachmentUploadedToday",
        data: 'id='+id,
        success: function (response) {
            let result = JSON.parse(response);
            console.log(result)
            result.forEach(function (filename) {
                let imageUrl = "/uploaded_files/TemperatureAttachments/" + filename;
                let slide = '<div class="swiper-slide">' +
                    '<img src="' + imageUrl + '" alt="" class="img-fluid" style="width: 100%;" />' +
                    '</div>';
                console.log("slide", slide)        
                $('.appendSwiperImages').append(slide);
            });
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    });
    $("#attachmentEquipModal").modal('show');
}

function getCurrentTime() {
    let now = new Date();
    let hours = String(now.getHours()).padStart(2, '0');
    let minutes = String(now.getMinutes()).padStart(2, '0');
    return hours + ':' + minutes;
}
</script>

<script>
$(document).ready(function () {
    $('#managerSignatureBtn').on('click', function () {
        $('#managerSignatureBox').slideToggle();
    });

    $('#saveManagerSignature').on('click', function () {
        const signature = $('#managerSignatureInput').val().trim();
        if (signature === '') {
            alert('Please enter a signature.');
        } else {
            $('#managerSignatureBox').slideUp();
        }

        $.ajax({
            url: '/Temp/FryerTemp/Fryerhome/save_signature',
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