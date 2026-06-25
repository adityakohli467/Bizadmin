<style>
    h6{
        color: #172153 !important;
    }
    .step-wizard .step {
        flex: 1;
        text-decoration: none;
        color: inherit;
        position: relative;
    }
    .step-wizard .step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 24px;
        right: -50%;
        width: 100%;
        height: 2px;
        background-color: #4867aa8f;
        z-index: 0;
    }
    .step .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #284990;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-size: 20px;
        position: relative;
        z-index: 1;
    }
    .step.active .icon-circle,
    .step:hover .icon-circle {
        background-color: #F06549;
    }
    .step .label {
        font-weight: 800;
        color: #172153 !important;
        font-size: 14px;
    }
    .pageTitle{
        font-weight: 800 !important;
        font-size: 28px !important;
        color: #172153 !important;
    }

    .sectionTitle{
        font-weight: 800 !important;
        color: #172153 !important;
    }

</style>

<div class="main-content">
    <div class="page-content" style="margin-top: 0px;">
        <div class="container-fluid">

            <div class="row">

                <h3 class="pageTitle">  Employee Details </h3>

                <!-- end col -->
                <div class="col-md-12 col-sm-12 col-lg-12 col-xxl-12">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="step-wizard d-flex justify-content-between mb-4" id="v-pills-tab" role="tablist">
                                <a class="step text-center active" id="v-pills-home-tab" data-bs-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">
                                    <div class="icon-circle"><i class="fas fa-user"></i></div>
                                    <div class="label">Personal</div>
                                </a>

                                <?php if($this->roleId != 4) { ?>
                                    <a class="step text-center" id="v-pills-profile-tab" data-bs-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">
                                        <div class="icon-circle"><i class="fas fa-briefcase"></i></div>
                                        <div class="label">Employment</div>
                                    </a>
                                <?php } ?>

                                <a class="step text-center" id="v-pills-onboarding-tab" data-bs-toggle="pill" href="#v-pills-onboarding" role="tab" aria-controls="v-pills-onboarding" aria-selected="false">
                                    <div class="icon-circle"><i class="fas fa-file-alt"></i></div>
                                    <div class="label">Onboarding Form</div>
                                </a>

                                <!--<a class="step text-center" id="v-pills-shifts-tab" data-bs-toggle="pill" href="#v-pills-shifts" role="tab" aria-controls="v-pills-shifts" aria-selected="false">-->
                                <!--  <div class="icon-circle"><i class="fas fa-clock"></i></div>-->
                                <!--  <div class="label">Shifts</div>-->
                                <!--</a>-->

                                <!--<a class="step text-center" id="v-pills-leaves-tab" data-bs-toggle="pill" href="#v-pills-leaves" role="tab" aria-controls="v-pills-leaves" aria-selected="false">-->
                                <!--  <div class="icon-circle"><i class="fas fa-plane-departure"></i></div>-->
                                <!--  <div class="label">Leave</div>-->
                                <!--</a>-->

                                <a class="step text-center" id="v-pills-unavailability-tab" data-bs-toggle="pill" href="#v-pills-unavailability" role="tab" aria-controls="v-pills-unavailability" aria-selected="false">
                                    <div class="icon-circle"><i class="fas fa-ban"></i></div>
                                    <div class="label">Unavailability</div>
                                </a>
                            </div>
                            <div class="tab-content text-black mt-4 mt-md-0" id="v-pills-tabContent">
                                <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                                    <form role="form" id="personalDetailsForm" method="post" class="mt-4" enctype="multipart/form-data">
                                        <div class="alert alert-success border-0 shadow d-none" role="alert">Details have been saved successfully</div>

                                        <input type="hidden" name="emp_id" value="<?php echo isset($employee['emp_id']) ? htmlspecialchars($employee['emp_id']) : ''; ?>">

                                        <div class="row gy-4">
                                            <h4 class="sectionTitle">Personal Details</h4>

                                            <!-- First Row: Title, First Name, Last Name, Preferred Name -->
                                            <div class="col-lg-2 col-md-3">
                                                <label for="title" class="form-label">Title:</label>
                                                <select class="form-select" id="title" name="title">
                                                    <option value="">Select</option>
                                                    <option value="Mr" <?php echo (isset($employee['title']) && $employee['title'] == 'Mr') ? 'selected' : ''; ?>>Mr</option>
                                                    <option value="Ms" <?php echo (isset($employee['title']) && $employee['title'] == 'Ms') ? 'selected' : ''; ?>>Ms</option>
                                                    <option value="Mrs" <?php echo (isset($employee['title']) && $employee['title'] == 'Mrs') ? 'selected' : ''; ?>>Mrs</option>
                                                    <option value="Miss" <?php echo (isset($employee['title']) && $employee['title'] == 'Miss') ? 'selected' : ''; ?>>Miss</option>
                                                    <option value="Dr" <?php echo (isset($employee['title']) && $employee['title'] == 'Dr') ? 'selected' : ''; ?>>Dr</option>
                                                </select>
                                                <span class="fieldError" id="title_error"></span>
                                            </div>

                                            <div class="col-lg-3 col-md-3">
                                                <label for="first_name" class="control-label">First Name:<span>*</span></label>
                                                <input type="text" id="first_name" class="form-control" name="first_name" value="<?php echo isset($employee['first_name']) ? htmlspecialchars($employee['first_name']) : ''; ?>" autocomplete="off">
                                                <span class="fieldError" id="first_name_error"></span>
                                            </div>

                                            <div class="col-lg-3 col-md-3">
                                                <label for="last_name" class="control-label">Last Name:<span>*</span></label>
                                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo isset($employee['last_name']) ? htmlspecialchars($employee['last_name']) : ''; ?>" autocomplete="off">
                                                <span class="fieldError" id="last_name_error"></span>
                                            </div>

                                            <div class="col-lg-4 col-md-3">
                                                <label for="preferred_name" class="control-label">Preferred Name:</label>
                                                <input type="text" id="preferred_name" class="form-control" name="preferred_name" value="<?php echo isset($employee['preferred_name']) ? htmlspecialchars($employee['preferred_name']) : ''; ?>" autocomplete="off">
                                            </div>

                                            <!-- Second Row: Contact Number, Email Address, Date of Birth, PIN Number -->
                                            <div class="col-lg-3 col-md-6">
                                                <label for="phone" class="form-label">Contact Number:<span>*</span></label>
                                                <input type="text" class="form-control" id="phone" name="phone" onkeypress='validate(event)' autocomplete="off" value="<?php echo isset($employee['phone']) ? htmlspecialchars($employee['phone']) : ''; ?>">
                                                <span class="fieldError" id="phone_error"></span>
                                            </div>

                                            <div class="col-lg-3 col-md-6">
                                                <label for="email" class="form-label">Email Address:<span>*</span></label>
                                                <input type="text" class="form-control" id="email" name="email" value="<?php echo isset($employee['email']) ? htmlspecialchars($employee['email']) : ''; ?>">
                                                <span class="fieldError" id="email_error"></span>
                                            </div>

                                            <div class="col-lg-3 col-md-6">
                                                <label for="dob" class="form-label">Date of Birth:<span>*</span></label>
                                                <input type="date" class="form-control datetime" id="dob" name="dob" autocomplete="off" value="<?php echo isset($employee['dob']) ? htmlspecialchars($employee['dob']) : ''; ?>">
                                                <span class="fieldError" id="dob_error"></span>
                                            </div>

                                            <div class="col-lg-3 col-md-6">
                                                <label for="pin" class="control-label">PIN Number:</label>
                                                <input type="text" id="pin" class="form-control" name="pin" value="<?php echo isset($employee['pin']) ? htmlspecialchars($employee['pin']) : ''; ?>" autocomplete="off">
                                            </div>

                                            <!-- Third Row: Password, Highest Academic Qualifications -->
                                            <div class="col-lg-3 col-md-6">
                                                <label for="password" class="form-label">Password:</label>
                                                <input type="password" class="form-control" id="password" name="password" autocomplete="off">
                                                <small class="text-muted">Leave blank to keep current password</small>
                                            </div>

                                            <div class="col-lg-9 col-md-6">
                                                <label for="heighest_acd_achmts" class="form-label">Highest Academic Qualifications:</label>
                                                <textarea class="form-control" rows="2" name="heighest_acd_achmts" id="heighest_acd_achmts"><?php echo isset($employee['heighest_acd_achmts']) ? htmlspecialchars($employee['heighest_acd_achmts']) : ''; ?></textarea>
                                            </div>

                                            <!-- Address Section -->
                                            <h5 class="fw-bold text-black">Residential Address</h5>

                                            <div class="col-lg-12 col-md-12">
                                                <label for="address" class="form-label">Address:</label>
                                                <input type="text" class="form-control" id="address" name="address" value="<?php echo isset($employee['address']) ? htmlspecialchars($employee['address']) : ''; ?>">
                                            </div>

                                            <!-- Commented sections preserved from original -->
                                            <!--   <div class="col-lg-3 col-md-6">-->
                                            <!--		<label for="businessname" class="form-label">Unit Number:</label>-->
                                            <!--		<input type="text" class="form-control" id="unit_number" name="unit_number" value="<?php echo isset($employee['unit_number']) ? htmlspecialchars($employee['unit_number']) : ''; ?>" autocomplete="off">-->
                                            <!--	</div>	-->
                                            <!--<div class="col-lg-3 col-md-6">-->
                                            <!--		<label for="businessname" class="form-label">Street Number:</label>-->
                                            <!--		<input type="text" class="form-control" id="street" name="street" value="<?php echo isset($employee['street']) ? htmlspecialchars($employee['street']) : ''; ?>"  autocomplete="off" >-->
                                            <!--			 <span class="fieldError" id="street_error"></span>-->
                                            <!--   </div>-->
                                            <!--<div class="col-lg-3 col-md-6">-->
                                            <!--		<label for="businessname" class="form-label">Street Name:<span>*</span></label>-->
                                            <!--		<input type="text" class="form-control" id="street_name" name="street_name" value="<?php echo isset($employee['street_name']) ? htmlspecialchars($employee['street_name']) : ''; ?>"  autocomplete="off" >-->
                                            <!--		 <span class="fieldError" id="streetname_error"></span>-->
                                            <!--	</div>-->
                                            <!--<div class="col-lg-3 col-md-6">-->
                                            <!--		<label for="businessname" class="form-label">Suburb:<span>*</span></label>-->
                                            <!--		<input type="text" class="form-control"  id="suburb" name="suburb" value="<?php echo isset($employee['suburb']) ? htmlspecialchars($employee['suburb']) : ''; ?>" autocomplete="off" >-->
                                            <!--			 <span class="fieldError" id="suburb_error"></span>-->
                                            <!--	</div>-->
                                            <!--<div class="col-lg-3 col-md-6">-->
                                            <!--		<label for="businessname" class="form-label">Postcode:<span>*</span></label>-->
                                            <!--		<input type="text" id="postcode" class="form-control" name="postcode" onkeypress='validate(event)' value="<?php echo (isset($employee['postcode']) && $employee['postcode'] != '0') ? htmlspecialchars($employee['postcode']) : ''; ?>" autocomplete="off" maxlength="4">-->
                                            <!--		 <span class="fieldError" id="postcode_error"></span>-->
                                            <!--	</div>-->
                                            <!--<div class="col-lg-3 col-md-6">-->
                                            <!--		<label for="businessname" class="form-label">State:<span>*</span></label>-->
                                            <!--		 <span class="fieldError" id="state_error"></span>-->
                                            <!--		<select class="form-select" name="state" id="state">-->
                                            <!--			<option value="">Select</option>-->
                                            <!--			<option value="nsw" <?php echo (isset($employee['state']) && $employee['state'] == 'nsw') ? 'selected' : ''; ?>>New South Wales</option>-->
                                            <!--			<option value="vic" <?php echo (isset($employee['state']) && $employee['state'] == 'vic') ? 'selected' : ''; ?>>Victoria</option>-->
                                            <!--			<option value="qld" <?php echo (isset($employee['state']) && $employee['state'] == 'qld') ? 'selected' : ''; ?>>Queensland</option>-->
                                            <!--			<option value="wa" <?php echo (isset($employee['state']) && $employee['state'] == 'wa') ? 'selected' : ''; ?>>Western Australia</option>-->
                                            <!--			<option value="sa" <?php echo (isset($employee['state']) && $employee['state'] == 'sa') ? 'selected' : ''; ?>>South Australia</option>-->
                                            <!--			<option value="tas" <?php echo (isset($employee['state']) && $employee['state'] == 'tas') ? 'selected' : ''; ?>>Tasmania</option>-->
                                            <!--			<option value="act" <?php echo (isset($employee['state']) && $employee['state'] == 'act') ? 'selected' : ''; ?>>Australian Capital Territory</option>-->
                                            <!--			<option value="nt" <?php echo (isset($employee['state']) && $employee['state'] == 'nt') ? 'selected' : ''; ?>>Northern Territory</option>-->
                                            <!--		</select>-->
                                            <!--	</div>-->
                                        </div>

                                        <input type="button" name="contact_submit" id="save_continue_personal" class="btn btn-success btn-ph" value="SAVE">
                                    </form>
                                </div>

     <div class="tab-pane fade mx-4" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
    <button type="button" class="btn btn-sm shadow-none show" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <span class="d-flex align-items-center">
            <img class="rounded-circle header-profile-user" src="/theme-assets/images/users/avatar-1.jpg" alt="Header Avatar">
            <span class="text-start ms-xl-2">
            <span class="ms-1 fs-16 text-black user-name-sub-text fw-bold"><?php echo $employee['first_name'].' '.$employee['last_name']; ?></span>
            </span>
        </span>
                                    </button>

                                    <form role="form" id="workDetailsForm" method="post" action="" class="mt-4" enctype="multipart/form-data">
                                        <div class="alert alert-success border-0 shadow d-none" role="alert">Details have been saved successfully</div>

                                        <input type="hidden" name="emp_id" value="<?php echo isset($employee['emp_id']) ? htmlspecialchars($employee['emp_id']) : ''; ?>">
                                        <input type="hidden" name="positionIdToRemove" class="allPositionIdsToRemove" value="">

                                        <div class="row gy-4">
                                            <h4 class="sectionTitle">Work Details</h4>
 
                                            <div class="col-lg-3 col-md-6">
                                                <label for="hired_on" class="control-label">Hired On:</label>
                                                <input readonly type="text" id="hired_on" class="form-control" name="created_at" value="<?php echo isset($employee['date_modified']) ? htmlspecialchars(date('d-m-Y', strtotime($employee['date_modified']))) : ''; ?>">
                                            </div>
                                             <?php if($this->roleId != 4) { ?>
                                            <div class="col-lg-3 col-md-6">
                                                <label for="effective_start_date" class="control-label">Effective Start Date:</label>
                                                <input  type="text" id="effective_start_date" class="form-control" name="effective_start_date" value="<?php echo isset($employee['effective_start_date']) ? htmlspecialchars(date('d-m-Y', strtotime($employee['effective_start_date']))) : ''; ?>">
                                            </div>
                                          <?php }  ?>
                                            <div class="col-lg-3 col-md-6">
                                                <label for="rate" class="control-label">Works At:</label>
                                                <input readonly type="text" id="rate" class="form-control" name="rate" value="<?php echo (isset($locationNames) && !empty($locationNames)) ? htmlspecialchars(implode(', ', $locationNames)) : ''; ?>">
                                            </div>

                                            <!-- Fields visible to Manager and Admin only -->
                                            <?php if($this->roleId != 4) { ?>
                                                <div class="col-lg-3 col-md-6">
                                                    <label for="emp_prep_area" class="form-label">Prep Area:<span>*</span></label>
                                                    <?php if (isset($prepAreaLists) && !empty($prepAreaLists)) { ?>
                                                        <select name="emp_prep_area" class="form-select" id="emp_prep_area">
                                                            <option value="">Select Prep Area</option>
                                                            <?php foreach ($prepAreaLists as $prepAreaList) { ?>
                                                                <option value="<?php echo isset($prepAreaList['id']) ? htmlspecialchars($prepAreaList['id']) : ''; ?>"
                                                                    <?php echo (isset($employee['emp_prep_area']) && isset($prepAreaList['id']) && $employee['emp_prep_area'] == $prepAreaList['id']) ? 'selected' : ''; ?>>
                                                                    <?php echo isset($prepAreaList['prep_name']) ? htmlspecialchars($prepAreaList['prep_name']) : ''; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    <?php } else { ?>
                                                        <select name="emp_prep_area" class="form-select" id="emp_prep_area">
                                                            <option value="">No prep areas available</option>
                                                        </select>
                                                    <?php } ?>
                                                    <span class="fieldError" id="prep_area_error"></span>
                                                </div>

                                                <div class="col-lg-3 col-md-6">
                                                    <label for="employee_type" class="form-label">Employment Type<span>*</span></label>
                                                    <select class="form-select" name="employee_type" id="employee_type">
                                                        <option value="">Select</option>
                                                        <option value="1" <?php echo (isset($employee['employee_type']) && $employee['employee_type'] == '1') ? 'selected' : ''; ?>>Full Time</option>
                                                        <option value="2" <?php echo (isset($employee['employee_type']) && $employee['employee_type'] == '2') ? 'selected' : ''; ?>>Part Time</option>
                                                        <option value="3" <?php echo (isset($employee['employee_type']) && $employee['employee_type'] == '3') ? 'selected' : ''; ?>>Casual</option>
                                                        <option value="4" <?php echo (isset($employee['employee_type']) && $employee['employee_type'] == '4') ? 'selected' : ''; ?>>Full Time Fixed Term</option>
                                                        <option value="5" <?php echo (isset($employee['employee_type']) && $employee['employee_type'] == '5') ? 'selected' : ''; ?>>Part Time Fixed Term</option>
                                                    </select>
                                                    <span class="fieldError" id="employee_type_error"></span>
                                                </div>

                                                <div class="col-lg-3 col-md-6">
                                                    <label for="stress_profile" class="form-label">Stress Profile:</label>
                                                    <select class="form-select" id="stress_profile" name="stress_profile">
                                                        <option value="">Select Stress Profile</option>
                                                        <?php if (isset($stressProfiles) && !empty($stressProfiles)) { ?>
                                                            <?php foreach ($stressProfiles as $stressProfile) { ?>
                                                                <option value="<?php echo isset($stressProfile['id']) ? htmlspecialchars($stressProfile['id']) : ''; ?>"
                                                                    <?php echo (isset($employee['stress_profile']) && isset($stressProfile['id']) && $stressProfile['id'] == $employee['stress_profile']) ? 'selected' : ''; ?>>
                                                                    <?php echo isset($stressProfile['stressProfileName']) ? htmlspecialchars($stressProfile['stressProfileName']) : ''; ?>
                                                                </option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            <?php } ?>

                                            <!-- Fields visible to Admin only -->
                                            <?php if($this->roleId == 1) { ?>
                                                <div class="col-lg-3 col-md-6">
                                                    <label for="tier" class="form-label">Tier</label>
                                                    <select class="form-select" name="tier" id="tier">
                                                        <option value="1" <?php echo (isset($employee['tier']) && ($employee['tier'] == '1' || $employee['tier'] == '')) ? 'selected' : ''; ?>>Tier 1</option>
                                                        <option value="2" <?php echo (isset($employee['tier']) && $employee['tier'] == '2') ? 'selected' : ''; ?>>Tier 2</option>
                                                        <option value="4" <?php echo (isset($employee['tier']) && $employee['tier'] == '4') ? 'selected' : ''; ?>>Tier 4 (Flat Rate)</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="col-lg-3 col-md-6">
                                                    <label for="tier" class="form-label">Primary position</label>
                                                    <select class="form-select" name="primary_position">
                            <option value="">Select</option>
                            <?php foreach ($positions as $position) { ?>
                                <option value="<?= htmlspecialchars($position['position_id']) ?>"
                                    <?= ($position['position_id'] == $employee['primary_position']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($position['position_name']) ?>
                                </option>
                            <?php } ?>
                        </select>
                                                </div>
                                                
                                            <?php } ?>
                                        </div>

                                        <!-- Position Details and Pay Rates - Admin only -->
                                        <?php if($this->roleId == 1) { ?>
                                            <div class="row mt-4">
                                                <h4 class="sectionTitle">Position Details and Pay Rates</h4>
                                                <small>select employee positions and their rates for weekday, sunday, public holidays etc....</small>

                                                <div class="row">
                                                    <div class="col-md-12 col-sm-12 table-responsive">
                                                        <table class="table table-bordered mt-3 positionTable" id="positionTable">
                                                            <tbody>
                                                            <?php if (isset($empPositionAndRatesData) && !empty($empPositionAndRatesData)) { ?>
                                                                <?php foreach ($empPositionAndRatesData as $employeePR) { ?>
                                                                      <tr class="positionMainRow withData">
            <td>
                <div class="row g-3 align-items-end">

                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Position<span>*</span></label>
                        <select class="form-select" name="position_id[]">
                            <option value="">Select</option>
                            <?php foreach ($positions as $position) { ?>
                                <option value="<?= htmlspecialchars($position['position_id']) ?>"
                                    <?= ($position['position_id'] == $employeePR['position_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($position['position_name']) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Payroll Type</label>
                        <select class="form-select" name="payroll_type_id[]">
                            <option value="">Select</option>
                            <?php foreach ($payrollTypes as $pt) { ?>
                                <option value="<?= htmlspecialchars($pt['id']) ?>"
                                    <?= ($employeePR['payroll_type_id'] == $pt['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pt['name']) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-lg-1 col-md-2">
                        <label class="form-label">Weekday<span>*</span></label>
                        <input type="number" class="form-control" name="rate[]"
                               value="<?= number_format((float)$employeePR['rate'], 2) ?>">
                    </div>

                    <div class="col-lg-1 col-md-2">
                        <label class="form-label">Saturday<span>*</span></label>
                        <input type="number" class="form-control" name="Saturday_rate[]"
                               value="<?= number_format((float)$employeePR['Saturday_rate'], 2) ?>">
                    </div>

                    <div class="col-lg-1 col-md-2">
                        <label class="form-label">Sunday<span>*</span></label>
                        <input type="number" class="form-control" name="Sunday_rate[]"
                               value="<?= number_format((float)$employeePR['Sunday_rate'], 2) ?>">
                    </div>

                    <div class="col-lg-2 col-md-3">
                        <label class="form-label">Public Holiday<span>*</span></label>
                        <input type="number" class="form-control" name="holiday_rate[]"
                               value="<?= number_format((float)$employeePR['holiday_rate'], 2) ?>">
                    </div>

                    <div class="col-lg-1 col-md-2">
                        <label class="form-label">Early Start</label>
                        <input type="number" class="form-control" name="early_start[]"
                               value="<?= number_format((float)$employeePR['early_start'], 2) ?>">
                    </div>

                    <div class="col-lg-1 col-md-2">
                        <label class="form-label">Late Night</label>
                        <input type="number" class="form-control" name="late_night[]"
                               value="<?= number_format((float)$employeePR['late_night'], 2) ?>">
                    </div>

                    <div class="col-lg-1 col-md-2">
                        <label class="form-label">Uniform</label>
                        <input type="number" class="form-control" name="uniform_allowance[]"
                               value="<?= number_format((float)$employeePR['uniform_allowance'], 2) ?>">
                    </div>

                    <input type="hidden" name="position_unique_id[]"
                           value="<?= htmlspecialchars($employeePR['id']) ?>">
                </div>
            </td>

            <td class="text-center align-middle" style="width:80px;">
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-success add-Positionrow" type="button">+</button>
                    <button class="btn btn-danger remove-Positionrow" type="button">−</button>
                </div>
            </td>
        </tr>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                             <tr class="positionMainRow withoutData">
    <td>
        <div class="row g-3 align-items-end">

            <div class="col-lg-2 col-md-4">
                <label class="form-label">Position<span>*</span></label>
                <select class="form-select" name="position_id[]">
                    <option value="">Select</option>
                    <?php foreach ($positions as $position) { ?>
                        <option value="<?= htmlspecialchars($position['position_id']) ?>">
                            <?= htmlspecialchars($position['position_name']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-lg-2 col-md-4">
                <label class="form-label">Payroll Type</label>
                <select class="form-select" name="payroll_type_id[]">
                    <option value="">Select</option>
                    <?php foreach ($payrollTypes as $pt) { ?>
                        <option value="<?= htmlspecialchars($pt['id']) ?>">
                            <?= htmlspecialchars($pt['name']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-lg-1 col-md-2">
                <label class="form-label">Weekday<span>*</span></label>
                <input type="number" class="form-control" name="rate[]" autocomplete="off">
            </div>

            <div class="col-lg-1 col-md-2">
                <label class="form-label">Saturday<span>*</span></label>
                <input type="number" class="form-control" name="Saturday_rate[]" autocomplete="off">
            </div>

            <div class="col-lg-1 col-md-2">
                <label class="form-label">Sunday<span>*</span></label>
                <input type="number" class="form-control" name="Sunday_rate[]" autocomplete="off">
            </div>

            <div class="col-lg-2 col-md-3">
                <label class="form-label">Public Holiday<span>*</span></label>
                <input type="number" class="form-control" name="holiday_rate[]" autocomplete="off">
            </div>

            <div class="col-lg-1 col-md-2">
                <label class="form-label">Early Start</label>
                <input type="number" class="form-control" name="early_start[]" autocomplete="off">
            </div>

            <div class="col-lg-1 col-md-2">
                <label class="form-label">Late Night</label>
                <input type="number" class="form-control" name="late_night[]" autocomplete="off">
            </div>

            <div class="col-lg-1 col-md-2">
                <label class="form-label">Uniform</label>
                <input type="number" class="form-control" name="uniform_allowance[]" autocomplete="off">
            </div>

        </div>
    </td>

    <td class="text-center align-middle" style="width:80px;">
        <div class="d-flex gap-2 justify-content-center">
            <button class="btn btn-success add-Positionrow" type="button">+</button>
            <button class="btn btn-danger remove-Positionrow" type="button">−</button>
        </div>
    </td>
</tr>

                                                            <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <input type="button" name="contact_submit" id="save_continue_work" class="btn btn-success btn-ph" value="SAVE">
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="v-pills-onboarding" role="tabpanel" aria-labelledby="v-pills-onboarding-tab">

                                    <style>
                                        /* Modern tab styling */
                                        .custom-tabs .nav-link {
                                            background: #f8f9fa;
                                            border-radius: 10px;
                                            margin: 0 6px;
                                            padding: 12px 16px;
                                            font-weight: 600;
                                            color: #6c757d;
                                            border: 1px solid #e5e7eb;
                                            transition: all .25s ease;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;

                                        }

                                        .custom-tabs .nav-link:hover {
                                            background: #eef2ff;
                                            color: #4f46e5;
                                            transform: translateY(-2px);
                                        }

                                        .custom-tabs .nav-link.active {
                                            background: #284990 !important;
                                            color: #fff !important;
                                        }

                                        .custom-tabs .nav-link i {
                                            font-size: 16px;
                                        }
                                    </style>

                                    <?php
                                    // Show/hide onboarding tabs based on the per-location
                                    // "Onboarding Form Customize" settings (configureFor = 'onboarding_tabs').
                                    $onbTabConfig = isset($onboardingTabsConfig) && is_array($onboardingTabsConfig) ? $onboardingTabsConfig : [];
                                    $isOnbTabEnabled = function($id) use ($onbTabConfig) {
                                        return !isset($onbTabConfig[$id]) || $onbTabConfig[$id] == '1' || $onbTabConfig[$id] === 1;
                                    };
                                    $onbTabOrder = ['emergencyDetails', 'bankDetails', 'policeClearance', 'taxDetails', 'superAnnuation', 'privacyPolicy'];
                                    $onbEnabledTabs = array_values(array_filter($onbTabOrder, $isOnbTabEnabled));
                                    $onbFirstTab = !empty($onbEnabledTabs) ? $onbEnabledTabs[0] : '';
                                    ?>

                                    <ul class="nav nav-tabs nav-justified mb-4 d-flex custom-tabs gap-2" role="tablist">

                                        <?php if($isOnbTabEnabled('emergencyDetails')): ?>
                                        <li class="nav-item" rel="emergencyDetails">
                                            <a class="nav-link <?php echo $onbFirstTab === 'emergencyDetails' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#emergencyDetails" role="tab">
                                                <span>Emergency Details</span>
                                                <?php if(isset($employee['stepsCompleted']) && $employee['stepsCompleted'] > 1){ ?>
                                                    <i class="ri-check-double-line text-success"></i>
                                                <?php } ?>
                                            </a>
                                        </li>
                                        <?php endif; ?>

                                        <?php if($isOnbTabEnabled('bankDetails')): ?>
                                        <li class="nav-item" rel="bankDetails">
                                            <a class="nav-link <?php echo $onbFirstTab === 'bankDetails' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#bankDetails" role="tab">
                                                <span>Bank Details</span>
                                                <?php if(isset($employee['stepsCompleted']) && $employee['stepsCompleted'] > 2){ ?>
                                                    <i class="ri-check-double-line text-success"></i>
                                                <?php } ?>
                                            </a>
                                        </li>
                                        <?php endif; ?>

                                        <?php if($isOnbTabEnabled('policeClearance')): ?>
                                        <li class="nav-item" rel="policeClearance">
                                            <a class="nav-link <?php echo $onbFirstTab === 'policeClearance' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#policeClearance" role="tab">
                                                <span>Police Clearance</span>
                                                <?php if(isset($employee['stepsCompleted']) && $employee['stepsCompleted'] > 3){ ?>
                                                    <i class="ri-check-double-line text-success"></i>
                                                <?php } ?>
                                            </a>
                                        </li>
                                        <?php endif; ?>

                                        <?php if($isOnbTabEnabled('taxDetails')): ?>
                                        <li class="nav-item" rel="taxDetails">
                                            <a class="nav-link <?php echo $onbFirstTab === 'taxDetails' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#taxDetails" role="tab">
                                                <span>Tax Details</span>
                                                <?php if(isset($employee['stepsCompleted']) && $employee['stepsCompleted'] > 4){ ?>
                                                    <i class="ri-check-double-line text-success"></i>
                                                <?php } ?>
                                            </a>
                                        </li>
                                        <?php endif; ?>

                                        <?php if($isOnbTabEnabled('superAnnuation')): ?>
                                        <li class="nav-item" rel="superAnnuation">
                                            <a class="nav-link <?php echo $onbFirstTab === 'superAnnuation' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#superAnnuation" role="tab">
                                                <span>Super Annuation</span>
                                                <?php if(isset($employee['stepsCompleted']) && $employee['stepsCompleted'] > 5){ ?>
                                                    <i class="ri-check-double-line text-success"></i>
                                                <?php } ?>
                                            </a>
                                        </li>
                                        <?php endif; ?>

                                        <?php if($isOnbTabEnabled('privacyPolicy')): ?>
                                        <li class="nav-item" rel="privacyPolicy">
                                            <a class="nav-link <?php echo $onbFirstTab === 'privacyPolicy' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#privacyPolicy" role="tab">
                                                <span>Policies</span>
                                                <?php if(isset($employee['stepsCompleted']) && $employee['stepsCompleted'] > 6){ ?>
                                                    <i class="ri-check-double-line text-success"></i>
                                                <?php } ?>
                                            </a>
                                        </li>
                                        <?php endif; ?>

                                    </ul>

                                    <div class="tab-content text-black">
                                        <div class="tab-pane <?php echo $onbFirstTab === 'emergencyDetails' ? 'active' : ''; ?>" id="emergencyDetails" role="emergencyDetails">
                                            <!-- Emergency Details content remains the same -->
                                            <form role="form" id="emergencyDetailsForm" method="post" action="" enctype="multipart/form-data">
                                                <div class="alert alert-success border-0 shadow d-none" role="alert">Details have been saved successfully</div>

                                                <input type="hidden" name="emp_id" value="<?php echo isset($employee['emp_id']) ? htmlspecialchars($employee['emp_id']) : ''; ?>">

                                                <div class="row gy-4">
                                                    <div class="col-lg-3 col-md-6">
                                                        <label for="nextkin_name_two" class="control-label">Name:<span>*</span></label>
                                                        <input type="text" id="nextkin_name_two" class="form-control required"
                                                               value="<?php echo isset($employee['nextkin_name_two']) ? htmlspecialchars($employee['nextkin_name_two']) : ''; ?>"
                                                               name="nextkin_name_two" autocomplete="off">
                                                        <span class="fieldError" id="nextkin_name_two_error"></span>
                                                    </div>

                                                    <div class="col-lg-3 col-md-6">
                                                        <label for="nextkin_relationship_two" class="control-label">Relationship:<span>*</span></label>
                                                        <input type="text" class="form-control required" id="nextkin_relationship_two"
                                                               value="<?php echo isset($employee['nextkin_relationship_two']) ? htmlspecialchars($employee['nextkin_relationship_two']) : ''; ?>"
                                                               name="nextkin_relationship_two" autocomplete="off">
                                                        <span class="fieldError" id="nextkin_relationship_two_error"></span>
                                                    </div>

                                                    <div class="col-lg-3 col-md-6">
                                                        <label for="nextkin_email_two" class="form-label">Email Address:</label>
                                                        <input type="text" class="form-control" name="nextkin_email_two"
                                                               value="<?php echo isset($employee['nextkin_email_two']) ? htmlspecialchars($employee['nextkin_email_two']) : ''; ?>"
                                                               autocomplete="off">
                                                    </div>

                                                    <div class="col-lg-3 col-md-6">
                                                        <label for="nextkin_phone_no" class="control-label">Contact No:<span>*</span></label>
                                                        <input type="text" id="nextkin_phone_no" class="form-control required"
                                                               value="<?php echo isset($employee['nextkin_phone_no']) ? htmlspecialchars($employee['nextkin_phone_no']) : ''; ?>"
                                                               name="nextkin_phone_no" autocomplete="off">
                                                        <span class="fieldError" id="nextkin_phone_no_error"></span>
                                                    </div>

                                                    <div class="col-lg-6 col-md-12">
                                                        <label for="emergency_address" class="form-label">Address:</label>
                                                        <input type="text" class="form-control" id="emergency_address" name="emergency_address"
                                                               value="<?php echo isset($employee['emergency_address']) ? htmlspecialchars($employee['emergency_address']) : ''; ?>"
                                                               placeholder="Start typing your address…" autocomplete="off">
                                                    </div>

                                                    <!-- Commented out fields preserved as in original -->
                                                    <!--<div class="col-lg-3 col-md-6">-->
                                                    <!--    <label for="nextkin_street" class="form-label">Street address:</label>-->
                                                    <!--    <input type="text" class="form-control" name="nextkin_street" -->
                                                    <!--        value="<?php echo isset($employee['nextkin_street']) ? htmlspecialchars($employee['nextkin_street']) : ''; ?>" -->
                                                    <!--        autocomplete="off">-->
                                                    <!--</div>-->

                                                    <!--<div class="col-lg-3 col-md-6">-->
                                                    <!--    <label for="nextkin_suburb" class="form-label">Town/Suburb:</label>-->
                                                    <!--    <input type="text" class="form-control" name="nextkin_suburb" -->
                                                    <!--        value="<?php echo isset($employee['nextkin_suburb']) ? htmlspecialchars($employee['nextkin_suburb']) : ''; ?>" -->
                                                    <!--        autocomplete="off">-->
                                                    <!--</div>-->

                                                    <!--<div class="col-lg-3 col-md-6">-->
                                                    <!--    <label for="nextkin_postcode" class="form-label">Postcode:</label>-->
                                                    <!--    <input type="text" class="form-control" name="nextkin_postcode" -->
                                                    <!--        value="<?php echo isset($employee['nextkin_postcode']) ? htmlspecialchars($employee['nextkin_postcode']) : ''; ?>" -->
                                                    <!--        autocomplete="off">-->
                                                    <!--</div>-->

                                                    <!--<div class="col-lg-3 col-md-6">-->
                                                    <!--    <label for="nextkin_state" class="form-label">State:</label>-->
                                                    <!--    <input type="text" class="form-control" name="nextkin_state" -->
                                                    <!--        value="<?php echo isset($employee['nextkin_state']) ? htmlspecialchars($employee['nextkin_state']) : ''; ?>" -->
                                                    <!--        autocomplete="off">-->
                                                    <!--</div>-->
                                                </div>

                                                <input type="button" rel="bankDetails" name="contact_submit" id="save_continue_emergency" class="btn btn-success btn-ph" value="SAVE">
                                            </form>
                                        </div>

                                        <div class="tab-pane <?php echo $onbFirstTab === 'bankDetails' ? 'active' : ''; ?>" id="bankDetails">
                                            <!-- Bank Details content remains the same -->
                                            <div class="card shadow-sm">
                                                <div class="card-body p-5">
                                                    <h4 class="card-title mb-4 sectionTitle">Bank Account Details</h4>
                                                    <form id="bankDetailsForm" method="post" enctype="multipart/form-data">
                                                        <input type="hidden" name="emp_id" value="<?php echo isset($employee['emp_id']) ? htmlspecialchars($employee['emp_id']) : ''; ?>">

                                                        <!-- Account 1 – Compact single row -->
                                                        <h5 class="mt-2 mb-3 fw-bold text-black">Account No 1 <span class="text-danger">*</span></h5>
                                                        <div class="row g-3 align-items-end mb-5">
                                                            <div class="col-md-3 col-lg-2">
                                                                <label class="form-label form-label-required">Bank Name</label>
                                                                <input type="text" class="form-control" placeholder="Bank Name" name="bank_1"
                                                                       value="<?php echo isset($employee['bank_1']) ? htmlspecialchars($employee['bank_1']) : ''; ?>" required>
                                                            </div>
                                                            <div class="col-md-3 col-lg-3">
                                                                <label class="form-label form-label-required">Account Name</label>
                                                                <input type="text" class="form-control" placeholder="Account Name" name="account_name_1"
                                                                       value="<?php echo isset($employee['account_name_1']) ? htmlspecialchars($employee['account_name_1']) : ''; ?>" required>
                                                            </div>
                                                            <div class="col-md-2 col-lg-1">
                                                                <label class="form-label form-label-required">BSB</label>
                                                                <input type="text" class="form-control" placeholder="BSB" name="bsb_1" maxlength="6"
                                                                       value="<?php echo isset($employee['bsb_1']) ? htmlspecialchars($employee['bsb_1']) : ''; ?>" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="form-label form-label-required">Account No</label>
                                                                <input type="text" class="form-control" placeholder="Account No" name="account_no_1"
                                                                       value="<?php echo isset($employee['account_no_1']) ? htmlspecialchars($employee['account_no_1']) : ''; ?>" required>
                                                            </div>
                                                            <div class="col-md-2 col-lg-1">
                                                                <label class="form-label form-label-required">% to Deposit</label>
                                                                <input type="number" min="1" max="100" class="form-control" placeholder="% to Deposit" name="percentage_1"
                                                                       value="<?php echo isset($employee['percentage_1']) && !empty($employee['percentage_1']) ? htmlspecialchars($employee['percentage_1']) : '100'; ?>" required>
                                                            </div>
                                                        </div>

                                                        <!-- Account 2 (optional) – same compact style -->
                                                        <h5 class="mt-4 mb-3 fw-bold text-black">Account No 2 (Optional)</h5>
                                                        <div class="row g-3 align-items-end mb-4">
                                                            <div class="col-md-3 col-lg-2">
                                                                <input type="text" class="form-control" placeholder="Bank Name" name="bank_2"
                                                                       value="<?php echo isset($employee['bank_2']) ? htmlspecialchars($employee['bank_2']) : ''; ?>">
                                                            </div>
                                                            <div class="col-md-3 col-lg-3">
                                                                <input type="text" class="form-control" placeholder="Account Name" name="account_name_2"
                                                                       value="<?php echo isset($employee['account_name_2']) ? htmlspecialchars($employee['account_name_2']) : ''; ?>">
                                                            </div>
                                                            <div class="col-md-2 col-lg-1">
                                                                <input type="text" class="form-control" placeholder="BSB" name="bsb_2"
                                                                       value="<?php echo isset($employee['bsb_2']) ? htmlspecialchars($employee['bsb_2']) : ''; ?>">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" placeholder="Account No" name="account_no_2"
                                                                       value="<?php echo isset($employee['account_no_2']) ? htmlspecialchars($employee['account_no_2']) : ''; ?>">
                                                            </div>
                                                            <div class="col-md-2 col-lg-1">
                                                                <input type="number" min="0" max="99" placeholder="% to Deposit" class="form-control" name="percentage_2"
                                                                       value="<?php echo isset($employee['percentage_2']) ? htmlspecialchars($employee['percentage_2']) : ''; ?>">
                                                            </div>
                                                        </div>

                                                        <!-- Account 3 (optional) – same compact style -->
                                                        <h5 class="mt-4 mb-3 fw-bold text-black">Account No 3 (Optional)</h5>
                                                        <div class="row g-3 align-items-end">
                                                            <div class="col-md-3 col-lg-2">
                                                                <input type="text" placeholder="Bank Name" class="form-control" name="bank_3"
                                                                       value="<?php echo isset($employee['bank_3']) ? htmlspecialchars($employee['bank_3']) : ''; ?>">
                                                            </div>
                                                            <div class="col-md-3 col-lg-3">
                                                                <input type="text" placeholder="Account Name" class="form-control" name="account_name_3"
                                                                       value="<?php echo isset($employee['account_name_3']) ? htmlspecialchars($employee['account_name_3']) : ''; ?>">
                                                            </div>
                                                            <div class="col-md-2 col-lg-1">
                                                                <input type="text" placeholder="BSB" class="form-control" name="bsb_3"
                                                                       value="<?php echo isset($employee['bsb_3']) ? htmlspecialchars($employee['bsb_3']) : ''; ?>">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" placeholder="Account No" name="account_no_3"
                                                                       value="<?php echo isset($employee['account_no_3']) ? htmlspecialchars($employee['account_no_3']) : ''; ?>">
                                                            </div>
                                                            <div class="col-md-2 col-lg-1">
                                                                <input type="number" placeholder="% to Deposit" min="0" max="99" class="form-control" name="percentage_3"
                                                                       value="<?php echo isset($employee['percentage_3']) ? htmlspecialchars($employee['percentage_3']) : ''; ?>">
                                                            </div>
                                                        </div>

                                                        <p class="text-muted small mt-4">
                                                            I authorise the company to deposit my wages into the above account(s)... (your existing text)
                                                        </p>

                                                        <div class="text-end mt-5">
                                                            <input type="button" id="save_continue_bank" rel="policeClearance" class="btn btn-success px-5" value="Save →">
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Police Clearance moved here (before Tax Details) -->
                                        <div class="tab-pane <?php echo $onbFirstTab === 'policeClearance' ? 'active' : ''; ?>" id="policeClearance">
                                            <div class="card shadow-sm">
                                                <div class="card-body p-5 text-center">
                                                    <h4 class="card-title mb-4 mb-4 fw-bold text-black">Police Clearance Certificate <span class="text-danger">*</span></h4>
                                                    <p class="lead">A valid police clearance is mandatory before you can start work.</p>

                                                    <?php
                                                    if (isset($employee['police_certificate']) && !empty($employee['police_certificate']) && is_string($employee['police_certificate'])) {
                                                        $files = [];

                                                        // Try to unserialize first
                                                        if (is_serialized($employee['police_certificate'])) {
                                                            $files = unserialize($employee['police_certificate']);
                                                        } else {
                                                            // If it's not serialized, treat as single file
                                                            $files = [$employee['police_certificate']];
                                                        }

                                                        if (is_array($files) && !empty($files)) {
                                                            foreach ($files as $f) {
                                                                if (!empty($f)) {
                                                                    $fileExtension = pathinfo($f, PATHINFO_EXTENSION);
                                                                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];

                                                                    if (in_array(strtolower($fileExtension), $allowedExtensions)) {
                                                                        ?>
                                                                        <div class="mb-3">
                                                                            <a href="<?php echo base_url('uploaded_files/' . $this->session->userdata('tenantIdentifier') . '/HR/OnboardingFiles/' . htmlspecialchars($f)); ?>"
                                                                               target="_blank"
                                                                               class="btn btn-sm btn-success mb-2">
                                                                                <i class="fas fa-eye me-1"></i> View Uploaded File (<?php echo strtoupper($fileExtension); ?>)
                                                                            </a>

                                                                            <?php if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif'])) { ?>
                                                                                <div class="mt-2">
                                                                                    <img src="<?php echo base_url('uploaded_files/' . $this->session->userdata('tenantIdentifier') . '/HR/OnboardingFiles/' . htmlspecialchars($f)); ?>"
                                                                                         alt="Police Certificate"
                                                                                         class="img-fluid rounded border"
                                                                                         style="max-height: 300px;">
                                                                                </div>
                                                                            <?php } ?>
                                                                        </div>
                                                                        <?php
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ?>

                                                    <form id="policeDetailsForm" method="post" enctype="multipart/form-data">
                                                        <input type="hidden" name="emp_id" value="<?php echo isset($employee['emp_id']) ? htmlspecialchars($employee['emp_id']) : ''; ?>">

                                                        <!-- Custom Dropzone – Click & Drag works perfectly -->
                                                        <div class="dropzone-wrapper position-relative">
                                                            <div class="dropzone border-3 border-primary border-dashed rounded-4 bg-light p-5"
                                                                 id="policeDropzone"
                                                                 style="cursor: pointer; transition: all 0.3s;">
                                                                <i class="fas fa-cloud-upload-alt fa-4x text-primary mb-3"></i>
                                                                <h5 class="mb-2 text-black">
                                                                    <strong>Drop your Police Clearance here</strong><br>
                                                                    <span class="text-black">or click to browse</span>
                                                                </h5>
                                                                <p class="text-black mb-0">
                                                                    PDF, JPG, PNG • Max 5MB
                                                                </p>
                                                                <!-- Hidden real file input -->
                                                                <input type="file"
                                                                       name="userfile[]"
                                                                       id="policeFileInput"
                                                                       multiple
                                                                       accept=".pdf,.jpg,.jpeg,.png"
                                                                       class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                                                       style="cursor: pointer;">
                                                            </div>
                                                        </div>

                                                        <!-- Optional: Show selected file names -->
                                                        <div id="fileList" class="mt-3 text-start"></div>

                                                        <div class="text-end mt-5">
                                                            <input type="button" id="save_continue_police" rel="taxDetails" class="btn btn-success px-5" value="Save →">
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tax Details moved after Police Clearance -->
                                        <div class="tab-pane <?php echo $onbFirstTab === 'taxDetails' ? 'active' : ''; ?>" id="taxDetails" role="taxDetails">
                                            <!-- Tax Details content remains the same -->
                                            <form role="form" id="taxDetailsForm" method="post" action="" enctype="multipart/form-data">
                                                <div class="alert alert-success border-0 shadow d-none" role="alert">Details have been saved successfully</div>

                                                <input type="hidden" name="emp_id" value="<?php echo isset($employee['emp_id']) ? htmlspecialchars($employee['emp_id']) : ''; ?>">

                                                <div class="section-wrap">
                                                    <div class="form-row">
                                                        <div class="checkbox-group col-md-12">
                                                            <p>Do you have your TFN?</p>

                                                            <div class="tab">
                                                                <a class="tablinks tablinks_tfn <?php echo (isset($employee['check_tfn_type']) && $employee['check_tfn_type'] == 'tfn_number') ? 'active' : ''; ?>"
                                                                   onclick="openThisTab(event, 'Yes','tfn')">Yes</a>
                                                                <a class="tablinks tablinks_tfn <?php echo (isset($employee['check_tfn_type']) && $employee['check_tfn_type'] == 'tfn_type') ? 'active' : ''; ?>"
                                                                   onclick="openThisTab(event, 'No','tfn')">No</a>
                                                            </div>

                                                            <!-- Tab content -->
                                                            <div id="Yes" class="tabcontent tabcontent_tfn">
                                                                <div class="form-row">
                                                                    <div class="form-group col-md-6">
                                                                        <label for="tfn_number" class="form-label">Enter Tax File Number:<span>*</span></label>
                                                                        <input type="text" class="form-control" id="tfn_number" name="tfn_number"
                                                                               value="<?php echo isset($employee['tfn_number']) ? htmlspecialchars($employee['tfn_number']) : ''; ?>" autocomplete="off">
                                                                        <span class="fieldError" id="tfn_number_error"></span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div id="No" class="tabcontent tabcontent_tfn">
                                                                <label>
                                                                    <input type="radio" value="pendingTFN" name="tfn_type"
                                                                        <?php echo (isset($employee['tfn_type']) && $employee['tfn_type'] == 'pendingTFN') ? 'checked' : ''; ?>>
                                                                    My TFN is pending
                                                                </label><br>
                                                                <label>
                                                                    <input type="radio" value="noTFN" name="tfn_type"
                                                                        <?php echo (isset($employee['tfn_type']) && $employee['tfn_type'] == 'noTFN') ? 'checked' : ''; ?>>
                                                                    I'm under 18 and don't have a TFN
                                                                </label><br>
                                                                <label>
                                                                    <input type="radio" value="quotingTFN" name="tfn_type"
                                                                        <?php echo (isset($employee['tfn_type']) && $employee['tfn_type'] == 'quotingTFN') ? 'checked' : ''; ?>>
                                                                    I have an exemption from quoting a TFN (such as receiving a social security or service pension)
                                                                </label>
                                                                <span class="fieldError" id="tfn_type_error"></span>
                                                            </div>

                                                            <input type="hidden" value="<?php echo isset($employee['check_tfn_type']) ? htmlspecialchars($employee['check_tfn_type']) : 'tfn_number'; ?>"
                                                                   name="check_tfn_type" class="check_tfn_type">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="section-wrap">
                                                    <div class="form-row">
                                                        <div class="checkbox-group col-md-12">
                                                            <p>Have you changed your surname since you last dealt with the Australian Tax Office?</p>

                                                            <div class="tab">
                                                                <a class="tablinks tablinks_surname <?php echo (isset($employee['have_surname_changed']) && $employee['have_surname_changed'] == 'yesChanged') ? 'active' : ''; ?>"
                                                                   onclick="openThisTab(event, 'yesChanged','surname')">Yes</a>
                                                                <a class="tablinks tablinks_surname <?php echo (!isset($employee['have_surname_changed']) || $employee['have_surname_changed'] == '' || $employee['have_surname_changed'] == 'noChanged') ? 'active' : ''; ?>"
                                                                   onclick="openThisTab(event, 'noChanged','surname')">No</a>
                                                            </div>

                                                            <!-- Tab content -->
                                                            <div id="yesChanged" class="tabcontent tabcontent_surname"
                                                                <?php echo (isset($employee['have_surname_changed']) && $employee['have_surname_changed'] == 'yesChanged') ? 'style="display:block;"' : ''; ?>>
                                                                <div class="form-row">
                                                                    <div class="form-group col-md-6">
                                                                        <label for="previous_surname" class="form-label">Enter Previous Surname:</label>
                                                                        <input type="text" class="form-control" id="previous_surname" name="previous_surname"
                                                                               value="<?php echo (isset($employee['previous_surname']) && $employee['previous_surname'] != 'noChanged') ? htmlspecialchars($employee['previous_surname']) : ''; ?>" autocomplete="off">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div id="noChanged" class="tabcontent tabcontent_surname"
                                                                <?php echo (isset($employee['have_surname_changed']) && $employee['have_surname_changed'] == 'noChanged') ? 'style="display:block;"' : ''; ?>>
                                                            </div>

                                                            <input type="hidden" value="<?php echo isset($employee['have_surname_changed']) ? htmlspecialchars($employee['have_surname_changed']) : ''; ?>"
                                                                   name="have_surname_changed" class="previous_surname_changed">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="section-wrap">
                                                    <div class="form-row">
                                                        <div class="checkbox-group col-md-12">
                                                            <p>Are you an Australian resident for tax purposes or a working holiday maker?</p>

                                                            <label>
                                                                <input type="radio" value="australian" name="resident_type"
                                                                    <?php echo (isset($employee['resident_type']) && $employee['resident_type'] == 'australian') ? 'checked' : ''; ?>>
                                                                Australian resident for tax purposes
                                                            </label><br>
                                                            <label>
                                                                <input type="radio" value="foreign" name="resident_type"
                                                                    <?php echo (isset($employee['resident_type']) && $employee['resident_type'] == 'foreign') ? 'checked' : ''; ?>>
                                                                Foreign resident
                                                            </label><br>
                                                            <label>
                                                                <input type="radio" value="working_holiday" name="resident_type"
                                                                    <?php echo (isset($employee['resident_type']) && $employee['resident_type'] == 'working_holiday') ? 'checked' : ''; ?>>
                                                                Working holiday maker
                                                            </label>
                                                            <span class="fieldError" id="resident_type_error"></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="section-wrap">
                                                    <div class="form-row">
                                                        <div class="checkbox-group col-md-12">
                                                            <p>Do you have any of the following outstanding debts or loans?</p>

                                                            <label>
                                                                <input type="radio" value="higher_education" name="loan_type"
                                                                    <?php echo (isset($employee['loan_type']) && $employee['loan_type'] == 'higher_education') ? 'checked' : ''; ?>>
                                                                Higher Education Loan Program (HELP)
                                                            </label><br>
                                                            <label>
                                                                <input type="radio" value="vet_student" name="loan_type"
                                                                    <?php echo (isset($employee['loan_type']) && $employee['loan_type'] == 'vet_student') ? 'checked' : ''; ?>>
                                                                VET Student Loan (VSL)
                                                            </label><br>
                                                            <label>
                                                                <input type="radio" value="financial_supplement" name="loan_type"
                                                                    <?php echo (isset($employee['loan_type']) && $employee['loan_type'] == 'financial_supplement') ? 'checked' : ''; ?>>
                                                                Financial Supplement (FS)
                                                            </label><br>
                                                            <label>
                                                                <input type="radio" value="student_loan" name="loan_type"
                                                                    <?php echo (isset($employee['loan_type']) && $employee['loan_type'] == 'student_loan') ? 'checked' : ''; ?>>
                                                                Student Start-up Loan (SSL)
                                                            </label><br>
                                                            <label>
                                                                <input type="radio" value="trade_loan" name="loan_type"
                                                                    <?php echo (isset($employee['loan_type']) && $employee['loan_type'] == 'trade_loan') ? 'checked' : ''; ?>>
                                                                Trade Support Loan (TSL)
                                                            </label>
                                                            <span class="fieldError" id="loan_type_error"></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="section-wrap">
                                                    <div class="form-row">
                                                        <div class="checkbox-group col-md-12">
                                                            <p>Would you like to claim the tax-free threshold from this payer?</p>

                                                            <label>
                                                                <input type="radio" value="yes" name="claim_tax_free"
                                                                    <?php echo (isset($employee['claim_tax_free']) && $employee['claim_tax_free'] == 'yes') ? 'checked' : ''; ?>>
                                                                Yes
                                                            </label>
                                                            <label>
                                                                <input type="radio" value="no" name="claim_tax_free"
                                                                    <?php echo (isset($employee['claim_tax_free']) && $employee['claim_tax_free'] == 'no') ? 'checked' : ''; ?>>
                                                                No
                                                            </label>
                                                            <span class="fieldError" id="claim_tax_free_error"></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="section-wrap">
                                                    <div class="form-row">
                                                        <div class="checkbox-group col-md-12">
                                                            <p>On what basis are you paid?</p>

                                                            <label>
                                                                <input type="radio" value="full_time" name="job_type"
                                                                    <?php echo (isset($employee['job_type']) && $employee['job_type'] == 'full_time') ? 'checked' : ''; ?>>
                                                                Full-time
                                                            </label><br>
                                                            <label>
                                                                <input type="radio" value="part_time" name="job_type"
                                                                    <?php echo (isset($employee['job_type']) && $employee['job_type'] == 'part_time') ? 'checked' : ''; ?>>
                                                                Part-time
                                                            </label><br>
                                                            <label>
                                                                <input type="radio" value="labour_hire" name="job_type"
                                                                    <?php echo (isset($employee['job_type']) && $employee['job_type'] == 'labour_hire') ? 'checked' : ''; ?>>
                                                                Labour Hire
                                                            </label><br>
                                                            <label>
                                                                <input type="radio" value="superannuation" name="job_type"
                                                                    <?php echo (isset($employee['job_type']) && $employee['job_type'] == 'superannuation') ? 'checked' : ''; ?>>
                                                                Superannuation or annuity income stream
                                                            </label><br>
                                                            <label>
                                                                <input type="radio" value="casual" name="job_type"
                                                                    <?php echo (isset($employee['job_type']) && $employee['job_type'] == 'casual') ? 'checked' : ''; ?>>
                                                                Casual
                                                            </label>
                                                            <span class="fieldError" id="job_type_error"></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!--button-->
                                                <input type="button" value="SAVE" rel="superAnnuation" name="contact_submit" id="save_continue_tax" class="btn btn-success btn-ph">
                                            </form>
                                        </div>

                                        <!-- Super Annuation section remains the same -->
                                        <div class="tab-pane <?php echo $onbFirstTab === 'superAnnuation' ? 'active' : ''; ?>" id="superAnnuation" role="superAnnuation">
                                            <!-- Super Annuation content remains the same -->
                                            <form role="form" id="annuationDetailsForm" method="post" action="" enctype="multipart/form-data">
                                                <div class="alert alert-success border-0 shadow d-none" role="alert">Details have been saved successfully</div>

                                                <input type="hidden" name="emp_id" value="<?php echo isset($employee['emp_id']) ? htmlspecialchars($employee['emp_id']) : ''; ?>">

                                                <div class="section-wrap">
                                                    <div class="checkbox-group col-md-12">
                                                        <p class="fw-bold">Do you have an existing superannuation account?</p>

                                                        <div class="tab">
                                                            <a class="tablinks tablinks_superAnnuation <?php echo (isset($employee['nominatedByEmployer']) && $employee['nominatedByEmployer'] == 0) ? 'active' : ''; ?>"
                                                               onclick="openThisTab(event, 'YesSuper','superAnnuation')">Yes</a>
                                                            <a class="tablinks tablinks_superAnnuation <?php echo (isset($employee['nominatedByEmployer']) && $employee['nominatedByEmployer'] == 1) ? 'active' : ''; ?>"
                                                               onclick="openThisTab(event, 'NoSuper','superAnnuation')">No</a>
                                                            <input type="hidden" value="<?php echo isset($employee['check_super_type']) ? htmlspecialchars($employee['check_super_type']) : ''; ?>"
                                                                   name="check_super_type" class="check_super_type">
                                                        </div>
                                                    </div>

                                                    <div id="YesSuper" class="tabcontent tabcontent_superAnnuation">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <p>Your details</p>
                                                            </div>

                                                            <div class="col-lg-3 col-md-6 col-sm-12">
                                                                <label for="pdf_name" class="control-label">Name:</label>
                                                                <input type="text" id="pdf_name" class="form-control required" name="pdf_first_name"
                                                                       value="<?php echo isset($employee['pdf_first_name']) ? htmlspecialchars($employee['pdf_first_name']) : ''; ?>" autocomplete="off">
                                                                <span class="fieldError" id="pdf_name_error"></span>
                                                            </div>

                                                            <div class="col-lg-3 col-md-6 col-sm-12">
                                                                <label for="pdf_emp_id_no" class="control-label">Employee identification number (if applicable)</label>
                                                                <input type="text" id="pdf_emp_id_no" class="form-control" name="pdf_emp_id_no"
                                                                       value="<?php echo isset($employee['pdf_emp_id_no']) ? htmlspecialchars($employee['pdf_emp_id_no']) : ''; ?>" autocomplete="off">
                                                            </div>

                                                            <div class="col-lg-3 col-md-6 col-sm-12">
                                                                <label for="pdf_apra_fund_abh" class="control-label">Fund ABN</label>
                                                                <input type="text" id="pdf_apra_fund_abh" class="form-control required" name="pdf_apra_fund_abh"
                                                                       value="<?php echo isset($employee['pdf_apra_fund_abh']) ? htmlspecialchars($employee['pdf_apra_fund_abh']) : ''; ?>" autocomplete="off">
                                                                <span class="fieldError" id="pdf_apra_fund_abh_error"></span>
                                                            </div>

                                                            <div class="col-lg-3 col-md-6 col-sm-12">
                                                                <label for="pdf_apra_fund_name" class="control-label">Fund name</label>
                                                                <input type="text" id="pdf_apra_fund_name" class="form-control required" name="pdf_apra_fund_name"
                                                                       value="<?php echo isset($employee['pdf_apra_fund_name']) ? htmlspecialchars($employee['pdf_apra_fund_name']) : ''; ?>" autocomplete="off">
                                                                <span class="fieldError" id="pdf_apra_fund_name_error"></span>
                                                            </div>

                                                            <div class="col-lg-3 col-md-6 col-sm-12">
                                                                <label for="pdf_apra_fund_usi" class="control-label">Unique superannuation identifier (USI)<span>*</span></label>
                                                                <input type="text" id="pdf_apra_fund_usi" class="form-control required" name="pdf_apra_fund_usi"
                                                                       value="<?php echo isset($employee['pdf_apra_fund_usi']) ? htmlspecialchars($employee['pdf_apra_fund_usi']) : ''; ?>" autocomplete="off">
                                                                <span class="fieldError" id="pdf_apra_fund_usi_error"></span>
                                                            </div>

                                                            <div class="col-lg-3 col-md-6 col-sm-12">
                                                                <label for="pdf_apra_fund_member_no" class="control-label">Your member number (if applicable)</label>
                                                                <input type="text" id="pdf_apra_fund_member_no" class="form-control" name="pdf_apra_fund_member_no"
                                                                       value="<?php echo isset($employee['pdf_apra_fund_member_no']) ? htmlspecialchars($employee['pdf_apra_fund_member_no']) : ''; ?>" autocomplete="off">
                                                                <span class="fieldError" id="pdf_apra_fund_member_no_error"></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div id="NoSuper" class="tabcontent tabcontent_superAnnuation select_nominatedByEmployer">
                                                        <input id="nominatedByEmployer" type="hidden"
                                                               value="<?php echo isset($employee['nominatedByEmployer']) ? htmlspecialchars($employee['nominatedByEmployer']) : ''; ?>"
                                                               name="nominatedByEmployer">

                                                        <div class="checkbox">
                                                            <label style="color:#000;">
                                                                <input type="checkbox"
                                                                    <?php echo (isset($employee['nominatedByEmployer']) && $employee['nominatedByEmployer'] == 1) ? 'checked' : ''; ?>
                                                                       value="1"
                                                                       id="select_nominatedByEmployer"
                                                                       onchange="document.getElementById('nominatedByEmployer').value = this.checked ? 1 : 0">
                                                                The super fund nominated by my employer.<span>*</span>
                                                            </label>
                                                            <span class="fieldError" id="nominatedByEmployer_error"></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <input type="button" rel="privacyPolicy" name="contact_submit" id="save_continue_annuation" class="btn btn-success btn-ph" value="SAVE →">
                                            </form>
                                        </div>

                                        <!-- Updated Policies section -->
                                        <div class="tab-pane <?php echo $onbFirstTab === 'privacyPolicy' ? 'active' : ''; ?>" id="privacyPolicy" role="privacyPolicy">
                                            <p class="fw-bold mb-4">
                                                You must read and agree to the company policies, staff induction and job description before submitting this form.
                                            </p>

                                            <form id="privacyDetailsForm" method="post" enctype="multipart/form-data">
                                                <input type="hidden" name="emp_id" value="<?php echo isset($employee['emp_id']) ? htmlspecialchars($employee['emp_id']) : ''; ?>">

                                                <div class="alert alert-success border-0 shadow d-none" role="alert">
                                                    Details have been saved successfully.
                                                </div>

                                                <div class="row g-4">
                                                    <!-- Staff Induction -->
                                                    <div class="col-lg-4">
                                                        <div class="card shadow-sm border-0 h-100">
                                                            <div class="card-body">
                                                                <h5 class="fw-bold text-dark mb-3">
                                                                    <i class="fas fa-users me-2 text-primary"></i>
                                                                    Staff Induction Manual
                                                                </h5>

                                                                <div class="mb-3">
                                                                    <a class="btn btn-success btn-sm"
                                                                       href="<?php echo base_url() . 'uploaded_files/' . $this->session->userdata('tenantIdentifier') . '/HR/JobDescr/job_10114.pdf'; ?>"
                                                                       target="_blank">
                                                                        <i class="fas fa-eye me-1"></i> View Document
                                                                    </a>
                                                                </div>

                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="agree_terms_one"
                                                                           name="agree_terms_one" value="1"
                                                                        <?php echo (isset($employee['agree_terms_one']) && $employee['agree_terms_one'] == 1) ? 'checked' : ''; ?>>
                                                                    <label class="form-check-label" for="agree_terms_one">
                                                                        I have read, understood and agree to the Staff Induction Manual. <span class="text-danger">*</span>
                                                                    </label>
                                                                    <span class="fieldError text-danger small" id="agree_terms_one_error"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Policies & Procedures -->
                                                    <div class="col-lg-4">
                                                        <div class="card shadow-sm border-0 h-100">
                                                            <div class="card-body">
                                                                <h5 class="fw-bold text-dark mb-3">
                                                                    <i class="fas fa-file-alt me-2 text-primary"></i>
                                                                    Company Policies & Procedures
                                                                </h5>

                                                                <div class="mb-3">
                                                                    <a class="btn btn-success btn-sm"
                                                                       href="<?php echo base_url() . 'uploaded_files/' . $this->session->userdata('tenantIdentifier') . '/HR/JobDescr/job_10114.pdf'; ?>"
                                                                       target="_blank">
                                                                        <i class="fas fa-eye me-1"></i> View Document
                                                                    </a>
                                                                </div>

                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="agree_terms_two"
                                                                           name="agree_terms_two" value="1"
                                                                        <?php echo (isset($employee['agree_terms_two']) && $employee['agree_terms_two'] == 1) ? 'checked' : ''; ?>>
                                                                    <label class="form-check-label" for="agree_terms_two">
                                                                        I have read, understood and agree to the Company Policies and Procedures. <span class="text-danger">*</span>
                                                                    </label>
                                                                    <span class="fieldError text-danger small" id="agree_terms_two_error"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Job Description -->
                                                    <div class="col-lg-4">
                                                        <?php
                                                        if (isset($employee['job_desc']) && !empty($employee['job_desc']) && $employee['job_desc'] != '' && is_string($employee['job_desc']) && is_serialized($employee['job_desc'])) {
                                                            $fileNameArray = unserialize($employee['job_desc']);
                                                            if (is_array($fileNameArray) && !empty($fileNameArray)) {
                                                                $fileName = $fileNameArray[0];
                                                                if (!empty($fileName)) {
                                                                    $file_parts = pathinfo($fileName);
                                                                    ?>
                                                                    <div class="card shadow-sm border-0 h-100">
                                                                        <div class="card-body">
                                                                            <h5 class="fw-bold text-dark mb-3">
                                                                                <i class="fas fa-briefcase me-2 text-primary"></i>
                                                                                Job Description
                                                                            </h5>

                                                                            <div class="mb-3">
                                                                                <a class="btn btn-success btn-sm"
                                                                                   href="<?php echo base_url() . 'uploaded_files/' . $this->session->userdata('tenantIdentifier') . '/HR/JobDescr/' . htmlspecialchars($fileName); ?>"
                                                                                   target="_blank">
                                                                                    <i class="fas fa-eye me-1"></i> View Document
                                                                                </a>
                                                                            </div>

                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                       id="agree_terms_three" name="agree_terms_three" value="1"
                                                                                    <?php echo (isset($employee['agree_terms_three']) && $employee['agree_terms_three'] == 1) ? 'checked' : ''; ?>>
                                                                                <label class="form-check-label" for="agree_terms_three">
                                                                                    I have read, understood and agree to the Job Description. <span class="text-danger">*</span>
                                                                                </label>
                                                                                <span class="fieldError text-danger small" id="agree_terms_three_error"></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>

                                                <div class="text-end mt-4">
                                                    <button type="button" id="save_continue_privacy" class="btn btn-success px-5">
                                                        <i class="fas fa-save me-1"></i> Save
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div><!--  end col -->

                                <!-- Unavailability Tab -->
                                <div class="tab-pane fade" id="v-pills-unavailability" role="tabpanel" aria-labelledby="v-pills-unavailability-tab">
                                    <?php
                                    $savedWeekly = [];
                                    if (!empty($availability) && isset($availability[0]['weekly_json'])) {
                                        $savedWeekly = json_decode($availability[0]['weekly_json'], true);
                                    }
                                    $sameHours = (isset($availability[0]) && isset($availability[0]['same_hours'])) ? $availability[0]['same_hours'] : 0;
                                    
                                    $days = [
                                        "mon" => "Monday",
                                        "tue" => "Tuesday",
                                        "wed" => "Wednesday",
                                        "thu" => "Thursday",
                                        "fri" => "Friday",
                                        "sat" => "Saturday",
                                        "sun" => "Sunday",
                                    ];
                                    ?>
                                    
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <h5 class="mb-4 text-black fw-bold">My Availability</h5>
                                            
                                            <form id="availabilityForm">
                                                <input type="hidden" name="emp_id" value="<?= $employee['emp_id'] ?? '' ?>">

                                                <!-- SAME HOURS SWITCH -->
                                                <div class="form-check form-switch mb-4">
                                                    <input class="form-check-input" type="checkbox" id="sameHours" name="same_hours"
                                                           <?= $sameHours ? "checked" : "" ?>>
                                                    <label class="form-check-label fw-semibold">Same hours for all days</label>
                                                </div>

                                                <!-- SAME HOURS BLOCK -->
                                                <div id="sameHoursBlock" class="<?= $sameHours ? '' : 'd-none' ?> mb-4">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Start Time</label>
                                                            <input type="text" class="form-control timepicker"
                                                                   id="same_start" name="same_start"
                                                                   value="<?= $sameHours ? ($savedWeekly['mon']['start'] ?? '') : '' ?>">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">End Time</label>
                                                            <input type="text" class="form-control timepicker"
                                                                   id="same_end" name="same_end"
                                                                   value="<?= $sameHours ? ($savedWeekly['mon']['end'] ?? '') : '' ?>">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- DAY WISE TABLE -->
                                                <div id="daysBlock" class="<?= $sameHours ? 'd-none' : '' ?>">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered align-middle">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Day</th>
                                                                    <th width="200">Start Time</th>
                                                                    <th width="200">End Time</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($days as $key => $label): ?>
                                                                <tr>
                                                                    <td class="fw-semibold"><?= $label ?></td>
                                                                    <td>
                                                                        <input type="text" class="form-control timepicker"
                                                                            name="weekly[<?= $key ?>][start]"
                                                                            value="<?= $savedWeekly[$key]['start'] ?? '' ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control timepicker"
                                                                            name="weekly[<?= $key ?>][end]"
                                                                            value="<?= $savedWeekly[$key]['end'] ?? '' ?>">
                                                                    </td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                <!-- SAVE BUTTON -->
                                                <div class="mt-4">
                                                    <button type="submit" id="saveAvailabilityBtn" class="btn btn-primary">
                                                        <i class="fas fa-save me-1"></i>
                                                        <span class="btn-text">Save Availability</span>
                                                        <span class="spinner-border spinner-border-sm d-none ms-1" id="availabilityLoader"></span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade requestLeave" tabindex="-1" role="dialog" aria-labelledby="requestLeaveModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="requestLeaveModalLabel">Request Leave</h5>

                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-success shadow successleaveRequest" role="alert" style="display:none">
                                    Leave request sent succesfully.
                                </div>
                                <div class="alert alert-danger shadow mb-xl-0 dangerEmployee" role="alert" style="display:none"></div>
                                <form  role="form" id="newLeaveRequest" method="post" action="" enctype="multipart/form-data">
                                    <input type="hidden" name="emp_id" value="<?php echo $employee['emp_id']; ?>">
                                    <div class="row">
                                        <div class="mb-3 col-lg-6 col-md-6 col-sm-12">
                                            <label for="customer-name" class="col-form-label">Start Date:</label>
                                            <input type="text" name="start_date" class="form-control flatpickr-input active" data-provider="flatpickr" data-date-format="d M, Y" readonly="readonly">
                                        </div>
                                        <div class="mb-3 col-lg-6 col-md-6 col-sm-12">
                                            <label for="customer-name" class="col-form-label">End Date:</label>
                                            <input type="text" name="end_date" class="form-control flatpickr-input active" data-provider="flatpickr" data-date-format="d M, Y" readonly="readonly">
                                        </div>
                                        <div class="mb-3 col-lg-6 col-md-6 col-sm-12">
                                            <label for="leaveType" class="form-label">Leave Type</label>
                                            <select class="form-select mb-3" name="leave_type" id="leaveType">
                                                <option value=""> Select Leave Type</option>
                                                <?php if(isset($leaveTypes) && $leaveTypes) { ?>
                                                    <?php foreach($leaveTypes as $leaveType) {  ?>
                                                        <option value="<?php echo $leaveType['id'] ?>"><?php echo $leaveType['leaveTypeName'] ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="mb-3 col-lg-6 col-md-6 col-sm-12 sickcertificateAttachment d-none">
                                            <label for="leaveType" class="form-label">Medical Certificate</label>
                                            <input type="file" id="sickleaveattachment" name="userfile[]" class="form-control" multiple>
                                        </div>

                                        <div class="mb-3 col-lg-6 col-md-6 col-sm-12">
                                            <div>
                                                <label for="leaveComments" class="form-label">Comment</label>
                                                <textarea class="form-control" id="leaveComments" name="leaveComments" rows="3"></textarea>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <a href="javascript:void(0);" class="btn btn-link link-success shadow-none fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                                <button type="button" class="btn btn-primary leaveRequestBtn" onclick="requestLeave()">Request Leave</button>
                            </div>
                        </div>
                    </div>
              
               
                </div>
               
                <script>





                    $("#leaveListTable").DataTable({
                        lengthChange: false,
                        pageLength: 100,
                    });
                    function activaTab(formId){

                        $("#"+formId).find(".alert").removeClass('d-none');
                    };
                    $(document).ready(function() {

                        $("#leaveType").on('change',function(){

                            let selectedOptionHTML = $('#leaveType option:selected')[0].outerHTML;
                            let regex = /Sick Leave/i;
                            console.log("nn",selectedOptionHTML)
                            // in case of sick leave attachment is must
                            if(regex.test(selectedOptionHTML)){
                                $(".sickcertificateAttachment").removeClass("d-none");
                            }else{
                                $(".sickcertificateAttachment").addClass("d-none");
                            }
                        })
                        if($(".check_super_type").val() == 'yes'){
                            $(".select_nominatedByEmployer").hide();
                        }
                        let nominatedByEmployer = '<?php echo $employee['nominatedByEmployer']; ?>';
                        let check_tfn_type = '<?php echo $employee['check_tfn_type']; ?>';
                        let have_surname_changed = '<?php echo $employee['have_surname_changed']; ?>';
                        let stepsCompleted = '<?php echo $employee['stepsCompleted']; ?>';

                        if(nominatedByEmployer == 0){
                            $("#YesSuper").show();
                            $("#NoSuper").hide();
                        }else{
                            $("#YesSuper").hide();
                            $("#NoSuper").show();
                        }

                        if(check_tfn_type == 'tfn_number'){
                            $("#Yes").show();
                            $("#No").hide();
                        }else{
                            $("#Yes").hide();
                            $("#No").show();
                        }

                        if(have_surname_changed =='noChanged'){
                            $("#noChanged").show();
                            $("#yesChanged").hide();
                        }else{
                            $("#noChanged").hide();
                            $("#yesChanged").show();
                        }


                        $('#save_continue_personal').click(function(e){
                            let err=0;

                            if($('#first_name').val() == ''){ $('#first_name_error').html('Please enter first name');err=1;}
                            if($('#last_name').val() == ''){ $('#last_name_error').html('Please enter last name');err=1;}
                            if($('#email').val() == ''){ $('#email_error').html('Please enter email address'); err=1; }
                            if($('#phone').val() == ''){ $('#phone_error').html('Please enter phone number'); err=1; }
                            if($('#dob').val() == ''){ $('#dob_error').html('Please enter date of birth'); err=1; }

                            if($('#street_name').val() == ''){ $('#streetname_error').html('Please enter street name'); err=1; }

                            if($('#state').val() == ''){ $('#state_error').html('Please select state'); err=1; }
                            if($('#suburb').val() == ''){ $('#suburb_error').html('Please enter suburb'); err=1; }
                            if($('#postcode').val() == ''){ $('#postcode_error').html('Please enter postcode'); err=1; }

                            if(err == '0'){
                                let btnsave = $('#save_continue_personal');
                                btnsave.val("Saving data...");
                                let data1 = $('#personalDetailsForm').serialize();
                                $.ajax({
                                    type: "POST",
                                    enctype: 'multipart/form-data',
                                    url: '/HR/Employee/submitOnboardingProcessForEmployee',
                                    data: data1,
                                    success: function(response){
                                        let res = JSON.parse(response)
                                        console.log("res",res);
                                        if(res?.status=='success'){
                                            activaTab('personalDetailsForm');
                                            btnsave.val("SAVE →");
                                        }
                                        else{
                                            alert("Some error occured,Please refresh page and try again.")
                                        }
                                    }
                                }); e.preventDefault();
                            }
                            else{ alert('Please enter all the mandatory fields'); return false; }
                        });

                        // WorkDetail submit

                        $('#save_continue_work').click(function(e){

                            if($('#employee_type').val() == ''){ $('#employee_type_error').html('Please enter employee type');err=1;}

                            let positionIdToRemove = localStorage.getItem("positionIdsToRemove")
                            $(".allPositionIdsToRemove").val(positionIdToRemove);
                            $('#save_continue_work').val("Saving...");
                            let data1 = $('#workDetailsForm').serialize();
                            $.ajax({
                                type: "POST",
                                enctype: 'multipart/form-data',
                                url: '/HR/Employee/submitOnboardingProcessForEmployee',
                                data: data1,
                                success: function(response){
                                    let res = JSON.parse(response)
                                    if(res?.status=='success'){
                                        addHiddenInputFields(res?.positionAddedDetails);
                                        localStorage.removeItem("positionIdsToRemove")
                                        $('#save_continue_work').val("SAVE →");
                                        activaTab('workDetailsForm');
                                    }
                                    else{
                                        alert("Some error occured,Please refresh page and try again.")
                                    }
                                }
                            });

                        });

// 	emergency form submit
                        $('#save_continue_emergency').click(function(e){
                            console.log("Clicked");
                            $('#save_continue_emergency').val("Saving...");
                            let data1 = $('#emergencyDetailsForm').serialize();
                            $.ajax({
                                type: "POST",
                                enctype: 'multipart/form-data',
                                url: '/HR/Employee/submitOnboardingProcessForEmployee',
                                data: data1,
                                success: function(response){
                                    let res = JSON.parse(response)
                                    if(res?.status=='success'){
                                        $('#save_continue_emergency').val("SAVE →");
                                        activaTab('emergencyDetailsForm');
                                    }
                                    else{
                                        alert("Some error occured,Please refresh page and try again.")
                                    }
                                }
                            });

                        });

                        // 	bank form submit
                        $('#save_continue_bank').click(function(e){
                            var err=0;
                            $('.fieldError').html('');

                            $('#bankDetailsForm').find('.required').each(function() {

                                if($(this).val() == "") {
                                    err = 1;
                                    let fieldID=$(this).attr('id');
                                    $('#'+fieldID+'_error').html('This field should not be empty');
                                }
                            });

                            if(err == '0'){
                                $('#save_continue_bank').val("Saving...");
                                let data1 = $('#bankDetailsForm').serialize();
                                $.ajax({
                                    type: "POST",
                                    enctype: 'multipart/form-data',
                                    url: '/HR/Employee/submitOnboardingProcessForEmployee',
                                    data: data1,
                                    success: function(response){
                                        let res = JSON.parse(response)
                                        if(res?.status=='success'){
                                            $('#save_continue_bank').val("SAVE →");
                                            activaTab('bankDetailsForm');
                                        }
                                        else{
                                            alert("Some error occured,Please refresh page and try again.")
                                        }
                                    }
                                }); e.preventDefault();
                            }
                            else{ alert('Please enter all the mandatory fields'); return false; }
                        });


                        // 	tax form submit
                        $('input[name=resident_type]').on("change",function(){
                            if($(this).val() == 'working_holiday'){
                                $(".countryOfOrigin").css("display","block");
                            }else{
                                $(".countryOfOrigin").css("display","none");
                            }
                        })
                        $('#save_continue_police').click(function(e){
                            var err=0;
                            $('.fieldError').html('');
                            let data1 = new FormData(document.getElementById("policeDetailsForm"));
                            if($("#policeFileInput")[0].files.length === 0 && !<?php echo (isset($employee['police_certificate']) && !empty($employee['police_certificate'])) ? 'true' : 'false'; ?>)
                            {
                                err=1;
                                alert('Please upload police clearance certificate.');
                            }

                            if(err == '0'){
                                $('#save_continue_police').val("Saving...");

                                let formData = new FormData($("#policeDetailsForm")[0]);
                                $.ajax({
                                    type: "POST",
                                    url: '/HR/Employee/submitOnboardingProcessForEmployee',
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    success: function(response){
                                        let res = JSON.parse(response)
                                        if(res?.status=='success'){
                                            $('#save_continue_police').val("SAVE →");
                                            activaTab('policeDetailsForm');
                                        }
                                        else{
                                            alert("Some error occured,Please refresh page and try again.")
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        console.error(error);
                                    }
                                });
                                e.preventDefault();
                            }
                            else{ return false; }
                        });

                        // Update tax form to point to superAnnuation instead of policeClearance
                        $('#save_continue_tax').click(function(e){
                            if($(".check_tfn_type ").val() == 'tfn_number' && $("#tfn_number").val() == ''){
                                alert('Please enter TFN number.');
                                $("#tfn_number_error").html('Please Enter TFN Number');
                                return false;
                            }

                            if($('input[name=resident_type]:checked').val() == 'working_holiday' && $("#origin_country").val() == ''){
                                alert('Please enter  your native country');
                                $("#resident_type_error").html('Please enter  your native country');
                                return false;
                            }
                            var err=0;

                            var allradio_tax = [];

                            $('#taxDetailsForm').find('input[type="radio"]').each(function() {
                                var fieldName=$(this).attr('name');

                                if(fieldName !='loan_type' && fieldName != 'tfn_type'){
                                    if(!allradio_tax.includes(fieldName)){
                                        if (!$('input[name='+fieldName+']:checked').length > 0) {

                                            $('#'+fieldName+'_error').html('This field should not be empty');
                                            err=1;
                                        }
                                    }

                                    allradio_tax.push(fieldName);
                                }
                            });

                            if(err == '0'){
                                $('#save_continue_tax').val("Saving...");
                                let data1 = $('#taxDetailsForm').serialize();
                                $.ajax({
                                    type: "POST",
                                    enctype: 'multipart/form-data',
                                    url: '/HR/Employee/submitOnboardingProcessForEmployee',
                                    data: data1,
                                    success: function(response){
                                        let res = JSON.parse(response)
                                        if(res?.status=='success'){
                                            $('#save_continue_tax').val("SAVE →");
                                            activaTab('taxDetailsForm');
                                        }
                                        else{
                                            alert("Some error occured,Please refresh page and try again.")
                                        }
                                    }
                                }); e.preventDefault();
                            }
                            else{ alert('Please enter all the mandatory fields'); return false; }
                        });

                        // Update annuation form to point to privacyPolicy
                        $('#save_continue_annuation').click(function(e){
                            $('.fieldError').html('');
                            var err=0;
                            if($(".check_super_type").val() == 'no'){
                                if($('#select_nominatedByEmployer').is(":checked")){}else{ $('#nominatedByEmployer_error').html('Please check the checkbox'); err=1; }
                            }

                            if(err == '0'){
                                $('#save_continue_annuation').val("Saving...");
                                let data1 = $('#annuationDetailsForm').serialize();
                                $.ajax({
                                    type: "POST",
                                    enctype: 'multipart/form-data',
                                    url: '/HR/Employee/submitOnboardingProcessForEmployee',
                                    data: data1,
                                    success: function(response){
                                        let res = JSON.parse(response)
                                        if(res?.status=='success'){
                                            $('#save_continue_annuation').val("SAVE →");
                                            activaTab('annuationDetailsForm');
                                        }
                                        else{
                                            alert("Some error occured,Please refresh page and try again.")
                                        }
                                    }
                                }); e.preventDefault();
                            }
                            else{
                                alert('Please enter all the mandatory fields');
                                return false;
                            }
                        });



                        $('#save_continue_privacy').click(function(){
                            let err=0;

                            if($('#agree_terms_one').is(":checked")){}else{ $('#agree_terms_one_error').html('Please agree the terms'); err=1; }
                            if($('#agree_terms_two').is(":checked")){}else{ $('#agree_terms_two_error').html('Please agree the terms'); err=1; }
                            if ($('#agree_terms_three').is(":visible")) {
                                if($('#agree_terms_three').is(":checked")){}else{ $('#agree_terms_three_error').html('Please agree the terms'); err=1; }
                            }
                            if(err == '0'){
                                $('#save_continue_privacy').val("Saving...");
                                let data1 = $('#privacyDetailsForm').serialize();
                                $.ajax({
                                    type: "POST",
                                    enctype: 'multipart/form-data',
                                    url: '/HR/Employee/submitOnboardingProcessForEmployee',
                                    data: data1,
                                    success: function(response){
                                        let res = JSON.parse(response)
                                        if(res?.status=='success'){
                                            $('#save_continue_privacy').val("SAVE →");
                                            activaTab('privacyDetailsForm');
                                        }
                                        else{
                                            alert("Some error occured,Please refresh page and try again.")
                                        }
                                    }

                                });
                            }
                            else{
                                alert('Please enter all the mandatory fields');
                                return false;
                            }

                        });

                        function validate(evt) {
                            var theEvent = evt || window.event;
                            var key = theEvent.keyCode || theEvent.which;
                            key = String.fromCharCode( key );
                            var regex = /[0-9]|\./;
                            if( !regex.test(key) ) {
                                theEvent.returnValue = false;
                                if(theEvent.preventDefault) theEvent.preventDefault();
                            }
                        }

                    });

                    function openThisTab(evt, selected_value,fieldname) {

                        let i, tabcontent, tablinks;

                        tabcontent = document.getElementsByClassName("tabcontent_"+fieldname);
                        for (i = 0; i < tabcontent.length; i++) {
                            tabcontent[i].style.display = "none";
                        }
                        tablinks = document.getElementsByClassName("tablinks_"+fieldname);
                        for (i = 0; i < tablinks.length; i++) {
                            tablinks[i].className = tablinks[i].className.replace(" active", "");
                        }
                        document.getElementById(selected_value).style.display = "block";


                        if(fieldname =='tfn'){
                            if(selected_value == 'No'){
                                $('.check_tfn_type').val('tfn_type');
                            }
                            else{
                                $('.check_tfn_type').val('tfn_number');
                            }
                        }

                        if(fieldname =='superAnnuation'){
                            if(selected_value == 'NoSuper'){
                                $('.check_super_type').val('no');
                                $('#nominatedByEmployer').val(1);
                            }
                            else{
                                $('.check_super_type').val('yes');
                                $('#nominatedByEmployer').val(0);
                            }
                        }

                        if(fieldname =='surname'){
                            if(selected_value == 'noChanged'){
                                $('#previous_surname').val(selected_value);
                            }
                            if(selected_value == 'noChanged' || selected_value == 'yesChanged'){
                                $('.previous_surname_changed').val(selected_value);
                            }
                        }
                        document.getElementById(selected_value).style.display = "block";
                        evt.currentTarget.className += " active";
                    }
                    function requestLeave(){
                        $('.leaveRequestBtn').html("Loading...");
                        let formData = new FormData($("#newLeaveRequest")[0]);
                        $.ajax({
                            type: "POST",
                            url: '/HR/Leave/requestLeave',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response){
                                $(".successleaveRequest").show();
                                $('.leaveRequestBtn').html("Request Leave");

                                $(".requestLeave").modal('hide');
                            },
                            error: function (xhr, status, error) {
                                console.error(error);
                            }
                        });
                    }
                    function cancelLeave(obj,leaveId){
                        $(obj).html('Cancelling...');
                        $.ajax({
                            type: "POST",
                            "url" : "/HR/Leave/cancelLeave",
                            data:'id='+leaveId,
                            success: function(data){
                                $("tr[data-delete-id='" + leaveId + "']").remove();
                                $(obj).html('Cancelled');
                            }
                        });
                    }


                    $(document).ready(function(){
                        $(document).on('click', '.add-Positionrow', function() {
                            let $positionMainRow = $(this).closest('.positionMainRow');
                            let clonedRow = $positionMainRow.clone();
                            clonedRow.find('.position_unique_id').remove(); // Remove the element with the class "position_unique_id" from the cloned row
                            $positionMainRow.after(clonedRow);
                        });




                        $(document).on('click', '.remove-Positionrow', function() {
                            if ($('.positionMainRow').length > 1) {
                                // Retrieve the existing array from localStorage
                                let storedPositionIds = JSON.parse(localStorage.getItem("positionIdsToRemove")) || [];
                                let positionIdToRemove = $(this).closest(".positionMainRow").find('input[name="position_unique_id[]"]').val();
                                storedPositionIds.push(positionIdToRemove);
                                localStorage.setItem("positionIdsToRemove", JSON.stringify(storedPositionIds));
                                $(this).closest('.positionMainRow').remove();
                            }
                        });

                    });

                    // adding updating multiple employee position

                    function addHiddenInputFields(data) {

                        $('.positionMainRow').each(function(index) {
                            var positionId = $(this).find('select[name="position_id[]"]').val();
                            var positionData = data.find(item => item.position_id == positionId);
                            if (positionData) {
                                // Check if the hidden input field already exists within this element
                                var hiddenInput = $(this).find('input[name="position_unique_id[]"]');

                                if (hiddenInput.length) {
                                    // If it exists, update its value
                                    hiddenInput.val(positionData.id);
                                } else {
                                    // If it doesn't exist, append a new hidden input field
                                    $(this).append('<input type="hidden" class="position_unique_id" name="position_unique_id[]" value="' + positionData.id + '">');
                                }
                            }
                        });
                    }

                </script>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const dropzone = document.getElementById('policeDropzone');
                        const fileInput = document.getElementById('policeFileInput');
                        const fileList = document.getElementById('fileList');

                        // Click anywhere on dropzone → open file picker
                        dropzone.addEventListener('click', () => fileInput.click());

                        // Change visual feedback on drag over
                        ['dragenter', 'dragover'].forEach(event => {
                            dropzone.addEventListener(event, e => {
                                e.preventDefault();
                                e.stopPropagation();
                                dropzone.classList.add('border-success', 'bg-success-subtle');
                            });
                        });

                        ['dragleave', 'drop'].forEach(event => {
                            dropzone.addEventListener(event, e => {
                                e.preventDefault();
                                e.stopPropagation();
                                dropzone.classList.remove('border-success', 'bg-success-subtle');
                            });
                        });

                        // Handle actual file drop
                        dropzone.addEventListener('drop', e => {
                            const files = e.dataTransfer.files;
                            if (files.length) {
                                fileInput.files = files;
                                showFileNames(files);
                            }
                        });

                        // When files are selected (via click or drop)
                        fileInput.addEventListener('change', () => {
                            if (fileInput.files.length) {
                                showFileNames(fileInput.files);
                            }
                        });

                        // Show selected file names
                        function showFileNames(files) {
                            fileList.innerHTML = '';
                            Array.from(files).forEach(file => {
                                const div = document.createElement('div');
                                div.className = 'alert alert-info py-2 px-3 mb-2 d-inline-block';
                                div.innerHTML = `<i class="fas fa-paperclip me-2"></i>${file.name} <small>(${(file.size/1024/1024).toFixed(2)} MB)</small>`;
                                fileList.appendChild(div);
                            });
                        }
                    });
                </script>

                <!--Employee Availability -->
                <script>
                    $(document).ready(function () {



                        // Initialize timepicker
                        $('.timepicker').timepicker({
                            timeFormat: 'h:mm p',
                            interval: 15,
                            dropdown: true,
                            scrollbar: true
                        });

                        // Set preloaded values from PHP
                        $('.timepicker').each(function () {
                            let v = $(this).val().trim();
                            if (v !== "") {
                                $(this).timepicker('setTime', v);
                            }
                        });

                        // SAME HOURS toggle
                        $("#sameHours").on("change", function () {
                            let chk = $(this).is(":checked");
                            $("#sameHoursBlock").toggleClass("d-none", !chk);
                            $("#daysBlock").toggleClass("d-none", chk);
                        });





                        // SUBMIT AVAILABILITY
                        $("#availabilityForm").submit(function(e) {
                            e.preventDefault();

                            let sameHoursChecked = $("#sameHours").is(":checked");

                            // If same hours → override weekly array
                            if (sameHoursChecked) {

                                let start = $("#same_start").val();
                                let end   = $("#same_end").val();

                                $("input[name^='weekly']").each(function() {
                                    if (this.name.includes('[start]')) $(this).val(start);
                                    if (this.name.includes('[end]')) $(this).val(end);
                                });
                            }

                            // Button loader
                            $("#saveAvailabilityBtn").prop("disabled", true);
                            $("#availabilityLoader").removeClass("d-none");
                            $(".btn-text").text("Saving...");

                            $.ajax({
                                url: "<?= base_url('HR/Employees/save_availability'); ?>",
                                type: "POST",
                                data: $(this).serialize(),
                                dataType: "json",
                                success: function(res) {
                                    $("#saveAvailabilityBtn").prop("disabled", false);
                                    $("#availabilityLoader").addClass("d-none");
                                    $(".btn-text").text("Save Availability");

                                    if (res.status === "success") {
                                        alert("Availability updated successfully");
                                    } else {
                                        alert(res.message || "Error updating availability");
                                    }
                                },
                                error: function() {
                                    $("#saveAvailabilityBtn").prop("disabled", false);
                                    $("#availabilityLoader").addClass("d-none");
                                    $(".btn-text").text("Save Availability");
                                    alert("Something went wrong.");
                                }
                            });
                        });

                    });
                </script>

