<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/16/16
 * Time: 12:50 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions extends Clients_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('merchants_model');
        $this->load->model('clients_model');
        $this->load->model('transactions_model');

        $this->load->library('currency');
    }

    public function index()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        if(!has_customer_permission('gateway')){
            redirect(site_url());
        }

        $this->db->where('merchantid',get_merchant_id());
        $data['transactions'] = $transactions = $this->db->get('tbltransactions')->result_array();

        $client =  $this->clients_model->get(get_client_user_id());

        if ($this->input->is_ajax_request()) {

            $json = array();

            $this->currency = new Currency();

            if ($transactions){

                foreach ($transactions as $transaction) {

                    $json['data'][] = array(
                        'transactionid'=>$transaction['transactionid'],
                        'type'=>$transaction['method'],
                        'cardMask'=>$transaction['card'],
                        'cardType'=>$transaction['type'],
                        'amount'=>format_money($transaction['settlement'],$this->currency->getNameById($client->default_currency)),
                        'status'=>format_trx_status($transaction['status']),
                        '3ds'=>($transaction['enrolled'] ? ' <i class="text-success fa fa-lock"></i>' : ' <i class="text-warning fa fa-unlock"></i>'),
                        'date_added'=>date('m/d/Y',strtotime($transaction['date_added'])),
                    );
                }
            }


            echo json_encode($json);
            die();
        }

        $data['title'] = get_option('companyname');
        $this->data    = $data;
        $this->view    = 'transactions';
        $this->layout();
    }

    public function transaction($id = ""){

        if ($this->input->is_ajax_request()) {

            $data['currency'] = new Currency();

            $transaction = $this->transactions_model->gettrx($id);
            $data['client'] = $this->clients_model->get(get_client_user_id());

            $this->db->where('merchantid',$transaction->merchantid);
            $merchantProcessorData = $this->db->get('tblmerchantprocessors')->row();

            $merchantProcessor = json_decode($merchantProcessorData->processor_data);


            $data['useOriginal'] = $merchantProcessor->use_original;

            $data['transaction'] = $transaction;

            $this->load->view('themes/semite/views/transaction', $data, false);
        }
    }

    public function refund($id){

        $this->db->where('transactionid',$id);
        $transaction = $this->db->get('tbltransactions')->row();

        $this->db->where('userid',get_client_user_id());
        $merchant = $this->db->get('tblmerchants')->row();


        if ($merchant->live_mode){
            $url = _LIVE_URL;
        } else {
            $url = _TEST_URL;
        }

        $post_string = '<?xml version="1.0" encoding="UTF-8"?>
<request>
  <memberId>'.$merchant->api_id.'</memberId>
  <memberGuid>'.$merchant->secret_key.'</memberGuid>
  <method>refund</method>
  <transactionId>'.$transaction->transactionid.'</transactionId>
  <transactionGuid>'.$transaction->transactionguid.'</transactionGuid>
  <amount>'.$transaction->amount.'</amount>
  <currencyId>'.$transaction->currency.'</currencyId>
  <countryId>USA</countryId>
  <trackingMemberCode>Refund '.date('Ymd His').'</trackingMemberCode>
  <additionalInfo></additionalInfo>
</request>';

        $postfields = $post_string;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);


        $data = curl_exec($ch);

        if(curl_errno($ch))
        {
            echo curl_error($ch);
        }
        else
        {
            curl_close($ch);

            echo $data;
            die();
        }

    }

    public function capture($id){

        $this->db->where('transactionid',$id);
        $transaction = $this->db->get('tbltransactions')->row();

        $this->db->where('userid',get_client_user_id());
        $merchant = $this->db->get('tblmerchants')->row();


        if ($merchant->live_mode){
            $url = _LIVE_URL;
        } else {
            $url = _TEST_URL;
        }

        $post_string = '<?xml version="1.0" encoding="UTF-8"?>
<request>
  <memberId>'.$merchant->api_id.'</memberId>
  <memberGuid>'.$merchant->secret_key.'</memberGuid>
  <method>capture</method>
  <transactionId>'.$transaction->transactionid.'</transactionId>
  <transactionGuid>'.$transaction->transactionguid.'</transactionGuid>
  <amount>'.$transaction->amount.'</amount>
  <currencyId>'.$transaction->currency.'</currencyId>
  <countryId>USA</countryId>
  <trackingMemberCode>Refund '.date('Ymd His').'</trackingMemberCode>
  <additionalInfo></additionalInfo>
</request>';

        $postfields = $post_string;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);


        $data = curl_exec($ch);

        if(curl_errno($ch))
        {
            echo curl_error($ch);
        }
        else
        {
            curl_close($ch);

            echo $data;
            die();
        }

    }

    public function void($id){

        $this->db->where('transactionid',$id);
        $transaction = $this->db->get('tbltransactions')->row();

        $this->db->where('userid',get_client_user_id());
        $merchant = $this->db->get('tblmerchants')->row();


        if ($merchant->live_mode){
            $url = _LIVE_URL;
        } else {
            $url = _TEST_URL;
        }

        $post_string = '<?xml version="1.0" encoding="UTF-8"?>
<request>
  <memberId>'.$merchant->api_id.'</memberId>
  <memberGuid>'.$merchant->secret_key.'</memberGuid>
  <method>void</method>
  <transactionId>'.$transaction->transactionid.'</transactionId>
  <transactionGuid>'.$transaction->transactionguid.'</transactionGuid>
  <amount>'.$transaction->amount.'</amount>
  <currencyId>'.$transaction->currency.'</currencyId>
  <countryId>USA</countryId>
  <trackingMemberCode>Refund '.date('Ymd His').'</trackingMemberCode>
  <additionalInfo></additionalInfo>
</request>';

        $postfields = $post_string;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);


        $data = curl_exec($ch);

        if(curl_errno($ch))
        {
            echo curl_error($ch);
        }
        else
        {
            curl_close($ch);

            echo $data;
            die();
        }

    }

}