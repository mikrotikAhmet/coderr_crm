<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Terminal extends Clients_controller
{

    function __construct()
    {
        parent::__construct();
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-validation">', '</div>');
        $this->load->helper('credit_card_validator');
    }

    public function index(){

        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        $this->db->where('userid',get_client_user_id());
        $merchant = $this->db->get('tblmerchants')->row();

        $this->db->where('merchantid',$merchant->id);
        $this->db->where('active',1);
        $merchantProcessorData = $this->db->get('tblmerchantprocessors')->row();

        $merchantProcessor = json_decode($merchantProcessorData->processor_data);

        $data['merchant'] = $merchant;
        $data['merchantProcessor'] = $merchantProcessor;

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
  <memberId>'.$merchant->api_id.'</memberId>
  <memberGuid>'.$merchant->secret_key.'</memberGuid>
  <method>'.$postData['method'].'</method>
  <countryId>'.$postData['countryId'].'</countryId>
  <amount>'.$postData['amount'].'</amount>
  <currencyId>'.$postData['currencyId'].'</currencyId>
  <trackingMemberCode>'.$postData['trackingMemberCode'].'</trackingMemberCode>
  <cardNumber>'.$postData['creditCard']['cardNumber'].'</cardNumber>
  <cardholder>'.(isset($postData['creditCard']['cardholder']) ? $postData['creditCard']['cardholder'] : null).'</cardholder>
  <cardExpiryMonth>'.$postData['creditCard']['cardExpiryMonth'].'</cardExpiryMonth>
  <cardExpiryYear>'.$postData['creditCard']['cardExpiryYear'].'</cardExpiryYear>
  <cardCvv>'.$postData['creditCard']['cardCvv'].'</cardCvv>
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
                    curl_error($ch);
                }
                else
                {
                    curl_close($ch);
                    echo $res;
                    die();
                }
        }
    }

    public function secure_process(){

        if ($this->input->is_ajax_request()) {
            $json = array();

            $this->db->where('userid',get_client_user_id());
            $merchant = $this->db->get('tblmerchants')->row();

            $postData = $this->input->post();

            if ($merchant->live_mode) {
                $url = _LIVE_URL;
            } else {
                $url = _TEST_URL;
            }


            $post_string = '<?xml version="1.0" encoding="UTF-8"?>
<request>
  <memberId>'.$merchant->api_id.'</memberId>
  <memberGuid>'.$merchant->secret_key.'</memberGuid>
  <method>CheckEnrollment</method>
  <countryId>'.$postData['countryId'].'</countryId>
  <amount>'.$postData['amount'].'</amount>
  <currencyId>'.$postData['currencyId'].'</currencyId>
  <trackingMemberCode>'.$postData['trackingMemberCode'].'</trackingMemberCode>
  <cardNumber>'.$postData['creditCard']['cardNumber'].'</cardNumber>
  <cardholder>'.(isset($postData['creditCard']['cardholder']) ? $postData['creditCard']['cardholder'] : null).'</cardholder>
  <cardExpiryMonth>'.$postData['creditCard']['cardExpiryMonth'].'</cardExpiryMonth>
  <cardExpiryYear>'.$postData['creditCard']['cardExpiryYear'].'</cardExpiryYear>
  <cardCvv>'.$postData['creditCard']['cardCvv'].'</cardCvv>
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
                echo $res;
            }

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

    public function result($transaction_id,$secure = 0){


        $this->db->where('transactionid',$transaction_id);
        $transaction = $this->db->get('tbltransactions')->row();


        $this->db->where('transactionid',$transaction->id);
        $this->db->where('type','response');
        $webhookResponse = $this->db->get('tblwebhooks')->row();

        $hookdataResponse = (array) json_decode($webhookResponse->hookdata);

        $this->db->where('transactionid',$transaction->id);
        $this->db->where('type','request');
        $webhookRequest = $this->db->get('tblwebhooks')->row();

        $hookdataRequest = (array) json_decode($webhookRequest->hookdata);

        $data['webhookresponse'] = $hookdataResponse;
        $data['webhookrequest'] = $hookdataRequest;

        $data['secure'] = $secure;

        if ($this->input->is_ajax_request() && !$secure){

            if ($transaction->status == 0){

                $this->load->view('themes/semite/views/success', $data, false);
            }

            if ($transaction->status == 1){

                if (isset($hookdataResponse['Cdc']->ErrorMessage)){ // Payvision

                    $data['reason'] =$hookdataResponse['Cdc']->ErrorMessage;
                } else if ($hookdataResponse['Cdc']->PROCESSING_RETURN){ // Noirepay
                    $data['reason'] = $hookdataResponse['Cdc']->PROCESSING_RETURN;
                }

                $this->load->view('themes/semite/views/failed', $data, false);
            }

        } elseif (!$this->input->is_ajax_request()) {

            if ($transaction->status == 0){

                $data['title'] = get_option('companyname').'- Virtual Terminal';
                $this->data    = $data;
                $this->view    = 'success';
                $this->layout();
            }

            if ($transaction->status == 1){

                $data['title'] = get_option('companyname').'- Virtual Terminal';
                $this->data    = $data;
                $this->view    = 'failed';
                $this->layout();
            }

        }
    }

    public function failedauthentication($secure){


        $data['webhookresponse']['SemiteId'] = '00000000';
        $data['webhookresponse']['SemiteGuid'] = '00000000-0000-0000-0000-00000000000';
        $data['webhookrequest']['TrackingMemberCode'] = 'N/A';

        $data['reason'] = '3D-Secure Authentication failed!';

        $data['secure'] = $secure;

        $data['title'] = get_option('companyname').'- Virtual Terminal';
        $this->data    = $data;
        $this->view    = 'failed';
        $this->layout();

    }

}