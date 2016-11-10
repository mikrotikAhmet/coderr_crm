<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('emails_model');
        $this->load->model('staff_model');
    }

    /**
     * Get task by id
     * @param  mixed $id task id
     * @return object
     */
    public function get($id)
    {

        $is_admin = is_admin();
        $this->db->where('id', $id);
        if (!$is_admin) {
            $this->db->where('(id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid = ' . get_staff_user_id() . ') OR id IN (SELECT taskid FROM tblstafftasksfollowers WHERE staffid = ' . get_staff_user_id() . ') OR addedfrom=' . get_staff_user_id() . ' OR is_public = 1)');
        }
        return $this->db->get('tblstafftasks')->row();
    }

    /**
     * Add new staff task
     * @param array $data task $_POST data
     * @return mixed
     */
    public function add($data)
    {

        $data['startdate']   = to_sql_date($data['startdate']);
        $data['duedate']     = to_sql_date($data['duedate']);
        $data['dateadded']   = date('Y-m-d');
        $data['addedfrom']   = get_staff_user_id();
        $data['description'] = nl2br($data['description']);

        unset($data['task_rel_id']);

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        if (isset($data['is_public'])) {
            $data['is_public'] = 1;
        } else {
            $data['is_public'] = 0;
        }

        if (empty($data['rel_type'])) {
            unset($data['rel_type']);
            unset($data['rel_id']);
        } else {
            if (empty($data['rel_id'])) {
                unset($data['rel_type']);
                unset($data['rel_id']);
            }
        }

        $this->db->insert('tblstafftasks', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }

            logActivity('New Task Added [ID:' . $insert_id . ', Name: ' . $data['name'] . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update task data
     * @param  array $data task data $_POST
     * @param  mixed $id   task id
     * @return boolean
     */
    public function update($data, $id)
    {
        $affectedRows        = 0;
        $data['startdate']   = to_sql_date($data['startdate']);
        $data['duedate']     = to_sql_date($data['duedate']);
        $data['description'] = nl2br($data['description']);

        unset($data['task_rel_id']);

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        if (isset($data['is_public'])) {
            $data['is_public'] = 1;
        } else {
            $data['is_public'] = 0;
        }

        if (empty($data['rel_type'])) {
           $data['rel_id'] = NULL;
           $data['rel_type'] = '';
        } else {
            if (empty($data['rel_id'])) {
               $data['rel_id'] = NULL;
               $data['rel_type'] = '';
            }
        }

        $this->db->where('id', $id);
        $this->db->update('tblstafftasks', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            logActivity('Task Updated [ID:' . $id . ', Name: ' . $data['name'] . ']');
        }

        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    public function get_checklist_item($id){
        $this->db->where('id',$id);
        return $this->db->get('tbltaskchecklists')->row();
    }
    public function get_checklist_items($taskid){
        $this->db->where('taskid',$taskid);
        $this->db->order_by('list_order','asc');
        return $this->db->get('tbltaskchecklists')->result_array();
    }
    /**
     * Add task new blank check list item
     * @param mixed $data $_POST data with taxid
     */
    public function add_checklist_item($data){

        $this->db->insert('tbltaskchecklists',array(
            'taskid'=>$data['taskid'],
            'description'=>'',
            'dateadded'=>date('Y-m-d H:i:s'),
            'addedfrom'=>get_staff_user_id(),
            ));

        $insert_id = $this->db->insert_id();
        if($insert_id){
            return true;
        }

        return false;
    }

    public function delete_checklist_item($id){

        $this->db->where('id',$id);
        $this->db->delete('tbltaskchecklists');
        if($this->db->affected_rows() > 0){
            return true;
        }


        return false;
    }

    public function update_checklist_order($data){
        foreach($data['order'] as $order){
            $this->db->where('id',$order[0]);
            $this->db->update('tbltaskchecklists',array('list_order'=>$order[1]));
        }
    }
    /**
     * Update checklist item
     * @param  mixed $id          check list id
     * @param  mixed $description checklist description
     * @return void
     */
     public function update_checklist_item($id,$description){
        $this->db->where('id',$id);
        $this->db->update('tbltaskchecklists',array('description'=>nl2br($description)));
    }
    /**
     * Make task public
     * @param  mixed $task_id task id
     * @return boolean
     */
    public function make_public($task_id){
        $this->db->where('id',$task_id);
        $this->db->update('tblstafftasks',array('is_public'=>1));
        if($this->db->affected_rows() > 0){
            return true;
        }

        return false;
    }
    /**
     * Get task creator id
     * @param  mixed $taskid task id
     * @return mixed
     */
    public function get_task_creator_id($taskid)
    {
        return $this->get($taskid)->addedfrom;
    }

    /**
     * Add new task comment
     * @param array $data comment $_POST data
     * @return boolean
     */
    public function add_task_comment($data)
    {

        $data['staffid']   = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['content']   = nl2br($data['content']);
        $this->db->insert('tblstafftaskcomments', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $task        = $this->get($data['taskid']);
            $description = 'commented on task ' . substr($task->name, 0, 50) . '...';
            $this->_send_task_responsible_users_notification($description, $data['taskid'], false, 'task-commented');
            return true;
        }
        return false;
    }

    /**
     * Add task followers
     * @param array $data followers $_POST data
     * @return boolean
     */
    public function add_task_followers($data)
    {
        $this->db->insert('tblstafftasksfollowers', array(
            'taskid' => $data['taskid'],
            'staffid' => $data['follower']
        ));

        if ($this->db->affected_rows() > 0) {
            $task = $this->get($data['taskid']);
            if ($task->addedfrom != $data['follower']) {

                $description = 'added you as follower on task ' . substr($task->name, 0, 50);
                add_notification(array(
                    'description' => $description,
                    'touserid' => $data['follower'],
                    'link' => 'tasks/list_tasks/' . $task->id
                ));

                $member = $this->staff_model->get($data['follower']);
                $this->emails_model->send_email_template('task-added-as-follower', $member->email, false, false, $member->staffid, false, false, false, $task->id);
            }

            $description = 'added ' . get_staff_full_name($data['follower']) . ' as follower on task ' . substr($task->name, 0, 50) . '...';

            if ($data['follower'] == get_staff_user_id()) {
                $description = 'added himself as follower on task ' . substr($task->name, 0, 50) . '...';
            }
            $this->_send_task_responsible_users_notification($description, $data['taskid'], $data['follower']);
            return true;
        }
        return false;
    }

    /**
     * Assign task to staff
     * @param array $data task assignee $_POST data
     * @return boolean
     */
    public function add_task_assignees($data)
    {

        $this->db->insert('tblstafftaskassignees', array(
            'taskid' => $data['taskid'],
            'staffid' => $data['assignee']
        ));

        if ($this->db->affected_rows() > 0) {
            $creator = $this->get_task_creator_id($data['taskid']);
            $task    = $this->get($data['taskid']);
            if ($creator != $data['assignee']) {
                $description = 'assigned a task to you ' . substr($task->name, 0, 50) . '...';
                add_notification(array(
                    'description' => $description,
                    'touserid' => $data['assignee'],
                    'link' => 'tasks/list_tasks/' . $task->id
                ));

                $member = $this->staff_model->get($data['assignee']);
                $this->emails_model->send_email_template('task-assigned', $member->email, false, false, $member->staffid, false, false, false, $task->id);
            }

            $description = 'assigned ' . get_staff_full_name($data['assignee']) . ' to task ' . substr($task->name, 0, 50) . '...';
            if ($data['assignee'] == get_staff_user_id()) {
                $description = 'will do task ' . substr($task->name, 0, 50) . '...';
            }
            $this->_send_task_responsible_users_notification($description, $data['taskid'], $data['assignee']);
            return true;
        }

        return false;
    }
    /**
     * Get all task attachments
     * @param  mixed $taskid taskid
     * @return array
     */
    public function get_task_attachments($taskid)
    {
        $this->db->where('taskid', $taskid);
        $this->db->order_by('dateadded', 'desc');
        $attachments = $this->db->get('tblstafftasksattachments')->result_array();

        $i = 0;
        foreach ($attachments as $attachment) {
            $attachments[$i]['mimeclass'] = get_mime_class($attachment['filetype']);
            $i++;
        }

        return $attachments;
    }

    /**
     * Remove task attachment from server and database
     * @param  mixed $id attachmentid
     * @return boolean
     */
    public function remove_task_attachment($id)
    {
        // Get the attachment
        $this->db->where('id', $id);
        $attachment = $this->db->get('tblstafftasksattachments')->row();
        if (is_file(TASKS_ATTACHMENTS_FOLDER . $attachment->taskid . '/' . $attachment->file_name)) {
            $deleted = unlink(TASKS_ATTACHMENTS_FOLDER . $attachment->taskid . '/' . $attachment->file_name);
            if ($deleted) {
                $this->db->where('id', $id);
                $this->db->delete('tblstafftasksattachments');
                if ($this->db->affected_rows() > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Add uploaded attachments to database
     * @since  Version 1.0.1
     * @param mixed $taskid     task id
     * @param array $attachment attachment data
     */
    public function add_attachment_to_database($taskid, $attachment)
    {
        $this->db->insert('tblstafftasksattachments', array(
            'file_name' => $attachment[0]['filename'],
            'dateadded' => date('Y-m-d H:i:s'),
            'taskid' => $taskid,
            'filetype' => $attachment[0]['filetype']
        ));

        if ($this->db->affected_rows() > 0) {
            $description = 'New attachment added';
            $this->_send_task_responsible_users_notification($description, $taskid, false, 'task-added-attachment');
            return true;
        }
        return false;
    }
    /**
     * Get all task followers
     * @param  mixed $id task id
     * @return array
     */
    public function get_task_followers($id)
    {
        $this->db->select('id,tblstafftasksfollowers.staffid as followerid');
        $this->db->from('tblstafftasksfollowers');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblstafftasksfollowers.staffid', 'left');
        $this->db->where('taskid', $id);

        return $this->db->get()->result_array();
    }

    /**
     * Get all task assigneed
     * @param  mixed $id task id
     * @return array
     */
    public function get_task_assignees($id)
    {
        $this->db->select('id,tblstafftaskassignees.staffid as assigneeid');
        $this->db->from('tblstafftaskassignees');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblstafftaskassignees.staffid', 'left');
        $this->db->where('taskid', $id);

        return $this->db->get()->result_array();
    }

    /**
     * Get task comment
     * @param  mixed $id task id
     * @return array
     */
    public function get_task_comments($id)
    {
        $this->db->select('id,dateadded,content,tblstaff.firstname,tblstaff.lastname,tblstafftaskcomments.staffid as commentator');
        $this->db->from('tblstafftaskcomments');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblstafftaskcomments.staffid', 'left');
        $this->db->where('taskid', $id);
        $this->db->order_by('dateadded', 'asc');

        return $this->db->get()->result_array();
    }

    /**
     * Remove task comment from database
     * @param  mixed $id task id
     * @return boolean
     */
    public function remove_comment($id)
    {
        // Check if user really creator
        $this->db->where('id', $id);
        $comment = $this->db->get('tblstafftaskcomments')->row();
        if ($comment->staffid == get_staff_user_id()) {
            $this->db->where('id', $id);
            $this->db->delete('tblstafftaskcomments');
            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove task assignee from database
     * @param  mixed $id     assignee id
     * @param  mixed $taskid task id
     * @return boolean
     */
    public function remove_assignee($id, $taskid)
    {

        $this->db->where('id', $taskid);
        $task = $this->db->get('tblstafftasks')->row();

        if ($task->addedfrom != get_staff_user_id()) {
            return false;
        }

        $this->db->where('id', $id);
        $this->db->delete('tblstafftaskassignees');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Remove task follower from database
     * @param  mixed $id     followerid
     * @param  mixed $taskid task id
     * @return boolean
     */
    public function remove_follower($id, $taskid)
    {
        $this->db->where('id', $taskid);
        $task = $this->db->get('tblstafftasks')->row();

        if ($task->addedfrom != get_staff_user_id()) {
            return false;
        }

        $this->db->where('id', $id);
        $this->db->delete('tblstafftasksfollowers');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Mark task as complete
     * @param  mixed $id task id
     * @return boolean
     */
    public function mark_complete($id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblstafftasks', array(
            'datefinished' => date('Y-m-d H:i:s'),
            'finished' => 1
        ));

        if ($this->db->affected_rows() > 0) {
            $task        = $this->get($id);
            $description = 'marked task as complete ' . substr($task->name, 0, 50) . '...';
            $this->_send_task_responsible_users_notification($description, $id, false, 'task-marked-as-finished');
            return true;
        }

        return false;
    }
    /**
     * Unmark task as complete
     * @param  mixed $id task id
     * @return boolean
     */
    public function unmark_complete($id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblstafftasks', array(
            'datefinished' => NULL,
            'finished' => 0
        ));

        if ($this->db->affected_rows() > 0) {
            $task        = $this->get($id);
            $description = 'unmarked task as complete ' . substr($task->name, 0, 50) . '...';
            $this->_send_task_responsible_users_notification($description, $id, false, 'task-unmarked-as-finished');
            return true;
        }

        return false;
    }

    /**
     * Delete task and all connections
     * @param  mixed $id taskid
     * @return boolean
     */
    public function delete_task($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblstafftasks');

        if ($this->db->affected_rows() > 0) {

            $this->db->where('taskid', $id);
            $this->db->delete('tblstafftasksfollowers');

            $this->db->where('taskid', $id);
            $this->db->delete('tblstafftaskassignees');

            $this->db->where('taskid', $id);
            $this->db->delete('tblstafftaskcomments');

            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'tasks');
            $this->db->delete('tblcustomfieldsvalues');

            if (is_dir(TASKS_ATTACHMENTS_FOLDER . $id)) {
                delete_dir(TASKS_ATTACHMENTS_FOLDER . $id);
            }

            return true;
        }

        return false;
    }

    /**
     * Send notification on task activity to creator,follower/s,assignee/s
     * @param  string  $description notification description
     * @param  mixed  $taskid      task id
     * @param  boolean $excludeid   excluded staff id to not send the notifications
     * @return boolean
     */
    private function _send_task_responsible_users_notification($description, $taskid, $excludeid = false, $email_template = '')
    {

        $this->load->model('staff_model');
        $staff = $this->staff_model->get();
        foreach ($staff as $member) {
            if (is_numeric($excludeid)) {
                if ($excludeid == $member['staffid']) {
                    continue;
                }
            }
            if ($member['staffid'] != get_staff_user_id()) {

                if ($this->is_task_follower($member['staffid'], $taskid) || $this->is_task_assignee($member['staffid'], $taskid) || $this->is_task_creator($member['staffid'], $taskid)) {
                    add_notification(array(
                        'description' => $description,
                        'touserid' => $member['staffid'],
                        'link' => 'tasks/list_tasks/' . $taskid
                    ));

                    if ($email_template != '') {
                        $this->emails_model->send_email_template($email_template, $member['email'], false, false, get_staff_user_id(), false, false, false, $taskid);
                    }
                }
            }
        }
    }

    /**
     * Check is user is task follower
     * @param  mixed  $userid staff id
     * @param  mixed  $taskid taskid
     * @return boolean
     */

    public function is_task_follower($userid, $taskid)
    {
        if (total_rows('tblstafftasksfollowers', array(
            'staffid' => $userid,
            'taskid' => $taskid
        )) == 0) {
            return false;
        }

        return true;
    }

    /**
     * Check is user is task assignee
     * @param  mixed  $userid staff id
     * @param  mixed  $taskid taskid
     * @return boolean
     */
    public function is_task_assignee($userid, $taskid)
    {
        if (total_rows('tblstafftaskassignees', array(
            'staffid' => $userid,
            'taskid' => $taskid
        )) == 0) {
            return false;
        }

        return true;
    }

    /**
     * Check is user is task creator
     * @param  mixed  $userid staff id
     * @param  mixed  $taskid taskid
     * @return boolean
     */
    public function is_task_creator($userid, $taskid)
    {
        if (total_rows('tblstafftasks', array(
            'addedfrom' => $userid,
            'id' => $taskid
        )) == 0) {
            return false;
        }

        return true;
    }

}
