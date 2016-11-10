<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Taxes extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('taxes_model');
    }

    /* List all taxes */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns     = array(
                'name',
                'taxrate'
            );
            $sIndexColumn = "id";
            $sTable       = 'tbltaxes';

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), array(), array(
                'id'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    $row[] = $_data;
                }

                $options = icon_btn('#' . $aRow['id'], 'pencil-square-o', 'btn-default', array(
                    'data-toggle' => 'modal',
                    'data-target' => '#tax_modal',
                    'data-id' => $aRow['id']
                ));
                $row[]   = $options .= icon_btn('admin/taxes/delete/' . $aRow['id'], 'remove', 'btn-danger');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('taxes');
        $this->load->view('admin/taxes/manage', $data);
    }
    /* Add or edit tax / ajax */
    public function manage()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['taxid'] == '') {
                $success = $this->taxes_model->add($data);
                $message = '';
                if($success == true){
                    $message =  _l('added_successfuly',_l('tax'));
                }
                echo json_encode(array('success'=>$success,'message'=>$message));
            } else {
                $success = $this->taxes_model->edit($data);
                $message = '';
                if($success == true){
                    $message = _l('updated_successfuly',_l('currency'));
                }
                echo json_encode(array('success'=>$success,'message'=>$message));
            }
        }
    }
    /* Delete tax from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('taxes'));
        }

        $response = $this->taxes_model->delete($id);
       if(is_array($response) && isset($response['referenced'])){
            set_alert('warning',_l('is_referenced',_l('tax_lowercase')));
        } else if($response == true){
            set_alert('success', _l('deleted',_l('tax')));
        } else {
            set_alert('warning', _l('problem_deleting',_l('tax_lowercase')));
        }
        redirect(admin_url('taxes'));
    }
}
