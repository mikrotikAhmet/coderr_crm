<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Omnipay\Omnipay;
require_once(APPPATH .'third_party/omnipay/vendor/autoload.php');
class Stripe_getaway
{
    function __construct(){
      $this->ci =& get_instance();
    }
    public function make_purchase($data){

        // Process online for PayPal payment start
        $gateway = Omnipay::create('Stripe');
        $gateway->setApiKey(get_option('paymentmethod_stripe_api_secret_key'));

        $response = $gateway->purchase(
          array(
            'amount' => number_format($data['amount'],2,'.',''),
            'metadata'=>array('ClientID'=>$data['clientid']),
            'description'=>$data['description'],
            'currency' => $data['currency'],
            'token' => $data['stripeToken'])
          )->send();

        if ($response->isSuccessful()) {
             return $response;
          } elseif ($response->isRedirect()) {
              $response->redirect();
          } else {
              return $response->getMessage();
          }
      }
}
