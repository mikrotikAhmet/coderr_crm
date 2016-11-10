<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Misc_model extends CRM_Model
{
    public $notifications_limit = 15;
    function __construct()
    {
        parent::__construct();
    }

    public function get_taxes_dropdown_template($name, $taxid)
    {
        $this->load->model('taxes_model');
        $taxes            = $this->taxes_model->get();
        $select           = '<select class="selectpicker display-block tax" data-width="100%" name="' . $name . '">';
        $_no_tax_selected = '';
        if (!$taxid) {
            $_no_tax_selected = 'selected';
        }
        $select .= '<option value="0" ' . $_no_tax_selected . ' data-taxrate="0">' . _l('no_tax') . '</option>';
        foreach ($taxes as $tax) {
            $selected = '';
            if ($taxid == $tax['id']) {
                $selected = 'selected';
            }
            $select .= '<option value="' . $tax['id'] . '" ' . $selected . ' data-taxrate="' . $tax['taxrate'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['taxrate'] . '%</option>';
        }
        $select .= '</select>';
        return $select;
    }

    /**
     * Add reminder
     * @since  Version 1.0.2
     * @param mixed $data All $_POST data for the reminder
     * @param mixed $id   relid id
     * @return boolean
     */
    public function add_reminder($data, $id)
    {

        if (isset($data['notify_by_email'])) {
            $data['notify_by_email'] = 1;
        } else {
            $data['notify_by_email'] = 0;
        }

        $data['date']        = to_sql_date($data['date']);
        $data['description'] = nl2br($data['description']);
        $data['creator']     = get_staff_user_id();
        $this->db->insert('tblreminders', $data);

        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Reminder Added [' . ucfirst($data['rel_type']) . 'ID: ' . $data['rel_id'] . ' Description: ' . $data['description'] . ']');
            return true;
        }

        return false;
    }

    /**
     * Get all reminders or 1 reminder if id is passed
     * @since Version 1.0.2
     * @param  mixed $id reminder id OPTIONAL
     * @return array or object
     */
    public function get_reminders($id = '')
    {

        $this->db->join('tblstaff', 'tblstaff.staffid = tblreminders.staff');

        if (is_numeric($id)) {
            $this->db->where('tblreminders.id', $id);
            return $this->db->get('tblreminders')->row();
        }

        $this->db->order_by('date', 'desc');
        return $this->db->get('tblreminders')->result_array();
    }

    /**
     * Remove client reminder from database
     * @since Version 1.0.2
     * @param  mixed $id reminder id
     * @return boolean
     */
    public function delete_reminder($id)
    {
        $reminder = $this->get_reminders($id);
        if ($reminder->creator == get_staff_user_id() || is_admin()) {
            $this->db->where('id', $id);
            $this->db->delete('tblreminders');
            if ($this->db->affected_rows() > 0) {
                logActivity('Reminder Deleted [' . ucfirst($reminder->rel_type) . 'ID: ' . $reminder->id . ' Description: ' . $reminder->description . ']');
                return true;
            }
            return false;
        }

        return false;
    }

    public function get_google_calendar_ids()
    {

        $is_admin = is_admin();
        $this->load->model('departments_model');
        $departments       = $this->departments_model->get();
        $staff_departments = $this->departments_model->get_staff_departments(false, true);
        $ids               = array();
        // Check departments google calendar ids
        foreach ($departments as $department) {
            if ($department['calendar_id'] == '') {
                continue;
            }
            if ($is_admin) {
                $ids[] = $department['calendar_id'];
            } else {
                if (in_array($department['departmentid'], $staff_departments)) {
                    $ids[] = $department['calendar_id'];
                }
            }
        }
        // Ok now check if main calendar is setup
        $main_id_calendar = get_option('google_calendar_main_calendar');
        if ($main_id_calendar != '') {
            $ids[] = $main_id_calendar;
        }

        return $ids;
    }

    /**
     * Get current user notifications
     * @param  boolean $read include and readed notifications
     * @return array
     */
    public function get_user_notifications($read = 1)
    {
        $total        = 10;
        $total_unread = total_rows('tblnotifications', array(
            'isread' => $read,
            'touserid' => get_staff_user_id()
        ));
        if (is_numeric($read)) {
            $this->db->where('isread', $read);
        }
        if ($total_unread > $total) {
            $_diff = $total_unread - $total;
            $total = $_diff + $total;
        }

        $this->db->where('touserid', get_staff_user_id());
        $this->db->limit($total);
        $this->db->order_by('date', 'desc');
        return $this->db->get('tblnotifications')->result_array();
    }

    /**
     * Get current user all notifications
     * @param  mixed $page page number / ajax request
     * @return array
     */
    public function get_all_user_notifications($page)
    {

        $offset = ($page * $this->notifications_limit);
        $this->db->limit($this->notifications_limit, $offset);
        $this->db->where('touserid', get_staff_user_id());
        $this->db->order_by('date', 'desc');
        $notifications = $this->db->get('tblnotifications')->result_array();
        $i             = 0;
        foreach ($notifications as $notification) {
            if ($notification['fromcompany'] == NULL) {
                $notifications[$i]['profile_image'] = '<a href="' . admin_url('staff/profile/' . $notification['fromuserid']) . '">' . staff_profile_image($notification['fromuserid'], array(
                    'staff-profile-image-small',
                    'img-circle',
                    'pull-left'
                )) . '</a>';
            } else {
                $notifications[$i]['profile_image'] = '';
            }
            $notifications[$i]['date'] = time_ago($notification['date']);
            $i++;
        }

        return $notifications;
    }

    /**
     * Set notification read when user open notification dropdown
     * @return boolean
     */
    public function set_notifications_read()
    {
        $this->db->where('touserid', get_staff_user_id());
        $this->db->update('tblnotifications', array(
            'isread' => 1
        ));
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Dismiss announcement
     * @param  array  $data  announcement data
     * @param  boolean $staff is staff or client
     * @return boolean
     */
    public function dismiss_announcement($data, $staff = true)
    {
        if ($staff == false) {
            $userid = get_client_user_id();
        } else {
            $userid = get_staff_user_id();
        }
        $data['userid'] = $userid;
        $data['staff']  = $staff;

        $this->db->insert('tbldismissedannouncements', $data);
        return true;
    }

    /**
     * Add user note (used for staff and clients)
     * @param array $data   note data
     * @param mixed $userid staff/client id
     * @param boolean $staff  is staff or client
     */
    public function add_user_note($data, $userid, $staff)
    {
        $data['dateadded']   = date('Y-m-d H:i:s');
        $data['addedfrom']   = get_staff_user_id();
        $data['staff']       = $staff;
        $data['description'] = nl2br($data['description']);
        $data['userid']      = $userid;

        $this->db->insert('tbluseradminnotes', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            logActivity('User Note Added [UserID: ' . $userid . ' Staff: ' . $staff . ']');
        }

        return $insert_id;
    }

    /**
     * Delete staff/client note
     * @param  mixed $noteid note id
     * @return boolean
     */
    public function delete_user_note($noteid)
    {
        $this->db->where('usernoteid', $noteid);
        $this->db->delete('tbluseradminnotes');
        if ($this->db->affected_rows() > 0) {
            logActivity('User Note Deleted [NoteID: ' . $noteid . ']');
            return true;
        }

        return false;
    }
    /**
     * Get all user notes / Staff or client
     * @param  mixed  $userid staff/client idate(format)
     * @param  boolean $staff  is staff or client
     * @return array
     */
    public function get_user_notes($userid, $staff = true)
    {
        $this->db->select('tbluseradminnotes.dateadded,description,usernoteid,tblstaff.firstname,tblstaff.lastname,addedfrom');
        $this->db->from('tbluseradminnotes');
        $this->db->join('tblstaff', 'tbluseradminnotes.addedfrom = tblstaff.staffid', 'left');
        $this->db->where('staff', $staff);
        $this->db->where('tbluseradminnotes.userid', $userid);
        $this->db->order_by('tbluseradminnotes.dateadded', 'ASC');
        return $this->db->get()->result_array();
    }

     /**
     * Perform search on top header
     * @since  Version 1.0.1
     * @param  string $q search
     * @return array    search results
     */
    public function perform_search($q){

        $result = array();
        $limit = get_option('limit_top_search_bar_results_to');
        // Clients
        $this->db->select()
        ->join('tblcountries','tblcountries.country_id = tblclients.country')
        ->from('tblclients')
        ->like('firstname',$q)
        ->or_like('lastname',$q)
        ->or_like('email',$q)
        ->or_like("CONCAT(firstname, ' ', lastname)", $q)
        ->or_like('company',$q)
        ->or_like('vat',$q)
        ->or_like('phonenumber',$q)
        ->or_like('city',$q)
        ->or_like('zip',$q)
        ->or_like('state',$q)
        ->or_like('address',$q)
        ->or_like('tblcountries.short_name',$q)
        ->or_like('tblcountries.long_name',$q)
        ->or_like('tblcountries.numcode',$q)
        ->or_like('tblcountries.calling_code',$q)
        ->limit($limit);

        $result['clients'] = $this->db->get()->result_array();

        // Staff
        $this->db->select()
        ->from('tblstaff')
        ->like('firstname',$q)
        ->or_like('lastname',$q)
        ->or_like("CONCAT(firstname, ' ', lastname)", $q, FALSE)
        ->or_like('facebook',$q)
        ->or_like('linkedin',$q)
        ->or_like('phonenumber',$q)
        ->or_like('email',$q)
        ->or_like('skype',$q)
        ->limit($limit);

        $result['staff'] = $this->db->get()->result_array();

        // Tickets
        $this->db->select()
        ->join('tbldepartments','tbldepartments.departmentid = tbltickets.department')
        ->from('tbltickets')
        ->like('ticketid',$q)
        ->or_like('subject',$q)
        ->or_like('message',$q)
        ->or_like('tbldepartments.name',$q)
        ->limit($limit);

        $result['tickets'] = $this->db->get()->result_array();

        // Surveys
        $this->db->select()
        ->from('tblsurveys')
        ->like('subject',$q)
        ->or_like('slug',$q)
        ->or_like('description',$q)
        ->or_like('viewdescription',$q)
        ->limit($limit);

        $result['surveys'] = $this->db->get()->result_array();

        // Knowledge base articles
        $this->db->select()
        ->from('tblknowledgebase')
        ->like('subject',$q)
        ->or_like('description',$q)
        ->or_like('slug',$q)
        ->limit($limit);

        $result['knowledge_base_articles'] = $this->db->get()->result_array();

        // Leads
        $this->db->select()
        ->from('tblleads')
        ->like('name',$q)
        ->or_like('email',$q)
        ->or_like('phonenumber',$q)
        ->or_like('notes',$q)
        ->limit($limit);

        $result['leads'] = $this->db->get()->result_array();

        // Staff tasks
        $this->db->select()
        ->from('tblstafftasks')
        ->like('name',$q)
        ->or_like('priority',$q)
        ->or_like('description',$q)
        ->limit($limit);

        $result['tasks'] = $this->db->get()->result_array();

        // Contracts
        $this->db->select()
        ->from('tblcontracts')
        ->like('description',$q)
        ->or_like('subject',$q)
        ->limit($limit);

        $result['contracts'] = $this->db->get()->result_array();

        // Invoice payment records
        $this->db->select('*,tblinvoicepaymentrecords.id as paymentid')
        ->from('tblinvoicepaymentrecords')
        ->join('tblinvoicepaymentsmodes','tblinvoicepaymentrecords.paymentmode = tblinvoicepaymentsmodes.id','LEFT')
        ->like('tblinvoicepaymentrecords.id',$q)
        ->or_like('paymentmode',$q)
        ->or_like('tblinvoicepaymentsmodes.name',$q)
        ->or_like('invoiceid',$q)
        ->limit($limit);

        $result['invoice_payment_records'] = $this->db->get()->result_array();

        // Invoices
        $this->db->select('*,tblinvoices.id as invoiceid')
        ->from('tblinvoices')
        ->join('tblclients','tblclients.userid = tblinvoices.clientid')
        ->join('tblcurrencies','tblcurrencies.id = tblinvoices.currency')
        ->like('number',$q)
        ->or_like('year',$q)
        ->or_like("CONCAT(firstname, ' ', lastname)", $q, FALSE)
        ->or_like('company',$q)
        ->or_like('clientnote',$q)
        ->or_like('email',$q)
        ->or_like('vat',$q)
        ->or_like('phonenumber',$q)
        ->or_like('city',$q)
        ->or_like('zip',$q)
        ->or_like('state',$q)
        ->or_like('address',$q)
        ->or_like('adminnote',$q)
        ->or_like('tblinvoices.id',$q)
        ->or_like('token',$q)
        ->limit($limit);

        $result['invoices'] = $this->db->get()->result_array();

        // Expenses
        $this->db->select('*,tblexpenses.amount as amount,tblexpensescategories.name as category_name,tblinvoicepaymentsmodes.name as payment_mode_name,tbltaxes.name as tax_name, tblexpenses.id as expenseid')
        ->from('tblexpenses')
        ->join('tblclients', 'tblclients.userid = tblexpenses.clientid', 'left')
        ->join('tblinvoicepaymentsmodes', 'tblinvoicepaymentsmodes.id = tblexpenses.paymentmode', 'left')
        ->join('tbltaxes', 'tbltaxes.id = tblexpenses.tax', 'left')
        ->join('tblexpensescategories', 'tblexpensescategories.id = tblexpenses.category')
        ->or_like("CONCAT(firstname, ' ', lastname)", $q, FALSE)
        ->or_like('company',$q)
        ->or_like('paymentmode',$q)
        ->or_like('tblinvoicepaymentsmodes.name',$q)
        ->or_like('email',$q)
        ->or_like('vat',$q)
        ->or_like('phonenumber',$q)
        ->or_like('city',$q)
        ->or_like('zip',$q)
        ->or_like('state',$q)
        ->or_like('address',$q)
        ->or_like('tblexpensescategories.name',$q)
        ->or_like('tblexpenses.note',$q)
        ->limit($limit);

         $result['expenses'] = $this->db->get()->result_array();

         // Goals
        $this->db->select()
        ->from('tblgoals')
        ->like('description',$q)
        ->or_like('subject',$q)
        ->limit($limit);

        $result['goals'] = $this->db->get()->result_array();

        // Custom fields
        $this->db->select()
        ->from('tblcustomfieldsvalues')
        ->like('value',$q)
        ->limit($limit);

        $result['custom_fields'] = $this->db->get()->result_array();

        // Invoice items
        $this->db->select()
        ->from('tblinvoiceitems')
        ->like('description',$q)
        ->or_like('long_description',$q)
        ->limit($limit);

        $result['invoice_items'] = $this->db->get()->result_array();

        return $result;

    }

}
