<?php if(isset($form_type) && $form_type == 'view'){ $disabled = 'disabled'; }else{ $disabled = ''; } ?>
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row rowMarginNegative">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <?php if($form_type == 'add'){ ?>    
                                <form action="<?php echo base_url('Compliance/ThermometerCalibration/Prep/add'); ?>" method="POST">
                            <?php } else if($form_type == 'edit'){ ?>
                                <form action="<?php echo base_url('Compliance/ThermometerCalibration/Prep/edit'); ?>" method="POST">
                                <input type="hidden" name="id" value="<?php echo (isset($prep_detail[0]['id']) ? $prep_detail[0]['id'] : ''); ?>">     
                            <?php } ?>
                            <div id="customerList">
                                <div class="row g-4 mb-3">
                                    <div class="col-sm-auto">
                                        <div>
                                            <h4 class="card-title mb-0 text-uppercase fw-bold text-black">
                                                <i class="fa-solid fa-thermometer-half"></i> <?php echo ucfirst($form_type); ?> Thermometer Calibration Prep Area
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
                                            <a href="<?php echo base_url('Compliance/ThermometerCalibration/Prep'); ?>" class="btn bg-orange waves-effect btn-label waves-light">
                                                <i class="ri-reply-fill label-icon align-middle fs-16 me-2"></i><span>Back</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-lg-4 mb-4">
                                        <label for="prep_name" class="form-label fw-semibold">Prep Area Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" required name="prep_name" id="prep_name" 
                                               placeholder="Enter Prep Area Name" <?php echo $disabled; ?> 
                                               value="<?php echo (isset($prep_detail[0]['prep_name']) ? htmlspecialchars($prep_detail[0]['prep_name']) : ''); ?>">
                                    </div>
                                    
                                    <div class="col-lg-4 mb-4">
                                        <label for="site_id" class="form-label fw-semibold">Site <span class="text-danger">*</span></label>
                                        <select class="form-select" name="site_id" id="site_id" required <?php echo $disabled; ?>>
                                            <option value="">Select Site</option>
                                            <?php if(!empty($site_detail)) { ?>
                                                <?php foreach($site_detail as $site) { ?>
                                                    <option value="<?php echo $site['id']; ?>" 
                                                            <?php echo (isset($prep_detail[0]['site_id']) && $prep_detail[0]['site_id'] == $site['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($site['site_name']); ?>
                                                    </option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
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
