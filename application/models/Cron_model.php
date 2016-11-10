<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
define('CRON', true);
class Cron_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('emails_model');

    }

    public function run($manualy = false)
    {

        $last_recurring_invoices_cron = get_option('last_recurring_invoices_cron');
        $last_recurring_expenses_cron = get_option('last_recurring_expenses_cron');

        if ($manualy == true) {
            logActivity('Cron Invoked Manually');
        }

        $this->make_backup_db();
        $this->goals_notification();
        $this->surveys();
        $this->reminders();
        $this->invoice_overdue_status();
        $this->estimate_expiry_check();
        $this->contracts_expiration_check();

        if ((time() > ($last_recurring_invoices_cron + 86400) || $last_recurring_invoices_cron == '') || $manualy == true) {
            $this->recurring_invoices();
            update_option('last_recurring_invoices_cron', time());
        }
        if ((time() > ($last_recurring_expenses_cron + 86400) || $last_recurring_expenses_cron == '') || $manualy == true) {
            $this->recurring_expenses();
            update_option('last_recurring_expenses_cron', time());
        }

        $this->check_leads_email_integration();
        update_option('last_cron_run', time());

    }

    public function contracts_expiration_check()
    {
        // Contracts
        $this->load->model('staff_model');
        $staff = $this->staff_model->get('', 1);

        $this->db->where('isexpirynotified', 0);
        $this->db->where('dateend is NOT NULL');
        $this->db->where('trash', 0);
        $contracts = $this->db->get('tblcontracts')->result_array();

        $now = new DateTime(date('Y-m-d'));
        foreach ($contracts as $contract) {
            if ($contract['dateend'] > date('Y-m-d')) {
                $dateend = new DateTime($contract['dateend']);
                $diff    = $dateend->diff($now)->format("%a");

                if ($diff <= get_option('contract_expiration_before')) {
                    foreach ($staff as $member) {
                        if (has_permission('manageContracts', $member['staffid'])) {
                            add_notification(array(
                                'description' => 'Contracts expiry reminder - ' . substr($contract['description'], 0, 50),
                                'touserid' => $member['staffid'],
                                'fromcompany' => 1,
                                'fromuserid' => NULL,
                                'link' => 'contracts/contract/' . $contract['id']
                            ));
                            $this->emails_model->send_email_template('contract-expiration', $member['email'], $contract['client'], false, false, false, false, $contract['id']);
                        }
                    }
                    $this->db->where('id', $contract['id']);
                    $this->db->update('tblcontracts', array(
                        'isexpirynotified' => 1
                    ));
                }
            }
        }
    }
    private function recurring_expenses()
    {
        $recurring_expenses = total_rows('tblexpenses', array(
            'recurring' => 1
        ));
        if ($recurring_expenses == 0) {
            return;
        }

        $this->db->where('recurring', 1);
        $recurring_expenses = $this->db->get('tblexpenses')->result_array();
        // Load the necessary models
        $this->load->model('invoices_model');

        $this->load->model('expenses_model');
        $this->load->model('staff_model');

        $recurring_expenses_email_data = 'Recurring Expenses Cron Job Activity - ' . _dt(date('Y-m-d H:i:s')) . '<br /><br />';
        $total_renewed                 = 0;
        foreach ($recurring_expenses as $expense) {

            $type                     = $expense['recurring_type'];
            $repeat_every             = $expense['repeat_every'];
            $last_recurring_date      = $expense['last_recurring_date'];
            $create_invoice_billable  = $expense['create_invoice_billable'];
            $send_invoice_to_customer = $expense['send_invoice_to_customer'];
            $expense_date             = $expense['date'];

            // Current date
            $date = new DateTime(date('Y-m-d'));

            // Check if is first recurring
            if (!$last_recurring_date) {
                $last_recurring_date = date('Y-m-d', strtotime($expense_date));
            } else {
                $last_recurring_date = date('Y-m-d', strtotime($last_recurring_date));
            }

            $calculated_date_difference = date('Y-m-d', strtotime('+' . $repeat_every . ' ' . strtoupper($type), strtotime($last_recurring_date)));

            if (date('Y-m-d') > $calculated_date_difference) {
                // Ok we can repeat the expense now
                $new_expense_data['category']       = $expense['category'];
                $new_expense_data['amount']         = $expense['amount'];
                $new_expense_data['tax']            = $expense['tax'];
                $new_expense_data['reference_no']   = $expense['reference_no'];
                $new_expense_data['note']           = $expense['note'];
                $new_expense_data['clientid']       = $expense['clientid'];
                $new_expense_data['billable']       = $expense['billable'];
                $new_expense_data['paymentmode']    = $expense['paymentmode'];
                $new_expense_data['discount_total']    = 0;
                $new_expense_data['date']           = date('Y-m-d');
                $new_expense_data['recurring_from'] = $expense['id'];
                $new_expense_data['dateadded']      = date('Y-m-d H:i:s');
                $new_expense_data['addedfrom']      = $expense['addedfrom'];

                $this->db->insert('tblexpenses', $new_expense_data);
                $insert_id = $this->db->insert_id();
                if ($insert_id) {
                    // Get the old expense custom field and add to the new
                    $custom_fields = get_custom_fields('expenses');
                    foreach ($custom_fields as $field) {
                        $value = get_custom_field_value($expense['id'], $field['id'], 'expenses');
                        if ($value == '') {
                            continue;
                        }
                        $this->db->insert('tblcustomfieldsvalues', array(
                            'relid' => $insert_id,
                            'fieldid' => $field['id'],
                            'fieldto' => 'expenses',
                            'value' => $value
                        ));
                    }

                    $total_renewed++;
                    $this->db->where('id', $expense['id']);
                    $this->db->update('tblexpenses', array(
                        'last_recurring_date' => date('Y-m-d')
                    ));

                    $recurring_expenses_email_data .= 'Action taken from recurring expense: <a href="' . admin_url('expenses/list_expenses/' . $expense['id']) . '">ID:' . $expense['id'] . '</a><br />';
                    $recurring_expenses_email_data .= 'Renewed Expense: <a href="' . admin_url('expenses/list_expenses/' . $insert_id) . '">ID: ' . $insert_id . '</a>';

                    if ($expense['create_invoice_billable'] == 1) {
                        $recurring_expenses_email_data .= '<br />Invoice Created: ';
                        $invoiceid = $this->expenses_model->convert_to_invoice($insert_id, true);
                        if ($invoiceid) {
                            $recurring_expenses_email_data .= 'Yes';
                            if ($expense['send_invoice_to_customer'] == 1) {
                                $sent = $this->invoices_model->sent_invoice_to_client($invoiceid, 'invoice-send-to-client', true, true);
                                $recurring_expenses_email_data .= '<br />Invoice Sent to Customer:';
                                if ($sent) {
                                    $recurring_expenses_email_data .= 'Yes';
                                } else {
                                    $recurring_expenses_email_data .= 'No';
                                }
                            }
                        } else {
                            $recurring_expenses_email_data .= 'No';
                        }
                    }

                    $recurring_expenses_email_data .= '<br /><br />';
                }
            }
        }

        $recurring_expenses_email_data .= 'Total Renewed: ' . $total_renewed;
        if($total_renewed > 0){
            // Get all active staff members
            $staff = $this->staff_model->get('', 1);

            foreach ($staff as $member) {
                if (has_permission('manageExpenses', $member['staffid'])) {
                    $this->emails_model->send_simple_email($member['email'], 'Recurring Expenses Cron Job Activity', $recurring_expenses_email_data);
                    // Add notifications to user
                    add_notification(array(
                        'fromcompany' => 1,
                        'touserid' => $member['staffid'],
                        'description' => $recurring_expenses_email_data
                    ));
                }
            }
        }
    }

    private function recurring_invoices()
    {
        // dont run this function if no recurring invoices exists
        $recurring_invoices = total_rows('tblinvoices', array(
            'recurring >' => 0
        ));
        if ($recurring_invoices == 0) {
            return;
        }

        $invoices_create_invoice_from_recurring_only_on_paid_invoices = get_option('invoices_create_invoice_from_recurring_only_on_paid_invoices');
        $this->load->model('invoices_model');
        $this->db->select('id,recurring,date,last_recurring_date,number,duedate');
        $this->db->from('tblinvoices');
        $this->db->where('recurring !=', 0);
        if ($invoices_create_invoice_from_recurring_only_on_paid_invoices == 1) {
            // Includes all recurring invoices with paid status if this option set to Yes
            $this->db->where('status', 2);
        }
        $invoices                      = $this->db->get()->result_array();
        $recurring_invoices_email_data = 'Recurring Invoices Cron Job Activity - ' . _dt(date('Y-m-d H:i:s')) . '<br /><br />';
        $total_renewed                 = 0;
        foreach ($invoices as $invoice) {

            // Current date
            $date = new DateTime(date('Y-m-d'));
            // Check if is first recurring
            if (!$invoice['last_recurring_date']) {
                $last_recurring_date = new DateTime($invoice['date']);
            } else {
                $last_recurring_date = new DateTime($invoice['last_recurring_date']);
            }

            $diff   = $last_recurring_date->diff($date);
            $months = (($diff->format('%y') * 12) + $diff->format('%m'));
            if ($months >= (int) $invoice['recurring']) {

                // Recurring invoice date is okey lets convert it to new invoice
                $_invoice = $this->invoices_model->get($invoice['id']);

                $new_invoice_data             = array();
                $new_invoice_data['clientid'] = $_invoice->clientid;
                $new_invoice_data['_number']  = get_option('next_invoice_number');
                $new_invoice_data['date']     = _d(date('Y-m-d'));


                if ($_invoice->duedate) {
                    // Now we need to get duedate from the old invoice and calculate the time difference and set new duedate
                    // Ex. if the first invoice had duedate 20 days from now we will add the same duedate date but starting from now
                    $dStart                      = new DateTime($invoice['date']);
                    $dEnd                        = new DateTime($invoice['duedate']);
                    $dDiff                       = $dStart->diff($dEnd);
                    $new_invoice_data['duedate'] = _d(date('Y-m-d', strtotime(date('Y-m-d', strtotime('+' . $dDiff->days . 'DAY')))));
                } else {
                    if (get_option('invoice_due_after') != 0) {
                        $new_invoice_data['duedate'] = _d(date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime(date('Y-m-d')))));
                    }

                }

                $new_invoice_data['currency']                 = $_invoice->currency;
                $new_invoice_data['subtotal']                 = $_invoice->subtotal;
                $new_invoice_data['total']                    = $_invoice->total;
                $new_invoice_data['adjustment']               = $_invoice->adjustment;
                $new_invoice_data['discount_percent']         = $_invoice->discount_percent;
                $new_invoice_data['discount_total']           = $_invoice->discount_total;
                $new_invoice_data['discount_type']            = $_invoice->discount_type;
                $new_invoice_data['terms']                    = $_invoice->terms;
                $new_invoice_data['sale_agent']               = $_invoice->sale_agent;
                // Since version 1.0.6
                $new_invoice_data['billing_street']           = $_invoice->billing_street;
                $new_invoice_data['billing_city']             = $_invoice->billing_city;
                $new_invoice_data['billing_state']            = $_invoice->billing_state;
                $new_invoice_data['billing_zip']              = $_invoice->billing_zip;
                $new_invoice_data['billing_country']          = $_invoice->billing_country;
                $new_invoice_data['shipping_street']          = $_invoice->shipping_street;
                $new_invoice_data['shipping_city']            = $_invoice->shipping_city;
                $new_invoice_data['shipping_state']           = $_invoice->shipping_state;
                $new_invoice_data['shipping_zip']             = $_invoice->shipping_zip;
                $new_invoice_data['shipping_country']         = $_invoice->shipping_country;

                if($_invoice->include_shipping == 1){
                  $new_invoice_data['include_shipping']         = $_invoice->include_shipping;
                }

                $new_invoice_data['include_shipping']         = $_invoice->include_shipping;
                $new_invoice_data['show_shipping_on_invoice'] = $_invoice->show_shipping_on_invoice;
                // Set to unpaid status automatically
                $new_invoice_data['status']                   = 1;
                $new_invoice_data['clientnote']               = $_invoice->clientnote;
                $new_invoice_data['adminnote']                = 'Recuring invoice created from Cron Job from invoice ' . format_invoice_number($invoice['id']);
                $new_invoice_data['allowed_payment_modes']    = unserialize($_invoice->allowed_payment_modes);
                $new_invoice_data['is_recurring_from']        = $_invoice->id;

                $new_invoice_data['newitems'] = array();

                $key = 1;
                foreach ($_invoice->items as $item) {
                    $new_invoice_data['newitems'][$key]['description']      = $item['description'];
                    $new_invoice_data['newitems'][$key]['long_description'] = $item['long_description'];
                    $new_invoice_data['newitems'][$key]['qty']              = $item['qty'];
                    $new_invoice_data['newitems'][$key]['taxid']            = $item['taxid'];
                    $new_invoice_data['newitems'][$key]['rate']             = $item['rate'];
                    $new_invoice_data['newitems'][$key]['order']            = $item['item_order'];
                    $key++;
                }

                $id = $this->invoices_model->add($new_invoice_data, true);
                if ($id) {
                    // Get the old expense custom field and add to the new
                    $custom_fields = get_custom_fields('invoice');
                    foreach ($custom_fields as $field) {
                        $value = get_custom_field_value($invoice['id'], $field['id'], 'invoice');
                        if ($value == '') {
                            continue;
                        }
                        $this->db->insert('tblcustomfieldsvalues', array(
                            'relid' => $id,
                            'fieldid' => $field['id'],
                            'fieldto' => 'invoice',
                            'value' => $value
                        ));
                    }

                    // Increment total renewed invoices
                    $total_renewed++;
                    // Update last recurring date to this invoice
                    $this->db->where('id', $invoice['id']);
                    $this->db->update('tblinvoices', array(
                        'last_recurring_date' => date('Y-m-d')
                    ));
                    // Used for email to determins is sent to email or not.
                    if (get_option('send_renewed_invoice_from_recurring_to_email') == 1) {
                        $this->invoices_model->sent_invoice_to_client($id, 'invoice-sent-to-client', true, true);
                    }

                    $recurring_invoices_email_data .= 'Action taken from recurring invoice: <a href="' . admin_url('invoices/list_invoices/' . $invoice['id']) . '">' . format_invoice_number($invoice['id']) . '</a><br />';
                    // Get the new invoice
                    $invoice = $this->invoices_model->get($id);
                    $recurring_invoices_email_data .= 'Renewed Invoice: <a href="' . admin_url('invoices/list_invoices/' . $id) . '">' . format_invoice_number($invoice->id) . '</a><br /><br />';
                }
            }
        }

        $recurring_invoices_email_data .= 'Total Renewed: ' . $total_renewed;
        if($total_renewed > 0){
            $this->load->model('staff_model');
            // Get all active staff members
            $staff = $this->staff_model->get('', 1);

            foreach ($staff as $member) {
                if (has_permission('manageSales', $member['staffid'])) {
                    $this->emails_model->send_simple_email($member['email'], 'Recurring Invoices Cron Job Activity', $recurring_invoices_email_data);
                    // Add notifications to user
                    add_notification(array(
                        'fromcompany' => 1,
                        'touserid' => $member['staffid'],
                        'description' => $recurring_invoices_email_data
                    ));
                }
            }
        }
    }
    private function surveys()
    {
        $found_emails = $this->db->count_all_results('tblsurveysemailsendcron');
        if ($found_emails > 0) {
            $total_emails_per_cron = get_option('survey_send_emails_per_cron_run');
            $this->load->model('mail_lists_model');
            // Initialize mail library
            $this->email->initialize();
            $this->load->library('email');
            // Load survey model
            $this->load->model('surveys_model');
            // Get all surveys send log where sending emails is not finished
            $this->db->where('iscronfinished', 0);
            $unfinished_surveys_send_log = $this->db->get('tblsurveysendlog')->result_array();

            foreach ($unfinished_surveys_send_log as $_survey) {
                $surveyid = $_survey['surveyid'];

                // Get survey emails that has been not sent yet.
                $this->db->where('surveyid', $surveyid);
                $this->db->limit($total_emails_per_cron);
                $emails = $this->db->get('tblsurveysemailsendcron')->result_array();
                $survey = $this->surveys_model->get($surveyid);

                if ($survey->fromname == '' || $survey->fromname == NULL) {
                    $survey->fromname = get_option('companyname');
                }

                if (stripos($survey->description, '{survey_link}') !== false) {
                    $survey->description = str_ireplace('{survey_link}', '<a href="' . site_url('survey/' . $survey->surveyid . '/' . $survey->hash) . '" target="_blank">' . $survey->subject . '</a>', $survey->description);
                }

                $total = 0;
                foreach ($emails as $data) {
                    if (isset($data['emailid']) && isset($data['listid'])) {
                        $customfields = $this->mail_lists_model->get_list_custom_fields($data['listid']);
                        foreach ($customfields as $custom_field) {
                            $value                     = $this->mail_lists_model->get_email_custom_field_value($data['emailid'], $data['listid'], $custom_field['customfieldid']);
                            $custom_field['fieldslug'] = '{' . $custom_field['fieldslug'] . '}';
                            if ($value != '') {
                                if (stripos($survey->description, $custom_field['fieldslug']) !== false) {
                                    $survey->description = str_ireplace($custom_field['fieldslug'], $value, $survey->description);
                                }
                            }
                        }
                    }

                    $this->email->clear();
                    $this->email->from($survey->fromname);
                    $this->email->to($data['email']);
                    $this->email->subject($survey->subject);
                    $this->email->message($survey->description);
                    if ($this->email->send()) {
                        $total++;
                        $this->db->where('id', $data['id']);
                        $this->db->delete('tblsurveysemailsendcron');
                    }
                }

                // Update survey send log
                $this->db->where('id', $_survey['id']);
                $this->db->update('tblsurveysendlog', array(
                    'total' => $total
                ));
                // Check if all emails send
                $this->db->where('surveyid', $surveyid);
                $found_emails = $this->db->count_all_results('tblsurveysemailsendcron');
                if ($found_emails == 0) {
                    // Update that survey send is finished
                    $this->db->where('id', $_survey['id']);
                    $this->db->update('tblsurveysendlog', array(
                        'iscronfinished' => 1
                    ));
                }
            }
        }
    }
    private function reminders()
    {
        // Customer reminders
        $this->db->where('isnotified', 0);
        $reminders = $this->db->get('tblreminders')->result_array();
        if (count($reminders) > 0) {
            $this->load->model('clients_model');
            $this->load->model('staff_model');
            $this->load->model('leads_model');
        }
        foreach ($reminders as $reminder) {

            if (date('Y-m-d') >= $reminder['date']) {
                $staff       = $this->staff_model->get($reminder['staff']);
                if($reminder['rel_type'] == 'customer'){

                $client      = $this->clients_model->get($reminder['rel_id']);
                $not_link = 'clients/client/' . $client->userid;
                $description = 'New reminder for '.ucfirst($reminder['rel_type']).' ' . $client->firstname . ' ' . $client->lastname;
                    if(!empty($client->company)){
                        $description .= '(' . $client->company . ')';
                    }
                } else if($reminder['rel_type'] == 'lead'){

                $lead = $this->leads_model->get($reminder['rel_id']);
                $not_link = 'leads/lead/' . $lead->id;
                $description = 'New reminder for '.ucfirst($reminder['rel_type']).' ' . $lead->name;
                }

                $description .= '<br /><br />' . $reminder['description'];

                add_notification(array(
                    'fromcompany' => true,
                    'touserid' => $reminder['staff'],
                    'description' => $description,
                    'link' => $not_link
                ));

                if ($reminder['notify_by_email'] == 1) {
                    $this->emails_model->send_simple_email($staff->email, 'New Reminder for '.ucfirst($reminder['rel_type']), $description . '<br />' . get_option('email_signature'));
                }

                $this->db->where('id', $reminder['id']);
                $this->db->update('tblreminders', array(
                    'isnotified' => 1
                ));
            }
        }
    }
    private function invoice_overdue_status()
    {
        $this->load->model('invoices_model');
        $send_invoice_overdue_reminder = get_option('cron_send_invoice_overdue_reminder');
        $this->db->select('id,date,status,last_overdue_reminder');
        $this->db->from('tblinvoices');
        $this->db->where('status !=', 2); // We dont need paid status
        $invoices = $this->db->get()->result_array();

        foreach ($invoices as $invoice) {
            if ($invoice['status'] == '4') {
                if ($send_invoice_overdue_reminder == '1') {
                    $now = time();
                    // Check if already sent invoice reminder
                    if ($invoice['last_overdue_reminder']) {
                        // We already have sent reminder, check for resending
                        $resend_days = get_option('automatically_resend_invoice_overdue_reminder_after');
                        // If resend_days from options is 0 means that the admin dont want to resend the mails.
                        if ($resend_days != 0) {
                            $datediff  = $now - strtotime($invoice['last_overdue_reminder']);
                            $days_diff = floor($datediff / (60 * 60 * 24));
                            if ($days_diff >= $resend_days) {
                                $this->invoices_model->send_invoice_overdue_notice($invoice['id'], true);
                            }
                        }
                    } else {
                        $datediff  = $now - strtotime($invoice['date']);
                        $days_diff = floor($datediff / (60 * 60 * 24));
                        if ($days_diff >= get_option('automatically_send_invoice_overdue_reminder_after')) {
                            $this->invoices_model->send_invoice_overdue_notice($invoice['id'], true);
                        }
                    }
                }
            } else {
                $statusid = update_invoice_status($invoice['id'], true);
                if ($statusid == '4') {
                    if ($send_invoice_overdue_reminder == '1') {
                        $this->invoices_model->send_invoice_overdue_notice($invoice['id'], true);
                    }
                }
            }
        }
    }
    private function estimate_expiry_check()
    {
        // Draft;
        $this->db->select('id,expirydate,status');
        $this->db->from('tblestimates');
        $this->db->where('status', 1);
        // Sent
        $this->db->or_where('status', 2);
        // WHere not expired
        $this->db->where('status !=', 5);
        $estimates = $this->db->get()->result_array();

        $this->load->model('estimates_model');
        foreach ($estimates as $estimate) {
            if ($estimate['expirydate'] != NULL) {
                if (date('Y-m-d') > $estimate['expirydate']) {
                    $this->db->where('id', $estimate['id']);
                    $this->db->update('tblestimates', array(
                        'status' => 5
                    ));
                    if ($this->db->affected_rows() > 0) {
                        $this->estimates_model->log_estimate_activity($estimate['id'], '[CRON] Estimate Status Updated: From: ' . format_estimate_status($estimate['status']) . ' To: ' . format_estimate_status(5), true);
                    }
                }
            }
        }
    }

    public function goals_notification()
    {
        $this->load->model('goals_model');
        $goals = $this->goals_model->get('', true);
        foreach ($goals as $goal) {
            $achievement = $this->goals_model->calculate_goal_achievement($goal['id']);
            if ($achievement['percent'] >= 100) {
                if ($goal['notify_when_achieve'] == 1) {
                    if ((date('Y-m-d') == $goal['end_date']) || date('Y-m-d') >= $goal['end_date']) {
                        $this->goals_model->notify_staff_members($goal['id'], 'success', $achievement);
                    }
                }
            } else {
                // not yet achieved, check for end date
                if ($goal['notify_when_fail'] == 1) {
                    if (date('Y-m-d') > $goal['end_date']) {
                        $this->goals_model->notify_staff_members($goal['id'], 'failed', $achievement);
                    }
                }
            }
        }
    }

    public function check_leads_email_integration()
    {

        $this->load->model('leads_model');
        $mail = $this->leads_model->get_email_integration();
        if ($mail->active == 0) {
            return false;
        }

        require_once(APPPATH . 'third_party/php-imap/Imap.php');
       if (empty($mail->last_run) || (time() > $mail->last_run + ($mail->check_every * 60))) {
            $this->db->where('id', 1);
            $this->db->update('tblleadsemailintegration', array(
                'last_run' => time()
            ));
            $mailbox    = $mail->imap_server;
            $username   = $mail->email;
            $password   = $mail->password;
            $encryption = $mail->encryption;
            // open connection
            $imap       = new Imap($mailbox, $username, $password, $encryption);
            if ($imap->isConnected() === false) {
                logActivity('[CRON] Failed to connect to IMAP lead email integration - Set option to false if you dont use it.', null, true);
                return false;
            }

            if ($mail->folder == '') {
                $mail->folder = 'INBOX';
            }

            $imap->selectFolder($mail->folder);

            if ($mail->only_loop_on_unseen_emails == 1) {
                $emails = $imap->getUnreadMessages();
            } else {
                $emails = $imap->getMessages();
            }




            foreach ($emails as $email) {

                $from      = $email['from'];
                $fromname  = preg_replace("/(.*)<(.*)>/", "\\1", $from);
                $fromname  = trim(str_replace("\"", "", $fromname));
                $fromemail = trim(preg_replace("/(.*)<(.*)>/", "\\2", $from));


                // Okey everything good now let make some statements
                // Check if this email exists in customers table first
                $this->db->where('email', $fromemail);
                $total = $this->db->count_all_results('tblclients');

                if ($total > 0) {
                    // Set message to seen to in the next time we dont need to loop over this message
                    $imap->setUnseenMessage($email['uid']);
                    // Exists no need to do anything
                    continue;
                } else {
                    // Not exists its okey.
                    // Now we need to check the leads table
                    $this->db->where('email', $fromemail);
                    $lead = $this->db->get('tblleads')->row();
                    if ($lead) {
                        // Check if the lead uid is the same with the email uid
                        if ($lead->email_integration_uid == $email['uid']) {
                            // Set message to seen to in the next time we dont need to loop over this message
                            $imap->setUnseenMessage($email['uid']);
                            continue;
                        }

                        // Check if this uid exists in the emails data log table
                        $this->db->where('emailid', $email['uid']);
                        $exists_in_emails = $this->db->count_all_results('tblleadsemailintegrationemails');

                        if ($exists_in_emails > 0) {
                            // Set message to seen to in the next time we dont need to loop over this message
                            $imap->setUnseenMessage($email['uid']);
                            continue;
                        }

                        // We dont need the junk leads
                        if ($lead->junk == 1) {
                            // Set message to seen to in the next time we dont need to loop over this message
                            $imap->setUnseenMessage($email['uid']);
                            continue;
                        }

                        // More the one time email from this lead, insert into the lead emails log table
                        $this->db->insert('tblleadsemailintegrationemails', array(
                            'leadid' => $lead->id,
                            'subject' => trim($email['subject']),
                            'body' => strip_tags(nl2br(trim($email['body'])),'<br>,\n\n'),
                            'dateadded' => date('Y-m-d H:i:s'),
                            'emailid' => $email['uid']
                        ));

                        // Set message to seen to in the next time we dont need to loop over this message
                        $imap->setUnseenMessage($email['uid']);

                        $this->_notification_lead_email_integration('Received one more email message from lead', $mail, $lead->id);
                        $this->_check_lead_email_integration_attachments($email, $lead->id, $imap);
                        // Exists not need to do anything except to add the email
                        continue;
                    }

                    // Lets insert into the leads table
                    $lead_data = array(
                        'name' => $fromname,
                        'assigned' => $mail->responsible,
                        'dateadded' => date('Y-m-d H:i:s'),
                        'status' => $mail->lead_status,
                        'source' => $mail->lead_source,
                        'addedfrom' => 0,
                        'email' => $fromemail,
                        'is_imported_from_email_integration' => 1,
                        'email_integration_uid' => $email['uid']
                    );

                    // Set to public lead if responsible is not set
                    if ($mail->responsible == 0) {
                        $lead_data['is_public'] = 1;
                    }


                    $this->db->insert('tblleads', $lead_data);

                    $insert_id = $this->db->insert_id();
                    if ($insert_id) {
                        $this->db->insert('tblleadsemailintegrationemails', array(
                            'leadid' => $insert_id,
                            'subject' => trim($email['subject']),
                            'body' => strip_tags(nl2br(trim($email['body'])),'<br>,\n\n'),
                            'dateadded' => date('Y-m-d H:i:s'),
                            'emailid' => $email['uid']
                        ));
                        // Set message to seen to in the next time we dont need to loop over this message
                        $imap->setUnseenMessage($email['uid']);
                        $this->_notification_lead_email_integration('Lead Imported From Email Integration', $mail, $insert_id);
                        $this->leads_model->log_lead_activity($insert_id, 'Lead Imported From Email Integration', '', true);
                        $this->_check_lead_email_integration_attachments($email, $insert_id, $imap);
                    }
                }
            }
        }
    }

    public function make_backup_db($manual = false)
    {

        if ((get_option('auto_backup_enabled') == "1" && time() > (get_option('last_auto_backup') + get_option('auto_backup_every') * 24 * 60 * 60)) || $manual == true) {

            $this->load->dbutil();

            $prefs = array(
                'format' => 'zip',
                'filename' => date("Y-m-d-H-i-s") . '_backup.sql'
            );

            $backup      = $this->dbutil->backup($prefs);
            $backup_name = 'database_backup_' . date("Y-m-d-H-i-s") . '.zip';
            $backup_name = unique_filename(BACKUPS_FOLDER, $backup_name);
            $save        = BACKUPS_FOLDER . $backup_name;
            $this->load->helper('file');
            if (write_file($save, $backup)) {
                if ($manual == false) {
                    logActivity('[CRON] Database Backup [' . $backup_name . ']', null, true);
                    update_option('last_auto_backup', time());
                } else {
                    logActivity('Database Backup [' . $backup_name . ']');
                }
                return true;
            }
        }

        return false;

    }

    private function _notification_lead_email_integration($description, $mail, $leadid)
    {

        if (!empty($mail->notify_type)) {
            $ids = unserialize($mail->notify_ids);

            if (!is_array($ids) || count($ids) == 0) {
                return;
            }

            if ($mail->notify_type == 'specific_staff') {
                $field = 'staffid';
            } else if ($mail->notify_type == 'roles') {
                $field = 'role';
            } else {
                return;
            }

            $this->db->where('active', 1);
            $this->db->where_in($field, $ids);

            $staff = $this->db->get('tblstaff')->result_array();

            foreach ($staff as $member) {
                add_notification(array(
                    'description' => $description,
                    'touserid' => $member['staffid'],
                    'fromcompany' => 1,
                    'fromuserid' => NULL,
                    'link' => 'leads/lead/' . $leadid
                ));
            }
        }
    }
    private function _check_lead_email_integration_attachments($email, $leadid, &$imap)
    {
        // Check for any attachments
        if (isset($email['attachments'])) {
            foreach ($email['attachments'] as $key => $attachment) {

                $_attachment = $imap->getAttachment($email['uid']);
                $path = LEAD_ATTACHMENTS_FOLDER . $leadid . '/';
                $file_name   = unique_filename($path,$attachment['name']);

                if (!file_exists($path)) {
                    mkdir($path);
                    fopen($path . 'index.html', 'w');
                }

                $path = $path . $file_name;

                $fp = fopen($path, "w+");
                if (fwrite($fp, $_attachment['content'])) {
                    $this->db->insert('tblleadattachments', array(
                        'leadid' => $leadid,
                        'file_name' => $file_name,
                        'addedfrom' => 0,
                        'dateadded' => date('Y-m-d H:i:s')
                    ));

                    $attachment_id = $this->db->insert_id();
                    if ($attachment_id) {
                        $this->leads_model->log_lead_activity($leadid, 'Imported attachment from email', '', true);
                        $file_type = get_mime_by_extension($attachment['name']);
                        // update the file type
                        $this->db->where('id', $attachment_id);
                        $this->db->update('tblleadattachments', array(
                            'filetype' => $file_type
                        ));
                    }
                }

                fclose($fp);
            }
        }
    }
}
