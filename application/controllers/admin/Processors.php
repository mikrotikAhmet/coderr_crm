<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/25/16
 * Time: 12:31 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Processors extends Admin_controller
{
    function __construct(){
        parent::__construct();
        $this->load->model('processors_model');
    }

    /* List all affiliates */
    public function index()
    {
        if($this->input->is_ajax_request()){
            $aColumns = array('name','status');

            $sIndexColumn = "id";
            $sTable = 'tblprocessors';

            $result = data_tables_init($aColumns,$sIndexColumn,$sTable,array(),array(),array('id','object'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ( $rResult as $aRow )
            {
                $row = array();
                for ( $i=0 ; $i<count($aColumns) ; $i++ )
                {

                    if($aColumns[$i]  == 'status'){
                        $_data = format_processor_status($aRow['status']);
                    } else {
                        $_data = $aRow[ $aColumns[$i] ];
                    }
                    $row[] = $_data;
                }
                $options = icon_btn('#','pencil-square-o','btn-default',array('data-toggle'=>'modal','data-target'=>'#processor_modal','data-id'=>$aRow['id'],'data-object'=>$aRow['object'],'data-status'=>$aRow['status']));
                $row[]  = $options .= icon_btn('admin/processors/delete_processor/'.$aRow['id'],'remove','btn-danger');

                $output['aaData'][] = $row;
            }

            echo json_encode( $output );
            die();
        }

        $data['title'] = 'Processors';
        $this->load->view('admin/processors/manage_processor',$data);
    }

    public function delete_processor($id){

        if(!$id){
            redirect(admin_url('processors'));
        }

        $response = $this->processors_model->delete_processor($id);
        if(is_array($response) && isset($response['referenced'])){
            set_alert('warning',_l('is_referenced','Processor'));
        } else if($response == true){
            set_alert('success', _l('deleted',_l('affiliate_group')));
        } else {
            set_alert('warning', _l('problem_deleting','Processor'));
        }

        redirect(admin_url('processors'));

    }

    public function processor(){
        if($this->input->is_ajax_request()){

            $data = $this->input->post();

            if (isset($data['status'])){

                $data['status'] = 1;
            } else {
                $data['status'] = 0;
            }


            if($data['id'] == ''){

                unset($data['id']);

                $success = $this->processors_model->add_processor($data);
                $message = '';
                if($success == true){
                    $message =  _l('added_successfuly','Processor');
                }
                echo json_encode(array('success'=>$success,'message'=>$message));
            } else {
                $success = $this->processors_model->edit_processor($data);
                $message = '';
                if($success == true){
                    $message = _l('updated_successfuly','Processor');
                }
                echo json_encode(array('success'=>$success,'message'=>$message));
            }
        }
    }

}