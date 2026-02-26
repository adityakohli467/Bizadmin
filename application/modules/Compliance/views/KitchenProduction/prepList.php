<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
        
            <div class="col-12">
                <div class="alert alert-success fade show" role="alert" style="display:none">
                    Area Added Successfully
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 text-black"><i class="fa-solid fa-utensils"></i> Kitchen Production Prep Areas</h4>

                                <div class="page-title-right">
                                    <div class="d-flex justify-content-sm-end gap-2">
                                        <a href="<?php echo base_url('Compliance/KitchenProduction/Home') ?>" class="btn btn-secondary">
                                            <i class="ri-arrow-go-back-line"></i> Back to Dashboard
                                        </a>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#flipModal">
                                            <i class="ri-add-line fs-14 align-bottom me-1"></i>Add Area
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div id="siteList">
                                <div class="table-responsive table-card mb-1">
                                    <table class="table align-middle table-nowrap" id="prepListDatatable">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="sort" data-sort="prep_name">Area Name</th>
                                                <th class="sort" data-sort="site_name">Site Name</th>
                                                <th class="sort" data-sort="status">Status</th>
                                                <th class="no-sort">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all" id="sortable">
                                            <?php if(!empty($prep_detail)) { ?>
                                                <?php foreach($prep_detail as $prep) { ?>
                                                    <tr id="row_<?php echo $prep['id']; ?>">
                                                        <td class="customer_name"><?php echo htmlspecialchars($prep['prep_name']); ?></td>
                                                        <td class="customer_name"><?php echo htmlspecialchars($prep['site_name']); ?></td>
                                                        <td>
                                                            <div class="form-check form-switch form-switch-custom form-switch-success">
                                                                <input class="form-check-input toggle-demo" type="checkbox" role="switch" 
                                                                       id="<?php echo $prep['id']; ?>" 
                                                                       <?php if(isset($prep['status']) && $prep['status'] == '1'){ echo 'checked'; }?>>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <ul class="list-inline hstack gap-2 mb-0">
                                                                <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                                                    data-bs-placement="top" title="Edit">
                                                                    <a onclick="showEditModal('<?php echo htmlspecialchars($prep['prep_name']); ?>', <?php echo $prep['site_id']; ?>, <?php echo $prep['id']; ?>)" 
                                                                       class="text-primary d-inline-block edit-item-btn">
                                                                        <i class="ri-pencil-fill fs-16"></i>
                                                                    </a>
                                                                </li>
                                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                                                    data-bs-placement="top" title="Remove">
                                                                    <a class="text-danger d-inline-block remove-item-btn" data-bs-toggle="modal"
                                                                       data-rel-id="<?php echo $prep['id']; ?>">
                                                                        <i class="ri-delete-bin-5-fill fs-16"></i>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">No prep areas found. Add your first prep area.</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <div class="noresult" style="display: none">
                                        <div class="text-center">
                                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                                colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px">
                                            </lord-icon>
                                            <h5 class="mt-2">Sorry! No Result Found</h5>
                                            <p class="text-muted mb-0">We did not find any record for your search.</p>
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

<!-- Add Area Modal -->
<div id="flipModal" class="modal fade flip" tabindex="-1" aria-labelledby="flipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Add Prep Area</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo base_url('Compliance/KitchenProduction/Prep/add'); ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="prep_name" class="form-label fw-semibold">Area Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="prep_name" id="prep_name" placeholder="Enter Area Name" required>
                    </div>
                    <div class="mb-3">
                        <label for="site_id" class="form-label fw-semibold">Site <span class="text-danger">*</span></label>
                        <select class="form-select" name="site_id" id="site_id" required>
                            <option value="">Select Site</option>
                            <?php if(!empty($site_detail)) { ?>
                                <?php foreach($site_detail as $site) { ?>
                                    <option value="<?php echo $site['id']; ?>"><?php echo htmlspecialchars($site['site_name']); ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Add Area</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Area Modal -->
<div id="flipEditModal" class="modal fade flip" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-warning p-3">
                <h5 class="modal-title">Edit Prep Area</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo base_url('Compliance/KitchenProduction/Prep/edit'); ?>" method="POST">
                <input type="hidden" name="id" id="edit_prep_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_prep_name" class="form-label fw-semibold">Area Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="prep_name" id="edit_prep_name" placeholder="Enter Area Name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_site_id" class="form-label fw-semibold">Site <span class="text-danger">*</span></label>
                        <select class="form-select" name="site_id" id="edit_site_id" required>
                            <option value="">Select Site</option>
                            <?php if(!empty($site_detail)) { ?>
                                <?php foreach($site_detail as $site) { ?>
                                    <option value="<?php echo $site['id']; ?>"><?php echo htmlspecialchars($site['site_name']); ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">Update Area</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#prepListDatatable').DataTable({
        lengthChange: false,
        pageLength: 25,
        "columnDefs": [{
            "targets": 'no-sort',
            "orderable": false
        }]
    });

    // Make sortable
    $("#sortable").sortable({
        update: function(event, ui) {
            let sortOrder = $(this).sortable("toArray", { attribute: "id" });
            $.ajax({
                url: "<?php echo base_url('Compliance/KitchenProduction/Prep/updateSortOrder'); ?>",
                type: "POST",
                data: { order: sortOrder },
                success: function(response) {
                    console.log("Order updated successfully");
                }
            });
        }
    });
});

function showEditModal(prepName, siteId, prepId) {
    $('#edit_prep_name').val(prepName);
    $('#edit_site_id').val(siteId);
    $('#edit_prep_id').val(prepId);
    $('#flipEditModal').modal('show');
}

$(document).on("click", ".remove-item-btn", function() {
    var id = $(this).attr('data-rel-id');
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('Compliance/KitchenProduction/Prep/delete'); ?>",
                data: { id: id },
                success: function(response) {
                    if(response == 'success') {
                        Swal.fire('Deleted!', 'Prep area has been deleted.', 'success');
                        $('#row_' + id).fadeOut();
                    }
                }
            });
        }
    });
});

$(document).on("change", ".toggle-demo", function() {
    var id = $(this).attr('id');
    var status = $(this).is(':checked') ? 1 : 0;
    $.ajax({
        type: "POST",
        url: "<?php echo base_url('Compliance/KitchenProduction/Prep/change_status'); ?>",
        data: { id: id, status: status },
        success: function(response) {
            console.log("Status updated");
        }
    });
});
</script>
