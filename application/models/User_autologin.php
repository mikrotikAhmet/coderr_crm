<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_Autologin extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Check if autologin found
     * @param  mixed $user_id clientid/staffid
     * @param  string $key     key from cookie to retrieve from database
     * @return mixed
     */
    function get($user_id, $key)
    {
        // check if user is staff
        $this->db->where('user_id', $user_id);
        $this->db->where('key_id', $key);
        $user = $this->db->get('tbluserautologin')->row();

        if (!$user) {
            return NULL;
        }

        if ($user->staff == 1) {
            $table = 'tblstaff';
            $this->db->select($table . '.staffid as id');
            $_id   = 'staffid';
            $staff = true;
        } else {
            $table = 'tblclients';
            $this->db->select($table . '.userid as id');
            $_id   = 'userid';
            $staff = false;
        }


        $this->db->select($table . '.' . $_id);
        $this->db->from($table);
        $this->db->join('tbluserautologin','tbluserautologin.user_id = ' . $table . '.' . $_id);
        $this->db->where('tbluserautologin.user_id', $user_id);
        $this->db->where('tbluserautologin.key_id', $key);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            $user        = $query->row();
            $user->staff = $staff;
            return $user;
        }
        return NULL;
    }

    /**
     * Set new autologin if user have clicked remember me
     * @param mixed $user_id clientid/userid
     * @param string $key     cookie key
     * @param integer $staff   is staff or client
     */
    function set($user_id, $key, $staff)
    {
        return $this->db->insert('tbluserautologin', array(
            'user_id' => $user_id,
            'key_id' => $key,
            'user_agent' => substr($this->input->user_agent(), 0, 149),
            'last_ip' => $this->input->ip_address(),
            'staff' => $staff
        ));
    }

    /**
     * Delete user autologin
     * @param  mixed $user_id clientid/userid
     * @param  string $key     cookie key
     * @param integer $staff   is staff or client
     */
    function delete($user_id, $key, $staff)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('key_id', $key);
        $this->db->where('staff', $staff);
        $this->db->delete('tbluserautologin');
    }
}
