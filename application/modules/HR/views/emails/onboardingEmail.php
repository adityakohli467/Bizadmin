<?php
    // Branding fallbacks so the template is safe if a variable is not supplied.
    $orgName       = isset($orgName) && $orgName !== '' ? $orgName : 'BizAdmin';
    $locationLabel = isset($locationLabel) && $locationLabel !== '' ? $locationLabel : '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Complete Your Employee Onboarding</title>
</head>

<body style="font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f7f9fc; color: #333;">

    <p style="font-size: 16px;">Hello <strong><?php echo $employeeName; ?></strong>,</p>

    <p style="font-size: 15px;">
        Welcome to <strong><?php echo htmlspecialchars($orgName); ?></strong>! We're excited to have you join the team<?php echo $locationLabel !== '' ? ' at <strong>' . htmlspecialchars($locationLabel) . '</strong>' : ''; ?>.
    </p>

    <p style="font-size: 15px;">
        To get started, you'll need to complete your employee onboarding through BizAdmin. This only takes a few minutes and ensures your employment records are set up correctly before your first shift.
    </p>

    <p style="font-size: 15px; margin-top: 20px;">
        During onboarding, you'll be able to:
    </p>

    <ul style="font-size: 15px; line-height: 1.6;">
        <li>Complete your personal and employment details</li>
        <li>Provide your emergency contact information</li>
        <li>Upload required licences, certifications and identification documents</li>
        <li>Submit your tax, superannuation and banking details</li>
        <li>Review and acknowledge company policies and workplace documents</li>
        <li>Sign any required employment forms electronically</li>
    </ul>

    <p style="font-size: 15px; margin-top: 20px;">
        Once your onboarding has been completed and approved, you'll be able to access your Employee Portal where you can:
    </p>

    <ul style="font-size: 15px; line-height: 1.6;">
        <li>View your roster and upcoming shifts</li>
        <li>Submit your weekly availability</li>
        <li>View your timesheets</li>
        <li>Apply for leave</li>
        <li>Update your personal details</li>
        <li>Receive workplace announcements and notifications</li>
    </ul>

    <p style="font-size: 15px; margin-top: 20px;">
        Please click the button below to begin your onboarding.
    </p>

    <div style="margin: 30px 0;">
        <a href="<?php echo $onboardingUrl; ?>" style="text-decoration: none;">
            <button style="
                cursor: pointer;
                display: inline-block;
                width: 250px;
                height: 45px;
                background: #1a2f52;
                padding: 4px;
                text-align: center;
                border-radius: 6px;
                color: #fff;
                font-weight: bold;
                line-height: 25px;
                border: none;
                font-size: 17px;">
                Complete Your Onboarding
            </button>
        </a>
    </div>

    <p style="font-size: 15px;">
        If you have any questions or require assistance, please contact your manager or HR representative.
    </p>

    <p style="font-size: 15px;">
        We look forward to welcoming you to the team!
    </p>

    <p style="margin-top: 30px; font-size: 15px;">
        Kind regards,<br>
        <strong>The <?php echo htmlspecialchars($orgName); ?> Team</strong>
    </p>

</body>
</html>
