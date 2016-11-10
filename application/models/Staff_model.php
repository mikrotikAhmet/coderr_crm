<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Staff_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get staff member/s
     * @param  mixed $id Optional - staff id
     * @param  integer $active Optional get all active or inactive
     * @return mixed if id is passed return object else array
     */
    public function get($id = '', $active = '',$where = array())
    {
        if (is_int($active)) {
            $this->db->where('active', $active);
        }

        if (is_numeric($id)) {
            $this->db->where('staffid', $id);
            return $this->db->get('tblstaff')->row();
        }

        $this->db->order_by('datecreated', 'desc');
        return $this->db->get('tblstaff')->result_array();
    }


    /**
     * Add new staff member
     * @param array $data staff $_POST data
     */
    public function add($data)
    {

        // First check for all cases if the email exists.
        $this->db->where('email', $data['email']);
        $email = $this->db->get('tblstaff')->row();
        if ($email) {
            die('Email already exists');
        }

        $data['admin'] = 0;
        if (is_admin()) {
            if (isset($data['administrator'])) {
                $data['admin'] = 1;
                unset($data['administrator']);
            }
        }

        $this->load->helper('phpass');
        $hasher           = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        $data['password'] = $hasher->HashPassword($data['password']);

        $data['datecreated'] = date('Y-m-d H:i:s');

        if (isset($data['departments'])) {
            $departments = $data['departments'];
            unset($data['departments']);
        }

        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
            unset($data['permissions']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $this->db->insert('tblstaff', $data);
        $staffid = $this->db->insert_id();

        if ($staffid) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($staffid, $custom_fields);
            }

            if (isset($departments)) {
                foreach ($departments as $department) {
                    $this->db->insert('tblstaffdepartments', array(
                        'staffid' => $staffid,
                        'departmentid' => $department
                    ));
                }
            }


            if (isset($permissions)) {
                foreach ($permissions as $permission) {
                    $this->db->insert('tblstaffpermissions', array(
                        'staffid' => $staffid,
                        'permissionid' => $permission
                    ));
                }
            }

            logActivity('New Staff Member Added [ID: ' . $staffid . ', ' . $data['firstname'] . ' ' . $data['lastname'] . ']');

            // Delete all staff permission if is admin we dont need permissions stored in database (in case admin check some permissions)
            if ($data['admin'] == 1) {
                $this->db->where('staffid', $staffid);
                $this->db->delete('tblstaffpermissions');
            }

            // Get all announcements and set it to read.
            $this->db->select('announcementid');
            $this->db->from('tblannouncements');
            $this->db->where('showtostaff',1);
            $announcements = $this->db->get()->result_array();

            foreach($announcements as $announcement){
                $this->db->insert('tbldismissedannouncements',array(
                    'announcementid'=>$announcement['announcementid'],
                    'staff'=>1,
                    'userid'=>$staffid
                    ));
            }

            return $staffid;

        }
        return false;
    }

    /**
     * Update staff member info
     * @param  array $data staff data
     * @param  mixed $id   staff id
     * @return boolean
     */
    public function update($data, $id)
    {

        $affectedRows = 0;
        if (isset($data['departments'])) {
            $departments = $data['departments'];
            unset($data['departments']);
        }
        $permissions = array();
        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
            unset($data['permissions']);
        }

         if(isset($data['custom_fields'])){
            $custom_fields = $data['custom_fields'];
            if(handle_custom_fields_post($id,$custom_fields)){
                $affectedRows++;
            }
            unset($data['custom_fields']);
         }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $this->load->helper('phpass');
            $hasher                       = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
            $data['password']             = $hasher->HashPassword($data['password']);
            $data['last_password_change'] = date('Y-m-d H:i:s');
        }

        if (strpos($this->uri->uri_string(), 'edit_profile') === false) {
            if (is_admin()) {
                if (isset($data['administrator'])) {
                    $data['admin'] = 1;
                } else {
                    if ($id != get_staff_user_id()) {
                        if ($id == 1) {
                            set_alert('warning', _l('staff_cant_remove_main_admin'));
                            return false;
                        }
                        $data['admin'] = 0;
                    } else {
                        set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
                    }
                }
            }
        }

        unset($data['administrator']);

        $this->db->where('staffid', $id);
        $this->db->update('tblstaff', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        $this->load->model('departments_model');
        $staff_departments = $this->departments_model->get_staff_departments($id);

        if (sizeof($staff_departments) > 0) {
            foreach ($staff_departments as $staff_department) {
                if (isset($departments)) {
                    if (!in_array($staff_department['departmentid'], $departments)) {
                        $this->db->where('staffid', $id);
                        $this->db->where('departmentid', $staff_department['departmentid']);
                        $this->db->delete('tblstaffdepartments');

                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
            }

            if (isset($departments)) {
                foreach ($departments as $department) {
                    $this->db->where('staffid', $id);
                    $this->db->where('departmentid', $department);
                    $_exists = $this->db->get('tblstaffdepartments')->row();
                    if (!$_exists) {
                        $this->db->insert('tblstaffdepartments', array(
                            'staffid' => $id,
                            'departmentid' => $department
                        ));

                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
            }

        } else {
            if (isset($departments)) {
                foreach ($departments as $department) {
                    $this->db->insert('tblstaffdepartments', array(
                        'staffid' => $id,
                        'departmentid' => $department
                    ));
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
        }

        if (!is_admin($id)) {
            $staff_permissions = $this->roles_model->get_staff_permissions($id);
            if (sizeof($staff_permissions) > 0) {
                foreach ($staff_permissions as $staff_permission) {
                    if (!in_array($staff_permission['permissionid'], $permissions)) {
                        $this->db->where('staffid', $id);
                        $this->db->where('permissionid', $staff_permission['permissionid']);
                        $this->db->delete('tblstaffpermissions');
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
                foreach ($permissions as $permission) {
                    $this->db->where('staffid', $id);
                    $this->db->where('permissionid', $permission);
                    $_exists = $this->db->get('tblstaffpermissions')->row();

                    if (!$_exists) {
                        $this->db->insert('tblstaffpermissions', array(
                            'staffid' => $id,
                            'permissionid' => $permission
                        ));

                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
            } else {
                foreach ($permissions as $permission) {
                    $this->db->insert('tblstaffpermissions', array(
                        'staffid' => $id,
                        'permissionid' => $permission
                    ));
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
        }

        if (isset($data['admin']) && $data['admin'] == 1) {
            $this->db->where('staffid', $id);
            $this->db->delete('tblstaffpermissions');
        }
        if ($affectedRows > 0) {
            logActivity('Staff Member Updated [ID: ' . $id . ', ' . $data['firstname'] . ' ' . $data['lastname'] . ']');
            return true;
        }

        return false;
    }

    /**
     * Change staff passwordn
     * @param  mixed $data   password data
     * @param  mixed $userid staff id
     * @return mixed
     */
    public function change_password($data, $userid)
    {

        $member = $this->get($userid);
        // CHeck if member is active
        if ($member->active == 0) {
            return array(
                array(
                    'memberinactive' => true
                )
            );
        }

        $this->load->helper('phpass');
        $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        // Check new old password
        if (!$hasher->CheckPassword($data['oldpassword'], $member->password)) {
            return array(
                array(
                    'passwordnotmatch' => true
                )
            );
        }

        $data['newpasswordr'] = $hasher->HashPassword($data['newpasswordr']);

        $this->db->where('staffid', $userid);
        $this->db->update('tblstaff', array(
            'password' => $data['newpasswordr'],
            'last_password_change' => date('Y-m-d H:i:s')
        ));

        if ($this->db->affected_rows() > 0) {
            logActivity('Staff Password Changed [' . $userid . ']');
            return true;
        }
        return false;
    }

    /**
     * Change staff status / active / inactive
     * @param  mixed $id     staff id
     * @param  mixed $status status(0/1)
     */
    public function change_staff_status($id, $status)
    {
        $this->db->where('staffid', $id);
        $this->db->update('tblstaff', array(
            'active' => $status
        ));
        logActivity('Staff Status Changed [StaffID: ' . $id . ' - Status(Active/Inactive): ' . $status . ']');
    }
}
