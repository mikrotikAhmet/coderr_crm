<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clients_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }

     /**
     * With this function staff can login as client in the clients area
     * @param  mixed $id client id
     */
    public function login_as_client($id)
    {
        $this->load->model('clients_model');
        $client = $this->clients_model->get($id);

        $user_data = array(
            'client_user_id' => $client->userid,
            'client_logged_in' => true,
            'logged_in_as_client' => true,
        );

        $this->session->set_userdata($user_data);
        redirect(site_url());
    }

    /**
     * @param  mixed $id client id (optional)
     * @param  integer $active (optional) get all active or inactive
     * @return mixed
     * Get client object based on passed clientid if not passed clientid return array of all clients
     */
    public function get($id = false, $active = '')
    {


        $this->db->join('tblcountries','tblcountries.country_id = tblclients.country', 'left');

        if (is_int($active)) {
            $this->db->where('active', $active);
        }

        if (is_numeric($id)) {
            $this->db->where('userid', $id);
            $client = $this->db->get('tblclients')->row();

            return $client;
        }

        $this->db->order_by('datecreated', 'desc');
        return $this->db->get('tblclients')->result_array();
    }


    /**
     * @param array $_POST data
     * @return integer Insert ID
     * Add new client to database
     */
    public function add($data)
    {

       // First check for all cases if the email exists.
        $this->db->where('email', $data['email']);
        $email = $this->db->get('tblclients')->row();
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

         if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
            unset($data['permissions']);
        }

        if($data['country'] == ''){
            $data['country'] = 0;
        }


        $this->load->helper('phpass');
        $hasher              = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        $data['password']    = $hasher->HashPassword($data['password']);
        $data['datecreated'] = date('Y-m-d H:i:s');

        $data = do_action('before_client_added', $data);

        $this->db->insert('tblclients', $data);
        $userid = $this->db->insert_id();
        if ($userid) {
            if(isset($custom_fields)){
                handle_custom_fields_post($userid,$custom_fields);
            }

             if (isset($permissions)) {
                foreach ($permissions as $permission) {
                    $this->db->insert('tblcustomerpermissions', array(
                        'userid' => $userid,
                        'permission_id' => $permission
                    ));
                }
            }

            if (isset($groups_in)) {
                foreach ($groups_in as $group) {
                    $this->db->insert('tblcustomergroups_in', array(
                        'customer_id' => $userid,
                        'groupid' => $group
                    ));
                }
            }

             // Get all announcements and set it to read.
            $this->db->select('announcementid');
            $this->db->from('tblannouncements');
            $this->db->where('showtousers',1);
            $announcements = $this->db->get()->result_array();

            foreach($announcements as $announcement){
                $this->db->insert('tbldismissedannouncements',array(
                    'announcementid'=>$announcement['announcementid'],
                    'staff'=>1,
                    'userid'=>$userid
                    ));
            }

            do_action('after_client_added', $userid);
            $_new_client_log = $data['firstname'] . ' ' . $data['lastname'];
            $_is_staff       = NULL;

            if (is_staff_logged_in()) {
                $_new_client_log .= ' From Staff: ' . get_staff_user_id();
                $_is_staff = get_staff_user_id();
            }

            if ($send_welcome_email == true) {
                $this->load->model('emails_model');
                $this->emails_model->send_email_template('new-client-created', $data['email'], $userid, false, false, false);
            }

            if(isset($send_set_password_email)){
                $this->authentication_model->set_password_email($data['email'],0);
            }

            logActivity('New Client Created [' . $_new_client_log . ']', $_is_staff);

        }


        return $userid;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update client informations
     */
    public function update($data, $id,$client_request = false)
    {
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $this->load->helper('phpass');
            $hasher                       = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
            $data['password']             = $hasher->HashPassword($data['password']);
            $data['last_password_change'] = date('Y-m-d H:i:s');
        }

         $permissions = array();
        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
            unset($data['permissions']);
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

        $_data = do_action('before_client_updated', array(
            'userid' => $id,
            'data' => $data
            ));

        $data = $_data['data'];

        $this->db->where('userid', $id);
        $this->db->update('tblclients', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            do_action('after_client_updated', $id);
            $_update_client_log = $data['firstname'] . ' ' . $data['lastname'];
            $_is_staff          = NULL;

            if (is_staff_logged_in()) {
                $_update_client_log .= ' From Staff: ' . get_staff_user_id();
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
            // Update all unpaid invoices
        $this->db->where('clientid',$id);
        $this->db->where('status !=',2);
        $invoices = $this->db->get('tblinvoices')->result_array();
        foreach($invoices as $invoice){
            $this->db->where('id',$invoice['id']);
            $this->db->update('tblinvoices',array(
                'billing_street'=>$data['billing_street'],
                'billing_city'=>$data['billing_city'],
                'billing_state'=>$data['billing_state'],
                'billing_zip'=>$data['billing_zip'],
                'billing_country'=>$data['billing_country'],
                'shipping_street'=>$data['shipping_street'],
                'shipping_city'=>$data['shipping_city'],
                'shipping_state'=>$data['shipping_state'],
                'shipping_zip'=>$data['shipping_zip'],
                'shipping_country'=>$data['shipping_country'],
                ));
        }

            // Update all estimates
        $this->db->where('clientid',$id);
        $estimates = $this->db->get('tblestimates')->result_array();
        foreach($estimates as $estimate){
            $this->db->where('id',$estimate['id']);
            $this->db->update('tblestimates',array(
                'billing_street'=>$data['billing_street'],
                'billing_city'=>$data['billing_city'],
                'billing_state'=>$data['billing_state'],
                'billing_zip'=>$data['billing_zip'],
                'billing_country'=>$data['billing_country'],
                'shipping_street'=>$data['shipping_street'],
                'shipping_city'=>$data['shipping_city'],
                'shipping_state'=>$data['shipping_state'],
                'shipping_zip'=>$data['shipping_zip'],
                'shipping_country'=>$data['shipping_country'],
                ));
        }
    }

        if($client_request == false){
            $customer_permissions = $this->roles_model->get_customer_permissions($id);
            if (sizeof($customer_permissions) > 0) {
                foreach ($customer_permissions as $customer_permission) {
                    if (!in_array($customer_permission['permission_id'], $permissions)) {
                        $this->db->where('userid', $id);
                        $this->db->where('permission_id', $customer_permission['permission_id']);
                        $this->db->delete('tblcustomerpermissions');
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
                foreach ($permissions as $permission) {
                    $this->db->where('userid', $id);
                    $this->db->where('permission_id', $permission);
                    $_exists = $this->db->get('tblcustomerpermissions')->row();

                    if (!$_exists) {
                        $this->db->insert('tblcustomerpermissions', array(
                            'userid' => $id,
                            'permission_id' => $permission
                        ));

                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
            } else {
                foreach ($permissions as $permission) {
                    $this->db->insert('tblcustomerpermissions', array(
                        'userid' => $id,
                        'permission_id' => $permission
                    ));
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            }



        $customer_groups = $this->get_customer_groups($id);

        if (sizeof($customer_groups) > 0) {
            foreach ($customer_groups as $customer_group) {
                if (isset($groups_in)) {
                    if (!in_array($customer_group['id'], $groups_in)) {
                        $this->db->where('customer_id', $id);
                        $this->db->where('id', $customer_group['id']);
                        $this->db->delete('tblcustomergroups_in');
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                } else {
                     $this->db->where('customer_id', $id);
                     $this->db->delete('tblcustomergroups_in');
                       if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                }
            }
            if (isset($groups_in)) {
                foreach ($groups_in as $group) {
                    $this->db->where('customer_id', $id);
                    $this->db->where('groupid', $group);
                    $_exists = $this->db->get('tblcustomergroups_in')->row();
                    if (!$_exists) {
                        if(empty($group)){continue;}
                        $this->db->insert('tblcustomergroups_in', array(
                            'customer_id' => $id,
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
                    $this->db->insert('tblcustomergroups_in', array(
                        'customer_id' => $id,
                        'groupid' => $group
                    ));
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
        }


    if($affectedRows > 0 && !isset($set_password_email_sent)){
        logActivity('Client Info Updated [' . $_update_client_log . ']', $_is_staff);
        return true;
    } else if($affectedRows > 0 && isset($set_password_email_sent)){
        return array('set_password_email_sent_and_profile_updated'=>true);
    } else if($affectedRows == 0 && isset($set_password_email_sent)){
        return array('set_password_email_sent'=>true);
    }

    return false;
}

    /**
     * @param  integer ID
     * @return boolean
     * Delete client, also deleting rows from, dismissed client announcements, ticket replies, tickets, autologin, user notes
     */
    public function delete($id)
    {

        $affectedRows = 0;
        do_action('before_client_deleted', $id);

        if (is_reference_in_table('clientid', 'tblinvoices', $id) || is_reference_in_table('clientid', 'tblestimates', $id)) {
            set_alert('warning', _l('client_delete_invoices_warning'));
            redirect(admin_url('clients/client/' . $id),'refresh');
        }


        $this->db->where('userid', $id);
        $this->db->delete('tblclients');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;

            $this->db->where('userid', $id);
            $this->db->where('staff', 0);
            $this->db->delete('tbldismissedannouncements');



            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }

            // Delete all tickets start here
            $this->db->where('userid', $id);
            $tickets = $this->db->get('tbltickets')->result_array();
            $this->load->model('tickets_model');
            foreach ($tickets as $ticket) {
                $deleted = $this->tickets_model->delete($ticket['ticketid']);
                if ($deleted) {
                    $affectedRows++;
                }
            }

            // Delete autologin if found
            $this->db->where('user_id', $id);
            $this->db->where('staff', 0);
            $this->db->delete('tbluserautologin');

            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }

            // Delete client admin notes
            $this->db->where('userid', $id);
            $this->db->where('staff', 0);
            $this->db->delete('tbluseradminnotes');

            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }

            // Delete contracts
            $this->load->model('contracts_model');
            // Get all client contracts
            $this->db->where('client', $id);
            $contracts = $this->db->get('tblcontracts')->result_array();

            // Delete the custom field values
            $this->db->where('relid',$id);
            $this->db->where('fieldto','customers');
            $this->db->delete('tblcustomfieldsvalues');

            foreach ($contracts as $contract) {
                $deleted = $this->contracts_model->delete($contract['id']);
                if ($deleted) {
                    $affectedRows++;
                }
            }
        }

        if ($affectedRows > 0) {
            do_action('after_client_deleted');
            logActivity('Client Deleted [' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Get customer default currency
     * @param  mixed $id customer id
     * @return mixed
     */
    public function get_customer_default_currency($id){
        $this->db->where('userid',$id);
        return $this->db->get('tblclients')->row()->default_currency;
    }
    /**
     *  Get customer billing details
     * @param   mixed $id   customer id
     * @return  array
     */
    public function get_customer_billing_and_shipping_details($id){
        $this->db->select('billing_street,billing_city,billing_state,billing_zip,billing_country,shipping_street,shipping_city,shipping_state,shipping_zip,shipping_country');
        $this->db->from('tblclients');
        $this->db->where('userid',$id);
        return $this->db->get()->result_array();
    }
    /**
     *  Get customer attachment
     * @param   mixed $id   customer id
     * @return  array
     */
    public function get_all_customer_attachments($id){

        $attachments = array();
        $attachments['invoices'] = array();
        $attachments['contracts'] = array();
        $attachments['leads'] = array();
        $attachments['tickets'] = array();
        $attachments['tasks'] = array();
        $attachments['customer'] = array();
        // Invoices
        $this->db->select('clientid,id');
        $this->db->where('clientid',$id);
        $this->db->from('tblinvoices');
        $invoices = $this->db->get()->result_array();
        foreach($invoices as $invoice){
            $this->db->where('invoiceid',$invoice['id']);
            $_attachments = $this->db->get('tblinvoiceattachments')->result_array();
            if(count($_attachments) > 0){
                foreach($_attachments as $_att){
                    array_push($attachments['invoices'],$_att);
                }
            }
        }

        $this->db->select('client,id');
        $this->db->where('client',$id);
        $this->db->from('tblcontracts');
        $contracts = $this->db->get()->result_array();
        foreach($contracts as $contract){
            $this->db->where('contractid',$contract['id']);
            $_attachments = $this->db->get('tblcontractattachments')->result_array();
            if(count($_attachments) > 0){
                foreach($_attachments as $_att){
                    array_push($attachments['contracts'],$_att);
                }
            }
        }

        $customer = $this->get($id);
        if($customer->leadid != NULL){
          $this->db->where('leadid',$customer->leadid);
          $_attachments =$this->db->get('tblleadattachments')->result_array();
          if(count($_attachments) > 0){
            foreach($_attachments as $_att){
                array_push($attachments['leads'],$_att);
            }
        }
    }

    $this->db->select('ticketid,userid');
    $this->db->where('userid',$id);
    $this->db->from('tbltickets');
    $tickets = $this->db->get()->result_array();

    foreach($tickets as $ticket){
        $this->db->where('ticketid',$ticket['ticketid']);
        $_attachments = $this->db->get('tblticketattachments')->result_array();
        if(count($_attachments) > 0){
           foreach($_attachments as $_att){
            array_push($attachments['tickets'],$_att);
        }
    }
}


$this->db->select('rel_id,id');
$this->db->where('rel_id',$id);
$this->db->where('rel_type','customer');
$this->db->from('tblstafftasks');
$tasks = $this->db->get()->result_array();

foreach($tasks as $task){
    $this->db->where('taskid',$task['id']);
    $_attachments = $this->db->get('tblstafftasksattachments')->result_array();
    if(count($_attachments) > 0){
       foreach($_attachments as $_att){
          array_push($attachments['tasks'],$_att);
      }
  }
}

$this->db->where('clientid',$id);
$client_main_attachments = $this->db->get('tblclientattachments')->result_array();
$attachments['client'] = $client_main_attachments;

return $attachments;
}
public function delete_attachment($id){
    $this->db->where('id',$id);
    $attachment = $this->db->get('tblclientattachments')->row();
    if($attachment){
        if(unlink(CLIENT_ATTACHMENTS_FOLDER . $attachment->clientid . '/' . $attachment->file_name)){
            $this->db->where('id',$id);
            $this->db->delete('tblclientattachments');

            return true;
        }

          // Check if no attachments left, so we can delete the folder also
        $other_attachments = list_files(CLIENT_ATTACHMENTS_FOLDER . $attachment->clientid);
        if(count($other_attachments) == 0){
            delete_dir(CLIENT_ATTACHMENTS_FOLDER . $attachment->clientid);
        }
    }

    return false;
}

    /**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update client status Active/Inactive
     */
    public function change_client_status($id, $status)
    {
        $this->db->where('userid', $id);
        $this->db->update('tblclients', array(
            'active' => $status
            ));

        if ($this->db->affected_rows() > 0) {
            logActivity('Client Status Changed [ClientID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');
            return true;
        }

        return false;
    }
    /**
     * @param  mixed $_POST data
     * @return mixed
     * Change client password, used from client area
     */
    public function change_client_password($data)
    {
        // Get current password
        $this->db->where('userid', get_client_user_id());
        $client = $this->db->get('tblclients')->row();

        $this->load->helper('phpass');
        $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        if (!$hasher->CheckPassword($data['oldpassword'], $client->password)) {
            logActivity('Client Password Not Match [ClientID: ' . get_client_user_id() . ']');
            return array(
                'old_password_not_match' => true
                );
        }

        $update_data['password']             = $hasher->HashPassword($data['password']);
        $update_data['last_password_change'] = date('Y-m-d H:i:s');
        $this->db->where('userid', get_client_user_id());
        $this->db->update('tblclients', $update_data);

        if ($this->db->affected_rows() > 0) {
            logActivity('Client Password Changed [ClientID: ' . get_client_user_id() . ']');
            return true;
        }

        return false;
    }

    public function get_customer_groups($id){
        $this->db->where('customer_id',$id);
        return $this->db->get('tblcustomergroups_in')->result_array();
    }

    public function get_groups($id = ''){

        if(is_numeric($id)){
            $this->db->where('id',$id);
            return $this->db->get('tblcustomersgroups')->row();
        }

        return $this->db->get('tblcustomersgroups')->result_array();
    }

    public function delete_group($id){

        if(is_reference_in_table('groupid','tblcustomergroups_in',$id)){
            return array('referenced'=>true);
        }

        $this->db->where('id',$id);
        $this->db->delete('tblcustomersgroups');

        if($this->db->affected_rows() > 0){
            logActivity('Customer Group Delete [ID:'.$id.']');
            return true;
        }

        return false;

    }

    public function add_group($data){
        $this->db->insert('tblcustomersgroups',$data);
        $insert_id = $this->db->insert_id();

        if($insert_id){
            logActivity('New Customer Group Created [ID:'.$insert_id.', Name:'.$data['name'].']');
            return true;
        }

        return false;
    }

    public function edit_group($data){
        $this->db->where('id',$data['id']);
        $this->db->update('tblcustomersgroups',array('name'=>$data['name']));
        if($this->db->affected_rows() > 0){
            logActivity('Customer Group Updated [ID:'.$data['id'].']');
            return true;
        }
        return false;
    }
}
