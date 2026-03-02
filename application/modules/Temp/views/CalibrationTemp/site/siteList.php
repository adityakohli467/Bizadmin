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
                                <h4 class="mb-sm-0 text-black">Calibration Manage Sites</h4>
                                <div class="page-title-right">
                                    <div class="d-flex justify-content-sm-end">
                                        <div class="d-flex justify-content-sm-end">
                                            <a href="<?php echo base_url('Temp/CalibrationTemp/Sitecalib/add') ?>" class="btn btn-primary">
                                                <i class="ri-add-line fs-14 align-bottom me-1"></i>Add Site
                                            </a>
                                            <button type="button" class="btn btn-primary" id="editModal" data-bs-toggle="modal" data-bs-target="#flipEditModal" style="display:none"></button>
                                        </div>
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
                                            <?php if (!empty($site_detail)) { ?>
                                                <?php foreach ($site_detail as $site) { ?>
                                                    <tr id="row_<?php echo $site['id']; ?>">
                                                        <td class="site_name"><?php echo $site['site_name']; ?></td>
                                                        <td>
                                                            <div class="form-check form-switch form-switch-custom form-switch-success">
                                                                <input class="form-check-input toggle-demo" type="checkbox" role="switch" id="<?php echo $site['id']; ?>" <?php if (isset($site['status']) && $site['status'] == '1') { echo 'checked'; } ?>>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <div class="edit">
                                                                    <a href="<?php echo base_url('Temp/CalibrationTemp/Sitecalib/edit/'.$site['id']); ?>" class="text-primary d-inline-block edit-item-btn">
                                                                        <i class="ri-pencil-fill fs-16"></i>
                                                                    </a>
                                                                </div>
                                                                <div class="remove">
                                                                    <a class="text-danger d-inline-block remove-item-btn" data-rel-id="<?php echo $site['id']; ?>">
                                                                        <i class="ri-delete-bin-5-fill fs-16"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
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
$(document).on("click", ".remove-item-btn", function() {
    var id = $(this).attr('data-rel-id');
    Swal.fire({
        title: "Are you sure?",
        icon: "warning",
        showCancelButton: !0,
        confirmButtonClass: "btn btn-primary w-xs me-2 mt-2",
        cancelButtonClass: "btn btn-danger w-xs mt-2",
        confirmButtonText: "Yes, delete it!",
        buttonsStyling: !1,
        showCloseButton: !0,
    }).then(function (e) {
        if (e.value) {
            $.ajax({
                type: "POST",
                url: "/Temp/CalibrationTemp/Sitecalib/delete",
                data: 'id=' + id,
                success: function(data) {
                    $('#row_' + id).remove();
                }
            });
        }
    });
});

$('#siteListDatatable').DataTable({
    pageLength: 100,
    bPaginate: false,
    bInfo: false,
    lengthMenu: [0, 5, 10, 20, 50, 100, 200, 500],
    "columnDefs": [{
        "targets": 'no-sort',
        "orderable": false
    }]
});

$(document).ready(() => {
    setTimeout(() => {
        $(".alert-success").fadeOut();
    }, 7000);
});

$('.toggle-demo').on('change', function() {
    let id = $(this).attr('id');
    let status = $(this).prop('checked') ? 1 : 0;
    $.ajax({
        type: "POST",
        enctype: 'multipart/form-data',
        url: "/Temp/CalibrationTemp/Sitecalib/change_status",
        data: {"status": status, "id": id},
        success: function(data) {
            console.log(data);
        }
    });
});
</script>
