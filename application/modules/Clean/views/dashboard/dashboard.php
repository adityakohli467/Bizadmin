<div class="container-fluid mb-5" style="margin-top: 130px !important;">
   <div class="row">
                        <div class="col-12 tempDiv">
                        
                            <div class="card">
                                
                                <div class="card-header d-flex align-items-center gap-2">
    <select class="form-select siteDropdown">
        <option> Select Site</option>
        <?php if(!empty($site_detail)) {
            $count = 0;
            foreach($site_detail as $sites) {
                $selected = ($count == 0 ? 'selected' : ''); ?>
                <option <?php echo $selected; ?> value="<?php echo $sites['id'] ?>">
                    <?php echo $sites['site_name'] ?>
                </option>
        <?php $count++; } } ?>
    </select>
    <!-- 🔍 Add Search Box -->
    <input type="text" id="taskSearch" class="form-control" placeholder="Search tasks...">
</div>

                                
                                <div class="d-none alert alert-success alert-dismissible alert-label-icon rounded-label shadow fade show successRecorded" role="alert">
                                  <i class="ri-notification-off-line label-icon"></i><strong>Success</strong>
                                   Record saved succesfully.
                                   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive table-card">
                                        <table class="table table-borderless table-hover table-nowrap align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="text-muted">
                                                    <th scope="col">Task Name</th>
                                                    <th scope="col">Record by</th>
                                                    <th scope="col">Record at</th>
                                                    <th scope="col">Entered by</th>
                                                    <th scope="col">Comments</th>
                                                    <th scope="col">Attachments</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                         <?php   
                                       if(isset($site_detail) && !empty($site_detail)){  ?>
                                       <?php  foreach($site_detail as $AllSites) { ?>
                                       <?php  $staffComments = unserialize($AllSites['staff_comments']);?>
                                        <?php  $prep_areas = json_decode($AllSites['prep_areas']); ?>
                                      
                                      <?php foreach($prep_areas as $prep_area) {  ?>
                                        <tbody class="prep_<?php echo $prep_area->id ?>  tbodySite <?php echo 'siteId_'.$AllSites['id'] ?>">
                                        <th colspan="9" class="text-black w-100 " style="background-color: #dff0fa;"> <b><?php echo $prep_area->prep_name; ?></b></th>     
                                       <?php if(isset($taskListForDash) && !empty($taskListForDash)){  ?>
                                       <?php  foreach($taskListForDash as $taskList) {  $taskId = $taskList['id']; ?> 
                                        <?php $rolesIdsAssigned = (isset($taskList['role_id']) ? unserialize($taskList['role_id']) : ''); ?>
                                       <?php $disabled = (isset($todaysEnteredData[$taskId]) ? 'disabled' : ''); ?>
                                       <?php $is_completed = (isset($todaysEnteredData[$taskId]) ? 'disabled' : ''); ?>
                                      <?php $completeText = (isset($todaysEnteredData[$taskId]) ? '⏱ Completed' : '⏰ Complete');  ?>  
                                       <?php $classComplete = (isset($todaysEnteredData[$taskId]) ? '⏱ btn-success' : '⏰ btn-orange');  ?> 
                                       <?php if($taskList['prep_id'] == $prep_area->id) { ?>
                                       <?php  if((isset($rolesIdsAssigned) && in_array($currentUserRoleId,$rolesIdsAssigned)) || ($this->ion_auth->in_group('admin') || $this->ion_auth->in_group('manager'))) { ?>
                                       <tr>
                                         <td><?php echo $taskList['task_name'] ?></td>
                                         <td><?php echo $taskList['task_time'] ?></td>
                                         <td class="capturedTime_<?php echo $taskId; ?>"><?php echo (isset($todaysEnteredData[$taskId]) && $todaysEnteredData[$taskId]['entered_time'] !='' ? $todaysEnteredData[$taskId]['entered_time'] : '') ?></td>
    <td><input type="text" class="form-control" <?php echo $disabled; ?> name="entered_by_<?php echo $taskId; ?>" value="<?php echo (isset($todaysEnteredData[$taskId]) && $todaysEnteredData[$taskId]['entered_by'] !='' ? $todaysEnteredData[$taskId]['entered_by'] : '') ?>" ></td> 
                                         <td>
                                                     <select class="form-select" name="staff_comments_<?php echo $taskId; ?>" <?php echo $disabled; ?>>
                                                         <option value=""> Select Comment</option>
                                                     <?php if(!empty($staffComments)) {
                                                     foreach($staffComments as $staffComment)  { ?>
                                        <?php if(isset($todaysEnteredData[$taskId]) && $todaysEnteredData[$taskId]['staff_comments'] == $staffComment){ ?>   
                                        <option value="<?php echo $staffComment; ?>" selected><?php echo $staffComment; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $staffComment; ?>"><?php echo $staffComment; ?></option>
                                        <?php }  ?>
                                                     
                                                     <?php } } ?>
                                                     </select>   
                                                    </td>
                                                    
                                                    
                                       <?php if(!$is_completed && $taskList['is_attchmentRequired']==1) { ?>
                                                     <td class="attchmentN_<?php echo $taskId; ?>">
                                                     <i class="ri-attachment-2 align-bottom me-1 mx-3 fs-16 " style="color: red;" onclick="showAttchmentModal(<?php echo $taskId ?>)"></i>
                                                     </td>
                                                     <?php }else if($is_completed && $taskList['is_attchmentRequired']==1) {   ?>
                                                     <td> <i class="mdi mdi-file-eye align-bottom me-1 mx-3 fs-16" style="color: red;" onclick="fetchAttachment(<?php echo $taskId ?>)"></i></td>
                                                      <?php }else { ?>
                                                      <td></td>
                                                      <?php } ?>             
                                                    
                                                    
                                    <td><button <?php echo $disabled; ?> name="complete_<?php echo $taskId; ?>" class="btn btn-sm <?php echo $classComplete; ?>" onclick="completeThisRow(<?php echo $AllSites['id']; ?>,<?php echo $taskList['prep_id']; ?>,<?php echo $taskId; ?>)"><?php echo $completeText; ?></button></td>
                                          </tr>
                                        <?php } ?>  
                                       <?php } ?>
                                       <?php } ?>
                                        <?php } ?>
                                        </tbody>   
                                        <?php } ?>
                                        
                                     <?php } } ?>    
                                       
                                        </table><!-- end table -->
                                    </div><!-- end table responsive -->
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div><!-- end col -->

                       <!-- end col -->
                      
                    </div>
                    
                     
       
         </div>
                                         
          <div id="attachmentCleaningModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                               <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title" id="myModalLabel">Attachments</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                         </div>
                      <div class="modal-body appendAttachment">
                       <div class="card">
                          <div class="card-body">
                                        <div class="swiper pagination-progress-swiper rounded">
                                            <div class="swiper-wrapper appendSwiperImages">
                                                
                                            </div>
                                            <div class="swiper-button-next bg-white shadow"></div>
                                            <div class="swiper-button-prev bg-white shadow"></div>
                                            <div class="swiper-pagination"></div>
                                        </div>
                                    </div><!-- end card-body -->
                            </div><!-- end card --> 
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
                            </div>
            <div id="tempAttachmentModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="myModalLabel">Attachments</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form id="attachmentUploadForm" enctype="multipart/form-data">
                                                        <div class="modal-body">
                                                            <label> Select multiple images and upload</label>
                                                     <div class="file-input-container">
                                                             <input type="file" id="userfile" name="userfile[]" class="form-control-file" multiple>
                                                        </div>
                                                        <!--<input type="text" class="form-control mt-2" name="checklistComments" placeholder="Comments (Examples: details on why a task couldn’t be completed)" />-->
                                                       
                                                        <input type="hidden" id="task_id" name="task_id" value="">
                                                        </div>
                                                        </form>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                            <button type="button" class="btn btn-success uploadAttachmentButton">Upload</button>
                                                        </div>

                                                    </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div>                                  
                  
                            
                                            <script>
function completeThisRow(siteId,prepId,taskId){

    let currentTime = getCurrentTime();
    let enteredBy = $('[name="entered_by_' + taskId + '"]').val();
    
    if ($('.attchmentN_'+taskId).length > 0) {
     alert("Please attach a photo before completing this record");
     return false;
     } 
    if(enteredBy==''){
    alert("Please enter your name  in Entered By field to complete"); 
    return false;
  } 
    
    
    $(".capturedTime_"+ taskId).html(currentTime)
     
        let data = [
        {
        task_id: taskId,
        site_id: siteId,
        prep_id: prepId,
        entered_time: currentTime,
        entered_by: $('[name="entered_by_' + taskId + '"]').val(),
        staff_comments: $('[name="staff_comments_' + taskId + '"]').val(),
    },
];

$('[name="entered_by_' + taskId + '"],[name="complete_' + taskId + '"], [name="staff_comments_' + taskId + '"]').prop('disabled', true);
    $('[name="complete_' + taskId + '"]').html('Completed');
     $('[name="complete_' + taskId + '"]').removeClass('btn-orange'); $('[name="complete_' + taskId + '"]').addClass('btn-success');
    
formData = JSON.stringify(data);
       
           $.ajax({
            type: 'POST',
            url: '/Clean/home/saveDashboardData',
            data: formData,
            success: function (response) {
              $(".successRecorded").removeClass("d-none");
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        }); 
    
}

function showAttchmentModal(id){
 $("#task_id").val(id);
 $("#tempAttachmentModal").modal('show');
}



$(document).ready(function(){
   
  
  $(".uploadAttachmentButton").on("click", function () {
        var formData = new FormData($("#attachmentUploadForm")[0]);
        $(".uploadAttachmentButton").html("Loading...");
        // Debugging: Output FormData object to console
        console.log(formData);

        $.ajax({
            type: "POST",
            url: "/Clean/home/uploadTemperatureAttachment", // Replace with your controller's URL
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $("#tempAttachmentModal").modal('hide');
                $(".uploadAttachmentButton").html("Save");
               let className = 'attchmentN_' + $("#task_id").val();
               $('.' + className).removeClass(className);
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });
    

 let selectedSite = localStorage.getItem('selectedSiteCleanDashBoard');
   
    if(selectedSite =='' || selectedSite == undefined){
      selectedSite = $(".siteDropdown").val();   
    }
    $(".siteDropdown").val(selectedSite);
   
    $(".tbodySite").addClass('d-none');
    $(".siteId_"+selectedSite).removeClass('d-none');
     $(".siteDropdown").on('change',function(){
      let selectedSite = $(this).val();  
      localStorage.setItem('selectedSiteCleanDashBoard',selectedSite);
      $(".tbodySite").addClass('d-none');
      $(".siteId_"+selectedSite).removeClass('d-none');
    });  
    
    
     $('#taskSearch').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('.tbodySite tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // Optional: Reset search when site changes
    $('.siteDropdown').on('change', function () {
        $('#taskSearch').val('');
        $('.tbodySite tr').show(); // show all rows again
    });
    
})

 function fetchAttachment(taskId){
     let orzName =  "<?php echo $this->session->userdata('tenantIdentifier'); ?>"
       $.ajax({
            type: "POST",
            url: "/Clean/home/fetchAttachmentUploadedToday", 
            data: 'task_id='+taskId,
            success: function (response) {
                let result= JSON.parse(response);
                console.log(result)
           result.forEach(function (filename) {
           let imageUrl = "/uploaded_files/"+orzName+"/Clean/CleaningAttachments/" + filename;
           let slide = '<div class="swiper-slide">' +
                    '<img src="' + imageUrl + '" alt="" class="img-fluid" style="width: 100%;" />' +
                    '</div>';
            console.log("slide",slide)        
             $('.appendSwiperImages').append(slide);
           });
          },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
         $("#attachmentCleaningModal").modal('show');
    
}





</script>