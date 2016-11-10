<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paymentmodes extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('payment_modes_model');
        if(!has_permission('manageSales')){
            access_denied('manageSales');
        }
    }

    /* List all peyment modes*/
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns     = array(
                'name',
                'description',
                'active',
            );
            $sIndexColumn = "id";
            $sTable       = 'tblinvoicepaymentsmodes';

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), array(), array(
                'id'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];

                    if($aColumns[$i] == 'active'){
                        $checked = '';
                        if($aRow['active'] == 1){
                            $checked = 'checked';
                        }
                        $_data = '<input type="checkbox" class="switch-box input-xs" data-size="mini" data-id="'.$aRow['id'].'" data-switch-url="admin/paymentmodes/change_payment_mode_status" '.$checked.'>';
                        // For exporting
                        $_data .=  '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) .'</span>';
                    }

                    $row[] = $_data;
                }

                $options = icon_btn('#' . $aRow['id'], 'pencil-square-o', 'btn-default', array(
                    'data-toggle' => 'modal',
                    'data-target' => '#payment_mode_modal',
                    'data-id' => $aRow['id']
                ));
                $row[]   = $options .= icon_btn('admin/paymentmodes/delete/' . $aRow['id'], 'remove', 'btn-danger');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('payment_modes');
        $this->load->view('admin/paymentmodes/manage', $data);
    }
    /* Add or update payment mode / ajax */
    public function manage()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['paymentmodeid'] == '') {
                $message = '';
                $success = $this->payment_modes_model->add($data);
                if($success) {
                    $message = _l('added_successfuly',_l('payment_mode'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message'=>$message
                ));
            } else {
                $message = '';
                $success = $this->payment_modes_model->edit($data);
                if($success){
                    $message = _l('updated_successfuly',_l('payment_mode'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message'=>$message
                ));
            }
        }
    }

    /* Delete payment mode */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('paymentmodes'));
        }

        $response = $this->payment_modes_model->delete($id);
       if(is_array($response) && isset($response['referenced'])){
            set_alert('warning',_l('is_referenced',_l('payment_mode_lowercase')));
        } else if($response == true){
            set_alert('success', _l('deleted',_l('payment_mode')));
        } else {
            set_alert('warning', _l('problem_deleting',_l('payment_mode_lowercase')));
        }

        redirect(admin_url('paymentmodes'));
    }
    // Since version 1.0.1
    // Change payment mode active or inactive
    public function change_payment_mode_status($id,$status){

        if(!has_permission('manageSales')){
            access_denied('manageSales');
        }
        if($this->input->is_ajax_request()){
            $this->payment_modes_model->change_payment_mode_status($id,$status);
        }
    }
     // Since version 1.0.1
    // Change to show this mode to client or not
    public function change_payment_mode_show_to_client_status($id,$status){

        if(!has_permission('manageSales')){
            access_denied('manageSales');
        }
        if($this->input->is_ajax_request()){
            $this->payment_modes_model->change_payment_mode_show_to_client_status($id,$status);
        }
    }
}
