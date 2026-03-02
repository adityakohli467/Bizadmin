<?php if($form_type == 'view'){ $disabled = 'disabled'; }else{ $disabled = ''; } ?>
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row rowMarginNegative">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <?php if ($form_type == 'add') { ?>    
                                <form action="/Temp/CalibrationTemp/Sitecalib/add" method="POST">
                            <?php } else if ($form_type == 'edit') { ?>
                                <form action="/Temp/CalibrationTemp/Sitecalib/edit/<?php echo (isset($site_detail[0]['id']) ? $site_detail[0]['id'] : ''); ?>" method="POST">     
                            <?php } ?>
                            <div id="customerList">
                                <div class="row g-4 mb-3">
                                    <div class="col-sm-auto">
                                        <div>
                                            <h4 class="card-title mb-0 text-uppercase fw-bold text-black"><?php echo $form_type; ?> Site</h4>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div class="d-flex justify-content-sm-end gap-2">
                                            <a href="/Temp/calibrationTemp/site" class="btn bg-orange waves-effect btn-label waves-light">
                                                <i class="ri-reply-fill label-icon align-middle fs-16 me-2"></i><span>Back</span>
                                            </a>     
                                            <?php if ($form_type == 'add') { ?>
                                                <button type="submit" class="btn btn-primary btn-label waves-effect waves-light">
                                                    <i class="ri-save-3-fill label-icon align-middle fs-16 me-2"></i><span>Add</span>
                                                </button>
                                            <?php } else if ($form_type == 'edit') { ?>
                                                <button type="submit" class="btn btn-primary btn-label waves-effect waves-light">
                                                    <i class="ri-refresh-line label-icon align-middle fs-16 me-2"></i><span>Update</span>
                                                </button>
                                                <input type="hidden" name="id" value>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-lg-4 mb-4">
                                        <label for="site_name" class="form-label">Site Name</label>
                                        <input type="text" class="form-control" required name="site_name" id="site_name" placeholder="Site Name" <?php echo $disabled; ?> value="<?php echo (isset($site_detail[0]['site_name']) ? $site_detail[0]['site_name'] : ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="row mt-4"> 
                                    <div class="col-md-5">
                                        <label for="sort_order" class="form-label fw-semibold">Add Staff Comments</label>
                                        <table class="table table-bordered">
                                            <tbody>
                                                <?php if (isset($site_detail[0]['staff_comments'])) {
                                                    $staff_comments = unserialize($site_detail[0]['staff_comments']);
                                                    if (is_array($staff_comments)) {
                                                        foreach ($staff_comments as $staff_comment) { ?>
                                                            <tr>
                                                                <td class="gap-2 d-flex">
                                                                    <input type="text" name="staff_comments[]" class="form-control item" value="<?php echo $staff_comment; ?>" placeholder="Enter comments" autocomplete="off" />
                                                                </td>
                                                                <td><button class="btn btn-success add-row" type="button">+</button></td>
                                                                <td><button type="button" class="btn btn-danger remove-row">-</button></td>
                                                            </tr>
                                                        <?php }
                                                    }
                                                } else { ?>
                                                    <tr>
                                                        <td class="gap-2 d-flex">
                                                            <input type="text" name="staff_comments[]" class="form-control item" placeholder="Enter staff comments" autocomplete="off" />
                                                        </td>
                                                        <td><button class="btn btn-success add-row" type="button">+</button></td>
                                                    </tr>  
                                                <?php } ?>
                                            </tbody>
                                        </table> 
                                        <small>Add the predetermined action items to be available for Staff while recording the calibration</small> 
                                    </div>     
                                    <div class="col-md-5">
                                        <label for="sort_order" class="form-label fw-semibold">Add Manager Comments</label>
                                        <table class="table table-bordered">
                                            <tbody>
                                                <?php if (isset($site_detail[0]['manager_comments'])) {
                                                    $manager_comments = unserialize($site_detail[0]['manager_comments']);
                                                    if (is_array($manager_comments)) {
                                                        foreach ($manager_comments as $manager_comment) { ?>
                                                            <tr>
                                                                <td class="gap-2 d-flex">
                                                                    <input type="text" name="manager_comments[]" class="form-control item" value="<?php echo $manager_comment; ?>" placeholder="Enter comments" autocomplete="off" />
                                                                </td>
                                                                <td><button class="btn btn-success add-rowManager" type="button">+</button></td>
                                                                <td><button type="button" class="btn btn-danger remove-rowManager">-</button></td>
                                                            </tr>
                                                        <?php }
                                                    }
                                                } else { ?>
                                                    <tr>
                                                        <td class="gap-2 d-flex">
                                                            <input type="text" name="manager_comments[]" class="form-control item" placeholder="Enter manager comments" autocomplete="off" />
                                                        </td>
                                                        <td><button class="btn btn-success add-rowManager" type="button">+</button></td>
                                                    </tr>  
                                                <?php } ?>
                                            </tbody>
                                        </table> 
                                        <small>Add the predetermined action items to be available if calibration issues are found</small> 
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('tbody').on('click', '.add-row', function () {
        let newRow = '<tr><td class="gap-2 d-flex"><input type="text" name="staff_comments[]" class="form-control item w-100" placeholder="Enter staff comments" autocomplete="off" />';
        newRow += '</td><td><button type="button" class="btn btn-success add-row">+</button></td><td><button type="button" class="btn btn-danger remove-row">-</button></td></tr>';
        $(this).closest('tr').after(newRow);
    });
    
    $('tbody').on('click', '.add-rowManager', function () {
        let newRow = '<tr><td class="gap-2 d-flex"><input type="text" name="manager_comments[]" class="form-control item w-100" placeholder="Enter manager comments" autocomplete="off" />';
        newRow += '</td><td><button type="button" class="btn btn-success add-rowManager">+</button></td><td><button type="button" class="btn btn-danger remove-rowManager">-</button></td></tr>';
        $(this).closest('tr').after(newRow);
    });

    $('tbody').on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
    });
    
    $('tbody').on('click', '.remove-rowManager', function () {
        $(this).closest('tr').remove();
    });
});
</script>
