<html>
<body>
    <p> Dear Customer</p>
    <p>A new password was requested for your Bizadmin staff account.</p>
    <p>To reset your password, please click on the link below:</p>

	<p><?php
		$CI =& get_instance();
		$tenant = $CI->session->userdata('tenantIdentifier');
		$resetLink = site_url('auth/reset_password/' . $forgotten_password_code)
			. (!empty($tenant) ? '?tenant=' . urlencode($tenant) : '');
		echo sprintf(lang('email_forgot_password_subheading'), '<a href="' . $resetLink . '">' . lang('email_forgot_password_link') . '</a>');
	?></p>
<p>Kind regards,</p>
<p> Bizadmin</p>
</body>
</html>