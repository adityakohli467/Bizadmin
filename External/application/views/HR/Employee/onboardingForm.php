<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onboarding Form</title>
    <link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
    <?php include(APPPATH . '../../application/views/general/tailwind_common_assets.php'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://bizadmin.com.au/login-assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCkr6VbGs7uYPJn_AFfvnMZztcQIigx9J0&libraries=places"></script>

    <script>
        function initAutocomplete() {
            const fields = ["address", "emergency_address"];

            fields.forEach(function(fieldId) {
                const input = document.getElementById(fieldId);
                if (!input) return; // skip if field doesn't exist

                const autocomplete = new google.maps.places.Autocomplete(input, {
                    types: ["geocode"],
                    componentRestrictions: { country: "au" }
                });

                autocomplete.addListener("place_changed", function () {
                    const place = autocomplete.getPlace();

                    if (!place.formatted_address) {
                        alert("Please select an address from the dropdown.");
                        return;
                    }

                    input.value = place.formatted_address;
                });
            });
        }

        document.addEventListener("DOMContentLoaded", initAutocomplete);
    </script>

    <!-- Mobile-first onboarding theme (matches BizAdmin mobile onboarding UX mockup) -->
    <style id="obMobileTheme">
        :root { --ob-navy: #1a2f52; --ob-green: #22c55e; }

        body.bg-gray-50 { background: #f4f5f7 !important; }

        /* ===== Topbar ===== */
        #header {
            background: var(--ob-navy) !important;
            padding: 16px 20px 14px !important;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.12) !important;
        }
        #header .ob-topbar-title {
            color: #fff; font-size: 16px; font-weight: 600;
            text-align: center; letter-spacing: 0.3px; margin: 0;
        }
        #header .ob-topbar-sub {
            color: rgba(255, 255, 255, 0.6); font-size: 12px;
            text-align: center; margin-top: 3px;
        }

        /* ===== Step progress ===== */
        #tab-navigation {
            background: var(--ob-navy) !important;
            border: none !important; box-shadow: none !important;
        }
        #tab-navigation .ob-steps {
            display: flex; align-items: flex-start; justify-content: space-between;
            gap: 0; padding: 2px 14px 16px; max-width: 720px; margin: 0 auto;
            overflow-x: auto; -webkit-overflow-scrolling: touch;
        }
        #tab-navigation .tab-btn {
            background: transparent !important; border: none !important;
            padding: 0 !important; margin: 0 !important; box-shadow: none !important;
            display: flex; flex-direction: column; align-items: center; gap: 5px;
            flex: 0 0 auto; min-width: 46px; cursor: pointer;
        }
        #tab-navigation .tab-btn:hover { background: transparent !important; }
        .ob-step-circle {
            width: 30px; height: 30px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 600;
            background: rgba(255, 255, 255, 0.15); color: rgba(255, 255, 255, 0.55);
            transition: all 0.2s ease;
        }
        .tab-btn.ob-done .ob-step-circle { background: var(--ob-green); color: #fff; }
        .tab-btn.active .ob-step-circle { background: #fff; color: var(--ob-navy); }
        .ob-step-label {
            font-size: 10px; color: rgba(255, 255, 255, 0.55);
            text-align: center; line-height: 1.2; max-width: 62px;
        }
        .tab-btn.active .ob-step-label { color: rgba(255, 255, 255, 0.95); font-weight: 600; }
        .ob-step-line {
            flex: 1 1 auto; height: 2px; min-width: 8px; align-self: flex-start;
            margin: 14px 3px 0; background: rgba(255, 255, 255, 0.15);
        }
        .ob-step-line.ob-done { background: var(--ob-green); }

        /* ===== Content shell ===== */
        #main-content {
            max-width: 720px !important; margin: 0 auto !important;
            padding: 14px 16px 110px !important;
        }
        #main-content > small {
            display: block; font-size: 11px; color: #64748b;
            margin-bottom: 12px; line-height: 1.5;
        }
        /* Neutralise the old outer card so each tab-pane becomes its own card */
        #main-content > .bg-white {
            background: transparent !important; box-shadow: none !important;
            border: none !important; padding: 0 !important;
        }
        .tab-content .tab-pane {
            background: #fff; border-radius: 14px; padding: 16px;
            border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
        }

        /* ===== Inputs ===== */
        .tab-content input[type="text"],
        .tab-content input[type="email"],
        .tab-content input[type="tel"],
        .tab-content input[type="number"],
        .tab-content input[type="date"],
        .tab-content input[type="password"],
        .tab-content input[type="search"],
        .tab-content input[type="url"],
        .tab-content input[type="file"],
        .tab-content select,
        .tab-content textarea {
            width: 100% !important; background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important; border-radius: 8px !important;
            padding: 10px 12px !important; font-size: 14px !important;
            color: #1e293b !important; box-shadow: none !important;
            transition: border-color 0.15s ease, background 0.15s ease;
        }
        .tab-content input:focus,
        .tab-content select:focus,
        .tab-content textarea:focus {
            border-color: #3b82f6 !important; background: #fff !important;
            outline: none !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12) !important;
        }
        .tab-content label:not(:has(input)):not(:has(.form-check-input)) {
            font-size: 12px !important; color: #64748b !important; font-weight: 500 !important;
        }
        .tab-content .fieldError { color: #ef4444 !important; font-size: 11px; display: block; margin-top: 4px; }

        /* ===== Save / continue buttons ===== */
        #save_continue_personal, #save_continue_emergency, #save_continue_bank,
        #save_continue_tax, #save_continue_police, #save_continue_annuation,
        #save_continue_privacy {
            width: 100% !important; background: var(--ob-navy) !important; color: #fff !important;
            border: none !important; border-radius: 10px !important; padding: 13px !important;
            font-size: 14px !important; font-weight: 600 !important; margin-top: 10px !important;
            cursor: pointer; box-shadow: 0 3px 8px rgba(26, 47, 82, 0.25) !important;
            transition: background 0.15s ease;
        }
        #save_continue_personal:hover, #save_continue_emergency:hover, #save_continue_bank:hover,
        #save_continue_tax:hover, #save_continue_police:hover, #save_continue_annuation:hover,
        #save_continue_privacy:hover { background: #16243f !important; }

        /* ===== Add-account button (bank tab) ===== */
        #addBankAccountBtn {
            background: #f0f9ff !important; border: 1px dashed #93c5fd !important;
            color: #2563eb !important; border-radius: 10px !important; font-weight: 500 !important;
        }
    </style>

</head>

<body class="bg-gray-50 font-sans">

<header id="header" class="bg-navy text-white shadow-xl">
    <p class="ob-topbar-title">Onboarding</p>
    <p class="ob-topbar-sub" id="onboardingStepSub">Step 1 · Personal Details</p>
</header>



<?php if(null !==$this->session->userdata('error_msg')) { ?>
    <div class='hideMe alert alert-danger'>
        <?php echo $this->session->flashdata('error_msg'); ?>
    </div>
<?php } ?>
<nav id="tab-navigation" class="bg-white border-b border-gray-200 shadow-sm">
    <div class="ob-steps">
        <?php
        $tabs = [
            ['id' => 'personalDetails', 'label' => 'Personal Details', 'short' => 'Personal', 'step' => 0],
            ['id' => 'emergencyDetails', 'label' => 'Emergency Details', 'short' => 'Emergency', 'step' => 1],
            ['id' => 'bankDetails', 'label' => 'Bank Details', 'short' => 'Bank', 'step' => 2],
            ['id' => 'policeClearance', 'label' => 'Police Clearance', 'short' => 'Police', 'step' => 3],
            ['id' => 'taxDetails', 'label' => 'Tax Details', 'short' => 'Tax', 'step' => 4],
            ['id' => 'superAnnuation', 'label' => 'Super Annuation', 'short' => 'Super', 'step' => 5],
            ['id' => 'privacyPolicy', 'label' => 'Policies', 'short' => 'Policies', 'step' => 6]
        ];

        // Show/hide tabs based on the "Onboarding Form Customize" settings (per location)
        $tabConfig = isset($onboardingTabsConfig) && is_array($onboardingTabsConfig) ? $onboardingTabsConfig : [];
        $isTabEnabled = function($id) use ($tabConfig) {
            return !isset($tabConfig[$id]) || $tabConfig[$id] == '1' || $tabConfig[$id] === 1;
        };
        $tabs = array_values(array_filter($tabs, function($t) use ($isTabEnabled) {
            return $isTabEnabled($t['id']);
        }));

        $stepsCompleted = isset($employee['stepsCompleted']) ? $employee['stepsCompleted'] : 0;
        $tabCount = count($tabs);

        foreach ($tabs as $index => $tab) {
            $isActive = $index === 0 ? 'active' : '';
            ?>
            <button
                    class="tab-btn <?php echo $isActive; ?>"
                    data-tab="<?php echo $tab['id']; ?>"
                    rel="<?php echo $tab['id']; ?>"
                    role="tab"
                    aria-selected="<?php echo $isActive ? 'true' : 'false'; ?>"
                    aria-controls="<?php echo $tab['id']; ?>">
                <span class="ob-step-circle"><?php echo $index + 1; ?></span>
                <span class="ob-step-label"><?php echo $tab['short']; ?></span>
            </button>
            <?php if ($index < $tabCount - 1): ?>
                <span class="ob-step-line" aria-hidden="true"></span>
            <?php endif; ?>
        <?php } ?>
    </div>
</nav>
<script>
    // Onboarding tab navigation config (driven by HR "Onboarding Form Customize" settings)
    var ONBOARDING_FLOW_ORDER = ['personalDetails','emergencyDetails','bankDetails','taxDetails','policeClearance','superAnnuation','privacyPolicy'];
    var ONBOARDING_ENABLED_TABS = <?php echo json_encode(array_map(function($t){ return $t['id']; }, $tabs)); ?>;
    var ONBOARDING_TAB_LABELS = <?php echo json_encode(array_reduce($tabs, function($carry, $t){ $carry[$t['id']] = $t['label']; return $carry; }, [])); ?>;
    var ONBOARDING_EMP_ID = '<?php echo $employee['emp_id']; ?>';

    // Update the topbar subtitle and step-circle / connector states for the active step
    function updateOnboardingProgress(activeTabId){
        var order = ONBOARDING_ENABLED_TABS;
        var activeIdx = order.indexOf(activeTabId);
        if(activeIdx === -1){ return; }

        order.forEach(function(id, i){
            var $btn = $(".tab-btn[data-tab='" + id + "']");
            var $circle = $btn.find('.ob-step-circle');
            $btn.removeClass('ob-done');
            if(i < activeIdx){
                $btn.addClass('ob-done');
                $circle.html('<i class="fas fa-check"></i>');
            } else {
                $circle.html(i + 1);
            }
        });

        $('.ob-step-line').each(function(i){
            if(i < activeIdx){ $(this).addClass('ob-done'); }
            else { $(this).removeClass('ob-done'); }
        });

        var label = ONBOARDING_TAB_LABELS[activeTabId] || '';
        $('#onboardingStepSub').text('Step ' + (activeIdx + 1) + ' of ' + order.length + ' \u00B7 ' + label);
    }

    function getNextEnabledTab(currentId){
        var startIdx = ONBOARDING_FLOW_ORDER.indexOf(currentId);
        if(startIdx === -1) return null;
        for(var i = startIdx + 1; i < ONBOARDING_FLOW_ORDER.length; i++){
            if(ONBOARDING_ENABLED_TABS.indexOf(ONBOARDING_FLOW_ORDER[i]) !== -1){
                return ONBOARDING_FLOW_ORDER[i];
            }
        }
        return null;
    }

    // Move to the next enabled tab, or finalize the onboarding if this is the last enabled tab
    function proceedFromTab(currentId){
        var next = getNextEnabledTab(currentId);
        if(next){
            $('#loaderContainer').hide();
            activateTab(next);
        } else {
            finalizeOnboarding();
        }
    }

    // Final submission used when the last enabled tab is not the Policies tab
    function finalizeOnboarding(){
        $('#loaderContainer').show();
        $.ajax({
            type: 'POST',
            url: '/External/Employee/submit_onboarding_process',
            data: { emp_id: ONBOARDING_EMP_ID, final_submit: 1, stepsCompleted: 7 },
            success: function(response){
                $('#loaderContainer').hide();
                var res;
                try { res = JSON.parse(response); } catch(e){ res = {}; }
                if(res && res.status === 'success'){
                    document.getElementById('onboardSuccessModal').classList.remove('hidden');
                } else {
                    alert('Some error occured, Please refresh page and try again.');
                }
            },
            error: function(){
                $('#loaderContainer').hide();
                alert('Some error occured, Please refresh page and try again.');
            }
        });
    }
</script>
<main id="main-content" class="max-w-9xl mx-auto px-6 py-10">
    <small>Please click on individual tab to fill the details,please scroll tabs from top(in mobile devices)</small>
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-8 md:p-12">
        <div class="tab-content text-black">

            <!-- Personal Details form start here-->
            <div class="tab-pane active" id="personalDetails" role="personalDetails">

                <form role="form" id="personalDetailsForm" method="post" action="" class="mt-6" enctype="multipart/form-data">

                    <input type="hidden" name="emp_id" id="emp_id" value="<?php echo $employee['emp_id']; ?>">
                    <input type="hidden" name="stepsCompleted" class="personalDetailsFormSteps" value="1">

                    <!-- MAIN GRID -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <select id="title" name="title"
                                    class="form-select w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-navy focus:border-transparent">
                                <option value="">Select</option>
                                <option value="Mr" <?php if($employee['title']=='Mr') echo 'selected'; ?>>Mr</option>
                                <option value="Ms" <?php if($employee['title']=='Ms') echo 'selected'; ?>>Ms</option>
                                <option value="Mrs" <?php if($employee['title']=='Mrs') echo 'selected'; ?>>Mrs</option>
                                <option value="Miss" <?php if($employee['title']=='Miss') echo 'selected'; ?>>Miss</option>
                                <option value="Dr" <?php if($employee['title']=='Dr') echo 'selected'; ?>>Dr</option>
                            </select>
                            <span class="fieldError text-red-500 text-sm" id="title_error"></span>
                        </div>

                        <!-- First Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="first_name">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="first_name" name="first_name"
                                   value="<?php echo $employee['first_name']; ?>"
                                   class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy"
                                   autocomplete="off">
                            <span class="fieldError text-red-500 text-sm" id="first_name_error"></span>
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="last_name">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="last_name" name="last_name"
                                   value="<?php echo $employee['last_name']; ?>"
                                   class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy"
                                   autocomplete="off">
                            <span class="fieldError text-red-500 text-sm" id="last_name_error"></span>
                        </div>

                        <!-- Preferred Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="preferred_name">Preferred Name</label>
                            <input type="text" id="preferred_name" name="preferred_name"
                                   value="<?php echo $employee['preferred_name']; ?>"
                                   class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy"
                                   autocomplete="off">
                        </div>

                        <!-- Email -->
                        <div class="md:col-span-2 lg:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="email">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="email" name="email" readonly
                                   value="<?php echo $employee['email']; ?>"
                                   class="form-control w-full px-4 py-3 bg-gray-100 text-gray-600 border border-gray-300 rounded-lg cursor-not-allowed">
                            <span class="fieldError text-red-500 text-sm" id="email_error"></span>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="phone">
                                Contact Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="phone" name="phone"
                                   onkeypress='validate(event)'
                                   value="<?php echo $employee['phone']; ?>"
                                   class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-navy">
                            <span class="fieldError text-red-500 text-sm" id="phone_error"></span>
                        </div>

                        <!-- DOB -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="dob">
                                Date of Birth <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="dob" name="dob"
                                   value="<?php echo $employee['dob']; ?>"
                                   class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-navy">
                            <span class="fieldError text-red-500 text-sm" id="dob_error"></span>
                        </div>

                        <!-- Academic Qualifications -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Highest Academic Qualifications
                            </label>
                            <textarea name="heighest_acd_achmts" rows="2"
                                      class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-navy resize-none"><?php echo $employee['heighest_acd_achmts']; ?></textarea>
                        </div>

                        <!-- Visa Status -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Are you on Visa <span class="text-red-500">*</span>
                            </label>

                            <div class="flex items-center gap-6">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="visa_status" value="yes"
                                           class="form-radio text-blue-600"
                                        <?php echo ($employee['visa_status'] == 'yes') ? 'checked' : ''; ?>>
                                    <span>Yes</span>
                                </label>

                                <label class="flex items-center gap-2">
                                    <input type="radio" name="visa_status" value="no"
                                           class="form-radio text-blue-600"
                                        <?php echo ($employee['visa_status'] == 'no') ? 'checked' : ''; ?>>
                                    <span>No</span>
                                </label>
                            </div>
                            <span class="fieldError text-red-500 text-sm" id="visa_status_error"></span>
                        </div>

                        <!-- Visa Details (hidden by default) -->
                        <div id="visaDetails" class="col-span-full <?php echo ($employee['visa_status'] == 'yes') ? '' : 'hidden'; ?>">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Visa Expiry Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Date of Expiry <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="visa_expiry" id="visa_expiry"
                                           value="<?php echo $employee['visa_expiry']; ?>"
                                           class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-navy">
                                    <span class="fieldError text-red-500 text-sm" id="visa_expiry_error"></span>
                                </div>

                                <!-- Visa Attachment -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Upload Visa Document <span class="text-red-500">*</span>
                                    </label>
                                    <input type="file" name="visa_attachment" id="visa_attachment"
                                           accept=".pdf,.jpg,.jpeg,.png"
                                           class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-navy">
                                    <span class="fieldError text-red-500 text-sm" id="visa_attachment_error"></span>

                                    <?php if(isset($employee['visa_attachment']) && !empty($employee['visa_attachment'])): ?>
                                        <div class="mt-2">
                                            <a href="<?php echo base_url('uploaded_files/'.$this->session->userdata('tenantIdentifier').'/HR/VisaFiles/'.$employee['visa_attachment']); ?>"
                                               target="_blank"
                                               class="text-blue-600 hover:text-blue-800 text-sm">
                                                View Current Document
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Face Verification -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Face Verification
                            </label>
                            <small>Note: By continuing, you consent to your face being captured and securely used for identity and attendance purposes</small>
                            <!-- Face Verify Button -->
                            <button type="button" id="faceVerifyBtn" onclick="startCamera()"
                                    class="flex items-center justify-center w-full md:w-auto px-6 py-3 border-2 border-dashed border-gray-300 rounded-lg hover:border-navy hover:bg-blue-50 transition-all">
                                <i class="text-navy text-xl mr-3 fas fa-camera"></i>
                                <span class="text-navy font-medium">
                    <?php echo !empty($employee['face_image_url']) ? 'Recapture Photo' : 'Complete Face Verification'; ?>
                </span>
                            </button>

                            <!-- Success Message -->
                            <small id="verificationMessage"
                                   class="text-green-600 font-semibold mt-2 block <?php echo !empty($employee['face_image_url']) ? '' : 'hidden'; ?>">
                                Verification completed successfully
                            </small>

                            <!-- Initial Instructions -->
                            <small id="verificationPreMessage"
                                   class="text-blue-600 font-semibold mt-2 block <?php echo empty($employee['face_image_url']) ? '' : 'hidden'; ?>">
                                Click on the button to capture your photo
                            </small>

                            <!-- Error Message -->
                            <div id="cameraErrorWrapper" class="hidden mt-2 flex items-start gap-2">
                                <small id="verificationError" class="text-red-600 font-semibold"></small>

                                <!-- Info Icon -->
                                <button id="cameraHelpBtn" class="text-blue-600 hover:text-blue-800 text-lg hidden" type="button" onclick="showCameraHelp()">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            </div>

                            <!-- Saved Image (if exists) -->
                            <img id="savedCapturedImage"
                                 src="<?php echo !empty($employee['face_image_url']) ? $employee['face_image_url'] : ''; ?>"
                                 width="320" height="240"
                                 class="rounded-lg mt-3 <?php echo !empty($employee['face_image_url']) ? '' : 'hidden'; ?>" />

                            <!-- Always-present hidden inputs and camera elements -->
                            <input type="hidden" id="employeePhoto" value="<?php echo $employee['face_image_url']; ?>" />

                            <video id="video" width="320" height="240" autoplay class="hidden"></video>

                            <canvas id="canvas" width="320" height="240" class="hidden"></canvas>

                            <!-- Live Capture Preview -->
                            <img id="capturedImagePreview" src="" width="320" height="240"
                                 class="hidden rounded-lg mt-3" />
                        </div>
                    </div>

                    <!-- Address Section Header -->
                    <h5 class="text-xl font-bold text-gray-800 mt-10 mb-4 pb-2 border-b-2 border-gray-200">Residential Address</h5>

                    <!-- ADDRESS GRID -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Address autocomplete -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address <span class="text-red-500">*</span></label>
                            <input type="text" id="address" name="address"
                                   value="<?php echo $employee['address']; ?>"
                                   class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg">
                            <span class="fieldError text-red-500 text-sm" id="address_error"></span>
                        </div>
                    </div>

                    <!-- SAVE BUTTON -->
                    <div class="flex justify-end mt-8 pt-6 border-t border-gray-200">
                        <input type="button" rel="emergencyDetails" id="save_continue_personal"
                               class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition-all"
                               value="SAVE AND CONTINUE">
                    </div>
                </form>

            </div>
            <!-- Emergency Details  form start here-->
            <div class="tab-pane" id="emergencyDetails" role="emergencyDetails">


                <h3 class="text-xl font-bold text-gray-800 mb-6">Emergency Contact</h3>

                <form role="form" id="emergencyDetailsForm" method="post" action="" enctype="multipart/form-data">

                    <input type="hidden" name="emp_id" value="<?php echo $employee['emp_id']; ?>">
                    <input type="hidden" name="stepsCompleted" class="emergencyDetailsFormSteps" value="2">

                    <!-- GRID WRAPPER -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                        <!-- Emergency Contact Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Emergency Contact Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="nextkin_name_two"
                                   name="nextkin_name_two"
                                   value="<?php echo $employee['nextkin_name_two']; ?>"
                                   autocomplete="off"
                                   class="form-control required w-full px-4 py-3 border border-gray-300 rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-navy focus:border-transparent">
                            <span class="fieldError text-red-500 text-sm" id="nextkin_name_two_error"></span>
                        </div>

                        <!-- Relationship to you -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Relationship to you <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="nextkin_relationship_two"
                                   name="nextkin_relationship_two"
                                   value="<?php echo $employee['nextkin_relationship_two']; ?>"
                                   autocomplete="off"
                                   class="form-control required w-full px-4 py-3 border border-gray-300 rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-navy">
                            <span class="fieldError text-red-500 text-sm" id="nextkin_relationship_two_error"></span>
                        </div>

                        <!-- Contact Number (moved before email) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="nextkin_phone_no"
                                   name="nextkin_phone_no"
                                   value="<?php echo $employee['nextkin_phone_no']; ?>"
                                   autocomplete="off"
                                   class="form-control required w-full px-4 py-3 border border-gray-300 rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-navy">
                            <span class="fieldError text-red-500 text-sm" id="nextkin_phone_no_error"></span>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address
                            </label>
                            <input type="text"
                                   name="nextkin_email_two"
                                   value="<?php echo $employee['nextkin_email_two']; ?>"
                                   autocomplete="off"
                                   class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-navy">
                        </div>

                        <!-- Contact Address -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Address</label>
                            <input type="text" id="emergency_address" name="emergency_address"
                                   value="<?php echo $employee['emergency_address']; ?>"
                                   class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg">
                        </div>
                    </div>

                    <!-- SAVE BUTTON -->
                    <div class="flex justify-end mt-8 pt-6 border-t border-gray-200">
                        <input type="button"
                               rel="bankDetails"
                               id="save_continue_emergency"
                               class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition-all"
                               value="SAVE AND CONTINUE">
                    </div>
                </form>

            </div>
            <!-- Bank Details form start here-->
            <div class="tab-pane" id="bankDetails" role="bankDetails">

                <form role="form" id="bankDetailsForm" method="post" action="" enctype="multipart/form-data">

                    <input type="hidden" name="emp_id" value="<?php echo $employee['emp_id']; ?>">
                    <input type="hidden" name="stepsCompleted" class="bankDetailsFormSteps" value="3">

                    <!-- ACCOUNT 1 (Always visible) -->
                    <div id="bankAccount1" class="mb-8 p-6 bg-gray-50 rounded-lg border">
                        <h5 class="text-lg font-bold text-gray-800 mb-4">Bank Account Details</h5>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <!-- Bank Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Bank Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="bank_1"
                                       name="bank_1"
                                       value="<?php echo $employee['bank_1']; ?>"
                                       autocomplete="off"
                                       class="form-control required w-full px-3 py-2 border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-navy">
                                <span class="fieldError text-red-500 text-sm" id="bank_1_error"></span>
                            </div>

                            <!-- Account Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Account Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="account_name_1"
                                       name="account_name_1"
                                       value="<?php echo $employee['account_name_1']; ?>"
                                       autocomplete="off"
                                       class="form-control required w-full px-3 py-2 border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-navy">
                                <span class="fieldError text-red-500 text-sm" id="account_name_1_error"></span>
                            </div>

                            <!-- BSB -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    BSB <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="bsb_1"
                                       name="bsb_1"
                                       value="<?php echo $employee['bsb_1']; ?>"
                                       autocomplete="off"
                                       class="form-control required w-full px-3 py-2 border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-navy">
                                <span class="fieldError text-red-500 text-sm" id="bsb_1_error"></span>
                            </div>

                            <!-- Account Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Account No <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="account_no_1"
                                       name="account_no_1"
                                       value="<?php echo $employee['account_no_1']; ?>"
                                       autocomplete="off"
                                       class="form-control required w-full px-3 py-2 border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-navy">
                                <span class="fieldError text-red-500 text-sm" id="account_no_1_error"></span>
                            </div>

                            <!-- % to Deposit -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    % to Deposit <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="percentage_1"
                                       name="percentage_1"
                                       value="<?php echo !empty($employee['percentage_1']) ? $employee['percentage_1'] : '100'; ?>"
                                       autocomplete="off"
                                       class="form-control required w-full px-3 py-2 border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-navy">
                                <span class="fieldError text-red-500 text-sm" id="percentage_1_error"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Bank Accounts (Hidden by default) -->
                    <div id="bankAccount2" class="mb-8 p-6 bg-gray-50 rounded-lg border hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="text-lg font-bold text-gray-800">Bank Account 2</h5>
                            <button type="button" onclick="removeBankAccount(2)" class="text-red-600 hover:text-red-800 font-medium">
                                Remove Account
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                                <input type="text" name="bank_2"
                                       value="<?php echo $employee['bank_2']; ?>"
                                       autocomplete="off"
                                       class="form-control w-full px-3 py-2 border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-navy">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Account Name</label>
                                <input type="text"
                                       name="account_name_2"
                                       value="<?php echo $employee['account_name_2']; ?>"
                                       autocomplete="off"
                                       class="form-control w-full px-3 py-2 border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-navy">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">BSB</label>
                                <input type="text"
                                       name="bsb_2"
                                       value="<?php echo $employee['bsb_2']; ?>"
                                       autocomplete="off"
                                       class="form-control w-full px-3 py-2 border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-navy">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Account No</label>
                                <input type="text"
                                       name="account_no_2"
                                       value="<?php echo $employee['account_no_2']; ?>"
                                       autocomplete="off"
                                       class="form-control w-full px-3 py-2 border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-navy">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">% to Deposit</label>
                                <input type="text"
                                       name="percentage_2"
                                       value="<?php echo $employee['percentage_2']; ?>"
                                       autocomplete="off"
                                       class="form-control w-full px-3 py-2 border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-navy">
                            </div>
                        </div>
                    </div>

                    <div id="bankAccount3" class="mb-8 p-6 bg-gray-50 rounded-lg border hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="text-lg font-bold text-gray-800">Bank Account 3</h5>
                            <button type="button" onclick="removeBankAccount(3)" class="text-red-600 hover:text-red-800 font-medium">
                                Remove Account
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                                <input type="text"
                                       name="bank_3"
                                       value="<?php echo $employee['bank_3']; ?>"
                                       autocomplete="off"
                                       class="form-control w-full px-3 py-2 border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-navy">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Account Name</label>
                                <input type="text"
                                       name="account_name_3"
                                       value="<?php echo $employee['account_name_3']; ?>"
                                       autocomplete="off"
                                       class="form-control w-full px-3 py-2 border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-navy">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">BSB</label>
                                <input type="text"
                                       name="bsb_3"
                                       value="<?php echo $employee['bsb_3']; ?>"
                                       autocomplete="off"
                                       class="form-control w-full px-3 py-2 border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-navy">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Account No</label>
                                <input type="text"
                                       name="account_no_3"
                                       value="<?php echo $employee['account_no_3']; ?>"
                                       autocomplete="off"
                                       class="form-control w-full px-3 py-2 border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-navy">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">% to Deposit</label>
                                <input type="text"
                                       name="percentage_3"
                                       value="<?php echo $employee['percentage_3']; ?>"
                                       autocomplete="off"
                                       class="form-control w-full px-3 py-2 border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-navy">
                            </div>
                        </div>
                    </div>

                    <!-- Add Bank Account Button -->
                    <div class="mb-6">
                        <button type="button" id="addBankAccountBtn" onclick="addBankAccount()"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                            + Add Bank Account
                        </button>
                    </div>

                    <!-- AGREEMENT TEXT -->
                    <div class="mt-10 p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 leading-6">
                        <?php $location=' ';?>
                        <p>
                            I hereby authorize <?php echo $location; ?> to initiate automatic deposits for my fortnightly wages
                            to my bank account(s) as detailed above and also authorize adjustments to be deducted
                            from my wage in case of an error. I will not hold <?php echo $location; ?> responsible
                            for delays or losses due to incorrect information.
                            This agreement remains valid until I submit written cancellation.
                        </p>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <div class="flex justify-end mt-8 pt-6 border-t border-gray-200">
                        <input type="button"
                               id="save_continue_bank"
                               rel="policeClearance"
                               value="SAVE AND CONTINUE"
                               class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition-all">
                    </div>
                </form>


            </div>
            <!-- Tax Details form start here-->
            <div class="tab-pane" id="taxDetails" role="taxDetails">


                <form role="form" id="taxDetailsForm" method="post" action="" enctype="multipart/form-data">

                    <input type="hidden" name="emp_id" value="<?php echo $employee['emp_id']; ?>">
                    <input type="hidden" name="stepsCompleted" class="taxDetailsFormSteps" value="4">

                    <!-- SECTION: TFN -->
                    <div class="section-wrap mb-10 p-6 bg-white rounded-lg shadow-sm border border-gray-200">

                        <p class="text-lg font-semibold text-gray-800 mb-4">Do you have your TFN? <span class="text-red-500">*</span></p>

                        <!-- TABS -->
                        <div class="tab flex space-x-4 mb-4">
                            <a class="tablinks tablinks_tfn <?php echo (isset($employee['check_tfn_type']) && $employee['check_tfn_type']=='tfn_number' ? 'active' : ''); ?>
                          px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 cursor-pointer"
                               onclick="openThisTab(event, 'Yes','tfn')">
                                Yes
                            </a>

                            <a class="tablinks tablinks_tfn <?php echo (isset($employee['check_tfn_type']) && $employee['check_tfn_type']=='tfn_type' ? 'active' : ''); ?>
                          px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 cursor-pointer"
                               onclick="openThisTab(event, 'No','tfn')">
                                No
                            </a>
                        </div>

                        <!-- TAB CONTENT: YES -->
                        <div id="Yes" class="tabcontent tabcontent_tfn <?php echo ($employee['check_tfn_type']=='tfn_number'?'block':'hidden'); ?>">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Enter Tax File Number <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           id="tfn_number"
                                           name="tfn_number"
                                           value="<?php echo $employee['tfn_number']; ?>"
                                           autocomplete="off"
                                           class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg
                                      focus:outline-none focus:ring-2 focus:ring-navy">
                                    <span class="fieldError text-red-500 text-sm" id="tfn_number_error"></span>
                                </div>
                            </div>
                        </div>

                        <!-- TAB CONTENT: NO -->
                        <div id="No" class="tabcontent tabcontent_tfn <?php echo ($employee['check_tfn_type']=='tfn_type'?'block':'hidden'); ?>">
                            <div class="space-y-2 text-gray-700">
                                <label><input type="radio" name="tfn_type" value="pendingTFN" <?php if($employee['tfn_type']=='pendingTFN') echo "checked"; ?>> My TFN is pending</label><br>
                                <label><input type="radio" name="tfn_type" value="noTFN" <?php if($employee['tfn_type']=='noTFN') echo "checked"; ?>> I'm under 18 and don't have a TFN</label><br>
                                <label><input type="radio" name="tfn_type" value="quotingTFN" <?php if($employee['tfn_type']=='quotingTFN') echo "checked"; ?>> I have an exemption from quoting a TFN</label>
                            </div>
                            <span class="fieldError text-red-500 text-sm" id="tfn_type_error"></span>
                        </div>

                        <input type="hidden"
                               name="check_tfn_type"
                               class="check_tfn_type"
                               value="<?php echo (isset($employee['check_tfn_type']) ? $employee['check_tfn_type'] : 'tfn_number'); ?>">
                    </div>

                    <!-- SECTION: SURNAME CHANGE (Updated order: No first, then Yes) -->
                    <div class="section-wrap mb-10 p-6 bg-white rounded-lg shadow-sm border border-gray-200">
                        <p class="text-lg font-semibold text-gray-800 mb-4">
                            Have you changed your surname since last dealing with ATO? <span class="text-red-500">*</span>
                        </p>

                        <!-- Tabs (No first, then Yes) -->
                        <div class="tab flex space-x-4 mb-4">
                            <a class="tablinks tablinks_surname <?php echo (empty($employee['have_surname_changed']) || $employee['have_surname_changed']=='noChanged'?'active':''); ?>
                         px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100 cursor-pointer"
                               onclick="openThisTab(event, 'noChanged','surname')">No</a>

                            <a class="tablinks tablinks_surname <?php echo ($employee['have_surname_changed']=='yesChanged'?'active':''); ?>
                         px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100 cursor-pointer"
                               onclick="openThisTab(event, 'yesChanged','surname')">Yes</a>
                        </div>

                        <!-- NO SURNAME CHANGED -->
                        <div id="noChanged"
                             class="tabcontent tabcontent_surname <?php echo (empty($employee['have_surname_changed']) || $employee['have_surname_changed']=='noChanged'?'block':'hidden'); ?>">
                            <p class="text-gray-600">No previous surname to report.</p>
                        </div>

                        <!-- YES SURNAME CHANGED -->
                        <div id="yesChanged"
                             class="tabcontent tabcontent_surname <?php echo ($employee['have_surname_changed']=='yesChanged'?'block':'hidden'); ?>">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Enter Previous Surname <span class="text-red-500">*</span>:</label>
                                    <input type="text"
                                           id="previous_surname"
                                           name="previous_surname"
                                           class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-navy"
                                           value="<?php echo ($employee['previous_surname'] != 'noChanged' ? $employee['previous_surname'] : ''); ?>">
                                    <span class="fieldError text-red-500 text-sm" id="previous_surname_error"></span>
                                </div>
                            </div>
                        </div>

                        <input type="hidden"
                               name="have_surname_changed"
                               class="previous_surname_changed"
                               value="<?php echo !empty($employee['have_surname_changed']) ? $employee['have_surname_changed'] : 'noChanged'; ?>">
                    </div>

                    <!-- SECTION: RESIDENT TYPE -->
                    <div class="section-wrap mb-10 p-6 bg-white rounded-lg shadow-sm border border-gray-200">
                        <p class="text-lg font-semibold text-gray-800 mb-4">Are you an Australian resident for tax purposes? <span class="text-red-500">*</span></p>

                        <div class="space-y-3 text-gray-700">
                            <label><input type="radio" name="resident_type" value="australian" <?php if($employee['resident_type']=='australian') echo "checked"; ?>> Australian resident</label><br>
                            <label><input type="radio" name="resident_type" value="foreign" <?php if($employee['resident_type']=='foreign') echo "checked"; ?>> Foreign resident</label><br>
                            <label><input type="radio" name="resident_type" value="working_holiday" <?php if($employee['resident_type']=='working_holiday') echo "checked"; ?>> Working holiday maker</label>
                        </div>
                        <span class="fieldError text-red-500 text-sm" id="resident_type_error"></span>
                    </div>

                    <!-- SECTION: LOANS (Updated with No option) -->
                    <div class="section-wrap mb-10 p-6 bg-white rounded-lg shadow-sm border border-gray-200">
                        <p class="text-lg font-semibold text-gray-800 mb-4">Do you have any outstanding student loans? <span class="text-red-500">*</span></p>

                        <div class="space-y-3 text-gray-700">
                            <label><input type="radio" name="loan_type" value="no" <?php if($employee['loan_type']=='no' || empty($employee['loan_type'])) echo "checked"; ?>> No</label><br>
                            <label><input type="radio" name="loan_type" value="higher_education" <?php if($employee['loan_type']=='higher_education') echo "checked"; ?>> HELP</label><br>
                            <label><input type="radio" name="loan_type" value="vet_student" <?php if($employee['loan_type']=='vet_student') echo "checked"; ?>> VET Student Loan</label><br>
                            <label><input type="radio" name="loan_type" value="financial_supplement" <?php if($employee['loan_type']=='financial_supplement') echo "checked"; ?>> Financial Supplement</label><br>
                            <label><input type="radio" name="loan_type" value="student_loan" <?php if($employee['loan_type']=='student_loan') echo "checked"; ?>> Student Start-up Loan</label><br>
                            <label><input type="radio" name="loan_type" value="trade_loan" <?php if($employee['loan_type']=='trade_loan') echo "checked"; ?>> Trade Support Loan</label>
                        </div>
                        <span class="fieldError text-red-500 text-sm" id="loan_type_error"></span>
                    </div>

                    <!-- TAX-FREE THRESHOLD -->
                    <div class="section-wrap mb-10 p-6 bg-white rounded-lg shadow-sm border border-gray-200">
                        <p class="text-lg font-semibold text-gray-800 mb-4">Would you like to claim the tax-free threshold? <span class="text-red-500">*</span></p>

                        <div class="space-x-6 text-gray-700">
                            <label><input type="radio" name="claim_tax_free" value="yes" <?php if($employee['claim_tax_free']=='yes') echo "checked"; ?>> Yes</label>
                            <label><input type="radio" name="claim_tax_free" value="no" <?php if($employee['claim_tax_free']=='no') echo "checked"; ?>> No</label>
                        </div>
                        <span class="fieldError text-red-500 text-sm" id="claim_tax_free_error"></span>
                    </div>

                    <!-- Removed the job type question as requested -->

                    <!-- SUBMIT BUTTON -->
                    <div class="flex justify-end pt-6 border-t border-gray-200">
                        <input type="button" value="SAVE AND CONTINUE"
                               rel="superAnnuation"
                               id="save_continue_tax"
                               class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition-all">
                    </div>
                </form>


            </div>
            <!-- Police Clearance form start here-->
            <div class="tab-pane" id="policeClearance" role="policeClearance">

                <!-- SECTION TITLE -->
                <p class="fw-bold text-lg font-semibold text-gray-800 mb-3">
                    Police clearance certificate is mandatory to commence work.
                    Please upload your certificate <span class="text-red-500">*</span>
                </p>

                <!-- EXISTING UPLOADED FILES -->
                <?php $requiredPolice = 0; // later make it adjustable as confgiured from settings page
                if(isset($employee['police_certificate']) && $employee['police_certificate'] !=''){
                    $requiredPolice = 0;
                    ?>
                    <?php foreach(unserialize($employee['police_certificate']) as $attachment){ ?>
                        <p class="mb-2">
                            <a href="<?php echo base_url().'uploaded_files/'.$this->session->userdata('tenantIdentifier').'/HR/OnboardingFiles/'.$attachment; ?>"
                               target="_blank"
                               class="btn btn-sm btn-success px-4 py-2 text-white rounded-md shadow-sm hover:bg-green-700 transition bg-green-600">
                                View Attachment
                            </a>
                        </p>
                    <?php } ?>
                <?php } else { ?>
                    <p class="text-sm text-gray-600 mb-4">Refresh page to view already uploaded document</p>
                <?php } ?>

                <!-- FORM START -->
                <form role="form"
                      id="policeDetailsForm"
                      method="post"
                      action=""
                      enctype="multipart/form-data"
                      class="mt-6">

                    <input type="hidden" name="emp_id" value="<?php echo $employee['emp_id']; ?>">
                    <input type="hidden" name="stepsCompleted" class="policeDetailsFormSteps" value="5">

                    <!-- FILE UPLOAD BOX (No Dropzone, Only UI) -->
                    <div class="d-flex justify-content-center my-5">
                        <div class="border border-3 border-dashed border-primary rounded-4 bg-light text-center position-relative overflow-hidden"
                             style="max-width: 520px; width: 100%; padding: 60px 20px; cursor: pointer; transition: all 0.3s ease;"
                             id="policeUploadArea">

                            <!-- Upload Icon & Text -->
                            <div class="mb-4">
                                <i class="ri-upload-cloud-2-fill text-primary" style="font-size: 64px;"></i>
                            </div>
                            <h5 class="text-dark fw-semibold mb-2">Click to upload Police Clearance</h5>
                            <p class="text-muted small mb-0">
                                PDF, JPG, PNG • Max 10MB • Multiple files allowed
                            </p>

                            <!-- Hidden File Input (covers entire area) -->
                            <input type="file"
                                   id="police"
                                   name="userfile[]"
                                   multiple
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   class="position-absolute top-0 start-0 w-100 h-100 "
                                   style="cursor: pointer; z-index: 10;"
                                   data-required="<?php echo $requiredPolice; ?>"
                            >

                            <div id="selectedFiles" class="mt-4"></div>
                        </div>
                    </div>

                    <!-- BUTTON -->
                    <div class="mt-8 flex justify-end">
                        <input type="button"
                               rel="superAnnuation"
                               name="contact_submit"
                               id="save_continue_police"
                               value="SAVE AND CONTINUE"
                               class="btn btn-success btn-ph px-10 py-4 bg-green-600 hover:bg-green-700 text-white
                          font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                    </div>

                </form>

            </div>

            <!-- Super Annuation form start here-->
            <div class="tab-pane" id="superAnnuation" role="superAnnuation">
                <form id="annuationDetailsForm" class="max-w-6xl mx-auto" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="emp_id" value="<?= $employee['emp_id'] ?>">
                    <input type="hidden" name="stepsCompleted" class="annuationDetailsFormSteps" value="6">
                    <input type="hidden" name="check_super_type" class="check_super_type" value="<?= $employee['check_super_type'] ?? 'yes' ?>">
                    <input type="hidden" name="nominatedByEmployer" id="nominatedByEmployer" value="<?= $employee['nominatedByEmployer'] == 1 ? 1 : 0 ?>">

                    <div class="">
                        <!-- Question -->
                        <h3 class="text-xl font-bold text-gray-900 mb-8">
                            Do you have an existing superannuation account?
                        </h3>
                        <style>
                            .tablinks_superAnnuation.active {
                                background-color: #17A34B !important; /* blue-700 */
                                color: white !important;
                                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                            }
                        </style>
                        <!-- Yes / No Tabs -->
                        <div class="flex flex-wrap gap-4 mb-10">
                            <!-- YES TAB -->
                            <a href="javascript:void(0)"
                               onclick="openThisTab(event, 'YesSuper', 'superAnnuation')"
                               class="tablinks tablinks_superAnnuation px-10 py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-md
          <?= (!isset($employee['nominatedByEmployer']) || $employee['nominatedByEmployer'] == 0) ? 'bg-blue-600 text-white active' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                                Yes
                            </a>

                            <!-- NO TAB -->
                            <a href="javascript:void(0)"
                               onclick="openThisTab(event, 'NoSuper', 'superAnnuation')"
                               class="tablinks tablinks_superAnnuation px-10 py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-md
          <?= (isset($employee['nominatedByEmployer']) && $employee['nominatedByEmployer'] == 1) ? 'bg-blue-600 text-white active' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                                No
                            </a>
                        </div>

                        <!-- YES: Existing Fund Details -->
                        <div id="YesSuper" class="tabcontent tabcontent_superAnnuation" style="display: <?= (!isset($employee['nominatedByEmployer']) || $employee['nominatedByEmployer'] == 0) ? 'block' : 'none' ?>;">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 mb-6">
                                <h4 class="text-xl font-bold text-gray-800 mb-6">Your Super Fund Details</h4>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="pdf_first_name" id="pdf_name" required
                                               value="<?= $employee['pdf_first_name'] ?? '' ?>"
                                               class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy required">
                                        <span class="text-red-500 text-xs" id="pdf_name_error"></span>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Employee ID (if known)</label>
                                        <input type="text" name="pdf_emp_id_no"
                                               value="<?= $employee['pdf_emp_id_no'] ?? '' ?>"
                                               class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Fund ABN <span class="text-red-500">*</span></label>
                                        <input type="text" name="pdf_apra_fund_abh" required
                                               value="<?= $employee['pdf_apra_fund_abh'] ?? '' ?>"
                                               class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy required">
                                        <span class="text-red-500 text-xs" id="pdf_apra_fund_abh_error"></span>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Fund Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="pdf_apra_fund_name" required
                                               value="<?= $employee['pdf_apra_fund_name'] ?? '' ?>"
                                               class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy required">
                                        <span class="text-red-500 text-xs" id="pdf_apra_fund_name_error"></span>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">USI <span class="text-red-500">*</span></label>
                                        <input type="text" name="pdf_apra_fund_usi" required
                                               value="<?= $employee['pdf_apra_fund_usi'] ?? '' ?>"
                                               class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy required">
                                        <span class="text-red-500 text-xs" id="pdf_apra_fund_usi_error"></span>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Member Number</label>
                                        <input type="text" name="pdf_apra_fund_member_no"
                                               value="<?= $employee['pdf_apra_fund_member_no'] ?? '' ?>"
                                               class="form-control w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-navy">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- NO: Use Employer Fund -->
                        <div id="NoSuper" class="tabcontent tabcontent_superAnnuation" style="display: <?= (isset($employee['nominatedByEmployer']) && $employee['nominatedByEmployer'] == 1) ? 'block' : 'none' ?>;">
                            <div class="bg-green-50 border-2 border-green-200 rounded-2xl p-8 text-center">
                                <label class="flex items-center justify-center space-x-4 cursor-pointer text-lg font-bold text-gray-800">
                                    <input type="checkbox" id="select_nominatedByEmployer"
                                           class="w-8 h-8 text-green-600 rounded-lg focus:ring-green-500"
                                        <?= $employee['nominatedByEmployer'] == 1 ? 'checked' : '' ?>
                                           onchange="document.getElementById('nominatedByEmployer').value = this.checked ? 1 : 0">
                                    <span>I agree to use the super fund nominated by my employer <span class="text-red-500">*</span></span>
                                </label>
                                <span class="text-red-500 text-sm mt-3 block" id="nominatedByEmployer_error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <input type="button"
                               rel="privacyPolicy"
                               name="contact_submit"
                               id="save_continue_annuation"
                               value="SAVE AND CONTINUE"
                               class="btn btn-success btn-ph px-10 py-4 bg-green-600 hover:bg-green-700 text-white
                          font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                    </div>

                    <!-- Submit Button -->

                </form>

            </div>
            <!--Privacy Policyform start here-->
            <div class="tab-pane" id="privacyPolicy" role="privacyPolicy">
                <p class="fw-bold text-lg font-semibold text-gray-800 mb-4">
                    You must read and agree to the below attached company policies, staff induction
                    and job description policies before submitting the form.
                </p>

                <form role="form" id="privacyDetailsForm" method="post" action="" enctype="multipart/form-data" class="space-y-8">


                    <input type="hidden" name="emp_id" value="<?php echo $employee['emp_id']; ?>">
                    <input type="hidden" name="stepsCompleted" class="privacyDetailsFormSteps" value="7">

                    <div class="row grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                        <!-- ----------------------- STAFF INDUCTION ----------------------- -->
                        <div class="col-lg-4 bg-white p-6 rounded-xl shadow border border-gray-200">

                            <h4 class="text-black text-xl font-semibold mb-4">Staff Induction Manual</h4>

                            <?php $inductionAttachment = ''; ?>
                            <?php if(isset($uploadedFiles) && !empty($uploadedFiles)){ ?>
                                <?php foreach($uploadedFiles as $uploadedFile) { ?>
                                    <?php if($uploadedFile['metaData'] == 'induction') {
                                        $inductionAttachment = unserialize($uploadedFile['data']); ?>
                                        <a href="<?php echo base_url().'uploaded_files/'.$this->session->userdata('tenantIdentifier').'/HR/OtherFiles/'.$inductionAttachment[0] ?>"
                                           target="_blank"
                                           class="btn btn-sm btn-success px-4 py-2 text-white rounded-md shadow-sm hover:bg-green-700 transition bg-green-600">
                                            View Staff Induction Manual
                                        </a>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>

                            <div class="form-group mt-4">
                                <div class="pdf-view-wrap border rounded-lg overflow-hidden shadow-sm h-[400px]">

                                    <iframe src="<?php echo base_url();?>uploaded_files/<?php echo $this->session->userdata('tenantIdentifier'); ?>/HR/OtherFiles/<?php echo $inductionAttachment[0]; ?>"
                                            class="w-full h-full"></iframe>

                                </div>

                                <div class="form-check mt-3 flex items-start space-x-2">
                                    <input class="form-check-input mt-1"
                                           id="agree_terms_one"
                                           type="checkbox"
                                           value="1"
                                           name="agree_terms_one">

                                    <label class="form-check-label text-gray-700 text-sm leading-5" for="agree_terms_one">
                                        I read, understood and agree to the Staff Induction Manual. <span class="text-red-500">*</span>
                                    </label>
                                </div>

                                <span class="fieldError text-red-500 text-sm" id="agree_terms_one_error"></span>
                            </div>

                        </div>

                        <!-- ----------------------- POLICIES ----------------------- -->
                        <div class="col-lg-4 bg-white p-6 rounded-xl shadow border border-gray-200">

                            <h4 class="text-black text-xl font-semibold mb-4">Company Policies and Procedures</h4>

                            <?php $policyAttachment = ''; ?>
                            <?php if(isset($uploadedFiles) && !empty($uploadedFiles)){ ?>
                                <?php foreach($uploadedFiles as $uploadedFile) { ?>
                                    <?php if($uploadedFile['metaData'] == 'policy') {
                                        $policyAttachment = unserialize($uploadedFile['data']); ?>
                                        <a href="<?php echo base_url().'uploaded_files/'.$this->session->userdata('tenantIdentifier').'/HR/OtherFiles/'.$policyAttachment[0] ?>"
                                           target="_blank"
                                           class="btn btn-sm btn-success px-4 py-2 text-white rounded-md shadow-sm hover:bg-green-700 transition bg-green-600">
                                            View Policies
                                        </a>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>

                            <div class="form-group mt-4">

                                <div class="pdf-view-wrap border rounded-lg overflow-hidden shadow-sm h-[400px]">
                                    <iframe src="<?php echo base_url();?>uploaded_files/<?php echo $this->session->userdata('tenantIdentifier'); ?>/HR/OtherFiles/<?php echo $policyAttachment[0]; ?>"
                                            class="w-full h-full"></iframe>
                                </div>

                                <div class="form-check mt-3 flex items-start space-x-2">
                                    <input class="form-check-input mt-1"
                                           type="checkbox"
                                           id="agree_terms_two"
                                           value="1"
                                           name="agree_terms_two">

                                    <label class="form-check-label text-gray-700 text-sm leading-5" for="agree_terms_two">
                                        I read, understood and agree to the Company Policies and Procedures Manual. <span class="text-red-500">*</span>
                                    </label>
                                </div>

                                <span class="fieldError text-red-500 text-sm" id="agree_terms_two_error"></span>
                            </div>

                        </div>

                        <!-- ----------------------- JOB DESCRIPTION ----------------------- -->
                        <div class="col-lg-4 bg-white p-6 rounded-xl shadow border border-gray-200">

                            <?php
                            $fileNameArray = unserialize($employee['job_desc']);
                            if(isset($fileNameArray) && !empty($fileNameArray)){
                                $fileName = $fileNameArray[0];
                                $file_parts = pathinfo($fileName);
                                ?>

                                <h4 class="text-black text-xl font-semibold mb-4">Job Description</h4>

                                <a class="btn btn-sm btn-success px-4 py-2 text-white rounded-md shadow-sm hover:bg-green-700 transition bg-green-600"
                                   href="<?php echo base_url();?>uploaded_files/<?php echo $this->session->userdata('tenantIdentifier'); ?>/HR/JobDescr/<?php echo $fileName; ?>"
                                   target="_blank">
                                    View JD
                                </a>

                                <div class="form-group mt-4">

                                    <div class="pdf-view-wrap border rounded-lg overflow-hidden shadow-sm h-[400px]">

                                        <?php if($file_parts['extension'] == 'pdf'){ ?>
                                            <iframe src="<?php echo base_url();?>uploaded_files/<?php echo $this->session->userdata('tenantIdentifier'); ?>/HR/JobDescr/<?php echo $fileName; ?>"
                                                    class="w-full h-full"></iframe>
                                        <?php } else { ?>
                                            <iframe src="https://docs.google.com/viewer?url=<?php echo base_url();?>uploaded_files/<?php echo $this->session->userdata('tenantIdentifier'); ?>/HR/JobDescr/<?php echo $fileName; ?>&embedded=true"
                                                    class="w-full h-full"></iframe>
                                        <?php } ?>

                                    </div>

                                    <div class="form-check mt-3 flex items-start space-x-2">
                                        <input class="form-check-input mt-1"
                                               type="checkbox"
                                               id="agree_terms_three"
                                               value="1"
                                               name="agree_terms_three">

                                        <label class="form-check-label text-gray-700 text-sm leading-5" for="agree_terms_three">
                                            I read, understood and agree to the Job Descriptions Manual. <span class="text-red-500">*</span>
                                        </label>
                                    </div>

                                    <span class="fieldError text-red-500 text-sm" id="agree_terms_three_error"></span>
                                </div>

                            <?php } ?>

                        </div>

                    </div>

                    <!-- BUTTON -->
                    <div class="flex justify-end mt-8">
                        <input type="button"
                               name="contact_submit"
                               id="save_continue_privacy"
                               value="Submit"
                               class="btn btn-success btn-ph px-10 py-4 bg-green-600 hover:bg-green-700
                      text-white font-semibold rounded-lg shadow-md hover:shadow-lg 
                      transition-all duration-200">
                    </div>

                </form>

            </div>
        </div>




    </div>

    <div id="loaderContainer" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 ">
        <div class="w-32 h-32 border-8 border-blue-700 border-t-transparent rounded-full animate-spin"></div>
    </div>
</main>


<!-- SUCCESS MODAL (HIDDEN BY DEFAULT) -->
<div id="onboardSuccessModal"
     class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">

    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-8 text-center">

        <!-- Success Animation Icon -->


        <!-- Title -->
        <h2 class="text-2xl font-bold text-gray-800 mb-2">
            Success
        </h2>

        <!-- Message -->
        <p class="text-gray-600 mb-6">
            Thank you for submitting your onboarding application.
            You will receive an email shortly with the HR employee portal login steps.
            Please contact your manager if you face any issues.
        </p>

        <!-- Close Button -->
        <button id="closeSuccessModal"
                class="mt-2 px-6 py-2 bg-red-500 hover:bg-orange-600 text-white rounded-lg transition">
            Close
        </button>
    </div>
</div>

<!-- allow Camera access for browser Instruction Modal -->
<div id="cameraHelpModal" class="fixed inset-0 bg-black/40 z-[99999] hidden flex items-center justify-center">
    <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 relative max-h-[90vh] overflow-y-auto">
        <!-- Close Button -->
        <button onclick="hideCameraHelp()"
                class="absolute top-3 right-3 text-gray-500 hover:text-black">
            ✕
        </button>
        <h2 class="text-xl font-bold mb-4 text-navy">How to Enable Camera Access</h2>
        <div class="space-y-4 text-gray-700 text-sm">
            <div>
                <h3 class="font-semibold text-navy">Google Chrome</h3>
                <ol class="list-decimal ml-5">
                    <li>Click the camera icon (or lock/tune icon) in the address bar.</li>
                    <li>Click on <strong>Camera</strong> and select <strong>"Allow"</strong>.</li>
                    <li>Alternatively: Click the three dots → Settings → Privacy and security → Site Settings → Camera.</li>
                    <li>Reload this page.</li>
                </ol>
            </div>
            <div>
                <h3 class="font-semibold text-navy">Safari (Mac)</h3>
                <ol class="list-decimal ml-5">
                    <li>Go to Safari → Settings for This Website (or Safari → Preferences → Websites).</li>
                    <li>Select <strong>Camera</strong> in the left sidebar.</li>
                    <li>Find this website and set to <strong>Allow</strong>.</li>
                    <li>Reload the page.</li>
                </ol>
            </div>
            <div>
                <h3 class="font-semibold text-navy">Firefox</h3>
                <ol class="list-decimal ml-5">
                    <li>Click the camera icon (or site information icon) in the address bar.</li>
                    <li>Click the <strong>X</strong> next to "Blocked Temporarily" if camera is blocked.</li>
                    <li>Click <strong>Allow</strong> when prompted again.</li>
                    <li>Or go to: Settings → Privacy & Security → Permissions → Camera → Settings.</li>
                </ol>
            </div>
            <div>
                <h3 class="font-semibold text-navy">Microsoft Edge</h3>
                <ol class="list-decimal ml-5">
                    <li>Click the lock icon (or camera icon) in the address bar.</li>
                    <li>Click <strong>Permissions for this site</strong>.</li>
                    <li>Set <strong>Camera</strong> to <strong>Allow</strong>.</li>
                    <li>Reload the page.</li>
                </ol>
            </div>
            <div>
                <h3 class="font-semibold text-navy">Mobile Browsers</h3>
                <ol class="list-decimal ml-5">
                    <li><strong>iOS Safari:</strong> Settings → Safari → Camera → Allow.</li>
                    <li><strong>Android Chrome:</strong> Settings → Site settings → Camera → Allow for this site.</li>
                </ol>
            </div>
        </div>
    </div>
</div>




<script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<script>
    function showCameraHelp() {
        document.getElementById("cameraHelpModal").classList.remove("hidden");
    }

    function hideCameraHelp() {
        document.getElementById("cameraHelpModal").classList.add("hidden");
    }


    $(document).ready(function(){
        $("#loaderContainer").hide();
    })
    async function startCamera() {

        // Show loader
        $("#loaderContainer").show();

        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const emp_id = document.getElementById('emp_id').value;
        const btn = document.getElementById('faceVerifyBtn');
        const message = document.getElementById('verificationMessage');
        const preVerificationmessage = document.getElementById('verificationPreMessage');
        const preview = document.getElementById('capturedImagePreview');
        const savedCapturedImage = document.getElementById('savedCapturedImage');
        const errorBox = document.getElementById('verificationError');

        // Reset UI
        errorBox.textContent = "";
        btn.innerText = "Capture Photo";
        savedCapturedImage.classList.add("hidden");
        preview.classList.add("hidden");

        // --- STEP 1: Load Models ---
        try {
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri('/External/models/tiny_face_detector'),
                faceapi.nets.faceLandmark68Net.loadFromUri('/External/models/face_landmark_68'),
                faceapi.nets.faceRecognitionNet.loadFromUri('/External/models/face_recognition')
            ]);
        } catch (e) {
            $("#loaderContainer").hide();
            errorBox.textContent = "Unable to load face detection models.";
            return;
        }

        // --- STEP 2: Ask for Camera Access ---
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {

                // Camera opened successfully → hide loader
                $("#loaderContainer").hide();

                video.classList.remove("hidden");
                video.srcObject = stream;

                video.onloadedmetadata = () => video.play();

                // Once video starts playing
                video.addEventListener('play', async () => {

                    const result = await faceapi
                        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                        .withFaceLandmarks()
                        .withFaceDescriptor();

                    if (result) {

                        // Draw frame to canvas
                        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                        const imageData = canvas.toDataURL('image/jpeg');

                        // Stop camera
                        stream.getTracks().forEach(t => t.stop());
                        video.classList.add("hidden");

                        // Show preview
                        preview.src = imageData;
                        preview.classList.remove("hidden");

                        // Upload to server
                        fetch("<?= base_url('/External/Employee/uploadFaceImage') ?>", {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({
                                emp_id: emp_id,
                                image: imageData
                            })
                        })
                            .then(res => res.json())
                            .then(response => {
                                if (response.status === "success") {
                                    btn.innerText = "Recapture Photo";
                                    message.classList.remove("hidden");
                                    preVerificationmessage.classList.add("hidden");
                                    $('#employeePhoto').val(true);
                                    alert("Face verification completed.");
                                } else {
                                    alert("Upload failed: " + response.message);
                                }
                            });
                    }
                }, { once: true });

            })
            .catch(err => {
                // CAMERA BLOCKED by browser
                document.getElementById("cameraErrorWrapper").classList.remove("hidden");
                document.getElementById("cameraHelpBtn").classList.remove("hidden");
                $("#loaderContainer").hide();
                let msg = "Camera access denied. Please enable camera permission in your browser.";

                if (err.name === "NotAllowedError") {
                    msg = "Camera permission blocked. Please allow camera access.";
                }
                if (err.name === "NotFoundError") {
                    msg = "No camera device detected.";
                }

                errorBox.textContent = msg;
            });
    }

</script>
<script>

    $(document).ready(function() {

        // Initialize visa toggle
        document.querySelectorAll("input[name='visa_status']").forEach(radio => {
            radio.addEventListener("change", toggleVisaFields);
        });

        // Run on load
        toggleVisaFields();

        if($(".check_super_type").val() == 'yes'){
            $(".select_nominatedByEmployer").hide();
        }
        let nominatedByEmployer = '<?php echo $employee['nominatedByEmployer']; ?>';
        let check_tfn_type = '<?php echo $employee['check_tfn_type']; ?>';
        let have_surname_changed = '<?php echo $employee['have_surname_changed']; ?>';
        let stepsCompleted = '<?php echo $employee['stepsCompleted']; ?>';

        if(nominatedByEmployer == 0){
            $("#YesSuper").show();
            $("#NoSuper").hide();
        }else{
            $("#YesSuper").hide();
            $("#NoSuper").show();
        }

        if(check_tfn_type == 'tfn_number'){
            $("#Yes").show();
            $("#No").hide();
        }else{
            $("#Yes").hide();
            $("#No").show();
        }

        if(have_surname_changed =='noChanged'){
            $("#noChanged").show();
            $("#yesChanged").hide();
        }else{
            $("#noChanged").hide();
            $("#yesChanged").show();
        }

        document.getElementById("closeSuccessModal").addEventListener("click", function () {
            document.getElementById("onboardSuccessModal").classList.add("hidden");

            // Redirect after modal closes
            window.location.href = "https://bizadmin.com.au/<?php echo $this->session->userdata('tenantIdentifier'); ?>";
        });


        $('#save_continue_personal').click(function(e){
            var err=0;
            if($('#title').val() == ''){ $('#title_error').html('Please select title');err=1;}
            if($('#first_name').val() == ''){ $('#first_name_error').html('Please enter first name');err=1;}
            if($('#last_name').val() == ''){ $('#last_name_error').html('Please enter last name');err=1;}
            if($('#email').val() == ''){ $('#email_error').html('Please enter email address'); err=1; }
            if($('#phone').val() == ''){ $('#phone_error').html('Please enter phone number'); err=1; }
            if($('#dob').val() == ''){ $('#dob_error').html('Please enter date of birth'); err=1; }
            // if($('#employeePhoto').val() == ''){ $('#verificationError').html('Please capture your photo'); err=1; }

            if($('#street_name').val() == ''){ $('#streetname_error').html('Please enter street name'); err=1; }

            if($('#state').val() == ''){ $('#state_error').html('Please select state'); err=1; }
            if($('#suburb').val() == ''){ $('#suburb_error').html('Please enter suburb'); err=1; }
            if($('#postcode').val() == ''){ $('#postcode_error').html('Please enter postcode'); err=1; }

            // Visa validation
            if(!$('input[name=visa_status]:checked').length){
                $('#visa_status_error').html('Please select visa status');
                err=1;
            } else if($('input[name=visa_status]:checked').val() === 'yes') {
                if($('#visa_expiry').val() == '') {
                    $('#visa_expiry_error').html('Please enter visa expiry date');
                    err=1;
                }
                if($('#visa_attachment')[0].files.length === 0) {
                    $('#visa_attachment_error').html('Please upload visa document');
                    err=1;
                }
            }

            if(err == '0'){
                $('#loaderContainer').show();
                var data1 = $('#personalDetailsForm').serialize();
                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: '/External/Employee/submit_onboarding_process',
                    data: data1,
                    success: function(response){
                        let res = JSON.parse(response)
                        if(res?.status=='success'){
                            proceedFromTab('personalDetails');
                        }
                        else{
                            alert("Some error occured,Please refresh page and try again.")
                        }
                    }
                }); e.preventDefault();
            }
            else{ alert('Please enter all the mandatory fields'); return false; }
        });


// 	emergency form submit
        $('#save_continue_emergency').click(function(e){
            var err=0;
            $('.fieldError').html('');
            $('#emergencyDetailsForm').find('.required').each(function() {
                // if((parseInt($(".emergencyDetailsFormSteps").val()) - parseInt(stepsCompleted)) > 1){
                //     alert("Please complete previous steps first"); activateTab('personalDetails');
                //     err = 1;
                //     return false;
                // }
                if($(this).val() == "") {
                    err = 1;
                    var fieldID=$(this).attr('id');
                    //   console.log(fieldID);
                    $('#'+fieldID+'_error').html('This field should not be empty');
                }
            });
            // validate prev steps are completed before completing further steps


            if(err == '0'){
                $('#loaderContainer').show();
                let data1 = $('#emergencyDetailsForm').serialize();
                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: '/External/Employee/submit_onboarding_process',
                    data: data1,
                    success: function(response){
                        let res = JSON.parse(response)
                        if(res?.status=='success'){
                            proceedFromTab('emergencyDetails');
                        }
                        else{
                            alert("Some error occured,Please refresh page and try again.")
                        }
                    }
                }); e.preventDefault();
            }
            else{ alert('Please enter all the mandatory fields'); return false; }
        });

        // 	bank form submit
        $('#save_continue_bank').click(function(e){
            var err=0;
            $('.fieldError').html('');

            $('#bankDetailsForm').find('.required').each(function() {

                if($(this).val() == "") {

                    err = 1;
                    var fieldID=$(this).attr('id');
                    //   console.log(fieldID);
                    $('#'+fieldID+'_error').html('This field should not be empty');
                }
            });

            if(err == '0'){
                $('#loaderContainer').show();
                let data1 = $('#bankDetailsForm').serialize();
                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: '/External/Employee/submit_onboarding_process',
                    data: data1,
                    success: function(response){
                        let res = JSON.parse(response)
                        if(res?.status=='success'){
                            proceedFromTab('bankDetails');
                        }
                        else{
                            alert("Some error occured,Please refresh page and try again.")
                        }
                    }
                }); e.preventDefault();
            }
            else{ alert('Please enter all the mandatory fields'); return false; }
        });


        // 	tax form submit
        $('input[name=resident_type]').on("change",function(){
            if($(this).val() == 'working_holiday'){
                $(".countryOfOrigin").css("display","block");
            }else{
                $(".countryOfOrigin").css("display","none");
            }
        })
        $('#save_continue_tax').click(function(e){
            if($(".check_tfn_type ").val() == 'tfn_number' && $("#tfn_number").val() == ''){
                alert('Please enter TFN number.');
                $("#tfn_number_error").html('Please Enter TFN Number');
                return false;
            }
            //else if($(".check_tfn_type ").val() == 'tfn_type' && !$('input[name=tfn_type]:checked').length > 0){
            //      alert('Please choose correct TFN option.');
            //      $("#tfn_type_error").html('Please choose correct TFN option');
            //     return false;
            // }

            if($('input[name=resident_type]:checked').val() == 'working_holiday' && $("#origin_country").val() == ''){
                alert('Please enter  your native country');
                $("#resident_type_error").html('Please enter  your native country');
                return false;
            }
            var err=0;

            var allradio_tax = [];

            $('#taxDetailsForm').find('input[type="radio"]').each(function() {
                var fieldName=$(this).attr('name');

                if(fieldName !='loan_type' && fieldName != 'tfn_type'){
                    if(!allradio_tax.includes(fieldName)){
                        if (!$('input[name='+fieldName+']:checked').length > 0) {

                            $('#'+fieldName+'_error').html('This field should not be empty');
                            err=1;
                        }
                    }

                    allradio_tax.push(fieldName);
                }
            });
            // return false;
            // err=1;
            if(err == '0'){
                $('#loaderContainer').show();
                let data1 = $('#taxDetailsForm').serialize();
                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: '/External/Employee/submit_onboarding_process',
                    data: data1,
                    success: function(response){
                        let res = JSON.parse(response)
                        if(res?.status=='success'){
                            proceedFromTab('taxDetails');
                        }
                        else{
                            alert("Some error occured,Please refresh page and try again.")
                        }
                    }
                }); e.preventDefault();
            }
            else{ alert('Please enter all the mandatory fields'); return false; }
        });

        // 	police form submit
        $('#save_continue_police').click(function (e) {
            e.preventDefault();

            const isRequired = $('#police').data('required') == '1';
            const fileInput = $('#police')[0];
            const hasFiles = fileInput && fileInput.files.length > 0;

            if (isRequired && !hasFiles) {
                alert('Please upload required document');
                return;
            }

            // File size check
            const maxSize = 10 * 1024 * 1024;
            for (let file of fileInput.files) {
                if (file.size > maxSize) {
                    alert('File size must be less than 10MB');
                    return;
                }
            }

            $('#loaderContainer').show();

            let formData = new FormData($('#policeDetailsForm')[0]);

            $.ajax({
                type: "POST",
                url: '/External/Employee/submit_onboarding_process',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',

                success: function (res) {
                    if (res && res.status === 'success') {
                        proceedFromTab('policeClearance');
                    } else {
                        alert(res.message || 'Upload failed. Please try again.');
                    }
                },

                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert('Upload failed. Please try again.');
                },

                complete: function () {
                    // 🔥 ALWAYS hide loader
                    $('#loaderContainer').hide();
                }
            });
        });


        // 	annuation
        $('#save_continue_annuation').click(function(e){

            // Determine the selected option from the CURRENTLY VISIBLE sub-tab
            // (YesSuper = existing fund, NoSuper = employer-nominated fund).
            // The hidden check_super_type / nominatedByEmployer fields can be out of
            // sync with what is displayed, so we sync them here to the visible tab.
            var isYesSuper = $('#YesSuper').is(':visible');
            if (isYesSuper) {
                $('.check_super_type').val('yes');
                $('#nominatedByEmployer').val(0);
            } else {
                $('.check_super_type').val('no');
                $('#nominatedByEmployer').val(1);
            }

            $('.fieldError').html('');
            let err = 0;

            if (isYesSuper) {
                let hasError = false;
                // Only validate the visible "Yes" (existing fund) section
                $("#YesSuper .required").each(function() {
                    if ($(this).val() === '' || $(this).val() === null) {
                        $(this).addClass('border-red-500'); // highlight empty fields
                        hasError = true;
                    } else {
                        $(this).removeClass('border-red-500');
                    }
                });

                if (hasError) {
                    alert("Please fill in all required superannuation fund details.");
                    return false; // stop form submission
                }
            } else {
                // "No" tab: must agree to use the employer-nominated fund
                if (!$('#select_nominatedByEmployer').is(":checked")) {
                    $('#nominatedByEmployer_error').html('Please check the checkbox');
                    err = 1;
                }
            }

            if(err == '0'){
                $('#loaderContainer').show();
                let data1 = $('#annuationDetailsForm').serialize();
                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: '/External/Employee/submit_onboarding_process',
                    data: data1,
                    success: function(response){
                        let res = JSON.parse(response)
                        if(res?.status=='success'){
                            proceedFromTab('superAnnuation');
                        }
                        else{
                            alert("Some error occured,Please refresh page and try again.")
                        }
                    }
                }); e.preventDefault();
            }
            else{
                alert('Please enter all the mandatory fields');
                return false;
            }

        });
        $('#save_continue_privacy').click(function(){
            let err=0;

            if($('#agree_terms_one').is(":checked")){}else{ $('#agree_terms_one_error').html('Please agree the terms'); err=1; }
            if($('#agree_terms_two').is(":checked")){}else{ $('#agree_terms_two_error').html('Please agree the terms'); err=1; }
            if ($('#agree_terms_three').is(":visible")) {
                if($('#agree_terms_three').is(":checked")){}else{ $('#agree_terms_three_error').html('Please agree the terms'); err=1; }
            }
            if(err == '0'){
                let data1 = $('#privacyDetailsForm').serialize();
                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: '/External/Employee/submit_onboarding_process',
                    data: data1,
                    beforeSend: function(){
                        $('#loaderContainer').show();
                    },
                    complete:function(data){
                        $('#loaderContainer').hide();
                    },
                    success: function(response){
                        let res = JSON.parse(response)
                        if(res?.status=='success'){
                            $('#loaderContainer').hide();
                            document.getElementById("onboardSuccessModal").classList.remove("hidden");
                        }
                        else{
                            alert("Some error occured,Please refresh page and try again.")
                        }
                    }

                });
            }
            else{
                alert('Please enter all the mandatory fields');
                return false;
            }

        });

        function validate(evt) {
            var theEvent = evt || window.event;
            var key = theEvent.keyCode || theEvent.which;
            key = String.fromCharCode( key );
            var regex = /[0-9]|\./;
            if( !regex.test(key) ) {
                theEvent.returnValue = false;
                if(theEvent.preventDefault) theEvent.preventDefault();
            }
        }



    });

    function openThisTab(evt, selected_value, fieldname) {
        let i, tabcontent, tablinks;

        // Hide all tab contents
        tabcontent = document.getElementsByClassName("tabcontent_" + fieldname);
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Remove "active" from all tab links
        tablinks = document.getElementsByClassName("tablinks_" + fieldname);
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        // Show selected tab content
        document.getElementById(selected_value).style.display = "block";

        // Add "active" class to the clicked tab (this line must be AFTER the loop above!)
        evt.currentTarget.classList.add("active");

        // Your existing logic for hidden fields
        if (fieldname === 'tfn') {
            $('.check_tfn_type').val(selected_value === 'No' ? 'tfn_type' : 'tfn_number');
        }

        if (fieldname === 'superAnnuation') {
            if (selected_value === 'NoSuper') {
                $('.check_super_type').val('no');
                $('#nominatedByEmployer').val(1);
            } else {
                $('.check_super_type').val('yes');
                $('#nominatedByEmployer').val(0);
            }
        }

        if (fieldname === 'surname') {
            if (selected_value === 'noChanged') {
                $('#previous_surname').val(selected_value);
            }
            $('.previous_surname_changed').val(selected_value);
        }
    }

</script>

<script>
    $(document).ready(function () {

        // Default to the first enabled tab
        var defaultTab = (typeof ONBOARDING_ENABLED_TABS !== 'undefined' && ONBOARDING_ENABLED_TABS.length) ? ONBOARDING_ENABLED_TABS[0] : "personalDetails";
        activateTab(defaultTab);

        // Relabel the last enabled tab's button to "Submit" so the user knows it completes the form
        (function(){
            var btnMap = {
                personalDetails: '#save_continue_personal',
                emergencyDetails: '#save_continue_emergency',
                bankDetails: '#save_continue_bank',
                taxDetails: '#save_continue_tax',
                policeClearance: '#save_continue_police',
                superAnnuation: '#save_continue_annuation',
                privacyPolicy: '#save_continue_privacy'
            };
            if(typeof ONBOARDING_ENABLED_TABS !== 'undefined' && ONBOARDING_ENABLED_TABS.length){
                var last = ONBOARDING_ENABLED_TABS[ONBOARDING_ENABLED_TABS.length - 1];
                if(btnMap[last]){ $(btnMap[last]).val('Submit'); }
            }
        })();

        // On click
        $(".tab-btn").on("click", function () {
            let tabId = $(this).data("tab");
            activateTab(tabId);
        });

        // Initialise the step progress indicator for the first active tab
        updateOnboardingProgress($('.tab-btn.active').data('tab') || ONBOARDING_ENABLED_TABS[0]);

    });



    function activateTab(tabId) {
        // Switch tab buttons
        $(".tab-btn").removeClass("active border-navy text-navy bg-blue-50 border-b-4")
            .addClass("text-gray-600 border-transparent");

        $(".tab-btn[data-tab='" + tabId + "']")
            .addClass("active border-navy text-navy bg-blue-50 border-b-4")
            .removeClass("text-gray-600");

        // Switch tab panes
        $(".tab-pane").hide();
        $("#" + tabId).show();

        // Update step circles, connectors and topbar subtitle, then scroll to top
        if (typeof updateOnboardingProgress === 'function') { updateOnboardingProgress(tabId); }
        try { window.scrollTo({ top: 0, behavior: 'smooth' }); } catch (e) { window.scrollTo(0, 0); }
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {

        function toggleVisaField() {
            let value = document.querySelector("input[name='visa_status']:checked")?.value;
            let wrapper = document.getElementById("visaExpiryWrapper");
            if (!wrapper) return;

            if (value === "yes") {
                wrapper.classList.remove("hidden");
            } else {
                wrapper.classList.add("hidden");
            }
        }

        // Attach event listeners
        document.querySelectorAll("input[name='visa_status']").forEach(radio => {
            radio.addEventListener("change", toggleVisaField);
        });

        // Run on load in case the form loads with "yes"
        toggleVisaField();
    });

    let bankAccountCount = 1;

    function addBankAccount() {
        if (bankAccountCount >= 3) {
            alert('You can only add up to 3 bank accounts.');
            return;
        }

        bankAccountCount++;
        document.getElementById(`bankAccount${bankAccountCount}`).classList.remove('hidden');

        // Hide the add button if we've reached 3 accounts
        if (bankAccountCount >= 3) {
            document.getElementById('addBankAccountBtn').style.display = 'none';
        }
    }

    function removeBankAccount(accountNumber) {
        document.getElementById(`bankAccount${accountNumber}`).classList.add('hidden');

        // Clear the form values for this account
        const account = document.getElementById(`bankAccount${accountNumber}`);
        const inputs = account.querySelectorAll('input');
        inputs.forEach(input => input.value = '');

        bankAccountCount--;

        // Show the add button again
        if (bankAccountCount < 3) {
            document.getElementById('addBankAccountBtn').style.display = 'block';
        }
    }

    // Show selected files for police clearance
    function showSelectedFiles(input) {
        const fileList = document.getElementById('selectedFiles');
        fileList.innerHTML = '';

        if (input.files.length > 0) {
            fileList.innerHTML = '<div class="mt-4"><strong>Selected Files:</strong></div>';
            for (let i = 0; i < input.files.length; i++) {
                const file = input.files[i];
                fileList.innerHTML += `<div class="text-sm text-gray-600 mt-1">📄 ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)</div>`;
            }
        }
    }

    // Visa status toggle
    function toggleVisaFields() {
        let value = document.querySelector("input[name='visa_status']:checked")?.value;
        let visaDetails = document.getElementById("visaDetails");
        if (!visaDetails) return;

        let visaExpiry = document.getElementById('visa_expiry');
        let visaAttachment = document.getElementById('visa_attachment');

        if (value === "yes") {
            visaDetails.classList.remove("hidden");
            // Make visa expiry and attachment required
            if (visaExpiry) visaExpiry.setAttribute('required', 'required');
            if (visaAttachment) visaAttachment.setAttribute('required', 'required');
        } else {
            visaDetails.classList.add("hidden");
            // Remove required attributes
            if (visaExpiry) visaExpiry.removeAttribute('required');
            if (visaAttachment) visaAttachment.removeAttribute('required');
        }
    }
</script>

<footer id="footer" class="bg-white border-t border-gray-200 py-6 mt-16">
    <div class="max-w-7xl mx-auto text-center">
        <p class="text-gray-500 text-sm"><?php echo date('Y') ?> © Bizadmin</p>
    </div>
</footer>

<div id="loaderContainer"
     class="fixed inset-0 z-50 hidden 
            flex items-center justify-center 
            bg-black bg-opacity-40 backdrop-blur-sm">

    <div class="flex flex-col items-center space-y-4">

        <!-- Animated Loader -->
        <div class="w-16 h-16 border-4 border-success-700 border-t-transparent 
                    rounded-full animate-spin"></div>

        <!-- Optional Text -->
        <p class="text-white text-lg font-semibold tracking-wide animate-pulse">
            Loading, please wait...
        </p>

    </div>
</div>
</body>
</html>