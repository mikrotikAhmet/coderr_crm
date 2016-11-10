<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leads_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /**
     * Get lead
     * @param  string $id Optional - leadid
     * @return mixed
     */

    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblleads')->row();
        }

        return $this->db->get('tblleads')->result_array();
    }

    /**
     * Add new lead to database
     * @param mixed $data lead data
     * @return mixed false || leadid
     */
    public function add($data)
    {

        if (isset($data['contacted_today'])) {
            $data['lastcontact'] = date('Y-m-d H:i:s');
            unset($data['contacted_today']);
        } else {
            $data['lastcontact'] = to_sql_date($data['custom_contact_date']);
        }

        if(isset($data['custom_fields'])){
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }


         if(isset($data['is_public'])){
            $data['is_public'] = 1;
            } else {
                $data['is_public'] = 0;
            }


        unset($data['custom_contact_date']);
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();
        $data = do_action('before_lead_added', $data);

         if(isset($data['custom_fields'])){
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $this->db->insert('tblleads', $data);
        $insert_id = $this->db->insert_id();


        if ($insert_id) {

            logActivity('New Lead Added [LeadID: ' . $insert_id . ']');
            $this->log_lead_activity($insert_id, get_staff_full_name() . ' Created Lead');
            if(isset($custom_fields)){
                handle_custom_fields_post($insert_id,$custom_fields);
            }

            if ((!empty($data['assigned']) && $data['assigned'] != 0) && $data['assigned'] != get_staff_user_id()) {
                add_notification(array(
                    'description' => get_staff_full_name(get_staff_user_id()) . ' assigned lead ' . $data['name'] . ' to you.',
                    'touserid' => $data['assigned'],
                    'link'=>'leads/lead/'.$insert_id
                ));

                $this->log_lead_activity($insert_id, get_staff_full_name() . ' assigned to <a href="' . admin_url('profile/' . $data['assigned']) . '">' . get_staff_full_name($data['assigned']) . '</a>');
            }

            return $insert_id;
        }
        return false;
    }

    /**
     * Update lead
     * @param  array $data lead data
     * @param  mixed $id   leadid
     * @return boolean
     */
    public function update($data, $id)
    {

        $current_lead_data = $this->get($id);
        $current_status    = $this->get_status($current_lead_data->status)->name;
        $current_status_id = $this->get_status($current_lead_data->status)->id;

       $affectedRows = 0;
       if(isset($data['custom_fields'])){
            $custom_fields = $data['custom_fields'];
            if(handle_custom_fields_post($id,$custom_fields)){
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

         if(isset($data['is_public'])){
            $data['is_public'] = 1;
            } else {
                $data['is_public'] = 0;
            }

        $this->db->where('id', $id);
        $this->db->update('tblleads', $data);


        if ($this->db->affected_rows() > 0) {
            $affectedRows++;

            if ($current_status_id != $data['status']) {
                $this->db->where('id', $id);
                $this->db->update('tblleads', array(
                    'last_status_change' => date('Y-m-d H:i:s')
                ));

                $new_status_name = $this->get_status($data['status'])->name;
                $this->log_lead_activity($id, '<span class="bold">' . get_staff_full_name() . '</span> updated lead status from <span class="bold">' . $current_status . '</span> to <span class="bold">' . $new_status_name . '</span>');

            }

            if ($current_lead_data->assigned != $data['assigned'] && (!empty($data['assigned']) && $data['assigned'] != 0)) {
                if ($data['assigned'] != get_staff_user_id()) {
                    add_notification(array(
                        'description' => get_staff_full_name(get_staff_user_id()) . ' assigned lead ' . $data['name'] . ' to you.',
                        'touserid' => $data['assigned'],
                        'link'=>'leads/lead/' . $id
                    ));

                    $this->log_lead_activity($id, get_staff_full_name() . ' assigned to <a href="' . admin_url('profile/' . $data['assigned']) . '">' . get_staff_full_name($data['assigned']) . '</a>');

                }
            }

            logActivity('Lead Updated [LeadID: ' . $id . ']');
            return true;
        }

        if($affectedRows > 0){
            return true;
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
         if (is_reference_in_table('leadid', 'tblclients', $id)) {
            return array(
                'referenced' => true
                );
        }

        do_action('before_lead_deleted', $id);
        $this->db->where('id', $id);
        $this->db->delete('tblleads');

        if ($this->db->affected_rows() > 0) {
            logActivity('Lead Deleted [Deleted by: ' . get_staff_full_name() . '(ID: ' . get_staff_user_id() . ') LeadID: ' . $id . ']');

            $attachments = $this->get_lead_attachments($id);
            foreach($attachments as $attachment){
                $this->delete_lead_attachment($attachment['id']);
            }

            // Delete the custom field values
            $this->db->where('relid',$id);
            $this->db->where('fieldto','leads');
            $this->db->delete('tblcustomfieldsvalues');
            $affectedRows++;
        }

        $this->db->where('leadid', $id);
        $this->db->delete('tblleadactivitylog');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        $this->db->where('leadid',$id);
        $this->db->delete('tblleadsemailintegrationemails');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        $this->db->where('leadid', $id);
        $this->db->delete('tblleadnotes');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }


        if ($affectedRows > 0) {
            logActivity('Lead Deleted [LeadID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Mark lead as lost
     * @param  mixed $id lead id
     * @return boolean
     */
    public function mark_as_lost($id){
        $this->db->where('id',$id);
        $this->db->update('tblleads',array('lost'=>1,'status'=>0));
        if($this->db->affected_rows() > 0){
            $this->log_lead_activity($id,'Marked as Lost');
            logActivity('Lead Marked as Lost [LeadID: ' . $id . ']');
            return true;
        }

        return false;
    }
    /**
     * Unmark lead as lost
     * @param  mixed $id leadid
     * @return boolean
     */
    public function unmark_as_lost($id){
        $this->db->where('id',$id);
        $this->db->update('tblleads',array('lost'=>0));
        if($this->db->affected_rows() > 0){
            $this->log_lead_activity($id,'Unmarked as Lost');
            logActivity('Lead Unmarked as Lost [LeadID: ' . $id . ']');
            return true;
        }
        return false;
    }


    /**
     * Mark lead as junk
     * @param  mixed $id lead id
     * @return boolean
     */
    public function mark_as_junk($id){
        $this->db->where('id',$id);
        $this->db->update('tblleads',array('junk'=>1,'status'=>0));
        if($this->db->affected_rows() > 0){
            $this->log_lead_activity($id,'Marked as Junk');
            logActivity('Lead Marked as Junk [LeadID: ' . $id . ']');
            return true;
        }

        return false;
    }
    /**
     * Unmark lead as junk
     * @param  mixed $id leadid
     * @return boolean
     */
    public function unmark_as_junk($id){

        $this->db->where('id',$id);
        $this->db->update('tblleads',array('junk'=>0));
        if($this->db->affected_rows() > 0){
            $this->log_lead_activity($id,'Unmarked as Junk');
            logActivity('Lead Unmarked as Junk [LeadID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Get lead attachments
     * @since Version 1.0.4
     * @param  mixed $id lead id
     * @return array
     */
    public function get_lead_attachments($id = '',$attachment_id = ''){

        if(is_numeric($attachment_id)){
            $this->db->where('id',$attachment_id);
            return $this->db->get('tblleadattachments')->row();
        }
        $this->db->where('leadid',$id);
        $this->db->order_by('dateadded','DESC');
        return $this->db->get('tblleadattachments')->result_array();
    }
    /**
     * Delete lead attachment
     * @param  mixed $id attachment id
     * @return boolean
     */
    public function delete_lead_attachment($id){
            $attachment =  $this->get_lead_attachments('',$id);
            if($attachment){
            if(unlink(LEAD_ATTACHMENTS_FOLDER . $attachment->leadid . '/' . $attachment->file_name)){
                $this->db->where('id',$id);
                $this->db->delete('tblleadattachments');
                $other_attachments = list_files(LEAD_ATTACHMENTS_FOLDER . $attachment->leadid);
                if(count($other_attachments) == 0){
                // delete the dir if no other attachments found
                    delete_dir(LEAD_ATTACHMENTS_FOLDER . $attachment->leadid);
                }

                 $this->log_lead_activity($attachment->leadid,'Delete attachment');
            }
            return true;
        }

        return false;
        }

    public function delete_lead_note($id){
        $this->db->where('id',$id);
        $this->db->delete('tblleadnotes');
        if($this->db->affected_rows() > 0){
            return true;
        }

        return false;
    }
    // Sources
    /**
     * Get leads sources
     * @param  mixed $id Optional - Source ID
     * @return mixed object if id passed else array
     */
    public function get_source($id = false)
    {

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $source =  $this->db->get('tblleadssources')->row();

            if (!$source && $id == 99999){

                $source = (object)array(
                    'id'=>99999,
                    'name'=>'Affiliate'
                );

            }

            return $source;
        }

        $sources = $this->db->get('tblleadssources')->result_array();


        /*
         * Check if there are Affiliates
         * and add Affiliate as a source
         */

        $total_affiliate = total_rows('tblaffiliates');

        if ($total_affiliate){

            $affiliate[] = array(
                'id'=>99999,
                'name'=>'Affiliate'
            );


            $sources = array_merge($sources,$affiliate);
        }


        return $sources;
    }

    /**
     * Add new lead source
     * @param mixed $data source data
     */
    public function add_source($data)
    {
        $this->db->insert('tblleadssources', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            logActivity('New Leads Source Added [SourceID: ' . $insert_id . ', Name: '.$data['name'].']');
        }

        return $insert_id;
    }

    /**
     * Update lead source
     * @param  mixed $data source data
     * @param  mixed $id   source id
     * @return boolean
     */
    public function update_source($data, $id)
    {

        $this->db->where('id', $id);
        $this->db->update('tblleadssources', $data);

        if ($this->db->affected_rows() > 0) {
            logActivity('Leads Source Updated [SourceID: ' . $id . ', Name: '.$data['name'].']');
            return true;
        }

        return false;
    }

    /**
     * Delete lead source from database
     * @param  mixed $id source id
     * @return mixed
     */
    public function delete_source($id)
    {

        $current = $this->get_source($id);
        // Check if is already using in table
        if (is_reference_in_table('source', 'tblleads', $id)) {
            return array(
                'referenced' => true
            );
        }
        $this->db->where('id', $id);
        $this->db->delete('tblleadssources');

        if ($this->db->affected_rows() > 0) {
            logActivity('Leads Source Deleted [LeadID: ' . $id . ']');
            return true;
        }

        return false;
    }

    // Statuses
    /**
     * Get lead statuses
     * @param  mixed $id status id
     * @return mixed      object if id passed else array
     */
    public function get_status($id = '',$default = false)
    {
        if($default == true){
            $this->db->where('isdefault',1);
            return $this->db->get('tblleadsstatus')->row();
        }
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblleadsstatus')->row();
        }
        $this->db->order_by('statusorder', 'asc');
        return $this->db->get('tblleadsstatus')->result_array();
    }

    /**
     * Add new lead status
     * @param array $data lead status data
     */
    public function add_status($data)
    {
        $this->db->insert('tblleadsstatus', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            logActivity('New Leads Status Added [StatusID: ' . $insert_id . ', Name: '.$data['name'].']');
            return $insert_id;
        }

       return false;
    }

    public function update_status($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblleadsstatus', $data);

        if ($this->db->affected_rows() > 0) {
            logActivity('Leads Status Updated [StatusID: ' . $id . ', Name: '.$data['name'].']');
            return true;
        }

        return false;
    }

    /**
     * Delete lead status from database
     * @param  mixed $id status id
     * @return boolean
     */
    public function delete_status($id)
    {

        $current = $this->get_status($id);
        // Check if is already using in table
        if (is_reference_in_table('status', 'tblleads', $id)) {
            return array(
                'referenced' => true
            );
        }
        $this->db->where('id', $id);
        $this->db->delete('tblleadsstatus');

        if ($this->db->affected_rows() > 0) {
            logActivity('Leads Status Deleted [StatusID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Update canban lead status when drag and drop
     * @param  array $data lead data
     * @return boolean
     */
    public function update_can_ban_lead_status($data)
    {

        if(total_rows('tblclients',array('leadid'=>$data['leadid'])) > 0){
            return array('is_converted_to_client'=>true);
        }

        $affectedRows = 0;
        $current_status = $this->get_status($data['status'])->name;
        $old_status     = $this->get_status($data['old_status'])->name;

        $this->db->where('id', $data['leadid']);
        $this->db->update('tblleads', array(
            'status' => $data['status']
        ));

        $_log_message = '';
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            if($current_status != $old_status){
              $_log_message = '<span class="bold">' . get_staff_full_name() . '</span> updated lead status from <span class="bold">' . $old_status . '</span> to <span class="bold">' . $current_status . '</span>';
            }
            $this->db->where('id', $data['leadid']);
            $this->db->update('tblleads', array(
                'last_status_change' => date('Y-m-d H:i:s')
            ));
        }
        foreach($data['order'] as $order_data){
            $this->db->where('id',$order_data[0]);
            $this->db->update('tblleads',array('leadorder'=>$order_data[1]));
        }

        if($affectedRows > 0){
            if($_log_message == ''){
                return true;
            }
            $this->log_lead_activity($data['leadid'], $_log_message);
            return true;
        }
        return false;
    }
    /**
     * Add lead note
     * @param mixed $data note data including lead ID
     */
    public function add_note($data)
    {
        if($data['contacted_indicator'] == 'yes'){
            $contacted_date = to_sql_date($data['custom_contact_date']);
        }

        unset($data['contacted_indicator']);
        unset($data['custom_contact_date']);
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['staffid'] =get_staff_user_id();
        $data['description'] = nl2br($data['description']);

        $this->db->insert('tblleadnotes',$data);
        $insert_id = $this->db->insert_id();
        if($insert_id){
            if(isset($contacted_date)){
                $this->db->where('id',$data['leadid']);
                $this->db->update('tblleads',array('lastcontact'=>$contacted_date));
                if($this->db->affected_rows() > 0){
                 $this->log_lead_activity($data['leadid'], '<span class="bold">' . get_staff_full_name() . '</span> contacted this lead on '._d($contacted_date));
                }
            }
            return $insert_id;
        }

        return false;
    }

    /**
     * Get all lead notes added by staff member
     * @param  [type] $id lead id
     * @return array notes
     */

    public function get_lead_notes($id,$limit = ''){
        $this->db->where('leadid',$id);
           if(is_numeric($limit)){
                $this->db->limit($limit);
           }
        $this->db->order_by('dateadded','DESC');
        return $this->db->get('tblleadnotes')->result_array();
    }

    /* Ajax */

    /**
     * All lead activity by staff
     * @param  mixed $id lead id
     * @return array
     */
    public function get_lead_activity_log($id)
    {
        $this->db->where('leadid', $id);
        $this->db->order_by('date','ASC');
        return $this->db->get('tblleadactivitylog')->result_array();
    }

    /**
     * Add lead activity from staff
     * @param  mixed  $id          lead id
     * @param  string  $description activity description
     * @param  mixed $noteid      if log is from note
     */
    public function log_lead_activity($id, $description, $noteid = '', $integration = false)
    {
        $log = array(
            'date' => date('Y-m-d H:i:s'),
            'description' => $description,
            'leadid' => $id,
            'staffid' => get_staff_user_id()
        );

        if($integration == true){
            $log['staffid'] = 0;
        }

        if (is_numeric($noteid)) {
            $log['noteid'] = $noteid;
        }

        $this->db->insert('tblleadactivitylog', $log);
    }

    /**
     * Get email integration config
     * @return object
     */
    public function get_email_integration(){
        $this->db->where('id',1);
        return $this->db->get('tblleadsemailintegration')->row();
    }

    /**
     * Get lead imported email activity
     * @param  mixed $id leadid
     * @return array
     */
    public function get_mail_activity($id){
        $this->db->where('leadid',$id);
        $this->db->order_by('dateadded','asc');
        return $this->db->get('tblleadsemailintegrationemails')->result_array();
    }

    /**
     * Update email integration config
     * @param  mixed $data All $_POST data
     * @return boolean
     */
    public function update_email_integration($data){
        $this->db->where('id',1);

        if(isset($data['active'])){
            $data['active'] = 1;
        } else {
            $data['active'] = 0;
        }

        if(isset($data['notify_lead_imported'])){
            $data['notify_lead_imported'] = 1;
        } else {
            $data['notify_lead_imported'] = 0;
        }

        if(isset($data['notify_lead_contact_more_times'])){
            $data['notify_lead_contact_more_times'] = 1;
        } else {
            $data['notify_lead_contact_more_times'] = 0;
        }

        if($data['notify_lead_contact_more_times'] != 0 || $data['notify_lead_imported'] != 0){
            if($data['notify_type'] == 'specific_staff'){
                if(isset($data['notify_ids_staff'])){
                     $data['notify_ids'] = serialize($data['notify_ids_staff']);
                     unset($data['notify_ids_staff']);
                 } else {
                     $data['notify_ids'] = serialize(array());
                     unset($data['notify_ids_staff']);
                 }

                if(isset($data['notify_ids_roles'])){
                    unset($data['notify_ids_roles']);
                }
            } else {
                 if(isset($data['notify_ids_roles'])){
                     $data['notify_ids'] = serialize($data['notify_ids_roles']);
                     unset($data['notify_ids_roles']);
                 } else {
                     $data['notify_ids'] = serialize(array());
                     unset($data['notify_ids_roles']);
                 }
                if(isset($data['notify_ids_staff'])){
                    unset($data['notify_ids_staff']);
                }
            }
        } else {
           $data['notify_ids'] = serialize(array());
           $data['notify_type'] = NULL;
            if(isset($data['notify_ids_staff'])){
                unset($data['notify_ids_staff']);
            }
            if(isset($data['notify_ids_roles'])){
                unset($data['notify_ids_roles']);
            }
        }

        if(isset($data['only_loop_on_unseen_emails'])){
            $data['only_loop_on_unseen_emails'] = 1;
        } else {
            $data['only_loop_on_unseen_emails'] = 0;
        }

        $this->db->update('tblleadsemailintegration',$data);
        if($this->db->affected_rows() > 0){
            return true;
        }

        return false;
    }

    public function change_status_color($data){
        $this->db->where('id',$data['status_id']);
        $this->db->update('tblleadsstatus',array('color'=>$data['color']));
    }

    public function update_status_order(){
        $data =$this->input->post();
        foreach($data['order'] as $status){
            $this->db->where('id',$status[0]);
            $this->db->update('tblleadsstatus',array('statusorder'=>$status[1]));
        }
    }
}
