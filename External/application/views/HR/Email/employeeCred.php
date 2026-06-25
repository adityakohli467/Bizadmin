<?php
    // Branding fallbacks so the template is safe if a variable is not supplied.
    $orgName  = isset($orgName) && $orgName !== '' ? $orgName : 'Bizadmin';
    $resetUrl = isset($resetUrl) && $resetUrl !== '' ? $resetUrl : (isset($portalUrl) ? $portalUrl : '#');
    // Portal link: href uses the full URL, the visible label drops the https:// prefix.
    $portalUrl   = isset($portalUrl) ? $portalUrl : '#';
    $portalLabel = isset($portalLabel) && $portalLabel !== '' ? $portalLabel : preg_replace('#^https?://#', '', $portalUrl);
    $empPin      = isset($empPin) ? $empPin : '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($orgName); ?> HR Portal</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:30px 0;">
    <tr>
        <td align="center">

            <!-- Main Container -->
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08);">

                <!-- Header -->
                <tr>
                    <td style="background:#1a2332; padding:24px; text-align:center;">
                        <h1 style="color:#ffffff; margin:0; font-size:22px; font-weight:600;">
                            Welcome to <?php echo htmlspecialchars($orgName); ?>
                        </h1>
                        <p style="color:#cfd6e0; margin:6px 0 0; font-size:14px;">
                            HR Management Portal
                        </p>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:30px; color:#333333; font-size:14px; line-height:1.6;">

                        <p style="margin-top:0;">
                            Hello <strong><?php echo htmlspecialchars($empName); ?></strong>,
                        </p>

                        <p>
                            Welcome to your new <strong><?php echo htmlspecialchars($orgName); ?> HR Portal</strong>.
                        </p>

                        <p>
                            Through this portal, you will be able to:
                        </p>

                        <ul style="padding-left:18px; margin:10px 0 20px;">
                            <li>View your rosters and timesheets</li>
                            <li>Communicate with management</li>
                            <li>Update your employee and leave details</li>
                            <li>Submit required compliance forms</li>
                        </ul>

                        <!-- Login Box -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fb; border:1px solid #e5e7eb; border-radius:6px; padding:16px; margin:20px 0;">
                            <tr>
                                <td>
                                    <p style="margin:0 0 10px; font-weight:600; color:#1a2332;">
                                        Login Details
                                    </p>
                                    <p style="margin:0;">
                                        <strong>Portal Link:</strong><br>
                                        <a href="<?php echo $portalUrl; ?>" style="color:#2563eb; text-decoration:none;">
                                            <?php echo htmlspecialchars($portalLabel); ?>
                                        </a>
                                    </p>
                                    <p style="margin:10px 0 0;">
                                        <strong>Username:</strong> <?php echo htmlspecialchars($empEmail); ?>
                                    </p>
                                    <?php if ($empPin !== '') : ?>
                                    <p style="margin:10px 0 0;">
                                        <strong>Your Employee PIN:</strong>
                                        <span style="display:inline-block; font-size:18px; font-weight:bold; color:#1a2332; letter-spacing:2px;"><?php echo htmlspecialchars($empPin); ?></span>
                                    </p>
                                    <p style="margin:4px 0 0; font-size:12px; color:#6b7280;">
                                        Use this PIN to clock in/out. Keep it secure and do not share it.
                                    </p>
                                    <?php endif; ?>
                                    <p style="margin:10px 0 0;">
                                        <strong>Password:</strong>
                                        <a href="<?php echo $resetUrl; ?>" style="color:#2563eb; text-decoration:none;">
                                            Set your password here
                                        </a>
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <p>
                            If you have any questions or need assistance, please contact your manager.
                        </p>

                        <p style="margin-bottom:0;">
                            Kind regards,<br>
                            <strong><?php echo htmlspecialchars($orgName); ?> Team</strong>
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background:#f4f6f8; padding:16px; text-align:center; font-size:12px; color:#6b7280;">
                        © <?php echo date('Y'); ?> <?php echo htmlspecialchars($orgName); ?>. All rights reserved.
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
