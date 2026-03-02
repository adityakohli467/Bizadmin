<div class="container-fluid mb-5" style="margin-top: 130px !important;">
   <div class="row">
     <div class="col-12 tempDiv">
    <div class="d-flex flex-wrap gap-2 w-100 mb-3">

  <a class="btn btn-primary d-flex align-items-center gap-1" 
     href="<?php echo base_url('/Compliance/Cake/Cakehome'); ?>" 
     data-bs-toggle="tooltip" title="View Best Before Dashboard">
    <i class="fa-solid fa-cake-candles"></i> Best Before Dashboard
  </a>

  <a class="btn btn-danger d-flex align-items-center gap-1" 
     href="<?php echo base_url('/Compliance/Waste/Home'); ?>" 
     data-bs-toggle="tooltip" title="Manage Waste Records">
    <i class="fa-solid fa-trash-can"></i> Waste Management
  </a>

  <a class="btn btn-secondary d-flex align-items-center gap-1" 
     href="<?php echo base_url('/Compliance/Sanitation/Home'); ?>" 
     data-bs-toggle="tooltip" title="Sanitation Compliance">
    <i class="fa-solid fa-soap"></i> Sanitation
  </a>

  <a class="btn btn-success d-flex align-items-center gap-1" 
     href="<?php echo base_url('/Compliance/Goods/Home'); ?>" 
     data-bs-toggle="tooltip" title="Incoming Goods Checks">
    <i class="fa-solid fa-truck"></i> Incoming Goods
  </a>

  <a class="btn btn-warning d-flex align-items-center gap-1" 
     href="<?php echo base_url('/Compliance/KitchenProduction/Home'); ?>" 
     data-bs-toggle="tooltip" title="Kitchen Production">
    <i class="fa-solid fa-utensils"></i> Kitchen Production
  </a>

  <a class="btn btn-info d-flex align-items-center gap-1" 
     href="<?php echo base_url('/Compliance/ThermometerCalibration/Home'); ?>" 
     data-bs-toggle="tooltip" title="Thermometer Calibration">
    <i class="fa-solid fa-thermometer-half"></i> Thermometer Calibration
  </a>

</div>
    <div class="card">
     <div class="card-header align-items-center d-flex">
     <h4 class="card-title mb-0 flex-grow-1 text-faded"><i class="fa-solid fa-thermometer-half"></i> Thermometer Calibration Dashboard</h4>
                                    <div class="flex-shrink-0">
                                     <select class="form-select siteDropdown">
                                             <option> Select Site</option>
                                                <?php if (!empty($site_detail)) { $count = 0; foreach ($site_detail as $site) { $selected = ($count == 0 ? 'selected' : ''); ?>
                                                <option <?php echo $selected; ?> class="dropdown-item" href="#" value="<?php echo $site['id'] ?>"><?php echo $site['site_name'] ?></option>
                                                <?php $count++; } } ?>
                                       </select>
                                    </div>
                                    <a id="saveBtn" class="btn btn-success mx-4" onclick="handleSaveClick(this)"><i class='fas fa-save'></i> Save</a>

                                </div><!-- end card header -->
                                
                                <div class="d-none alert alert-success alert-dismissible alert-label-icon rounded-label shadow fade show tempSuccessRecorded" role="alert">
                                  <i class="ri-notification-off-line label-icon"></i><strong>Success</strong>
                                   Value recorded successfully.
                                   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive table-card">
                                        <table class="table table-borderless table-hover table-nowrap align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="text-muted">
                                                    <th scope="col">Equipment Name</th>
                                                    <th scope="col">Thermometer Serial Number</th>
                                                    <th scope="col">Check Date</th>
                                                </tr>
                                            </thead>
                                            <?php if (isset($site_detail) && !empty($site_detail)) { ?>
                                                <?php foreach ($site_detail as $site) { ?>
                                                    <?php foreach ($prep_detail as $prep_area) { ?>
                                                        <?php if ($prep_area['site_id'] == $site['id']) { ?>
                                                            <tbody class="prep_<?php echo $prep_area['id'] ?> tbodySite <?php echo 'siteId_' . $site['id'] ?>">
                                                                <th colspan="3" class="text-black w-100" style="background-color: #d1ecf1;">
                                                                    <b><i class="fa-solid fa-thermometer-half"></i> <?php echo $prep_area['prep_name']; ?> (Site: <?php echo $site['site_name']; ?>)</b>
                                                                </th>
                                                                <?php if (isset($products) && !empty($products)) { ?>
                                                                    <?php foreach ($products as $product) { ?>
                                                                        <?php if ($product['prep_id'] == $prep_area['id']) { ?>
                                                                            <tr>
                                                                                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                                                                <td>
                                                                                    <input type="text" class="form-control auto-save" 
                                                                                           data-product-id="<?= $product['id']; ?>" 
                                                                                           data-field="serial_number"
                                                                                           data-prep-id="<?= $prep_area['id']; ?>"
                                                                                           value="<?= isset($todaysEnteredData[$product['id']]) ? htmlspecialchars($todaysEnteredData[$product['id']]['serial_number']) : ''; ?>"
                                                                                           placeholder="e.g. 26335330151">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="date" class="form-control auto-save" 
                                                                                           data-product-id="<?= $product['id']; ?>" 
                                                                                           data-field="check_date"
                                                                                           data-prep-id="<?= $prep_area['id']; ?>"
                                                                                           value="<?= isset($todaysEnteredData[$product['id']]['check_date']) ? htmlspecialchars($todaysEnteredData[$product['id']]['check_date']) : ''; ?>">
                                                                                </td>
                                                                            </tr>
                                                                        <?php } ?>
                                                                    <?php } ?>
                                                                <?php } else { ?>
                                                                    <tr>
                                                                        <td colspan="3" class="text-center">No equipment found for this prep area.</td>
                                                                    </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="3" class="text-center">No sites or prep areas found. <a href="<?php echo base_url('Compliance/ThermometerCalibration/Site'); ?>">Create a Site</a> first.</td>
                                                    </tr>
                                                </tbody>
                                            <?php } ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        <script>

$(document).on('blur change', '.auto-save', function() {
    const productId = $(this).data('product-id');
    const field = $(this).data('field');
    const value = $(this).val();
    let prep = $(this).data('prep-id') || $(".siteDropdown").val();

    $.ajax({
        url: "<?= base_url('Compliance/ThermometerCalibration/Home/saveDashboardData') ?>",
        method: "POST",
        data: {
            product_id: productId,
            field: field,
            prep: prep,
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

$(".siteDropdown").on('change', function(){
    let siteId = $(this).val();
    localStorage.setItem('selectedSiteThermCalib', siteId);
    $(".tbodySite").each(function(index, element) {
        if (!$(element).hasClass("d-none")) {
            $(element).addClass("d-none");
        }
    });
    $(".siteId_" + siteId).removeClass("d-none");
});

$(document).ready(function(){
    let siteId = localStorage.getItem('selectedSiteThermCalib');
    if(siteId == '' || siteId == undefined || siteId == null){
        siteId = $(".siteDropdown option:eq(1)").val();
    }
    if(siteId) {
        $(".siteDropdown").val(siteId);
        $(".tbodySite").each(function(index, element) {
            if (!$(element).hasClass("d-none")) {
                $(element).addClass("d-none");
            }
        });
        $(".siteId_" + siteId).removeClass("d-none");
    }
});

function handleSaveClick(obj) {
    $(obj).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
    setTimeout(() => {
        $(obj).html('<i class="fas fa-save"></i> Save');
        $(".tempSuccessRecorded").removeClass('d-none');
        setTimeout(function() {
            $(".tempSuccessRecorded").addClass('d-none');
        }, 2000);
    }, 1000);
}

</script>
