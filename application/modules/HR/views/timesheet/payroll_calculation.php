<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll & Superannuation Calculation</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        ::-webkit-scrollbar { display: none; }
        h2,h1,h6{
                color: #1a2f52 !important;
        }
    </style>

    <!-- jQuery (required for the original AJAX & calculations) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-gray-50 font-inter">

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Page Header -->
        <section class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-navy mb-1 mt-5">Payroll & Superannuation Calculation</h1>
                    <p class="text-gray-700">
                        Week: <?= date('jS F', strtotime($timesheet['date_from'])) ?> – <?= date('jS F Y', strtotime($timesheet['date_to'])) ?>
                    </p>
                </div>
                <a href="/HR/timesheetWithoutRoster" class="flex items-center gap-2 px-5 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back
                </a>
            </div>
        </section>

        <!-- Configuration Summary -->
        <section class="mb-8">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                <div class="flex items-start gap-4">
                    <i class="fa-solid fa-circle-info text-blue-600 text-2xl mt-0.5"></i>
                    <div class="flex-1">
                        <h6 class="text-lg font-bold text-navy mb-4">Current Configuration</h6>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Superannuation Rate</p>
                                <p class="text-xl font-bold text-navy"><?= $superConfig['super_percentage'] ?>%</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Payroll Tax Rate</p>
                                <p class="text-xl font-bold text-navy"><?= $superConfig['payroll_tax_rate'] ?>%</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Tier-Based</p>
                                <p class="text-xl font-bold text-navy"><?= $superConfig['enable_tier_payroll'] == '1' ? 'Enabled (Tier 1 only)' : 'Disabled (All employees)' ?></p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Public Holidays</p>
                                <p class="text-xl font-bold text-navy"><?= count($public_holidays) ?> in this week</p>
                            </div>
                        </div>

                        <?php if (!empty($public_holidays)): ?>
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-700">
                                <strong>Holidays:</strong>
                                <?php foreach ($public_holidays as $holiday): ?>
                                    <span class="inline-block px-3 py-1 mr-2 mt-2 text-xs font-semibold text-amber-800 bg-amber-100 rounded-full">
                                        <?= date('M d', strtotime($holiday)) ?>
                                    </span>
                                <?php endforeach; ?>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Calculation Form -->
        <form id="payrollCalculationForm">
            <input type="hidden" name="timesheet_id" value="<?= $timesheet_id ?>">
            <input type="hidden" name="super_rate" value="<?= $superConfig['super_percentage'] ?>">
            <input type="hidden" name="payroll_tax_rate" value="<?= $superConfig['payroll_tax_rate'] ?>">
            <input type="hidden" name="tier_based_enabled" value="<?= $superConfig['enable_tier_payroll'] ?>">

            <section class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

                <!-- Labour Cost Input Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                    <h2 class="text-2xl font-bold text-navy mb-6">Labour Cost Input</h2>

                    <!-- X Labour Cost (Forecast) -->
                    <div class="mb-6">
                        <label for="x_labour_cost" class="block text-sm font-semibold text-gray-700 mb-2">
                            Net Income 
                            <i class="fa-solid fa-info-circle text-gray-500" title="Can be entered by Ops manager for forecasting"></i>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 font-semibold">$</span>
                            <input type="number" step="0.01" id="x_labour_cost" name="x_labour_cost"
                                   value="<?= isset($existingCalculation['x_labour_cost']) ? $existingCalculation['x_labour_cost'] : '' ?>"
                                   placeholder="Enter forecasted labour cost"
                                   class="w-full pl-10 pr-4 py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-medium text-navy">
                        </div>
                       
                    </div>

                    <!-- Net Cost (Calculated) -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Net Cost (Calculated)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">$</span>
                            <input type="text" id="net_income_display"
                                   value="<?= number_format($calculated_net_income['total'], 2) ?>"
                                   readonly class="w-full pl-10 pr-4 py-3.5 border border-gray-200 rounded-lg bg-gray-100 text-gray-700 font-medium cursor-not-allowed">
                            <input type="hidden" name="net_income" id="net_income" value="<?= $calculated_net_income['total'] ?>">
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Calculated from actual hours worked × rates (considering weekends & holidays)</p>
                    </div>

                    <!-- Employee Breakdown Toggle -->
                    <button type="button" id="toggle-breakdown-btn"
                            class="flex items-center gap-2 px-5 py-3 border-2 border-navy text-navy rounded-lg font-medium hover:bg-navy hover:text-white transition-colors">
                        <i class="fa-solid fa-table"></i>
                        <span>View Employee Breakdown</span>
                    </button>

                    <!-- Collapsible Employee Breakdown -->
                    <div id="employee-breakdown-table" class="mt-6 hidden">
                        <div class="overflow-x-auto rounded-lg border border-gray-200 max-h-96 overflow-y-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-navy text-white sticky top-0">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold">Date</th>
                                        <th class="px-4 py-3 text-left font-semibold">Hours</th>
                                        <th class="px-4 py-3 text-left font-semibold">Rate</th>
                                        <th class="px-4 py-3 text-left font-semibold">Type</th>
                                        <th class="px-4 py-3 text-right font-semibold">Cost</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php if (!empty($calculated_net_income['breakdown'])): ?>
                                        <?php foreach ($calculated_net_income['breakdown'] as $item): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-gray-700"><?= date('M d', strtotime($item['date'])) ?></td>
                                            <td class="px-4 py-3 text-gray-700"><?= $item['hours'] ?>h</td>
                                            <td class="px-4 py-3 text-gray-700">$<?= $item['rate'] ?></td>
                                            <td class="px-4 py-3">
                                                <?php if ($item['is_public_holiday']): ?>
                                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Holiday</span>
                                                <?php elseif ($item['day_type'] == 'Sunday'): ?>
                                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Sunday</span>
                                                <?php elseif ($item['day_type'] == 'Saturday'): ?>
                                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Saturday</span>
                                                <?php else: ?>
                                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Weekday</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-3 text-right font-medium text-gray-900">$<?= number_format($item['cost'], 2) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">No data available</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Calculation Results Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                    <h2 class="text-2xl font-bold text-navy mb-6">Calculation Results</h2>

                    <table class="w-full text-lg mb-8">
                        <tbody>
                            <tr class="border-b border-gray-200">
                                <td class="py-4"><strong class="text-gray-800">Net Income</strong></td>
                                <td class="py-4 text-right font-bold text-navy">$<span id="display_net_income"><?= number_format($calculated_net_income['total'], 2) ?></span></td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td class="py-4">
                                    <strong class="text-gray-800">Superannuation</strong><br>
                                    <span class="text-sm text-gray-600"><?= $superConfig['super_percentage'] ?>% of Net Income</span>
                                </td>
                                <td class="py-4 text-right">
                                    <span class="text-2xl font-bold text-green-600">$<span id="display_superannuation">0.00</span></span>
                                    <input type="hidden" name="superannuation" id="superannuation">
                                </td>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <td class="py-4"><strong class="text-gray-800">Cost inc Super</strong></td>
                                <td class="py-4 text-right font-bold text-navy">$<span id="display_cost_inc_super">0.00</span></td>
                                <input type="hidden" name="cost_inc_super" id="cost_inc_super">
                            </tr>
                            <tr class="bg-amber-50">
                                <td class="py-4">
                                    <strong class="text-amber-800">Payroll Tax</strong><br>
                                    <span class="text-sm text-gray-600"><?= $superConfig['payroll_tax_rate'] ?>% of Cost inc Super</span>
                                </td>
                                <td class="py-4 text-right">
                                    <span class="text-2xl font-bold text-amber-600">$<span id="display_payroll_tax">0.00</span></span>
                                    <input type="hidden" name="payroll_tax" id="payroll_tax">
                                </td>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <td class="py-4"><strong class="text-gray-800">Total Cost inc Payroll Tax</strong></td>
                                <td class="py-4 text-right font-bold text-navy">$<span id="display_total_cost">0.00</span></td>
                                <input type="hidden" name="cost_inc_payroll" id="cost_inc_payroll">
                            </tr>
                            <tr class="bg-purple-50">
                                <td class="py-4">
                                    <strong class="text-purple-800">Final Percentage</strong><br>
                                    <span class="text-sm text-gray-700">Of Net Income</span>
                                </td>
                                <td class="py-4 text-right">
                                    <span class="text-4xl font-bold text-purple-600"><span id="display_percentage">0.00</span>%</span>
                                    <input type="hidden" name="final_percentage" id="final_percentage">
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <button type="submit" id="saveBtn"
                            class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold text-lg transition-colors shadow-md">
                        <i class="fa-solid fa-save"></i>
                        Save Calculation
                    </button>

                    <?php if ($existingCalculation): ?>
                    <p class="text-center text-sm text-gray-500 mt-4">
                        <i class="fa-solid fa-clock mr-1"></i>
                        Last saved: <?= date('d M Y h:i A', strtotime($existingCalculation['updated_at'])) ?>
                    </p>
                    <?php endif; ?>
                </div>

            </section>
        </form>

        <!-- Formula Reference -->
        <section class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 mb-5">
            <h2 class="text-2xl font-bold text-navy mb-6 flex items-center gap-3">
                <i class="fa-solid fa-calculator text-blue-600"></i>
                Calculation Formulas
            </h2>
            <ul class="space-y-3 text-gray-700">
                <li><strong>Net Income:</strong> Sum of (Hours Worked × Hourly Rate) for all employees</li>
                <li><strong>Superannuation (SUP1):</strong> Net Income × <?= $superConfig['super_percentage'] ?>%</li>
                <li><strong>Cost inc Super (SUP2):</strong> Net Income + Superannuation</li>
                <li><strong>Payroll Tax (PT1):</strong> Cost inc Super × <?= $superConfig['payroll_tax_rate'] ?>%</li>
                <li><strong>Total Cost (PT2):</strong> Cost inc Super + Payroll Tax</li>
                <li><strong>Percentage:</strong> (Total Cost / Net Income) × 100</li>
            </ul>
        </section>

    </main>

    <script>
        $(document).ready(function() {
            // Initial calculation on page load
            calculatePayroll();

            // Toggle employee breakdown
            $('#toggle-breakdown-btn').on('click', function() {
                $('#employee-breakdown-table').toggleClass('hidden');
                const icon = $(this).find('i');
                const text = $(this).find('span');
                if ($('#employee-breakdown-table').hasClass('hidden')) {
                    icon.removeClass('fa-eye-slash').addClass('fa-table');
                    text.text('View Employee Breakdown');
                } else {
                    icon.removeClass('fa-table').addClass('fa-eye-slash');
                    text.text('Hide Employee Breakdown');
                }
            });

            function calculatePayroll() {
                let netIncome = parseFloat($('#x_labour_cost').val()) || 0; //manual entry
                let netCost = parseFloat($('#net_income').val()) || 0;
                let superRate = parseFloat('<?= $superConfig["super_percentage"] ?>');
                let taxRate = parseFloat('<?= $superConfig["payroll_tax_rate"] ?>');

                let superannuation = netCost * (superRate / 100);
                let costIncSuper = netCost + superannuation;
                let payrollTax = costIncSuper * (taxRate / 100);
                let totalCost = costIncSuper + payrollTax;
                let percentage = netIncome > 0 ? (totalCost / netIncome) * 100 : 0;

                // Update display fields
                $('#display_net_income').text(netCost.toFixed(2));
                $('#display_superannuation').text(superannuation.toFixed(2));
                $('#display_cost_inc_super').text(costIncSuper.toFixed(2));
                $('#display_payroll_tax').text(payrollTax.toFixed(2));
                $('#display_total_cost').text(totalCost.toFixed(2));
                $('#display_percentage').text(percentage.toFixed(2));

                // Update hidden inputs
                $('#superannuation').val(superannuation.toFixed(2));
                $('#cost_inc_super').val(costIncSuper.toFixed(2));
                $('#payroll_tax').val(payrollTax.toFixed(2));
                $('#cost_inc_payroll').val(totalCost.toFixed(2));
                $('#final_percentage').val(percentage.toFixed(2));
            }

            // Form submission via AJAX
            $('#payrollCalculationForm').on('submit', function(e) {
                e.preventDefault();

                $('#saveBtn')
                    .html('<i class="fa-solid fa-spinner fa-spin mr-2"></i>Saving...')
                    .prop('disabled', true);

                $.ajax({
                    url: '/HR/Timesheet/savePayrollCalculation',
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#saveBtn')
                                .html('<i class="fa-solid fa-check mr-2"></i>Saved!')
                                .removeClass('bg-green-500').addClass('bg-green-600');

                            setTimeout(function() {
                                $('#saveBtn')
                                    .html('<i class="fa-solid fa-save mr-2"></i>Save Calculation')
                                    .prop('disabled', false)
                                    .removeClass('bg-green-600').addClass('bg-green-500');
                            }, 2000);
                        } else {
                            alert('Error: ' + response.message);
                            $('#saveBtn')
                                .html('<i class="fa-solid fa-save mr-2"></i>Save Calculation')
                                .prop('disabled', false);
                        }
                    },
                    error: function() {
                        alert('Failed to save calculation. Please try again.');
                        $('#saveBtn')
                            .html('<i class="fa-solid fa-save mr-2"></i>Save Calculation')
                            .prop('disabled', false);
                    }
                });
            });
        });
    </script>

</body>
</html>