<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Announcements_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  integer (optional)
     * @return object
     * Get single announcement
     */
    public function get($id = false)
    {

        if (is_numeric($id)) {
            $this->db->where('announcementid', $id);
            return $this->db->get('tblannouncements')->row();
        }

        return $this->db->get('tblannouncements')->result_array();
    }

    /**
     * @param $_POST array
     * @return Insert ID
     * Add new announcement calling this function
     */
    public function add($data)
    {
        $data['dateadded'] = date('Y-m-d H:i:s');


        if (isset($data['showname'])) {
            $data['showname'] = 1;
        } else {
            $data['showname'] = 0;
        }

        if (isset($data['showtostaff'])) {
            $data['showtostaff'] = 1;
        } else {
            $data['showtostaff'] = 0;
        }

        if (isset($data['showtousers'])) {
            $data['showtousers'] = 1;
        } else {
            $data['showtousers'] = 0;
        }

        $data['message'] = nl2br($data['message']);

        $data['userid'] = get_staff_user_id();
        $data = do_action('before_announcement_added', $data);
        $this->db->insert('tblannouncements', $data);
        $insert_id = $this->db->insert_id();
        do_action('after_announcement_added', $insertid);
        logActivity('New Announcement Added [' . $data['name'] . ']');
        return $insert_id;
    }

    /**
     * @param  $_POST array
     * @param  integer
     * @return boolean
     * This function updates announcement
     */
    public function update($data, $id)
    {

        if (isset($data['showname'])) {
            $data['showname'] = 1;
        } else {
            $data['showname'] = 0;
        }

        if (isset($data['showtostaff'])) {
            $data['showtostaff'] = 1;
        } else {
            $data['showtostaff'] = 0;
        }

        if (isset($data['showtousers'])) {
            $data['showtousers'] = 1;
        } else {
            $data['showtousers'] = 0;
        }
        $data['message'] = nl2br($data['message']);

        $_data = do_action('before_announcement_updated',array('data'=>$data,'id'=>$id));
        $data = $_data['data'];

        $this->db->where('announcementid', $id);
        $this->db->update('tblannouncements', $data);

        if ($this->db->affected_rows() > 0) {
            do_action('after_announcement_updated', $id);
            logActivity('Announcement Updated [' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer
     * @return boolean
     * Delete Announcement
     * All Dimissed announcements from database will be cleaned
     */
    public function delete($id)
    {

        do_action('before_announcement_deleted', $id);
        $this->db->where('announcementid', $id);
        $this->db->delete('tblannouncements');

        if ($this->db->affected_rows() > 0) {
            logActivity('Announcement Deleted [' . $id . ']');
            $this->db->where('announcementid', $id);
            $DISMISSED_ANNOUNCEMENTS_TABLE      = $this->db->get('tbldismissedannouncements')->result_array();
            $total_dismissed_announcement = count($DISMISSED_ANNOUNCEMENTS_TABLE);

            if ($total_dismissed_announcement > 0) {
                $this->db->where('announcementid', $id);
                $this->db->delete('tbldismissedannouncements');

                if ($this->db->affected_rows() == $total_dismissed_announcement) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return false;
    }

}
