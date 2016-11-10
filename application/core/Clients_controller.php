<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clients_controller extends CRM_Controller
{
    public $template = array();
    public $data = array();
    public $use_footer = true;
    public $use_head = true;
    public $add_scripts = true;
    public $use_navigation = true;

    function __construct()
    {
        parent::__construct();

         $language = get_option('active_language');
         $client_language = get_client_default_language();

         if(!empty($client_language)){
               if(file_exists(APPPATH .'language/'.$client_language)){
                $language = $client_language;
            }
         }

        $this->lang->load($language . '_lang', $language);

        $this->load->library('form_validation');
        $this->form_validation->set_message('required', _l('form_validation_required'));
        $this->form_validation->set_message('valid_email', _l('form_validation_valid_email'));
        $this->form_validation->set_message('matches', _l('form_validation_matches'));
        $this->form_validation->set_message('is_unique', _l('form_validation_is_unique'));

        $this->load->model('authentication_model');
        $this->authentication_model->autologin();

        $this->load->model('tickets_model');
        $this->load->model('departments_model');
        $this->load->model('clients_model');
        $this->load->model('currencies_model');
        $this->load->model('invoices_model');
        $this->load->model('estimates_model');

        $_auto_loaded_vars = array(
            'departments' => $this->departments_model->get(false, true),
            'priorities' => $this->tickets_model->get_priority(),
            'ticket_statuses' => $this->tickets_model->get_ticket_status(),
            'currencies' => $this->currencies_model->get()
            );

        if (get_option('services') == 1) {
            $_auto_loaded_vars['services'] = $this->tickets_model->get_service();
        }

        if (get_option('use_knowledge_base') == 1) {
            $this->load->model('knowledge_base_model');
        }

        if (is_client_logged_in()) {
            $_auto_loaded_vars['client'] = $this->clients_model->get(get_client_user_id());
        }

        $this->load->vars($_auto_loaded_vars);
    }

    public function layout()
    {
        $this->data['use_navigation'] = true;
        if ($this->use_navigation == false) {
            $this->data['use_navigation'] = false;
        }

        $this->template['head'] = '';
        if ($this->use_head == true) {
            $this->template['head'] = $this->load->view('themes/' . active_clients_theme() . '/head', $this->data, true);
        }

        $this->template['view'] = $this->load->view('themes/' . active_clients_theme() . '/views/' . $this->view, $this->data, true);

        $this->template['footer'] = '';
        if ($this->use_footer == true) {
            $this->template['footer'] = $this->load->view('themes/' . active_clients_theme() . '/footer', $this->data, true);
        }

        $this->template['scripts'] = '';
        if ($this->add_scripts == true) {
            $this->template['scripts'] = $this->load->view('themes/' . active_clients_theme() . '/scripts', $this->data, true);
        }

        $this->load->view('themes/' . active_clients_theme() . '/index', $this->template);
    }
}
