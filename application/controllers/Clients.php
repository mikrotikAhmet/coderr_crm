<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clients extends Clients_controller
{

    function __construct()
    {
        parent::__construct();
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-validation">', '</div>');
    }

    public function index()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        $data['title'] = get_option('companyname');
        $this->data    = $data;
        $this->view    = 'home';
        $this->layout();
    }
    public function tickets($status = false)
    {

        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        if(!has_customer_permission('support')){
            redirect(site_url());
        }

        if ($this->input->is_ajax_request()) {
            ClientTicketsTable(array(
                'status' => $status,
                'userid' => get_client_user_id()
                ));
        }

        $data['title'] = _l('clients_tickets_heading');
        $this->data    = $data;
        $this->view    = 'tickets';
        $this->layout();

    }

    public function viewproposal($id,$hash){

        check_proposal_restrictions($id,$hash);
        $this->load->model('proposals_model');
        $proposal = $this->proposals_model->get($id);

        if($proposal->rel_type == 'customer'){
         // Init the client language in case is accesed directly without login
            $language = get_client_default_language($proposal->rel_id);

            if (!empty($language)) {
                if(file_exists(APPPATH .'language/'.$language)){
                  $this->lang->load($language . '_lang'. $language);
              }
          }
      }



        if($this->input->post()){
            $action = $this->input->post('action');
            switch($action){
                case 'proposal_pdf':
                $pdf = proposal_pdf($proposal);
                $pdf->Output(slug_it($proposal->subject) . '.pdf', 'D');
                break;
                case 'proposal_comment':
                $client = true;
                if(is_staff_logged_in()){
                    $client = false;
                }
                // comment is blank
                if(!$this->input->post('content')){
                    redirect($this->uri->uri_string());
                }
                $data = $this->input->post();
                $data['proposalid'] = $id;
                $this->proposals_model->add_comment($data,$client);
                redirect($this->uri->uri_string());
                break;
                case 'accept_proposal':
                $success = $this->proposals_model->mark_action_status(3,$id,true);
                if($success){
                    redirect($this->uri->uri_string(),'refresh');
                }
                break;
                case 'decline_proposal':
                $success = $this->proposals_model->mark_action_status(2,$id,true);
                if($success){
                    redirect($this->uri->uri_string(),'refresh');
                }
                break;
            }
        }

        $this->use_footer     = false;
        $this->use_head       = false;
        $this->add_scripts    = false;
        $this->use_navigation = false;

        $data['title'] = $proposal->subject;
        $this->view = 'viewproposal';
        $data['proposal'] = $proposal;
        $data['comments'] = $this->proposals_model->get_comments($id);
        $this->load->view('themes/'.active_clients_theme(). '/views/viewproposal',$data);
    }


    public function proposals()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        if(!has_customer_permission('proposals')){
            redirect(site_url());
        }
        if ($this->input->is_ajax_request()) {
            $aColumns     = array(
                'subject',
                'total',
                'date',
                'open_till',
                'status'
                );
            $sIndexColumn = "id";
            $sTable       = 'tblproposals';

            $custom_fields = get_custom_fields('proposal',array('show_on_client_portal'=>1));
            $join = array();
            $i = 0;
            foreach($custom_fields as $field){
                array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
                array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblproposals.id = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
                $i++;
            }

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, array('AND rel_id = '.get_client_user_id(). ' AND rel_type = "customer"'), array(
                'tblproposals.id',
                'currency',
                'hash',
                'estimate_id',
                'invoice_id',
                ));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
                        $_data = $aRow[ strafter($aColumns[$i],'as ')];
                    } else {
                        $_data = $aRow[ $aColumns[$i] ];
                    }
                    if ($aColumns[$i] == 'subject') {
                        $_data = '<a href="'.site_url('viewproposal/'.$aRow['id']).'/'.$aRow['hash'].'">' . $_data . '</a>';

                        if($aRow['invoice_id'] != NULL){
                            $invoice = $this->invoices_model->get($aRow['invoice_id']);
                            $_data .= '<br /><a href="'.site_url('viewinvoice/'.$invoice->id.'/'.$invoice->hash).'" target="_blank">'.format_invoice_number($invoice->id).'</a>';
                        } else if($aRow['estimate_id'] != NULL) {
                            $estimate = $this->estimates_model->get($aRow['estimate_id']);
                            $_data .= '<br /><a href="'.site_url('viewestimate/'.$estimate->id.'/'.$estimate->hash).'" target="_blank">'.format_estimate_number($estimate->id).'</a>';
                        }
                    } else if ($aColumns[$i] == 'status') {
                        $_data = format_proposal_status($aRow['status']);
                    } else if($aColumns[$i] == 'open_till' || $aColumns[$i] == 'datecreated'){
                        $_data = _d($_data);
                    } else if($aColumns[$i] == 'total'){
                        if($aRow['currency'] != 0){
                            $_data = format_money($_data,$this->currencies_model->get_currency_symbol($aRow['currency']));
                        } else {
                            $_data = format_money($_data,$this->currencies_model->get_base_currency($aRow['currency'])->symbol);
                        }
                    }
                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('proposals');
        $this->data    = $data;
        $this->view    = 'proposals';
        $this->layout();
    }
    public function open_ticket()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        if(!has_customer_permission('support')){
            redirect(site_url());
        }

        if ($this->input->post()) {
            $id = $this->tickets_model->add($this->input->post());
            if ($id) {
                $attachments = handle_ticket_attachments($id);

                if ($attachments) {
                    $this->tickets_model->insert_ticket_attachments_to_database($attachments, $id);
                }

                set_alert('success', 'Ticket #' . $id . ' added successfuly');
                redirect(site_url('clients/ticket/' . $id));
            }
        }
        $data                   = array();
        $data['latest_tickets'] = $this->tickets_model->get_client_latests_ticket();
        $data['title']          = _l('new_ticket');
        $this->data             = $data;
        $this->view             = 'open_ticket';
        $this->layout();
    }

    public function ticket($id)
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

         if(!has_customer_permission('support')){
            redirect(site_url());
        }

        if (!$id) {
            redirect(site_url());
        }

        if ($this->input->post()) {
            $replyid = $this->tickets_model->add_reply($this->input->post(), $id, NULL);
            $attachments = handle_ticket_attachments($id);

            if ($attachments) {
                $this->tickets_model->insert_ticket_attachments_to_database($attachments, $id, $replyid);
            }
            if ($replyid) {
                set_alert('success', 'Replied to ticket #' . $id . ' successfuly');
                redirect(site_url('clients/ticket/' . $id));
            }
        }

        $data['ticket'] = $this->tickets_model->get_ticket_by_id($id, get_client_user_id());

        if ($data['ticket']->userid != get_client_user_id()) {
            redirect(site_url());
        }
        $data['ticket_replies'] = $this->tickets_model->get_ticket_replies($id, get_client_user_id());
        $data['title']          = '';
        $this->data             = $data;
        $this->view             = 'single_ticket';
        $this->layout();
    }



    public function contracts()
    {

        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        if(!has_customer_permission('contracts')){
            redirect(site_url());
        }

        $this->load->model('contracts_model');

        if ($this->input->is_ajax_request()) {
            $aColumns         = array(
                'subject',
                'contract_type',
                'datestart',
                'dateend',
                '(SELECT GROUP_CONCAT(tblcontractattachments.id) FROM tblcontractattachments LEFT JOIN tblcontracts ON tblcontracts.id = tblcontractattachments.contractid JOIN tblclients ON tblclients.userid = tblcontracts.client WHERE userid = tblcontracts.client AND tblcontractattachments.contractid = tblcontracts.id AND userid="'.get_client_user_id().'") as attachments'
                );

            $join             = array(
                'LEFT JOIN tblclients ON tblclients.userid = tblcontracts.client',
                'LEFT JOIN tblcontracttypes ON tblcontracttypes.id = tblcontracts.contract_type'
                );

            $custom_fields = get_custom_fields('contracts',array('show_on_client_portal'=>1));

            $i = 0;
            foreach($custom_fields as $field){
                array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
                array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblclients.userid = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
                $i++;
            }

            $sIndexColumn     = "id";
            $sTable           = 'tblcontracts';
            $additionalSelect = array(
                'tblcontracts.id',
                'tblcontracttypes.name'
                );

            $where = array(
                'WHERE client=' . get_client_user_id(),
                'AND trash = 0',
                'AND not_visible_to_client = 0' // dont output contracts check hide from customer
                );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {

                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {

                    if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
                        $_data = $aRow[ strafter($aColumns[$i],'as ')];
                    } else {
                        $_data = $aRow[ $aColumns[$i] ];
                    }

                    if($i == 4){
                        $attachments_ids = explode(',',$_data);
                        $_attachments = '';
                        if(count($attachments_ids)){
                            foreach($attachments_ids as $id){
                                   if($id == ''){continue;}
                                $_attachment = $this->contracts_model->get_contract_attachments($id);
                                $_attachments .= '<div class="mbot5"><i class="'.get_mime_class($_attachment->filetype).'"></i><a href="'.site_url('download/file/contract/'.$_attachment->id).'">'.$_attachment->file_name.'</a></div>';
                            }
                        }
                        $_data = $_attachments;
                    } else {
                        if ($aColumns[$i] == 'dateend' || $aColumns[$i] == 'datestart') {
                            $_data = _d($_data);
                        } else if ($aColumns[$i] == 'contract_type') {
                            $_data = $aRow['name'];
                        }
                    }
                    $row[] = $_data;
                }

                if (!empty($aRow['dateend'])) {
                    $_date_end = date('Y-m-d', strtotime($aRow['dateend']));
                    if ($_date_end < date('Y-m-d')) {
                        $row['DT_RowClass'] = 'alert-danger';
                    }
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('clients_contracts');
        $this->data    = $data;
        $this->view    = 'contracts';
        $this->layout();
    }
    public function invoices($status = false)
    {

        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        if(!has_customer_permission('invoices')){
            redirect(site_url());
        }

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'number',
                'date',
                'duedate',
                'total',
                'status'
                );

            $join = array(
                'LEFT JOIN tblclients ON tblclients.userid = tblinvoices.clientid',
                'LEFT JOIN tblcurrencies ON tblcurrencies.id = tblinvoices.currency'
                );

            $custom_fields = get_custom_fields('invoice',array('show_on_client_portal'=>1));

            $i = 0;
            foreach($custom_fields as $field){
                array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
                array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblclients.userid = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
                $i++;
            }


            $where = array(
                'AND clientid=' . get_client_user_id()
                );

            if (is_numeric($status)) {
                array_push($where, 'AND status=' . $status);
            }

            $sIndexColumn = "id";
            $sTable       = 'tblinvoices';
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblinvoices.id',
                'firstname',
                'lastname',
                'clientid',
                'symbol',
                'total',
                'hash',
                'tblinvoices.datecreated',
                'status'
                ));
            $output       = $result['output'];
            $rResult      = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {

                   if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
                    $_data = $aRow[ strafter($aColumns[$i],'as ')];
                } else {
                    $_data = $aRow[ $aColumns[$i] ];
                }

                if ($aColumns[$i] == 'number') {
                    $__data = '<a href="' . site_url('viewinvoice/' . $aRow['id'] . '/' . $aRow['hash']) . '">' . format_invoice_number($aRow['id']) . '</a><br />';
                } else if ($aColumns[$i] == 'date') {
                    $__data = _d($_data);
                } else if ($aColumns[$i] == 'duedate') {
                    $__data = _d($_data);
                } else if ($aColumns[$i] == 'total') {
                    $__data = format_money($_data, $aRow['symbol']);
                } else if($aColumns[$i] == 'status') {
                    $__data = format_invoice_status($aRow['status'], 'pull-left', true);
                } else {
                    $__data = $_data;
                }
                $row[] = $__data;
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
        die();
    }


    $data['title'] = _l('clients_my_invoices');
    $this->data    = $data;
    $this->view    = 'invoices';
    $this->layout();
}

public function viewinvoice($id = '', $hash = '')
{
    check_invoice_restrictions($id, $hash);

    $invoice = $this->invoices_model->get($id);

    // Init the client language in case is accesed directly without login
    $language = get_client_default_language($invoice->clientid);

    if (!empty($language)) {
        if(file_exists(APPPATH .'language/'.$language)){
          $this->lang->load($language . '_lang'. $language);
        }
    }

        // Handle Invoice PDF generator
    if ($this->input->post('invoicepdf')) {
        $pdf            = invoice_pdf($invoice);
        $invoice_number = format_invoice_number($invoice->id);

        $companyname = get_option('invoice_company_name');
        if ($companyname != '') {
            $invoice_number .= '-' . strtoupper(slug_it($companyname));
        }
        $pdf->Output($invoice_number . '.pdf', 'D');
        die();
    }


        // Handle $_POST payment
    if ($this->input->post('make_payment')) {
        $this->load->model('payments_model');

        if (!$this->input->post('paymentmode')) {
            set_alert('warning', _l('invoice_html_payment_modes_not_selected'));
            redirect(site_url('viewinvoice/' . $id . '/' . $hash));
        } else if ((!$this->input->post('amount') || $this->input->post('amount') == 0) && get_option('allow_payment_amount_to_be_modified') == 1) {
            set_alert('warning', _l('invoice_html_amount_blank'));
            redirect(site_url('viewinvoice/' . $id . '/' . $hash));
        }
        $this->payments_model->process_payment($this->input->post(), $id);
    }

    if ($this->input->post('paymentpdf')) {
        $id      = $this->input->post('paymentpdf');
        $payment = $this->payments_model->get($id);

        $payment->invoice_data = $this->invoices_model->get($payment->invoiceid);
        $paymentpdf            = payment_pdf($payment);
        $paymentpdf->Output(_l('payment') . '-' . $payment->paymentid . '.pdf', 'D');
        die;
    }

    $this->load->model('payment_modes_model');
    $this->load->model('payments_model');
    $data['payments']      = $this->payments_model->get_invoice_payments($id);
    $data['payment_modes'] = $this->payment_modes_model->get();
    $data['title']         = format_invoice_number($invoice->id);
    $this->use_navigation  = false;
    $data['hash']          = $hash;
    $data['invoice']       = $invoice;
    $data['bodyclass']     = 'viewinvoice';
    $this->data            = $data;
    $this->view            = 'invoicehtml';
    $this->layout();
}

public function viewestimate($id,$hash){

    check_estimate_restrictions($id, $hash);

    $estimate = $this->estimates_model->get($id);

    if ($this->input->post('estimate_action')) {
        $action = $this->input->post('estimate_action');

            // Only decline and accept allowed
        if ($action == 4 || $action == 3) {
            $success = $this->estimates_model->mark_action_status($action, $id,true);
            if (is_array($success) && $success['invoiced'] == true) {
                $invoice = $this->invoices_model->get($success['invoiceid']);
                set_alert('success', _l('clients_estimate_invoiced_successfuly'));
                redirect(site_url('viewinvoice/' . $invoice->id . '/' . $invoice->hash));
            } else if (is_array($success) && $success['invoiced'] == false) {
                if ($action == 4) {
                    set_alert('success', _l('clients_estimate_accepted_not_invoiced'));
                } else {
                    set_alert('success', _l('clients_estimate_declined'));
                }
            } else {
                set_alert('warning', _l('clients_estimate_failed_action'));
            }
        }

        redirect($this->uri->uri_string());
    }


    // Init the client language in case is accesed directly without login
    $language = get_client_default_language($estimate->clientid);

    if (!empty($language)) {
        if(file_exists(APPPATH .'language/'.$language)){
          $this->lang->load($language . '_lang'. $language);
        }
    }

        // Handle Estimate PDF generator
    if ($this->input->post('estimatepdf')) {
        $pdf            = estimate_pdf($estimate);
        $estimate_number = format_estimate_number($estimate->id);

        $companyname = get_option('invoice_company_name');
        if ($companyname != '') {
            $estimate_number .= '-' . strtoupper(slug_it($companyname));
        }
        $pdf->Output($estimate_number . '.pdf', 'D');
        die();
    }

    $data['title']         = format_estimate_number($estimate->id);
    $this->use_navigation  = false;
    $data['hash']          = $hash;
    $data['estimate']       = $estimate;
    $data['bodyclass']     = 'viewestimate';
    $this->data            = $data;
    $this->view            = 'estimatehtml';
    $this->layout();
}

public function estimates($status = false)
{

    if (!is_client_logged_in()) {
        redirect(site_url('clients/login'));
    }

     if(!has_customer_permission('estimates')){
            redirect(site_url());
     }

    if ($this->input->is_ajax_request()) {
        $aColumns = array(
            'number',
            'date',
            'reference_no',
            'expirydate',
            'total',
            'status'
            );

        $join = array(
            'LEFT JOIN tblclients ON tblclients.userid = tblestimates.clientid',
            'LEFT JOIN tblcurrencies ON tblcurrencies.id = tblestimates.currency'
            );

        $custom_fields = get_custom_fields('estimate',array('show_on_client_portal'=>1));

        $i = 0;
        foreach($custom_fields as $field){
            array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
            array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblclients.userid = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
            $i++;
        }


        $where = array(
            'AND clientid=' . get_client_user_id()
            );

        if (is_numeric($status)) {
            array_push($where, 'AND status=' . $status);
        }

            // Check if exclude draft estimates from client area is checked
        if (get_option('exclude_estimate_from_client_area_with_draft_status') == 1) {
            array_push($where, 'AND status != 1');
        }

        $sIndexColumn = "id";
        $sTable       = 'tblestimates';
        $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
            'tblestimates.id',
            'symbol',
            'invoiceid',
            'status',
            'hash',
            ));

        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {

                if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
                    $_data = $aRow[ strafter($aColumns[$i],'as ')];
                } else {
                    $_data = $aRow[ $aColumns[$i] ];
                }

                if ($aColumns[$i] == 'number') {
                    $__data = '<a href="' . site_url('viewestimate/' . $aRow['id']) . '/'.$aRow['hash'].'">' . format_estimate_number($aRow['id']) . '</a><br />';
                    if ($aRow['invoiceid'] !== NULL) {
                        $invoice = $this->invoices_model->get($aRow['invoiceid']);
                        $__data .= '<br /><a href="' . site_url('viewinvoice/' . $invoice->id . '/' . $invoice->hash) . '">' . format_invoice_number($invoice->id) . '</a>';
                    }

                } else if ($aColumns[$i] == 'date') {
                    $__data = _d($_data);
                } else if ($aColumns[$i] == 'expirydate') {
                    $__data = _d($_data);
                } else if ($aColumns[$i] == 'total') {
                    $__data = format_money($_data, $aRow['symbol']);
                } else if ($aColumns[$i] == 'status') {
                    $__data = format_estimate_status($aRow['status'], 'pull-left');
                } else {
                    $__data = $_data;
                }

                $row[] = $__data;
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
        die();
    }

    $data['title'] = _l('clients_my_estimates');
    $this->data    = $data;
    $this->view    = 'estimates';
    $this->layout();
}

public function survey($id, $hash)
{
    if (!$hash || !$id) {
        die('No survey specified');
    }

    $this->load->model('surveys_model');
    $survey = $this->surveys_model->get($id);

    if (!$survey) {
        die('Survey not found');
    }

    if ($survey->hash != $hash) {
        die;
    }

    if ($survey->active == 0) {
            // Allow users with permission manage surveys to preview the survey even if is not active
        if (!has_permission('manageSurveys')) {
            die('Survey not active');
        }
    }

        // Check if survey is only for logged in participants / staff / clients
    if ($survey->onlyforloggedin == 1) {
        if (!is_logged_in()) {
            die('This survey is only for logged in users');
        }
    }

        // Ip Restrict check
    if ($survey->iprestrict == 1) {
        $this->db->where('surveyid', $id);
        $this->db->where('ip', $this->input->ip_address());
        $total = $this->db->count_all_results('tblsurveyresultsets');
        if ($total > 0) {
            die('Already participated on this survey. Thanks');
        }
    }

    if ($this->input->post()) {
        $success = $this->surveys_model->add_survey_result($id, $this->input->post());
        if ($success) {
            $survey = $this->surveys_model->get($id);
            if ($survey->redirect_url !== '') {
                redirect($survey->redirect_url);
            }
            set_alert('success', 'Thank you for participating in this survey. Your answers are very important to us.');
            redirect(site_url());

        }
    }

    $this->use_footer     = false;
    $this->use_head       = false;
    $this->add_scripts    = false;
    $this->use_navigation = false;

    $data['survey'] = $survey;
    $data['title']  = $data['survey']->subject;
    $this->data     = $data;
    $this->view     = 'survey_view';
    $this->layout();
}

public function profile()
{
    if (!is_client_logged_in()) {
        redirect(site_url('clients/login'));
    }

    if ($this->input->post('profile')) {
        $this->form_validation->set_rules('firstname',_l('client_firstname'),'required');
        $this->form_validation->set_rules('lastname',_l('client_lastname'),'required');
         $custom_fields = get_custom_fields('customers',array('show_on_client_portal'=>1,'required'=>1));
         foreach($custom_fields as $field){
            $this->form_validation->set_rules('custom_fields['.$field['fieldto'].']['.$field['id'].']',$field['name'],'required');
        }
        if($this->form_validation->run() !== FALSE){
            $data = $this->input->post();
            // Unset the form indicator so we wont send it to the model
            unset($data['profile']);
            $success = $this->clients_model->update($data, get_client_user_id(),true);
            if ($success == true) {
                set_alert('success', _l('clients_profile_updated'));
            }
            redirect(site_url('clients/profile'));
        }
    } else if($this->input->post('change_password')){
        $this->form_validation->set_rules('oldpassword',_l('clients_edit_profile_old_password'),'required');
        $this->form_validation->set_rules('newpassword',_l('clients_edit_profile_new_password'),'required');
        $this->form_validation->set_rules('newpasswordr',_l('clients_edit_profile_new_password_repeat'),'required|matches[newpassword]');

        if($this->form_validation->run() !== FALSE){
          $success = $this->clients_model->change_client_password($this->input->post());
          if (is_array($success) && isset($success['old_password_not_match'])) {
            set_alert('danger', 'Your old password dont match');
        } else if ($success == true) {
            set_alert('success', 'Password changed successfuly');
        }
        redirect(site_url('clients/profile'));
    }

}
$data['title'] = _l('clients_profile_heading');
$this->data    = $data;
$this->view    = 'profile';
$this->layout();
}

public function register()
{
    if (get_option('allow_registration') != 1 || is_client_logged_in()) {
        redirect(site_url());
    }

     $this->form_validation->set_rules('firstname',_l('client_firstname'),'required');
        $this->form_validation->set_rules('lastname',_l('client_lastname'),'required');
        $this->form_validation->set_rules('email',_l('client_email'),'required|is_unique[tblclients.email]|valid_email');
        $this->form_validation->set_rules('password',_l('clients_register_password'),'required');
        $this->form_validation->set_rules('passwordr',_l('clients_register_password_repeat'),'required|matches[password]');
         $custom_fields = get_custom_fields('customers',array('show_on_client_portal'=>1,'required'=>1));
         foreach($custom_fields as $field){
            $this->form_validation->set_rules('custom_fields['.$field['fieldto'].']['.$field['id'].']',$field['name'],'required');
        }
    if($this->form_validation->run() !== FALSE){
    if ($this->input->post()) {
        $clientid = $this->clients_model->add($this->input->post());

        if ($clientid) {
            $this->load->model('authentication_model');
            $logged_in = $this->authentication_model->login($this->input->post('email'), $this->input->post('password'), false, false);
            if ($logged_in) {
                set_alert('success', _l('clients_successfully_registered'));
            } else {
                set_alert('warning', _l('clients_account_created_but_not_logged_in'));
                redirect(site_url('clients/login'));
            }
            redirect(site_url());
        }
    }
    }
    $data['title'] = _l('clients_register_heading');
    $this->data    = $data;
    $this->view    = 'register';
    $this->layout();
}

public function forgot_password()
{
    if (is_client_logged_in()) {
        redirect(site_url());
    }

    $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_client_email_exists');

    if ($this->input->post()) {
        if ($this->form_validation->run() !== false) {
            $this->load->model('Authentication_model');
            $success = $this->Authentication_model->forgot_password($this->input->post('email'));
            if (is_array($success) && isset($success['memberinactive'])) {
                set_alert('danger', 'Inactive account');
            } else if ($success == true) {
                set_alert('success', 'Check your email for further instructions resetting your password');
            } else {
                set_alert('danger', 'Error setting new password key');
            }
            redirect(site_url('clients/forgot_password'));
        }
    }
    $this->view = 'forgot_password';
    $this->layout();
}

public function reset_password($staff, $userid, $new_pass_key)
{
 $this->load->model('Authentication_model');
 if (!$this->Authentication_model->can_reset_password($staff, $userid, $new_pass_key)) {
    set_alert('danger', 'Password key expired or invalid user');
    redirect(site_url('clients/forgot_password'));
}

$this->form_validation->set_rules('password', 'Password', 'required');
$this->form_validation->set_rules('passwordr', 'Password Confirmation', 'required|matches[password]');

if ($this->input->post()) {
    if ($this->form_validation->run() !== false) {
        do_action('before_user_reset_password', array(
            'staff' => $staff,
            'userid' => $userid
            ));
        $success = $this->Authentication_model->reset_password(0, $userid, $new_pass_key, $this->input->post('passwordr'));
        if (is_array($success) && $success['expired'] == true) {
            set_alert('danger', 'Password key expired');
        } else if ($success == true) {
            do_action('after_user_reset_password', array(
                'staff' => $staff,
                'userid' => $userid
                ));
            set_alert('success', 'Your password has been reset. Please login now!');
        } else {
            set_alert('danger', 'Error resetting your password. Try again.');
        }

    }

}

$this->view = 'reset_password';
$this->layout();

}

public function dismiss_announcement()
{
    if (!is_client_logged_in()) {
        redirect(site_url('clients/login'));
    }
    if ($this->input->post()) {
        $this->misc_model->dismiss_announcement($this->input->post(), false);
    }
}


public function knowledge_base($slug = '')
{
    if ((get_option('use_knowledge_base') == 1 && !is_client_logged_in() && get_option('knowledge_base_without_registration') == 1) || (get_option('use_knowledge_base') == 1 && is_client_logged_in())) {

        // This is for did you find this answer useful
        if (($this->input->post() && $this->input->is_ajax_request())) {
            echo json_encode($this->knowledge_base_model->add_article_answer($this->input->post()));
            die();
        }

        $data = array();
        if ($slug == '' || $this->input->get('groupid')) {
            $data['title'] = _l('clients_knowledge_base');
            $this->view    = 'knowledge_base';
        } else {
            $data['article'] = $this->knowledge_base_model->get(false, $slug);
            if ($data['article']->active_article == 0) {
                redirect(site_url('knowledge_base'));
            }
            $data['title'] = $data['article']->subject;
            $this->view    = 'knowledge_base_article';
        }

        $this->data = $data;
        $this->layout();
    } else {
        redirect(site_url());
    }
}

public function login()
{

    if (is_client_logged_in()) {
        redirect(site_url());
    }

    if (get_option('allow_registration') == 1) {
        $data['title'] = _l('clients_login_heading_register');
    } else {
        $data['title'] = _l('clients_login_heading_no_register');
    }

    $this->data = $data;
    $this->view = 'login';
    $this->layout();
}

public function logout()
{
    $this->load->model('authentication_model');
    $this->authentication_model->logout(false);
    redirect(site_url('clients/login'));
}

public function client_email_exists($email = '')
{
    if($email == ''){
        $email = $this->input->post('email');
    }

    $this->db->where('email', $email);
    $total_rows = $this->db->count_all_results('tblclients');

    if ($this->input->post() && $this->input->is_ajax_request()) {
        if ($total_rows > 0) {
            echo json_encode(false);
        } else {
            echo json_encode(true);
        }
        die();
    } else if($this->input->post()) {
       if($total_rows == 0){
        $this->form_validation->set_message('client_email_exists', '%s not found.');
        return false;
    }

    return true;
}
}

    /**
     * Client home chart
     * @return mixed
     */
    public function client_home_chart()
    {


        if (is_client_logged_in()) {
            $statuses = array(
                1,
                2,
                4
                );
            $chart    = array(
                'labels' => array(
                    'January',
                    'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July',
                    'August',
                    'September',
                    'October',
                    'November',
                    'December'
                    ),
                'datasets' => array()
                );
            foreach ($statuses as $status) {

                $months_report = $this->input->post('months_report');
                if ($months_report != '') {
                    $custom_date_select = '';
                    if (is_numeric($months_report)) {
                        $custom_date_select = 'date > DATE_SUB(now(), INTERVAL ' . $months_report . ' MONTH)';
                    } else if ($months_report == 'custom') {

                        $from_date = to_sql_date($this->input->post('report_from'));
                        $to_date   = to_sql_date($this->input->post('report_to'));

                        if ($from_date == $to_date) {
                            $custom_date_select = 'date ="' . $from_date . '"';
                        } else {
                            $custom_date_select = '(date BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
                        }
                    }
                    $this->db->where($custom_date_select);
                }

                $this->db->select('total as amount, date');
                $this->db->from('tblinvoices');
                $this->db->where('clientid', get_client_user_id());
                $this->db->where('status', $status);

                $by_currency = $this->input->post('report_currency');
                if ($by_currency) {
                    $this->db->where('currency', $by_currency);
                }

                $payments = $this->db->get()->result_array();

                $data          = array();
                $data['temp']  = $chart['labels'];
                $data['total'] = array();

                $i = 0;
                foreach ($chart['labels'] as $month) {
                    $data['temp'][$i] = array();
                    foreach ($payments as $payment) {
                        $_month = date('F', strtotime($payment['date']));
                        if ($_month == $month) {
                            $data['temp'][$i][] = $payment['amount'];
                        }
                    }
                    $data['total'][] = array_sum($data['temp'][$i]);
                    $i++;
                }

                if ($status == 1) {
                    $strokeColor = '#fc142b';
                    $borderColor = '#fc2d42';
                } else if ($status == 2) {
                    $strokeColor = '#259b24';
                    $borderColor = '#20861f';
                } else if ($status == 4) {
                    $strokeColor = '#ff6f00';
                    $borderColor = '#e66400';
                }

                array_push($chart['datasets'], array(
                    'label' => format_invoice_status($status, '', false, true),
                    'fillColor' => 'rgba(151,187,205,0.2)',
                    'strokeColor' => $strokeColor,
                    'pointColor' => $strokeColor,
                    'pointStrokeColor' => '#fff',
                    'pointHighlightFill' => '#fff',
                    'pointHighlightStroke' => $strokeColor,
                    'data' => $data['total']
                    ));
            }

            echo json_encode($chart);
        }
    }
}
