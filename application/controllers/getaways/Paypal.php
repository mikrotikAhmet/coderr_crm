<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paypal extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('getaways/paypal_getaway');
    }

    public function complete_purchase()
    {

        $invoiceid             = $this->input->get('invoiceid');
        $hash                  = $this->input->get('hash');
        $online_payment_amount = $this->session->userdata('online_payment_amount');
        $token                 = $this->input->get('token');
        check_invoice_restrictions($invoiceid, $hash);

        // Check if token is the same like the one in the database
        $this->db->where('token', $token);
        $this->db->where('id', $invoiceid);
        $db_token = $this->db->get('tblinvoices')->row()->token;

        if ($db_token != $token) {
            set_alert('danger', _l('payment_getaway_token_not_found'));
            redirect(site_url('viewinvoice/' . $invoiceid . '/' . $hash));
        }

        $paypalResponse = $this->paypal_getaway->complete_purchase(array(
            'token' => $db_token,
            'amount' => $online_payment_amount
        ));

        // Check if error exists in the response
        if (isset($paypalResponse['L_ERRORCODE0'])) {
            set_alert('warning', $paypalResponse['L_SHORTMESSAGE0'] . '<br />' . $paypalResponse['L_LONGMESSAGE0']);
            logActivity('Paypal Payment Error [Error CODE: ' . $paypalResponse['L_ERRORCODE0'] . ' Message: ' . $paypalResponse['L_SHORTMESSAGE0'] . '<br />' . $paypalResponse['L_LONGMESSAGE0'] . ']');
            redirect(site_url('viewinvoice/' . $invoiceid . '/' . $hash));

        } else if (isset($paypalResponse['PAYMENTINFO_0_ACK']) && $paypalResponse['PAYMENTINFO_0_ACK'] === 'Success') {
            // Add payment to database
            $payment_data['amount']        = $online_payment_amount;
            $payment_data['invoiceid']     = $invoiceid;
            $payment_data['paymentmode']   = 'paypal';
            $payment_data['transactionid'] = $paypalResponse['PAYMENTINFO_0_TRANSACTIONID'];

            $this->load->model('payments_model');
            $success = $this->payments_model->add($payment_data);

            if ($success) {
                set_alert('success', _l('online_payment_recorded_success'));
            } else {
                set_alert('danger', _l('online_payment_recorded_success_fail_database'));
            }
            redirect(site_url('viewinvoice/' . $invoiceid . '/' . $hash));
        }

        $this->session->unset_userdata(array(
            'online_payment_amount' => ''
        ));
    }
}
