<div class="container-fluid mb-5" style="margin-top: 130px !important;">
   <div class="row">
     <div class="col-12 tempDiv">
    <!--<a class="btn btn-danger btn-sm mb-2 " href="/Compliance/Home/index/<?php echo $this->session->userdata('system_id'); ?>">Tasks</a> -->
   <div class="d-flex flex-wrap gap-2 w-100 mb-3">

  <a class="btn btn-primary d-flex align-items-center gap-1" 
     href="<?php echo base_url('/Compliance/Cake/Cakehome'); ?>" 
     data-bs-toggle="tooltip" title="View Best Before Dashboard">
    <i class="fa-solid fa-cake-candles"></i> Best Before Dashboard
  </a>

  <a class="btn btn-danger d-flex align-items-center gap-1" 
     href="<?php echo base_url('/Compliance/Waste/Home'); ?>" 
     data-bs-toggle="tooltip" title="Manage Waste Records">
    <i class="fa-solid fa-trash-can"></i> Waste Management
  </a>

  <a class="btn btn-secondary d-flex align-items-center gap-1" 
     href="<?php echo base_url('/Compliance/Sanitation/Home'); ?>" 
     data-bs-toggle="tooltip" title="Sanitation Compliance">
    <i class="fa-solid fa-soap"></i> Sanitation
  </a>

  <a class="btn btn-success d-flex align-items-center gap-1" 
     href="<?php echo base_url('/Compliance/Goods/Home'); ?>" 
     data-bs-toggle="tooltip" title="Incoming Goods Checks">
    <i class="fa-solid fa-truck"></i> Incoming Goods
  </a>

  <a class="btn btn-warning d-flex align-items-center gap-1" 
     href="<?php echo base_url('/Compliance/KitchenProduction/Home'); ?>" 
     data-bs-toggle="tooltip" title="Kitchen Production">
    <i class="fa-solid fa-utensils"></i> Kitchen Production
  </a>

</div>
   
    <div class="card">
     <div class="card-header align-items-center d-flex">
     <h4 class="card-title mb-0 flex-grow-1 text-faded"><a href="<?php echo base_url('Temp/home/tempHistory') ?>">🌡</a> Best Before 🗓</h4>
                                    <div class="flex-shrink-0">
                                     <select class="form-select siteDropdown">
                                             <option> Select Site</option>
                                                <?php if(!empty($site_detail)) { $count =0; foreach($site_detail as $sites) { $selected = ($count == 0 ? 'selected' : ''); ?>
                                                <option <?php echo $selected; ?> class="dropdown-item" href="#" value="<?php echo $sites['id'] ?>"><?php echo $sites['site_name'] ?></option>
                                                <?php $count++; } } ?>
                                       </select>
                                    </div>
                                    <a id="saveBtn" class="btn btn-success mx-4" onclick="handleSaveClick(this)"><i class='fas fa-save' ></i> Save</a>

                                </div><!-- end card header -->
                                
                                <div class="d-none alert alert-success alert-dismissible alert-label-icon rounded-label shadow fade show tempSuccessRecorded" role="alert">
                                  <i class="ri-notification-off-line label-icon"></i><strong>Success</strong>
                                   Value recorded succesfully.
                                   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive table-card">
                                        <table class="table table-borderless table-hover table-nowrap align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="text-muted">
                                                    <th scope="col">Product Name</th>
                                                    <th scope="col">No Of cakes</th>
                                                    <th scope="col">Best Before</th>
                                                    <th scope="col">Entered by</th>
                                                   
                                                </tr>
                                            </thead>
                                         <?php   
                                       if(isset($site_detail) && !empty($site_detail)){  ?>
                                       <?php  foreach($site_detail as $AllSites) { ?>
                                       <?php  $staffcComments = unserialize($AllSites['staff_comments']);?>
                                        <?php  $prep_areas = json_decode($AllSites['prep_areas']); ?>
                                      
                                      <?php foreach($prep_areas as $prep_area) {  ?>
                                        <tbody class="prep_<?php echo $prep_area->id ?>  tbodySite <?php echo 'siteId_'.$AllSites['id'] ?>">
                                        <th colspan="9" class="text-black w-100 " style="background-color: #dff0fa;"> <b><?php echo $prep_area->prep_name; ?></b></th>     
                                       <?php if(isset($products) && !empty($products)){  ?>
                                       <?php  foreach($products as $product) {   $productId = $product['id']; ?> 
                                       <?php $disabled = (isset($todaysEnteredData[$productId]) ? 'disabled' : ''); ?>
                                       <?php $is_completed = (isset($todaysEnteredData[$productId]) ? 'disabled' : ''); ?>
                                     
                                    
                                       <?php if($product['prep_id'] == $prep_area->id) { ?>
                                       <tr>
      <td><?php echo $product['product_name'] ?></td>
      <td>
  <input type="text" class="form-control auto-save" 
         data-product-id="<?= $productId ?>" 
         data-field="no_of_cake"
         value="<?= isset($todaysEnteredData[$productId]) ? $todaysEnteredData[$productId]['no_of_cake'] : '' ?>">
</td>

<?php
$bestBeforeRaw = isset($todaysEnteredData[$productId]['best_before']) 
                 ? $todaysEnteredData[$productId]['best_before'] 
                 : '';

$bestBefore = !empty($bestBeforeRaw) 
              ? date('d M, Y', strtotime($bestBeforeRaw)) 
              : '';
?>
<td>
  <div class="input-group">
    <input type="text" 
           class="form-control auto-save dash-filter-picker shadow flatpickr-input" 
           id="best_before_<?= $productId ?>"
           data-product-id="<?= $productId ?>" 
           data-field="best_before"
           value="<?= $bestBefore ?>"
           data-provider="flatpickr" 
           data-date-format="d M, Y" 
           readonly="readonly">

    <div class="input-group-text bg-dark border-dark text-white calendar-trigger" 
         data-target="#best_before_<?= $productId ?>">
      <i class="ri-calendar-2-line"></i>
    </div>
  </div>
</td>





<td>
  <input type="text" class="form-control auto-save" 
         data-product-id="<?= $productId ?>" 
         data-field="entered_by"
         value="<?= isset($todaysEnteredData[$productId]['entered_by']) ? $todaysEnteredData[$productId]['entered_by'] : '' ?>">
</td>

                                  </tr>
                                       <?php } ?>
                                       <?php } ?>
                                        <?php } ?>
                                        </tbody>   
                                        <?php } ?>
                                        
                                     <?php } } ?>    
                                       
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                      
                  
                    </div>
                    
                     
       
         </div>
                       
        <script>
$(document).on('blur', '.auto-save', function() {
    const productId = $(this).data('product-id');
    const field = $(this).data('field');
    const value = $(this).val();
    let prep = $(".siteDropdown").val();

    $.ajax({
        url: "<?= base_url('Compliance/Cake/Cakehome/saveCakeRecord') ?>",
        method: "POST",
        data: {
            product_id: productId,
            field: field,
            prep: prep,
            value: value
        },
        success: function(response) {
            console.log("Saved:", response);
        },
        error: function(err) {
            console.error("Error saving data", err);
        }
    });
});
</script>
 
                            
                            
                                            <script>



$(".siteDropdown").on('change',function(){
    let siteId = $(this).val();
    localStorage.setItem('selectedSiteDashBoard',siteId);
$(".tbodySite").each(function(index, element) {
    if (!$(element).hasClass("d-none")) {
        console.log($(element).val());
        $(element).addClass("d-none");
    }
});
console.log(".siteId"+siteId)
$(".siteId_"+siteId).removeClass("d-none");
})

$(document).ready(function(){
    
    let siteId = localStorage.getItem('selectedSiteDashBoard');
    console.log("siteId",siteId)
    if(siteId =='' || siteId == undefined){
      siteId = $(".siteDropdown").val();   
    }
    $(".siteDropdown").val(siteId);
    
    $(".tbodySite").each(function(index, element) {
    if (!$(element).hasClass("d-none")) {
        console.log($(element).val());
        $(element).addClass("d-none");
    }
});
    
  $(".siteId_"+siteId).removeClass("d-none");  
  
    
})

  function handleSaveClick(obj) {
     $(obj).html('Saving...');
    
    setTimeout(() => {
      $(obj).html('Save');
    }, 1000);
  }





</script>