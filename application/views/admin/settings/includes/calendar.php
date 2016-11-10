<div class="row">
    <div class="col-md-6">
        <a href="<?php echo admin_url('departments'); ?>" class="mbot30 display-block"><?php echo _l('setup_calendar_by_departments'); ?></a>
        <?php echo render_input('settings[google_calendar_main_calendar]','settings_gcal_main_calendar_id',get_option('google_calendar_main_calendar'),'text',array('data-toggle'=>'tooltip','title'=>'settings_gcal_main_calendar_id_help')); ?>
        <hr />
        <h4><?php echo _l('show_on_calendar'); ?></h4>
        <hr />
        <?php render_yes_no_option('show_invoices_on_calendar','show_invoices_on_calendar'); ?>
        <hr />
        <?php render_yes_no_option('show_estimates_on_calendar','show_estimates_on_calendar'); ?>
        <hr />
        <?php render_yes_no_option('show_contracts_on_calendar','show_contracts_on_calendar'); ?>
        <hr />
        <?php render_yes_no_option('show_tasks_on_calendar','show_tasks_on_calendar'); ?>
        <hr />
        <?php render_yes_no_option('show_client_reminders_on_calendar','show_client_reminders_on_calendar'); ?>
        <hr />
         <?php render_yes_no_option('show_leads_reminders_on_calendar','show_lead_reminders_on_calendar'); ?>
        <hr />
    </div>
</div>
