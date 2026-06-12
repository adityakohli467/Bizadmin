<div id="checklistPage" class="container-fluid px-3 py-3" style="margin-top: 90px !important;">

                        <?php
                            $clHour = date('H');
                            if ($clHour >= 5 && $clHour < 12)      { $clGreeting = 'Good Morning';   $clIcon = 'bx bxs-sun'; }
                            elseif ($clHour >= 12 && $clHour < 18) { $clGreeting = 'Good Afternoon'; $clIcon = 'bx bxs-sun'; }
                            else                                   { $clGreeting = 'Good Evening';   $clIcon = 'bx bxs-moon'; }
                        ?>
                        <!-- Page hero -->
                        <div class="cl-hero mb-4">
                            <div class="cl-hero-left">
                                <span class="cl-hero-icon"><i class="<?php echo $clIcon; ?>"></i></span>
                                <div>
                                    <h1 class="cl-hero-title">
                                        <?php echo $clGreeting; ?>,
                                        <span class="cl-hero-name"><?php echo htmlspecialchars($this->session->userdata('username'), ENT_QUOTES, 'UTF-8'); ?></span>
                                    </h1>
                                    <p class="cl-hero-sub">Here's what's happening today</p>
                                </div>
                            </div>
                            <?php if($this->session->userdata('location_name') != '') { ?>
                            <a href="<?php echo base_url('auth/dashboard'); ?>" class="cl-loc-chip" title="Switch location">
                                <i class="bx bxs-map"></i>
                                <span><?php echo htmlspecialchars($this->session->userdata('location_name'), ENT_QUOTES, 'UTF-8'); ?></span>
                                <i class="bx bx-chevron-right cl-loc-chev"></i>
                            </a>
                            <?php } ?>
                        </div>

                        <div class="row">
                            
                            <div class="col-xl-5 col-md-5 col-lg-5 d-none d-xl-block">
                                <div class="d-flex flex-column h-100">
                                    <div class="cl-section-label">Your Apps</div>
                                    <div class="row">
                                         <?php if(!empty($systemAssignedToThisUser)) {  ?>
                                            <?php foreach($systemAssignedToThisUser as $system)  {  ?>  
                                            
                                        <div class="col-xl-6 col-md-6">
                                            <?php if(isset($system['custom_redirect_url']) && $system['custom_redirect_url'] !=''){ ?>
                                            <a href="<?php echo $system['custom_redirect_url']; ?>">
                                            <?php } else { ?>
                                            <a href="/<?php echo $system['slug']; ?>/<?php echo $system['system_id']; ?>">
                                            <?php } ?>
                                            
                                            <div class="card card-animate overflow-hidden" style="background-color: #282A53;">
                                                <div class="position-absolute start-0" style="z-index: 0;">
                                                    <svg version="1.2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 120" width="200" height="120" fill="">
                                                        <style>
                                                            .s0 {
                                                                opacity: .05;
                                                                fill: var(--vz-info)c
                                                            }
                                                        </style>
                                                        <path id="Shape 8" class="s0" d="m189.5-25.8c0 0 20.1 46.2-26.7 71.4 0 0-60 15.4-62.3 65.3-2.2 49.8-50.6 59.3-57.8 61.5-7.2 2.3-60.8 0-60.8 0l-11.9-199.4z" />
                                                    </svg>
                                                </div>
                                                <div class="card-body" style="z-index:1 ;">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-grow-1 overflow-hidden">
                                                            <p class="text-uppercase fw-bold text-muted text-truncate mb-3"> <?php echo $system['system_name']; ?></p>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                    <h4 class="fs-50 fw-semibold ff-secondary mb-0">
                                                    <i class="<?php echo (isset($system['system_icon']) && $system['system_icon'] !='' ? $system['system_icon'] : 'bx bx-laptop'); ?> fs-50" style="color:<?php echo $system['system_color']; ?>;font-size: 45px;"></i>
                                                     </h4>
                                                        </div>
                                                        
                                                </div><!-- end card body -->
                                            </div>
                                             </a>
                                        </div>
                                       
                                          <?php } ?>
                                            <?php } ?>  
                                       
                                    </div><!--end row-->
                                </div>
                            </div>
                            <div class="col-xl-7 col-md-12 col-lg-12 col-sm-12">
                                <div class="card card-height-100" style="background-color:#ffff;">
                                    <div class="card-header align-items-center d-flex" style="background-color: #ffff;">
                                        <div class="flex-shrink-0 w-75">
                                         <div class="d-flex gap-2 my-2">
                                          <i class="ri-file-list-fill align-bottom fw-semibold"></i><span class="fw-semibold"> Today's Checklist</span>
                                        </div>
                                        </div>
                                       
                                        <?php if ($this->ion_auth->in_group('admin') || $this->ion_auth->in_group('manager')): ?>
                                         <div class="flex-shrink-0 d-flex gap-2">
                                            <a href="<?php echo base_url('checklist/checklist') ?>" class="btn btn-primary btn-sm fs-14 d-flex gap-2">
                                                <i class="ri-add-line align-bottom"></i><span class="d-none d-sm-block"> Create Task</span> </a>
                                                
                                              
                                        </div>
                                        <?php endif  ?>
                                    </div><!-- end card header -->

                                    <div class="card-body d-flex flex-column">
                                        <div class="table-responsive table-card dashboardChecklist">
                                            <table class="table table-centered table-hover align-middle table-nowrap mb-0">
                                            <tbody>
                <?php if(isset($checkListData) && !empty($checkListData)) { ?>
              <?php $i=0; foreach($checkListData as $checkList) {   ?>
               <?php $intersection = array_intersect($checkListUncheckedRoles, unserialize($checkList->role_id));  ?>
               <?php  if( (in_array($currentUserRoleId,unserialize($checkList->role_id)))  &&   is_array(unserialize($checkList->role_id)) && empty($intersection)) { ?>
                                                    <?php  if(isset($checkList->has_subtask) && $checkList->has_subtask != 1){  ?>
                                                   
                                                <div class="accordion custom-accordionwithicon accordion-border-box" id="accordionFill" >
                                              <div class="accordion-item">
                         <h2 class="accordion-header" id="accordionFillExample1">
                    <label class="switch mt-2 w-auto" style="margin-left: 13px;" ><input type="checkbox" <?php echo (isset($checkList->is_completed) && $checkList->is_completed == 1 ? 'checked' : '') ?>  class="success">
        <span class="slider checklistSlider"  data-checklistId="<?php echo $checkList->id ?>"  onclick="toggleMainCheckbox(this,'Global_checklist')" style="width: 44px;"></span>
        </label>         
                       <button style="margin-left: 62px;margin-top: -42px; width: 90%;    padding-top: 10px;padding-bottom: 10px;" class="accordion-button text-lightblack collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accor_fill<?php echo $i; ?>"  aria-controls="accor_fill1">
                        <h6 class="text-lightblack w-100"><?php echo  ($checkList->deadline_time !='' ? "[".date("g:i A", strtotime($checkList->deadline_time))."]" : '') ; ?>  <?php echo $checkList->title ?> </h6> 
                       
                        <?php if($checkList->urlSystem !='') { ?>
                        <a href="<?php echo base_url('/'.$checkList->urlSystem); ?>"><i class="ri-eye-fill align-bottom me-1 mx-3 fs-16 "style="color: #0440B0;" ></i></a>
                        <?php } ?>
                         <i class="ri-attachment-2 align-bottom me-1 mx-3 fs-16 " style="color: red;" onclick="showCheckListInfoModal(<?php echo $checkList->id ?>)"></i>
                        </button>
                      </h2>
             <div id="accor_fill<?php echo $i; ?>" class="accordion-collapse collapse " aria-labelledby="accordionFillExample1" data-bs-parent="#accordionFill">
            <div class="accordion-body" style=" background-color: #f5f4f4;border: 1px solid #0065ff3b;">
              <span class="mx-5 text-black">  <?php echo $checkList->descr ?></span>
                </div>
              </div>
            </div>
        </div>          
                                       <?php }else{ $subtasks = json_decode($checkList->subchecklists);  ?>
             <div class="accordion custom-accordionwithicon accordion-border-box" id="accordionFill">
                                              <div class="accordion-item">
                         <h2 class="accordion-header" id="accordionFillExample1">
                    <label class="switch mt-2 w-auto" style="margin-left: 13px;" ><input type="checkbox" <?php echo (isset($checkList->is_completed) && $checkList->is_completed == 1 ? 'checked' : '') ?>  class="success">
        <span class="slider checklistSlider" data-checklistId="<?php echo $checkList->id ?>"  onclick="toggleMainCheckbox(this,'Global_checklist',false)"   style="width: 44px;"></span>
        
        </label>         
                       <button style="margin-left: 62px;margin-top: -42px; width: 90%;padding-top: 10px;padding-bottom: 10px;" class="accordion-button text-lightblack collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accor_fill<?php echo $i; ?>"  aria-controls="accor_fill1">
                        <h6 class="text-lightblack w-100  "><?php echo  ($checkList->deadline_time !='' ? "[".date("g:i A", strtotime($checkList->deadline_time))."]" : '') ; ?> <?php echo $checkList->title ?></h6> 

                        <?php if($checkList->urlSystem !='') { ?>
                        <a href="<?php echo base_url('/'.$checkList->urlSystem); ?>"><i class="ri-eye-fill align-bottom me-1 mx-3 fs-16 "style="color: #0440B0;" ></i></a>
                        <?php } ?>
                         <i class="ri-attachment-2 align-bottom me-1 mx-3 fs-16 " style="color: red;" onclick="showCheckListInfoModal(<?php echo $checkList->id ?>)"></i>
                        
                        </button>
                      </h2>
             <div id="accor_fill<?php echo $i; ?>" class="accordion-collapse collapse " aria-labelledby="accordionFillExample1" data-bs-parent="#accordionFill">
            <div class="accordion-body" style=" background-color: #f5f4f4;border: 1px solid #0065ff3b;">
              <span class=" text-black">  <?php echo $checkList->descr ?></span>
              <?php if(!empty($subtasks)) {  foreach($subtasks as $subtask) {  ?>  
            <div class="d-flex align-items-center">
                <div class="form-check form-check-success" style="width: 100%;padding-bottom: 10px;padding-top: 10px;">
        
        <div class="d-flex">
             <label class="switch  w-auto" ><input type="checkbox" <?php echo (isset($subtask->subchecklist_is_completed) && $subtask->subchecklist_is_completed == 1 ? 'checked' : '') ?>  class="success Ccheckbox ">
        <span class="slider checklistSlider" data-checklistId="<?php echo (isset($subtask->subChecklistId) ? $subtask->subChecklistId : '') ?>"  onclick="toggleCheckbox(this,'Global_subchecklist')" style="width: 44px;"></span>
        </label>
          <h6 class="text-lightblack w-auto mx-5 "><?php echo ($subtask->subchecklist_time !='' ? "[".date("H:i A", strtotime($subtask->subchecklist_time))."]" :'') ; ?></h6> 
          <h6 class="mb-0 text-lightblack" data-fulltext="<?php echo $subtask->subChecklistDescr; ?>"><?php echo $subtask->subChecklistDescr; ?></h6>
         
         
        
         </div>
           </div>
           </div>
           <?php  } } else { ?>
           <p></p>
           <?php }  ?>
                </div>
              </div>
            </div>
        </div>
                 <?php } ?>
                 <?php $i++;} } ?>
                  <?php } ?>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                       
                                    </div>
                                </div> <!-- .card-->
                            </div><!--end col-->
                        </div>
                        
   <!-------------------------------------------------------------other roles  Checklist as configured from admin -------------------------------------------------------------------->
                      <?php  if($this->ion_auth->is_admin() || $this->ion_auth->is_manager()){ ?>
                       
                        <?php   if(!empty($checkListUncheckedRoles)) { ?>
                        <?php foreach($checkListUncheckedRoles as $roleID => $roleName) { ?>
                        <?php  if(isset($checkListData) && !empty($checkListData)) {  ?>
                      
                        <div class="row">
                            
                            <div class="col-xl-5 col-md-5 col-lg-5 d-none d-xl-block">
                                
                            </div>
                            <div class="col-xl-7 col-md-12 col-lg-12 col-sm-12">
                                <div class="card card-height-100" style="background-color:#ffff;">
                                    <div class="card-header align-items-center d-flex" style="background-color: #ffff;">
                                        <div class="flex-shrink-0 w-75">
                                         <div class="d-flex gap-2 my-2">
                                          <i class="ri-file-list-fill align-bottom fw-semibold"></i><span class="fw-semibold"> <?php echo $roleName; ?> Checklist</span>
                                        </div>
                                        </div>
                                       
                                       
                                    </div><!-- end card header -->

                                    <div class="card-body d-flex flex-column">
                                        <div class="table-responsive table-card dashboardChecklist">
                                            <table class="table table-centered table-hover align-middle table-nowrap mb-0">
                                            <tbody>
                                            <?php $i=0;  foreach($checkListData as $staffCheckList) { ?>
                                           
               <?php  if(is_array(unserialize($staffCheckList->role_id)) && in_array($roleID, unserialize($staffCheckList->role_id))) {  ?>
                                                    <?php  if(isset($staffCheckList->has_subtask) && $staffCheckList->has_subtask != 1){  ?>
            <div class="accordion custom-accordionwithicon accordion-border-box" id="accordionFill" >
                                              <div class="accordion-item">
                         <h2 class="accordion-header" id="accordionFillExample1">
                    <label class="switch mt-2 w-auto" style="margin-left: 13px;" ><input type="checkbox" <?php echo (isset($staffCheckList->is_completed) && $staffCheckList->is_completed == 1 ? 'checked' : '') ?>  class="success">
        <span class="slider checklistSlider"  data-checklistId="<?php echo $staffCheckList->id ?>"  onclick="toggleMainCheckbox(this,'Global_checklist')" style="width: 44px;"></span>
        </label>         
                       <button style="margin-left: 62px;margin-top: -42px; width: 90%;    padding-top: 10px;padding-bottom: 10px;" class="accordion-button text-lightblack collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accor_fill<?php echo $i; ?>"  aria-controls="accor_fill1">
                        <h6 class="text-lightblack w-100"><?php echo  ($staffCheckList->deadline_time !='' ? "[".date("g:i A", strtotime($staffCheckList->deadline_time))."]" : '') ; ?>  <?php echo $staffCheckList->title ?> </h6> 
                       
                        <?php if($staffCheckList->urlSystem !='') { ?>
                        <a href="<?php echo base_url('/'.$staffCheckList->urlSystem); ?>"><i class="ri-eye-fill align-bottom me-1 mx-3 fs-16 "style="color: #0440B0;" ></i></a>
                        <?php } ?>
                         <i class="ri-attachment-2 align-bottom me-1 mx-3 fs-16 " style="color: red;" onclick="showCheckListInfoModal(<?php echo $staffCheckList->id ?>)"></i>
                        </button>
                      </h2>
             <div id="accor_fill<?php echo $i; ?>" class="accordion-collapse collapse " aria-labelledby="accordionFillExample1" data-bs-parent="#accordionFill">
            <div class="accordion-body" style=" background-color: #f5f4f4;border: 1px solid #0065ff3b;">
              <span class="mx-5 text-black">  <?php echo $staffCheckList->descr ?></span>
                </div>
              </div>
            </div>
        </div>          
                          <?php }else{ $subtasks = json_decode($staffCheckList->subchecklists);  ?>
             <div class="accordion custom-accordionwithicon accordion-border-box" id="accordionFill">
                                              <div class="accordion-item">
                         <h2 class="accordion-header" id="accordionFillExample1">
                    <label class="switch mt-2 w-auto" style="margin-left: 13px;" ><input type="checkbox" <?php echo (isset($staffCheckList->is_completed) && $staffCheckList->is_completed == 1 ? 'checked' : '') ?>  class="success">
        <span class="slider checklistSlider" data-checklistId="<?php echo $staffCheckList->id ?>"  onclick="toggleMainCheckbox(this,'Global_checklist',false)"   style="width: 44px;"></span>
        
        </label>         
                       <button style="margin-left: 62px;margin-top: -42px; width: 90%;padding-top: 10px;padding-bottom: 10px;" class="accordion-button text-lightblack collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accor_fill<?php echo $i; ?>"  aria-controls="accor_fill1">
                        <h6 class="text-lightblack w-100  "><?php echo  ($staffCheckList->deadline_time !='' ? "[".date("g:i A", strtotime($staffCheckList->deadline_time))."]" : '') ; ?>  <?php echo $staffCheckList->title ?></h6> 

                        <?php if($staffCheckList->urlSystem !='') { ?>
                        <a href="<?php echo base_url('/'.$staffCheckList->urlSystem); ?>"><i class="ri-eye-fill align-bottom me-1 mx-3 fs-16 "style="color: #0440B0;" ></i></a>
                        <?php } ?>
                         <i class="ri-attachment-2 align-bottom me-1 mx-3 fs-16 " style="color: red;" onclick="showCheckListInfoModal(<?php echo $staffCheckList->id ?>)"></i>
                        
                        </button>
                      </h2>
             <div id="accor_fill<?php echo $i; ?>" class="accordion-collapse collapse " aria-labelledby="accordionFillExample1" data-bs-parent="#accordionFill">
            <div class="accordion-body" style=" background-color: #f5f4f4;border: 1px solid #0065ff3b;">
              <span class=" text-black">  <?php echo $staffCheckList->descr ?></span>
              <?php if(!empty($subtasks)) {  foreach($subtasks as $subtask) {  ?>  
            <div class="d-flex align-items-center">
                <div class="form-check form-check-success" style="width: 100%;padding-bottom: 10px;padding-top: 10px;">
        
        <div class="d-flex">
             <label class="switch  w-auto" ><input type="checkbox" <?php echo (isset($subtask->subchecklist_is_completed) && $subtask->subchecklist_is_completed == 1 ? 'checked' : '') ?>  class="success Ccheckbox ">
        <span class="slider checklistSlider" data-checklistId="<?php echo (isset($subtask->subChecklistId) ? $subtask->subChecklistId : '') ?>"  onclick="toggleCheckbox(this,'Global_subchecklist')" style="width: 44px;"></span>
        </label>
          <h6 class="text-lightblack w-auto mx-5 "><?php echo ($subtask->subchecklist_time !='' ? "[".date("H:i A", strtotime($subtask->subchecklist_time))."]" :'') ; ?></h6> 
          <h6 class="mb-0 text-lightblack" data-fulltext="<?php echo $subtask->subChecklistDescr; ?>"><?php echo $subtask->subChecklistDescr; ?></h6>
         
         
        
         </div>
           </div>
           </div>
           <?php  } } else { ?>
           <p>No Sub Task</p>
           <?php }  ?>
                </div>
              </div>
            </div>
        </div>
                 <?php } ?>
                                                    
                                                     <?php $i++; } } ?>
                                                      
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                       
                                    </div>
                                </div> <!-- .card-->
                            </div><!--end col-->
                        </div>
                        
                        
                         <?php } ?>  
                          <?php } ?>  
                        <?php } ?>  
                        
                        <div class="row">
                            <div class="col-xl-5 col-md-12 col-lg-12 col-sm-12 ">
                                </div>
                             <div class="col-xl-7 col-md-12 col-lg-12 col-sm-12">
                                <div class="card card-height-100" style="background-color:#ffff;">
                                    <div class="card-header align-items-center d-flex" style="background-color: #ffff;">
                                        <div class="flex-shrink-0 w-80 ">
                                           <i class="ri-file-list-fill align-bottom fw-semibold"></i><span class="fw-semibold"> My Tasks </span>
                                        </div>
                                         <div class="flex-shrink-0">
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#toDoListModal" class="btn btn-primary btn-sm fs-14 d-flex gap-2"><i class="ri-add-line align-bottom"></i><span class="d-none d-sm-block"> Add  Task</span></a>
                                        </div>
                                       
                                    </div><!-- end card header -->

                                    <div class="card-body d-flex flex-column">
                                        <div class="table-responsive table-card dashboardChecklist">
                                            <table class="table table-centered table-hover align-middle table-nowrap mb-0">
                                            <tbody>
                                                    <?php if(isset($todoListData) && !empty($todoListData)) { ?>
                                                    <?php $i=0; foreach($todoListData as $todoList) {   ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                               <div class="form-check form-check-success" style="width: 100%;">
     
                                                          <div class="d-flex">
         <label class="switch">
         <input type="checkbox" <?php echo (isset($todoList->is_completed) && $todoList->is_completed == 1 && $todoList->date == date('Y-m-d')  ? 'checked' : '') ?>  class="success">
      <span class="slider checklistSlider" data-checklistId="<?php echo $todoList->id ?>"  onclick="toggleTodoCheckbox(this)" style="width: 44px;"></span>
       </label>               <h6 class="mb-0 text-lightblack mx-3" data-fulltext="<?php echo $todoList->descr; ?>"><?php echo $todoList->descr; ?></h6>
                                                                   
                                                        
                                                        </div>
                                                        </div>
                                                            </div>
                                                            
                                                        </td>
                                                        </tr>

                                                     <?php $i++; } ?>
                                                       <?php } ?>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                       
                                    </div>
                                </div> <!-- .card-->
                            </div><!--end col-->
                            
                            </div>
                         <?php } ?>
                    </div>
                    
                     <div id="checklistInfoModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="myModalLabel">Attachments and Comments</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form id="attachmentUploadForm" enctype="multipart/form-data">
                                                        <div class="modal-body">
                                                     <div class="file-input-container">
                                                             <input type="file" id="userfile" name="userfile[]" class="form-control-file" multiple>
                                                        </div>
                                                        <input type="text" class="form-control mt-2" name="checklistComments" placeholder="Comments (Examples: details on why a task couldn’t be completed)" />
                                                       
                                                        <input type="hidden" id="checklistId" name="checklistId" value="">
                                                        </div>
                                                        </form>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                            <button type="button" class="btn btn-success uploadAttachmentButton">Upload</button>
                                                        </div>

                                                    </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div>

           <div id="toDoListModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="myModalLabel">Create todo list</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                        <input type="text" id="descrTodo" class="form-control mt-2" placeholder="Enter todo list" />
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                            <button type="button" class="btn btn-success " onclick="saveToDoList()">Save</button>
                                                        </div>

                                                    </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div>
                                            


<style>
.file-input-container {
            border: 1px dashed #ccc; /* Dotted border */
            text-align: center;
            padding: 20px;
        }
       
 .switch {
     position: relative;
    display: inline-block;
    width: 42px;
    height: 20px;
}

/* Hide default HTML checkbox */
.switch input {display:none;}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
 height: 14px;
    width: 14px;
    left: 3px;
    bottom: 3px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}


input.success:checked + .slider {
  background-color: #30a473;
}


input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(20px);
  -ms-transform: translateX(20px);
  transform: translateX(20px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
.truncated-text {
  display: inline-block;
  max-width: 96%; /* Adjust this value to control the truncation point */
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}
.truncated-text:hover {
  white-space: normal;
  max-width: none;
}
</style>

<!-- ============ Checklist page redesign (matches location screen) ============ -->
<style>
#checklistPage{position:relative;max-width:1320px;}

/* Page hero */
#checklistPage .cl-hero{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;}
#checklistPage .cl-hero-left{display:flex;align-items:center;gap:.75rem;}
#checklistPage .cl-hero-icon{width:46px;height:46px;border-radius:13px;display:grid;place-items:center;background:linear-gradient(135deg,#172153,#2a3a82);color:#ffd166;font-size:1.4rem;flex-shrink:0;}
#checklistPage .cl-hero-title{font-size:clamp(1.35rem,3vw,1.75rem);font-weight:700;color:#172153;margin:0;letter-spacing:-.02em;line-height:1.2;}
#checklistPage .cl-hero-name{background:linear-gradient(135deg,#6366f1,#06b6a4);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;}
#checklistPage .cl-hero-sub{margin:.1rem 0 0;color:#6b7280;font-size:.88rem;}
#checklistPage .cl-loc-chip{display:inline-flex;align-items:center;gap:.45rem;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:.55rem 1rem;font-size:.85rem;font-weight:600;color:#172153;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,.05);transition:border-color .2s,box-shadow .2s,transform .2s;}
#checklistPage .cl-loc-chip:hover{border-color:#6366f1;box-shadow:0 6px 18px rgba(99,102,241,.15);transform:translateY(-1px);}
#checklistPage .cl-loc-chip i.bxs-map{color:#06b6a4;font-size:1.05rem;}
#checklistPage .cl-loc-chev{color:#9ca3af;font-size:1.1rem;}

/* Section labels */
#checklistPage .cl-section-label{font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;margin-bottom:.85rem;}

/* App system tiles */
#checklistPage .card.card-animate{
    background:linear-gradient(145deg,#20254f 0%,#2c3270 100%)!important;
    border-radius:18px!important;border:1px solid rgba(255,255,255,.06);
    box-shadow:0 4px 14px rgba(16,24,64,.12);
    transition:transform .25s cubic-bezier(.4,0,.2,1),box-shadow .25s;
    position:relative;overflow:hidden;
}
#checklistPage .card.card-animate::after{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#6366f1,#06b6a4);transform:scaleX(0);transform-origin:left;transition:transform .3s;z-index:2;}
#checklistPage .card.card-animate:hover{transform:translateY(-4px);box-shadow:0 16px 34px rgba(16,24,64,.28);}
#checklistPage .card.card-animate:hover::after{transform:scaleX(1);}
#checklistPage .card.card-animate .card-body{padding:1.25rem 1.25rem 1.4rem;}
#checklistPage .card.card-animate .text-muted{color:#c7cbe6!important;font-size:.78rem;letter-spacing:.04em;}
#checklistPage .card.card-animate .flex-shrink-0 h4{margin-top:.35rem;}

/* White panels (Today's Checklist / role checklists / My Tasks) */
#checklistPage .card.card-height-100{
    border-radius:18px!important;border:0!important;
    box-shadow:0 2px 10px rgba(16,24,64,.06)!important;
    background:#fff!important;
}
#checklistPage .card.card-height-100 .card-header{
    background:#fff!important;border:0!important;border-radius:18px 18px 0 0;
    padding:1.1rem 1.3rem .4rem;
}
#checklistPage .card.card-height-100 .card-header .fw-semibold{color:#172153;font-size:1rem;}
#checklistPage .card.card-height-100 .card-header i.ri-file-list-fill{
    color:#6366f1;background:#eef0ff;width:32px;height:32px;border-radius:9px;
    display:inline-grid;place-items:center;font-size:1rem;margin-right:.25rem;
}
#checklistPage .card.card-height-100 .card-body{padding:.5rem 1.3rem 1.3rem;}

/* Buttons -> gradient pill (Create Task / Add Task) */
#checklistPage .btn-primary{
    background:linear-gradient(135deg,#6366f1,#8b5cf6)!important;border:0!important;
    border-radius:10px!important;font-weight:600;padding:.5rem .95rem;
    box-shadow:0 6px 16px -6px rgba(99,102,241,.6);transition:transform .2s,box-shadow .2s;
}
#checklistPage .btn-primary:hover{transform:translateY(-1px);box-shadow:0 10px 22px -6px rgba(99,102,241,.7);}

/* Accordion rows -> soft cards with hover */
#checklistPage .accordion.accordion-border-box{margin-bottom:.6rem;}
#checklistPage .accordion-item{border:1px solid #eef0f4!important;border-radius:14px!important;overflow:hidden;background:#fff;transition:box-shadow .2s,border-color .2s;}
#checklistPage .accordion-item:hover{box-shadow:0 4px 14px rgba(16,24,64,.07);border-color:#e0e3ee!important;}
#checklistPage .accordion-button{background:transparent!important;box-shadow:none!important;border-radius:14px!important;}
#checklistPage .accordion-button h6{font-weight:600;}
#checklistPage .accordion-button::after{display:none;}
#checklistPage .accordion-body{border-radius:0 0 14px 14px!important;}

/* Toggle slider accent -> theme teal */
#checklistPage input.success:checked + .slider{background-color:#06b6a4;}

/* My Tasks rows */
#checklistPage .table-hover > tbody > tr:hover > *{background-color:#f8f9ff;}

/* Empty checklist placeholder */
#checklistPage .table-card .table tbody:empty::after,
#checklistPage .dashboardChecklist .table tbody:empty::before{content:'';}

@media (max-width:575.98px){
    #checklistPage{margin-top:80px!important;}
    #checklistPage .cl-hero-sub{display:none;}
}
</style>

<script>
function showCheckListInfoModal(id){
 $("#checklistId").val(id);
 $("#checklistInfoModal").modal('show');
 

}

function toggleMainCheckbox(obj,tbaleName,mainc=true) {
            const checkboxes = $(obj).parents(".accordion-item").find('.Ccheckbox')
            let allChecked = true;
           console.log("checkboxes",checkboxes)
            checkboxes.each(function() {
                if (!$(this).prop("checked")) {
                    allChecked = false;
                    
                    return false; // Exit the loop early if a checkbox is not checked
                }
            });

            if (!allChecked && !mainc) {
                alert('Please complete the subtasks before completing the main task.');
                location.reload();
                return false;
            } 
            
            let checkbox = obj.previousElementSibling;
   let checklistId = obj.getAttribute("data-checklistid");
   let checklistStatus = (!checkbox.checked ? 1 : 0);
   
    $.ajax({
      type: "POST",
      enctype: 'multipart/form-data',
        url: "/checklist/updateCheckListForTodays",
        data: {"checklistStatus":checklistStatus,"checklistId":checklistId},
        success: function(data){
        
        }
    });
}


function toggleCheckbox(obj,tableName) {
   let checkbox = obj.previousElementSibling;
   let checklistId = obj.getAttribute("data-checklistid");
   let checklistStatus = (!checkbox.checked ? 1 : 0);
   
    $.ajax({
      type: "POST",
      enctype: 'multipart/form-data',
        url: "/checklist/markChecklistCompleted",
        data: {"checklistStatus":checklistStatus,"checklistId":checklistId,"tableName":tableName},
        success: function(data){
        
        }
    });
  
}


function toggleTodoCheckbox(obj) {
   let checkbox = obj.previousElementSibling;
   let checklistId = obj.getAttribute("data-checklistid");
   let checklistStatus = (!checkbox.checked ? 1 : 0);
   let tbaleName = 'Global_todoList';
    $.ajax({
      type: "POST",
      enctype: 'multipart/form-data',
        url: "/checklist/markChecklistCompleted",
        data: {"checklistStatus":checklistStatus,"checklistId":checklistId,"tableName":tbaleName},
        success: function(data){
        
        }
    });
  
}

function saveToDoList(){
      let descr =  $("#descrTodo").val();
      $.ajax({
        type: "POST",
        url: "/checklist/saveToDoList",
        data: {"listDescr":descr},
        success: function(data){
        $("#toDoListModal").modal('hide');
        location.reload();
        }
    }); 
}




$(document).ready(function () {
    $(".uploadAttachmentButton").on("click", function () {
        var formData = new FormData($("#attachmentUploadForm")[0]);
        $(".uploadAttachmentButton").html("Loading...");
        // Debugging: Output FormData object to console
        console.log(formData);

        $.ajax({
            type: "POST",
            url: "/checklist/uploadChecklistAttachment", // Replace with your controller's URL
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $("#checklistInfoModal").modal('hide');
                $(".uploadAttachmentButton").html("Upload");
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });
});

// Inject a friendly empty-state into any checklist/task panel that has no rows
$(document).ready(function () {
    $('#checklistPage .dashboardChecklist').each(function () {
        var $panel = $(this);
        var hasRows = $panel.find('.accordion-item, tbody tr').length > 0;
        if (!hasRows) {
            $panel.append(
                '<div class="cl-empty">' +
                '<i class="bx bx-check-circle"></i>' +
                '<p>All clear — nothing here right now.</p>' +
                '</div>'
            );
        }
    });
});
</script>

<style>
#checklistPage .cl-empty{text-align:center;padding:2.5rem 1rem;color:#94a3b8;}
#checklistPage .cl-empty i{font-size:2.6rem;display:block;margin-bottom:.4rem;color:#cbd5e1;}
#checklistPage .cl-empty p{margin:0;font-size:.9rem;}
</style>