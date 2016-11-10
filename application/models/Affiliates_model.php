<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/22/16
 * Time: 11:40 AM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Affiliates_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  mixed $id client id (optional)
     * @param  integer $active (optional) get all active or inactive
     * @return mixed
     * Get client object based on passed clientid if not passed clientid return array of all clients
     */
    public function get($id = false, $active = '')
    {


        $this->db->join('tblcountries','tblcountries.country_id = tblaffiliates.country', 'left');

        if (is_int($active)) {
            $this->db->where('active', $active);
        }

        if (is_numeric($id)) {
            $this->db->where('affiliateid', $id);
            $client = $this->db->get('tblaffiliates')->row();

            return $client;
        }

        $this->db->order_by('datecreated', 'desc');
        return $this->db->get('tblaffiliates')->result_array();
    }

    /**
     * @param array $_POST data
     * @return integer Insert ID
     * Add new affiliate to database
     */
    public function add($data)
    {

        // First check for all cases if the email exists.
        $this->db->where('email', $data['email']);
        $email = $this->db->get('tblaffiliates')->row();
        if ($email) {
            die('Email already exists');
        }

        if (isset($data['passwordr'])) {
            unset($data['passwordr']);
        }

        $send_welcome_email = true;
        if (isset($data['donotsendwelcomeemail'])) {
            $send_welcome_email = false;
            unset($data['donotsendwelcomeemail']);
        }

        if(isset($data['custom_fields'])){
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        if(isset($data['send_set_password_email'])){
            $send_set_password_email = true;
            unset($data['send_set_password_email']);
        }

        if(isset($data['groups_in'])){
            $groups_in = $data['groups_in'];
            unset($data['groups_in']);
        }

        if($data['country'] == ''){
            $data['country'] = 0;
        }


        $this->load->helper('phpass');
        $hasher              = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        $data['password']    = $hasher->HashPassword($data['password']);
        $data['datecreated'] = date('Y-m-d H:i:s');

        $data = do_action('before_affiliate_added', $data);

        $this->db->insert('tblaffiliates', $data);
        $affiliateid = $this->db->insert_id();
        if ($affiliateid) {
            if(isset($custom_fields)){
                handle_custom_fields_post($affiliateid,$custom_fields);
            }

            if (isset($groups_in)) {
                foreach ($groups_in as $group) {
                    $this->db->insert('tblaffiliategroups_in', array(
                        'affiliate_id' => $affiliateid,
                        'groupid' => $group
                    ));
                }
            }

            do_action('after_affiliate_added', $affiliateid);
            $_new_affiliate_log = $data['firstname'] . ' ' . $data['lastname'];
            $_is_staff       = NULL;

            if (is_staff_logged_in()) {
                $_new_affiliate_log .= ' From Staff: ' . get_staff_user_id();
                $_is_staff = get_staff_user_id();
            }

            if ($send_welcome_email == true) {
                $this->load->model('emails_model');
                $this->emails_model->send_email_template('new-affiliate-created', $data['email'], $affiliateid, false, false, false);
            }

            if(isset($send_set_password_email)){
                $this->authentication_model->set_password_email($data['email'],0);
            }

            logActivity('New Affiliate Created [' . $_new_affiliate_log . ']', $_is_staff);

        }


        return $affiliateid;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update affiliate informations
     */
    public function update($data, $id,$affiliate_request = false)
    {
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $this->load->helper('phpass');
            $hasher                       = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
            $data['password']             = $hasher->HashPassword($data['password']);
            $data['last_password_change'] = date('Y-m-d H:i:s');
        }

        if(isset($data['update_all_other_transactions'])){
            $update_all_other_transactions = true;
            unset($data['update_all_other_transactions']);
        }

        $affectedRows = 0;
        if(isset($data['custom_fields'])){
            $custom_fields = $data['custom_fields'];
            if(handle_custom_fields_post($id,$custom_fields)){
                $affectedRows++;
            }

            unset($data['custom_fields']);
        }

        // in case client try to manipulate with the disabled field in client side
        if(!is_staff_logged_in() && isset($data['email'])){
            unset($data['email']);
        }


        if(isset($data['groups_in'])){
            $groups_in = $data['groups_in'];
            unset($data['groups_in']);
        }

        if ($data['company'] == '') {
            $data['company'] = NULL;
        }

        if($data['country'] == ''){
            $data['country'] = 0;
        }

        if(isset($data['send_set_password_email'])){
            $send_set_password_email = true;
            unset($data['send_set_password_email']);
        }

        $_data = do_action('before_affiliate_updated', array(
            'affiliateid' => $id,
            'data' => $data
        ));

        $data = $_data['data'];

        $this->db->where('affiliateid', $id);
        $this->db->update('tblaffiliates', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            do_action('after_affiliate_updated', $id);
            $_update_affiliate_log = $data['firstname'] . ' ' . $data['lastname'];
            $_is_staff          = NULL;

            if (is_staff_logged_in()) {
                $_update_affiliate_log .= ' From Staff: ' . get_staff_user_id();
                $_is_staff = get_staff_user_id();
            }
        }



        if(isset($send_set_password_email)){
            $success = $this->authentication_model->set_password_email($data['email'],0);
            if($success){
                $set_password_email_sent = true;
            }

        }

        if(isset($update_all_other_transactions)){


        }

        if($affiliate_request == false){
            
        }



        $affiliate_groups = $this->get_affiliate_groups($id);

        if (sizeof($affiliate_groups) > 0) {
            foreach ($affiliate_groups as $affiliate_group) {
                if (isset($groups_in)) {
                    if (!in_array($affiliate_group['id'], $groups_in)) {
                        $this->db->where('affiliate_id', $id);
                        $this->db->where('id', $affiliate_group['id']);
                        $this->db->delete('tblaffiliategroups_in');
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                } else {
                    $this->db->where('affiliate_id', $id);
                    $this->db->delete('tblaffiliategroups_in');
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            if (isset($groups_in)) {
                foreach ($groups_in as $group) {
                    $this->db->where('affiliate_id', $id);
                    $this->db->where('groupid', $group);
                    $_exists = $this->db->get('tblaffiliategroups_in')->row();
                    if (!$_exists) {
                        if(empty($group)){continue;}
                        $this->db->insert('tblaffiliategroups_in', array(
                            'affiliate_id' => $id,
                            'groupid' => $group
                        ));

                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
            }
        } else {
            if (isset($groups_in)) {
                foreach ($groups_in as $group) {
                    if(empty($group)){continue;}
                    $this->db->insert('tblaffiliategroups_in', array(
                        'affiliate_id' => $id,
                        'groupid' => $group
                    ));
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
        }


        if($affectedRows > 0 && !isset($set_password_email_sent)){
            logActivity('Affiliate Info Updated [' . $_update_affiliate_log . ']', $_is_staff);
            return true;
        } else if($affectedRows > 0 && isset($set_password_email_sent)){
            return array('set_password_email_sent_and_profile_updated'=>true);
        } else if($affectedRows == 0 && isset($set_password_email_sent)){
            return array('set_password_email_sent'=>true);
        }

        return false;
    }

    /**
     * Delete lead from database and all connections
     * @param  mixed $id leadid
     * @return boolean
     */
    public function delete($id)
    {
        $affectedRows = 0;
        if (is_reference_in_table('affiliateid', 'tblclients', $id)) {
            return array(
                'referenced' => true
            );
        }

        do_action('before_affiliate_deleted', $id);
        $this->db->where('id', $id);
        $this->db->delete('tblaffiliates');

        if ($this->db->affected_rows() > 0) {
            logActivity('Affiliate Deleted [Deleted by: ' . get_staff_full_name() . '(ID: ' . get_staff_user_id() . ') Affiliate ID: ' . $id . ']');

            // Delete the custom field values
            $this->db->where('relid',$id);
            $this->db->where('fieldto','affiliates');
            $this->db->delete('tblcustomfieldsvalues');
            $affectedRows++;
        }


        if ($affectedRows > 0) {
            logActivity('Affiliate Deleted [LeadID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update affiliate status Active/Inactive
     */
    public function change_affiliate_status($id, $status)
    {
        $this->db->where('affiliateid', $id);
        $this->db->update('tblaffiliates', array(
            'active' => $status
        ));

        if ($this->db->affected_rows() > 0) {
            logActivity('Affiliate Status Changed [ClientID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');
            return true;
        }

        return false;
    }

    public function get_affiliate_groups($id){
        $this->db->where('affiliate_id',$id);
        return $this->db->get('tblaffiliategroups_in')->result_array();
    }

    public function get_groups($id = ''){

        if(is_numeric($id)){
            $this->db->where('id',$id);
            return $this->db->get('tblaffiliatesgroups')->row();
        }

        return $this->db->get('tblaffiliatesgroups')->result_array();
    }

    public function delete_group($id){

        if(is_reference_in_table('groupid','tblaffiliategroups_in',$id)){
            return array('referenced'=>true);
        }

        $this->db->where('id',$id);
        $this->db->delete('tblaffiliatesgroups');

        if($this->db->affected_rows() > 0){
            logActivity('Affiliate Group Delete [ID:'.$id.']');
            return true;
        }

        return false;

    }

    public function add_group($data){
        $this->db->insert('tblaffiliatesgroups',$data);
        $insert_id = $this->db->insert_id();

        if($insert_id){
            logActivity('New Affiliate Group Created [ID:'.$insert_id.', Name:'.$data['name'].']');
            return true;
        }

        return false;
    }

    public function edit_group($data){
        $this->db->where('id',$data['id']);
        $this->db->update('tblaffiliatesgroups',array('name'=>$data['name']));
        if($this->db->affected_rows() > 0){
            logActivity('Affiliate Group Updated [ID:'.$data['id'].']');
            return true;
        }
        return false;
    }

    /**
     *  Get customer attachment
     * @param   mixed $id   customer id
     * @return  array
     */
    public function get_all_affiliate_attachments($id){

        $attachments = array();
        $attachments['affiliate'] = array();


        $this->db->where('affiliateid',$id);
        $affiliate_main_attachments = $this->db->get('tblaffiliateattachments')->result_array();
        $attachments['affiliate'] = $affiliate_main_attachments;

        return $attachments;
    }
    public function delete_attachment($id){
        $this->db->where('affiliateid',$id);
        $attachment = $this->db->get('tblaffiliateattachments')->row();
        if($attachment){
            if(unlink(AFFILIATE_ATTACHMENTS_FOLDER . $attachment->affiliateid . '/' . $attachment->file_name)){
                $this->db->where('id',$id);
                $this->db->delete('tblaffiliateattachments');

                return true;
            }

            // Check if no attachments left, so we can delete the folder also
            $other_attachments = list_files(AFFILIATE_ATTACHMENTS_FOLDER . $attachment->affiliateid);
            if(count($other_attachments) == 0){
                delete_dir(AFFILIATE_ATTACHMENTS_FOLDER . $attachment->affiliateid);
            }
        }

        return false;
    }
}