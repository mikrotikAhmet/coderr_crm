<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emails extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        if (!has_permission('editEmailTemplates')) {
            access_denied('editEmailTemplates');
        }
        $this->load->model('emails_model');
    }

    /* List all email templates */
    public function index()
    {
        $data['staff']   = $this->emails_model->get('staff');
        $data['tasks']   = $this->emails_model->get('tasks');
        $data['client']  = $this->emails_model->get('client');
        $data['tickets'] = $this->emails_model->get('ticket');
        $data['invoice'] = $this->emails_model->get('invoice');
        $data['estimate'] = $this->emails_model->get('estimate');
        $data['contracts'] = $this->emails_model->get('contract');
        $data['proposals'] = $this->emails_model->get('proposals');

        $data['title'] = _l('email_templates');
        $this->load->view('admin/emails/email_templates', $data);
    }

    /* Edit email template */
    public function email_template($id)
    {
        if (!$id) {
            redirect(admin_url('emails'));
        }

        if ($this->input->post()) {
            $success = $this->emails_model->update($this->input->post(), $id);
            if ($success) {
                 set_alert('success', _l('updated_successfuly',_l('email_template')));
            }
            redirect(admin_url('emails/email_template/' . $id));
        }

        $data['available_merge_fields'] = $this->emails_model->get_available_merge_fields();
        $data['template'] = $this->emails_model->get_email_template_by_id($id);
        $title            = _l('edit',_l('email_template'));

        $data['title'] = $title;
        $this->load->view('admin/emails/template', $data);
    }
    /* Since version 1.0.1 - test your smtp settings */
    public function sent_smtp_test_email(){

      if($this->input->post()){
          $this->email->initialize();
          $this->email->from(get_option('smtp_email'), get_option('companyname'));
          $this->email->to($this->input->post('test_email'));
          $this->email->subject('Perfex SMTP setup testing');
          $this->email->message('This is test email SMTP from Perfex. <br />If you received this message that means that your SMTP settings is set correctly');

          if($this->email->send()) {
            set_alert('success','Seems like your SMTP settings is set correctly. Check your email now.');
        } else {
            set_debug_alert('<h1>Your SMTP settings are not set correctly here is the debug log.</h1><br />'.$this->email->print_debugger());
        }
    }
}
}
