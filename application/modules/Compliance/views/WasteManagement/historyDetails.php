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
                    <h4 class="card-title mb-0 flex-grow-1 text-faded">
                        <button type="button" class="btn btn-success waves-effect waves-light shadow-none custom-toggle" data-bs-toggle="button">
                            <span class="icon-on"><i class="ri-subtract-line align-bottom me-1"></i> View All</span>
                            <span class="icon-off"><i class="ri-add-line align-bottom me-1"></i> View Waste</span>
                        </button>
                    </h4>
                    <a href="/Compliance/Waste/Home/history" class="btn bg-orange waves-effect btn-label waves-light">
                        <i class="ri-reply-fill label-icon align-middle fs-16 me-2"></i><span>Back</span>
                    </a>
                </div>
                <div class="card-body">
                    <?php if (isset($uniqueDates) && !empty($uniqueDates)) { ?>
                        <div class="table-responsive table-card">
                            <form id="wasteHistoryForm" action="#" method="post">
                                <input type="hidden" name="dateRange" value="<?php echo htmlspecialchars($dateRange); ?>">
                                <input type="hidden" name="site_id" value="<?php echo htmlspecialchars($site_id); ?>">
                                <table class="table table-borderless table-hover table-nowrap align-middle mb-0 table-bordered wasteViewT">
                                    <thead class="table-light fixed-table-header">
                                        <tr class="text-muted">
                                            <th scope="col">Product Name</th>
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
                                                        <th colspan="<?php echo count($uniqueDates) + 1; ?>" class="text-black w-100" style="background-color: #dff0fa;">
                                                            <b><?php echo htmlspecialchars($prep_area['prep_name']); ?> (Site: <?php echo htmlspecialchars($site['site_name']); ?>)</b>
                                                        </th>
                                                        <?php foreach ($products as $product) { ?>
                                                            <?php if ($product['prep_id'] == $prep_area['id'] && ($product['site_id'] == $site['id'] || $product['site_id'] == 0)) { ?>
                                                                <tr class="rowData" data-product-id="<?php echo $product['id']; ?>">
                                                                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                                                    <?php foreach ($uniqueDates as $dateToFind) { ?>
                                                                        <td>
                                                                            <input type="text"
                                                                                   name="wasteM_value_<?php echo $site['id']; ?>_<?php echo $prep_area['id']; ?>_<?php echo $product['id']; ?>_<?php echo $dateToFind; ?>"
                                                                                   value="<?php echo isset($weeklyWasteData[$dateToFind][$product['id']]) ? htmlspecialchars($weeklyWasteData[$dateToFind][$product['id']]['wasteM_value']) : ''; ?>"
                                                                                   class="form-control wasteM-value"
                                                                                   data-product-id="<?php echo $product['id']; ?>"
                                                                                   data-date="<?php echo $dateToFind; ?>"
                                                                                   data-prep-id="<?php echo $prep_area['id']; ?>">
                                                                            <input type="text"
                                                                                   name="entered_by_<?php echo $site['id']; ?>_<?php echo $prep_area['id']; ?>_<?php echo $product['id']; ?>_<?php echo $dateToFind; ?>"
                                                                                   value="<?php echo isset($weeklyWasteData[$dateToFind][$product['id']]) ? htmlspecialchars($weeklyWasteData[$dateToFind][$product['id']]['entered_by']) : ''; ?>"
                                                                                   class="form-control entered-by mt-2"
                                                                                   data-product-id="<?php echo $product['id']; ?>"
                                                                                   data-date="<?php echo $dateToFind; ?>"
                                                                                   data-prep-id="<?php echo $prep_area['id']; ?>">
                                                                            <input type="text"
                                                                                   name="items_wasted_<?php echo $site['id']; ?>_<?php echo $prep_area['id']; ?>_<?php echo $product['id']; ?>_<?php echo $dateToFind; ?>"
                                                                                   value="<?php echo isset($weeklyWasteData[$dateToFind][$product['id']]) ? htmlspecialchars($weeklyWasteData[$dateToFind][$product['id']]['items_wasted']) : ''; ?>"
                                                                                   class="form-control items-wasted mt-2"
                                                                                   placeholder="Items wasted"
                                                                                   data-product-id="<?php echo $product['id']; ?>"
                                                                                   data-date="<?php echo $dateToFind; ?>"
                                                                                   data-prep-id="<?php echo $prep_area['id']; ?>">
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
        $('.wasteViewT').toggleClass('d-none');
    });

    // Handle input changes for wasteM_value, entered_by, and items_wasted
    $('.wasteM-value, .entered-by, .items-wasted').on('change', function() {
        var $input = $(this);
        var product_id = $input.data('product-id');
        var date_entered = $input.data('date');
        var prep_id = $input.data('prep-id');
        var field = 'wasteM_value';
        if ($input.hasClass('entered-by')) field = 'entered_by';
        if ($input.hasClass('items-wasted')) field = 'items_wasted';
        var value = $input.val();

        // Disable all inputs to prevent concurrent updates
        $('.wasteM-value, .entered-by, .items-wasted').prop('disabled', true).addClass('loading');

        // AJAX request to update data
        $.ajax({
            url: '/Compliance/Waste/Home/updateWasteHistory',
            type: 'POST',
            data: {
                product_id: product_id,
                date_entered: date_entered,
                prep_id: prep_id,
                location_id: '<?php echo $site_detail[0]['location_id']; ?>',
                [field]: value
            },
            success: function(response) {
                var res = JSON.parse(response);
                if (res.status === 'success') {
                    // alert(res.message);
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
                $('.wasteM-value, .entered-by, .items-wasted').prop('disabled', false).removeClass('loading');
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