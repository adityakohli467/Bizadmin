<style>
    .is-invalid {
        border-color: #dc3545;
    }
    .invalid-feedback {
        display: block;
        font-size: 13px;
    }
</style>

<div class="main-content">
  <div class="page-content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
           <div class="card-header d-flex justify-content-between align-items-center">
    
    <!-- Left Side Title -->
    <h5 class="card-title mb-0 text-black">Manage Employees</h5>

    <!-- Right Side Buttons -->
    <div class="d-flex gap-2">
        <button 
            class="btn btn-primary" 
            type="button" 
            id="addEmployeeBtn" 
            data-bs-toggle="modal" 
            data-bs-target="#addEmployeeModal">
            <i class="ri-user-add-line"></i> Add Employee
        </button>

        <a href="/HR/contractors/addEditContractor" 
           class="btn btn-success">
            <i class="ri-user-add-line"></i> Add Contractor
        </a>
    </div>

</div>

            <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                <li class="nav-item">
                 <a class="nav-link py-3 Delivered active" data-bs-toggle="tab" href="#activeEmployeeTab" role="tab" aria-selected="false">
                 <i class="ri-checkbox-circle-line me-1 align-bottom"></i> Employees
                  </a>
                 </li>
                <li class="nav-item">
                <a class="nav-link py-3 Cancelled" data-bs-toggle="tab" href="#inActiveEmployeeTab" role="tab" aria-selected="false">
                <i class="ri-close-circle-line me-1 align-bottom"></i> Ex-Employees
                </a>
                 </li>
                 
                 <li class="nav-item">
                <a class="nav-link py-3 Cancelled" data-bs-toggle="tab" href="#contractorsTab" role="tab" aria-selected="false">
                <i class="ri-checkbox-circle-line me-1 align-bottom"></i> Contractors
                </a>
                 </li>
                 
                  <li class="nav-item">
                <a class="nav-link py-3 Cancelled" data-bs-toggle="tab" href="#inActiveContractorsTab" role="tab" aria-selected="false">
                <i class="ri-close-circle-line me-1 align-bottom"></i> Ex-Contractors
                </a>
                 </li>
                </ul>
            <div class="tab-content mb-1">
            <div class="tab-pane   active show table-responsive" role="tabpanel"  id="activeEmployeeTab">  
            <table id="employeeList" class="table dt-responsive nowrap table-striped align-middle" style="width:100%">
                <thead class="table-light">
                  <tr>
                    <th scope="col" style="width: 10px;">
                      <div class="form-check">
                        <input class="form-check-input fs-15" type="checkbox" id="checkAll" value="option">
                        
                      </div>
                    </th>
                    <th data-ordering="false">Name</th>
                    <th>Email</th>
                    <th>Hire Start Date</th>
                    <th>Contact Number</th>
                     <th>Onboarding Status</th>
                    <th>Stress Profile</th>
                     <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(isset($empLists) && !empty($empLists)) { foreach($empLists as $empList) { ?>
                  <tr data-delete-id="<?php echo $empList['emp_id']; ?>" class="empMainRow">
                    <th scope="row">
                      <div class="form-check">
                        <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                        <label class="form-check-label"><i class="bx bx-chevron-down"></i></label>
                      </div>
                    </th>
                    <td><a class="first_name" href="/HR/Employee/edit/<?php echo $empList['emp_id']; ?>"><?php echo $empList['name']; ?></a></td>
                    <!-- removing this line wont send onboarding mail so do not remove this or modify send onboaridng mail code in JS at bottom of this page -->
                    <td><a class="email" href="/HR/Employee/edit/<?php echo $empList['emp_id']; ?>"><?php echo $empList['email']; ?></a></td>
                    <td><?php
                      $rawDate = !empty($empList['effective_start_date']) ? $empList['effective_start_date'] : (!empty($empList['created_at']) ? $empList['created_at'] : '');
                      $ts = $rawDate ? strtotime($rawDate) : false;
                      echo ($ts && $ts > 0) ? date('d-m-Y', $ts) : '-';
                    ?></td>
                    <td><?php echo $empList['phone']; ?></td>
                    <td>
                      <?php 
                      $onboard_status = isset($empList['onboarding_status']) ? (int)$empList['onboarding_status'] : 0;
                      $status_class = '';
                      $status_text = '';
                      
                      switch($onboard_status) {
                        case 0:
                          $status_class = '';
                          $status_text = '';
                          break;
                        case 1:
                          $status_class = 'badge bg-warning';
                          $status_text = 'Onboarding email sent';
                          break;  
                        case 2:
                          $status_class = 'badge bg-secondary';
                          $status_text = 'Viewed Email';
                          break;
                        case 3:
                          $status_class = 'badge bg-info';
                          $status_text = 'In Progress';
                          break;
                        case 4:
                          $status_class = 'badge bg-success';
                          $status_text = 'Completed';
                          break;
                       
                        default:
                          $status_class = 'badge bg-secondary';
                          $status_text = 'Unknown';
                      }
                      ?>
                      <span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                    </td>
                    <td><?php echo $empList['stress_profile']; ?></td>
                   
                    
                    <td>
                    <ul class="list-inline hstack gap-2 mb-0">
                        <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                         <a class="text-success"  href="/HR/Employee/edit/<?php echo $empList['emp_id']; ?>"  class="text-primary d-inline-block edit-item-btn">
                         <i class="ri-pencil-fill fs-16"></i>
                         </a>
                         </li>
                         <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Remove">
                          <a class="text-danger d-inline-block remove-item-btn"  href="#" onclick="deleteEmployee(this,<?php echo $empList['emp_id']; ?>,<?php echo $empList['userId']; ?>)">
                         <i class="ri-delete-bin-5-fill fs-16"></i>
                         </a>
                         </li>
                          <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Send Onboarding Email">
                          <a class="text-primary d-inline-block" href="#"
   onclick="confirmSendOnboarding(this, <?php echo $empList['emp_id']; ?>)">
   <i class="ri-mail-send-fill fs-16"></i>
</a>

                         </li>
                        </ul>      
                        
                      
                    </td>
                  </tr>
                  <?php }  } ?>
                </tbody>
              </table>
            </div>
            
            <div class="tab-pane table-responsive" role="tabpanel"  id="inActiveEmployeeTab">  
            <table id="employeeList" class="table dt-responsive nowrap table-striped align-middle" style="width:100%">
                <thead class="table-light">
                  <tr>
                    <th scope="col" style="width: 10px;">
                      <div class="form-check">
                        <input class="form-check-input fs-15" type="checkbox" id="checkAll" value="option">
                        
                      </div>
                    </th>
                    <th data-ordering="false">Name</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Stress Profile</th>
                    <th>Status</th>
                  
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(isset($inActiveEmpLists) && !empty($inActiveEmpLists)) { foreach($inActiveEmpLists as $empList) { ?>
                  <tr>
                    <th scope="row">
                      <div class="form-check">
                        <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                        <label class="form-check-label"><i class="bx bx-chevron-down"></i></label>
                      </div>
                    </th>
                    <td><?php echo $empList['name']; ?></td>
                    <td><a href="#!"><?php echo $empList['email']; ?></a></td>
                    <td><?php echo $empList['phone']; ?></td>
                    <td><?php echo $empList['stress_profile']; ?></td>
                    <td>
                    <div class="form-check form-switch form-switch-success">
                    <input class="form-check-input updateStatus" type="checkbox" rel-id="<?php echo $empList['emp_id']; ?>" rel-userId="<?php echo $empList['userId']; ?>" role="switch" <?php echo($empList['status'] == '1' ? 'checked' : '' ); ?>>
                    </div>
                    </td>
                   
                    <td>
                     <a class="text-success d-inline-block" onclick="reHire(this,<?php echo $empList['emp_id']; ?>)">
                       <i class="ri-recycle-fill fs-16"></i>
                     </a>
                    </td>
                  </tr>
                  <?php }  } ?>
                </tbody>
              </table>
            </div>
            
             <div class="tab-pane table-responsive" role="tabpanel"  id="contractorsTab">  
           <table id="contractorList" class="table dt-responsive nowrap table-striped align-middle" style="width:100%">
                <thead class="table-light">
                  <tr>
                    <th scope="col" style="width: 10px;">
                      <div class="form-check">
                        <input class="form-check-input fs-15" type="checkbox" id="checkAll" value="option">
                        
                      </div>
                    </th>
                    <th data-ordering="false">Contractor Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Company Name</th>
                    <th>Status</th>
                    
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php  if(isset($activeContractorLists) && !empty($activeContractorLists)) { foreach($activeContractorLists as $contractorList) { ?>
                  <tr data-delete-id="<?php echo $contractorList['emp_id']; ?>">
                    <th scope="row">
                      <div class="form-check">
                        <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                        <label class="form-check-label"><i class="bx bx-chevron-down"></i></label>
                      </div>
                    </th>
                    <td><a href="/HR/editContractor/<?php echo $contractorList['emp_id']; ?>"><?php echo $contractorList['name']; ?></a></td>
                    <td><a href="/HR/editContractor/<?php echo $contractorList['emp_id']; ?>"><?php echo $contractorList['email']; ?></a></td>
                    <td><?php echo $contractorList['phone']; ?></td>
                    <td><?php echo $contractorList['company_name']; ?></td>
                    <td>
                    <div class="form-check form-switch form-switch-success">
                    <input class="form-check-input updateStatus" type="checkbox" rel-id="<?php echo $contractorList['emp_id']; ?>" role="switch" <?php echo($contractorList['status'] == '1' ? 'checked' : '' ); ?>>
                    </div>
                    </td>
                   
                    <td>
                    <ul class="list-inline hstack gap-2 mb-0">
                        <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                         <a class="text-success"  href="/HR/editContractor/<?php echo $contractorList['emp_id']; ?>"  class="text-primary d-inline-block edit-item-btn">
                         <i class="ri-pencil-fill fs-16"></i>
                         </a>
                         </li>
                         <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Remove">
                          <a class="text-danger d-inline-block remove-item-btn"  href="#" onclick="deleteEmployee(this,<?php echo $contractorList['emp_id']; ?>,<?php echo $contractorList['userId']; ?>)">
                         <i class="ri-delete-bin-5-fill fs-16"></i>
                         </a>
                         </li>
                        </ul>     
                        
                    </td>
                  </tr>
                  <?php }  } ?>
                </tbody>
              </table>
             </div>
             
             <div class="tab-pane table-responsive" role="tabpanel"  id="inActiveContractorsTab">  
           <table id="contractorList" class="table dt-responsive nowrap table-striped align-middle" style="width:100%">
                <thead class="table-light">
                  <tr>
                    <th scope="col" style="width: 10px;">
                      <div class="form-check">
                        <input class="form-check-input fs-15" type="checkbox" id="checkAll" value="option">
                        
                      </div>
                    </th>
                    <th data-ordering="false">Contractor Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Company Name</th>
                    <th>Status</th>
                    
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php  if(isset($inactiveContractorLists) && !empty($inactiveContractorLists)) { foreach($inactiveContractorLists as $contractorList) { ?>
                  <tr data-delete-id="<?php echo $contractorList['emp_id']; ?>">
                    <th scope="row">
                      <div class="form-check">
                        <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                        <label class="form-check-label"><i class="bx bx-chevron-down"></i></label>
                      </div>
                    </th>
                    <td><a href="/HR/editContractor/<?php echo $contractorList['emp_id']; ?>"><?php echo $contractorList['name']; ?></a></td>
                    <td><a href="/HR/editContractor/<?php echo $contractorList['emp_id']; ?>"><?php echo $contractorList['email']; ?></a></td>
                    <td><?php echo $contractorList['phone']; ?></td>
                    <td><?php echo $contractorList['company_name']; ?></td>
                    <td>
                    <div class="form-check form-switch form-switch-success">
                    <input class="form-check-input updateStatus" type="checkbox" rel-id="<?php echo $contractorList['emp_id']; ?>" role="switch" <?php echo($contractorList['status'] == '1' ? 'checked' : '' ); ?>>
                    </div>
                    </td>
                   
                    <td>
                    <ul class="list-inline hstack gap-2 mb-0">
                        <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                         <a class="text-success"  href="/HR/editContractor/<?php echo $contractorList['emp_id']; ?>"  class="text-primary d-inline-block edit-item-btn">
                         <i class="ri-pencil-fill fs-16"></i>
                         </a>
                         </li>
                         <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Remove">
                          <a class="text-danger d-inline-block remove-item-btn"  href="#" onclick="deleteEmployee(this,<?php echo $contractorList['emp_id']; ?>,<?php echo $contractorList['userId']; ?>)">
                         <i class="ri-delete-bin-5-fill fs-16"></i>
                         </a>
                         </li>
                        </ul>     
                        
                    </td>
                  </tr>
                  <?php }  } ?>
                </tbody>
              </table>
             </div>
            
          </div>
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="varyingcontentModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="varyingcontentModalLabel">Onboard New Employee</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                             <div class="alert alert-success shadow successEmployee" role="alert" style="display:none">
                                              Onboarding email succesfully sent to employee.
                                                </div> 
                                            <div class="alert alert-danger shadow mb-xl-0 dangerEmployee" role="alert" style="display:none">
                                             
                                                </div>    
                              
                     <form  role="form" id="onboardNewEmployeeForm" method="post" action="" enctype="multipart/form-data">                                            
                                                            <div class="row">
                                                                <div class="mb-3 col-lg-6 col-md-6 col-sm-12">
                                                                    <label for="customer-name" class="col-form-label">First Name:</label>
                                                                   <input type="text" name="first_name" class="form-control" id="first_name"  required>
                                                                </div>
                                                                 <div class="mb-3 col-lg-6 col-md-6 col-sm-12">
                                                                    <label for="customer-name" class="col-form-label">Last Name:</label>
                                                                    <input type="text" class="form-control" id="name" name="last_name">
                                                                </div>
                                                        <div class="mb-3 col-lg-6 col-md-6 col-sm-12">
                                                         <label for="customer-name" class="col-form-label">Email:</label>
                                                       <input type="text" class="form-control"  name="email" id="email" aria-describedby="inputGroupPrepend" required>
                                                      <input type="hidden" class="form-control"  name="username" id="username"> 
                                                      <input type="hidden" class="form-control"  name="password" id="password">
                                                      <!-- value 4 always belongs to employee/contractor role by default -->
                                                      <input type="hidden" class="form-control"  name="role_id" id="role_id" value="4">
                                                      <input type="hidden" class="form-control"  name="userId" id="lastInsertedUserId">
                                                      <input type="hidden" class="form-control"  name="ajaxSubmit" id="ajaxSubmit" value="true">
                                                        </div>
                                                        
                                    <div class="mb-3 col-lg-6 col-md-6 col-sm-12">  
                                    <label class="col-form-label">Job Descriptiopn</label>
                                       <input type="file" id="jd" name="userfile[]" class="form-control mt-2" multiple>
                                        </div>
                                                                
                               <?php if (isset($locations) && !empty($locations)) { ?>   
                                   <div class="col-lg-12">
        <h6 class="fw-semibold text-black">Location Access *</h6>

        <select class="js-example-basic-multiple employeeLocations" name="locationIds[]" multiple="multiple">

            <?php 
            $totalLocations = count($locations);
            foreach ($locations as $location) { 
                $isSelected = ($totalLocations == 1) ? 'selected' : ''; 
            ?>
                <option value="<?php echo trim($location['location_id']); ?>" <?php echo $isSelected; ?>>
                    <?php echo $location['location_name']; ?>
                </option>
            <?php } ?>

        </select>

        <small> click on the box to view and select multiple locations</small>    
    </div>
                                <?php } ?>
                                
                               
                                    
                                    
                                                <div class="mb-3 col-lg-6 col-md-6 col-sm-12">
                                                    <label for="tier" class="form-label">Tier</label>
                                                    <select class="form-select" name="tier" id="tier">
                                                        <option value="1">Tier 1</option>
                                                        <option value="2">Tier 2</option>
                                                    </select>
                                                </div>
                                          
                                            
                                    
                                    <div class="mb-3 col-lg-6 col-md-6 col-sm-12">
                                        <label for="prep_area" class="col-form-label">Prep Area: *</label>
                                        <select class="form-control" name="emp_prep_area" id="prep_area" required>
                                            <option value="">Select Prep Area</option>
                                            <?php if(isset($prepAreaLists) && !empty($prepAreaLists)) { 
                                                foreach($prepAreaLists as $prepArea) { ?>
                                                <option value="<?php echo $prepArea['id']; ?>"><?php echo $prepArea['prep_name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3 col-lg-6 col-md-6 col-sm-12">
                                        <label for="employee_type" class="col-form-label">Employment Type:</label>
                                        <select class="form-control" name="employee_type" id="employee_type">
                                           <option value="1">Full Time</option>
<option value="2">Part Time</option>
<option value="3">Casual</option>
<option value="4">Full Time Fixed Term</option>
<option value="5">Part Time Fixed Term</option>
                                        </select>
                                    </div>

                                                            </div>
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-orange" data-bs-dismiss="modal">Close</button>
                                                             <button type="button" class="btn btn-secondary save" onclick="onboardNewEmployee('save')">Save</button>
                                                            <button type="button" class="btn btn-success onboard" onclick="onboardNewEmployee('onboard')">Onboard and send email</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
  <div class="modal fade flip" id="deleteRecord" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-body p-5 text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json"
                                                        trigger="loop" colors="primary:#405189,secondary:#f06548"
                                                        style="width:90px;height:90px"></lord-icon>
                                                    <div class="mt-4 text-center">
                                                        <h4 class="text-black">You are about to delete a employee ?</h4>
                                                        <p class="text-black fs-15 mb-4">Deleting an employee will make the employee profile inactive.  </p>
                                                        <div class="hstack gap-2 justify-content-center remove">
                                                            <button
                                                                class="btn btn-link link-success fw-medium text-decoration-none"
                                                                data-bs-dismiss="modal"><i
                                                                    class="ri-close-line me-1 align-middle"></i>
                                                                Close</button>
                                                                <input class="deletedRecordEmpId" type="hidden">
                                                                <input class="deletedRecordUserId" type="hidden">
                                                            <button class="btn btn-danger" id="delete-record">Yes,
                                                                Delete It</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                           

<script>
let table = $('#employeeList').DataTable({
    pageLength: 100,
});

function confirmSendOnboarding(obj, empId) {
    Swal.fire({
        title: "Send Onboarding Email?",
        text: "This will send the onboarding email to the employee.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, send it",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#45CB88",
        cancelButtonColor: "#d33"
    }).then((result) => {
        if (result.isConfirmed) {
            sendOnboardingEmail(obj, empId);
        }
    });
}


function sendOnboardingEmail(obj, empId) {
    let first_name = $(obj).parents('.empMainRow').find(".first_name").html();
    let email = $(obj).parents('.empMainRow').find(".email").html();
    $(obj).html("Sending...");
    
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: { first_name: first_name, email: email },
        url: "/HR/Employee/sendOnboardingEmail/" + empId,
        success: function(data) {
            $(obj).html("Mail Sent");
        },
        error: function(xhr, status, error) {
            console.error(error); // Log any errors that occur during the Ajax request
        }
    });   
}

function validateOnboardEmployeeForm() {
    let isValid = true;

    // Reset previous errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    // First Name (required)
    if (!$('#first_name').val().trim()) {
        showError('#first_name', 'First name is required');
        isValid = false;
    }

    // Email (required + format)
    const email = $('#email').val().trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!email) {
        showError('#email', 'Email is required');
        isValid = false;
    } else if (!emailRegex.test(email)) {
        showError('#email', 'Enter a valid email address');
        isValid = false;
    }

    // Location Access (required – select2 compatible)
    if ($('.employeeLocations').length) {
        const locations = $('.employeeLocations').val();
        if (!locations || locations.length === 0) {
            showError('.employeeLocations', 'Select at least one location');
            isValid = false;
        }
    }

    // Prep Area (required)
    if (!$('#prep_area').val()) {
        showError('#prep_area', 'Prep area is required');
        isValid = false;
    }

    // Employment Type (required)
    if (!$('#employee_type').val()) {
        showError('#employee_type', 'Employment type is required');
        isValid = false;
    }

    return isValid;
}

// Helper to show inline error
function showError(selector, message) {
    const el = $(selector);
    el.addClass('is-invalid');
    el.after(`<div class="invalid-feedback">${message}</div>`);
}


function onboardNewEmployee(type){
   
   if (!validateOnboardEmployeeForm()) {
        return false;
    }
   $("#username").val($("#email").val());
  
   
   // by default we have to set any dummy password for new employee , so that later they can reset it, it must not be guessed by employee
 $("#password").val($("#email").val()+'#123!Allowed!');
 $("."+type).html('Loading...');
  let formData = $("#onboardNewEmployeeForm").serialize();
  $.ajax({
  url: '/auth/create_user',
  method: 'post',
  data: formData,
  success: function (response) {
    try {
      let responseData = JSON.parse(response);
      if (responseData?.status === 'success') {
        $("#lastInsertedUserId").val(responseData?.user_id)    
        addRecordToEmployeeTable(type);
      } else {
        $('.dangerEmployee').html(responseData?.message);
        $('.dangerEmployee').show();
        if(type == 'save'){
           $("."+type).html('Save');  
        }else{
           $("."+type).html('Onboard and send email');  
        }
       
      }

    } catch (error) {
      console.error("Error parsing JSON:", error);
    }
  },
  error: function (xhr, textStatus, errorThrown) {
    console.error("AJAX request failed:", errorThrown);
  }
});
       setTimeout(function () {
        $('.dangerEmployee').hide();
      }, 5000);
}

function addRecordToEmployeeTable(type) {
    let formData = new FormData($("#onboardNewEmployeeForm")[0]);
    formData.append('type', type);
    $.ajax({
        url: "/HR/onboardNewEmployee",
        method: "post",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log("response", response);

            // Parse form data into usable object
            let formObj = {};
            formData.forEach((value, key) => {
                formObj[key] = value;
            });

            // Extract needed values
            let firstName = formObj.first_name || '';
            let lastName  = formObj.last_name || '';
            let fullName  = firstName + ' ' + lastName;
            let email     = formObj.email || '';
            let empId     = response.emp_id; // From backend
            let hireDate  = new Date().toLocaleDateString('en-GB'); // Today: dd-mm-yyyy
            let phone     = formObj.phone || '-';
            let stress    = 'Not Set';

            // Build the new row HTML
            let newRow = `
                <tr data-delete-id="${empId}" class="empMainRow">
                    <th scope="row">
                        <div class="form-check">
                            <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                            <label class="form-check-label"><i class="bx bx-chevron-down"></i></label>
                        </div>
                    </th>
                    <td><a class="first_name" href="/HR/Employee/edit/${empId}">${fullName}</a></td>
                    <td><a class="email" href="/HR/Employee/edit/${empId}">${email}</a></td>
                    <td>${hireDate}</td>
                    <td>${phone}</td>
                    <td>${stress}</td>
                    <td>
                        <ul class="list-inline hstack gap-2 mb-0">
                            <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                                <a class="text-success" href="/HR/Employee/edit/${empId}" class="text-primary d-inline-block edit-item-btn">
                                    <i class="ri-pencil-fill fs-16"></i>
                                </a>
                            </li>
                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Remove">
                                <a class="text-danger d-inline-block remove-item-btn" href="#" onclick="deleteEmployee(this, ${empId}, ${formObj.userId || 0})">
                                    <i class="ri-delete-bin-5-fill fs-16"></i>
                                </a>
                            </li>
                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Send Onboarding Email">
                                <a class="text-primary d-inline-block" href="#" onclick="confirmSendOnboarding(this, ${empId})">
                                    <i class="ri-mail-send-fill fs-16"></i>
                                </a>
                            </li>
                        </ul>
                    </td>
                </tr>`;

            // Prepend to table body
            $('#employeeList tbody').prepend(newRow);

            // Show success feedback
            $('.successEmployee').show();
             if(type == 'save'){
           $("."+type).html('Save');  
        }else{
           $("."+type).html('Onboard and send email');  
        }

            // Optional: Re-init tooltips if using Bootstrap
            $('[data-bs-toggle="tooltip"]').tooltip();
        },
        error: function(xhr, status, error) {
            console.error("Ajax Error:", error);
            alert("Failed to add employee. Please try again.");
        }
    });

    // Hide success message & modal after delay
    setTimeout(function() {
        $('.successEmployee').hide();
        $('#addEmployeeModal').modal('hide');
        $('#onboardNewEmployeeForm')[0].reset();
       
    }, 3000);
}


   $(document).on("change", ".updateStatus" , function() {
     let id = $(this).attr('rel-id');
     let userId = $(this).attr('rel-userId');
     let status = 0;
     if($(this).is(":checked")){ status = 1; }else{ status = 0; }
     $.ajax({
     type: "POST",
     "url" : "/HR/Employee/employeeStatusUpdate",
     data:'id='+id+'&user_id='+userId+'&status='+status,
      success: function(data){
    }
     });
        });
    function deleteEmployee(obj, emp_id,user_id){
     $("#deleteRecord").modal('show');   
     $(".deletedRecordEmpId").val(emp_id);
     $(".deletedRecordUserId").val(user_id);
    }    
    
$('#delete-record').click(function(){
    
     let deleteEmpId =  $(".deletedRecordEmpId").val();
     let deleteUserId =  $(".deletedRecordUserId").val();
     let status = 0;
               $.ajax({
                type: "POST",
                "url" : "/HR/Employee/employeeDelete",
                data:'emp_id='+deleteEmpId+'&user_id='+deleteUserId+'&status='+status+'&is_deleted=1',
                success: function(data){
                 $("tr[data-delete-id='" + deleteEmpId + "']").remove();
                   $("#deleteRecord").modal('hide');
                }
                });
   
    
});

function reHire(obj,deleteEmpId){
    let deleteUserId =  $(".deletedRecordUserId").val();
             $(obj).html("Loading...");
              $.ajax({
                type: "POST",
                "url" : "/HR/Employee/employeeDelete",
                data:'emp_id='+deleteEmpId+'&user_id='+deleteUserId+'&is_deleted=0',
                success: function(data){
                location.reload();
                }
                });     
    }  
    
    

 
</script>
