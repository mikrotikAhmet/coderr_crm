<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Surveys_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get survey and all questions by id
     * @param  mixed $id survey id
     * @return object
     */
    function get($id = '')
    {

        $this->db->where('surveyid', $id);
        $survey = $this->db->get('tblsurveys')->row();

        if (!$survey) {
            return false;
        }
        $this->db->where('surveyid', $survey->surveyid);
        $this->db->order_by('question_order', 'asc');
        $questions = $this->db->get('tblsurveyquestions')->result_array();

        $i = 0;
        foreach ($questions as $question) {
            $this->db->where('questionid', $question['questionid']);
            $box                      = $this->db->get('tblsurveyquestionboxes')->row();
            $questions[$i]['boxid']   = $box->boxid;
            $questions[$i]['boxtype'] = $box->boxtype;
            if ($box->boxtype == 'checkbox' || $box->boxtype == 'radio') {
                $this->db->order_by('questionboxdescriptionid', 'asc');
                $this->db->where('boxid', $box->boxid);
                $boxes_description = $this->db->get('tblsurveyquestionboxesdescription')->result_array();
                if (count($boxes_description) > 0) {
                    $questions[$i]['box_descriptions'] = array();
                    foreach ($boxes_description as $box_description) {
                        $questions[$i]['box_descriptions'][] = $box_description;
                    }
                }
            }
            $i++;
        }

        $survey->questions = $questions;
        return $survey;


    }

    /**
     * Update survey
     * @param  array $data     survey $_POST data
     * @param  mixed $surveyid survey id
     * @return boolean
     */
    public function update($data, $surveyid)
    {

        if (isset($data['disabled'])) {
            $data['active'] = 0;
            unset($data['disabled']);
        } else {
            $data['active'] = 1;
        }

        if (isset($data['onlyforloggedin'])) {
            $data['onlyforloggedin'] = 1;
        } else {
            $data['onlyforloggedin'] = 0;
        }

        if (isset($data['iprestrict'])) {
            $data['iprestrict'] = 1;
        } else {
            $data['iprestrict'] = 0;
        }

        $this->db->where('surveyid', $surveyid);
        $this->db->update('tblsurveys', array(
            'subject' => $data['subject'],
            'slug' => slug_it($data['subject']),
            'description' => nl2br($data['description']),
            'viewdescription' => nl2br($data['viewdescription']),
            'iprestrict' => $data['iprestrict'],
            'active' => $data['active'],
            'onlyforloggedin' => $data['onlyforloggedin'],
            'redirect_url' => $data['redirect_url']
        ));


        if ($this->db->affected_rows() > 0) {
            logActivity('Survey Updated [ID: ' . $surveyid . ', Subject: '.$data['subject'].']');
            return true;
        }

        return false;
    }

    /**
     * Add new survey
     * @param array $data survey $_POST data
     * @return mixed
     */
    public function add($data)
    {

        if (isset($data['disabled'])) {
            $data['active'] = 0;
            unset($data['disabled']);
        } else {
            $data['active'] = 1;
        }

        if (isset($data['iprestrict'])) {
            $data['iprestrict'] = 1;
        } else {
            $data['iprestrict'] = 0;
        }

         if (isset($data['onlyforloggedin'])) {
            $data['onlyforloggedin'] = 1;
        } else {
            $data['onlyforloggedin'] = 0;
        }

        $datecreated = date('Y-m-d H:i:s');
        $this->db->insert('tblsurveys', array(
            'subject' => $data['subject'],
            'slug' => slug_it($data['subject']),
            'description' => nl2br($data['description']),
            'viewdescription' => nl2br($data['viewdescription']),
            'datecreated' => $datecreated,
            'creator' => get_staff_user_id(),
            'active' => $data['active'],
            'onlyforloggedin' => $data['onlyforloggedin'],
            'iprestrict' => $data['iprestrict'],
            'redirect_url' => $data['redirect_url'],
            'hash' => md5($datecreated)
        ));

        $surveyid = $this->db->insert_id();

        if (!$surveyid) {
           // return false;
        }

        logActivity('New Survey Added [ID: '.$surveyid.', Subject: ' . $data['subject'] . ']');

        return $surveyid;
    }

    /**
     * Delete survey and all connections
     * @param  mixed $surveyid survey id
     * @return boolean
     */
    public function delete($surveyid)
    {

        $affectedRows = 0;
        $this->db->where('surveyid', $surveyid);
        $this->db->delete('tblsurveys');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        // get all questions from the survey

        $this->db->where('surveyid', $surveyid);
        $questions = $this->db->get('tblsurveyquestions')->result_array();

        // Delete the question boxes
        foreach ($questions as $question) {

            $this->db->where('questionid', $question['questionid']);
            $this->db->delete('tblsurveyquestionboxes');
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }

            $this->db->where('questionid', $question['questionid']);
            $this->db->delete('tblsurveyquestionboxesdescription');
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }

        }
        $this->db->where('surveyid', $surveyid);
        $this->db->delete('tblsurveyquestions');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        $this->db->where('surveyid', $surveyid);
        $this->db->delete('tblsurveyresults');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        $this->db->where('surveyid', $surveyid);
        $this->db->delete('tblsurveyresultsets');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            logActivity('Survey Deleted [ID: ' . $surveyid . ']');
            return true;
        }

        return false;

    }

    /**
     * Get survey send log
     * @param  mixed $surveyid surveyid
     * @return array
     */
    public function get_survey_send_log($surveyid)
    {
        $this->db->where('surveyid', $surveyid);
        return $this->db->get('tblsurveysendlog')->result_array();
    }

    /**
     * Add new survey send log
     * @param mixed $surveyid surveyid
     * @param integer @iscronfinished always to 0
     * @param integer $lists array of lists which survey has been send
     */
    public function init_survey_send_log($surveyid,$iscronfinished = 0,$lists = array())
    {
        $this->db->insert('tblsurveysendlog', array(
            'date' => date('Y-m-d H:i:s'),
            'surveyid' => $surveyid,
            'total' => 0,
            'iscronfinished' => $iscronfinished,
            'send_to_mail_lists' => serialize($lists)
        ));

        logActivity('Survey Email Lists Send Setup [ID: ' . $surveyid . ', Lists: '.implode(' ',$lists).']');
    }

    /**
     * Add survey result by user
     * @param mixed $id     surveyid
     * @param mixed $result $_POST results/questions answers
     */
    public function add_survey_result($id, $result)
    {

        $userid = NULL;


        if (is_logged_in()) {
            $userid = get_staff_user_id();
        }

        $this->db->insert('tblsurveyresultsets', array(
            'date' => date('Y-m-d H:i:s'),
            'userid' => $userid,
            'surveyid' => $id,
            'ip' => $this->input->ip_address(),
            'useragent' => substr($this->input->user_agent(), 0, 149)
        ));

        $resultsetid = $this->db->insert_id();
        if ($resultsetid) {
            if (isset($result['selectable']) && sizeof($result['selectable']) > 0) {
                foreach ($result['selectable'] as $boxid => $question_answers) {
                    foreach ($question_answers as $questionid => $answer) {
                        $count = count($answer);
                        for ($i = 0; $i < $count; $i++) {
                            $this->db->insert('tblsurveyresults', array(
                                'boxid' => $boxid,
                                'boxdescriptionid' => $answer[$i],
                                'surveyid' => $id,
                                'questionid' => $questionid,
                                'resultsetid' => $resultsetid
                            ));
                        }
                    }
                }
            }

            unset($result['selectable']);
            foreach ($result['question'] as $questionid => $val) {
                $boxid = $this->get_question_box_id($questionid);
                $this->db->insert('tblsurveyresults', array(
                    'boxid' => $boxid,
                    'surveyid' => $id,
                    'questionid' => $questionid,
                    'answer' => $val[0],
                    'resultsetid' => $resultsetid
                ));
            }

          return true;

        }

        return false;

    }

    /**
     * Remove survey question
     * @param  mixed $questionid questionid
     * @return boolean
     */
    public function remove_question($questionid)
    {
        $affectedRows = 0;

        $this->db->where('questionid', $questionid);
        $this->db->delete('tblsurveyquestionboxesdescription');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        $this->db->where('questionid', $questionid);
        $this->db->delete('tblsurveyquestionboxes');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        $this->db->where('questionid', $questionid);
        $this->db->delete('tblsurveyquestions');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        if ($affectedRows > 0) {
            logActivity('Survey Question Deleted [' . $questionid . ']');
            return true;
        }
        return false;
    }

    /**
     * Remove survey question box description / radio/checkbox
     * @param  mixed $questionboxdescriptionid question box description id
     * @return boolean
     */
    public function remove_box_description($questionboxdescriptionid)
    {
        $this->db->where('questionboxdescriptionid', $questionboxdescriptionid);
        $this->db->delete('tblsurveyquestionboxesdescription');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Add survey box description radio/checkbox
     * @param mixed $questionid  question id
     * @param mixed $boxid       main box id
     * @param string $description box question
     */
    public function add_box_description($questionid, $boxid, $description = '')
    {
        $this->db->insert('tblsurveyquestionboxesdescription', array(
            'questionid' => $questionid,
            'boxid' => $boxid,
            'description' => $description
        ));
        return $this->db->insert_id();
    }

    /**
     * Private functino for insert question
     * @param  mixed $surveyid survey id
     * @param  string $question question
     * @return mixed
     */
    private function insert_survey_question($surveyid, $question = '')
    {
        $this->db->insert('tblsurveyquestions', array(
            'surveyid' => $surveyid,
            'question' => $question
        ));

        $insert_id = $this->db->insert_id();
        if($insert_id){
            logActivity('New Survey Question Added [SurveyID: ' . $surveyid . ']');
        }
        return $insert_id;
    }
    /**
     * Add new question type
     * @param  string $type       checkbox/textarea/radio/input
     * @param  mixed $questionid question id
     * @return mixed
     */
    private function insert_question_type($type, $questionid)
    {
        $this->db->insert('tblsurveyquestionboxes', array(
            'boxtype' => $type,
            'questionid' => $questionid
        ));
        return $this->db->insert_id();
    }

    /**
     * Add new question ti survey / ajax
     * @param array $data $_POST question data
     */
    public function add_survey_question($data)
    {
        $questionid = $this->insert_survey_question($data['surveyid']);

        if ($questionid) {
            $boxid    = $this->insert_question_type($data['type'], $questionid);
            $response = array(
                'questionid' => $questionid,
                'boxid' => $boxid
            );
            if ($data['type'] == 'checkbox' OR $data['type'] == 'radio') {

                $questionboxdescriptionid = $this->add_box_description($questionid, $boxid);
                array_push($response, array(
                    'questionboxdescriptionid' => $questionboxdescriptionid
                ));
            }

            return $response;

        } else {
            return false;
        }

    }

    /**
     * Update question / ajax
     * @param  array $data $_POST question data
     * @return boolean
     */
    public function update_question($data)
    {
        $_required = 1;
        if ($data['question']['required'] == 'false') {
            $_required = 0;
        }
        $affectedRows = 0;
        $this->db->where('questionid', $data['questionid']);
        $this->db->update('tblsurveyquestions', array(
            'question' => $data['question']['value'],
            'required' => $_required
        ));

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if (isset($data['boxes_description'])) {
            foreach ($data['boxes_description'] as $box_description) {
                $this->db->where('questionboxdescriptionid', $box_description[0]);
                $this->db->update('tblsurveyquestionboxesdescription', array(
                    'description' => $box_description[1]
                ));
                if ($this->db->affected_rows() > 0) {
                    $affectedRows++;
                }
            }
        }

        if ($affectedRows > 0) {
             logActivity('Survey Question Updated [QuestionID: '.$data['questionid'].']');
            return true;
        }

        return false;
    }

    /**
     * Reorder survey quesions / ajax
     * @param  mixed $data surveys order and question id
     */
    public function update_survey_questions_orders($data)
    {
        foreach ($data['data'] as $question) {
            $this->db->where('questionid', $question[0]);
            $this->db->update('tblsurveyquestions', array(
                'question_order' => $question[1]
            ));
        }
    }

    /**
     * Get quesion box id
     * @param  mixed $questionid questionid
     * @return integer
     */
    private function get_question_box_id($questionid)
    {
        $this->db->select('boxid');
        $this->db->from('tblsurveyquestionboxes');
        $this->db->where('questionid', $questionid);
        $box = $this->db->get()->row();
        return $box->boxid;
    }

    /**
     * Change survey status / active / inactive
     * @param  mixed $id     surveyid
     * @param  integer $status active or inactive
     */
    public function change_survey_status($id, $status)
    {
        $this->db->where('surveyid', $id);
        $this->db->update('tblsurveys', array(
            'active' => $status
        ));
        logActivity('Survey Status Changed [SurveyID: ' . $id . ' - Active: ' . $status . ']');
    }
}
