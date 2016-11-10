<div class="row">
	<div class="col-md-6">
		<h4><?php echo _l('settings_smtp_settings_heading'); ?></h4>
		<p class="text-muted"><?php echo _l('settings_smtp_settings_subheading'); ?></p>
		<hr />
		<?php echo render_input('settings[smtp_host]','settings_email_host',get_option('smtp_host')); ?>
		<?php echo render_input('settings[smtp_port]','settings_email_port',get_option('smtp_port')); ?>
		<?php echo render_input('settings[smtp_email]','settings_email',get_option('smtp_email'),'email'); ?>
		<?php echo render_input('settings[smtp_password]','settings_email_password',get_option('smtp_password')); ?>
		<?php echo render_input('settings[smtp_email_charset]','settings_email_charset',get_option('smtp_email_charset')); ?>
		<?php echo render_textarea('settings[email_signature]','settings_email_signature',get_option('email_signature')); ?>
	</div>
	<div class="col-md-6">
		<h4><?php echo _l('settings_send_test_email_heading'); ?></h4>
		<p class="text-muted"><?php echo _l('settings_send_test_email_subheading'); ?></p>
		<hr />
		<?php echo render_input('test_email','settings_send_test_email_string'); ?>
		<div class="form-group">
			<button type="button" class="btn btn-info test_email">Test</button>
		</div>

	</div>
</div>
