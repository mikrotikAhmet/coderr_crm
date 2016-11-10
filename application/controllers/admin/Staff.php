<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Staff extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('staff_model');

        if (!has_permission('manageStaff')) {
            // Allowed to view profile and to edit own profile
           if (strpos($this->uri->uri_string(),'/profile') == false && strpos($this->uri->uri_string(),'/edit_profile') == false && strpos($this->uri->uri_string(),'/remove_staff_profile_image') == false && strpos($this->uri->uri_string(),'/notifications') == false) {
               access_denied('manageStaff');
           }
        }
    }

    /* List all staff members */
    public function index()
    {
        if ($this->input->is_ajax_request()) {

            $custom_fields = get_custom_fields('staff',array('show_on_table'=>1));

            $aColumns     = array(
                'staffid',
                'firstname',
                'email',
                'last_login',
                'active'
            );
            $sIndexColumn = "staffid";
            $sTable       = 'tblstaff';

            $join = array();

             $i = 0;
            foreach($custom_fields as $field){
                    array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
                    array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblstaff.staffid = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
                    $i++;
                }


            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, array(), array(
                'profile_image',
                'lastname'
            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {

                    if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
                        $_data = $aRow[ strafter($aColumns[$i],'as ')];
                    } else {
                        $_data = $aRow[ $aColumns[$i] ];
                    }

                    if ($aColumns[$i] == 'staffid') {
                        $_data = '<a href="' . admin_url('staff/member/' . $aRow['staffid']) . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'last_login') {
                        if ($_data != NULL) {
                            $_data = time_ago($_data);
                        } else {
                            $_data = 'Never';
                        }
                    } else if ($aColumns[$i] == 'active') {
                        $checked = '';
                        if ($aRow['active'] == 1) {
                            $checked = 'checked';
                        }
                        $_data = '<input type="checkbox" class="switch-box input-xs" data-size="mini" data-id="' . $aRow['staffid'] . '" data-switch-url="admin/staff/change_staff_status" ' . $checked . '>';
                        // For exporting
                        $_data .=  '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) .'</span>';

                    } else if ($aColumns[$i] == 'firstname') {
                        $_data = staff_profile_image($aRow['staffid'], array(
                            'staff-profile-image-small'
                        ));
                        $_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['staffid']) . '">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>';
                    } else if($aColumns[$i] == 'email'){
                                    $_data = '<a href="mailto:'.$_data.'">'.$_data.'</a>';
                                }

                    $row[] = $_data;
                }

                $row[]              = icon_btn('admin/staff/member/' . $aRow['staffid'], 'pencil-square-o');
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
        $data['title'] = _l('staff_members');
        $this->load->view('admin/staff/manage', $data);
    }

    /* Add new staff member or edit existing */
    public function member($id = '')
    {
        $this->load->model('departments_model');
        if ($this->input->post()) {
            if ($id == '') {
                $id = $this->staff_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly',_l('staff_member')));
                    redirect(admin_url('staff/member/' . $id));
                }
            } else {
                $success = $this->staff_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly',_l('staff_member')));
                }

                redirect(admin_url('staff/member/' . $id));
            }
        }

        if ($id == '') {
            $title = _l('add_new',_l('staff_member_lowercase'));
        } else {

            $member                    = $this->staff_model->get($id);
            $data['member']            = $member;
            $title                     = _l('edit',_l('staff_member_lowercase')). ' ' . $member->firstname . ' ' . $member->lastname;
            $data['staff_permissions'] = $this->roles_model->get_staff_permissions($id);
            $data['staff_departments'] = $this->departments_model->get_staff_departments($member->staffid);
        }
        $data['roles']       = $this->roles_model->get();
        $data['permissions'] = $this->roles_model->get_permissions();
        $data['user_notes']  = $this->misc_model->get_user_notes($id);
        $data['departments'] = $this->departments_model->get();

        $data['title'] = $title;
        $this->load->view('admin/staff/member', $data);
    }

    /* When staff edit his profile */
    public function edit_profile()
    {

        if ($this->input->post()) {
            handle_staff_profile_image_upload();
            $success = $this->staff_model->update($this->input->post(), get_staff_user_id());
            if ($success) {
               set_alert('success', _l('staff_profile_updated'));
            }
            redirect(admin_url('staff/edit_profile/' . get_staff_user_id()));
        }
        $member = $this->staff_model->get(get_staff_user_id());
        $this->load->model('departments_model');
        $data['member']            = $member;
        $data['departments']       = $this->departments_model->get();
        $data['staff_departments'] = $this->departments_model->get_staff_departments($member->staffid);

        $data['title'] = $member->firstname . ' ' . $member->lastname;
        $this->load->view('admin/staff/profile', $data);
    }

    /* Remove staff profile image / ajax */
    public function remove_staff_profile_image()
    {
            $member        = $this->staff_model->get(get_staff_user_id());
            if(file_exists(STAFF_PROFILE_IMAGES_FOLDER . get_staff_user_id())){
                delete_dir(STAFF_PROFILE_IMAGES_FOLDER . get_staff_user_id());
            }
            $this->db->where('staffid', get_staff_user_id());
            $this->db->update('tblstaff', array(
                'profile_image' => NULL
                ));
            if ($this->db->affected_rows() > 0) {
                 redirect(admin_url('staff/edit_profile/'.get_staff_user_id()));
            }

    }
    /* When staff change his password */
    public function change_password_profile()
    {
        if ($this->input->post()) {
            $response = $this->staff_model->change_password($this->input->post(), get_staff_user_id());

            if (is_array($response) && isset($response[0]['passwordnotmatch'])) {
                set_alert('danger', _l('staff_old_password_incorect'));

            } else {
                if ($response == true) {
                    set_alert('success', _l('staff_password_changed'));

                } else {
                    set_alert('warning', _l('staff_problem_changing_password'));
                }
            }

            redirect(admin_url('staff/edit_profile'));
        }
    }
    /* View public profile. If id passed view profile by staff id else current user*/
    public function profile($id = '')
    {
        if ($id == '') {
            $data['staff_p'] = $this->staff_model->get(get_staff_user_id());
        } else {
            $data['staff_p'] = $this->staff_model->get($id);
        }
        $this->load->model('departments_model');
        $data['staff_departments'] = $this->departments_model->get_staff_departments($data['staff_p']->staffid);
        $data['departments']       = $this->departments_model->get();

        $data['title']             = _l('staff_profile_string') . ' - ' . $data['staff_p']->firstname . ' ' . $data['staff_p']->lastname;
        // notifications
        $total_notifications       = total_rows('tblnotifications', array(
            'touserid' => get_staff_user_id()
        ));
        $data['total_pages']       = ceil($total_notifications / $this->misc_model->notifications_limit);
        $this->load->view('admin/staff/myprofile', $data);
    }
    /* Change status to staff active or inactive / ajax */
    public function change_staff_status($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $this->staff_model->change_staff_status($id, $status);
        }
    }

    /* Logged in staff notifications*/
    public function notifications()
    {
        $this->load->model('misc_model');

        if ($this->input->post()) {
            echo json_encode($this->misc_model->get_all_user_notifications($this->input->post('page')));
            die;
        }

    }

}
