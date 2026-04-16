<!-- Setup Status Page - Shows results of automated organization onboarding -->
<style>
    .setup-log-table { width: 100%; }
    .setup-log-table th, .setup-log-table td { padding: 8px 12px; border-bottom: 1px solid #e9ecef; }
    .setup-log-table th { background: #f8f9fa; text-align: left; font-weight: 600; }
    .status-ok { color: #28a745; font-weight: bold; }
    .status-error { color: #dc3545; font-weight: bold; }
    .step-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 12px; background: #e9ecef; }
    .summary-card { border-left: 4px solid; padding: 15px 20px; margin-bottom: 15px; border-radius: 4px; }
    .summary-success { border-left-color: #28a745; background: #d4edda; }
    .summary-warning { border-left-color: #ffc107; background: #fff3cd; }
    .summary-error { border-left-color: #dc3545; background: #f8d7da; }
</style>

<div class="main-content">
    <div class="page-content">
        <?php $this->load->view('general/listpageTopBg'); ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-content-inner">
                        
                        <!-- Summary Card -->
                        <div class="card mb-4">
                            <div class="card-header border-bottom-dashed">
                                <div class="row g-4 align-items-center">
                                    <div class="col-sm">
                                        <h5 class="card-title mb-0 text-uppercase fw-bold">
                                            <i class="ri-settings-3-line"></i> Organization Setup Status
                                        </h5>
                                    </div>
                                    <div class="col-sm-auto">
                                        <a href="<?php echo base_url(); ?>index.php/organization" class="btn btn-info">
                                            <i class="ri-arrow-left-line"></i> Back to Organizations
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (empty($setup_log) && empty($setup_errors)): ?>
                                    <div class="summary-card summary-error">
                                        <h6 class="mb-1"><i class="ri-close-circle-fill"></i> No Setup Data Available</h6>
                                        <p class="mb-0">The setup may have failed before any steps could run. Please go back to the organization list, and click <strong>"Re-run Setup"</strong> on the organization to retry.</p>
                                    </div>
                                <?php elseif (empty($setup_errors)): ?>
                                    <div class="summary-card summary-success">
                                        <h6 class="mb-1"><i class="ri-checkbox-circle-fill"></i> All Setup Steps Completed Successfully!</h6>
                                        <p class="mb-0">The organization has been fully onboarded. The new tenant can now log in.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="summary-card summary-warning">
                                        <h6 class="mb-1"><i class="ri-error-warning-fill"></i> Setup Completed With <?php echo count($setup_errors); ?> Issue(s)</h6>
                                        <p class="mb-0">Some steps completed but the following issues need manual attention:</p>
                                        <ul class="mt-2 mb-0">
                                            <?php foreach ($setup_errors as $err): ?>
                                                <li><strong><?php echo htmlspecialchars($err['step']); ?>:</strong> <?php echo htmlspecialchars($err['message']); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Detailed Log -->
                        <div class="card" id="setupLog">
                            <div class="card-header border-bottom-dashed">
                                <h5 class="card-title mb-0 fw-bold">Detailed Setup Log</h5>
                            </div>
                            <div class="card-body p-0">
                                <?php if (!empty($setup_log)): ?>
                                <table class="setup-log-table">
                                    <thead>
                                        <tr>
                                            <th style="width:5%">#</th>
                                            <th style="width:10%">Time</th>
                                            <th style="width:15%">Step</th>
                                            <th style="width:10%">Status</th>
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($setup_log as $index => $entry): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($entry['time']); ?></td>
                                            <td><span class="step-badge"><?php echo htmlspecialchars($entry['step']); ?></span></td>
                                            <td>
                                                <?php if ($entry['status'] === 'ok'): ?>
                                                    <span class="status-ok"><i class="ri-checkbox-circle-fill"></i> OK</span>
                                                <?php else: ?>
                                                    <span class="status-error"><i class="ri-close-circle-fill"></i> ERROR</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($entry['message']); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                    <div class="p-4 text-center text-muted">
                                        <p>No setup log available. This page shows results after creating a new organization.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Manual Checklist Reminder -->
                        <div class="card mt-4">
                            <div class="card-header border-bottom-dashed">
                                <h5 class="card-title mb-0 fw-bold">Post-Setup Reminders</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Task</th>
                                                <th>Status</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>Database created &amp; schema imported</td>
                                                <td><?php echo _checkStepHelper($setup_log, 'import_schema'); ?></td>
                                                <td>Verify via phpMyAdmin if needed</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Admin user, roles (5), SMTP configured</td>
                                                <td><?php echo _checkStepHelper($setup_log, 'populate_seed_data'); ?></td>
                                                <td>Admin, Manager, Staff, Employee, Timesheet</td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>Database config files updated</td>
                                                <td><?php echo _checkStepHelper($setup_log, 'update_config_files'); ?></td>
                                                <td>Both application/ and External/ configs</td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>Upload folders created</td>
                                                <td><?php echo _checkStepHelper($setup_log, 'create_folders'); ?></td>
                                                <td>With system subfolders</td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>Notification times configured</td>
                                                <td><span class="text-warning"><i class="ri-information-line"></i> Manual</span></td>
                                                <td>Must be done by the org admin after first login (for each location &amp; system)</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Helper to check if a step passed in the log
function _checkStepHelper($log, $stepName) {
    $hasError = false;
    $found = false;
    if (is_array($log)) {
        foreach ($log as $entry) {
            if ($entry['step'] === $stepName) {
                $found = true;
                if ($entry['status'] === 'error') {
                    $hasError = true;
                }
            }
        }
    }
    if (!$found) return '<span class="text-muted">-</span>';
    if ($hasError) return '<span class="status-error"><i class="ri-close-circle-fill"></i> Issues Found</span>';
    return '<span class="status-ok"><i class="ri-checkbox-circle-fill"></i> Done</span>';
}
?>

<script>
// Auto-refresh helper - highlight error rows
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.status-error').forEach(function(el) {
        el.closest('tr').style.backgroundColor = '#fff5f5';
    });
});
</script>
