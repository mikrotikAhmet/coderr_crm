<div class="row">
    <div class="col-md-6">
        <p class="well">
            <span class="bold text-success">CRON COMMAND: wget -q -O- <?php echo site_url('cron/index'); ?></span><br />
            <a href="<?php echo admin_url('misc/run_cron_manually'); ?>">Run Cron Manually</a><br />
            <?php
            $last_cron_run = get_option('last_cron_run');
            if($last_cron_run == ''){
                echo '<span class="text-danger bold">Cron has never run. Cron setup is necessary for Perfex</span>';
            }
            ?>
        </p>
        <h4 class="bold"><?php echo _l('settings_sales_cron_invoice_heading'); ?></h4>
        <?php render_yes_no_option('cron_send_invoice_overdue_reminder','settings_cron_send_overdue_reminder','settings_cron_send_overdue_reminder_tooltip'); ?>
        <hr />
        <?php echo render_input('settings[automatically_send_invoice_overdue_reminder_after]','automatically_send_invoice_overdue_reminder_after',get_option('automatically_send_invoice_overdue_reminder_after'),'number'); ?>
        <hr />
        <?php echo render_input('settings[automatically_resend_invoice_overdue_reminder_after]','automatically_resend_invoice_overdue_reminder_after',get_option('automatically_resend_invoice_overdue_reminder_after'),'number'); ?>
        <hr />
        <?php render_yes_no_option('create_invoice_from_recurring_only_on_paid_invoices','invoices_create_invoice_from_recurring_only_on_paid_invoices','invoices_create_invoice_from_recurring_only_on_paid_invoices_tooltip'); ?>
        <hr />
        <?php render_yes_no_option('send_renewed_invoice_from_recurring_to_email','send_renewed_invoice_from_recurring_to_email'); ?>
        <hr />
        <h4 class="bold"><?php echo _l('settings_cron_surveys'); ?></h4>
        <?php echo render_input('settings[survey_send_emails_per_cron_run]','settings_survey_send_emails_per_cron_run',get_option('survey_send_emails_per_cron_run'),'number',array('data-toggle'=>'tooltip','title'=>'settings_survey_send_emails_per_cron_run_tooltip')); ?>
    </div>
</div>
