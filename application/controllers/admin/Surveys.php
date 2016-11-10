<?php
defined('BASEPATH') OR exit('No direct script access allowed');
set_time_limit(0);

class Surveys extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        if (!has_permission('manageSurveys')) {
            access_denied('manageSurveys');
        }
        $this->load->model('surveys_model');
    }

    /* List all surveys */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns     = array(
                'surveyid',
                'subject',
                '(SELECT count(questionid) FROM tblsurveyquestions WHERE tblsurveyquestions.surveyid = tblsurveys.surveyid)',
                '(SELECT count(resultsetid) FROM tblsurveyresultsets WHERE tblsurveyresultsets.surveyid = tblsurveys.surveyid)',
                'datecreated',
                'active'
            );
            $sIndexColumn = "surveyid";
            $sTable       = 'tblsurveys';

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), array(), array(
                'hash'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'subject') {
                        $_data = '<a href="' . admin_url('surveys/survey/' . $aRow['surveyid']) . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'datecreated') {
                        $_data = _dt($_data);
                    } else if ($aColumns[$i] == 'active') {
                        $checked = '';
                        if ($aRow['active'] == 1) {
                            $checked = 'checked';
                        }
                        $_data = '<input type="checkbox" class="switch-box input-xs" data-size="mini" data-id="' . $aRow['surveyid'] . '" data-switch-url="admin/surveys/change_survey_status" ' . $checked . '>';

                                                // For exporting
                        $_data .=  '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) .'</span>';
                    }

                    $row[] = $_data;
                }

                $options = '';

                if (total_rows('tblsurveyresultsets', 'surveyid=' . $aRow['surveyid']) > 0) {
                    $options .= icon_btn('admin/surveys/results/' . $aRow['surveyid'], 'area-chart','btn-success',
                        array(
                            'data-toggle'=>'tooltip',
                            'title'=>_l('survey_list_view_results_tooltip'))
                        );
                }

                $options .= icon_btn('survey/' . $aRow['surveyid'] . '/' . $aRow['hash'], 'eye', 'btn-default',
                        array(
                            'data-toggle'=>'tooltip',
                            'title'=>_l('survey_list_view_tooltip'),
                            'target'=>'_blank')
                        );
                $options .= icon_btn('admin/surveys/survey/' . $aRow['surveyid'], 'pencil-square-o');
                $row[] = $options .= icon_btn('admin/surveys/delete/' . $aRow['surveyid'], 'remove', 'btn-danger');
                $output['aaData'][] = $row;

            }

            echo json_encode($output);
            die();
        }
        $data['title'] = _l('surveys');
        $this->load->view('admin/surveys/all', $data);
    }

    /* Add new survey or update existing */
    public function survey($id = '')
    {

        if ($this->input->post()) {

            if ($id == '') {
                $id = $this->surveys_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly',_l('survey')));
                    redirect(admin_url('surveys/survey/' . $id));
                }
            } else {
                $success = $this->surveys_model->update($this->input->post(), $id);
                if ($success) {
                   set_alert('success', _l('updated_successfuly',_l('survey')));
                }

                redirect(admin_url('surveys/survey/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new',_l('survey_lowercase'));

        } else {
            $survey             = $this->surveys_model->get($id);
            $data['send_log']   = $this->surveys_model->get_survey_send_log($id);
            $data['survey']     = $survey;
            $title              = _l('edit',_l('survey_lowercase')) . ' ' . $survey->subject;
        }

         $this->load->model('mail_lists_model');
            $data['mail_lists'] = $this->mail_lists_model->get();
            $data['found_custom_fields'] = false;
            $i = 0;
            foreach($data['mail_lists'] as $mail_list){
                 $fields = $this->mail_lists_model->get_list_custom_fields($mail_list['listid']);

                 if(count($fields) > 0){
                    $data['found_custom_fields'] = true;
                 }
                 $data['mail_lists'][$i]['customfields'] = $fields;
                $i++;
            }

        $data['title'] = $title;
        $this->load->view('admin/surveys/survey', $data);
    }

    /* Send survey to mail list */
    public function send($surveyid)
    {

        if (!$surveyid) {
            redirect(admin_url('surveys'));
        }


        $this->load->model('mail_lists_model');
        $this->load->model('staff_model');
        $this->load->model('clients_model');

        $_lists = array();
        $_all_emails = array();
        if ($this->input->post('send_survey_to')) {
            $lists = $this->input->post('send_survey_to');

            foreach ($lists as $key => $val) {
                // is mail list
                if (is_int($key)) {
                    $list = $this->mail_lists_model->get($key);
                    $emails = $this->mail_lists_model->get_mail_list_emails($key);
                    foreach ($emails as $email) {
                        // We dont need to validate emails becuase email are already validated when added to mail list
                        array_push($_all_emails, array('listid'=>$key,'emailid'=>$email['emailid'],'email'=>$email['email']));
                    }

                    if(count($emails) > 0){
                        array_push($_lists,$list->name);
                    }
                } else {
                    if ($key == 'staff') {
                        // Pass second paramter to get all active staff, we dont need inactive staff
                        // If you want adjustments feel free to pass 0 or '' for all
                        $staff = $this->staff_model->get('',1);
                        foreach ($staff as $email) {
                            array_push($_all_emails, $email['email']);
                        }

                        if(count($staff) > 0){
                            array_push($_lists,'Staff');
                        }

                    } else if ($key == 'clients') {
                        $clients = $this->clients_model->get();
                        foreach ($clients as $email) {
                            array_push($_all_emails, $email['email']);
                        }
                         if(count($clients) > 0){
                            array_push($_lists,'Clients');
                        }
                    }
                }
            }
        } else {
            set_alert('warning', _l('survey_no_mail_lists_selected'));
            redirect(admin_url('surveys/survey/' . $surveyid));
        }

        foreach ($_all_emails as $email) {
            // Is not from email lists
            if(!is_array($email)){
              $this->db->insert('tblsurveysemailsendcron',array(
                'email'=>$email,
                'surveyid'=>$surveyid
                ));
          } else {
            // Yay its a mail list
            // We will need this info for the custom fields when sending the survey
              $this->db->insert('tblsurveysemailsendcron',array(
                'email'=>$email['email'],
                'surveyid'=>$surveyid,
                'listid'=>$email['listid'],
                'emailid'=>$email['emailid']
                ));
          }
      }

        // We dont need to include in query CRON if 0 emails found
        $iscronfinished = 0;
        if(count($_all_emails) == 0){
            $iscronfinished = 1;
        }

        $this->surveys_model->init_survey_send_log($surveyid,$iscronfinished,$_lists);
        set_alert('success',_l('survey_send_success_note',count($_all_emails)));
        redirect(admin_url('surveys/survey/' . $surveyid));
    }

    /* View survey participating results*/
    public function results($id)
    {

        if (!$id) {
            redirect(admin_url('surveys'));
        }

        $data['surveyid']  = $id;
        $data['bodyclass'] = 'survey_results';
        $survey            = $this->surveys_model->get($id);
        $data['survey']    = $survey;
        $data['title']     = _l('survey_result',$survey->subject);
        $this->load->view('admin/surveys/results', $data);
    }

    /* Delete survey from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('surveys'));
        }

        $success = $this->surveys_model->delete($id);
        if ($success) {
           set_alert('success', _l('deleted',_l('survey')));
        } else {
            set_alert('warning', _l('problem_deleting',_l('survey')));
        }
        redirect(admin_url('surveys'));

    }
    // Ajax
    /* Remove survey question */
    public function remove_question($questionid)
    {
        if ($this->input->is_ajax_request()) {
            $success = $this->surveys_model->remove_question($questionid);
            $message = '';
            if($success){
                $message = _l('deleted',_l('question_string'));
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
                ));
            die();
        }
    }
    /* Removes survey checkbox/radio description*/
    public function remove_box_description($questionboxdescriptionid)
    {
        if ($this->input->is_ajax_request()) {
            $success = $this->surveys_model->remove_box_description($questionboxdescriptionid);
            $message = '';
            if($success){
                $message = _l('deleted',_l('question_field_string'));
            }
             echo json_encode(array(
                'success' => $success,
                'message' => $message
                ));
            die();
        }
    }

    /* Add box description */
    public function add_box_description($questionid, $boxid)
    {
        if ($this->input->is_ajax_request()) {

            $boxdescriptionid = $this->surveys_model->add_box_description($questionid, $boxid);
            $message = '';

            if($boxdescriptionid){
                $message = _l('added_successfuly',_l('question_field_string'));
            }
             echo json_encode(array(
                'boxdescriptionid' => $boxdescriptionid,
                'message' => $message
                ));
            die();
        }
    }
    /* New survey question */
    public function add_survey_question()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                echo json_encode(
                    array(
                        'data'=>$this->surveys_model->add_survey_question($this->input->post()),
                        'message'=>_l('added_successfuly',_l('question_string')),
                        'survey_question_only_for_preview'=>_l('survey_question_only_for_preview'),
                        'survey_question_required'=>_l('survey_question_required')
                        )
                    );
                die();
            }
        }
    }
    /* Update question */
    public function update_question()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $success = $this->surveys_model->update_question($this->input->post());
                $message = '';
                if($success){
                    $message = _l('updated_successfuly',_l('question_string'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            }
        }
    }
    /* Reorder surveys */
    public function update_survey_questions_orders()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $this->surveys_model->update_survey_questions_orders($this->input->post());
            }
        }
    }
    /* Change survey status active or inactive*/
    public function change_survey_status($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $this->surveys_model->change_survey_status($id, $status);
        }
    }
}
