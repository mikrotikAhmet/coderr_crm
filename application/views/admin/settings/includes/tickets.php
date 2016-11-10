<div class="row">
	<div class="col-md-6">
		<?php render_yes_no_option('services','settings_tickets_use_services'); ?>
		<hr />
		<?php echo render_input('settings[maximum_allowed_ticket_attachments]','settings_tickets_max_attachments',get_option('maximum_allowed_ticket_attachments'),'number'); ?>
		<hr />
		<?php render_yes_no_option('staff_access_only_assigned_departments','settings_tickets_allow_departments_access'); ?>
		<hr />
		<?php echo render_input('settings[ticket_attachments_file_extensions]','settings_tickets_allowed_file_extensions',get_option('ticket_attachments_file_extensions')); ?>
        <hr />
        <h4><?php echo _l('tickets_piping'); ?></h4>
        <code>Pipe path <?php echo FCPATH .'pipe.php'; ?></code>
        <hr />
        <?php render_yes_no_option('email_piping_enabled','email_piping_enabled'); ?>
        <hr />
        <?php render_yes_no_option('email_piping_only_registered','email_piping_only_registered'); ?>
         <hr />
        <?php render_yes_no_option('email_piping_only_replies','email_piping_only_replies'); ?>
        <hr />
        <?php echo render_select('settings[email_piping_default_priority]',$ticket_priorities,array('priorityid','name'),'email_piping_default_priority',get_option('email_piping_default_priority')); ?>
	</div>
</div>
