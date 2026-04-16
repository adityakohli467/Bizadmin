<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid" style="margin-top: 130px !important;">
    <div class="row">
        <div class="col-lg-12">
            <div class="card py-3" id="orderList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="card-title mb-0 flex-grow-1 text-dark">Waste Management Products</h5>
                        <button type="button" class="btn btn-primary btn-sm fs-14" data-bs-toggle="modal" data-bs-target="#productModal" onclick="openProductModal()">
                            <i class="bi bi-plus-lg me-1"></i> Add Product
                        </button>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="d-none text-center my-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <!-- Success/Error Alert -->
                    <div id="alertMessage" class="d-none alert alert-dismissible fade show" role="alert">
                        <span id="alertText"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <div class="table-responsive table-card mb-1">
                        <table class="table table-nowrap align-middle" id="activeRow">
                            <thead class="table-light">
                                <tr class="text-uppercase text-muted">
                                    <th scope="col" style="width: 25px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                        </div>
                                    </th>
                                    <th class="sort" data-sort="product_name">Product Name</th>
                                    <th class="sort" data-sort="par_level">PAR Level</th>
                                    <th class="sort" data-sort="site_name">Site</th>
                                    <th class="sort" data-sort="prep_name">Prep Area</th>
                                    <th class="no-sort">Action</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all" id="sortable">
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $product): ?>
                                        <?php
                                        // Find matching prep area
                                        $selectedPrep = array_reduce($prep_detail, function ($carry, $prep) use ($product) {
                                            return $prep['id'] == $product['prep_id'] ? $prep : $carry;
                                        }, []);
                                        $siteId = isset($selectedPrep['site_id']) ? $selectedPrep['site_id'] : null;
                                        // Find matching site
                                        $selectedSite = array_reduce($site_detail, function ($carry, $site) use ($siteId) {
                                            return $site['id'] == $siteId ? $site : $carry;
                                        }, []);
                                        ?>
                                        <tr id="row_<?php echo htmlspecialchars($product['id']); ?>">
                                            <th scope="row">
                                                <div class="form-check">
                                                    <input class="form-check-input checkAll checkbox-item" type="checkbox" name="checkAll" value="<?php echo htmlspecialchars($product['id']); ?>">
                                                </div>
                                            </th>
                                            <td class="descr text-wrap handle"><?php echo htmlspecialchars($product['product_name']); ?></td>
                                            <td class="descr text-wrap handle"><?php echo htmlspecialchars($product['par_level']); ?></td>
                                            <td class="descr text-wrap handle"><?php echo isset($selectedSite['site_name']) ? htmlspecialchars($selectedSite['site_name']) : ''; ?></td>
                                            <td class="descr text-wrap handle"><?php echo isset($selectedPrep['prep_name']) ? htmlspecialchars($selectedPrep['prep_name']) : ''; ?></td>
                                          
                                            <td>
                                                                
                                                         <ul class="list-inline hstack gap-2 mb-0">
                                                               
                                                                <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                                                    data-bs-placement="top" title="Edit">
                                                                    <a onclick="editProduct(<?php echo htmlspecialchars($product['id']); ?>)"  class="text-primary d-inline-block edit-item-btn">
                                                                        <i class="ri-pencil-fill fs-16"></i>
                                                                    </a>
                                                                </li>
                                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                                                    data-bs-placement="top" title="Remove">
                                                                    <a class="text-danger d-inline-block remove-item-btn" data-bs-toggle="modal"
                                                                        data-rel-id="<?php echo  $product['id']; ?>"  onclick="deleteThisRow(this, <?php echo htmlspecialchars($product['id']); ?>)">
                                                                        <i class="ri-delete-bin-5-fill fs-16"></i>
                                                                    </a>
                                                                </li>
                                                            </ul>  
                                                            
                                                            </td>
                                          
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No products found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteProduct" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body p-5 text-center">
                                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 48px;"></i>
                                    <div class="mt-4 text-center">
                                        <h4 class="text-dark">Are you sure you want to delete this product?</h4>
                                        <p class="fs-15 mb-4 text-muted">Deleting this product will remove all its information from the database.</p>
                                        <div class="hstack gap-2 justify-content-center">
                                            <button class="btn btn-link link-success fw-medium" data-bs-dismiss="modal">
                                                <i class="bi bi-x-lg me-1 align-middle"></i> Close
                                            </button>
                                            <button class="btn btn-danger" id="delete-record">Yes, Delete It</button>
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

<!-- Add/Edit Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="productForm" action="<?php echo base_url('Compliance/Waste/home/addOrUpdateProduct'); ?>" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Add/Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="product_id">
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Product Name</label>
                        <input type="text" name="product_name" class="form-control" id="product_name" required>
                        <div class="invalid-feedback">Please enter a product name.</div>
                    </div>
                    <div class="mb-3">
                        <label for="par_level" class="form-label">PAR Level</label>
                        <input type="number" name="par_level" class="form-control" id="par_level" min="0" required>
                        <div class="invalid-feedback">Please enter a valid PAR level.</div>
                    </div>
                    <div class="mb-3">
                        <label for="prep_id" class="form-label">Prep Area</label>
                        <select name="prep_id" class="form-control" id="prep_id" required>
                            <option value="">Select Area</option>
                            <?php foreach ($prep_detail as $prep): ?>
                                <option value="<?php echo htmlspecialchars($prep['id']); ?>"><?php echo htmlspecialchars($prep['prep_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Please select a prep area.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="addEditProduct">Submit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#activeRow').DataTable({
        lengthChange: false,
        pageLength: 100,
        columnDefs: [{
            targets: 'no-sort',
            orderable: false
        }],
        language: {
            emptyTable: "No products found."
        }
    });

    // Initialize Bootstrap tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Check/Uncheck all checkboxes
    $('#checkAll').on('change', function() {
        $('.checkbox-item').prop('checked', this.checked);
    });

    // Make table rows sortable
    $("#sortable").sortable({
        handle: '.handle',
        update: function(event, ui) {
            showLoading();
            let sortOrder = $(this).sortable('toArray', { attribute: 'id' });
            $.ajax({
                url: "<?php echo base_url('Compliance/Waste/home/updateSortOrder'); ?>",
                type: "POST",
                data: { order: sortOrder },
                success: function(response) {
                    hideLoading();
                    showAlert('success', 'Sort order updated successfully.');
                },
                error: function(xhr) {
                    hideLoading();
                    showAlert('danger', 'Error updating sort order: ' + (xhr.responseJSON?.message || 'Unknown error.'));
                }
            });
        }
    });

    // Open Add Product Modal
    window.openProductModal = function() {
        $('#productForm')[0].reset();
        $('#productForm').removeClass('was-validated');
        $('#product_id').val('');
        $('#productModalLabel').text('Add Product');
        $('#productModal').modal('show');
    };

    // Edit Product
    window.editProduct = function(id) {
        showLoading();
        $.ajax({
            url: "<?php echo base_url('Compliance/Waste/home/getProductById/'); ?>" + id,
            type: "GET",
            success: function(res) {
                hideLoading();
                try {
                    const data = JSON.parse(res)[0];
                    $('#product_id').val(data.id);
                    $('#product_name').val(data.product_name);
                    $('#par_level').val(data.par_level);
                    $('#prep_id').val(data.prep_id);
                    $('#productForm').removeClass('was-validated');
                    $('#productModalLabel').text('Edit Product');
                    $('#productModal').modal('show');
                } catch (e) {
                    showAlert('danger', 'Error parsing product data.');
                }
            },
            error: function(xhr) {
                hideLoading();
                showAlert('danger', 'Error fetching product: ' + (xhr.responseJSON?.message || 'Unknown error.'));
            }
        });
    };

    // Delete Product
    window.deleteThisRow = function(element, id) {
        $("#deleteProduct").modal('show');
        $("#delete-record").val(id);
    };

    $('#delete-record').on('click', function() {
        const id = $(this).val();
        showLoading();
        $.ajax({
            url: "<?php echo base_url('Compliance/Waste/Home/delete'); ?>",
            type: "POST",
            data: { id: id,table_name:'Compliance_wasteManagementproducts' },
            success: function(response) {
                hideLoading();
                if (response === 'success') {
                    $('#row_' + id).remove();
                    $("#deleteProduct").modal('hide');
                    showAlert('success', 'Product deleted successfully.');
                    if (!table.data().any()) {
                        table.draw();
                    }
                } else {
                    showAlert('danger', 'Error deleting product.');
                }
            },
            error: function(xhr) {
                hideLoading();
                showAlert('danger', 'Error deleting product: ' + (xhr.responseJSON?.message || 'Unknown error.'));
            }
        });
    });

    // Product Form Submission
   

    // Show Loading Spinner
    function showLoading() {
        $('#loadingSpinner').removeClass('d-none');
    }

    // Hide Loading Spinner
    function hideLoading() {
        $('#loadingSpinner').addClass('d-none');
    }

    // Show Alert Message
    function showAlert(type, message) {
        $('#alertMessage').removeClass('alert-success alert-danger d-none')
            .addClass(`alert-${type}`)
            .find('#alertText').text(message);
        setTimeout(() => $('#alertMessage').addClass('d-none'), 5000);
    }
});
</script>

<style>
.handle {
    cursor: move;
}
.table-responsive {
    min-height: 200px;
}
.was-validated .form-control:invalid,
.form-control.is-invalid {
    background-image: none;
}
</style>