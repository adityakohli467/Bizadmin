<div class="container-fluid mb-5" style="margin-top: 130px !important;">
   <div class="row">
                        <div class="col-12 tempDiv">
                            <div class="card">
                              <div class="card-header align-items-center d-flex">
                               <h4 class="card-title mb-0 flex-grow-1 text-faded"><i class="fa-solid fa-utensils"></i> Kitchen Production History</h4>   
                                  
                                   </div>  
                                
                                 <div class="card-body">
                                     <form action="<?php echo base_url('/Compliance/KitchenProduction/historyData') ?>" method="post">
                                <div class="col-md-10 mt-2 d-flex gap-3">
                                    
                       <div class="date col-md-4">             
                      <label class="form-label mb-0 fw-semibold">Date Range</label>
                      <input type="text" required class="form-control flatpickr-input" data-provider="flatpickr" name="date_range" data-date-format="d M, Y" data-range-date="true" placeholder="Select date range" readonly="readonly">         
                      <small>Select from and to date to view the history (Weekly range recommended)</small>
                      </div>
                       <div class="date col-md-4">  
                         <label class="form-label mb-0 fw-semibold">Site</label>
                       <select class="form-select siteDropdown" name="site_id">
                                             <option> Select Site</option>
                                                <?php if(!empty($site_detail)) { $count = 0; foreach($site_detail as $sites) { $selected = ($count == 0 ? 'selected' : ''); ?>
                                                <option <?php echo $selected; ?> class="dropdown-item" href="#" value="<?php echo $sites['id'] ?>"><?php echo $sites['site_name'] ?></option>
                                                <?php $count++; } } ?>
                                       </select>
                                       </div>  
                      <div class="buttonTemp col-md-1 mt-3">
            <button type="submit" class="btn btn-success btn-label waves-effect waves-light"><i class="ri-check-double-line label-icon align-middle fs-16 me-2"></i> View</button>              
                    
                      </div>
                          
                     
                    </div>
                    </form>
                   
                                 
                                     
                                
                                 </div>
                                </div>
                                
                                </div>
                                
                                </div>
                                </div>
