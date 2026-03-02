<style>
.table td {
    width: 10%; 
}
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
                    <div class="flex-shrink-0"></div>
                    <h4 class="card-title mb-0 flex-grow-1 text-faded">
                        Thermometer Calibration History
                    </h4>
                    <a href="/Temp/home/calibrationTempHistory" class="btn bg-orange waves-effect btn-label waves-light">
                        <i class="ri-reply-fill label-icon align-middle fs-16 me-2"></i><span>Back</span>
                    </a>
                </div>
                <div class="card-body">
                    <?php if (isset($weeklyTempData) && !empty($weeklyTempData)) { ?>
                    <div class="table-responsive table-card">
                        <?php $dateCount = 0; foreach ($uniqueDates as $dateToFind) { ?>
                            <?php 
                            $isDateExist = array_filter($weeklyTempData, function ($dayData) use ($dateToFind) {
                                return $dayData['date_entered'] === $dateToFind;
                            });
                            ?>
                            <?php if (!empty($isDateExist)) { ?>
                                <h4 class="card-title mb-0 flex-grow-1 text-faded mt-4 mb-4 px-4 text-center">
                                    <?php echo date('d-m-Y', strtotime($dateToFind)); ?>
                                </h4>
                                <table class="table table-borderless table-hover table-nowrap align-middle mb-0 table-bordered">
                                    <?php if ($dateCount === 0) { $dateCount++; ?>
                                        <thead class="table-light fixed-table-header">
                                            <tr class="text-muted">
                                                <th scope="col">Date</th>
                                                <th scope="col">Equipment</th>
                                                <th scope="col">Ice Point Calibration (°C)</th>
                                                <th scope="col">Boiling Point Calibration (°C)</th>
                                                <th scope="col">Corrective Action</th>
                                                <th scope="col">Calibrated By</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                    <?php } ?>
                                    <tbody>
                                        <?php if (isset($site_detail) && !empty($site_detail)) { 
                                            foreach ($site_detail as $AllSites) { 
                                                $prep_areas = json_decode($AllSites['prep_areas']);
                                                foreach ($prep_areas as $prep_area) { ?>
                                                    <tr>
                                                        <th colspan="7" class="text-black w-100" style="background-color: #07070b2e;">
                                                            <b><?php echo $prep_area->prep_name; ?></b>
                                                        </th>
                                                    </tr>
                                                    <?php 
                                                    foreach ($isDateExist as $calibData) { 
                                                        if ($calibData['prep_id'] == $prep_area->id && $calibData['site_id'] == $AllSites['id']) { ?>
                                                            <tr class="rowData">
                                                                <td>
                                                                    <input type="text" readonly 
                                                                           value="<?php echo date('d-m-Y', strtotime($calibData['date_entered'])); ?>" 
                                                                           class="form-control">
                                                                </td>
                                                                <td>
                                                                    <input type="text" readonly 
                                                                           name="equipment_name[]" 
                                                                           value="<?php echo htmlspecialchars($calibData['product_name'] ?? ''); ?>" 
                                                                           data-field="equipment_name" 
                                                                           data-row-id="<?php echo $calibData['id']; ?>" 
                                                                           data-product-id="<?php echo $calibData['product_id']; ?>"
                                                                           class="form-control">
                                                                </td>
                                                                <td>
                                                                    <input type="number" step="0.1" 
                                                                           name="ice_point_temp[]" 
                                                                           value="<?php echo $calibData['ice_point_temp'] ?? ''; ?>" 
                                                                           data-field="ice_point_temp" 
                                                                           data-product-id="<?php echo $calibData['product_id']; ?>"
                                                                           data-row-id="<?php echo $calibData['id']; ?>" 
                                                                           class="form-control auto-save">
                                                                </td>
                                                                <td>
                                                                    <input type="number" step="0.1" 
                                                                           name="boiling_point_temp[]" 
                                                                           value="<?php echo $calibData['boiling_point_temp'] ?? ''; ?>" 
                                                                           data-field="boiling_point_temp" 
                                                                           data-product-id="<?php echo $calibData['product_id']; ?>"
                                                                           data-row-id="<?php echo $calibData['id']; ?>" 
                                                                           class="form-control auto-save">
                                                                </td>
                                                                <td>
                                                                    <textarea name="corrective_action[]" 
                                                                              data-field="corrective_action" 
                                                                              data-product-id="<?php echo $calibData['product_id']; ?>" 
                                                                              data-row-id="<?php echo $calibData['id']; ?>" 
                                                                              class="form-control auto-save" rows="1"><?php echo $calibData['corrective_action'] ?? ''; ?></textarea>
                                                                </td>
                                                                <td>
                                                                    <input type="text" 
                                                                           name="calibrated_by[]" 
                                                                           value="<?php echo $calibData['calibrated_by'] ?? ''; ?>" 
                                                                           data-field="calibrated_by" 
                                                                           data-row-id="<?php echo $calibData['id']; ?>" 
                                                                           data-product-id="<?php echo $calibData['product_id']; ?>"
                                                                           class="form-control auto-save">
                                                                </td>
                                                                <td>
                                                                    <button class="btn btn-sm btn-success" onclick="handleSaveClick(this)">Update</button>
                                                                </td>
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
                    </div>
                    <?php } else { ?>
                        <h3 class="text-black">No result found for this date range/site</h3>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).on('blur', '.auto-save', function() {
    const productId = $(this).data('product-id');
    const field = $(this).data('field');
    const value = $(this).val();
    let rowId = $(this).data('row-id');

    $.ajax({
        url: "<?= base_url('Temp/CalibrationTemp/Calibrationhome/updateRecord') ?>",
        method: "POST",
        data: {
            product_id: productId,
            field: field,
            rowId: rowId,
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
    $button.html('<i class="fas fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
    setTimeout(() => {
        $button.html('<i class="fas fa-save"></i> Update').prop('disabled', false);
    }, 1000);
}
</script>
