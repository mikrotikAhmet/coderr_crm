<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Roles extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        if (!has_permission('manageRoles')) {
            access_denied('manageRoles');
        }
        // Model is autoloaded
    }

    /* List all staff roles */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns     = array(
                'name'
            );

            $sIndexColumn = "roleid";
            $sTable       = 'tblroles';

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable,array(),array(),array('roleid'));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name') {
                        $role_permissions = $this->roles_model->get_role_permissions($aRow['roleid']);
                        $_data            = '<a href="' . admin_url('roles/role/' . $aRow['roleid']) . '" class="mbot10 display-block">' . $_data . '</a>';
                        foreach ($role_permissions as $permission) {
                            $_data .= '<div class="chip-circle chip-small">' . $permission['name'] . '</div>';
                        }
                        $_data .= '<span class="mtop10 display-block">'._l('roles_total_users'). ' ' . total_rows('tblstaff', array(
                            'role' => $aRow['roleid']
                        )) . '</span>';
                    }
                    $row[] = $_data;
                }

                $options = icon_btn('admin/roles/role/' . $aRow['roleid'], 'pencil-square-o');
                $row[]   = $options .= icon_btn('admin/roles/delete/' . $aRow['roleid'], 'remove', 'btn-danger');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
        $data['title'] = _l('all_roles');
        $this->load->view('admin/roles/manage', $data);
    }

    /* Add new role or edit existing one */
    public function role($id = '')
    {

        if ($this->input->post()) {
            if ($id == '') {
                $id = $this->roles_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly',_l('contract')));
                    redirect(admin_url('roles/role/' . $id));
                }
            } else {
                $success = $this->roles_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly',_l('role')));
                }
                redirect(admin_url('roles/role/' . $id));
            }
        }

        if ($id == '') {
            $title = _l('add_new',_l('role_lowercase'));
        } else {
            $data['role_permissions'] = $this->roles_model->get_role_permissions($id);
            $role                     = $this->roles_model->get($id);
            $data['role']             = $role;
            $title                    = _l('edit',_l('role_lowercase')) . ' ' . $role->name;
        }
        $data['permissions'] = $this->roles_model->get_permissions();
        $data['title']       = $title;
        $this->load->view('admin/roles/role', $data);
    }

    /* Delete staff role from database */
    public function delete($id)
    {

        if (!$id) {
            redirect(admin_url('roles'));
        }

        $response = $this->roles_model->delete($id);

       if(is_array($response) && isset($response['referenced'])){
            set_alert('warning',_l('is_referenced',_l('role_lowercase')));
        } else if($response == true){
            set_alert('success', _l('deleted',_l('role')));
        } else {
            set_alert('warning', _l('problem_deleting',_l('role_lowercase')));
        }

        redirect(admin_url('roles'));
    }
}
