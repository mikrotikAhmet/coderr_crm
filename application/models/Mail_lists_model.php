<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mail_lists_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get mail list/s
     * @param  mixed $id Optional
     * @return mixed     object if id is passed else array
     */
    public function get($id = '')
    {

        $this->db->select('firstname,lastname,listid,name,creator,tblstaff.datecreated');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblemaillists.creator', 'left');
        $this->db->from('tblemaillists');

        if (is_numeric($id)) {
            $this->db->where('listid', $id);
            return $this->db->get()->row();
        }

        $lists = $this->db->get()->result_array();
        $i     = 0;
        foreach ($lists as $list) {
            $lists[$i]['creator'] = $list['firstname'] . ' ' . $list['lastname'];
            unset($lists[$i]['firstname']);
            unset($lists[$i]['lastname']);
            $i++;
        }

        return $lists;
    }

    /**
     * Add new mail list
     * @param array $data mail list data
     */
    public function add($data)
    {

        $data['creator']     = get_staff_user_id();
        $data['datecreated'] = date('Y-m-d H:i:s');
        if (isset($data['list_custom_fields_add'])) {
            $custom_fields = $data['list_custom_fields_add'];
            unset($data['list_custom_fields_add']);
        }
        $this->db->insert('tblemaillists', $data);
        $listid = $this->db->insert_id();
        if (isset($custom_fields)) {
            foreach ($custom_fields as $field) {
                if (!empty($field)) {
                    $this->db->insert('tblmaillistscustomfields', array(
                        'listid' => $listid,
                        'fieldname' => $field,
                        'fieldslug' => slug_it($data['name'].'-'.$field)
                    ));
                }
            }
        }

        logActivity('New Email List Added [ID: '.$listid.', ' . $data['name'] . ']');
        return $listid;
    }

    /**
     * Update mail list
     * @param  mixed $data mail list data
     * @param  mixed $id   list id
     * @return boolean
     */
    public function update($data, $id)
    {
        if (isset($data['list_custom_fields_add'])) {
            foreach ($data['list_custom_fields_add'] as $field) {
                if (!empty($field)) {
                    $this->db->insert('tblmaillistscustomfields', array(
                        'listid' => $id,
                        'fieldname' => $field,
                        'fieldslug' => slug_it($field)
                    ));
                }
            }
            unset($data['list_custom_fields_add']);
        }

        if (isset($data['list_custom_fields_update'])) {
            foreach ($data['list_custom_fields_update'] as $key => $update_field) {

                $this->db->where('customfieldid', $key);
                $this->db->update('tblmaillistscustomfields', array(
                    'fieldname' => $update_field,
                    'fieldslug' => slug_it($data['name'].'-'.$update_field)
                ));

            }
            unset($data['list_custom_fields_update']);
        }


        $this->db->where('listid', $id);
        $this->db->update('tblemaillists', $data);

        if ($this->db->affected_rows() > 0) {
            logActivity('Mail List Updated [ID: '.$id.', ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete mail list and all connections
     * @param  mixed $id list id
     * @return boolean
     */
    public function delete($id)
    {
        $affectedRows = 0;

        $this->db->where('listid', $id);
        $this->db->delete('tblmaillistscustomfieldvalues');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        $this->db->where('listid', $id);
        $this->db->delete('tblmaillistscustomfields');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        $this->db->where('listid', $id);
        $this->db->delete('tbllistemails');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        $this->db->where('listid', $id);
        $this->db->delete('tblemaillists');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            logActivity('Mail List Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Get all emails from mail list
     * @param  mixed $id list id
     * @return array
     */
    public function get_mail_list_emails($id)
    {
        $this->db->select('email,emailid')->from('tbllistemails')->where('listid', $id);
        return $this->db->get()->result_array();
    }
    /**
     * List data used in view
     * @param  mixed $id list id
     * @return mixed object
     */
    public function get_data_for_view_list($id)
    {
        $list = $this->get($id);
        $list_emails = $this->db->select('email,dateadded,emailid')->from('tbllistemails')->where('listid', $id)->get()->result_array();
        $list->emails = $list_emails;
        return $list;
    }

    /**
     * Get list custom fields added by staff
     * @param  mixed $listid list id
     * @return array
     */
    public function get_list_custom_fields($id)
    {
        $this->db->where('listid', $id);
        return $this->db->get('tblmaillistscustomfields')->result_array();
    }

    /**
     * Get custom field values
     * @param  mixed $emailid       email id from db
     * @param  mixed $listid        lis id
     * @param  mixed $customfieldid custom field id from db
     * @return mixed
     */
    function get_email_custom_field_value($emailid, $listid, $customfieldid)
    {
        $this->db->where('emailid', $emailid);
        $this->db->where('listid', $listid);
        $this->db->where('customfieldid', $customfieldid);
        $row = $this->db->get('tblmaillistscustomfieldvalues')->row();
        if ($row) {
            return $row->value;
        }
        return '';
    }

    /**
     * Add new email to mail list
     * @param array $data
     * @return mixed
     */
    public function add_email_to_list($data)
    {

        $exists = total_rows('tbllistemails', array(
            'email' => $data['email'],
            'listid' => $data['listid']
        ));
        if ($exists > 0) {
            return array(
                'success' => false,
                'duplicate' => true,
                'error_message' => _l('email_is_duplicate_mail_list')
            );
        }

        $dateadded = date('Y-m-d H:i:s');
        $this->db->insert('tbllistemails', array(
            'listid' => $data['listid'],
            'email' => $data['email'],
            'dateadded' => $dateadded
        ));

        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if(isset($data['customfields'])){
                foreach($data['customfields'] as $key => $val){
                    $this->db->insert('tblmaillistscustomfieldvalues',array(
                        'listid'=>$data['listid'],
                        'customfieldid'=>$key,
                        'emailid'=>$insert_id,
                        'value'=>$val
                        ));
                }
            }
            logActivity('Email Added To Mail List [ID:' . $data['listid'] . ' - Email:' . $data['email'] . ']');
            return array(
                'success' => true,
                'dateadded' => $dateadded,
                'email' => $data['email'],
                'emailid' => $insert_id,
                'message' => _l('email_added_to_mail_list_successfuly')
            );
        }
        return array(
            'success' => false
        );
    }

    /**
     * Remove email from mail list
     * @param  mixed $emailid email id (is unique)
     * @return mixed          array
     */
    public function remove_email_from_mail_list($emailid)
    {

        $this->db->where('emailid', $emailid);
        $this->db->delete('tbllistemails');

        if ($this->db->affected_rows() > 0) {
            $this->db->where('emailid', $emailid);
            $this->db->delete('tblmaillistscustomfieldvalues');
            return array(
                'success' => true,
                'message'=>_l('email_removed_from_list')
            );
        }

        return array(
            'success' => false,
            'message'=>_l('email_remove_fail')
        );
    }

    /**
     * Remove mail list custom field and all connections
     * @param  mixed $fieldid custom field id from db
     * @return mixed          array
     */
    public function remove_list_custom_field($fieldid)
    {

        $this->db->where('customfieldid', $fieldid);
        $this->db->delete('tblmaillistscustomfields');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('customfieldid', $fieldid);
            $this->db->delete('tblmaillistscustomfieldvalues');
            return array(
                'success' => true,
                'message'=>_l('custom_field_deleted_success')
            );
        }

        return array(
            'success' => false,
            'message' => _l('custom_field_deleted_fail')
        );
    }
}
