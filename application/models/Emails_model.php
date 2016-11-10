<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('EMAIL_TEMPLATE_SEND',true);
class Emails_model extends CRM_Model
{
    private $attachment = array();
    function __construct()
    {
        parent::__construct();
        $this->load->library('email');

    }

    /**
     * @param  string
     * @return array
     * Get email template by type
     */
    public function get($type)
    {
        $this->db->where('type', $type);
        return $this->db->get('tblemailtemplates')->result_array();
    }

    /**
     * @param  integer
     * @return object
     * Get email template by id
     */
    public function get_email_template_by_id($id)
    {
        $this->db->where('emailtemplateid', $id);
        return $this->db->get('tblemailtemplates')->row();
    }


    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update email template
     */
    public function update($data, $id)
    {
        if (isset($data['plaintext'])) {
            $data['plaintext'] = 1;
        } else {
            $data['plaintext'] = 0;
        }

        if (isset($data['disabled'])) {
            $data['active'] = 0;
            unset($data['disabled']);
        } else {
            $data['active'] = 1;
        }

        $this->db->where('emailtemplateid', $id);
        $this->db->update('tblemailtemplates', $data);

        if ($this->db->affected_rows() > 0) {
            logActivity('Email Template Updated [' . $id . ']');
            return true;
        }

        return false;
    }

   /**
    * Send email - No templates used only simple string
    * @since Version 1.0.2
    * @param  string $email   email
    * @param  string $message message
    * @param  string $subject email subject
    * @return boolean
    */
    public function send_simple_email($email,$subject,$message){

        $this->email->initialize();
        $this->email->clear(TRUE);
        $this->email->from(get_option('smtp_email'), 'Perfex');
        $this->email->to($email);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->set_alt_message(strip_tags($message));

        if (count($this->attachment) > 0) {
            foreach($this->attachment as $attach){
                if(!isset($attach['read'])){
                     $this->email->attach($attach['attachment'], 'attachment', $attach['filename'], $attach['type']);
                } else {
                     $this->email->attach($attach['attachment'],'',$attach['filename']);
                }
            }
        }

        $this->clear_attachments();

        if ($this->email->send()) {
            return true;
        }

        return false;
    }
    /**
     * @param  string (email address)
     * @param  string (email subject)
     * @param  string (html email template type)
     * @param  array available data for the email template
     * @return boolean
     * Send email template from views/email
     */
    public function send_email($email, $subject, $type, &$data)
    {
        // Check if overide happens
        if(file_exists(APPPATH . 'views/my_email/'.$type .'.php')){
            $template = $this->load->view('my_email/' . $type, $data, TRUE);
        } else {
            $template = $this->load->view('email/' . $type, $data, TRUE);
        }

        $this->email->initialize();
        $this->email->clear(TRUE);
        $this->email->from(get_option('smtp_email'), get_option('companyname'));
        $this->email->to($email);
        $this->email->subject($subject);
        $this->email->message($template);
        $this->email->set_alt_message(strip_tags($template));

        if (count($this->attachment) > 0) {
            foreach($this->attachment as $attach){
                if(!isset($attach['read'])){
                     $this->email->attach($attach['attachment'], 'attachment', $attach['filename'], $attach['type']);
                } else {
                     $this->email->attach($attach['attachment'],'',$attach['filename']);
                }
            }
        }

        $this->clear_attachments();
        if ($this->email->send()) {
            logActivity('Email Send To [Email:' . $email . ', Type:' . $type .']');
            return true;
        }

        return false;
    }

   /**
    * @param  string (email template slug)
    * @param  string (email address)
    * @param  integer (client ID)
    * @param  integer (invoice ID)
    * @param  integer (staff ID)
    * @param  integer (ticket ID)
    * @return boolean
    * Send email template
    */
    public function send_email_template($template, $email = '', $clientid = false, $invoiceid = false, $staffid = false, $ticketid = false,$estimateid = false,$contractid = false,$taskid = false,$proposalid = false)
    {
        $this->db->where('slug', $template);
        $template = $this->db->get('tblemailtemplates')->row();

        if ($template->active == 0) {
            return false;
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $template = $this->parse_template($template, $clientid, $invoiceid, $staffid, $ticketid, $estimateid,$contractid,$taskid,$proposalid);


        // email config
        if ($template->plaintext == 1) {
            $this->config->set_item('mailtype', 'text');
            $template->message = strip_tags($template->message);
        }

        $fromemail = $template->fromemail;
        $fromname  = $template->fromname;

        if ($fromemail == '') {
            $fromemail = get_option('smtp_email');
        }

        if ($fromname == '') {
            $fromname = get_option('companyname');
        }

        $reply_to = false;
        if(is_numeric($ticketid) && $template->type == 'ticket'){
            $this->load->model('tickets_model');
            $ticket = $this->tickets_model->get_ticket_by_id($ticketid);
            $departmentid = $ticket->department;
            $department_email = get_department_email($departmentid);

            if(!empty($department_email) && filter_var($department_email, FILTER_VALIDATE_EMAIL)){
                $reply_to = $department_email;
            }

            // IMPORTANT
            // Dont change/remove this line, this is used for email piping so the software can recognize the ticket id.
            if (substr($template->subject, 0, 10) != "[Ticket ID") {
              $template->subject = '[Ticket ID: '.$ticketid.'] ' . $template->subject;
            }
        }

        $template = do_action('before_email_template_send', $template);
        $this->email->initialize();
        $this->email->clear(TRUE);
        $this->email->from($fromemail, $fromname);
        $this->email->subject($template->subject);
        $this->email->message($template->message);

        if($reply_to != false){
            $this->email->reply_to($reply_to);
        }

        if ($template->plaintext == 0) {
           $this->email->set_alt_message(strip_tags($template->message));
        }

        $this->email->to($email);
        if (count($this->attachment) > 0) {
            foreach($this->attachment as $attach){
                if(!isset($attach['read'])){
                     $this->email->attach($attach['attachment'], 'attachment', $attach['filename'], $attach['type']);
                } else {
                     $this->email->attach($attach['attachment'],'',$attach['filename']);
                }
            }
        }

        $this->clear_attachments();
        if ($this->email->send()) {
            logActivity('Email Send To [Email:' . $email . ', Template:' . $template->name . ']');
            return true;
        }

        return false;
    }

     /**
    * @param  string (email template slug)
    * @param  integer (client ID)
    * @param  integer (invoice ID)
    * @param  integer (staff ID)
    * @param  integer (ticket ID)
    * @return boolean
    * Parse template and replace all merge fields found in the template BODY / SUBJECT / FROM NAME
    */
    public function parse_template($template = '', $clientid = false, $invoiceid = false, $staffid = false, $ticketid = false, $estimateid = false,$contractid = false,$taskid = false,$proposalid = false)
    {
        $available_merge_fields = $this->get_available_merge_fields();

        if (!is_object($template)) {
            $this->db->where('slug', $template);
            $template = $this->db->get('tblemailtemplates')->row();
        }

        $this->load->model('currencies_model');
        $use_services = get_option('services');

        foreach ($available_merge_fields as $field) {
            foreach ($field as $key => $val) {
                // key staff
                foreach ($val as $_field) {
                    foreach ($_field['available'] as $_available) {

                        if ($_available == $template->type || $template->type == 'other') {
                            $text_replace = '';
                            //if ( preg_match('~\b{client_firstname}\b~',$template->message) ) {
                            if (mb_stripos($template->message, $_field['key']) !== false || mb_stripos($template->subject, $_field['key']) !== false || mb_stripos($template->fromname, $_field['key']) !== false) {
                                // remove last } and get db key from second key
                                if (isset($_field['fromoptions'])) {
                                    $_tempfield = $_field['key'];
                                    $_tempfield = mb_substr(mb_substr($_tempfield, 1, strlen($_tempfield)), 0, -1);
                                } else {
                                    $_tempfield = mb_substr($_field['key'], strpos($_field['key'], "_") + 1);
                                    $_tempfield = mb_substr($_tempfield, 0, -1);
                                }
                                if (isset($_field['fromoptions'])) {
                                    if ($_tempfield !== 'crm_url') {
                                        if ($_tempfield == 'logo_url') {
                                            $text_replace = '<a href="' . site_url() . '" target="_blank"><img src="' . site_url('uploads/company/' . get_option('company_logo')) . '"></a>';
                                        } else {
                                            $text_replace = check_for_links(get_option($_tempfield));
                                        }
                                    } else {
                                        // site url
                                        $text_replace = check_for_links(site_url());
                                    }
                                } else if (_startsWith($_field['key'], '{staff')) {
                                    $this->db->where('staffid', $staffid);
                                    $this->db->select($_tempfield)->from('tblstaff');
                                    $_user        = $this->db->get()->row();
                                    $text_replace = '';
                                    if ($_user) {
                                        $text_replace = $_user->$_tempfield;
                                    }
                                } else if (_startsWith($_field['key'], '{client')) {
                                    $this->db->where('userid', $clientid);
                                    if ($_field['key'] == '{client_country}') {
                                        $this->db->select('short_name')
                                        ->from('tblclients')
                                        ->join('tblcountries','tblcountries.country_id = tblclients.country');
                                        $text_replace = $this->db->get()->row()->short_name;
                                    } else {
                                        $this->db->select($_tempfield)->from('tblclients');
                                        $_user = $this->db->get()->row();
                                        if ($_user) {
                                            $text_replace = $_user->$_tempfield;
                                        }
                                    }
                                } else if (_startsWith($_field['key'], '{ticket')) {
                                    if ($_field['key'] == '{ticket_department}') {
                                        $this->db->select('tbldepartments.name')
                                        ->from('tbltickets')
                                        ->join('tbldepartments','tbldepartments.departmentid = tbltickets.department');

                                        $this->db->where('ticketid', $ticketid);
                                        $text_replace = $this->db->get()->row()->name;
                                    } else if ($_field['key'] == '{ticket_status}') {

                                        $this->db->select('tblticketstatus.name')
                                        ->from('tbltickets')
                                        ->join('tblticketstatus', 'tblticketstatus.ticketstatusid = tbltickets.status', 'left');
                                        $this->db->where('ticketid', $ticketid);
                                        $text_replace = $this->db->get()->row()->name;
                                    } else if ($_field['key'] == '{ticket_service}') {
                                        if($use_services == '1'){

                                        $this->db->select('tblservices.name')
                                        ->from('tbltickets')
                                        ->join('tblservices', 'tblservices.serviceid = tbltickets.service', 'left');

                                        $this->db->where('ticketid', $ticketid);
                                        $_service = $this->db->get()->row();
                                        if ($_service) {
                                            $text_replace = $_service->name;
                                        } else {
                                            $text_replace = '';
                                        }
                                    } else {
                                         $text_replace = '';
                                    }
                                    } else if ($_field['key'] == '{ticket_id}') {
                                        $text_replace = $ticketid;
                                    } else if ($_field['key'] == '{ticket_priority}') {
                                        $this->db->select('tblpriorities.name')->from('tbltickets')->join('tblpriorities', 'tblpriorities.priorityid = tbltickets.priority', 'left');
                                        $this->db->where('ticketid', $ticketid);
                                        $_priority = $this->db->get()->row();
                                        if ($_priority) {
                                            $text_replace = $_priority->name;
                                        } else {
                                            $text_replace = '';
                                        }
                                    } else if ($_field['key'] == '{ticket_url}') {
                                        $this->db->where('ticketid',$ticketid);
                                        // Send different ticket url for admins
                                        if($template->slug != 'ticket-reply-to-admin' && $template->slug != 'new-ticket-created-staff'){
                                              $text_replace = '<a href="' . site_url('clients/ticket/' . $ticketid) . '" target="_blank">Ticket #' . $ticketid . '</a>';
                                          } else {
                                              $text_replace = '<a href="' . admin_url('tickets/ticket/' . $ticketid) . '" target="_blank">Ticket #' . $ticketid . '</a>';
                                          }

                                    } else if($_field['key'] == '{ticket_message}' &&
                                     ($template->slug == 'ticket-reply-to-admin' || $template->slug == 'ticket-reply')){
                                       // for ticket message we need to replace with the reply message
                                       $this->db->where('ticketid',$ticketid);
                                       $this->db->limit(1);
                                       $this->db->order_by('date','desc');
                                       $reply = $this->db->get('tblticketreplies')->row();
                                       $text_replace = $reply->message;
                                    } else {
                                        $this->db->select($_tempfield)->from('tbltickets');
                                        $this->db->where('ticketid', $ticketid);
                                        $_ticket = $this->db->get()->row();
                                        if ($_ticket) {
                                            if (is_date($_ticket->$_tempfield)) {
                                                $text_replace = _d($_ticket->$_tempfield);
                                            } else {
                                                $text_replace = $_ticket->$_tempfield;
                                            }
                                        }
                                    }
                                } else if (_startsWith($_field['key'], '{invoice')) {
                                    if ($invoiceid !== false) {
                                        if ($_field['key'] != '{invoice_link}') {
                                            $this->db->where('id', $invoiceid);
                                            $this->db->select($_tempfield)->from('tblinvoices');
                                            $_row = $this->db->get()->row();
                                            if ($_row) {
                                                $text_replace = $_row->$_tempfield;
                                                if ($_field['key'] == '{invoice_number}') {
                                                    $text_replace = format_invoice_number($invoiceid);
                                                } else if ($_field['key'] == '{invoice_status}') {
                                                    $text_replace = format_invoice_status($text_replace, '', false);
                                                } else {
                                                    if (is_date($text_replace)) {
                                                        $text_replace = _d($text_replace);
                                                    }
                                                }
                                            }
                                        } else {
                                            $this->db->where('id', $invoiceid);
                                            $this->db->select('hash')->from('tblinvoices');
                                            $_row         = $this->db->get()->row();
                                            $text_replace = '<a href="' . site_url('viewinvoice/' . $invoiceid . '/' . $_row->hash) . '" target="_blank">'._l('invoice_email_link_text').'</a>';
                                        }
                                    }

                                } else if (_startsWith($_field['key'], '{estimate')) {
                                    if ($estimateid !== false) {
                                        if($_field['key'] != '{estimate_link}'){
                                            $this->db->where('id', $estimateid);
                                            $this->db->select($_tempfield)->from('tblestimates');
                                            $_row = $this->db->get()->row();
                                            if ($_row) {
                                                $text_replace = $_row->$_tempfield;
                                                if ($_field['key'] == '{estimate_number}') {
                                                    $text_replace = format_estimate_number($estimateid);
                                                } else if ($_field['key'] == '{estimate_status}') {
                                                    $text_replace = format_estimate_status($text_replace, '', false);
                                                } else {
                                                    if (is_date($text_replace)) {
                                                        $text_replace = _d($text_replace);
                                                    }
                                            }
                                       }
                                   } else {
                                          $this->db->where('id', $estimateid);
                                          $this->db->select('hash')->from('tblestimates');
                                          $_row         = $this->db->get()->row();
                                          $text_replace = '<a href="' . site_url('viewestimate/' . $estimateid . '/' . $_row->hash) . '" target="_blank">'._l('estimate_email_link_text').'</a>';
                                   }
                                    }
                                } else if(_startsWith($_field['key'],'{contract')){
                                    if($contractid !== false){
                                        $this->db->where('id', $contractid);
                                        $this->db->select($_tempfield)->from('tblcontracts');
                                        $_row = $this->db->get()->row();
                                        if ($_row) {
                                            $text_replace = $_row->$_tempfield;
                                            if (is_date($text_replace)) {
                                                $text_replace = _d($text_replace);
                                            }
                                        }
                                    }
                                } else if(_startsWith($_field['key'],'{task')){
                                    if($taskid !== false){
                                        if ($_field['key'] == '{task_link}') {
                                            $this->db->where('id', $taskid);
                                            $this->db->select('name')->from('tblstafftasks');
                                            $_row = $this->db->get()->row();
                                            if($_row){
                                                 $text_replace = '<a href="'.admin_url('tasks/list_tasks/'.$taskid).'">'.$_row->name.'</a>';
                                            }
                                        } else {
                                        $this->db->where('id', $taskid);
                                        $this->db->select($_tempfield)->from('tblstafftasks');
                                        $_row = $this->db->get()->row();
                                        if ($_row) {
                                            $text_replace = $_row->$_tempfield;
                                                if (is_date($text_replace)) {
                                                    $text_replace = _d($text_replace);
                                                }
                                         }
                                       }
                                    }
                                } else if(_startsWith($_field['key'],'{proposal')){
                                        if($proposalid !== false){
                                        if ($_field['key'] == '{proposal_link}') {
                                            $this->db->where('id', $proposalid);
                                            $_row = $this->db->get('tblproposals')->row();
                                            if($_row){
                                                 $text_replace = '<a href="'.site_url('viewproposal/'.$proposalid . '/'.$_row->hash).'">'.$_row->subject.'</a>';
                                            }
                                        } else {
                                        $this->db->where('id', $proposalid);
                                        // get also the currency for total formating
                                        $this->db->select($_tempfield.',currency')->from('tblproposals');
                                        $_row = $this->db->get()->row();
                                        if ($_row) {
                                            $text_replace = $_row->$_tempfield;
                                            if($_field['key'] == '{proposal_total}'){
                                                if($_row->currency != 0){
                                                $currency = $this->currencies_model->get($_row->currency);
                                            } else {
                                                 $currency = $this->currencies_model->get_base_currency();
                                            }
                                                $text_replace = format_money($text_replace,$currency->symbol);

                                            }
                                                if (is_date($text_replace)) {
                                                    $text_replace = _d($text_replace);
                                                }
                                         }
                                       }
                                    }
                                }
                                else {
                                    return $template;
                                }

                                // replace
                                $template->message = str_ireplace($_field['key'], $text_replace, $template->message);

                                if (mb_stripos($template->subject, $_field['key']) !== false) {
                                    $template->subject = str_ireplace($_field['key'], $text_replace, $template->subject);
                                }
                                if (mb_stripos($template->fromname, $_field['key']) !== false) {
                                    $template->fromname = str_ireplace($_field['key'], $text_replace, $template->fromname);
                                }
                                // replace end
                            }
                        }
                    }
                }
            }
        }
        return $template;
    }

    /**
     * @return array
     * All available merge fields for templates are defined here
     */
    public function get_available_merge_fields()
    {
        $available_merge_fields = array(
            array(
                'staff' => array(
                    array(
                        'name' => 'Staff Firstname',
                        'key' => '{staff_firstname}',
                        'available' => array(
                            'staff',
                            'tasks'
                        )
                    ),
                    array(
                        'name' => 'Staff Lastname',
                        'key' => '{staff_lastname}',
                        'available' => array(
                            'staff',
                            'tasks'
                        )
                    ),

                    array(
                        'name' => 'Staff Email',
                        'key' => '{staff_email}',
                        'available' => array(
                            'staff',
                            'tasks'
                        )
                    ),
                    array(
                        'name' => 'Staff Date Created',
                        'key' => '{staff_datecreated}',
                        'available' => array(
                            'staff'
                        )
                    )
                )
            ),
            array(
                'clients' => array(
                    array(
                        'name' => 'Client Firstname',
                        'key' => '{client_firstname}',
                        'available' => array(
                            'client',
                            'ticket',
                            'invoice',
                            'estimate',
                            'contract'
                        )
                    ),
                    array(
                        'name' => 'Client Lastname',
                        'key' => '{client_lastname}',
                        'available' => array(
                            'client',
                            'ticket',
                            'invoice',
                            'estimate',
                            'contract'
                        )
                    ),
                    array(
                        'name' => 'Client Email',
                        'key' => '{client_email}',
                        'available' => array(
                            'client',
                            'invoice',
                            'estimate',
                            'ticket',
                            'contract'
                        )
                    ),

                    array(
                        'name' => 'Client Company',
                        'key' => '{client_company}',
                        'available' => array(
                            'client',
                            'invoice',
                            'estimate',
                            'ticket',
                            'contract'
                        )
                    ),
                    array(
                        'name' => 'Client Phonenumber',
                        'key' => '{client_phonenumber}',
                        'available' => array(
                            'client',
                            'invoice',
                            'estimate',
                            'ticket',
                            'contract'
                        )

                    ),
                    array(
                        'name' => 'Client Country',
                        'key' => '{client_country}',
                        'available' => array(
                            'client',
                            'invoice',
                            'estimate',
                            'ticket',
                            'contract'
                        )
                    ),
                    array(
                        'name' => 'Client City',
                        'key' => '{client_city}',
                        'available' => array(
                            'client',
                            'invoice',
                            'estimate',
                            'ticket',
                            'contract'
                        )
                    ),
                    array(
                        'name' => 'Client Zip',
                        'key' => '{client_zip}',
                        'available' => array(
                            'client',
                            'invoice',
                            'estimate',
                            'ticket',
                            'contract'
                        )
                    ),
                    array(
                        'name' => 'Client State',
                        'key' => '{client_state}',
                        'available' => array(
                            'client',
                            'invoice',
                            'estimate',
                            'ticket',
                            'contract'
                        )
                    ),
                    array(
                        'name' => 'Client Address',
                        'key' => '{client_address}',
                        'available' => array(
                            'client',
                            'invoice',
                            'estimate',
                            'ticket',
                            'contract'
                        )
                    )
                )
            ),
            array(
                'ticket' => array(
                    array(
                        'name' => 'Ticket ID',
                        'key' => '{ticket_id}',
                        'available' => array(
                            'ticket'
                        )
                    ),
                    array(
                        'name' => 'Ticket URL',
                        'key' => '{ticket_url}',
                        'available' => array(
                            'ticket'
                        )
                    ),
                    array(
                        'name' => 'Department',
                        'key' => '{ticket_department}',
                        'available' => array(
                            'ticket'
                        )
                    ),
                    array(
                        'name' => 'Date Opened',
                        'key' => '{ticket_date}',
                        'available' => array(
                            'ticket'
                        )
                    ),
                    array(
                        'name' => 'Ticket Subject',
                        'key' => '{ticket_subject}',
                        'available' => array(
                            'ticket'
                        )
                    ),
                    array(
                        'name' => 'Ticket Message',
                        'key' => '{ticket_message}',
                        'available' => array(
                            'ticket'
                        )
                    ),
                    array(
                        'name' => 'Ticket Status',
                        'key' => '{ticket_status}',
                        'available' => array(
                            'ticket'
                        )
                    ),
                    array(
                        'name' => 'Ticket Priority',
                        'key' => '{ticket_priority}',
                        'available' => array(
                            'ticket'
                        )
                    )
                )
            ),
            array(
                'contract'=>array(
                    array(
                        'name'=>'Contract Subject',
                        'key'=>'{contract_subject}',
                        'available'=>array(
                            'contract'
                            )
                        ),
                    array(
                        'name'=>'Contract Description',
                        'key'=>'{contract_description}',
                        'available'=>array(
                            'contract'
                            )
                        ),
                    array(
                        'name'=>'Contract Date Start',
                        'key'=>'{contract_datestart}',
                        'available'=>array(
                            'contract'
                            )
                        ),
                     array(
                        'name'=>'Contract Date End',
                        'key'=>'{contract_dateend}',
                        'available'=>array(
                            'contract'
                            )
                        ),
                     array(
                        'name'=>'Contract Value',
                        'key'=>'{contract_contract_value}',
                        'available'=>array(
                            'contract'
                            )
                        ),

                    )
                ),
            array(
                'invoice' => array(
                    array(
                        'name' => 'Invoice Link',
                        'key' => '{invoice_link}',
                        'available' => array(
                            'invoice'
                        )
                    ),
                    array(
                        'name' => 'Invoice Number',
                        'key' => '{invoice_number}',
                        'available' => array(
                            'invoice'
                        )
                    ),
                    array(
                        'name' => 'Invoice Duedate',
                        'key' => '{invoice_duedate}',
                        'available' => array(
                            'invoice'
                        )
                    ),
                    array(
                        'name' => 'Invoice Date',
                        'key' => '{invoice_date}',
                        'available' => array(
                            'invoice'
                        )
                    ),
                    array(
                        'name' => 'Invoice Status',
                        'key' => '{invoice_status}',
                        'available' => array(
                            'invoice'
                        )
                    )
                )
            ),
          array(
                'estimate' => array(
                    array(
                        'name'=>'Estimate Link',
                        'key'=>'{estimate_link}',
                        'available'=>array(
                            'estimate',
                            )
                        ),
                    array(
                        'name' => 'Estimate Number',
                        'key' => '{estimate_number}',
                        'available' => array(
                            'estimate',
                        )
                    ),
                    array(
                        'name' => 'Estimate Expiry Date',
                        'key' => '{estimate_expirydate}',
                        'available' => array(
                            'estimate',
                        )
                    ),
                    array(
                        'name' => 'Estimate Date',
                        'key' => '{estimate_date}',
                        'available' => array(
                            'estimate',
                        )
                    ),
                    array(
                        'name' => 'Estimate Status',
                        'key' => '{estimate_status}',
                        'available' => array(
                            'estimate',
                        )
                    )
                )
            ),
            array(
                'tasks'=>array(
                    array(
                          'name' => 'Task Link',
                            'key' => '{task_link}',
                            'available' => array(
                                'tasks'
                            )
                        ),
                    array(
                          'name' => 'Task Name',
                            'key' => '{task_name}',
                            'available' => array(
                                'tasks'
                            )
                        ),
                      array(
                          'name' => 'Task Description',
                            'key' => '{task_description}',
                            'available' => array(
                                'tasks'
                            )
                        ),
                         array(
                          'name' => 'Task Priority',
                            'key' => '{task_priority}',
                            'available' => array(
                                'tasks'
                            )
                        ),
                        array(
                          'name' => 'Task Start Date',
                            'key' => '{task_startdate}',
                            'available' => array(
                                'tasks'
                            )
                        ),
                       array(
                          'name' => 'Task Due Date',
                            'key' => '{task_duedate}',
                            'available' => array(
                                'tasks'
                            )
                        ),
                    )
                ),
            array(
                'proposals'=> array(
                    array(
                        'name'=>'Subject',
                        'key'=>'{proposal_subject}',
                        'available'=>array(
                            'proposals'
                            )
                        ),
                    array(
                        'name'=>'Total',
                        'key'=>'{proposal_total}',
                        'available'=>array(
                            'proposals'
                            )
                        ),
                    array(
                        'name'=>'Open Till',
                        'key'=>'{proposal_open_till}',
                        'available'=>array(
                            'proposals'
                            )
                        ),
                        array(
                    'name'=>'Company Name',
                        'key'=>'{proposal_proposal_to}',
                        'available'=>array(
                            'proposals'
                            )
                        ),
                    array(
                    'name'=>'Address',
                        'key'=>'{proposal_address}',
                        'available'=>array(
                            'proposals'
                            )
                        ),
                                   array(
                    'name'=>'Email',
                        'key'=>'{proposal_email}',
                        'available'=>array(
                            'proposals'
                            )
                        ),
                    array(
                        'name'=>'Phone',
                            'key'=>'{proposal_phone}',
                            'available'=>array(
                                'proposals'
                                )
                            ),
                     array(
                        'name'=>'Proposal Link',
                            'key'=>'{proposal_link}',
                            'available'=>array(
                                'proposals'
                                )
                            ),
                        )
                ),
            array(
                'other' => array(
                    array(
                        'name' => 'Logo Url',
                        'key' => '{logo_url}',
                        'fromoptions' => true,
                        'available' => array(
                            'ticket',
                            'client',
                            'staff',
                            'invoice',
                            'estimate',
                            'contract',
                            'tasks',
                            'proposals',

                        )
                    ),
                    array(
                        'name' => 'CRM Url',
                        'key' => '{crm_url}',
                        'fromoptions' => true,
                        'available' => array(
                            'ticket',
                            'client',
                            'staff',
                            'invoice',
                            'estimate',
                            'contract',
                            'tasks',
                            'proposals',

                        )
                    ),
                    array(
                        'name' => 'Main Domain',
                        'key' => '{main_domain}',
                        'fromoptions' => true,
                        'available' => array(
                            'ticket',
                            'client',
                            'staff',
                            'invoice',
                            'estimate',
                            'contract',
                            'tasks',
                            'proposals',

                        )
                    ),
                    array(
                        'name' => 'Company Name',
                        'key' => '{companyname}',
                        'fromoptions' => true,
                        'available' => array(
                            'ticket',
                            'client',
                            'staff',
                            'invoice',
                            'estimate',
                            'contract',
                            'tasks',
                            'proposals',

                        )
                    ),
                    array(
                        'name' => 'Email Signature',
                        'key' => '{email_signature}',
                        'fromoptions' => true,
                        'available' => array(
                            'ticket',
                            'client',
                            'staff',
                            'invoice',
                            'estimate',
                            'contract',
                            'tasks',
                            'proposals',

                        )
                    )
                )
            )
        );

        if (get_option('services') == 1) {
            $services = array(
                'name' => 'Ticket Service',
                'key' => '{ticket_service}',
                'available' => array(
                    'ticket'
                )
            );
            array_push($available_merge_fields[2]['ticket'], $services);
        }

        return $available_merge_fields;
    }

    /**
     * @param resource
     * @param string
     * @param string (mime type)
     * @return none
     * Add attachment to property to check before an email is send
     */
    public function add_attachment($attachment)
    {
        $this->attachment[]      = $attachment;
    }

    /**
     * @return none
     * Clear all attachment properties
     */
    private function clear_attachments()
    {
        $this->attachment      = array();
    }

}
