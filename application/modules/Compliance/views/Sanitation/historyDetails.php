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
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    
                    <a href="/Compliance/Sanitation/Home/history" class="btn bg-orange waves-effect btn-label waves-light">
                        <i class="ri-reply-fill label-icon align-middle fs-16 me-2"></i><span>Back</span>
                    </a>
                </div>
                <div class="card-body">
                    <?php if (isset($weeklyData) && !empty($weeklyData)): ?>
                        <div class="table-responsive table-card">
                            <form id="sanitationHistoryForm" action="#" method="post">
                                <input type="hidden" name="dateRange" value="<?php echo htmlspecialchars($dateRange); ?>">
                                <input type="hidden" name="site_id" value="<?php echo htmlspecialchars($site_id); ?>">
                                <table class="table table-borderless table-hover table-nowrap align-middle mb-0 table-bordered sanitationViewT">
                                    <thead class="table-light fixed-table-header">
                                        <tr class="text-muted">
                                            <th>Product Name</th>
                                            <?php foreach ($uniqueDates as $dateToFind): ?>
                                                <th><?php echo date('d-m-Y', strtotime($dateToFind)); ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <?php if (!empty($site_detail) && !empty($prep_detail)): ?>
                                        <?php foreach ($site_detail as $site): ?>
                                            <?php foreach ($prep_detail as $prep_area): ?>
                                                <?php if ($prep_area['site_id'] == $site['id']): ?>
                                                    <tbody class="tbodySite">
                                                        <th colspan="<?php echo count($uniqueDates) + 1; ?>" class="text-black w-100" style="background-color: #dff0fa;">
                                                            <b><?php echo htmlspecialchars($prep_area['prep_name']); ?> (Site: <?php echo htmlspecialchars($site['site_name']); ?>)</b>
                                                        </th>
                                                        <?php foreach ($products as $product): ?>
                                                            <?php if ($product['prep_id'] == $prep_area['id'] && ($product['site_id'] == $site['id'] || $product['site_id'] == 0)): ?>
                                                                <tr class="rowData" data-product-id="<?php echo $product['id']; ?>">
                                                                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                                                    <?php foreach ($uniqueDates as $dateToFind): ?>
                                                                        <td>
                                                                           
                                                                            <input type="text"
                                                                                   placeholder="Recorded by"
                                                                                   name="entered_by_<?php echo $site['id']; ?>_<?php echo $prep_area['id']; ?>_<?php echo $product['id']; ?>_<?php echo $dateToFind; ?>"
                                                                                   value="<?php echo isset($weeklyData[$dateToFind][$product['id']]) ? htmlspecialchars($weeklyData[$dateToFind][$product['id']]['entered_by']) : ''; ?>"
                                                                                   class="form-control auto-save mt-2"
                                                                                   data-field="entered_by"
                                                                                   data-product-id="<?php echo $product['id']; ?>"
                                                                                   data-date="<?php echo $dateToFind; ?>"
                                                                                   data-prep-id="<?php echo $prep_area['id']; ?>">
                                                                          
                                                                            <input type="text"
                                                                                   placeholder="Commenced"
                                                                                   name="commenced_<?php echo $site['id']; ?>_<?php echo $prep_area['id']; ?>_<?php echo $product['id']; ?>_<?php echo $dateToFind; ?>"
                                                                                   value="<?php echo isset($weeklyData[$dateToFind][$product['id']]) ? htmlspecialchars($weeklyData[$dateToFind][$product['id']]['commenced']) : ''; ?>"
                                                                                   class="form-control auto-save mt-2"
                                                                                   data-field="commenced"
                                                                                   data-product-id="<?php echo $product['id']; ?>"
                                                                                   data-date="<?php echo $dateToFind; ?>"
                                                                                   data-prep-id="<?php echo $prep_area['id']; ?>">
                                                                            <input type="text"
                                                                                   placeholder="Time completed"
                                                                                   name="time_completed_<?php echo $site['id']; ?>_<?php echo $prep_area['id']; ?>_<?php echo $product['id']; ?>_<?php echo $dateToFind; ?>"
                                                                                   value="<?php echo isset($weeklyData[$dateToFind][$product['id']]) ? htmlspecialchars($weeklyData[$dateToFind][$product['id']]['time_completed']) : ''; ?>"
                                                                                   class="form-control auto-save mt-2"
                                                                                   data-field="time_completed"
                                                                                   data-product-id="<?php echo $product['id']; ?>"
                                                                                   data-date="<?php echo $dateToFind; ?>"
                                                                                   data-prep-id="<?php echo $prep_area['id']; ?>">
                                                                            <input type="text"
                                                                                   placeholder="Completed as per MDS"
                                                                                   name="completed_mds_<?php echo $site['id']; ?>_<?php echo $prep_area['id']; ?>_<?php echo $product['id']; ?>_<?php echo $dateToFind; ?>"
                                                                                   value="<?php echo isset($weeklyData[$dateToFind][$product['id']]) ? htmlspecialchars($weeklyData[$dateToFind][$product['id']]['completed_mds']) : ''; ?>"
                                                                                   class="form-control auto-save mt-2"
                                                                                   data-field="completed_mds"
                                                                                   data-product-id="<?php echo $product['id']; ?>"
                                                                                   data-date="<?php echo $dateToFind; ?>"
                                                                                   data-prep-id="<?php echo $prep_area['id']; ?>">
                                                                            <textarea
                                                                                   placeholder="Comments"
                                                                                   name="comments_<?php echo $site['id']; ?>_<?php echo $prep_area['id']; ?>_<?php echo $product['id']; ?>_<?php echo $dateToFind; ?>"
                                                                                   class="form-control auto-save mt-2"
                                                                                   data-field="comments"
                                                                                   data-product-id="<?php echo $product['id']; ?>"
                                                                                   data-date="<?php echo $dateToFind; ?>"
                                                                                   data-prep-id="<?php echo $prep_area['id']; ?>">
                                                                                <?php echo isset($weeklyData[$dateToFind][$product['id']]) ? htmlspecialchars($weeklyData[$dateToFind][$product['id']]['comments']) : ''; ?>
                                                                            </textarea>
                                                                           
                                                                           
                                                                        </td>
                                                                    <?php endforeach; ?>
                                                                </tr>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tbody>
                                            <tr>
                                                <td colspan="<?php echo count($uniqueDates) + 1; ?>" class="text-center">No sites or prep areas found.</td>
                                            </tr>
                                        </tbody>
                                    <?php endif; ?>
                                </table>
                            </form>
                        </div>
                    <?php else: ?>
                        <h3 class="text-black">No result found for this date range/site</h3>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.custom-toggle').on('click', function() {
        $('.sanitationViewT').toggleClass('d-none');
    });

    // Initialize flatpickr for best_before
    flatpickr('.flatpickr-input', {
        dateFormat: 'Y-m-d',
        allowInput: true
    });

    // Handle input changes
    
        $('.auto-save').on('change', function() {
         if ($(this).hasClass('auto-save')) {
            const $input = $(this);
            const product_id = $input.data('product-id');
            const date_entered = $input.data('date');
            const prep_id = $input.data('prep-id');
            const field = $input.data('field');
            const value = $input.val();

            // $('.auto-save, .complete-task').prop('disabled', true).addClass('loading');

            $.ajax({
                url: '/Compliance/Sanitation/Home/updateSanitationHistory',
                type: 'POST',
                data: {
                    product_id: product_id,
                    date_entered: date_entered,
                    prep_id: prep_id,
                    location_id: '<?php echo $site_detail[0]['location_id']; ?>',
                    [field]: value
                },
                success: function(response) {
                    const res = JSON.parse(response);
                    if (res.status !== 'success') {
                        alert('Error: ' + res.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    alert('An error occurred while updating the data.');
                },
                complete: function() {
                    // $('.auto-save, .complete-task').prop('disabled', false).removeClass('loading');
                }
            });
        }
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