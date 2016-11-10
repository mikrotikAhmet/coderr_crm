<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title>Your new password on <?php echo get_option('company_name'); ?></title></head>
<body>
	<div style="max-width: 800px; margin: 0; padding: 30px 0;">
		<table width="80%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="5%"></td>
				<td align="left" width="95%" style="font: 13px/18px Arial, Helvetica, sans-serif;">
					<h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;">Your new password on <?php echo get_option('company_name'); ?></h2>
					You have changed your password.<br />
					Please, keep it in your records so you don't forget it.<br />
					<br />
					Your email address: <?php echo $email; ?><br />
					If this wasnt you, please contact us.<br />
					<br />
					<br />
					<?php echo get_option('email_signature'); ?>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>