<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Custom_fields extends Admin_controller
{

    private $pdf_fields = array();
    private $client_portal_fields = array();
    function __construct()
    {
        parent::__construct();
        $this->load->model('custom_fields_model');
        if(!is_admin()){
            access_denied('Access Custom Fields');
        }
        // Add the pdf allowed fields
        $this->pdf_fields = $this->custom_fields_model->get_pdf_allowed_fields();
        $this->client_portal_fields = $this->custom_fields_model->get_client_portal_allowed_fields();
    }


    /* List all custom fields */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns     = array(
                'id',
                'name',
                'fieldto',
                'type',
                'active',
                );
            $sIndexColumn = "id";
            $sTable       = 'tblcustomfields';

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name' || $aColumns[$i] == 'id') {
                        $_data = '<a href="' . admin_url('custom_fields/field/' . $aRow['id']) . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'active') {
                        $checked = '';
                        if ($aRow['active'] == 1) {
                            $checked = 'checked';
                        }
                        $_data = '<input type="checkbox" class="switch-box input-xs" data-size="mini" data-id="' . $aRow['id'] . '" data-switch-url="admin/custom_fields/change_custom_field_status" ' . $checked . '>';
                        // For exporting
                        $_data .=  '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) .'</span>';
                    }

                    $row[] = $_data;
                }

                $options = icon_btn('admin/custom_fields/field/' . $aRow['id'], 'pencil-square-o');
                $row[]   = $options .= icon_btn('admin/custom_fields/delete/' . $aRow['id'], 'remove', 'btn-danger');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
        $data['title'] = _l('custom_field');
        $this->load->view('admin/custom_fields/manage', $data);
    }


    public function field($id = ''){
        if ($this->input->post()) {
            if ($id == '') {
                $id = $this->custom_fields_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly',_l('custom_field')));
                    redirect(admin_url('custom_fields/field/' . $id));
                }
            } else {
                $success = $this->custom_fields_model->update($this->input->post(), $id);
                if ($success) {
                   set_alert('success', _l('updated_successfuly',_l('custom_field')));

               }

               redirect(admin_url('custom_fields/field/' . $id));
           }
       }
       if ($id == '') {
        $title = _l('add_new',_l('custom_field_lowercase'));
    } else {
        $data['custom_field'] = $this->custom_fields_model->get($id);
        $title                = _l('edit',_l('custom_field_lowercase'));
    }
    $data['pdf_fields'] = $this->pdf_fields;
    $data['client_portal_fields'] = $this->client_portal_fields;
    $data['title'] = $title;
    $this->load->view('admin/custom_fields/customfield', $data);
}

    /* Delete announcement from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('custom_fields'));
        }

        $response = $this->custom_fields_model->delete($id);

        if ($response == true) {
            set_alert('success', _l('deleted',_l('custom_field')));
        } else {
            set_alert('warning', _l('problem_deleting',_l('custom_field_lowercase')));
        }

        redirect(admin_url('custom_fields'));
    }
 /* Change survey status active or inactive*/
    public function change_custom_field_status($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $this->custom_fields_model->change_custom_field_status($id, $status);
        }
    }
}
