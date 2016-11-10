<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stripe extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('getaways/stripe_getaway');
    }

    public function complete_purchase()
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            $hash      = $this->input->get('hash');
            $invoiceid = $this->input->get('invoiceid');
            $total     = $this->input->get('total');

            check_invoice_restrictions($invoiceid, $hash);

            $this->load->model('invoices_model');
            $invoice             = $this->invoices_model->get($invoiceid);
            $data['amount']      = $total;
            $data['description'] = 'Payment for invoice ' . format_invoice_number($invoice->id);
            $data['currency']    = $invoice->currency_name;
            $data['clientid']    = $invoice->clientid;
            $stripeResponse      = $this->stripe_getaway->make_purchase($data);

            if (is_object($stripeResponse)) {
                $transactionid  = $stripeResponse->getTransactionReference();
                $stripeResponse = $stripeResponse->getData();
                if ($stripeResponse['status'] == 'succeeded') {

                    // Add payment to database
                    $payment_data['amount']        = $total;
                    $payment_data['invoiceid']     = $invoiceid;
                    $payment_data['paymentmode']   = 'stripe';
                    $payment_data['transactionid'] = $transactionid;

                    $this->load->model('payments_model');
                    $success = $this->payments_model->add($payment_data);

                    if ($success) {
                        set_alert('success', _l('online_payment_recorded_success'));
                    } else {
                        set_alert('danger', _l('online_payment_recorded_success_fail_database'));
                    }
                    redirect(site_url('viewinvoice/' . $invoiceid . '/' . $hash));

                }
            } else if (is_string($stripeResponse)) {
                // Error
                set_alert('danger', $stripeResponse);
                redirect(site_url('viewinvoice/' . $invoiceid . '/' . $hash));
            }

        }
    }

    public function make_payment()
    {

        $form      = '';
        $hash      = $this->input->get('hash');
        $invoiceid = $this->input->get('invoiceid');
        check_invoice_restrictions($invoiceid, $hash);

        $this->load->model('invoices_model');
        $invoice      = $this->invoices_model->get($invoiceid);
        $total        = $this->input->get('total');
        $stripe_total = $total * 100;
        $form .= '
        <form action="' . site_url('getaways/stripe/complete_purchase?invoiceid=' . $invoiceid . '&hash=' . $hash . '&total=' . $total . '&stripe_total=' . $stripe_total) . '" method="POST">
            <script
            src="https://checkout.stripe.com/checkout.js" class="stripe-button"
            data-key="' . get_option('paymentmethod_stripe_api_publishable_key') . '"
            data-amount="' . $stripe_total . '"
            data-name="' . get_option('companyname') . '"
            data-description="Payment for invoice ' . format_invoice_number($invoice->id) . '";
            data-locale="auto">
        </script>
    </form>';

        $data['stripe_form']  = $form;
        $data['invoice']      = $invoice;
        $data['total']        = $total;
        $data['stripe_total'] = $total;
        $this->load->view('themes/' . active_clients_theme() . '/views/stripe_payment', $data);

    }
}
