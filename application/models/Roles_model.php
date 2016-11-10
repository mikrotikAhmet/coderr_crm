<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Roles_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Add new employee role
     * @param mixed $data
     */
    public function add($data)
    {
        $permissions = array();
        if(isset($data['permissions'])){
            $permissions = $data['permissions'];
            unset($data['permissions']);
        }


        $this->db->insert('tblroles', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            foreach ($permissions as $permission) {
                $this->db->insert('tblrolepermissions', array(
                    'roleid' => $insert_id,
                    'permissionid' => $permission
                ));
            }
            logActivity('New Role Added [ID: '.$insert_id. '.' . $data['name'] . ']');
           return $insert_id;
        }

        return false;
    }

    /**
     * Update employee role
     * @param  array $data role data
     * @param  mixed $id   role id
     * @return boolean
     */
    public function update($data, $id)
    {

        $affectedRows = 0;
        $permissions = array();
        if(isset($data['permissions'])){
            $permissions  = $data['permissions'];
            unset($data['permissions']);
        }


        $this->db->where('roleid', $id);
        $this->db->update('tblroles', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        $role_permissions = $this->get_role_permissions($id);
        if (sizeof($role_permissions) > 0) {
            foreach ($role_permissions as $role_permission) {
                if (!in_array($role_permission['permissionid'], $permissions)) {
                    $this->db->where('roleid', $id);
                    $this->db->where('permissionid', $role_permission['permissionid']);
                    $this->db->delete('tblrolepermissions');
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            foreach ($permissions as $permission) {
                $this->db->where('roleid', $id);
                $this->db->where('permissionid', $permission);
                $_exists = $this->db->get('tblrolepermissions')->row();
                if (!$_exists) {
                    $this->db->insert('tblrolepermissions', array(
                        'roleid' => $id,
                        'permissionid' => $permission
                    ));

                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
        } else {
            foreach ($permissions as $permission) {
                $this->db->insert('tblrolepermissions', array(
                    'roleid' => $id,
                    'permissionid' => $permission
                ));
                if ($this->db->affected_rows() > 0) {
                    $affectedRows++;
                }
            }
        }

        if ($affectedRows > 0) {
            logActivity('Role Updated [ID: '.$id. '.' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * Get employee role by id
     * @param  mixed $id Optional role id
     * @return mixed     array if not id passed else object
     */
    public function get($id = '')
    {

        if (is_numeric($id)) {
            $this->db->where('roleid', $id);
            return $this->db->get('tblroles')->row();
        }

        return $this->db->get('tblroles')->result_array();
    }

    /**
     * Delete employee role
     * @param  mixed $id role id
     * @return mixed
     */
    public function delete($id)
    {

        $current = $this->get($id);
        // Check first if role is used in table
        if (is_reference_in_table('role', 'tblstaff', $id)) {
            return array(
                'referenced' => true
            );
        }
        $affectedRows = 0;
        $this->db->where('roleid', $id);
        $this->db->delete('tblroles');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        $this->db->where('roleid', $id);
        $this->db->delete('tblrolepermissions');


        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            logActivity('Role Deleted [ID: ' . $id);
            return true;
        }

        return false;
    }

    /**
     * Get employee role permissions
     * @param  mixed $id permission id
     * @return mixed if id passed return object else array
     */
    public function get_permissions($id = '')
    {

        if (is_numeric($id)) {
            $this->db->where('permissionid', $id);
            return $this->db->get('tblpermissions')->row();
        }

        return $this->db->get('tblpermissions')->result_array();
    }

    /**
     * Get specific role permissions
     * @param  mixed $id role id
     * @return array
     */
    public function get_role_permissions($id)
    {
        $this->db->where('roleid', $id);
        $this->db->join('tblpermissions', 'tblpermissions.permissionid = tblrolepermissions.permissionid', 'left');
        return $this->db->get('tblrolepermissions')->result_array();
    }

    /**
     * Get staff permission / Staff can have other permissions too different from the role which is assigned
     * @param  mixed $id Optional - staff id
     * @return array
     */
    public function get_staff_permissions($id = '')
    {

        // If not id is passed get from current user
        if ($id == false) {
            $id = get_staff_user_id();
        }

        $this->db->where('staffid', $id);
        return $this->db->get('tblstaffpermissions')->result_array();
    }

    public function get_customer_permissions($id){
        $this->db->where('userid', $id);
        return $this->db->get('tblcustomerpermissions')->result_array();
    }

}
