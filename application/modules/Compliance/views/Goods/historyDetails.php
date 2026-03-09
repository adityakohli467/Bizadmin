<style>
.table td {
    width: 10%;
}
.fixed-table-header {
    position: sticky;
    top: 0;
    z-index: 999;
    background-color: #fff;
}
.loading {
    background-color: #f0f0f0;
    cursor: not-allowed;
}
</style>

<div class="container-fluid mb-5" style="margin-top: 130px !important;">
    <div class="row">
        <div class="col-12">
             <small>* This form is auto save, so just enter data it will be saved</small>
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1 text-faded">
                      Incoming / allocated stock checklistn historical record
                    </h4>
                    
                     <a href="#" readonly class="btn btn-success waves-effect btn-label waves-light">
                        <i class="ri-check-double-line label-icon align-middle fs-16 me-2"></i><span>Save</span>
                    </a>
                    <a href="/Compliance/Goods/home/history" class="btn bg-orange waves-effect btn-label waves-light">
                        <i class="ri-reply-fill label-icon align-middle fs-16 me-2"></i><span>Back</span>
                    </a>
                   
                </div>
                <div class="card-body">
                    <?php if (isset($weeklyHistoryData) && !empty($weeklyHistoryData)) { ?>
                        <div class="table-responsive table-card">
                            <form id="goodsHistoryForm" action="#" method="post">
                                <input type="hidden" name="dateRange" value="<?php echo htmlspecialchars($dateRange); ?>">
                                <input type="hidden" name="site_id" value="<?php echo htmlspecialchars($site_id); ?>">
                                <table class="table table-borderless table-hover table-nowrap align-middle mb-0 table-bordered goodsViewT">
                                    <thead class="table-light fixed-table-header">
                                        <tr class="text-muted">
                                            <th scope="col">Supplier Name</th>
                                            <?php foreach ($uniqueDates as $dateToFind) { ?>
                                                <th scope="col"><?php echo date('d-m-Y', strtotime($dateToFind)); ?></th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <?php if (isset($site_detail) && !empty($site_detail) && isset($prep_detail) && !empty($prep_detail)) { ?>
                                        <?php foreach ($site_detail as $site) { ?>
                                            <?php foreach ($prep_detail as $prep_area) { ?>
                                                <?php if ($prep_area['site_id'] == $site['id']) { ?>
                                                    <tbody class="tbodySite">
                                                        <tr>
                                                            <th colspan="<?php echo count($uniqueDates) + 1; ?>" class="text-black w-100" style="background-color: #dff0fa;">
                                                                <b><?php echo htmlspecialchars($prep_area['prep_name']); ?> (Site: <?php echo htmlspecialchars($site['site_name']); ?>)</b>
                                                            </th>
                                                        </tr>
                                                        <?php foreach ($suppliers as $supplier) { ?>
                                                            <?php if ($supplier['prep_id'] == $prep_area['id'] && ($supplier['site_id'] == $site['id'] || $supplier['site_id'] == 0)) { ?>
                                                                <tr class="rowData" data-supplier-id="<?php echo $supplier['id']; ?>">
                                                                    <td><strong><?php echo htmlspecialchars($supplier['supplier_name']); ?></strong></td>
                                                                    <?php foreach ($uniqueDates as $dateToFind) { ?>
                                                                        <td>

                                                                            
                                                                            <!-- Invoice No -->
                                                                            <input type="text"
                                                                                   name="invoice_no_<?php echo $site['id']; ?>_<?php echo $prep_area['id']; ?>_<?php echo $supplier['id']; ?>_<?php echo $dateToFind; ?>"
                                                                                   value="<?php echo isset($weeklyHistoryData[$dateToFind][$supplier['id']]['invoice_no']) ? htmlspecialchars($weeklyHistoryData[$dateToFind][$supplier['id']]['invoice_no']) : ''; ?>"
                                                                                   class="form-control goods-field mb-2"
                                                                                   data-supplier-id="<?php echo $supplier['id']; ?>"
                                                                                   data-date="<?php echo $dateToFind; ?>"
                                                                                   data-prep-id="<?php echo $prep_area['id']; ?>"
                                                                                   data-field="invoice_no"
                                                                                   placeholder="Invoice No.">
                                                                            
                                                                            <!-- Temp -->
                                                                            <input type="text"
                                                                                   name="temp_<?php echo $site['id']; ?>_<?php echo $prep_area['id']; ?>_<?php echo $supplier['id']; ?>_<?php echo $dateToFind; ?>"
                                                                                   value="<?php echo isset($weeklyHistoryData[$dateToFind][$supplier['id']]['temp']) ? htmlspecialchars($weeklyHistoryData[$dateToFind][$supplier['id']]['temp']) : ''; ?>"
                                                                                   class="form-control goods-field mb-2"
                                                                                   data-supplier-id="<?php echo $supplier['id']; ?>"
                                                                                   data-date="<?php echo $dateToFind; ?>"
                                                                                   data-prep-id="<?php echo $prep_area['id']; ?>"
                                                                                   data-field="temp"
                                                                                   placeholder="Temp.">
                                                                            
                                                                            <!-- Comments -->
                                                                            <input type="text"
                                                                                   name="comments_<?php echo $site['id']; ?>_<?php echo $prep_area['id']; ?>_<?php echo $supplier['id']; ?>_<?php echo $dateToFind; ?>"
                                                                                   value="<?php echo isset($weeklyHistoryData[$dateToFind][$supplier['id']]['comments']) ? htmlspecialchars($weeklyHistoryData[$dateToFind][$supplier['id']]['comments']) : ''; ?>"
                                                                                   class="form-control goods-field mb-2"
                                                                                   data-supplier-id="<?php echo $supplier['id']; ?>"
                                                                                   data-date="<?php echo $dateToFind; ?>"
                                                                                   data-prep-id="<?php echo $prep_area['id']; ?>"
                                                                                   data-field="comments"
                                                                                   placeholder="Comments">
                                                                            
                                                                            <!-- Received By -->
                                                                            <input type="text"
                                                                                   name="received_by_<?php echo $site['id']; ?>_<?php echo $prep_area['id']; ?>_<?php echo $supplier['id']; ?>_<?php echo $dateToFind; ?>"
                                                                                   value="<?php echo isset($weeklyHistoryData[$dateToFind][$supplier['id']]['received_by']) ? htmlspecialchars($weeklyHistoryData[$dateToFind][$supplier['id']]['received_by']) : ''; ?>"
                                                                                   class="form-control goods-field mb-2"
                                                                                   data-supplier-id="<?php echo $supplier['id']; ?>"
                                                                                   data-date="<?php echo $dateToFind; ?>"
                                                                                   data-prep-id="<?php echo $prep_area['id']; ?>"
                                                                                   data-field="received_by"
                                                                                   placeholder="Received By">
                                                                            
                                                                            <!-- Signature -->
                                                                            <input type="text"
                                                                                   name="signature_<?php echo $site['id']; ?>_<?php echo $prep_area['id']; ?>_<?php echo $supplier['id']; ?>_<?php echo $dateToFind; ?>"
                                                                                   value="<?php echo isset($weeklyHistoryData[$dateToFind][$supplier['id']]['signature']) ? htmlspecialchars($weeklyHistoryData[$dateToFind][$supplier['id']]['signature']) : ''; ?>"
                                                                                   class="form-control goods-field"
                                                                                   data-supplier-id="<?php echo $supplier['id']; ?>"
                                                                                   data-date="<?php echo $dateToFind; ?>"
                                                                                   data-prep-id="<?php echo $prep_area['id']; ?>"
                                                                                   data-field="signature"
                                                                                   placeholder="Signature">
                                                                        </td>
                                                                    <?php } ?>
                                                                </tr>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </tbody>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tbody>
                                            <tr>
                                                <td colspan="<?php echo count($uniqueDates) + 1; ?>" class="text-center">No sites or prep areas found.</td>
                                            </tr>
                                        </tbody>
                                    <?php } ?>
                                </table>
                            </form>
                        </div>
                    <?php } else { ?>
                        <h3 class="text-black">No result found for this date range/site</h3>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.custom-toggle').on('click', function() {
        $('.goodsViewT').toggleClass('d-none');
    });

    // Handle input changes for all goods fields
    $('.goods-field').on('change', function() {
        var $input = $(this);
        var supplier_id = $input.data('supplier-id');
        var date_entered = $input.data('date');
        var prep_id = $input.data('prep-id');
        var field = $input.data('field');
        var value = $input.val();

        // Disable all inputs to prevent concurrent updates
        $('.goods-field').prop('disabled', true).addClass('loading');

        // Prepare AJAX data
        var ajaxData = {
            supplier_id: supplier_id,
            date_entered: date_entered,
            prep_id: prep_id,
            location_id: '<?php echo isset($site_detail[0]['location_id']) ? $site_detail[0]['location_id'] : ''; ?>'
        };
        
        // Add the specific field being updated
        ajaxData[field] = value;

        // AJAX request to update data
        $.ajax({
            url: '/Compliance/Goods/home/updateHistory',
            type: 'POST',
            data: ajaxData,
            success: function(response) {
                var res = JSON.parse(response);
                if (res.status === 'success') {
                    // Optional: Show success indicator
                    $input.addClass('border-success');
                    setTimeout(function() {
                        $input.removeClass('border-success');
                    }, 1000);
                } else {
                    alert('Error: ' + res.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('An error occurred while updating the data.');
            },
            complete: function() {
                // Re-enable all inputs after the request completes
                $('.goods-field').prop('disabled', false).removeClass('loading');
            }
        });
    });
});
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