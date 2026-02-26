<div class="container-fluid mb-5" style="margin-top: 130px !important;">
    <div class="row">
        <div class="col-12 tempDiv">
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
                    <h4 class="card-title mb-0 flex-grow-1 text-faded">Sanitation Dashboard</h4>
                    <div class="flex-shrink-0">
                        <select class="form-select siteDropdown">
                            <option>Select Site</option>
                            <?php foreach ($site_detail as $index => $site): ?>
                                <option value="<?php echo htmlspecialchars($site['id']); ?>" <?php echo $index === 0 ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($site['site_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <a id="saveBtn" class="btn btn-success mx-4" onclick="handleSaveClick(this)">
                        <i class="fas fa-save"></i> Save
                    </a>
                </div>

                <div class="d-none alert alert-success alert-dismissible alert-label-icon rounded-label shadow fade show tempSuccessRecorded" role="alert">
                    <i class="ri-notification-off-line label-icon"></i>
                    <strong>Success</strong> Value recorded successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-borderless table-hover table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-muted">
                                    <th>Product Name</th>
                                    <th>Commenced</th>
                                    <th>Time Completed</th>
                                    <th>Completed MDS</th>
                                    <th>Comments</th>
                                    <th>Recorded By</th>
                                </tr>
                            </thead>

                            <?php if (!empty($site_detail)): ?>
                                <?php foreach ($site_detail as $site): ?>
                                    <?php foreach ($prep_detail as $prep_area): ?>
                                        <?php if ($prep_area['site_id'] == $site['id']): ?>
                                            <tbody class="prep_<?php echo (int)$prep_area['id']; ?> tbodySite siteId_<?php echo (int)$site['id']; ?>">
                                                <tr>
                                                    <th colspan="11" class="text-black w-100" style="background-color: #dff0fa;">
                                                        <b><?php echo htmlspecialchars($prep_area['prep_name']); ?> (Site: <?php echo htmlspecialchars($site['site_name']); ?>)</b>
                                                    </th>
                                                </tr>

                                                <?php 
                                                $hasProducts = false;
                                                foreach ($products as $product): 
                                                    if ($product['prep_id'] == $prep_area['id'] && ($product['site_id'] == $site['id'] || $product['site_id'] == 0)): 
                                                        $hasProducts = true;
                                                ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                                        <td>
                                                            <input type="text" class="form-control auto-save"
                                                                data-product-id="<?php echo (int)$product['id']; ?>"
                                                                data-field="commenced"
                                                                data-prep-id="<?php echo (int)$prep_area['id']; ?>"
                                                                value="<?php echo isset($todaysEnteredData[$product['id']]) ? htmlspecialchars($todaysEnteredData[$product['id']]['commenced']) : ''; ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control auto-save"
                                                                data-product-id="<?php echo (int)$product['id']; ?>"
                                                                data-field="time_completed"
                                                                data-prep-id="<?php echo (int)$prep_area['id']; ?>"
                                                                value="<?php echo isset($todaysEnteredData[$product['id']]) ? htmlspecialchars($todaysEnteredData[$product['id']]['time_completed']) : ''; ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control auto-save"
                                                                data-product-id="<?php echo (int)$product['id']; ?>"
                                                                data-field="completed_mds"
                                                                data-prep-id="<?php echo (int)$prep_area['id']; ?>"
                                                                value="<?php echo isset($todaysEnteredData[$product['id']]) ? htmlspecialchars($todaysEnteredData[$product['id']]['completed_mds']) : ''; ?>">
                                                        </td>
                                                        <td>
                                                            <textarea class="form-control auto-save"
                                                                data-product-id="<?php echo (int)$product['id']; ?>"
                                                                data-field="comments"
                                                                data-prep-id="<?php echo (int)$prep_area['id']; ?>"><?php echo isset($todaysEnteredData[$product['id']]) ? htmlspecialchars($todaysEnteredData[$product['id']]['comments']) : ''; ?></textarea>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control auto-save"
                                                                data-product-id="<?php echo (int)$product['id']; ?>"
                                                                data-field="entered_by"
                                                                data-prep-id="<?php echo (int)$prep_area['id']; ?>"
                                                                value="<?php echo isset($todaysEnteredData[$product['id']]) ? htmlspecialchars($todaysEnteredData[$product['id']]['entered_by']) : ''; ?>">
                                                        </td>
                                                    </tr>
                                                <?php 
                                                    endif;
                                                endforeach;

                                                if (!$hasProducts): ?>
                                                    <tr>
                                                        <td colspan="11" class="text-center">No products found for this prep area.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tbody>
                                    <tr>
                                        <td colspan="11" class="text-center">No sites or prep areas found.</td>
                                    </tr>
                                </tbody>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {

    // Site dropdown filter
    $('.siteDropdown').on('change', function() {
        const siteId = $(this).val();
        localStorage.setItem('selectedSiteDashBoard', siteId);
        $('.tbodySite').addClass('d-none');
        $('.siteId_' + siteId).removeClass('d-none');
    });

    // Initialize site selection
    const storedSiteId = localStorage.getItem('selectedSiteDashBoard') || $('.siteDropdown').val();
    $('.siteDropdown').val(storedSiteId);
    $('.tbodySite').addClass('d-none');
    $('.siteId_' + storedSiteId).removeClass('d-none');

    // Auto-save input fields
    $('.auto-save').on('change blur', function() {
        const $input = $(this);
        const data = {
            product_id: $input.data('product-id'),
            field: $input.data('field'),
            value: $input.val(),
            prep_id: $input.data('prep-id')
        };

        $.ajax({
            url: '<?php echo base_url('Compliance/Sanitation/Home/saveDashboardData'); ?>',
            method: 'POST',
            data: data,
            success: function(response) {
                try {
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        console.log('Auto-saved successfully');
                    }
                } catch (e) {
                    console.error('Invalid JSON response', response);
                }
            },
            error: function(err) {
                console.error('Error saving data', err);
            }
        });
    });
});

// Save button animation
function handleSaveClick(obj) {
    const $btn = $(obj);
    $btn.html('Saving...');
    setTimeout(() => $btn.html("<i class='fas fa-save'></i> Save"), 1000);
}
</script>

<style>
.loading {
    background-color: #f0f0f0;
    cursor: not-allowed;
}
</style>
