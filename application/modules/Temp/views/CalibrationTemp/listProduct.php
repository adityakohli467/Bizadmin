<div class="container-fluid" style="margin-top: 130px !important;">
    
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
                <a class="btn btn-primary d-flex align-items-center gap-1" data-bs-toggle="tooltip" href="<?php echo base_url('/Temp/calibrationTemp/listProduct'); ?>">
                    Add Equipment
                </a>
            </div>
        </div>
    </nav>

    <div class="row">
        <div class="col-lg-12">
            <div class="card py-3" id="orderList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="card-title mb-0 flex-grow-1 text-black">Calibration Equipment</h5>
                        <a type="button" class="btn btn-primary btn-sm fs-14" data-bs-toggle="modal" data-bs-target="#productModal" onclick="openProductModal()">
                            <i class="ri-add-line align-bottom"></i> Add Equipment
                        </a>                         
                    </div>
                </div>
              
                <div class="card-body pt-0">
                    <div class="table-responsive table-card mb-1">
                        <table class="table table-nowrap align-middle" id="activeEquip">
                            <thead class="text-muted table-light">
                                <tr class="text-uppercase">
                                    <th scope="col" style="width: 25px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                        </div>
                                    </th>
                                    <th class="sort" data-sort="customer_name">Equipment Name</th>
                                    <th class="sort" data-sort="customer_name">Site</th>
                                    <th class="sort" data-sort="customer_name">Prep Area</th>
                                    <th class="sort" data-sort="city">Action</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all" id="sortable">
                                <?php if (!empty($products)) { ?>
                                    <?php foreach ($products as $product) { 
                                        $selectedPrep = array_filter($prep_detail, function ($prep) use ($product) {
                                            return $prep['id'] == $product['prep_id'];
                                        });
                                        $selectedPrep = reset($selectedPrep) ?: [];
                                        $siteId = isset($selectedPrep['site_id']) ? $selectedPrep['site_id'] : null;
                                        $selectedSite = array_filter($site_detail, function ($site) use ($siteId) {
                                            return $site['id'] == $siteId;
                                        });
                                        $selectedSite = reset($selectedSite) ?: [];
                                    ?>
                                    <tr id="<?php echo 'row_' . $product['id']; ?>">
                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input checkAll checkbox-item" type="checkbox" name="checkAll" value="<?php echo $product['id']; ?>">
                                            </div>
                                        </th>
                                        <td class="descr text-wrap handle"><?php echo isset($product['product_name']) ? htmlspecialchars($product['product_name']) : ''; ?></td>
                                        <td class="descr text-wrap handle"><?php echo isset($selectedSite['site_name']) ? htmlspecialchars($selectedSite['site_name']) : ''; ?></td>
                                        <td class="descr text-wrap handle"><?php echo isset($selectedPrep['prep_name']) ? htmlspecialchars($selectedPrep['prep_name']) : ''; ?></td>
                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                                                    <a href="#" class="text-primary d-inline-block edit-item-btn" onclick="editProduct(<?php echo $product['id']; ?>)">
                                                        <i class="ri-pencil-fill fs-16"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Remove">
                                                    <a class="text-danger d-inline-block remove-item-btn" data-bs-toggle="modal" onclick="deleteThisRow(this, <?php echo $product['id']; ?>)">
                                                        <i class="ri-delete-bin-5-fill fs-16"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No equipment found.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade flip" id="deleteProduct" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body p-5 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json"
                                        trigger="loop" colors="primary:#405189,secondary:#f06548"
                                        style="width:90px;height:90px"></lord-icon>
                                    <div class="mt-4 text-center">
                                        <h4 class="text-black">You are about to delete this equipment?</h4>
                                        <p class="fs-15 mb-4 text-black">Deleting equipment will remove all of the information from our database.</p>
                                        <div class="hstack gap-2 justify-content-center remove">
                                            <button class="btn btn-link link-success fw-medium text-decoration-none" data-bs-dismiss="modal">
                                                <i class="ri-close-line me-1 align-middle"></i> Close
                                            </button>
                                            <button class="btn btn-danger" value="" id="delete-record">Yes, Delete It</button>
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
</div>

<!-- Add/Edit Equipment Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="productForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="product_id">
                    <div class="mb-3">
                        <label>Equipment Name</label>
                        <input type="text" name="product_name" class="form-control" id="product_name" required>
                    </div>
                    <div class="mb-3">
                        <label>Area</label>
                        <select name="prep_id" class="form-control" id="prep_id" required>
                            <option value="">Select Area</option>
                            <?php foreach($prep_detail as $prep): ?>
                                <option value="<?= $prep['id'] ?>"><?= $prep['prep_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="submitButton">
                        <span class="spinner-border spinner-border-sm d-none" id="button-loader" role="status" aria-hidden="true"></span>
                        <span class="visually-hidden">Loading...</span>
                        Submit
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$('#activeEquip').DataTable({
    lengthChange: false,
    pageLength: 100,
    "columnDefs": [{
        "targets": 'no-sort',
        "orderable": false
    }]
});

function deleteThisRow(obj, rowId) {
    $("#deleteProduct").modal('show');
    $("#delete-record").val(rowId);
}

$(document).on("click", "#delete-record", function () {
    let id = $(this).val();
    $.ajax({
        type: "POST",
        url: "<?= base_url('Temp/CalibrationTemp/Calibrationhome/deleteProduct') ?>",
        data: { id: id },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                $('#row_' + id).remove();
                $("#deleteProduct").modal('hide');
            } else {
                alert(response.message || 'Failed to delete record');
            }
        },
        error: function () {
            alert('Something went wrong. Please try again.');
        }
    });
});

function openProductModal() {
    $('#productForm')[0].reset();
    $('#product_id').val('');
}

function editProduct(id) {
    $.get("<?= base_url('/Temp/CalibrationTemp/Calibrationhome/getProductById/') ?>" + id, function(res) {
        const data = JSON.parse(res)[0];
        $('#product_id').val(data.id);
        $('#product_name').val(data.product_name);
        $('#prep_id').val(data.prep_id);
        $('#productModal').modal('show');
    });
}
</script>
<script>
$('#productForm').on('submit', function(e) {
    e.preventDefault();

    const $button = $('#submitButton');
    $button.prop('disabled', true);
    $('#button-loader').removeClass('d-none');
    $button.find('span.visually-hidden').text('Loading...');
    $button.find('span:contains("Submit")').addClass('d-none');

    $.ajax({
        url: "<?= base_url('/Temp/CalibrationTemp/Calibrationhome/addOrUpdateProduct') ?>",
        type: "POST",
        data: $(this).serialize(),
        success: function(response) {
            let res = JSON.parse(response);
            if(res.status == 'success') {
                location.reload();
            }
        },
        complete: function () {
            $button.prop('disabled', false);
            $('#button-loader').addClass('d-none');
            $button.find('span.visually-hidden').text('');
            $button.find('span:contains("Submit")').removeClass('d-none');
        }
    });
});
</script>
