<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_controller extends CRM_Controller
{
    function __construct()
    {
        parent::__construct();

        $language       = get_option('active_language');
        $staff_language = get_staff_default_language();

        if (!empty($staff_language)) {
            if(file_exists(APPPATH .'language/'.$staff_language)){
                $language = $staff_language;
            }
        }

        $this->lang->load($language . '_lang', $language);

        $this->load->model('authentication_model');
        $this->authentication_model->autologin();

        if (!is_staff_logged_in()) {
            if (strpos($this->uri->uri_string(), 'authentication/admin') === FALSE) {
                $this->session->set_userdata(array(
                    'red_url' => $this->uri->uri_string()
                ));
            }

            redirect(site_url('authentication/admin'));
        }

        // In case staff have setup logged in as client
        $this->session->unset_userdata('client_user_id');
        $this->session->unset_userdata('client_logged_in');
        $this->session->unset_userdata('logged_in_as_client');

        $this->load->model('staff_model');
        $this->load->model('tickets_model');

        $this->init_quick_actions_links();
        $this->set_nofications();

        $this->load->vars(array(
            '_staff' => $this->staff_model->get(get_staff_user_id()),
            '_ticket_statuses' => $this->tickets_model->get_ticket_status(),
            '_notifications' => $this->misc_model->get_user_notifications(false),
            '_quick_actions' => $this->perfex_base->get_quick_actions_links(),
            'google_api_key' => get_option('google_api_key'),
            'customer_permissions'=>$this->perfex_base->get_customer_permissions(),
        ));

        $this->load->library('user_agent');
        if ($this->agent->is_mobile()) {
            $this->session->set_userdata(array(
                'is_mobile' => true
            ));
        } else {
            $this->session->unset_userdata('is_mobile');
        }
    }

    private function set_nofications()
    {
        $userid = get_staff_user_id();
        $today  = date('Y-m-d');

        $all_notified_events = array();

        // User events
        $this->db->where('start', $today);
        $this->db->where('userid', $userid);
        $this->db->where('isstartnotified', 0);

        $events = $this->db->get('tblevents')->result_array();

        foreach ($events as $event) {
            add_notification(array(
                'description' => 'Event starts today ' . substr($event['title'], 0, 50) . '...',
                'touserid' => $userid,
                'fromcompany' => true
            ));

            array_push($all_notified_events, $event['eventid']);
        }

        // Show public events
        $this->load->model('staff_model');
        $this->db->where('start', $today);
        $this->db->where('public', 1);
        $this->db->where('userid !=', $userid);
        $this->db->where('isstartnotified', 0);
        $events = $this->db->get('tblevents')->result_array();
        $staff  = $this->staff_model->get('', 1);

        foreach ($staff as $member) {
            foreach ($events as $event) {
                add_notification(array(
                    'description' => 'Public event starts today ' . substr($event['title'], 0, 50) . '...',
                    'touserid' => $userid,
                    'fromcompany' => true
                ));

                array_push($all_notified_events, $event['eventid']);
            }
        }

        foreach ($all_notified_events as $id) {
            $this->db->where('eventid', $id);
            $this->db->update('tblevents', array(
                'isstartnotified' => 1
            ));
        }
    }

    private function init_quick_actions_links()
    {

        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('qa_create_invoice'),
            'permission' => 'manageSales',
            'url' => 'invoices/invoice'
        ));

        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('qa_create_estimate'),
            'permission' => 'manageSales',
            'url' => 'estimates/estimate'
        ));

        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('qa_new_expense'),
            'permission' => 'manageExpenses',
            'url' => 'expenses/expense'
        ));

        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('new_proposal'),
            'permission' => 'manageSales',
            'url' => 'proposals/proposal'
        ));

        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('qa_create_task'),
            'url' => 'tasks/task',
            'permission' => 'manageTasks'
        ));

        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('qa_create_client'),
            'permission' => 'manageClients',
            'url' => 'clients/client'
        ));

        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('qa_create_contract'),
            'permission' => 'manageContracts',
            'url' => 'contracts/contract'
        ));

        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('qa_create_lead'),
            'url' => 'leads/lead'
        ));

        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('qa_new_goal'),
            'url' => 'goals/goal',
            'permission' => 'manageGoals'
        ));

        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('qa_create_kba'),
            'permission' => 'manageKnowledgeBase',
            'url' => 'knowledge_base/article'
        ));

        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('qa_create_survey'),
            'permission' => 'manageSurveys',
            'url' => 'surveys/survey'
        ));

        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('qa_create_ticket'),
            'url' => 'tickets/add'
        ));

        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('qa_create_staff'),
            'url' => 'staff/member',
            'permission' => 'manageStaff'
        ));

    }
}
