<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Authentication_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->admin();
    }

    public function admin()
    {
        if (is_staff_logged_in()) {
            redirect(site_url('admin'));
        }

        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {

                $success = $this->Authentication_model->login($this->input->post('email'), $this->input->post('password'), $this->input->post('remember'), true);

                if (is_array($success) && isset($success['memberinactive'])) {
                    set_alert('danger', _l('admin_auth_inactive_account'));
                    redirect(site_url('authentication/admin'));
                } else if ($success == false) {
                    set_alert('danger', _l('admin_auth_invalid_email_or_password'));
                    redirect(site_url('authentication/admin'));
                }

                redirect(site_url('admin'));
            }
        }

        $data['title'] = 'Login';
        $this->load->view('authentication/login_admin', $data);
    }

    public function forgot_password()
    {
        if (is_staff_logged_in()) {
            redirect(site_url('admin'));
        }

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_exists');

        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                $success = $this->Authentication_model->forgot_password($this->input->post('email'), true);

                if (is_array($success) && isset($success['memberinactive'])) {
                    set_alert('danger', 'Inactive account');
                    redirect(site_url('authentication/forgot_password'));
                } else if ($success == true) {
                    set_alert('success', 'Check your email for further instructions resetting your password');
                    redirect(site_url('authentication/admin'));
                } else {
                    set_alert('danger', 'Error setting new password key');
                    redirect(site_url('authentication/forgot_password'));
                }
            }
        }

        $this->load->view('authentication/forgot_password');
    }

    public function reset_password($staff, $userid, $new_pass_key)
    {

        if (!$this->Authentication_model->can_reset_password($staff, $userid, $new_pass_key)) {
            set_alert('danger', 'Password key expired or invalid user');
            redirect(site_url('authentication/admin'));
        }

        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('passwordr', 'Password Confirmation', 'required|matches[password]');

        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                do_action('before_user_reset_password', array(
                    'staff' => $staff,
                    'userid' => $userid
                ));
                $success = $this->Authentication_model->reset_password($staff, $userid, $new_pass_key, $this->input->post('passwordr'));
                if (is_array($success) && $success['expired'] == true) {
                    set_alert('danger', 'Password key expired');

                } else if ($success == true) {
                    do_action('after_user_reset_password', array(
                        'staff' => $staff,
                        'userid' => $userid
                    ));
                    set_alert('success', 'Your password has been reset. Please login now!');
                } else {
                    set_alert('danger', 'Error resetting your password. Try again.');
                }

                redirect(site_url('authentication/admin'));
            }

        }

        $this->load->view('authentication/reset_password');
    }

    public function client()
    {
        if (is_client_logged_in()) {
            redirect(site_url());
        }

        if ($this->input->post()) {
            $success = $this->Authentication_model->login($this->input->post('email'), $this->input->post('password'), $this->input->post('remember'), false);

            if (is_array($success) && isset($success['memberinactive'])) {
                set_alert('danger', 'Inactive account');
                redirect(site_url('clients/login'));
            } else if ($success == false) {
                set_alert('danger', 'Invaid username or password');
                redirect(site_url('clients/login'));
            }
            redirect(site_url());
        }
    }

    public function set_password($staff, $userid, $new_pass_key)
    {

        if (!$this->Authentication_model->can_set_password($staff, $userid, $new_pass_key)) {
            set_alert('danger', 'Password key expired or invalid user');
            redirect(site_url('authentication/admin'));
            if ($staff == 1) {
                redirect(site_url('authentication/admin'));
            } else {
                redirect(site_url());
            }
        }

        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('passwordr', 'Password Confirmation', 'required|matches[password]');

        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                $success = $this->Authentication_model->set_password($staff, $userid, $new_pass_key, $this->input->post('passwordr'));
                if (is_array($success) && $success['expired'] == true) {
                    set_alert('danger', 'Password key expired');
                } else if ($success == true) {
                    set_alert('success', 'Your password has been reset. Please login now!');
                } else {
                    set_alert('danger', 'Error resetting your password. Try again.');
                }

                if ($staff == 1) {
                    redirect(site_url('authentication/admin'));
                } else {
                    redirect(site_url());
                }
            }
        }
        $this->load->view('authentication/set_password');
    }
    public function logout()
    {
        $this->Authentication_model->logout();
        do_action('after_user_logout');
        redirect(site_url('authentication/admin'));
    }

    public function email_exists($email)
    {
        $total_rows = total_rows('tblstaff', array(
            'email' => $email
        ));
        if ($total_rows == 0) {
            $this->form_validation->set_message('email_exists', '%s not found.');
            return false;
        }

        return true;
    }
}
