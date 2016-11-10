<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payments_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('invoices_model');
    }

    /**
     * Get payment by ID
     * @param  mixed $id payment id
     * @return object
     */
    public function get($id)
    {
        $this->db->select('*,tblinvoicepaymentrecords.id as paymentid');
        $this->db->join('tblinvoicepaymentsmodes', 'tblinvoicepaymentsmodes.id = tblinvoicepaymentrecords.paymentmode', 'left');
        $this->db->order_by('tblinvoicepaymentrecords.id', 'asc');
        $this->db->where('tblinvoicepaymentrecords.id', $id);
        $payment = $this->db->get('tblinvoicepaymentrecords')->row();
        if(!$payment){
            return false;
        }
        // Since version 1.0.1
        $this->load->model('payment_modes_model');
        $online_modes = $this->payment_modes_model->get_online_payment_modes();
        if (is_null($payment->id)) {
            foreach ($online_modes as $online_mode) {
                if ($payment->paymentmode == $online_mode['id']) {
                    $payment->name = $online_mode['name'];
                }
            }
        }
        return $payment;
    }

    /**
     * Get all invoice payments
     * @param  mixed $invoiceid invoiceid
     * @return array
     */
    public function get_invoice_payments($invoiceid)
    {
        $this->db->select('*,tblinvoicepaymentrecords.id as paymentid');
        $this->db->join('tblinvoicepaymentsmodes', 'tblinvoicepaymentsmodes.id = tblinvoicepaymentrecords.paymentmode', 'left');
          $this->db->order_by('tblinvoicepaymentrecords.id', 'asc');
        $this->db->where('invoiceid', $invoiceid);

        $payments = $this->db->get('tblinvoicepaymentrecords')->result_array();

        // Since version 1.0.1
        $this->load->model('payment_modes_model');
        $online_modes = $this->payment_modes_model->get_online_payment_modes(true);

        $i = 0;
        foreach ($payments as $payment) {
            if (is_null($payment['id'])) {
                foreach ($online_modes as $online_mode) {
                    if ($payment['paymentmode'] == $online_mode['id']) {
                        $payments[$i]['id']   = $online_mode['id'];
                        $payments[$i]['name'] = $online_mode['name'];
                    }
                }
            }
            $i++;
        }
        return $payments;
    }

    /**
     * Process invoice payment offline or online
     * @since  Version 1.0.1
     * @param  array $data $_POST data
     * @return boolean
     */
    public function process_payment($data, $invoiceid = '')
    {

        // Offline payment mode from the admin side
        if (is_numeric($data['paymentmode'])) {
            if (is_staff_logged_in()) {
                $id = $this->add($data);
                return $id;
            } else {
                return false;
            }
            // Is online payment mode request by client or staff
        } else if (!is_numeric($data['paymentmode']) && !empty($data['paymentmode'])) {

            // This request will come from admin area only
            // If admin clicked the button that dont want to pay the invoice from the getaways only want
            if (is_staff_logged_in() && has_permission('manageSales')) {
                if (isset($data['do_not_redirect'])) {
                    $id = $this->add($data);
                    return $id;
                }
            }
            if (!is_numeric($invoiceid)) {
                if (!isset($data['invoiceid'])) {
                    die('No invoice specified');
                } else {
                    $invoiceid = $data['invoiceid'];
                }
            }

            $invoice = $this->invoices_model->get($invoiceid);
            // Check if request coming from admin area and the user added note so we can insert the note also when the payment is recorded
            if (isset($data['note']) && $data['note'] != '') {
                $this->session->set_userdata(array(
                    'payment_admin_note' => $data['note']
                ));
            }
            if(get_option('allow_payment_amount_to_be_modified') == 0){
                $data['amount'] = get_invoice_total_left_to_pay($invoiceid,$invoice->total);
            }

            if ($data['paymentmode'] == 'paypal') {
                $data['returnUrl']   = site_url('getaways/paypal/complete_purchase?hash=' . $invoice->hash . '&invoiceid=' . $invoiceid);
                $data['cancelUrl']   = site_url('viewinvoice/' . $invoiceid . '/' . $invoice->hash);
                $data['currency']    = $invoice->currency_name;
                $data['description'] = 'Payment for invoice ' . format_invoice_number($invoice->id);
                $data['invoiceid']   = $invoiceid;

                $this->load->library('getaways/paypal_getaway');
                $this->paypal_getaway->make_purchase($data);
            } else if ($data['paymentmode'] == 'stripe') {
                redirect(site_url('getaways/stripe/make_payment?invoiceid=' . $invoiceid . '&total=' . $data['amount'] . '&hash=' . $invoice->hash));
            }
        }
        return false;
    }
    /**
     * Record new payment
     * @param array $data payment data
     * @return boolean
     */
    public function add($data)
    {

        // Check if field do not redirect to payment processor is set so we can unset from the database
        if (isset($data['do_not_redirect'])) {
            unset($data['do_not_redirect']);
        }

        if (is_staff_logged_in()) {
            $data['addedfrom'] = get_staff_user_id();
            if (isset($data['date'])) {
                $data['date'] = to_sql_date($data['date']);
            } else {
                $data['date'] = date('Y-m-d H:i:s');
            }
            if (isset($data['note'])) {
                $data['note'] = nl2br($data['note']);
            } else if ($this->session->has_userdata('payment_admin_note')) {
                $data['note'] = nl2br($this->session->userdata('payment_admin_note'));
                $this->session->unset_userdata('payment_admin_note');
            }
        } else {
            $data['date'] = date('Y-m-d H:i:s');
        }

        $data['daterecorded'] = date('Y-m-d H:i:s');

        $data = do_action('before_payment_recorded', $data);
        $this->db->insert('tblinvoicepaymentrecords', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            $invoice = $this->invoices_model->get($data['invoiceid']);
            update_invoice_status($data['invoiceid']);
            if (is_staff_logged_in()) {
                $this->invoices_model->log_invoice_activity($data['invoiceid'], get_staff_full_name() . ' recorded payment from total <b>' . format_money($data['amount'], $invoice->symbol) . '</b> - <a href="' . admin_url('payments/payment/' . $insert_id) . '" target="_blank">#' . $insert_id . '</a>');
            } else {
                $this->invoices_model->log_invoice_activity($data['invoiceid'], 'Client made payment for the invoice from total <b>' . format_money($data['amount'], $invoice->symbol) . '</b> - <a href="' . admin_url('payments/payment/' . $insert_id) . '" target="_blank">#' . $insert_id . '</a>', false, true);
            }

            logActivity('Payment Recorded [ID:' . $insert_id . ', Invoice Number: ' . format_invoice_number($invoice->id) . ', Total: ' . format_money($data['amount'], $invoice->symbol) . ']');


            // Send email to the client that the payment is recorded
            $this->load->model('emails_model');
            $payment               = $this->get($insert_id);
            $payment->invoice_data = $this->invoices_model->get($payment->invoiceid);
            $paymentpdf            = payment_pdf($payment);
            $attach                = $paymentpdf->Output(_l('payment') . '-' . $payment->paymentid . '.pdf', 'S');

            $this->emails_model->add_attachment(array(
                'attachment'=>$attach,
                'filename'=>_l('payment') . '-' . $payment->paymentid . '.pdf',
                'type'=>'application/pdf'
                ));

            $this->emails_model->send_email_template('invoice-payment-recorded', $invoice->client->email, $invoice->clientid, $invoice->id);


            return $insert_id;
        }

        return false;
    }

    /**
     * Update payment
     * @param  array $data payment data
     * @param  mixed $id   paymentid
     * @return boolean
     */
    public function update($data, $id)
    {

        $data['date'] = to_sql_date($data['date']);
        $data['note'] = nl2br($data['note']);
        $_data        = do_action('before_payment_updated', array(
            'data' => $data,
            'id' => $id
        ));

        $data = $_data['data'];

        $this->db->where('id', $id);
        $this->db->update('tblinvoicepaymentrecords', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Payment Updated [ID:' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete payment from database
     * @param  mixed $id paymentid
     * @return boolean
     */
    public function delete($id)
    {
        $current         = $this->get($id);
        $current_invoice = $this->invoices_model->get($current->invoiceid);
        $invoiceid       = $current->invoiceid;

        do_action('before_payment_deleted', array(
            'paymentid' => $id,
            'invoiceid' => $invoiceid
        ));
        $this->db->where('id', $id);
        $this->db->delete('tblinvoicepaymentrecords');

        if ($this->db->affected_rows() > 0) {
            update_invoice_status($invoiceid);
            $this->invoices_model->log_invoice_activity($invoiceid, get_staff_full_name() . ' deleted payment for the invoice. Payment #' . $current->paymentid . ', total amount: ' . format_money($current->amount, $current_invoice->symbol));
            logActivity('Payment Deleted [ID:' . $id . ', Invoice Number: ' . format_invoice_number($current->id) . ']');

            return true;
        }

        return false;
    }
}
