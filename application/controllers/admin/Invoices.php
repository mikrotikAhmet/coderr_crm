<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoices extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('invoices_model');
    }

    /* Get all invoices in case user go on index page */
    public function index($id = false)
    {
        $this->list_invoices($id);
    }

    /* List all invoices datatables */
    public function list_invoices($id = false, $clientid = false)
    {
        $has_permission = has_permission('manageSales');

         if(!$has_permission && !$this->input->is_ajax_request()){
            access_denied('manageSales');
         }

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('',true);
        $_custom_view = '';
        $_status = '';

        if($this->input->get('custom_view')){
            $_custom_view = $this->input->get('custom_view');
        } else if($this->input->get('status')){
             $_status = $this->input->get('status');
        }

        if ($this->input->is_ajax_request()) {
            // From client profile
            if(is_numeric($clientid)){
                if(!$has_permission){
                    echo json_encode(json_decode('{"draw":1,"iTotalRecords":"0","iTotalDisplayRecords":"0","aaData":[]}'));
                    die;
                }
            }

            $aColumns = array(
                'number',
                'date',
                'company',
                'duedate',
                'total',
                'status'
            );

            $join = array(
                'LEFT JOIN tblclients ON tblclients.userid = tblinvoices.clientid',
                'LEFT JOIN tblcurrencies ON tblcurrencies.id = tblinvoices.currency'
            );

            $custom_fields = get_custom_fields('invoice',array('show_on_table'=>1));

            $i = 0;
            foreach($custom_fields as $field){
                    array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
                    array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblinvoices.id = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
                    $i++;
            }

            $where                    = array();

            if (is_numeric($clientid)) {
                $where                    = array(
                    'AND clientid=' . $clientid
                );

            }

            if ($this->input->post('status')) {
                array_push($where, 'AND status=' . $this->input->post('status'));
            }

            if($this->input->post('custom_view')){
                $custom_view = $this->input->post('custom_view');
                if($custom_view == 'not_sent'){
                   array_push($where,'AND sent=0 AND status != 2');
               } else if ($custom_view == 'not_have_payment'){
                   array_push($where,'AND tblinvoices.id NOT IN(SELECT invoiceid FROM tblinvoicepaymentrecords)');
               } else if($custom_view == 'recurring'){
                   array_push($where,'AND recurring > 0');
               } else {
                // Made payment mode
                foreach($data['payment_modes'] as $mode){
                    if($mode['id'] == $custom_view){
                        array_push($where,'AND tblinvoices.id IN (SELECT invoiceid FROM tblinvoicepaymentrecords WHERE paymentmode="'.$mode['id'].'")');
                        break;
                    }
                }
            }
        }

            $sIndexColumn = "id";
            $sTable       = 'tblinvoices';
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'firstname',
                'tblinvoices.id',
                'lastname',
                'company',
                'clientid',
                'symbol',
                'total',
                'status'
            ));
            $output       = $result['output'];
            $rResult      = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();

                $company = $aRow['company'];
                if ($company != '') {
                    $company .= '<small class="text-muted"> ' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</small>';
                } else {
                    $company = $aRow['firstname'] . ' ' . $aRow['lastname'];
                }
                for ($i = 0; $i < count($aColumns); $i++) {

                    if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
                        $_data = $aRow[ strafter($aColumns[$i],'as ')];
                    } else {
                        $_data = $aRow[ $aColumns[$i] ];
                    }

                    if ($aColumns[$i] == 'number') {
                        // If is from client area table
                    if (is_numeric($clientid)) {
                        $__data = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" target="_blank">' . format_invoice_number($aRow['id']) . '</a><br />';
                    } else {
                           $__data = '<a href="#" onclick="init_invoice(' . $aRow['id'] . '); return false;">' . format_invoice_number($aRow['id']) . '</a><br />';
                    }

                    } else if ($aColumns[$i] == 'date') {
                        $__data = _d($_data);
                    } else if ($aColumns[$i] == 'company') {
                        $__data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $company . '</a><br />';
                    } else if ($aColumns[$i] == 'duedate') {
                        $__data = _d($_data);
                    } else if ($aColumns[$i] == 'total') {
                        $__data = format_money($_data, $aRow['symbol']);
                    } else if($aColumns[$i] == 'status') {
                        $__data = format_invoice_status($aRow['status']);
                        // Status
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

        $data['invoiceid'] = '';
        if (is_numeric($id)) {
            $data['invoiceid'] = $id;
        }

        $data['custom_view'] = $_custom_view;
        $data['status'] = $_status;

        $data['bodyclass'] = 'small-table';
        $data['title']     = _l('invoices');
        $this->load->view('admin/invoices/manage', $data);
    }
    public function calc_due_date(){
        if($this->input->post()){
            $date = $this->input->post('date');
            $duedate = '';
            if(get_option('invoice_due_after') != 0){
              $duedate = _d(date('Y-m-d',strtotime('+'.get_option('invoice_due_after') .' DAY',strtotime($date))));
            }

            echo $duedate;
        }
    }
    public function copy($id){
        if(!$id){
            redirect(admin_url('invoices'));
        }
         if(!has_permission('manageSales')){
            access_denied('manageSales');
         }

         $new_id = $this->invoices_model->copy($id);
         if($new_id){
             set_alert('success',_l('invoice_copy_success'));
             redirect(admin_url('invoices/invoice/'.$new_id));
         } else {
             set_alert('success',_l('invoice_copy_fail'));
         }

         redirect(admin_url('invoices/invoice/'.$id));
    }
    function get_items_suggestions(){
         if(!has_permission('manageSales')){
            access_denied('manageSales');
         }

        $this->load->model('invoice_items_model');
        echo json_encode($this->invoice_items_model->get());
    }

    /* Add new invoice or update existing */
    public function invoice($id = '')
    {
         if(!has_permission('manageSales')){
            access_denied('manageSales');
         }

        if ($this->input->post()) {
            if ($id == '') {
                $id = $this->invoices_model->add($this->input->post());
                if ($id) {
                    $invoice = $this->invoices_model->get($id);
                    if ($invoice->number != $this->input->post('_number')) {
                        set_alert('warning', _l('invoice_number_changed'));
                    } else {
                       set_alert('success', _l('added_successfuly',_l('invoice')));
                    }

                    redirect(admin_url('invoices/list_invoices/' . $id));
                }
            } else {
                $success = $this->invoices_model->update($this->input->post(), $id);
                if ($success) {
                   set_alert('success', _l('updated_successfuly',_l('invoice')));
                }
                redirect(admin_url('invoices/list_invoices/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('create_new_invoice');
        } else {
            $invoice = $this->invoices_model->get($id);
            $data['invoice_recurring_invoices'] = $this->invoices_model->get_invoice_recuring_invoices($id);
            $invoice->date    = _d($invoice->date);
            $invoice->duedate = _d($invoice->duedate);
            $this->load->model('emails_model');
            if ($invoice->sent == 0) {
                $data['template'] = $this->emails_model->parse_template('invoice-send-to-client', $invoice->clientid, $id);
            } else {
                $data['template'] = $this->emails_model->parse_template('invoice-already-send', $invoice->clientid, $id);
            }
            $data['invoice']  = $invoice;
            $data['edit'] = true;
            $title           = _l('edit',_l('invoice_lowercase'));
        }

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get();

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();

        $this->load->model('invoice_items_model');
        $data['items'] = $this->invoice_items_model->get();

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $this->load->model('clients_model');
        $data['clients'] = $this->clients_model->get();

        $this->load->model('staff_model');
        $data['staff'] = $this->staff_model->get('',1);

        $data['title']     = $title;
        $data['bodyclass'] = 'invoice';
        $this->load->view('admin/invoices/invoice', $data);
    }

    public function init_invoice_items_ajax($id){
        echo json_encode($this->invoices_model->get_invoice_items($id));
    }

    /* Get all invoice data used when user click on invoiec number in a datatable left side*/
    public function get_invoice_data_ajax($id)
    {
        if(!has_permission('manageSales')){
            access_denied('manageSales');
         }

        if (!$id) {
            die('No invoice found');
        }

        $invoice = $this->invoices_model->get($id);
        if(!$invoice){
            echo 'Invoice Not Found';
            die;
        }
        $invoice->date    = _d($invoice->date);
        $invoice->duedate = _d($invoice->duedate);

        $this->load->model('emails_model');

        if ($invoice->sent == 0) {
            $data['template'] = $this->emails_model->parse_template('invoice-send-to-client', $invoice->clientid, $id);
        } else {
            $data['template'] = $this->emails_model->parse_template('invoice-already-send', $invoice->clientid, $id);
        }

        // Check for recorded payments
        $this->load->model('payments_model');
        $data['payments'] = $this->payments_model->get_invoice_payments($id);
        $data['activity'] = $this->invoices_model->get_invoice_activity($id);
        $data['invoice']  = $invoice;
        $data['attachments']  = $this->invoices_model->get_attachments($id);
        $this->load->view('admin/invoices/invoice_preview_template', $data);
    }

     public function get_invoices_total(){
        if($this->input->post()){
            $data['totals'] = $this->invoices_model->get_invoices_total($this->input->post());
            $this->load->model('currencies_model');
            $base_currency = $this->currencies_model->get_base_currency()->id;
            if(is_using_multiple_currencies() || (total_rows('tblinvoices',array('currency'=>$base_currency)) == 0) && total_rows('tblinvoices') > 0){
                $this->load->model('currencies_model');
                $data['currencies'] = $this->currencies_model->get();
            }
            $this->load->view('admin/invoices/invoices_total_template',$data);
        }
    }


    /* Record new inoice payment view */
    public function record_invoice_payment_ajax($id)
    {
        $this->load->model('payment_modes_model');
        $this->load->model('payments_model');
        $data['payment_modes'] = $this->payment_modes_model->get();
        $data['invoice']       = $invoice = $this->invoices_model->get($id);
        $data['payments']      = $this->payments_model->get_invoice_payments($id);
        $this->load->view('admin/invoices/record_payment_template', $data);
    }
    /* This is where invoice payment record $_POST data is send */
    public function record_payment()
    {
         if(!has_permission('manageSales')){
            access_denied('manageSales');
         }
        if ($this->input->post()) {
            $this->load->model('payments_model');
            $id = $this->payments_model->process_payment($this->input->post(),'');

            if ($id) {
                set_alert('success', _l('invoice_payment_recorded'));
                redirect(admin_url('payments/payment/' . $id));
            } else {
                set_alert('danger', _l('invoice_payment_record_failed'));
            }

            redirect(admin_url('invoices/list_invoices/' . $this->input->post('invoiceid')));
        }
    }

    /* Send invoiece to email */
    public function send_to_email($id)
    {
         if(!has_permission('manageSales')){
            access_denied('manageSales');
         }
        $success = $this->invoices_model->sent_invoice_to_client($id,'',$this->input->post('attach_pdf'));
        if ($success) {
            set_alert('success', _l('invoice_sent_to_client_success'));

        } else {
            set_alert('danger', _l('invoice_sent_to_client_fail'));
        }
        redirect(admin_url('invoices/list_invoices/' . $id));
    }

    /* Delete invoice payment*/
    public function delete_payment($id, $invoiceid)
    {
         if(!has_permission('manageSales')){
            access_denied('manageSales');
         }
        $this->load->model('payments_model');

        if (!$id) {
            redirect(admin_url('payments'));
        }

        $response = $this->payments_model->delete($id);

        if ($response == true) {
            set_alert('success', _l('deleted',_l('payment')));
        } else {
           set_alert('warning', _l('problem_deleting',_l('payment_lowercase')));
        }

        redirect(admin_url('invoices/list_invoices/' . $invoiceid));

    }
    /* Delete invoice */
    public function delete($id)
    {
         if(!has_permission('manageSales')){
            access_denied('manageSales');
         }

        if (!$id) {
            redirect(admin_url('invoices/list_invoices'));
        }

        $success = $this->invoices_model->delete($id);

        if ($success) {
           set_alert('success', _l('deleted',_l('invoice')));
        } else {
            set_alert('warning', _l('problem_deleting',_l('invoice_lowercase')));
        }

        redirect(admin_url('invoices/list_invoices'));
    }

    public function delete_attachment($id){
        if(!has_permission('manageSales')){
            access_denied('manageSales');
        }
        echo $this->invoices_model->delete_attachment($id);
    }
    /* Will send overdue notice to client */
        public function send_overdue_notice($id)
        {
        if(!has_permission('manageSales')){
            access_denied('manageSales');
        }

        $send = $this->invoices_model->send_invoice_overdue_notice($id);
        if ($send) {
            set_alert('success', _l('invoice_overdue_reminder_sent'));
        } else {
            set_alert('warning', _l('invoice_reminder_send_problem'));
        }
        redirect(admin_url('invoices/list_invoices/' . $id));
    }

    /* Generates invoice PDF and senting to email of $send_to_email = true is passed */
    public function pdf($id, $send_to_email = false)
    {
         if(!has_permission('manageSales')){
            access_denied('manageSales');
         }
        if (!$id) {
            redirect(admin_url('invoices/list_invoices'));
        }

        $invoice        = $this->invoices_model->get($id);

        $invoice_number = format_invoice_number($invoice->id);

        $pdf            = invoice_pdf($invoice);
        $pdf->Output($invoice_number . '.pdf', 'D');
    }

    public function upload_file(){
        handle_invoice_attachments($this->input->post('invoiceid'));
    }

    public function mark_as_sent($id){
        if (!$id) {
            redirect(admin_url('invoices/list_invoices'));
        }

        $success = $this->invoices_model->set_invoice_sent($id,false,true);
        if($success){
            set_alert('success',_l('invoice_marked_as_sent'));
        } else {
            set_alert('warning',_l('invoice_marked_as_sent_failed'));
        }
        redirect(admin_url('invoices/list_invoices/'.$id));
    }
}
