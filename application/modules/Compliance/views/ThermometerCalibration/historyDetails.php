<div class="container-fluid mb-5" style="margin-top: 130px !important;">
    <div class="row">
        <div class="col-12 tempDiv">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1 text-faded"><i class="fa-solid fa-thermometer-half"></i> Thermometer Calibration History Details</h4>
                    <a href="<?php echo base_url('Compliance/ThermometerCalibration/Home/history'); ?>" class="btn btn-primary btn-sm">
                        <i class="ri-arrow-go-back-line"></i> Back
                    </a>
                    <a class="btn btn-primary btn-sm" onclick="location.reload();">
    <i class="ri-save-line"></i> Save
</a>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <strong>Date Range:</strong> <?php echo htmlspecialchars($dateRange); ?>
                    </div>
                    
                    <div class="table-responsive table-card">
                        <table class="table table-bordered table-hover table-nowrap align-middle mb-0" id="historyTable">
                            <thead class="table-light">
                                <tr class="text-muted">
                                    <th scope="col" class="sticky-col">Equipment Name</th>
                                    <?php foreach ($uniqueDates as $date) { ?>
                                        <th scope="col" class="text-center" colspan="2"><?php echo date('D, d M', strtotime($date)); ?></th>
                                    <?php } ?>
                                </tr>
                                <tr class="text-muted">
                                    <th></th>
                                    <?php foreach ($uniqueDates as $date) { ?>
                                        <th class="text-center">Serial No</th>
                                        <th class="text-center">Check Date</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($site_detail) && !empty($site_detail)) { ?>
                                    <?php foreach ($site_detail as $site) { ?>
                                        <?php if (!empty($prep_detail)) { ?>
                                            <?php foreach ($prep_detail as $prep_area) { ?>
                                                <?php if ($prep_area['site_id'] == $site['id']) { ?>
                                                    <tr style="background-color: #d1ecf1;">
                                                        <td colspan="<?php echo 1 + (count($uniqueDates) * 2); ?>">
                                                            <strong><i class="fa-solid fa-thermometer-half"></i> <?php echo htmlspecialchars($prep_area['prep_name']); ?> (Site: <?php echo htmlspecialchars($site['site_name']); ?>)</strong>
                                                        </td>
                                                    </tr>
                                                    <?php if (isset($products) && !empty($products)) { ?>
                                                        <?php foreach ($products as $product) { ?>
                                                            <?php if ($product['prep_id'] == $prep_area['id']) { ?>
                                                                <tr>
                                                                    <td class="sticky-col"><?php echo htmlspecialchars($product['product_name']); ?></td>
                                                                    <?php foreach ($uniqueDates as $date) { ?>
                                                                        <?php
                                                                        $serial_number = isset($weeklyData[$date][$product['id']]['serial_number']) ? $weeklyData[$date][$product['id']]['serial_number'] : '';
                                                                        $check_date = isset($weeklyData[$date][$product['id']]['check_date']) ? $weeklyData[$date][$product['id']]['check_date'] : '';
                                                                        ?>
                                                                        <td>
                                                                            <input type="text" class="form-control form-control-sm history-input"
                                                                                   data-product-id="<?php echo $product['id']; ?>"
                                                                                   data-date="<?php echo $date; ?>"
                                                                                   data-prep-id="<?php echo $prep_area['id']; ?>"
                                                                                   data-field="serial_number"
                                                                                   value="<?php echo htmlspecialchars($serial_number); ?>"
                                                                                   placeholder="Serial No"
                                                                                   style="width: 120px;">
                                                                        </td>
                                                                        <td>
                                                                            <input type="date" class="form-control form-control-sm history-input"
                                                                                   data-product-id="<?php echo $product['id']; ?>"
                                                                                   data-date="<?php echo $date; ?>"
                                                                                   data-prep-id="<?php echo $prep_area['id']; ?>"
                                                                                   data-field="check_date"
                                                                                   value="<?php echo htmlspecialchars($check_date); ?>"
                                                                                   style="width: 140px;">
                                                                        </td>
                                                                    <?php } ?>
                                                                </tr>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="<?php echo 1 + (count($uniqueDates) * 2); ?>" class="text-center">No data found for the selected date range.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.sticky-col {
    position: sticky;
    left: 0;
    background: white;
    z-index: 1;
}
thead .sticky-col {
    z-index: 2;
}
.history-input {
    min-width: 60px;
}
</style>

<script>
$(document).on('blur change', '.history-input', function() {
    const productId = $(this).data('product-id');
    const dateEntered = $(this).data('date');
    const prepId = $(this).data('prep-id');
    const field = $(this).data('field');
    const value = $(this).val();
    const locationId = <?php echo json_encode($this->session->userdata('location_id')); ?>;

    let postData = {
        product_id: productId,
        date_entered: dateEntered,
        prep_id: prepId,
        location_id: locationId
    };
    postData[field] = value;

    $.ajax({
        url: "<?= base_url('Compliance/ThermometerCalibration/Home/updateHistory') ?>",
        method: "POST",
        data: postData,
        success: function(response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                console.log("Saved:", res.message);
            } else {
                console.error("Error:", res.message);
            }
        },
        error: function(err) {
            console.error("Error saving data", err);
        }
    });
});
</script>
