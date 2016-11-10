<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Currencies extends Admin_controller {

    function __construct(){
        parent::__construct();
        $this->load->model('currencies_model');
        if(!has_permission('manageSales')){
            access_denied('manageSales');
        }
    }


    /* List all currencies */
    public function index()
    {
        if($this->input->is_ajax_request()){
            $aColumns = array( 'name','symbol','value');
            $sIndexColumn = "id";
            $sTable = 'tblcurrencies';

            $result = data_tables_init($aColumns,$sIndexColumn,$sTable,array(),array(),array('id','isdefault','decimal_place'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ( $rResult as $aRow )
            {
                $row = array();
                for ( $i=0 ; $i<count($aColumns) ; $i++ )
                {
                    $_data = $aRow[ $aColumns[$i] ];
                    if($aColumns[$i] == 'name'){
                        $_data = '<span class="name">' . $_data . '</span>';
                        if($aRow['isdefault'] == 1){
                            $_data .= '<span class="display-block text-info">'._l('base_currency_string').'</span>';
                        }
                    }

                    $row[] = $_data;
                }

                $options = icon_btn('#'.$aRow['id'],'pencil-square-o','btn-default',array('data-toggle'=>'modal','data-target'=>'#currency_modal','data-id'=>$aRow['id'],'data-decimal'=>$aRow['decimal_place']));
                if($aRow['isdefault'] == 0){
                    $options .= icon_btn('admin/currencies/make_base_currency/'.$aRow['id'],'star','btn-primary',array('data-toggle'=>'tooltip','title'=>_l('make_base_currency')));
                }
                $row[]  = $options .= icon_btn('admin/currencies/delete/'.$aRow['id'],'remove','btn-danger');

                $output['aaData'][] = $row;
            }

            echo json_encode( $output );
            die();
        }

        $data['title'] = _l('currencies');
        $this->load->view('admin/currencies/manage',$data);
    }

    /* Update currency or add new / ajax */
    public function manage(){
        if($this->input->post()){
            $data = $this->input->post();
            if($data['currencyid'] == ''){
                $success = $this->currencies_model->add($data);
                $message = '';
                if($success == true){
                    $message =  _l('added_successfuly',_l('currency'));
                }
                echo json_encode(array('success'=>$success,'message'=>$message));
            } else {
                $success = $this->currencies_model->edit($data);
                $message = '';
                if($success == true){
                    $message = _l('updated_successfuly',_l('currency'));
                }
                echo json_encode(array('success'=>$success,'message'=>$message));
            }
        }
    }

    /* Make currency your base currency */
    public function make_base_currency($id){

        if(!$id){
            redirect(admin_url('currencies'));
        }

        $response = $this->currencies_model->make_base_currency($id);
        if($response == true){
            set_alert('success',_l('base_currency_set'));
        }

        redirect(admin_url('currencies'));
    }

    /* Update currencies*/
    public function update_currency(){

        $defaultCurrency = get_default_currency();

        $response = $this->currencies_model->make_base_currency($defaultCurrency->id);
        if($response == true){
            set_alert('success',_l('base_currency_set'));
        }

        redirect(admin_url('currencies'));
    }

    /* Delete currency from database */
    public function delete($id){

        if(!$id){
            redirect(admin_url('currencies'));
        }

        $response = $this->currencies_model->delete($id);
        if(is_array($response) && isset($response['referenced'])){
            set_alert('warning',_l('is_referenced',_l('currency_lowercase')));
        } else if(is_array($response) && isset($response['is_default'])){
            set_alert('warning',_l('cant_delete_base_currency'));
        } else if($response == true){
            set_alert('success', _l('deleted',_l('currency')));
        } else {
            set_alert('warning', _l('problem_deleting',_l('currency_lowercase')));
        }

        redirect(admin_url('currencies'));
    }

    /* Get symbol by currency id passed */
    public function get_currency_symbol($id)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode(array(
                'symbol' => $this->currencies_model->get_currency_symbol($id)
            ));
        }
    }

}
