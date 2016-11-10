<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'settings-form')); ?>
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
			<div class="panel_s">
				<div class="panel-body">
						<ul class="nav nav-tabs no-margin" role="tablist">
							<li role="presentation" class="active">
								<a href="#general_settings_tab" aria-controls="general_settings_tab" role="tab" data-toggle="tab">
								<?php echo _l('settings_group_general'); ?></a>
							</li>
							<li role="presentation">
								<a href="#company_info" aria-controls="general_settings_tab" role="tab" data-toggle="tab">
								<?php echo _l('settings_sales_heading_company'); ?></a>
							</li>
							<li role="presentation">
								<a href="#localization_settings_tab" aria-controls="localization_settings_tab" role="tab" data-toggle="tab">
								<?php echo _l('settings_group_localization'); ?></a>
							</li>
							<li role="presentation">
								<a href="#tickets_settings_tab" aria-controls="tickets_settings_tab" role="tab" data-toggle="tab">
								<?php echo _l('settings_group_tickets'); ?></a>
							</li>
							<li role="presentation">
								<a href="#sales_setting_tab" aria-controls="sales_setting_tab" role="tab" data-toggle="tab">
								<?php echo _l('settings_group_sales'); ?></a>
							</li>
							<?php if(get_staff_user_id() == 1){ ?>
							<li role="presentation">
								<a href="#online_payment_modes_setting_tab" aria-controls="online_payment_modes_setting_tab" role="tab" data-toggle="tab">
								<?php echo _l('settings_group_online_payment_modes'); ?></a>
							</li>
							<?php } ?>
							<li role="presentation">
								<a href="#email_settings_tab" aria-controls="email_settings_tab" role="tab" data-toggle="tab">
								<?php echo _l('settings_group_email'); ?></a>
							</li>
							<li role="presentation">
								<a href="#clients_settings_tab" aria-controls="clients_settings_tab" role="tab" data-toggle="tab">
								<?php echo _l('settings_group_clients'); ?></a>
							</li>
							<li role="presentation">
								<a href="#settings_calendar_tab" aria-controls="settings_calendar_tab" role="tab" data-toggle="tab">
								<?php echo _l('settings_calendar'); ?></a>
							</li>
							<li role="presentation">
								<a href="#newsfeed_settings_tab" aria-controls="newsfeed_settings_tab" role="tab" data-toggle="tab">
								<?php echo _l('settings_group_newsfeed'); ?></a>
							</li>
							<li role="presentation">
								<a href="#cron_settings_tab" aria-controls="cron_settings_tab" role="tab" data-toggle="tab">
								<?php echo _l('settings_group_cronjob'); ?></a>
							</li>
							<li role="presentation">
								<a href="#reminders_settings_tab" aria-controls="reminders_settings_tab" role="tab" data-toggle="tab">
								<?php echo _l('settings_group_notifications'); ?></a>
							</li>
						</ul>
				</div>
			</div>
				<div class="panel_s">
					<div class="panel-body">

						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="general_settings_tab">
								<?php include_once(APPPATH . 'views/admin/settings/includes/general.php'); ?>
							</div>
							<div role="tabpanel" class="tab-pane" id="company_info">
								<?php include_once(APPPATH . 'views/admin/settings/includes/company.php'); ?>
							</div>
							<div role="tabpanel" class="tab-pane" id="localization_settings_tab">
								<?php include_once(APPPATH . 'views/admin/settings/includes/localization.php'); ?>
							</div>
							<div role="tabpanel" class="tab-pane" id="tickets_settings_tab">
								<?php include_once(APPPATH . 'views/admin/settings/includes/tickets.php'); ?>
							</div>
							<div role="tabpanel" class="tab-pane" id="sales_setting_tab">
								<?php include_once(APPPATH . 'views/admin/settings/includes/sales.php'); ?>
							</div>
							<?php if(get_staff_user_id() == 1){ ?>
							<div role="tabpanel" class="tab-pane" id="online_payment_modes_setting_tab">
								<?php include_once(APPPATH . 'views/admin/settings/includes/online_payment_modes.php'); ?>
							</div>
							<?php } ?>
							<div role="tabpanel" class="tab-pane" id="email_settings_tab">
								<?php include_once(APPPATH . 'views/admin/settings/includes/email.php'); ?>
							</div>
							<div role="tabpanel" class="tab-pane" id="clients_settings_tab">
								<?php include_once(APPPATH . 'views/admin/settings/includes/clients.php'); ?>
							</div>
							<div role="tabpanel" class="tab-pane" id="settings_calendar_tab">
								<?php include_once(APPPATH . 'views/admin/settings/includes/calendar.php'); ?>
							</div>
							<div role="tabpanel" class="tab-pane" id="newsfeed_settings_tab">
								<?php include_once(APPPATH . 'views/admin/settings/includes/newsfeed.php'); ?>
							</div>
							<div role="tabpanel" class="tab-pane" id="cron_settings_tab">
								<?php include_once(APPPATH . 'views/admin/settings/includes/cronjob.php'); ?>
							</div>
							<div role="tabpanel" class="tab-pane" id="reminders_settings_tab">
								<?php include_once(APPPATH . 'views/admin/settings/includes/reminders.php'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12 text-left">
				<button type="submit" class="btn btn-primary"><?php echo _l('settings_save'); ?></button>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>
<?php init_tail(); ?>
<script>
	var temp_location = window.location.href.replace('#', '');
	$('form').attr('action', temp_location);
	$('body').on('click', 'a[data-toggle="tab"]', function() {
	    var location = window.location.href.split("?")[0];
	    var hash = this.hash;
	    hash = hash.replace('#', '');
	    $('form').attr('action', location + '?tab_hash=' + hash);
	});
	$('body').find('.nav-tabs a[href="#<?php echo $tab_hash; ?>"]').tab('show');
	// Deletes company logo
	function remove_company_logo() {
	    $.get(admin_url + 'settings/remove_company_logo', function(response) {
	        if (response.success == true) {
	            $('.company_logo').remove();
	            $('.company_logo_upload').removeClass('hide');
	        }
	    }, 'json');
	}

	function remove_favicon() {
	    $.get(admin_url + 'settings/remove_favicon', function(response) {
	        if (response.success == true) {
	            $('.favicon').remove();
	            $('.favicon_upload').removeClass('hide');
	        }
	    }, 'json');
	}

</script>
</body>
</html>
