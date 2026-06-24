<div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                   <div class="row">
                       <div class="col-md-3 col-lg-2 col-xl-2">
                         <div class="card h-100">
                         <div class="card-body">    
                         <div class="nav flex-column nav-pills text-center nav-customd" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                          <a class="nav-link mb-4 fs-16" id="generalSettings-tab" data-bs-toggle="pill" href="#generalSettings" role="tab" aria-selected="false">General Config</a>    
                         <a class="nav-link mb-4 active fs-16" id="stressProfile-tab" data-bs-toggle="pill" href="#stressProfile" role="tab" aria-selected="true">Stress Profile</a>
                         <a class="nav-link mb-4 fs-16" id="emailSettings-tab" data-bs-toggle="pill" href="#emailSettings" role="tab"  aria-selected="false">Email</a>
                         <a class="nav-link mb-4 fs-16" id="docsSettings-tab" data-bs-toggle="pill" href="#docsSettings" role="tab" aria-selected="false">Documents</a>
                         <a class="nav-link mb-4 fs-16" id="leaveSettings-tab" data-bs-toggle="pill" href="#leaveSettings" role="tab" aria-selected="false">Leaves</a>
                         <a class="nav-link mb-4 fs-16" id="positionSettings-tab" data-bs-toggle="pill" href="#positionSettings" role="tab" aria-selected="false">Positions</a>
                         <a class="nav-link mb-4 fs-16" id="payrollTypeSettings-tab" data-bs-toggle="pill" href="#payrollTypeSettings" role="tab" aria-selected="false">Payroll Type</a>
                         <a class="nav-link mb-4 fs-16" id="superannuationSettings-tab" data-bs-toggle="pill" href="#superannuationSettings" role="tab" aria-selected="false">Superannuation</a>
                         <a class="nav-link mb-4 fs-16" id="onboardingTabsSettings-tab" data-bs-toggle="pill" href="#onboardingTabsSettings" role="tab" aria-selected="false">Onboarding Form Customize</a>

                        </div>
                        </div>
                        </div>
                        </div>
                          <div class="col-md-9 col-lg-2 col-xl-10">
                          <div class="card h-100">
                         <div class="card-body">      
                           <div class="tab-content text-black mt-4 mt-md-0" id="v-pills-tabContent">
                             <div class="tab-pane fade show active" id="stressProfile" role="tabpanel" aria-labelledby="stressProfile-tab">
                            <div class="row">
                            <div class="col"></div>  
                            <div class="col-auto text-right">    
                            <button type="button" class="btn btn-success w-auto mx-5" onclick="addNewStressProfile()"><i class="ri-user-add-fill"></i>  New Stress Profile</button>    
                            </div>  
                            </div>
                           <div class="row mt-2">  
                            <table id="stressProfileList" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                        <thead class="table-light">
                                            <tr>
                                                <th data-ordering="false">Name</th>
                                                <th data-ordering="false">Maximum Hrs Per Week</th>
                                                <th data-ordering="false">Maximum Hrs Per Day</th>
                                                <th data-ordering="false">Maximum hours per shift </th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                            <?php if(isset($allStressProfile) && $allStressProfile) {  ?>
                                            <?php foreach($allStressProfile as $stresprofile){ ?>
                                            <tr data-delete-id="<?php echo $stresprofile['id'] ?>">
                                                <td><?php echo $stresprofile['stressProfileName'] ?></td>
                                                <td><?php echo $stresprofile['maxHrsPWeek'] ?></td>
                                                <td><?php echo $stresprofile['maxHrsPDay'] ?></td>
                                                <td><?php echo $stresprofile['maxDaysPWeek'] ?></td>
                                                <td>
                                              <div class="d-inline-block">
                                                <button type="button" class="btn btn-success btn-icon waves-effect waves-light" onclick="editStressProfile(<?php echo $stresprofile['id'] ?>,'<?php echo $stresprofile['stressProfileName'] ?>',<?php echo $stresprofile['maxHrsPWeek'] ?>,<?php echo $stresprofile['maxHrsPDay'] ?>,<?php echo $stresprofile['maxDaysPWeek'] ?>)"><i class="ri-edit-box-line"></i></button>  
                                                <button type="button" class="btn btn-danger btn-icon waves-effect waves-light" onclick="deleteRow(<?php echo $stresprofile['id'] ?>,'HR_stressProfile')"><i class="ri-delete-bin-5-line"></i></button>
                                               </div>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            <?php } ?>
                                            </tbody>
                                            </table>
                           </div>  
                            </div> 
                             <div class="tab-pane fade" id="emailSettings" role="tabpanel" aria-labelledby="emailSettings-tab">
             <form  method="post" class="form-horizontal" id="emailSettingForm">
                  <div class="row">       
                   <div class="col-md-6 col-sm-12">
                  <label for="sort_order" class="form-label fw-semibold">Add Notification Email</label>
                 
               <table class="table table-bordered mt-3" id="notificationMailTable">
            <tbody>
                <?php   if(isset($mailConfigData) && !empty($mailConfigData)) {  ?>
                <?php foreach($mailConfigData as $emails) { ?>
                 <input type="hidden" name="configId[]" value="<?php echo $emails['id'] ?>">
                <?php $emailTo = unserialize($emails['data']);  ?>
                <?php foreach($emailTo as $emailId) { ?>
               <tr>
               <td>
             <select class="form-select notificationMailoptions" name="mailType[]">
             <option value="" >Select option</option>
             <option value="managerEmail" <?php echo  ((isset($emails['metaData']) && $emails['metaData'] == 'managerEmail'  ? 'selected'  : '')) ?>>Manager Email </option>
             </select>
             </td>
             <td class="gap-2 d-flex">
              <input required type="text" name="emailTo[]" class="form-control " value="<?php echo (isset($emailId) ? $emailId : ''); ?>" placeholder="Enter mail" autocomplete="off" />
              </td>
              <td><button class="btn btn-success add-row " type="button">+</button></td>
              <td><button type="button" class="btn btn-danger remove-row">-</button></td>
                </tr>
                 <?php } ?>
                <?php } ?>
               <?php      }  else {   ?>
             <tr>
                         <td>
            <select class="form-select notificationMailoptions" name="mailType[]">
             <option value="">Select option</option>
             <option value="managerEmail">Manager Email </option>
             </select>
                           </td>
                    <td class="gap-2 d-flex">
                    <input type="text" name="emailTo[]" class="form-control" placeholder="Enter mail" autocomplete="off" required />
                    </td>
                    <td><button class="btn btn-success add-row " type="button">+</button></td>
                </tr>  
               
               <?php  } ?>
               
            </tbody>
        </table> 
          
        <small>Add the email and Cc emails in comma seprated value,  who will receive the notification mail for this system . </small> 
        </div>  
                  </div> 
                  <div class="col-xxl-3 col-md-6 mt-2">
                                     <button class="btn btn-success saveEmailSettingsBtn"  type="button" onclick="saveEmailSettings()" >Save</button>
                                        </div>
                        </form>     
                                                   
                              </div>
                             <div class="tab-pane fade" id="docsSettings" role="tabpanel" aria-labelledby="docsSettings-tab">
                               
                               <div class="row">
                             <div class="col-lg-6 col-md-12">
                                  <small>* choose pdf,jpeg,jpg file and click upload button</small>
                               <form role="form" id="policiesForm" method="post" action="" enctype="multipart/form-data">
                                   <input type="hidden" name="file_type" value="policy">
                                 <label class="label-control">Policies</label>  
        				        <input type="file" id="policyfile" name="userfile[]" class="form-control" multiple>
        				       
	 <input type="button" class="btn btn-success uploadConfigFile" value="Upload" data-formId="policiesForm" data-inputId="policyfile">
	 
	 <?php if(isset($uploadedFiles) && !empty($uploadedFiles)){  ?>
    <?php foreach($uploadedFiles as $uploadedFile) {  ?>
  <?php if($uploadedFile['metaData'] == 'policy') { $attachment = unserialize($uploadedFile['data']);  ?>   
    <a href="<?php echo base_url().'uploaded_files/'.$this->tenantIdentifier.'/HR/OtherFiles/'.$attachment[0] ?>" target="_blank" class="btn btn-orange my-3">View Policy File</a>
     <?php } ?>
     <?php } ?>
   <?php } ?>
                				 </form>
                			 </div>
                			 
                			  <div class="col-lg-6 col-md-12">
                			       <small>* choose pdf,jpeg,jpg file and click upload button</small>
                               <form role="form" id="inductionFileForm" method="post" action="" enctype="multipart/form-data">
                                 <input type="hidden" name="file_type" value="induction">
                                 <label class="label-control">Induction File</label>  
        				        <input type="file" id="inductionfile" name="userfile[]" class="form-control" multiple>
						        <input type="button" data-formId="inductionFileForm" data-inputId="inductionfile" class="btn btn-success uploadConfigFile" value="Upload">
                				 
   <?php if(isset($uploadedFiles) && !empty($uploadedFiles)){ ?>
    <?php foreach($uploadedFiles as $uploadedFile) {  ?>
  <?php if($uploadedFile['metaData'] == 'induction') { $attachment = unserialize($uploadedFile['data']);  ?>   
  <?php if(isset($attachment) && !empty($attachment)){ ?>
    <a href="<?php echo base_url().'uploaded_files/'.$this->tenantIdentifier.'/HR/OtherFiles/'.$attachment[0] ?>" target="_blank" class="btn  btn-orange my-3">View Induction File</a>
     <?php } ?>
     <?php } ?>
     <?php } ?>
   <?php }else { ?>
    <p><small>Refresh page to view already upladed document</small></p>
    <?php } ?> 
    </form>
                			 </div>
                				 
                				 </div>
                                                   
                              </div>   
                             <div class="tab-pane fade" id="leaveSettings" role="tabpanel" aria-labelledby="leaveSettings-tab">
                            <div class="row">
                            <div class="col"></div>  
                            <div class="col-auto text-right">
                            <button type="button" class="btn btn-success w-auto mx-5" onclick="addNewLeave()"><i class="ri-user-add-fill"></i> Add New</button>    
                            </div>   
                            </div>  
                           <div class="row mt-2">  
                            <table id="leaveList" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                        <thead class="table-light">
                                            <tr>
                                                <th data-ordering="false">Leave Type Name</th>
                                                 <th data-ordering="false">Entitlements</th>
                                                <th data-ordering="false">Paid Leave</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                            <?php if(isset($allLeaveTypes) && $allLeaveTypes) {  ?>
                                            <?php foreach($allLeaveTypes as $allLeaveType){  ?>
                                            <tr data-delete-id="<?php echo $allLeaveType['id'] ?>">
                                                <td><?php echo $allLeaveType['leaveTypeName'] ?></td>
                                                <td><?php echo $allLeaveType['entitlements'] ?></td>
                                                <td><?php echo $allLeaveType['is_paid'] == '1' ? 'Yes' : 'No' ?></td>
                                             
                                                <td>
                                              <div class="d-inline-block">
                                                <button type="button" class="btn btn-success btn-icon waves-effect waves-light" onclick="editLeave(<?php echo $allLeaveType['id'] ?>,'<?php echo $allLeaveType['leaveTypeName'] ?>',<?php echo $allLeaveType['entitlements'] ?>,<?php echo $allLeaveType['is_paid'] ?>)"><i class="ri-edit-box-line"></i></button>  
                                                <button type="button" class="btn btn-danger btn-icon waves-effect waves-light" onclick="deleteRow(<?php echo $allLeaveType['id'] ?>,'HR_leaves')"><i class="ri-delete-bin-5-line"></i></button>
                                               </div>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            <?php } ?>
                                            </tbody>
                                            </table>
                           </div>  
                            </div> 
                            <div class="tab-pane fade" id="positionSettings" role="tabpanel" aria-labelledby="positionSettings-tab">
                             <form  method="post" class="form-horizontal" id="positionSettingsForm">
                            <div class="row">       
                          <div class="col-md-6 col-sm-12">
                         <label for="sort_order" class="form-label fw-semibold">Add Position Name</label>
                 
                      <table class="table table-bordered mt-3" id="notificationMailTable">
                     <tbody>
                   <?php   if(isset($positionConfigData) && !empty($positionConfigData)) {  ?>
                   <?php foreach($positionConfigData as $position) { ?>
                  <tr>
                <td class="gap-2 d-flex">
    <input type="hidden" name="position_id[]" value="<?php echo $position['position_id']; ?>">

    <input type="text" name="position_name[]" class="form-control" value="<?php echo $position['position_name']; ?>" />
     <select name="position_type[]" class="form-select ms-2" required>
        <option value="boh" <?php echo ($position['position_type'] == 'boh') ? 'selected' : ''; ?>>BOH</option>
        <option value="foh" <?php echo ($position['position_type'] == 'foh') ? 'selected' : ''; ?>>FOH</option>
    </select>
</td>

              <td><button class="btn btn-success add-Positionrow" type="button">+</button></td>
              <td><button type="button" class="btn btn-danger remove-Positionrow">-</button></td>
                </tr>
                <?php } ?>
               <?php }  else {   ?>
                 <tr>
                    <td class="gap-2 d-flex">

    <input type="text" name="position_name[]" class="form-control" />
    <select name="position_type[]" class="form-select ms-2" required>
        <option value="boh">BOH</option>
        <option value="foh">FOH</option>
    </select>
</td>

                    <td><button class="btn btn-success add-Positionrow" type="button">+</button></td>
                </tr>  
               <?php  } ?>
               
            </tbody>
        </table> 

        </div>  
        </div> 
        <div class="col-xxl-3 col-md-6 mt-2">
         <button class="btn btn-success savePositionSettingsBtn"  type="button" onclick="savePositionSettings()" >Save</button>
        </div>
       </form>     
         </div>
           <div class="tab-pane fade" id="payrollTypeSettings" role="tabpanel" aria-labelledby="payrollTypeSettings-tab">
                             <form  method="post" class="form-horizontal" id="payrollTypeSettingsForm">
                            <div class="row">       
                          <div class="col-md-6 col-sm-12">
                         <label for="sort_order" class="form-label fw-semibold">Add Payroll Type</label>
                 
                      <table class="table table-bordered mt-3" id="notificationMailTable">
                     <tbody>
                   <?php   if(isset($payrollTypeConfigData) && !empty($payrollTypeConfigData)) {  ?>
                   <?php foreach($payrollTypeConfigData as $position) { ?>
                  <tr>
                 <td class="gap-2 d-flex">
                <input type="hidden" name="id[]" value="<?php echo (isset($position['id']) ? $position['id'] : ''); ?>">     
                <input  type="text" name="name[]" required class="form-control " value="<?php echo (isset($position['name']) ? $position['name'] : ''); ?>" placeholder="Enter payroll type name" autocomplete="off" />
              </td>
              <td><button class="btn btn-success add-Payrolltyperow" type="button">+</button></td>
              <td><button type="button" class="btn btn-danger remove-Payrolltyperow">-</button></td>
                </tr>
                <?php } ?>
               <?php }  else {   ?>
                 <tr>
                    <td class="gap-2 d-flex">
                    <input type="text" name="name[]" class="form-control" placeholder="Enter payroll type name" autocomplete="off" required />
                    </td>
                    <td><button class="btn btn-success add-Payrolltyperow" type="button">+</button></td>
                </tr>  
               <?php  } ?>
               
            </tbody>
        </table> 

        </div>  
        </div> 
        <div class="col-xxl-3 col-md-6 mt-2">
         <button class="btn btn-success savePayrollSettingsBtn"  type="button" onclick="savePayrollType()" >Save</button>
        </div>
       </form>     
         </div>
         
                             <div class="tab-pane fade" id="generalSettings" role="tabpanel" aria-labelledby="generalSettings-tab">
                            <div class="row">
                            <div class="col"></div>  
                            
                            </div>  
                           <div class="row mt-2">  
                            <table id="leaveList" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                        <thead class="table-light">
                                            <tr>
                                                <th data-ordering="false">Settings Name</th>
                                                 <th data-ordering="false">Action</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody >
                                          
                                            <tr>
                                                <td>Enable face verification for timesheet clockIn</br>
                                                <small>If disabled pin verification will be activated for timesheet portal clockIn/Out</small>
                                                </td>
                                                <td>
                                               <div class="form-check form-switch mb-3" dir="ltr">
    <input type="checkbox" 
           class="form-check-input config-toggle" 
           id="customSwitchsizesm" 
           data-config-key="feature_toggle" 
           <?php echo (isset($generalConfigData['feature_toggle']) && $generalConfigData['feature_toggle'] === '1') ? 'checked' : ''; ?>>
</div>    
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Enable timesheet creation without roster</br>
                                                <small>If disabled orz. must have to create roster than timesheet will be automatically created</small>
                                                </td>
                                                <td>
                                               <div class="form-check form-switch mb-3" dir="ltr">
    <input type="checkbox" 
           class="form-check-input config-toggle" 
           id="customSwitchsizesm" 
           data-config-key="timesheetWORoster_toggle" 
           <?php echo (isset($generalConfigData['timesheetWORoster_toggle']) && $generalConfigData['timesheetWORoster_toggle'] === '1') ? 'checked' : ''; ?>>
</div>    
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Enable location capture for timesheet clock in/out</br>
                                                <small>If disabled employee location will not be captured during clock in/out actions</small>
                                                </td>
                                                <td>
                                               <div class="form-check form-switch mb-3" dir="ltr">
    <input type="checkbox" 
           class="form-check-input config-toggle" 
           id="customSwitchsizesm" 
           data-config-key="location_capture_toggle" 
           <?php echo (isset($generalConfigData['location_capture_toggle']) && $generalConfigData['location_capture_toggle'] === '1') ? 'checked' : ''; ?>>
</div>    
                                                </td>
                                            </tr>
                                           
                                            </tbody>
                                            </table>
                           </div>  
                            </div> 
                            
                            <div class="tab-pane fade" id="onboardingTabsSettings" role="tabpanel" aria-labelledby="onboardingTabsSettings-tab">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="mb-2 text-black">Onboarding Form Customize</h5>
                                    <small class="text-muted d-block mb-3">Turn the onboarding form tabs on or off for this location. Disabled tabs are hidden from the employee onboarding form and their validation is skipped, so employees can still submit the form without getting stuck.</small>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <table class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th data-ordering="false">Onboarding Tab</th>
                                            <th data-ordering="false">Show in Form</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $onboardingTabsList = [
                                            'personalDetails'  => 'Personal Details',
                                            'emergencyDetails' => 'Emergency Details',
                                            'bankDetails'      => 'Bank Details',
                                            'policeClearance'  => 'Police Clearance',
                                            'taxDetails'       => 'Tax Details',
                                            'superAnnuation'   => 'Super Annuation',
                                            'privacyPolicy'    => 'Policies'
                                        ];
                                        $obCfg = isset($onboardingTabsConfig) && is_array($onboardingTabsConfig) ? $onboardingTabsConfig : [];
                                        foreach($onboardingTabsList as $obKey => $obLabel):
                                            $obChecked = (!isset($obCfg[$obKey]) || $obCfg[$obKey] === '1' || $obCfg[$obKey] === 1) ? 'checked' : '';
                                        ?>
                                        <tr>
                                            <td><?php echo $obLabel; ?></td>
                                            <td>
                                                <div class="form-check form-switch mb-1" dir="ltr">
                                                    <input type="checkbox"
                                                           class="form-check-input onboarding-tab-toggle"
                                                           data-tab-key="<?php echo $obKey; ?>"
                                                           <?php echo $obChecked; ?>>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            </div>
                            
                            <div class="tab-pane fade" id="superannuationSettings" role="tabpanel" aria-labelledby="superannuationSettings-tab">
    <form method="post" class="form-horizontal" id="superannuationSettingsForm">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <h5 class="mb-3 text-black">Superannuation Configuration</h5>
                
                <!-- Superannuation Percentage -->
                <div class="mb-4">
                    <label for="super_percentage" class="form-label fw-semibold">Superannuation Percentage (%)</label>
                    <input type="number" 
                           step="0.01" 
                           min="0" 
                           max="100" 
                           name="super_percentage" 
                           id="super_percentage" 
                           class="form-control" 
                           value="<?= isset($superConfigData['super_percentage']) ? $superConfigData['super_percentage'] : '11.5' ?>" 
                           placeholder="e.g., 11.5" 
                           required>
                    <small class="text-muted">Current Australian rate: 11.5% (2024-2025). Will increase to 12% from July 1, 2025.</small>
                </div>

                <!-- Enable Tier-Based Payroll -->
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="enable_tier_payroll" 
                               name="enable_tier_payroll"
                               <?= isset($superConfigData['enable_tier_payroll']) && $superConfigData['enable_tier_payroll'] == '1' ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="enable_tier_payroll">
                            Enable Tier-Based Payroll
                        </label>
                    </div>
                    <small class="text-muted d-block mt-2">
                        When enabled, superannuation will only be calculated for Tier 1 employees. 
                        Tier classifications can be set in employee profiles.
                    </small>
                </div>

                <!-- Payroll Tax Rate -->
                <div class="mb-4">
                    <label for="payroll_tax_rate" class="form-label fw-semibold">Payroll Tax Rate (%)</label>
                    <input type="number" 
                           step="0.01" 
                           min="0" 
                           max="100" 
                           name="payroll_tax_rate" 
                           id="payroll_tax_rate" 
                           class="form-control" 
                           value="<?= isset($superConfigData['payroll_tax_rate']) ? $superConfigData['payroll_tax_rate'] : '5.45' ?>" 
                           placeholder="e.g., 5.45" 
                           required>
                    <small class="text-muted">Standard payroll tax rate for your state/territory.</small>
                </div>

                <!-- Accounting Software Selection -->
                <div class="mb-4">
                    <label for="accounting_software" class="form-label fw-semibold">Choose Accounting Software</label>
                    <select name="accounting_software" 
                            id="accounting_software" 
                            class="form-select" 
                            required>
                        <option value="myob" <?= isset($superConfigData['accounting_software']) && $superConfigData['accounting_software'] == 'myob' ? 'selected' : '' ?>>MYOB</option>
                        <option value="reckon" <?= isset($superConfigData['accounting_software']) && $superConfigData['accounting_software'] == 'reckon' ? 'selected' : '' ?>>Reckon</option>
                        <option value="xero" <?= isset($superConfigData['accounting_software']) && $superConfigData['accounting_software'] == 'xero' ? 'selected' : '' ?>>Xero</option>
                    </select>
                    <small class="text-muted">Select the accounting software to format timesheet exports accordingly.</small>
                </div>

               
            </div>
            
             <!-- Public Holidays Configuration -->
                <div class="mb-4">
                    <label for="public_holidays" class="form-label fw-semibold">Public Holidays</label>
                    <div class="input-group mb-2">
                        <select class="form-select" id="state_select" style="max-width: 150px;">
                            <option value="nsw" <?= isset($superConfigData['holiday_state']) && $superConfigData['holiday_state'] == 'nsw' ? 'selected' : '' ?>>NSW</option>
                            <option value="vic" <?= isset($superConfigData['holiday_state']) && $superConfigData['holiday_state'] == 'vic' ? 'selected' : '' ?>>VIC</option>
                            <option value="qld" <?= isset($superConfigData['holiday_state']) && $superConfigData['holiday_state'] == 'qld' ? 'selected' : '' ?>>QLD</option>
                            <option value="wa" <?= isset($superConfigData['holiday_state']) && $superConfigData['holiday_state'] == 'wa' ? 'selected' : '' ?>>WA</option>
                            <option value="sa" <?= isset($superConfigData['holiday_state']) && $superConfigData['holiday_state'] == 'sa' ? 'selected' : '' ?>>SA</option>
                            <option value="tas" <?= isset($superConfigData['holiday_state']) && $superConfigData['holiday_state'] == 'tas' ? 'selected' : '' ?>>TAS</option>
                            <option value="act" <?= isset($superConfigData['holiday_state']) && $superConfigData['holiday_state'] == 'act' ? 'selected' : '' ?>>ACT</option>
                            <option value="nt" <?= isset($superConfigData['holiday_state']) && $superConfigData['holiday_state'] == 'nt' ? 'selected' : '' ?>>NT</option>
                        </select>
                        <input type="number" class="form-control" id="year_select" placeholder="Year" value="<?= isset($superConfigData['holiday_year']) ? $superConfigData['holiday_year'] : date('Y') ?>" style="max-width: 100px;">
                        <button type="button" class="btn btn-primary" onclick="fetchPublicHolidays()">
                            <i class="ri-download-line"></i> Fetch Holidays
                        </button>
                    </div>
                    <input type="hidden" name="holiday_state" id="holiday_state" value="<?= isset($superConfigData['holiday_state']) ? $superConfigData['holiday_state'] : 'nsw' ?>">
                    <input type="hidden" name="holiday_year" id="holiday_year" value="<?= isset($superConfigData['holiday_year']) ? $superConfigData['holiday_year'] : date('Y') ?>">
                    
                    <textarea class="form-control" 
                              name="public_holidays" 
                              id="public_holidays" 
                              rows="5" 
                              placeholder="Enter dates in YYYY-MM-DD format, comma separated. e.g., 2025-01-01, 2025-01-26, 2025-04-25"><?= isset($superConfigData['public_holidays']) ? $superConfigData['public_holidays'] : '' ?></textarea>
                    <small class="text-muted">
                        Enter public holiday dates manually or click "Fetch Holidays" to automatically load holidays for your state.
                        Format: YYYY-MM-DD, comma separated.
                    </small>
                    
                    <!-- Display parsed holidays -->
                    <div id="holidays_preview" class="mt-2"></div>
                </div>

          
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <button class="btn btn-success saveSuperSettingsBtn" type="button" onclick="saveSuperannuationSettings()">
                    <i class="ri-save-line"></i> Save Settings
                </button>
                <button class="btn btn-secondary" type="reset">
                    <i class="ri-refresh-line"></i> Reset
                </button>
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
                    </div>
    <div class="modal fade createStressProfileModal" tabindex="-1" role="dialog" aria-labelledby="newStressProfileModal" aria-hidden="true">
                                                <div class="modal-dialog modal-xl">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                           <h5 class="modal-title text-black fw-bold">Add New Stress Profile</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                            <form id="stressProfileForm">    
                                            <input type="hidden" name="id" id="stressProfileId">
                                            <div class="row gy-4">
                                            <div class="col-xxl-3 col-md-6">
                                                <div>
                                                    <label for="stressProfileName" class="form-label">Name*</label>
                                                    <input type="text" name="stressProfileName" class="form-control" id="stressProfileName">
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-xxl-3 col-md-6">
                                                <div>
                                                    <label for="maxHrsPWeek" class="form-label">Maximum hours per week</label>
                                                    <input type="text" name="maxHrsPWeek" class="form-control" id="maxHrsPWeek">
                                                    <small>The maximum number of hours that an employee should be expected to work within a week</small>
                                                </div>
                                            </div>

                                            <!--end col-->
                                            <div class="col-xxl-3 col-md-6">
                                                <div>
                                                    <label for="maxDaysPWeek" class="form-label">Maximum hours per shift </label>
                                                    <div class="form-icon">
                                                    <input type="number" name="maxDaysPWeek" class="form-control form-control-icon" id="maxDaysPWeek">
                                                     
                                                    </div>
                                                    <small>The maximum number of days that an employee should be expected to work per shift.</small>
                                                </div>
                                            </div>
                                            
                                            <div class="col-xxl-3 col-md-6">
                                                <div>
                                                    <label for="maxHrsPDay" class="form-label">Maximum hours per day</label>
                                                    <div class="form-icon">
                                                    <input type="number" name="maxHrsPDay" class="form-control form-control-icon" id="maxHrsPDay">
                                                     
                                                    </div>
                                                <small>The maximum number of hours that an employee should be expected to work within a day</small>    
                                                </div>
                                            </div>
                                           </div> 
                                           </form>
                                                           
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a href="javascript:void(0);" class="btn btn-link link-success shadow-none fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                                                            <button type="button" class="btn btn-primary createStressProfileBtn" onclick="createStressProfile()">Create</button>
                                                            <button type="button" class="btn btn-primary updateStressProfileBtn d-none" onclick="updateStressProfile(this)">Update</button>
                                                        </div>
                                                    </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div>
     <div class="modal fade addEditLeaveModal" tabindex="-1" role="dialog" aria-labelledby="newleaveModal" aria-hidden="true">
                                                <div class="modal-dialog modal-xl">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                           <h5 class="modal-title text-black fw-bold">Add Leave Type</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                            <form id="leaveForm">   
                                            <input type="hidden" name="id" class="form-control" id="leaveId">
                                            <div class="row gy-4">
                                          
                                            <div class="col-xxl-3 col-md-6">
                                                <div>
                                                    <label for="maxHrsPWeek" class="form-label">Leave Type Name</label>
                                                    <input type="text" name="leaveTypeName" class="form-control" id="leaveTypeName">
                                                </div>
                                            </div>
                                            
                                            <div class="col-xxl-3 col-md-6">
                                                <div class="form-check mb-3">
                                                <label for="entitlements" class="form-label">Number of entitlements</label>
                                                 <input class="form-control" type="text" id="entitlements" name="entitlements">
                                            </div>
                                            </div>

                                            <div class="col-xxl-3 col-md-6">
                                                <div class="form-check mb-3">
                                                 <input class="form-check-input" type="checkbox" id="is_paid" name="is_paid">
                                                 <label class="form-check-label" for="is_paid">Paid Leave</label>
                                            </div>
                                            </div>
                                           </div> 
                                           </form>
                                           </div>
                                          <div class="modal-footer">
                                          <a href="javascript:void(0);" class="btn btn-link link-success shadow-none fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                                          <button type="button" class="btn btn-primary addLeaveTypeBtn" onclick="addLeaveType()">Create</button>
                                          <button type="button" class="btn btn-primary updateLeaveTypeBtn d-none" onclick="updateLeaveType(this)">Update</button>
                                             </div>
                                                    </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div>
    <div class="modal fade flip" id="deleteRecordModal" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-body p-5 text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json"
                                                        trigger="loop" colors="primary:#405189,secondary:#f06548"
                                                        style="width:90px;height:90px"></lord-icon>
                                                    <div class="mt-4 text-center">
                                                        <h4 class="text-black">You are about to delete a record ?</h4>
                                                       
                                                        <div class="hstack gap-2 justify-content-center remove">
                                                            <button
                                                                class="btn btn-link link-success fw-medium text-decoration-none"
                                                                data-bs-dismiss="modal"><i
                                                                    class="ri-close-line me-1 align-middle"></i>
                                                                Close</button>
                                                            
                                                                <input class="deletedRecordId" type="hidden">
                                                                <input class="deletedtableName" type="hidden">
                                                            <button class="btn btn-danger" id="delete-record" onclick="deleteRowRecord(this)">Yes,
                                                                Delete It</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                          
                                            
   <script>
   $(document).ready(function(){
     $('form').on('click', '.add-row', function () {
             let newRow = '<tr>';
             newRow +='<td>';
             newRow +='<select class="form-select notificationMailoptions" name="mailType[]">';
             newRow +='<option value="" >Select option</option>';
             newRow +='<option value="managerEmail">Manager Email</option>';
             newRow +='</select>';
             newRow +='</td>'; 
             newRow +='<td class="gap-2 d-flex"><input type="text" name="emailTo[]" class="form-control " placeholder="Enter Email" autocomplete="off"  />';
             newRow +='</td><td><button type="button" class="btn btn-success add-row">+</button></td><td><button type="button" class="btn btn-danger remove-row">-</button></td></tr>';
                $(this).closest('tr').after(newRow);
            });

     // Remove row on minus button click
     $('form').on('click', '.remove-row, .remove-Positionrow,.remove-Payrolltyperow', function () {
     $(this).closest('tr').remove();
     });
            
        // for adding new positions
     $('form').on('click', '.add-Positionrow', function () {
             let newRow = '<tr>';
          newRow += `<td class="gap-2 d-flex"><input type="text" name="position_name[]" class="form-control" placeholder="Enter position name" />;
            <select name="position_type[]" class="form-select ms-2" required>
             <option value="boh">BOH</option>
            <option value="foh">FOH</option>
            </select>
            </td>`;
             newRow +='<td><button type="button" class="btn btn-success add-Positionrow">+</button></td><td><button type="button" class="btn btn-danger remove-Positionrow">-</button></td></tr>';
                $(this).closest('tr').after(newRow);
            });
            // for payroll type
            
             $('form').on('click', '.add-Payrolltyperow', function () {
             let newRow = '<tr>';
             newRow +='<td class="gap-2 d-flex"><input type="text" name="name[]" class="form-control " placeholder="Enter payroll type name" autocomplete="off"  />';
             newRow +='</td><td><button type="button" class="btn btn-success add-Payrolltyperow">+</button></td><td><button type="button" class="btn btn-danger remove-Payrolltyperow">-</button></td></tr>';
                $(this).closest('tr').after(newRow);
            });
            
            
   })
   let table = $('#stressProfileList').DataTable({
    pageLength: 10,
     paging: false,
     "language": {
            "info": ""
        }
   });
   
   function createStressProfile(){
       if($("#stressProfileName").val() ==''){
         alert("Please enter stress profile name");
         return false;
       } 
       $(".createStressProfileBtn").html('Creating...');
       let formData = $("#stressProfileForm").serialize();
       $.ajax({
           "url" : "/HR/Config/createStressProfile",
           "method" :"post",
           "data" :formData,
           "success" :function(response){
             $(".createStressProfileBtn").html('Stress Profile Created');
           }
       });
       
       setTimeout(function () {
        $(".createStressProfileBtn").html('Create');
        $('.createStressProfileModal').modal('hide');
      }, 2500);
   }
   
  $('.uploadConfigFile').click(function(e){
     let err=0;
     let formId = $(this).data('formid');
     let fileInputId = $(this).data('inputid');
     let self = $(this);
     console.log("id","#"+fileInputId)
     if($("#"+fileInputId).val() == "") { err=1; }
     
    if(err == '0'){
        $(this).val("Uploading..."); 
       let formData = new FormData($("#"+formId)[0]);
           $.ajax({
            type: "POST",
            url: '/HR/uploadConfigFiles',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response){
        	    let res = JSON.parse(response)
        	     console.log("res=",res)
        	    if(res?.status=='success'){
        	        console.log("res",res)
		         self.val("File Uploaded"); 
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
    else{ alert('Please select file to upload'); return false; }
	}); 
	
	function addLeaveType(){
       if($("#leaveTypeName").val() ==''){
         alert("Please enter leave type name");
         return false;
       } 
       $(".addLeaveTypeBtn").html('Adding...');
       let formData = $("#leaveForm").serialize();
       $.ajax({
           "url" : "/HR/Config/addleaveType",
           "method" :"post",
           "data" :formData,
           "success" :function(response){
             $(".addLeaveTypeBtn").html('Leave Type Added.');
           }
       });
       
       setTimeout(function () {
        $(".addLeaveTypeBtn").html('Create');
        $('.addEditLeaveModal').modal('hide');
      }, 2000);
   }
   
  function deleteRow(id,tableName){
   $("#deleteRecordModal").modal("show");
   $(".deletedRecordId").val(id);
   $(".deletedtableName").val(tableName);
  }
  function deleteRowRecord(obj){
     let id = $(".deletedRecordId").val();
     let btn = $(obj); btn.html("Deleting....")
     let tableName = $(".deletedtableName").val();
      $.ajax({
      type: "POST",
      "url" : "/HR/Leave/delete",
      data:'id='+id+'&tableName='+tableName,
      success: function(data){
      $("tr[data-delete-id='" + id + "']").remove();
      btn.html("Yes,Delete It")
      $("#deleteRecordModal").modal('hide');
      }
      });
  }
  
  function saveEmailSettings(){
      $(".saveEmailSettingsBtn").html('Saving...');
     let formData = $("#emailSettingForm").serialize();
       $.ajax({
           "url" : "/HR/Config/emailSetting",
           "method" :"post",
           "data" :formData,
           "success" :function(response){
             $(".saveEmailSettingsBtn").html('Save');;
           }
       });  
  }
  function savePositionSettings(){
      $(".savePositionSettingsBtn").html('Saving...');
     let formData = $("#positionSettingsForm").serialize();
       $.ajax({
           "url" : "/HR/Config/positionSetting",
           "method" :"post",
           "data" :formData,
           "success" :function(response){
             $(".savePositionSettingsBtn").html('Save');;
           }
       });  
  }
  // save payroll type
   function savePayrollType(){
      $(".savePayrollSettingsBtn").html('Saving...');
     let formData = $("#payrollTypeSettingsForm").serialize();
       $.ajax({
           "url" : "/HR/Config/addPayrollType",
           "method" :"post",
           "data" :formData,
           "success" :function(response){
             $(".savePayrollSettingsBtn").html('Save');;
           }
       });  
  }
  
  function editLeave(id, leaveTypeName, entitlements, is_paid) {
    $("#leaveTypeName").val(leaveTypeName);
    $("#leaveId").val(id);
    $("#entitlements").val(entitlements);
    if (is_paid == 1) {
        $("#is_paid").prop('checked', true); // Add attr checked
    } else {
        $("#is_paid").prop('checked', false); // Remove attr checked
    }
     $('.updateLeaveTypeBtn').removeClass('d-none');
     $('.addLeaveTypeBtn').addClass('d-none');
    $('.addEditLeaveModal').modal('show');
}
function addNewLeave(){
$("#leaveForm")[0].reset();    
$('.updateLeaveTypeBtn').addClass('d-none');
$('.addLeaveTypeBtn').removeClass('d-none');    
$('.addEditLeaveModal').modal('show');    
}
function updateLeaveType(obj){
  let formData = $("#leaveForm").serialize();
  $(obj).html("Updating...")
       $.ajax({
           "url" : "/HR/Config/updateleaveType",
           "method" :"post",
           "data" :formData,
           "success" :function(response){
             $(".updateLeaveTypeBtn").html('Leave Updated');
           }
       });  
}

function addNewStressProfile(){
$("#leaveForm")[0].reset();    
$('.updateStressProfileBtn').addClass('d-none');
$('.createStressProfileBtn').removeClass('d-none');    
$('.createStressProfileModal').modal('show');    
}
function editStressProfile(id,stressProfileName,maxHrsPWeek,maxDaysPWeek,maxHrsPDay){
 $("#stressProfileId").val(id);    
 $("#stressProfileName").val(stressProfileName);
 $("#maxHrsPWeek").val(maxHrsPWeek);
 $("#maxDaysPWeek").val(maxDaysPWeek);
 $("#maxHrsPDay").val(maxHrsPDay);
 $('.createStressProfileBtn').addClass('d-none');
 $(".updateStressProfileBtn").removeClass("d-none")
 $('.createStressProfileModal').modal('show');
}
function updateStressProfile(obj){
    $(obj).html("Updating...")
    let formData = $("#stressProfileForm").serialize();
       $.ajax({
           "url" : "/HR/Config/updateStressProfile",
           "method" :"post",
           "data" :formData,
           "success" :function(response){
           location.reload();
           }
       });
}


  	
   </script>
   
   <script>
$(document).ready(function() {
    $('.config-toggle').on('change', function() {
        let isChecked = $(this).is(':checked') ? 1 : 0;
        let configKey = $(this).data('config-key');
        
        $.ajax({
            url: '/HR/Config/saveGeneralConfig',
            type: 'POST',
            data: {
                config_key: configKey,
                value: isChecked,
            },
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    alert('Configuration saved successfully!');
                } else {
                    alert('Error saving configuration: ' + response.message);
                    // Revert toggle state on error
                    $(this).prop('checked', !isChecked);
                }
            },
            error: function(xhr, status, error) {
                alert('Network error occurred');
                // Revert toggle state on error
                $(this).prop('checked', !isChecked);
            }
        });
    });
});
</script>

<script>
$(document).ready(function() {
    $('.onboarding-tab-toggle').on('change', function() {
        var $toggle = $(this);
        var isChecked = $toggle.is(':checked') ? 1 : 0;
        var tabKey = $toggle.data('tab-key');

        $.ajax({
            url: '/HR/Config/saveOnboardingTabConfig',
            type: 'POST',
            data: {
                tab_key: tabKey,
                value: isChecked
            },
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    alert('Onboarding form updated successfully!');
                } else {
                    alert('Error saving configuration: ' + response.message);
                    $toggle.prop('checked', !isChecked);
                }
            },
            error: function() {
                alert('Network error occurred');
                $toggle.prop('checked', !isChecked);
            }
        });
    });
});
</script>
                                            
     <script>
     
   function fetchPublicHolidays() {
    let state = $('#state_select').val();
    let year = $('#year_select').val() || new Date().getFullYear();
    
    // Update hidden fields
    $('#holiday_state').val(state);
    $('#holiday_year').val(year);
    
    // Show loading
    $('#public_holidays').val('Loading holidays...');
    
    // Australian Public Holidays API
    let apiUrl = 'https://date.nager.at/api/v3/PublicHolidays/' + year + '/AU';
    
    $.ajax({
        url: apiUrl,
        method: 'GET',
        dataType: 'json',
        success: function(holidays) {
            // Filter by state (counties in API response)
            let filteredHolidays = holidays.filter(function(holiday) {
                // If no counties specified, it's a national holiday
                if (!holiday.counties || holiday.counties.length === 0) {
                    return true;
                }
                // Check if state is in counties
                return holiday.counties.includes('AU-' + state.toUpperCase());
            });
            
            // Extract dates
            let dates = filteredHolidays.map(h => h.date).join(', ');
            
            if (dates) {
                $('#public_holidays').val(dates);
                
                // Show success message with holiday names
                let holidayList = '<div class="alert alert-success mt-2"><strong>Fetched ' + filteredHolidays.length + ' holidays for ' + state.toUpperCase() + ' ' + year + ':</strong><ul class="mb-0 mt-2">';
                filteredHolidays.forEach(function(holiday) {
                    holidayList += '<li>' + holiday.localName + ' - ' + holiday.date + '</li>';
                });
                holidayList += '</ul></div>';
                $('#holidays_preview').html(holidayList);
            } else {
                $('#public_holidays').val('');
                alert('No holidays found for ' + state.toUpperCase() + ' in ' + year);
            }
        },
        error: function() {
            $('#public_holidays').val('');
            alert('Failed to fetch holidays. Please enter manually or try again.');
        }
    });
}

function saveSuperannuationSettings() {
    // Validate holidays format
    let holidays = $('#public_holidays').val().trim();
    if (holidays) {
        let dates = holidays.split(',').map(d => d.trim());
        let invalidDates = dates.filter(d => !/^\d{4}-\d{2}-\d{2}$/.test(d));
        
        if (invalidDates.length > 0) {
            alert('Invalid date format detected. Please use YYYY-MM-DD format.\nInvalid dates: ' + invalidDates.join(', '));
            return;
        }
    }
    
    $('.saveSuperSettingsBtn').html('<i class="ri-loader-4-line"></i> Saving...');
    let formData = $("#superannuationSettingsForm").serialize();
    
    $.ajax({
        url: "/HR/Config/saveSuperannuationSettings",
        method: "post",
        data: formData,
        success: function(response) {
            $('.saveSuperSettingsBtn').html('<i class="ri-check-line"></i> Saved!');
            setTimeout(function() {
                $('.saveSuperSettingsBtn').html('<i class="ri-save-line"></i> Save Settings');
            }, 2000);
        },
        error: function() {
            $('.saveSuperSettingsBtn').html('<i class="ri-close-line"></i> Error');
            alert('Failed to save settings. Please try again.');
            setTimeout(function() {
                $('.saveSuperSettingsBtn').html('<i class="ri-save-line"></i> Save Settings');
            }, 2000);
        }
    });
}
     </script>
                                            