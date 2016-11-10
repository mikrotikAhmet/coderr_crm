<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Terminal extends Clients_controller
{

    function __construct()
    {
        parent::__construct();
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-validation">', '</div>');

    }

    public function index(){

        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        $this->db->where('userid',get_client_user_id());
        $merchant = $this->db->get('tblmerchants')->row();

        $data['merchant'] = $merchant;

        $data['title'] = get_option('companyname').'- Virtual Terminal';
        $this->data    = $data;
        $this->view    = 'terminal';
        $this->layout();
    }

    public function process(){

        if ($this->input->is_ajax_request()) {
            $json = array();

            $this->db->where('userid', get_client_user_id());
            $merchant = $this->db->get('tblmerchants')->row();

            $postData = $this->input->post();

            if ($merchant->live_mode) {
                $url = _LIVE_URL;
            } else {
                $url = _TEST_URL;
            }

            $post_string = '<?xml version="1.0" encoding="UTF-8"?>
<request>
   <authentication>
      <api_id>'.$merchant->api_id.'</api_id>
      <secret_key>'.$merchant->secret_key.'</secret_key>
   </authentication>
   <type>'.$postData['type'].'</type>
   <processor>'.$merchant->default_processor.'</processor>
  <countryId>'.$postData['countryId'].'</countryId>
 <amount>'.$postData['amount'].'</amount>
 <currencyId>'.$postData['currencyId'].'</currencyId>
 <trackingMemberCode>'.$postData['trackingMemberCode'].'</trackingMemberCode>
 <creditCard>
 <cardNumber>'.$postData['creditCard']['cardNumber'].'</cardNumber>
 <cardholder>'.(isset($postData['creditCard']['cardholder']) ? $postData['creditCard']['cardholder'] : null).'</cardholder>
 <cardExpiryMonth>'.$postData['creditCard']['cardExpiryMonth'].'</cardExpiryMonth>
 <cardExpiryYear>'.$postData['creditCard']['cardExpiryYear'].'</cardExpiryYear>
 <cardCvv>'.$postData['creditCard']['cardCvv'].'</cardCvv>
 </creditCard>
 <merchantAccountType>1</merchantAccountType>
 <dbaName></dbaName>
 <dbaCity></dbaCity>
 <avsAddress></avsAddress>
 <avsZip></avsZip>
 <additionalInfo>'.json_encode($postData['additionalInfo']).'</additionalInfo>
</request>';

                $postfields = $post_string;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);


                $res = curl_exec($ch);

                if(curl_errno($ch))
                {
                    echo curl_error($ch);
                }
                else
                {
                    curl_close($ch);
                    $response = $arrayToXml->toArray($res);
                }


            $json['response_code'] = (isset($response['error']) ? $response['error'] : $response['response_code']);
            $json['transaction_id'] = (isset($response['TransactionId']) ? $response['TransactionId'] : date('YmdHis'));

            echo json_encode($json);
            die();
        }
    }

    public function threeds_process(){

        if ($this->input->is_ajax_request()) {
            $json = array();

            $this->db->where('userid',get_client_user_id());
            $merchant = $this->db->get('tblmerchants')->row();

            $postData = $this->input->post();

            $arrayToXml = new ArrayToXML();

            if ($merchant->live_mode) {
                $url = _LIVE_URL;
            } else {
                $url = _TEST_URL;
            }

            $post_string = '<?xml version="1.0" encoding="UTF-8"?>
<request>
   <authentication>
      <api_id>'.$merchant->api_id.'</api_id>
      <secret_key>'.$merchant->secret_key.'</secret_key>
   </authentication>
   <type>CheckEnrollment</type>
   <processor>'.$merchant->default_processor.'</processor>
  <countryId>'.$postData['countryId'].'</countryId>
 <amount>'.$postData['amount'].'</amount>
 <currencyId>'.$postData['currencyId'].'</currencyId>
 <trackingMemberCode>'.$postData['trackingMemberCode'].'</trackingMemberCode>
 <creditCard>
 <cardNumber>'.$postData['creditCard']['cardNumber'].'</cardNumber>
 <cardholder>'.(isset($postData['creditCard']['cardHolder']) ? $postData['creditCard']['cardHolder'] : null).'</cardholder>
 <cardExpiryMonth>'.$postData['creditCard']['cardExpiryMonth'].'</cardExpiryMonth>
 <cardExpiryYear>'.$postData['creditCard']['cardExpiryYear'].'</cardExpiryYear>
 <cardCvv>'.$postData['creditCard']['cardCvv'].'</cardCvv>
 </creditCard>
 <merchantAccountType>1</merchantAccountType>
 <dbaName></dbaName>
 <dbaCity></dbaCity>
 <avsAddress></avsAddress>
 <avsZip></avsZip>
 <additionalInfo>'.json_encode($postData['additionalInfo']).'</additionalInfo>
</request>';

            $postfields = $post_string;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);


            $res = curl_exec($ch);

            if(curl_errno($ch))
            {
                echo curl_error($ch);
            }
            else
            {
                curl_close($ch);
                $response = $arrayToXml->toArray($res);
            }

            $json = $response;

            echo json_encode($json);
            die();
        }

    }

    public function validatepayment(){

        if ($this->input->is_ajax_request()) {
            $json = array();

            $postData = $this->input->post();

            if (isset($postData['cardnumber'])) {

                $cardValidator = new CreditCardValidator();

                $cardValidator->Validate($this->input->post('cardnumber'));

                $json = $cardValidator->GetCardInfo();
            }
            echo json_encode($json);
            die();
        }

    }

    public function result($response_code,$transaction_id,$threeds = 0){


        if ($this->input->is_ajax_request() && !$threeds) {

            if ($response_code == 1){

                $this->db->where('transactionid',$transaction_id);
                $transaction = $this->db->get('tbltransactions')->row();

                $data['transaction'] = $transaction;
                $data['additionalInfo'] = json_decode($transaction->additionalInfo);

                $this->load->view('themes/semite/views/success', $data, false);

            } elseif ($response_code == 2) {

                $this->db->where('transactionid',$transaction_id);
                $transactionAuthorization = $this->db->get('tbltransactionauthorization')->row();

                $cdc = json_decode($transactionAuthorization->response);

                if (isset($cdc->result_cdc_data->ErrorMessage)){

                    $data['reason'] = $cdc->result_cdc_data->ErrorMessage;

                } elseif (isset($cdc->result_cdc_data->Result->ResultDetail)){

                    $data['reason'] = $cdc->result_cdc_data->Result->ResultDetail;
                }

                $this->load->view('themes/semite/views/failed', $data, false);
            } else {

                $data['reason'] = $this->response->Error($response_code);

                $this->load->view('themes/semite/views/failed', $data, false);
            }

        } elseif (!$this->input->is_ajax_request() && $threeds) {


            $data['threeds'] = $threeds;

            if ($response_code == 1){

                $this->db->where('transactionid',$transaction_id);
                $transaction = $this->db->get('tbltransactions')->row();

                $data['transaction'] = $transaction;
                $data['additionalInfo'] = json_decode($transaction->additionalInfo);

                $data['title'] = get_option('companyname').'- Virtual Terminal';
                $this->data    = $data;
                $this->view    = 'success';
                $this->layout();

            } elseif ($response_code == 2) {

                if ($transaction_id != '3dsfail') {

                    $this->db->where('transactionid', $transaction_id);
                    $transactionAuthorization = $this->db->get('tbltransactionauthorization')->row();

                    $cdc = json_decode($transactionAuthorization->response);

                    if (isset($cdc->result_cdc_data->ErrorMessage)) {

                        $data['reason'] = $cdc->result_cdc_data->ErrorMessage;

                        $data['title'] = get_option('companyname').'- Virtual Terminal';
                        $this->data    = $data;
                        $this->view    = 'failed';
                        $this->layout();
                    }
                } else {

                    $data['reason'] = '3D-Secure Authentication Failed!';

                    $data['title'] = get_option('companyname').'- Virtual Terminal';
                    $this->data    = $data;
                    $this->view    = 'failed';
                    $this->layout();
                }
            } else {

                    $data['reason'] = $this->response->Error($response_code);

                $data['title'] = get_option('companyname').'- Virtual Terminal';
                $this->data    = $data;
                $this->view    = 'failed';
                $this->layout();

            }

        }

    }

}