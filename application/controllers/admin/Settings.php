<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        if (!has_permission('editSettings')) {
            access_denied('editSettings');
        }
        $this->load->model('settings_model');
    }
    /* View all settings */
    public function index()
    {
        if ($this->input->post()) {
            handle_company_logo_upload();
            handle_favicon_upload();
            $post_data = $this->input->post();
            $success   = $this->settings_model->update($post_data);

            if ($success > 0) {
                set_alert('success', _l('settings_updated'));
            }
            $red_url = admin_url('settings');
            if($this->input->get('tab_hash')){
                $red_url = admin_url('settings?tab_hash='.$this->input->get('tab_hash'));
            }
            redirect($red_url,'refresh');
        }

        $data['tab_hash'] = $this->input->get('tab_hash');

        $this->load->model('taxes_model');
        $this->load->model('tickets_model');
        $data['taxes'] = $this->taxes_model->get();
        $data['ticket_priorities'] = $this->tickets_model->get_priority();
        $data['roles']     = $this->roles_model->get();
        $data['bodyclass'] = 'top-tabs';
        $data['title']     = 'Options';
        $this->load->view('admin/settings/all', $data);
    }
    /* Remove company logo from settings / ajax */
    public function remove_company_logo()
    {
        if ($this->input->is_ajax_request()) {
            if(file_exists(COMPANY_FILES_FOLDER . '/' . get_option('company_logo'))){
                unlink(COMPANY_FILES_FOLDER . '/' . get_option('company_logo'));
            }
             if (update_option('company_logo','')) {
                echo json_encode(array(
                    'success' => true
                    ));
                exit;
            }
        }
    }

    public function remove_favicon(){
        if ($this->input->is_ajax_request()) {
            if(file_exists(COMPANY_FILES_FOLDER . '/' . get_option('favicon'))){
                unlink(COMPANY_FILES_FOLDER . '/' . get_option('favicon'));
            }
            if (update_option('favicon','')) {
                echo json_encode(array(
                    'success' => true
                    ));
                exit;
            }

        }
    }

    public function new_pdf_company_field(){
        if($this->input->post()){
            $success = $this->settings_model->add_new_company_pdf_field($this->input->post());

            if($success === true){
                set_alert('success', _l('added_successfuly',_l('new_company_field_name')));
                echo 'true';
            } else {
                echo 'false';
            }
        }
    }

    public function delete_option($id){
        echo json_encode(array('success'=>delete_option($id)));
    }
}
