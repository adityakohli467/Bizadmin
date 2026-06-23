<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Onboarding Form</title>
</head>

<body style="font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f7f9fc; color: #333;">

    <p style="font-size: 16px;">Hello <strong><?php echo $employeeName; ?></strong>,</p>

    <p style="font-size: 15px;">
        Welcome to <strong>BizAdmin</strong> — we’re excited to have you onboard!
    </p>

    <p style="font-size: 15px; margin-top: 20px;">
        You have been granted system access for:
    </p>

    <?php if (!empty($locationNamesList)) { ?>
    <p style="font-size: 15px;">You have access to the following locations:</p>
    <ul>
        <?php foreach ($locationNamesList as $loc) { ?>
            <li><?php echo $loc; ?></li>
        <?php } ?>
    </ul>
<?php } ?>



    <p style="font-size: 15px; margin-top: 20px;">
        Your HR Portal will allow you to:
    </p>

    <ul style="font-size: 15px; line-height: 1.6;">
        <li>View your <strong>rosters</strong> and <strong>timesheets</strong></li>
        <li>Update your <strong>employee profile</strong> and <strong>leave details</strong></li>
        <li>Submit required <strong>compliance forms</strong></li>
        <li>Communicate with the <strong>management team</strong></li>
    </ul>

    <p style="font-size: 15px; margin-top: 20px;">
        To activate your account, please complete your onboarding by clicking the button below:
    </p>

    <div style="margin: 30px 0;">
        <a href="<?php echo $onboardingUrl; ?>" style="text-decoration: none;">
            <button style="
                cursor: pointer;
                display: inline-block;
                width: 250px;
                height: 45px;
                background: #1f3a5f;
                padding: 4px;
                text-align: center;
                border-radius: 6px;
                color: #fff;
                font-weight: bold;
                line-height: 25px;
                border: none;
                font-size: 17px;">
                Complete Onboarding
            </button>
        </a>
    </div>

    <p style="font-size: 15px;">
        If you have any questions, please reach out to your manager or the HR team.
    </p>

    <p style="margin-top: 30px; font-size: 15px;">
        Kind Regards,<br>
        <strong>HR Team</strong><br>
        BizAdmin
    </p>

</body>
</html>
