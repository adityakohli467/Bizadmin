
<style>
.table td {
    width: 10%; 
}
/* CSS to make the table header fixed */
.fixed-table-header {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    z-index: 999;
    background-color: #fff; /* Background color of the fixed header */
}
</style>

<div class="container-fluid mb-5" style="margin-top: 130px !important;">
   <div class="row">
       <div class="col-12 tempDiv">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-shrink-0"></div>
                    <h4 class="card-title mb-0 flex-grow-1 text-faded">
                        <button type="button" class="btn btn-success waves-effect waves-light shadow-none custom-toggle d-none" data-bs-toggle="button" aria-pressed="false">
                            <span class="icon-on"><i class="ri-subtract-line align-bottom me-1"></i> View All</span> 
                            <span class="icon-off"><i class="ri-add-line align-bottom me-1"></i>View Temp</span>
                        </button>
                    </h4>
                    <a href="/Temp/home/sliceTempHistory" class="btn bg-orange waves-effect btn-label waves-light">
                        <i class="ri-reply-fill label-icon align-middle fs-16 me-2"></i><span>Back</span>
                    </a>
                </div><!-- end card header -->
                <div class="card-body">
                    <?php if(isset($weeklyTempData) && !empty($weeklyTempData)) {  ?>
                    <div class="table-responsive table-card">
                        <?php $dateCount = 0; foreach($uniqueDates as $dateToFind) { ?>
                            <?php 
                            $isDateExist = array_filter($weeklyTempData, function ($dayData) use ($dateToFind) {
                                return $dayData['date_entered'] === $dateToFind;
                            });
                            ?>
                            <?php if (!empty($isDateExist)) { ?>
                                <h4 class="card-title mb-0 flex-grow-1 text-faded mt-4 mb-4 px-4 text-center allViewT">
                                    <?php echo date('d-m-Y', strtotime($dateToFind)); ?>
                                </h4>
                                <table class="table table-borderless table-hover table-nowrap align-middle mb-0 table-bordered allViewT">
                                    <?php if ($dateCount === 0) { $dateCount++; ?>
                                        <thead class="table-light fixed-table-header">
                                            <tr class="text-muted">
                                                <th scope="col">Product</th>
                                                <th scope="col">Internal Batch Code</th>
                                                <th scope="col">Start Slicing</th>
                                                <th scope="col">Finish Slicing</th>
                                                <th scope="col">Temp at End of Slicing</th>
                                                <th scope="col">Chilling Start Time</th>
                                                <th scope="col">Chilling Finish Time</th>
                                                <th scope="col">Temp at End of Chilling</th>
                                                <th scope="col">Comments</th>
                                                <th scope="col">Entered By</th>
                                                <th scope="col">Signature</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                    <?php } ?>
                                    <tbody>
                                        <?php if (isset($site_detail) && !empty($site_detail)) { 
                                            foreach ($site_detail as $AllSites) { 
                                                $prep_areas = json_decode($AllSites['prep_areas']);
                                                foreach ($prep_areas as $prep_area) { ?>
                                                    <tr>
                                                        <th colspan="12" class="text-black w-100" style="background-color: #07070b2e;">
                                                            <b><?php echo $prep_area->prep_name; ?></b>
                                                        </th>
                                                    </tr>
                                                    <?php 
                                                 
                                                    foreach ($isDateExist as $chillingTempData) { 
                                                        if ($chillingTempData['prep_id'] == $prep_area->id && $chillingTempData['site_id'] == $AllSites['id']) {
                                                          
                                                            
                                                            ?>
                                                            <tr class="rowData">
    <!-- Product ID (readonly, but still carries product_id) -->
    <td>
        <input type="text" 
               readonly 
               name="product_id[]" 
               value="<?php echo htmlspecialchars($chillingTempData['product_name'] ?? ''); ?>" 
               data-field="product_id" 
               data-row-id="<?php echo $chillingTempData['id']; ?>" 
               data-product-id="<?php echo $chillingTempData['product_id']; ?>"
               class="form-control auto-save">
    </td>

    <td>
        <input type="text" 
               name="internal_batch_code_allocated[]" 
               data-row-id="<?php echo $chillingTempData['id']; ?>" 
               value="<?php echo $chillingTempData['internal_batch_code_allocated'] ?? ''; ?>" 
               data-field="internal_batch_code_allocated" 
               data-product-id="<?php echo $chillingTempData['product_id']; ?>"
               class="form-control auto-save">
    </td>

    <td>
        <input type="time" 
               name="start_slicing[]" 
               value="<?php echo isset($chillingTempData['start_slicing']) ? date('H:i', strtotime($chillingTempData['start_slicing'])) : ''; ?>" 
               data-field="start_slicing" 
               data-product-id="<?php echo $chillingTempData['product_id']; ?>"
               data-row-id="<?php echo $chillingTempData['id']; ?>" 
               class="form-control auto-save">
    </td>

    <td>
        <input type="time" 
               name="time_finished_slicing[]" 
               value="<?php echo isset($chillingTempData['time_finished_slicing']) ? date('H:i', strtotime($chillingTempData['time_finished_slicing'])) : ''; ?>" 
               data-field="time_finished_slicing" 
               data-product-id="<?php echo $chillingTempData['product_id']; ?>"
               data-row-id="<?php echo $chillingTempData['id']; ?>" 
               class="form-control auto-save">
    </td>

    <td>
        <input type="number" step="0.01" 
               name="temp_of_product_at_end_of_slicing[]" 
               value="<?php echo $chillingTempData['temp_of_product_at_end_of_slicing'] ?? ''; ?>" 
               data-field="temp_of_product_at_end_of_slicing" 
               data-product-id="<?php echo $chillingTempData['product_id']; ?>"
               data-row-id="<?php echo $chillingTempData['id']; ?>" 
               class="form-control auto-save">
    </td>

    <td>
        <input type="time" 
               name="time_chilling_process_started[]" 
               value="<?php echo isset($chillingTempData['time_chilling_process_started']) ? date('H:i', strtotime($chillingTempData['time_chilling_process_started'])) : ''; ?>" 
               data-field="time_chilling_process_started" 
               data-product-id="<?php echo $chillingTempData['product_id']; ?>"
               data-row-id="<?php echo $chillingTempData['id']; ?>" 
               class="form-control auto-save">
    </td>

    <td>
        <input type="time" 
               name="time_chilling_process_finished[]" 
               value="<?php echo isset($chillingTempData['time_chilling_process_finished']) ? date('H:i', strtotime($chillingTempData['time_chilling_process_finished'])) : ''; ?>" 
               data-field="time_chilling_process_finished" 
               data-product-id="<?php echo $chillingTempData['product_id']; ?>"
               data-row-id="<?php echo $chillingTempData['id']; ?>" 
               class="form-control auto-save">
    </td>

    <td>
        <input type="number" step="0.01" 
               name="temp_of_product_at_end_of_chilling[]" 
               value="<?php echo $chillingTempData['temp_of_product_at_end_of_chilling'] ?? ''; ?>" 
               data-field="temp_of_product_at_end_of_chilling" 
               data-product-id="<?php echo $chillingTempData['product_id']; ?>"
               data-row-id="<?php echo $chillingTempData['id']; ?>" 
               class="form-control auto-save">
    </td>

    <td>
        <textarea name="comments[]" 
                  data-field="comments" 
                  data-product-id="<?php echo $chillingTempData['product_id']; ?>" 
                  data-row-id="<?php echo $chillingTempData['id']; ?>" 
                  class="form-control auto-save" rows="2"><?php echo $chillingTempData['comments'] ?? ''; ?></textarea>
    </td>

    <td>
        <input type="text" 
               name="entered_by[]" 
               value="<?php echo $chillingTempData['entered_by'] ?? ''; ?>" 
               data-field="entered_by" 
               data-row-id="<?php echo $chillingTempData['id']; ?>" 
               data-product-id="<?php echo $chillingTempData['product_id']; ?>"
               class="form-control auto-save">
    </td>

    <td>
        <input type="text" 
               name="signature[]" 
               value="<?php echo $chillingTempData['signature'] ?? ''; ?>" 
               data-field="signature" 
               data-row-id="<?php echo $chillingTempData['id']; ?>" 
               data-product-id="<?php echo $chillingTempData['product_id']; ?>"
               class="form-control auto-save">
    </td>

    <td>
        <button class="btn btn-sm btn-success" onclick="handleSaveClick(this)">Update</button>
    </td>
</tr>

                                                        <?php }
                                                    } ?>
                                                <?php }
                                            }
                                        } ?>
                                    </tbody>
                                </table>
                            <?php } ?>
                        <?php } ?>

                       
                    </div><!-- end table responsive -->
                    <?php } else {  ?>
                    <h3 class="text-black">No result found for this date range/site</h3>
                     <?php }   ?>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.custom-toggle').on('click', function() {
        $('.allViewT').toggleClass('d-none');
        $('.tempViewT').toggleClass('d-none');
    });
});
</script>
<script>
$(document).on('blur', '.auto-save', function() {
    const productId = $(this).data('product-id');
    const field = $(this).data('field');
    const value = $(this).val();
    let rowId = $(this).data('row-id');
   

    $.ajax({
        url: "<?= base_url('Temp/SliceTemp/Slicinghome/updateRecord') ?>",
        method: "POST",
        data: {
            product_id: productId,
            field: field,
            rowId: rowId,
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


 function handleSaveClick(obj) {
     
      const $button = $(obj);
    $button.html('<i class="fas fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
    
    setTimeout(() => {
      $button.html('<i class="fas fa-save"></i> Update').prop('disabled', false);
    }, 1000);
  }

</script>

<?php if (isset($can_edit_history) && !$can_edit_history): ?>
<style>
    .history-readonly input:not([type="hidden"]),
    .history-readonly select,
    .history-readonly textarea {
        background-color: #e9ecef !important;
        pointer-events: none !important;
    }
</style>
<script>
$(document).ready(function() {
    $('.card-body').addClass('history-readonly');
    $('.card-body input:not([type="hidden"]), .card-body select, .card-body textarea').prop('disabled', true);
    $('button, .btn').each(function() {
        var text = $(this).text().toLowerCase().trim();
        if (text.includes('update') || text.includes('save') || text.includes('add new') || text.includes('complete')) {
            $(this).hide();
        }
    });
});
</script>
<?php endif; ?>
