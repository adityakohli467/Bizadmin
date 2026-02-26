<?php if(isset($form_type) && $form_type == 'view'){ $disabled = 'disabled'; }else{ $disabled = ''; } ?>
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row rowMarginNegative">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <?php if($form_type == 'add'){ ?>    
                                <form action="<?php echo base_url('Compliance/KitchenProduction/Site/add'); ?>" method="POST">
                            <?php } else if($form_type == 'edit'){ ?>
                                <form action="<?php echo base_url('Compliance/KitchenProduction/Site/edit/'.(isset($site_detail[0]['id']) ? $site_detail[0]['id'] : '')); ?>" method="POST">     
                            <?php } ?>
                            <div id="customerList">
                                <div class="row g-4 mb-3">
                                    <div class="col-sm-auto">
                                        <div>
                                            <h4 class="card-title mb-0 text-uppercase fw-bold text-black">
                                                <i class="fa-solid fa-utensils"></i> <?php echo ucfirst($form_type); ?> Kitchen Production Site
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div class="d-flex justify-content-sm-end gap-2">
                                            <?php if($form_type == 'add'){ ?>
                                                <button type="submit" class="btn btn-success btn-label waves-effect waves-light">
                                                    <i class="ri-save-3-fill label-icon align-middle fs-16 me-2"></i><span>Submit</span>
                                                </button>
                                            <?php } else if($form_type == 'edit'){ ?>
                                                <button type="submit" class="btn btn-success btn-label waves-effect waves-light">
                                                    <i class="ri-refresh-line label-icon align-middle fs-16 me-2"></i><span>Update</span>
                                                </button>
                                            <?php } ?>
                                            <a href="<?php echo base_url('Compliance/KitchenProduction/Site'); ?>" class="btn bg-orange waves-effect btn-label waves-light">
                                                <i class="ri-reply-fill label-icon align-middle fs-16 me-2"></i><span>Back</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-lg-4 mb-4">
                                        <label for="site_name" class="form-label fw-semibold">Site Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" required name="site_name" id="site_name" 
                                               placeholder="Enter Site Name" <?php echo $disabled; ?> 
                                               value="<?php echo (isset($site_detail[0]['site_name']) ? htmlspecialchars($site_detail[0]['site_name']) : ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="row mt-4"> 
                                    <div class="col-md-5">
                                        <label for="sort_order" class="form-label fw-semibold">Add Staff Comments (Optional)</label>
                                        <table class="table table-bordered">
                                            <tbody>
                                                <?php if(isset($site_detail[0]['staff_comments'])) {
                                                    $staff_comments = unserialize($site_detail[0]['staff_comments']);
                                                    if(is_array($staff_comments)){
                                                        foreach($staff_comments as $staff_comment){ ?>
                                                            <tr>
                                                                <td class="gap-2 d-flex">
                                                                    <input type="text" name="staff_comments[]" class="form-control item" 
                                                                           value="<?php echo htmlspecialchars($staff_comment); ?>" 
                                                                           placeholder="Enter comments" autocomplete="off" />
                                                                </td>
                                                                <td><button class="btn btn-success add-row" type="button">+</button></td>
                                                                <td><button type="button" class="btn btn-danger remove-row">-</button></td>
                                                            </tr>
                                                <?php } } } else { ?>
                                                    <tr>
                                                        <td class="gap-2 d-flex">
                                                            <input type="text" name="staff_comments[]" class="form-control item" 
                                                                   placeholder="Enter staff comments" autocomplete="off" />
                                                        </td>
                                                        <td><button class="btn btn-success add-row" type="button">+</button></td>
                                                    </tr>  
                                                <?php } ?>
                                            </tbody>
                                        </table> 
                                        <small class="text-muted">Add predetermined action items to be available for Staff</small> 
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
$(document).ready(function() {
    // Add row
    $(document).on('click', '.add-row', function() {
        var newRow = '<tr>' +
            '<td class="gap-2 d-flex">' +
            '<input type="text" name="staff_comments[]" class="form-control item" placeholder="Enter comments" autocomplete="off" />' +
            '</td>' +
            '<td><button class="btn btn-success add-row" type="button">+</button></td>' +
            '<td><button type="button" class="btn btn-danger remove-row">-</button></td>' +
            '</tr>';
        $(this).closest('tr').after(newRow);
    });
    
    // Remove row
    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
    });
});
</script>
