<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payments extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('payments_model');
    }

    /* In case if user go only on /payments*/
    public function index($clientid = false)
    {
        $this->list_payments($clientid);
    }
    /* List all invoice paments */
    public function list_payments($clientid = false)
    {

         $has_permission = has_permission('manageSales');

         if(!$has_permission && !$this->input->is_ajax_request()){
            access_denied('manageSales');
         }

         $_custom_view = '';

         if($this->input->get('custom_view')){
            $_custom_view = $this->input->get('custom_view');
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
                'tblinvoicepaymentrecords.id',
                'invoiceid',
                'paymentmode',
                'company',
                'amount',
                'tblinvoicepaymentrecords.date'
            );

            $join = array(
                'LEFT JOIN tblinvoices ON tblinvoices.id = tblinvoicepaymentrecords.invoiceid',
                'LEFT JOIN tblclients ON tblclients.userid = tblinvoices.clientid',
                'LEFT JOIN tblcurrencies ON tblcurrencies.id = tblinvoices.currency',
                'LEFT JOIN tblinvoicepaymentsmodes ON tblinvoicepaymentsmodes.id = tblinvoicepaymentrecords.paymentmode'

            );

            $where = array();
            if(is_numeric($clientid)){
                array_push($where,'AND tblclients.userid='.$clientid);
            }

            if($this->input->post('custom_view')){
                $custom_view = $this->input->post('custom_view');
                if($custom_view == 'today'){
                    array_push($where,'AND DATE(tblinvoicepaymentrecords.date) = "'.date('Y-m-d').'"');
                }
            }

            $sIndexColumn = "id";
            $sTable       = 'tblinvoicepaymentrecords';
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblinvoices.id',
                'tblinvoicepaymentrecords.date',
                'firstname',
                'lastname',
                'company',
                'clientid',
                'symbol',
                'total',
                'number',
                'tblinvoicepaymentsmodes.name',
                'tblinvoicepaymentsmodes.id as paymentmodeid'
            ));
            $output       = $result['output'];
            $rResult      = $result['rResult'];

             $this->load->model('payment_modes_model');
             $online_modes = $this->payment_modes_model->get_online_payment_modes();

            foreach ($rResult as $aRow) {
                $row = array();

                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'paymentmode') {
                         $_data = $aRow['name'];
                         // Since version 1.0.1
                        if (is_null($aRow['paymentmodeid'])) {
                            foreach ($online_modes as $online_mode) {
                                if ($aRow['paymentmode'] == $online_mode['id']) {
                                    $_data = $online_mode['name'];
                                }
                            }
                        }
                    } else if ($aColumns[$i] == 'tblinvoicepaymentrecords.id') {
                        $_data = '<a href="' . admin_url('payments/payment/' . $_data) . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblinvoicepaymentrecords.date') {
                        $_data = _d($_data);
                    } else if ($aColumns[$i] == 'invoiceid') {
                        $_data = '<a href="' . admin_url('invoices/list_invoices/' . $aRow[$aColumns[$i]]) . '">' . format_invoice_number($aRow['id']) . '</a>';
                    } else if ($aColumns[$i] == 'company') {
                        $company = $aRow['company'];
                        if ($company != '') {
                            $company .= '<small class="text-muted"> ' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</small>';
                        } else {
                            $company = $aRow['firstname'] . ' ' . $aRow['lastname'];
                        }

                        $_data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $company . '</a>';
                    } else if ($aColumns[$i] == 'amount') {
                        $_data = format_money($_data,$aRow['symbol']);
                    }

                    $row[] = $_data;
                }

                $options            = icon_btn('admin/payments/payment/' . $aRow['tblinvoicepaymentrecords.id'], 'pencil-square-o');
                $row[]              = $options .= icon_btn('admin/payments/delete/' . $aRow['tblinvoicepaymentrecords.id'], 'remove', 'btn-danger');
                $output['aaData'][] = $row;
            }
            echo json_encode($output);
            die();
        }

        $data['custom_view'] = $_custom_view;
        $data['title'] = _l('payments');
        $this->load->view('admin/payments/manage', $data);
    }

    /* Update payment data */
    public function payment($id = '')
    {

        if (!$id) {
            redirect(admin_url('payments'));
        }

        if ($this->input->post()) {
            $success = $this->payments_model->update($this->input->post(), $id);
            if ($success) {
               set_alert('success', _l('updated_successfuly',_l('payment')));
            }
            redirect(admin_url('payments/payment/' . $id));
        }

        $data['payment'] = $this->payments_model->get($id);
        if (!$data['payment']) {
            blank_page(_l('payment_not_exists'));
        }

        $this->load->model('invoices_model');
        $data['invoice'] = $this->invoices_model->get($data['payment']->invoiceid);

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get();

        $data['title'] = _l('edit',_l('payment_lowercase'));
        $this->load->view('admin/payments/payment', $data);
    }

    /**
     * Generate payment pdf
     * @since  Version 1.0.1
     * @param  mixed $id Payment id
     */
    public function pdf($id){
        $payment = $this->payments_model->get($id);
        $this->load->model('invoices_model');
        $payment->invoice_data = $this->invoices_model->get($payment->invoiceid);
        $paymentpdf = payment_pdf($payment);
        $paymentpdf->Output(_l('payment') . '-' . $payment->paymentid . '.pdf', 'D');
    }

    /* Delete payment */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('payments'));
        }

        $response = $this->payments_model->delete($id);

        if($response == true){
            set_alert('success', _l('deleted',_l('payment')));
        } else {
            set_alert('warning', _l('problem_deleting',_l('payment_lowercase')));
        }

        redirect(admin_url('payments'));
    }
}
