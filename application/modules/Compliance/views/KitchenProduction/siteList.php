<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
        
            <div class="col-12">
                <div class="alert alert-success fade show" role="alert" style="display:none">
                    Site Added Successfully
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 text-black"><i class="fa-solid fa-utensils"></i> Kitchen Production Sites</h4>

                                <div class="page-title-right">
                                    <div class="d-flex justify-content-sm-end gap-2">
                                        <a href="<?php echo base_url('Compliance/KitchenProduction/Home') ?>" class="btn btn-secondary">
                                            <i class="ri-arrow-go-back-line"></i> Back to Dashboard
                                        </a>
                                        <a href="<?php echo base_url('Compliance/KitchenProduction/Site/add') ?>" class="btn btn-primary">
                                            <i class="ri-add-line fs-14 align-bottom me-1"></i>Add Site
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div id="siteList">
                                <div class="table-responsive table-card mb-1">
                                    <table class="table align-middle table-nowrap" id="siteListDatatable">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="sort" data-sort="customer_name">Site Name</th>
                                                <th class="sort" data-sort="status">Status</th>
                                                <th class="no-sort">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all">
                                            <?php if(!empty($site_detail)) { ?>
                                                <?php foreach($site_detail as $site) { ?>
                                                    <tr id="row_<?php echo $site['id']; ?>">
                                                        <td class="site_name"><?php echo htmlspecialchars($site['site_name']); ?></td>
                                                        <td>
                                                            <div class="form-check form-switch form-switch-custom form-switch-success">
                                                                <input class="form-check-input toggle-demo" type="checkbox" role="switch" 
                                                                       id="<?php echo $site['id']; ?>" 
                                                                       <?php if(isset($site['status']) && $site['status'] == '1'){ echo 'checked'; }?>>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <ul class="list-inline hstack gap-2 mb-0">
                                                                <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                                                    data-bs-placement="top" title="Edit">
                                                                    <a href="<?php echo base_url('Compliance/KitchenProduction/Site/edit/'.$site['id']); ?>" 
                                                                       class="text-primary d-inline-block edit-item-btn">
                                                                        <i class="ri-pencil-fill fs-16"></i>
                                                                    </a>
                                                                </li>
                                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                                                    data-bs-placement="top" title="Remove">
                                                                    <a class="text-danger d-inline-block remove-item-btn" data-bs-toggle="modal"
                                                                       data-rel-id="<?php echo $site['id']; ?>">
                                                                        <i class="ri-delete-bin-5-fill fs-16"></i>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <tr>
                                                    <td colspan="3" class="text-center">No sites found. <a href="<?php echo base_url('Compliance/KitchenProduction/Site/add'); ?>">Add your first site</a>.</td>
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

<script>
$(document).ready(function() {
    $('#siteListDatatable').DataTable({
        lengthChange: false,
        pageLength: 25,
        "columnDefs": [{
            "targets": 'no-sort',
            "orderable": false
        }]
    });
});

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
                url: "<?php echo base_url('Compliance/KitchenProduction/Site/delete'); ?>",
                data: { id: id },
                success: function(response) {
                    if(response == 'success') {
                        Swal.fire('Deleted!', 'Site has been deleted.', 'success');
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
        url: "<?php echo base_url('Compliance/KitchenProduction/Site/change_status'); ?>",
        data: { id: id, status: status },
        success: function(response) {
            console.log("Status updated");
        }
    });
});
</script>
