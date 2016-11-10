<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Departments extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('departments_model');
        if (!has_permission('manageDepartments')) {
            access_denied('manageDepartments');
        }
    }

    /* List all departments */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns     = array(
                'name',
                'email',
                'calendar_id',
            );
            $sIndexColumn = "departmentid";
            $sTable       = 'tbldepartments';

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable,array(),array(),array('departmentid','email','hidefromclient'));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name') {
                        $_data = '<a href="' . admin_url('departments/department/' . $aRow['departmentid']) . '">' . $_data . '</a>';
                    }
                    $row[] = $_data;
                }

                $options = icon_btn('admin/departments/department/' . $aRow['departmentid'], 'pencil-square-o','btn-default',array(
                    'onclick'=>'edit_department(this,'.$aRow['departmentid'].'); return false','data-name'=>$aRow['name'],'data-calendar-id'=>$aRow['calendar_id'],'data-email'=>$aRow['email'],'data-hide-from-client'=>$aRow['hidefromclient']
                    ));
                $row[]   = $options .= icon_btn('admin/departments/delete/' . $aRow['departmentid'], 'remove', 'btn-danger');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
        $data['title'] = _l('departments');
        $this->load->view('admin/departments/manage', $data);
    }

    /* Edit or add new department */
    public function department($id = '')
    {
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->departments_model->add($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfuly',_l('department'));
                }
                echo json_encode(array('success'=>$success,'message'=>$message));
            } else {
                $data = $this->input->post();
                $id = $data['id'];
                unset($data['id']);
                $success = $this->departments_model->update($data, $id);
                if ($success) {
                    $message = _l('updated_successfuly',_l('department'));
                }
                echo json_encode(array('success'=>$success,'message'=>$message));
            }
            die;
        }

    }

    /* Delete department from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('departments'));
        }

        $response = $this->departments_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
           set_alert('warning',_l('is_referenced',_l('department_lowercase')));
        } else if ($response == true) {
           set_alert('success', _l('deleted',_l('department')));
        } else {
            set_alert('warning', _l('problem_deleting',_l('department_lowercase')));
        }

        redirect(admin_url('departments'));
    }

    public function email_exists(){
        // First we need to check if the email is the same
        $departmentid = $this->input->post('departmentid');
        if ($departmentid != 'undefined') {
            $this->db->where('departmentid', $departmentid);
            $_current_email = $this->db->get('tbldepartments')->row();

            if ($_current_email->email == $this->input->post('email')) {
                echo json_encode(true);
                die();
            }
        }
        $exists = total_rows('tbldepartments',array('email'=>$this->input->post('email')));
        if($exists > 0){
            echo 'false';
        } else {
            echo 'true';
        }
    }
}
