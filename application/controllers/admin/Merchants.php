<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/25/16
 * Time: 3:08 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Merchants extends Admin_controller
{
    function __construct(){
        parent::__construct();
        $this->load->model('merchants_model');
        $this->load->model('clients_model');
        $this->load->model('processors_model');
        $this->load->model('transactions_model');
    }

    /* Get all merchants in case user go on index page */
    public function index($id = false)
    {
        $this->list_merchants($id);
    }

    /* List all merchants datatables */
    public function list_merchants($id = false, $clientid = false)
    {

        if(!has_permission('manageMerchants')){
            access_denied('manageMerchants');
        }

        if($this->input->is_ajax_request()){
            $aColumns = array('userid','descriptor','live_mode','date_added');

            $sIndexColumn = "id";
            $sTable = 'tblmerchants';

            $result = data_tables_init($aColumns,$sIndexColumn,$sTable,array(),array(),array('id'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ( $rResult as $aRow )
            {
                $row = array();
                for ( $i=0 ; $i<count($aColumns) ; $i++ )
                {
                    if ($aColumns[$i] == 'userid') {

                        $client = $this->clients_model->get($aRow['userid']);

                        $_data = '<a href="'.admin_url('clients/client/'.$client->userid).'">'.$client->firstname . ' ' . $client->lastname.'</a>';
//                        $_data .= '<br /><span class="text-muted">'.$client->company.'</span>';
                    } else if ($aColumns[$i] == 'live_mode'){
                        $_data = format_merchant_status($aRow['live_mode']);
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    $row[] = $_data;
                }
                $options = icon_btn('admin/merchants/merchant/'.$aRow['id'],'pencil-square-o','btn-default');
                $row[]  = $options .= icon_btn('admin/merchants/delete/'.$aRow['id'],'remove','btn-danger');

                $output['aaData'][] = $row;
            }

            echo json_encode( $output );
            die();
        }

        $data['title']     = 'Merchants';
        $this->load->view('admin/merchants/manage', $data);

    }

    /* Edit client or add new client*/
    public function merchant($id = ''){

        if(!has_permission('manageMerchants')){
            access_denied('manageMerchants');
        }


        if($this->input->post() && !$this->input->is_ajax_request()){

            if($id == ''){
                $id = $this->merchants_model->add($this->input->post());
                if($id){
                    set_alert('success', _l('added_successfuly','Merchant'));
                    redirect(admin_url('merchants/merchant/'.$id));
                }
            } else {
                $success = $this->merchants_model->update($this->input->post(),$id);

                if($success == true){
                    set_alert('success', _l('updated_successfuly','Merchant'));
                }
                redirect(admin_url('merchants/merchant/'.$id));
            }
        }

        if($id == ''){
            $title = _l('add_new','merchant');
        } else {
            $merchant = $this->merchants_model->get($id);

            if(!$merchant){
                blank_page('Merchant Not Found');
            }

            $data['merchant'] = $merchant;

            $client = $this->clients_model->get($merchant->userid);

            $title = $client->firstname . ' ' .$client->lastname;

        }

        $this->load->model('clients_model');
        $data['clients'] = $this->clients_model->get();

        $this->load->model('affiliates_model');
        $data['affiliates'] = $this->affiliates_model->get();

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $this->load->model('processors_model');
        $data['processors'] = $this->processors_model->get();


        $data['title'] = $title;
        $this->load->view('admin/merchants/merchant',$data);
    }

    /* Delete client */
    public function delete($id){

        if(!has_permission('manageMerchants')){
            access_denied('manageMerchants');
        }

        if(!$id){
            redirect(admin_url('merchants'));
        }

        $response = $this->merchants_model->delete($id);

        if(is_array($response) && isset($response['referenced'])){
            set_alert('warning',_l('is_referenced',_l('merchant_lowercase')));
        } else if($response == true){
            set_alert('success', _l('deleted',_l('merchant')));
        } else {
            set_alert('warning', _l('problem_deleting',_l('merchant_lowercase')));
        }

        redirect(admin_url('merchants'));
    }

    public function processors($id=""){

        if($this->input->is_ajax_request()){
            $aColumns = array('alias','active');

            $sIndexColumn = "id";
            $sTable = 'tblmerchantprocessors';

            $result = data_tables_init($aColumns,$sIndexColumn,$sTable,array(),array(),array('id','processorid','merchantid'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ( $rResult as $aRow )
            {
                if ($aRow['merchantid'] != $id){continue;}

                $row = array();
                for ( $i=0 ; $i<count($aColumns) ; $i++ )
                {
                    if ($aColumns[$i] == 'active') {
                        $checked = '';
                        if($aRow['active'] == 1){
                            $checked = 'checked';
                        }
                        $_data = '<input type="checkbox" class="switch-box input-xs" data-size="mini" data-id="'.$aRow['id'].'" data-switch-url="admin/merchants/change_processor_status" '.$checked.'>';
                        // For exporting
                        $_data .=  '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) .'</span>';
                    } else {
                        $_data = $aRow[ $aColumns[$i] ];
                    }

                    $row[] = $_data;
                }
                $options = icon_btn('#','pencil-square-o','btn-default',array('data-toggle'=>'modal','data-target'=>'#merchant_processor_modal','data-id'=>$aRow['id'],'data-processor-id'=>$aRow['processorid']));
                $row[]  = $options .= icon_btn('#','remove','btn-danger',array('onclick'=>'deleteMerchantProcessor('.$aRow['id'].')','data-id'=>$aRow['id']));

                $output['aaData'][] = $row;
            }

            echo json_encode( $output );
            die();
        }
    }

    public function processor($id = ""){

        if($this->input->is_ajax_request()){
            $params = $this->input->post();

            $data = array(
                'processorid'=>$params['processorid'],
                'merchantid'=>$params['merchantid'],
                'alias'=>$params['alias'],
                'processor_data'=>json_encode($params['processor_data']),
                'mccCode'=>$params['mccCode'],
                'buyRate'=>$params['buyRate'],
                'saleRate'=>$params['saleRate'],
                'rollbackReserve'=>$params['rollbackReserve'],
                'processingLimit'=>$params['processingLimit'],
                'transactionLimit'=>$params['transactionLimit'],
                'transactionFee'=>$params['transactionFee'],
                'active'=> (int) $params['active'],
            );

            if(!$params['id']){
                $success = $this->merchants_model->add_merchant_processor($data);
                $message = '';
                if($success == true){
                    $message =  _l('added_successfuly','Processor');
                }
                echo json_encode(array('success'=>$success,'message'=>$message));
            } else {
                $success = $this->merchants_model->edit_merchant_processor($params);
                $message = '';
                if($success == true){
                    $message = _l('updated_successfuly','Processor');
                }
                echo json_encode(array('success'=>$success,'message'=>$message));
            }

        }

    }

    public function load_processor_form($processor_id,$id){

        $data['processor'] = $this->processors_model->get($processor_id);
        $data['merchant_processor'] = $this->merchants_model->get_merchant_processor($id);

        $this->load->view('admin/merchants/processor/'.$data['processor']->object, $data,false);
    }

    public function load_merchant_processor($merchant_processor_id){

        $json = array();

        $merchant_processor = $this->merchants_model->get_merchant_processor($merchant_processor_id);

        $json = array(
            'id'=>$merchant_processor->id,
            'processorid'=>$merchant_processor->processorid,
            'merchantid'=>$merchant_processor->merchantid,
            'alias'=>$merchant_processor->alias,
            'processor_data'=>json_decode($merchant_processor->processor_data),
            'mccCode'=>$merchant_processor->mccCode,
            'buyRate'=>($merchant_processor->buyRate),
            'saleRate'=>($merchant_processor->saleRate),
            'rollbackReserve'=>($merchant_processor->rollbackReserve),
            'processingLimit'=>($merchant_processor->processingLimit),
            'transactionLimit'=>($merchant_processor->transactionLimit),
            'transactionFee'=>($merchant_processor->transactionFee),
            'active'=>$merchant_processor->active,


        );

        echo json_encode($json);
        die();


    }

    public function delete_merchant_processor($id){

        if(!$id){
            redirect(admin_url('merchants'));
        }

        $response = $this->merchants_model->delete_merchant_processor($id);
        if(is_array($response) && isset($response['referenced'])){
            set_alert('warning',_l('is_referenced','processor'));
        } else if($response == true){
            $message = '';
            $message =  _l('deleted','Processor');
            echo json_encode(array('success'=>$response,'message'=>$message));
        } else {
            $message = '';
            $message =  _l('warning','Processor');
            echo json_encode(array('success'=>$response,'message'=>$message));
        }

    }

    /* Change client status / active / inactive */
    public function change_processor_status($id,$status){

        if(!has_permission('manageMerchants')){
            access_denied('manageMerchants');
        }

        if($this->input->is_ajax_request()){
            $this->merchants_model->change_processor_status($id,$status);
        }
    }

    public function transactions($id = ""){

        $transactions = $this->transactions_model->get();

        $merchant = $this->merchants_model->get($id);

        if($this->input->is_ajax_request()){

            foreach ($transactions as $transaction) {

                if ($id != $transaction['merchantid']){
                    continue;
                }

                $json['data'][] = array(
                    'id'=>$transaction['transactionid'],
                    'method'=>$transaction['method'],
                    'card'=>($transaction['card'] ? $transaction['card'] : 'N/A'),
                    'type'=>($transaction['type'] ? $transaction['type'] : 'N/A'),
                    'settlement'=>format_money($transaction['settlement'],'USD'),
                    'status'=>format_trx_status($transaction['status']),
                    'date_added'=>$transaction['date_added'],
                );

            }
            echo json_encode( $json );
            die();
        }
    }

    public function transaction($id = ""){

        if ($this->input->is_ajax_request()) {

            $transaction = $this->transactions_model->gettrx($id);

            $data['transaction'] = $transaction;

            $this->load->view('admin/merchants/transaction', $data, false);
        }
    }

    public function api_access(){

        if($this->input->is_ajax_request()) {

            $this->load->helper('semite_uuid');

            echo json_encode(array(
                    'api_id'=>strtoupper(UUID::random_key(9,true,true)),
                    'secret_key'=>strtoupper(UUID::v5('1546058f-5a25-4334-85ae-e68f2a44bbaf',UUID::random_key(9))))
            );
        }

    }



}