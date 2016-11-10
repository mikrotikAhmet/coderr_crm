<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title>Setup your new password on <?php echo get_option('company_name'); ?></title></head>
<body>
	<div style="max-width: 800px; margin: 0; padding: 30px 0;">
		<table width="80%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="5%"></td>
				<td align="left" width="95%" style="font: 13px/18px Arial, Helvetica, sans-serif;">
					<h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;">
						Setup your new password on <?php echo get_option('company_name'); ?></h2>
						Please use the following link to setup your new password.<br />
						Please, keep it in your records so you don't forget it.<br />
						Please set your new password in 48 hours. After that you wont be able to set your password.
						<br />
						Your email address for login: <?php echo $email; ?><br />
						<br />
						<big style="font: 16px/18px Arial, Helvetica, sans-serif;"><b><a href="<?php echo site_url('authentication/set_password/'.$staff.'/'.$userid.'/'.$new_pass_key); ?>" style="color: #3366cc;">Create a new password</a></b></big><br />
						<br />
						Link doesn't work? Copy the following link to your browser address bar:<br />
						<nobr><a href="<?php echo site_url('authentication/set_password/'.$staff.'/'.$userid.'/'.$new_pass_key); ?>" style="color: #3366cc;"><?php echo site_url('authentication/set_password/'.$staff.'/'.$userid.'/'.$new_pass_key); ?></a></nobr><br />
						<br />
						<br />
						<?php echo get_option('email_signature'); ?>
					</td>
				</tr>
			</table>
		</div>
	</body>
	</html>
