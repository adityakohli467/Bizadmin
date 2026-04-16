<style>
    .table-card td:first-child, .table-card th:first-child {
    padding-left: 12px;
}
.table-card td:last-child, .table-card th:last-child {
    padding-right: 12px;
}
</style>
<?php $idName = $table_name.'_id'; ?>
<div class="main-content">

    <div class="page-content">
               <?php $this->load->view('general/listpageTopBg'); ?>    
    <div class="container-fluid">
     <div class="row">
        <div class="col-lg-12">
            <div class="page-content-inner">
                <div class="card" id="userList">
                    <div class="card-header border-bottom-dashed">

                        <div class="row g-4 align-items-center">
                            <div class="col-sm">
                                <div>
                                    <h5 class="card-title mb-0"><?php echo $page_title; ?></h5>
                                </div>
                            </div>
                            <div class="col-sm-auto">
                                <div>
                                    <a class="btn btn-primary add-btn" href="<?php echo base_url(); ?>index.php/<?php echo $controller_add; ?>"><i class="ri-add-line align-bottom me-1"></i> Add New</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                   
                    <div class="card-body">
                       <div>
                             <?php if($this->session->flashdata('sucess_msg') != '') { ?>  
                            <div class='hideMeAlert'>
                                <p class="alert alert-success"><?php echo $this->session->flashdata('sucess_msg'); ?></p>
                            </div>
                            <?php } else if($this->session->flashdata('error_msg') != '') { ?>  
                            <div class='hideMeAlert'>
                                <p class="alert alert-danger"><?php echo $this->session->flashdata('error_msg'); ?></p>
                            </div>
                            <?php }else{} ?>
                        </div>
                           
                                
                                <table class="table table-striped nowrap align-middle" id="customerDataTable">
                                    <thead class="table-light">
                                        <tr>
                                            <?php foreach($table_columns as $cols){ ?>
                                            <th class="fs-13 <?php echo ($cols['sort'] == 1 ? 'sort' : 'no-sort'); ?>" ><?php echo $cols['column_title']; ?></th>
                                            <?php } ?>
                                            <th class="fs-13 no-sort text-center" width="200">Login Link</th>
                                            <th class="fs-13 no-sort text-center" width="200">Action</th>
                                        </tr>
                                    </thead>
                                    <?php if(!empty($record)){ ?>
                                    <tbody class="list form-check-all">
                                        <?php foreach($record as $row){ ?>
                                        
                                        <tr class="recordRow">
                                            <?php foreach($table_columns as $cols){ 
                                            $colName = $cols['column_name'];
                                            if($cols['column_title'] == 'Status'){
                                                if($row->$colName == 0){
                                                    $statusHtml = '<span class="badge badge-soft-danger">Disabled</span>';
                                                }else if($row->$colName == 1){
                                                    $statusHtml = '<span class="badge badge-soft-success">Enabled</span>';
                                                }else{
                                                    $statusHtml = '';
                                                }
                                            ?>
                                                <td class="fs-14"><?php echo $statusHtml; ?></td>
                                                <?php }else{ ?>
                                               
                                                <td class="fs-14"><?php echo $row->$colName; ?></td>
                                           <?php } } ?>
                                           <?php if(isset($row->tenant_identifier) && $row->tenant_identifier !=''){  ?>
                                           <td class="fs-14"><a href="https://bizadmin.com.au/<?php echo $row->tenant_identifier; ?>" target="_remote">https://bizadmin.com.au/<?php echo $row->tenant_identifier; ?></a></td>
                                            <?php }else{ ?>
                                            <td></td>
                                             <?php } ?>
                                            <td>
                                                <div class="d-flex gap-2 justify-content-center">
                                                    
                                                    
                                                    
                                                    <div class="view">
                                                        <a class="btn btn-sm btn-primary edit-item-btn" href="<?php echo base_url(); ?>index.php/<?php echo $controller_view."/".$row->$idName ?>">
                                                          View</a>
                                                    </div>
                                                    <?php if(isset($controller_viewMenu)) { ?>
                                                     <div class="view">
                                                        <a  class="btn btn-sm btn-info edit-item-btn" target="_blank" href="<?php echo base_url(); ?>index.php/<?php echo $controller_viewMenu."/".$row->$idName ?>">
                                                          Menu</a>
                                                    </div>
                                                    <?php }  ?>
                                                    <div class="edit">
                                                        <a class="btn btn-sm btn-success edit-item-btn" href="<?php echo base_url(); ?>index.php/<?php echo $controller_edit."/".$row->$idName ?>">
                                                      Edit</a>
                                                    </div>
                                                    <!--<div class="remove">-->
                                                    <!--  <button class="btn btn-sm btn-danger remove-item-btn" data-rel="delete" data-rel-id="<?php echo  $row->$idName ?>" data-bs-toggle="modal" data-bs-target="#deleteRecordModal">Remove</button>-->
                                                    <!--</div>-->
                                                    <?php if($table_name == 'organization_list'){ ?>
                                                    <div class="rerun">
                                                        <a class="btn btn-sm btn-warning" 
                                                            href="<?php echo base_url(); ?>index.php/organization/rerun_setup/<?php echo $row->$idName; ?>"
                                                            title="Re-run automated setup (schema import, seed data, config, folders)"
                                                            onclick="return confirm('Re-run setup for <?php echo htmlspecialchars($row->orz_name, ENT_QUOTES); ?>? This will re-import schema and re-seed data.');">
                                                            <i class="ri-refresh-line"></i> Re-run Setup
                                                        </a>
                                                    </div>
                                                    <div class="remove">
                                                        <button class="btn btn-sm btn-danger delete-orz-btn" 
                                                            data-orz-id="<?php echo $row->$idName; ?>" 
                                                            data-orz-name="<?php echo htmlspecialchars($row->orz_name, ENT_QUOTES); ?>">
                                                            <i class="ri-delete-bin-5-line"></i> Delete
                                                        </button>
                                                    </div>
                                                    <?php } ?>
                                                </div>
                                                    
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                    <?php } ?>
                                </table>
                                
                                <div class="noresult" <?php if(!empty($record)){ ?>style="display: none" <?php } else{ ?>style="display: block" <?php } ?> >
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sorry! No Result Found</h5>
                                        <p class="text-muted mb-0">We did not find any record for you search.</p>
                                    </div>
                                </div>
                               
                          
                           
                      
                       
                    </div>
                </div>
            </div>
        </div>
            <!--end col-->
     </div>
        <!--end row-->
       
        
        
    </div>
           
    </div>
       

        
    </div>
   
</div>
<input type="hidden" id="table_name" value="<?php echo $table_name; ?>">

<?php if($table_name == 'organization_list'){ ?>
<!-- PIN Verification Modal for Organization Delete -->
<div class="modal fade" id="deletePinModal" tabindex="-1" aria-labelledby="deletePinModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deletePinModalLabel">
                    <i class="ri-shield-keyhole-line"></i> Security Verification
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f06548,secondary:#f7b84b" style="width:80px;height:80px"></lord-icon>
                </div>
                <p class="text-center text-muted mb-3">
                    You are about to delete organization: <br>
                    <strong class="text-danger fs-15" id="deleteOrzNameDisplay"></strong>
                </p>
                <div class="mb-3">
                    <label for="deletePin" class="form-label d-flex justify-content-between align-items-center">
                        <span>Enter PIN <span class="text-danger">*</span></span>
                        <a href="javascript:void(0);" id="forgotPinLink" class="text-primary fs-12">Forgot PIN?</a>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ri-lock-2-line"></i></span>
                        <input type="password" class="form-control" id="deletePin" placeholder="Enter 4-digit PIN" maxlength="10" autocomplete="off">
                        <button class="btn btn-outline-secondary" type="button" id="togglePinVisibility">
                            <i class="ri-eye-line" id="pinEyeIcon"></i>
                        </button>
                    </div>
                    <div class="text-danger mt-1 fs-12" id="pinErrorMsg" style="display:none;"></div>
                    <div class="text-success mt-1 fs-12" id="pinSuccessMsg" style="display:none;"></div>
                </div>
                <input type="hidden" id="deleteOrzId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="verifyPinBtn">
                    <i class="ri-check-line"></i> Verify & Continue
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    var deleteModal = null;
    var currentOrzId = null;
    var currentOrzName = '';

    // Open PIN modal on delete button click
    $(document).on('click', '.delete-orz-btn', function(){
        currentOrzId = $(this).data('orz-id');
        currentOrzName = $(this).data('orz-name');
        
        $('#deleteOrzId').val(currentOrzId);
        $('#deleteOrzNameDisplay').text(currentOrzName);
        $('#deletePin').val('');
        $('#pinErrorMsg').hide();
        $('#pinSuccessMsg').hide();
        
        deleteModal = new bootstrap.Modal(document.getElementById('deletePinModal'));
        deleteModal.show();
    });

    // Toggle PIN visibility
    $('#togglePinVisibility').on('click', function(){
        var pinInput = $('#deletePin');
        var icon = $('#pinEyeIcon');
        if(pinInput.attr('type') === 'password'){
            pinInput.attr('type', 'text');
            icon.removeClass('ri-eye-line').addClass('ri-eye-off-line');
        } else {
            pinInput.attr('type', 'password');
            icon.removeClass('ri-eye-off-line').addClass('ri-eye-line');
        }
    });

    // Allow Enter key on PIN input
    $('#deletePin').on('keypress', function(e){
        if(e.which === 13) $('#verifyPinBtn').click();
    });

    // Verify PIN and then ask confirmation
    $('#verifyPinBtn').on('click', function(){
        var pin = $('#deletePin').val().trim();
        if(!pin){
            $('#pinErrorMsg').text('Please enter the PIN').show();
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="ri-loader-4-line ri-spin"></i> Verifying...');
        $('#pinErrorMsg').hide();

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>index.php/organization/verify_delete_pin',
            data: { pin: pin },
            dataType: 'json',
            success: function(resp){
                btn.prop('disabled', false).html('<i class="ri-check-line"></i> Verify & Continue');
                
                if(resp.success){
                    // Close PIN modal
                    deleteModal.hide();

                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Are you sure?',
                        html: '<p class="text-danger fw-bold">This will permanently delete ALL data for:</p>' +
                              '<p class="fs-16 fw-bold">' + currentOrzName + '</p>' +
                              '<ul class="text-start">' +
                              '<li>Organization database (ALL tables & data)</li>' +
                              '<li>Uploaded files & folders</li>' +
                              '<li>Database config entries</li>' +
                              '<li>Organization record from Super Admin</li>' +
                              '</ul>' +
                              '<p class="text-danger"><strong>This action CANNOT be undone!</strong></p>',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, DELETE Everything!',
                        cancelButtonText: 'No, Cancel',
                        customClass: {
                            confirmButton: 'btn btn-danger w-xs me-2 mt-2',
                            cancelButton: 'btn btn-secondary w-xs mt-2'
                        },
                        buttonsStyling: false
                    }).then(function(result){
                        if(result.isConfirmed){
                            // Show loading
                            Swal.fire({
                                title: 'Deleting Organization...',
                                html: 'Please wait while we remove all data for <b>' + currentOrzName + '</b>',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: function(){
                                    Swal.showLoading();
                                }
                            });

                            // Execute delete
                            $.ajax({
                                type: 'POST',
                                url: '<?php echo base_url(); ?>index.php/organization/delete_organization',
                                data: { orz_id: currentOrzId },
                                dataType: 'json',
                                success: function(delResp){
                                    if(delResp.success){
                                        Swal.fire({
                                            title: 'Deleted!',
                                            text: delResp.message,
                                            icon: 'success',
                                            confirmButtonClass: 'btn btn-primary mt-2',
                                            buttonsStyling: false
                                        }).then(function(){
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Partially Deleted',
                                            text: delResp.message,
                                            icon: 'warning',
                                            confirmButtonClass: 'btn btn-primary mt-2',
                                            buttonsStyling: false
                                        }).then(function(){
                                            location.reload();
                                        });
                                    }
                                },
                                error: function(){
                                    Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                                }
                            });
                        }
                    });
                } else {
                    $('#pinErrorMsg').text(resp.message).show();
                }
            },
            error: function(){
                btn.prop('disabled', false).html('<i class="ri-check-line"></i> Verify & Continue');
                $('#pinErrorMsg').text('Network error. Please try again.').show();
            }
        });
    });

    // Forgot PIN
    $('#forgotPinLink').on('click', function(){
        var link = $(this);
        link.text('Sending...');
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>index.php/organization/forgot_delete_pin',
            dataType: 'json',
            success: function(resp){
                link.text('Forgot PIN?');
                $('#pinSuccessMsg').text(resp.message).show();
                setTimeout(function(){ $('#pinSuccessMsg').fadeOut(); }, 5000);
            },
            error: function(){
                link.text('Forgot PIN?');
                $('#pinErrorMsg').text('Failed to send email. Try again.').show();
            }
        });
    });
})();
</script>
<?php } ?>

<script type="text/javascript">
$('.remove-item-btn').click(function(){
    var id = $(this).attr('data-rel-id');
    var thisRow = $(this).closest('.recordRow');
    var table_name = $('#table_name').val();
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
                url: "<?php echo base_url();?>index.php/general/record_delete",
                data:'id='+id+'&table_name='+table_name,
                success: function(data){
                //   location.reload();
                  if(data == 'deleted'){
                      $(thisRow).remove();
                  }
                }
            });
            
        
              
          }
      })
   
    
});
    $(document).ready(function () {
            
    <?php if(empty($employees)){ ?>
        $('#customerDataTable').DataTable({
            paging: false,
            info: false,
           "columnDefs": [ {
                  "targets"  : 'no-sort',
                  "orderable": false
                }]
        });
    <?php  }else{ ?>
        $('#customerDataTable').DataTable({
            pageLength: 100,
            lengthMenu: [0, 5, 10, 20, 50, 100, 200, 500],
           "columnDefs": [ {
                  "targets"  : 'no-sort',
                  "orderable": false
                }]
        });
    <?php  } ?>
});
</script> 
<?php 
$this->session->unset_userdata('sucess_msg');
$this->session->unset_userdata('error_msg');
?>