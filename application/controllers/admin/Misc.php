<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Misc extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('misc_model');
    }

    public function get_taxes_dropdown_template()
    {
        $name  = $this->input->post('name');
        $taxid = $this->input->post('taxid');
        echo $this->misc_model->get_taxes_dropdown_template($name, $taxid);
    }

    public function get_relation_data()
    {

        if ($this->input->post()) {
            $type = $this->input->post('type');
            $data = get_relation_data($type);

            if ($this->input->post('rel_id')) {
                $rel_id = $this->input->post('rel_id');
            } else {
                $rel_id = '';
            }

            init_relation_options($data, $type, $rel_id);
        }
    }


    public function send_file($clientid)
    {
        if ($this->input->post('send_file_email')) {
            if ($this->input->post('file_path')) {
                $this->load->model('emails_model');
                $this->emails_model->add_attachment(array(
                    'attachment' => $this->input->post('file_path'),
                    'filename' => $this->input->post('file_name'),
                    'type' => $this->input->post('filetype'),
                    'read' => true
                ));
                $message = $this->input->post('send_file_message');
                $message .= get_option('email_signature');
                $success = $this->emails_model->send_simple_email($this->input->post('send_file_email'), $this->input->post('send_file_subject'), $message);

                if ($success) {
                    set_alert('success', _l('custom_file_success_send', $this->input->post('send_file_email')));
                } else {
                    set_alert('warning', _l('custom_file_fail_send'));
                }
            }
        }

        redirect($this->input->post('return_url'));
    }


    /* Since version 1.0.2 add client reminder */
    public function add_reminder($rel_id_id, $rel_type)
    {

        $message    = '';
        $alert_type = 'warning';
        if ($this->input->post()) {
            $success = $this->misc_model->add_reminder($this->input->post(), $rel_id_id);
            if ($success) {
                $alert_type = 'success';
                $message    = _l('reminder_added_successfuly');
            }
        }

        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }
    public function get_reminders($id, $rel_type)
    {
        if ($this->input->is_ajax_request()) {

            $aColumns     = array(
                'description',
                'date',
                'staff',
                'isnotified'
            );
            $sIndexColumn = "id";
            $sTable       = 'tblreminders';

            $where  = array(
                'AND rel_id=' . $id . ' AND rel_type="' . $rel_type . '"'
            );
            $join   = array(
                'JOIN tblstaff ON tblstaff.staffid = tblreminders.staff'
            );
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'firstname',
                'lastname',
                'id',
                'creator',
                'rel_type'
            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'staff') {
                        $_data = '<a href="' . admin_url('staff/profile/' . $aRow['staff']) . '">' . staff_profile_image($aRow['staff'], array(
                            'staff-profile-image-small'
                        )) . ' ' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>';
                    } else if ($aColumns[$i] == 'staff') {
                        $_data = _d($_data);
                    } else if ($aColumns[$i] == 'isnotified') {
                        if ($_data == 1) {
                            $_data = _l('reminder_is_notified_boolean_yes');
                        } else {
                            $_data = _l('reminder_is_notified_boolean_no');
                        }
                    }
                    $row[] = $_data;
                }

                if ($aRow['creator'] == get_staff_user_id()) {
                    $row[] = icon_btn('admin/misc/delete_reminder/' . $id . '/' . $aRow['id'] . '/' . $aRow['rel_type'], 'remove', 'btn-danger delete-reminder');
                } else {
                    $row[] = '';
                }


                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /* Since version 1.0.2 delete client reminder */
    public function delete_reminder($rel_id, $id, $rel_type)
    {
        if (!$id && !$rel_id) {
            die('No reminder found');
        }

        $success    = $this->misc_model->delete_reminder($id);
        $alert_type = 'warning';
        $message    = _l('reminder_failed_to_delete');
        if ($success) {
            $alert_type = 'success';
            $message    = _l('reminder_deleted');
        }


        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    public function run_cron_manually()
    {
        if (has_permission('editSettings')) {
            $this->load->model('cron_model');
            $this->cron_model->run(true);
            redirect(admin_url('settings'));
        }
    }

    /* Since Version 1.0.1 - General search */
    public function search()
    {
        $data['result'] = $this->misc_model->perform_search($this->input->post('q'));
        $this->load->view('admin/search', $data);
    }

    /* Remove customizer open from database */
    public function set_customizer_closed()
    {
        if ($this->input->is_ajax_request()) {
            $this->session->set_userdata(array(
                'customizer-open' => ''
            ));
        }
    }
    /* Set session that user clicked on customizer menu link to stay open */
    public function set_customizer_open()
    {
        if ($this->input->is_ajax_request()) {
            $this->session->set_userdata(array(
                'customizer-open' => true
            ));
        }
    }
    /* User dismiss announcement */
    public function dismiss_announcement()
    {
        if ($this->input->post()) {
            $this->misc_model->dismiss_announcement($this->input->post());
        }
    }
    /* Set notifications to read */
    public function set_notifications_read()
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode(array(
                'success' => $this->misc_model->set_notifications_read()
            ));
        }
    }
    /*Add new client /staff note from admin area */
    public function add_user_note($userid, $staff)
    {

        if (!$userid) {
            die('No user id found');
        }

        $redirect_controller = 'clients';
        $redirect_method     = 'client';
        if ($staff == 1) {
            $redirect_controller = 'staff';
            $redirect_method     = 'member';
        }

        if ($this->input->post()) {
            $success = $this->misc_model->add_user_note($this->input->post(), $userid, $staff);
            if ($success) {
                set_alert('success', 'User note added successfuly');
            }
            redirect(admin_url('' . $redirect_controller . '/' . $redirect_method . '/' . $userid));
        }
    }
    /* Delete client /staff note from admin area */
    public function remove_user_note($noteid, $userid, $staff)
    {
        if (!$noteid) {
            die('No note id found');
        }

        $redirect_controller = 'clients';
        $redirect_method     = 'client';
        if ($staff == 1) {
            $redirect_controller = 'staff';
            $redirect_method     = 'member';
        }

        $success = $this->misc_model->delete_user_note($noteid);
        if ($success) {
            set_alert('success', 'User note deleted successfuly');
        }

        redirect(admin_url('' . $redirect_controller . '/' . $redirect_method . '/' . $userid));
    }
    /* Check if staff email exists / ajax */
    public function staff_email_exists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the email is the same
                $member_id = $this->input->post('memberid');

                if ($member_id != 'undefined') {
                    $this->db->where('staffid', $member_id);
                    $_current_email = $this->db->get('tblstaff')->row();

                    if ($_current_email->email == $this->input->post('email')) {
                        echo json_encode(true);
                        die();
                    }
                }

                $this->db->where('email', $this->input->post('email'));
                $total_rows = $this->db->count_all_results('tblstaff');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }
    /* Check if client email exists/  ajax */
    public function clients_email_exists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the email is the same
                $userid = $this->input->post('userid');
                if ($userid != 'undefined') {
                    $this->db->where('userid', $userid);
                    $_current_email = $this->db->get('tblclients')->row();

                    if ($_current_email->email == $this->input->post('email')) {
                        echo json_encode(true);
                        die();
                    }
                }
                $this->db->where('email', $this->input->post('email'));
                $total_rows = $this->db->count_all_results('tblclients');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }

    /* Check if client email exists/  ajax */
    public function affiliates_email_exists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the email is the same
                $affiliateid = $this->input->post('affiliateid');
                if ($affiliateid != 'undefined') {
                    $this->db->where('affiliateid', $affiliateid);
                    $_current_email = $this->db->get('tblaffiliates')->row();

                    if ($_current_email->email == $this->input->post('email')) {
                        echo json_encode(true);
                        die();
                    }
                }
                $this->db->where('email', $this->input->post('email'));
                $total_rows = $this->db->count_all_results('tblaffiliates');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }

    /* Goes blank page but with messagae access danied / message set from session flashdata */
    public function access_denied()
    {
        $this->load->view('admin/blank_page');
    }
    /* Goes to blank page with message page not found / message set from session flashdata */
    public function not_found()
    {
        $this->load->view('admin/blank_page');
    }
    /* Get role permission for specific role id / Function relocated here becuase the Roles Model have statement on top if has role permission */
    public function get_role_permissions_ajax($id)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->roles_model->get_role_permissions($id));
            die();
        }
    }
}
