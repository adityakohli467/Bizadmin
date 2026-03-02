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

                <a class="btn btn-info d-flex align-items-center gap-1 active" href="<?php echo base_url('/Temp/CalibrationTemp/Calibrationhome'); ?>" data-bs-toggle="tooltip" title="Thermometer Calibration Record">
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

                <a class="btn btn-primary d-flex align-items-center gap-1" data-bs-toggle="tooltip" href="<?php echo base_url('/Temp/calibrationTemp/listProduct'); ?>">
                    Add Equipment
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0 text-black">
                            <i class="fas fa-tools"></i> Thermometer Calibration Record
                            <span class="text-black ms-2">🗓 <?php echo date('d-m-Y'); ?></span>
                        </h4>
                    </div>
                    <button type="button" class="btn btn-success" onclick="handleSaveClick(this)">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-borderless table-hover table-nowrap align-middle mb-0 calibrationTempTable">
                            <thead class="table-light">
                                <tr class="text-muted">
                                    <th scope="col">Equipment</th>
                                    <th scope="col">Ice Point Calibration (°C)</th>
                                    <th scope="col">Boiling Point Calibration (°C)</th>
                                    <th scope="col">Corrective Action</th>
                                    <th scope="col">Calibrated By</th>
                                </tr>
                            </thead>
                            <?php
                            if (!empty($site_detail)) {      
                                foreach ($site_detail as $sites) { ?>
                                    <?php $prep_areas = json_decode($sites['prep_areas']); ?>
                                    <?php foreach ($prep_areas as $prep_area) { ?>
                                        <tbody class="prep_<?php echo $prep_area->id ?> tbodySite <?php echo 'siteId_'.$sites['id'] ?>"> 
                                            <tr>
                                                <th colspan="5" class="text-black w-100" style="background-color: #dff0fa;">
                                                    <b><?php echo $prep_area->prep_name; ?></b>
                                                </th>
                                            </tr>
                                            <?php 
                                            if (isset($products) && !empty($products)) {
                                                foreach ($products as $product) { 
                                                    $productId = $product['id'];
                                                    if ($product['prep_id'] == $prep_area->id) {
                                                        $matched = array_filter($todaysCalibrationData, function($item) use ($productId, $prep_area, $sites) {
                                                            return isset($item['product_id'], $item['prep_id'], $item['site_id']) &&
                                                                   $item['product_id'] == $productId &&
                                                                   $item['prep_id'] == $prep_area->id &&
                                                                   $item['site_id'] == $sites['id'];
                                                        });
                                                        $matchedData = !empty($matched) ? reset($matched) : null;
                                            ?>
                                            <tr class="parentRow">
                                                <td>
                                                    <input type="text" name="equipmentName" 
                                                           value="<?php echo $product['product_name']; ?>" 
                                                           readonly 
                                                           class="auto-save form-control equipmentName" 
                                                           data-prepid="<?php echo $prep_area->id ?>" 
                                                           data-product-id="<?php echo $productId; ?>">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.1" name="ice_point_temp" 
                                                           data-prepid="<?php echo $prep_area->id ?>" 
                                                           data-field="ice_point_temp"
                                                           class="auto-save form-control"
                                                           value="<?php echo htmlspecialchars($matchedData['ice_point_temp'] ?? ''); ?>"
                                                           data-product-id="<?php echo $productId; ?>"
                                                           placeholder="0.0">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.1" name="boiling_point_temp" 
                                                           data-prepid="<?php echo $prep_area->id ?>" 
                                                           data-field="boiling_point_temp"
                                                           class="auto-save form-control"
                                                           value="<?php echo htmlspecialchars($matchedData['boiling_point_temp'] ?? ''); ?>"
                                                           data-product-id="<?php echo $productId; ?>"
                                                           placeholder="0.0">
                                                </td>
                                                <td>
                                                    <textarea name="corrective_action" class="auto-save form-control"
                                                              data-prepid="<?php echo $prep_area->id ?>" 
                                                              data-field="corrective_action"
                                                              data-product-id="<?php echo $productId; ?>" 
                                                              rows="1"
                                                              placeholder="e.g. service, batteries changed"><?php echo htmlspecialchars($matchedData['corrective_action'] ?? ''); ?></textarea>
                                                </td>
                                                <td>
                                                    <input type="text" name="calibrated_by" 
                                                           data-prepid="<?php echo $prep_area->id ?>" 
                                                           data-field="calibrated_by"
                                                           class="auto-save form-control"
                                                           value="<?php echo htmlspecialchars($matchedData['calibrated_by'] ?? ''); ?>"
                                                           data-product-id="<?php echo $productId; ?>">
                                                </td>
                                            </tr>
                                            <?php } // if prep_id matches ?>
                                            <?php } // foreach products ?>
                                            <?php } // if products ?>
                                        </tbody>
                                    <?php } // foreach prep_areas ?>
                                <?php } // foreach site_detail ?>
                            <?php } // if site_detail ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    let selectedSite = localStorage.getItem('selectedSiteCalibrationTempDashBoard');
    if (selectedSite == '' || selectedSite == undefined) {
        selectedSite = $(".siteDropdown").val();   
    }
    $(".siteDropdown").val(selectedSite);
   
    $(".tbodySite").addClass('d-none');
    $(".siteId_" + selectedSite).removeClass('d-none');
    $(".siteDropdown").on('change', function() {
        let selectedSite = $(this).val();  
        localStorage.setItem('selectedSiteCalibrationTempDashBoard', selectedSite);
        $(".tbodySite").addClass('d-none');
        $(".siteId_" + selectedSite).removeClass('d-none');
    });
});

$('#managerSignatureBtn').on('click', function () {
    $('#managerSignatureBox').slideToggle();
});

$('#saveManagerSignature').on('click', function () {
    const signature = $('#managerSignatureInput').val().trim();
    if (signature === '') {
        alert('Please enter a signature.');
        return;
    }
    $.ajax({
        url: "<?= base_url('Temp/CalibrationTemp/Calibrationhome/save_signature') ?>",
        type: 'POST',
        data: { signature: signature },
        success: function (response) {
            let res = JSON.parse(response);
            if (res.status === 'success') {
                alert('Signature saved successfully!');
                $('#managerSignatureBox').slideUp();
                $('#managerSignatureInput').val('');
            } else {
                alert('Error: ' + res.message);
            }
        },
        error: function (xhr) {
            alert('AJAX Error: ' + xhr.statusText);
        }
    });
});

$(document).on('blur', '.auto-save', function() {
    const productId = $(this).data('product-id');
    const field = $(this).data('field');
    const value = $(this).val();
    let siteId = $(".siteDropdown").val();
    const prepId = $(this).data('prepid');

    if (!field) return; // skip readonly equipment name field

    $.ajax({
        url: "<?= base_url('Temp/CalibrationTemp/Calibrationhome/saveRecord') ?>",
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
    $(".tempSuccessRecorded").removeClass("d-none");
    setTimeout(() => {
        $button.html('<i class="fas fa-save"></i> Save').prop('disabled', false);
    }, 1000);
}
</script>
