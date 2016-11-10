<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('tasks_model');

    }

    /* Open also all taks if user access this /tasks url */
    public function index($id = '')
    {
        $this->list_tasks($id);
    }
    /* List all tasks */
    public function list_tasks($id = '')
    {
        // if passed from url
        $_custom_view = '';
        if ($this->input->get('custom_view')) {
            $_custom_view = $this->input->get('custom_view');
        }
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'name',
                'startdate',
                'rel_id',
                'finished',
            );

            $where = array();

            if ($this->input->post('custom_view')) {
                $view   = $this->input->post('custom_view');
                $_where = '';
                if ($view == 'finished') {
                    $_where = 'AND finished = 1';
                } else if ($view == 'unfinished') {
                    $_where = 'AND finished = 0';
                } else if ($view == 'not_assigned') {
                    $_where = 'AND tblstafftasks.id NOT IN (SELECT taskid FROM tblstafftaskassignees)';
                } else if ($view == 'due_date_passed') {
                    $_where = 'AND (duedate < "' . date('Y-m-d') . '" AND duedate IS NOT NULL) AND finished = 0';
                } else if($view == 'tasks_staff_private'){
                    $_where = 'AND is_public = 0';
                } else if($view == 'tasks_staff_public'){
                    $_where = 'AND is_public = 1';
                }

                if($_where != ''){
                    array_push($where, $_where);
                }
            }

            if (!is_admin()) {
                $sql = 'AND (tblstafftasks.id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid = ' . get_staff_user_id() . ') OR tblstafftasks.id IN (SELECT taskid FROM tblstafftasksfollowers WHERE staffid = ' . get_staff_user_id() . ') OR addedfrom=' . get_staff_user_id() . ') OR is_public = 1';
                array_push($where, $sql);
            }

            $join = array();
            $custom_fields = get_custom_fields('tasks',array('show_on_table'=>1));

            $i = 0;
            foreach($custom_fields as $field){
                    array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
                    array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblstafftasks.id = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
                    $i++;
            }

            $sIndexColumn = "id";
            $sTable       = 'tblstafftasks';
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblstafftasks.id',
                'dateadded',
                'priority',
                'finished',
                'rel_type',
                'duedate'
            ));
            $output       = $result['output'];
            $rResult      = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {

                    if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
                        $_data = $aRow[ strafter($aColumns[$i],'as ')];
                    } else {
                        $_data = $aRow[ $aColumns[$i] ];
                    }

                    if ($aColumns[$i] == 'name') {

                        $task_name = $_data;
                        $_data     = '';
                        if ($aRow['finished'] == 1) {
                            $_data .= '<a href="#" onclick="unmark_complete(' . $aRow['id'] . '); return false;"><i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip" title="' . _l('task_unmark_as_complete') . '"></i></a>';
                        } else {
                            $_data .= '<a href="#" onclick="mark_complete(' . $aRow['id'] . '); return false;"><i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" title="' . _l('task_single_mark_as_complete') . '"></i></a>';
                        }
                        $_data .= '<a href="#" class="main-tasks-table-href-name" onclick="init_task(' . $aRow['id'] . '); return false;">' . $task_name . '</a>';
                    } else if ($aColumns[$i] == 'startdate') {
                        $_data = _d($aRow['startdate']);
                    } else if($aColumns[$i] == 'finished'){
                        if($_data == 1){
                            $_data = _l('task_table_is_finished_indicator');
                        } else {
                            $_data = _l('task_table_is_not_finished_indicator');
                        }
                    } else if ($aColumns[$i] == 'priority') {
                        if ($_data == _l('task_priority_low')) {
                            $label_class = 'label-default';
                        } else if ($_data == _l('task_priority_medium')) {
                            $label_class = 'label-info';
                        } else if ($_data == _l('task_priority_high')) {
                            $label_class = 'label-warning';
                        } else {
                            $label_class = 'label-danger';
                        }
                        $_data = '<span class="label ' . $label_class . '">' . $aRow['priority'] . '</span>';
                    } else if ($aColumns[$i] == 'rel_id') {
                        if (!empty($_data)) {
                            $rel_data   = get_relation_data($aRow['rel_type'], $aRow['rel_id']);
                            $rel_values = get_relation_values($rel_data, $aRow['rel_type']);
                            $_data      = '<a data-toggle="tooltip" class="pull-left" title="' . ucfirst($aRow['rel_type']) . '" href="' . $rel_values['link'] . '">' . $rel_values['name'] . '</a>';
                            // for exporting
                            $_data .= '<span class="hide"> - ' . ucfirst($aRow['rel_type']) . '</span>';
                        }
                    }
                    $row[] = $_data;
                }

                if ((!empty($aRow['duedate']) && $aRow['duedate'] < date('Y-m-d')) && $aRow['finished'] == 0) {
                    $row['DT_RowClass'] = 'text-danger bold';
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['taskid'] = '';
        if (is_numeric($id)) {
            $data['taskid'] = $id;
        }
        $data['bodyclass'] = 'small-table';
        $data['custom_view'] = $_custom_view;
        $data['title']       = _l('tasks');
        $this->load->view('admin/tasks/manage', $data);
    }

    public function init_relation_tasks($rel_id, $type)
    {

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'name',
                'startdate'
            );

            $where = array();

            if (!is_admin()) {
                array_push($where, 'AND (id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid = ' . get_staff_user_id() . ') OR id IN (SELECT taskid FROM tblstafftasksfollowers WHERE staffid = ' . get_staff_user_id() . ') OR addedfrom=' . get_staff_user_id() . ') OR is_public = 1');
            }

            array_push($where, 'AND rel_id="' . $rel_id . '" AND rel_type="' . $type . '"');

            $join = array();
            $custom_fields = get_custom_fields('tasks',array('show_on_table'=>1));

            $i = 0;
            foreach($custom_fields as $field){
                    array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
                    array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblstafftasks.id = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
                    $i++;
            }

            $sIndexColumn = "id";
            $sTable       = 'tblstafftasks';
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblstafftasks.id',
                'dateadded',
                'priority',
                'finished'
            ));
            $output       = $result['output'];
            $rResult      = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {

                   if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
                        $_data = $aRow[ strafter($aColumns[$i],'as ')];
                    } else {
                        $_data = $aRow[ $aColumns[$i] ];
                    }

                    if ($aColumns[$i] == 'name') {
                        $task_name = $_data;
                        $_data     = '';
                        if ($aRow['finished'] == 1) {
                            $_data .= '<a href="#" onclick="unmark_complete(' . $aRow['id'] . '); return false;"><i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip" title="' . _l('task_unmark_as_complete') . '"></i></a>';
                        } else {
                            $_data .= '<a href="#" onclick="mark_complete(' . $aRow['id'] . '); return false;"><i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" title="' . _l('task_single_mark_as_complete') . '"></i></a>';
                        }

                        $_data .= '<a href="#" class="main-tasks-table-href-name" onclick="init_task_related_modal(' . $aRow['id'] . '); return false;">' . $task_name . '</a>';

                        // $_data = '<a href="'.admin_url('tasks/list_tasks/'.$aRow['id']).'">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'startdate') {
                        $_data = _d($aRow['startdate']);
                    } else if ($aColumns[$i] == 'priority') {
                        if ($_data == _l('task_priority_low')) {
                            $label_class = 'label-default';
                        } else if ($_data == _l('task_priority_medium')) {
                            $label_class = 'label-info';
                        } else if ($_data == _l('task_priority_high')) {
                            $label_class = 'label-warning';
                        } else {
                            $label_class = 'label-danger';
                        }
                        $_data = '<span class="label ' . $label_class . '">' . $aRow['priority'] . '</span>';
                    }
                    $row[] = $_data;
                }
                if ($aRow['finished'] == 1) {
                    $row['DT_RowClass'] = 'text-success line-throught bold';
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }
    /* Add new task or update existing */
    public function task($id = '')
    {
        if (!has_permission('manageTasks')) {
            access_denied('manageTasks');
        }

        $data = array();
        if ($this->input->post()) {
            if ($id == '') {
                $id = $this->tasks_model->add($this->input->post());
                $_id = false;
                $success = false;
                $message = '';
                if ($id) {
                    $success = true;
                    $_id = $id;
                    $message = _l('added_successfuly', _l('task'));
                }
                echo json_encode(array('success'=>$success,'id'=>$_id,'message'=>$message));
            } else {
                $success = $this->tasks_model->update($this->input->post(), $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfuly', _l('task'));
                }
                echo json_encode(array('success'=>$success,'message'=>$message));
            }
            die;
        }

        if ($id == '') {
            $title = _l('add_new', _l('task_lowercase'));
        } else {
            $data['task'] = $this->tasks_model->get($id);
            $title        = _l('edit', _l('task_lowercase'));
        }

        $data['id'] = $id;
        $data['title'] = $title;
        $this->load->view('admin/tasks/task',$data);

    }

    /* Get task data in a right pane */
    public function get_task_data()
    {
        $taskid = $this->input->post('taskid');
        // Task main data
        $task   = $this->tasks_model->get($taskid);

        if (!$task) {
            echo 'Task not found';
            die();
        }


        $data['task']      = $task;
        $data['id'] = $task->id;
        // Get all comments html
        $data['comments']  = $this->get_task_comments($task->id, true);
        // Get task assignees
        $data['assignees'] = $this->tasks_model->get_task_assignees($taskid);
        // Get task followers
        $data['followers'] = $this->tasks_model->get_task_followers($taskid);
        $this->load->view('admin/tasks/view_task_template', $data);

    }

    public function get_tasks_relation_data_single()
    {
        $taskid = $this->input->post('taskid');
        // Task main data
        $task   = $this->tasks_model->get($taskid);

        if (!$task) {
            echo 'false';
            die();
        }

        $data['task'] = $task;
        $data['id'] = $task->id;

        // Get all comments html
        $data['comments']  = $this->get_task_comments($task->id, true);
        // Get task assignees
        $data['assignees'] = $this->tasks_model->get_task_assignees($taskid);
        // Get task followers
        $data['followers'] = $this->tasks_model->get_task_followers($taskid);
        $this->load->view('admin/tasks/view_task_template', $data);
    }

    public function init_checklist_items(){
         if($this->input->is_ajax_request()){
            if($this->input->post()){
                $post_data = $this->input->post();
                $data['task_id'] = $post_data['taskid'];
                $data['checklists'] = $this->tasks_model->get_checklist_items($post_data['taskid']);
                $this->load->view('admin/tasks/checklist_items_template',$data);
            }
        }
    }

    public function checkbox_action($listid,$value){
        $this->db->where('id',$listid);
        $this->db->update('tbltaskchecklists',array('finished'=>$value));
    }

    public function add_checklist_item(){
        if($this->input->is_ajax_request()){
            if($this->input->post()){
                echo json_encode(array('success'=>$this->tasks_model->add_checklist_item($this->input->post())));
            }
        }
    }
    public function update_checklist_order(){
       if($this->input->is_ajax_request()){
            if($this->input->post()){
              $this->tasks_model->update_checklist_order($this->input->post());
            }
        }
    }
    public function delete_checklist_item($id){
      $list = $this->tasks_model->get_checklist_item($id);
      if(has_permission('manageTasks') || $list->addedfrom == get_staff_user_id()){

        if($this->input->is_ajax_request()){
           echo json_encode(array('success'=>$this->tasks_model->delete_checklist_item($id)));
       }

        }
    }
    public function update_checklist_item(){
           if($this->input->is_ajax_request()){
            if($this->input->post()){
               $this->tasks_model->update_checklist_item($this->input->post('listid'),$this->input->post('description'));
            }
        }
    }
    public function make_public($task_id){
        if(!has_permission('manageTasks')){
            json_encode(array('success'=>false));
            die;
        }

        echo json_encode(array('success'=>$this->tasks_model->make_public($task_id)));
    }
    /* Add new task comment / ajax */
    public function add_task_comment()
    {
        echo json_encode(array(
            'success' => $this->tasks_model->add_task_comment($this->input->post())
        ));
    }
    /* Add new task follower / ajax */
    public function add_task_followers()
    {

        if (!has_permission('manageTasks')) {
            access_denied('manageTasks');
        }

        echo json_encode(array(
            'success' => $this->tasks_model->add_task_followers($this->input->post())
        ));
    }
    /* Add task assignees / ajax */
    public function add_task_assignees()
    {
        if (!has_permission('manageTasks')) {
            access_denied('manageTasks');
        }

        echo json_encode(array(
            'success' => $this->tasks_model->add_task_assignees($this->input->post())
        ));
    }

    /* Check which staff is already in this group and remove from select */
    public function reload_followers_select($taskid)
    {
        $options = '';
        $this->load->model('staff_model');
        $staff = $this->staff_model->get();

        foreach ($staff as $follower) {
            if (total_rows('tblstafftasksfollowers', array(
                'staffid' => $follower['staffid'],
                'taskid' => $taskid
            )) == 0) {
                $options .= '<option value="' . $follower['staffid'] . '">' . get_staff_full_name($follower['staffid']) . '</option>';
            }
        }

        echo $options;
    }
    /* Check which staff is already in this group and remove from select */
    public function reload_assignees_select($taskid)
    {
        $options = '';
        $this->load->model('staff_model');
        $staff = $this->staff_model->get();

        foreach ($staff as $assignee) {
            if (total_rows('tblstafftaskassignees', array(
                'staffid' => $assignee['staffid'],
                'taskid' => $taskid
            )) == 0) {
                $options .= '<option value="' . $assignee['staffid'] . '">' . get_staff_full_name($assignee['staffid']) . '</option>';
            }
        }

        echo $options;
    }
    /* Get all tasks comments */
    public function get_task_comments($taskid, $return = false)
    {

        $result   = $this->tasks_model->get_task_comments($taskid);
        $comments = '';
        foreach ($result as $comment) {
            $comments .= '<div class="col-md-12 mbot10" data-commentid="' . $comment['id'] . '">';
            $comments .= '<a href="' . admin_url('profile/' . $comment['commentator']) . '">' . staff_profile_image($comment['commentator'], array(
                'staff-profile-image-small',
                'media-object img-circle pull-left mright10'
            )) . '</a>';
            if ($comment['commentator'] == get_staff_user_id() || is_admin()) {
                $comments .= '<span class="pull-right"><a href="#" onclick="remove_task_comment(' . $comment['id'] . '); return false;"><i class="fa fa-trash text-danger"></i></span></a>';
            }
            $comments .= '<div class="media-body">';
            $comments .= '<a href="' . admin_url('profile/' . $comment['commentator']) . '">' . get_staff_full_name($comment['commentator']) . '</a> <br />';
            $comments .= check_for_links($comment['content']) . '<br />';
            $comments .= '<small class="mtop10 text-muted">' . _dt($comment['dateadded']) . '</small>';

            $comments .= '</div>';
            $comments .= '<hr />';
            $comments .= '</div>';

        }


        if ($return == false) {
            echo $comments;
        } else {
            return $comments;
        }

    }
    /* Remove task comment / ajax */
    public function remove_comment($id)
    {
        echo json_encode(array(
            'success' => $this->tasks_model->remove_comment($id)
        ));
    }
    /* Remove assignee / ajax */
    public function remove_assignee($id, $taskid)
    {
        if (!has_permission('manageTasks')) {
            access_denied('manageTasks');
        }

        $success = $this->tasks_model->remove_assignee($id, $taskid);
        $message = '';
        if ($success) {
            $message = _l('task_assignee_removed');
        }
        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
    }
    /* Remove task follower / ajax */
    public function remove_follower($id, $taskid)
    {
        if (!has_permission('manageTasks')) {
            access_denied('manageTasks');
        }
        $success = $this->tasks_model->remove_follower($id, $taskid);
        $message = '';
        if ($success) {
            $message = _l('task_follower_removed');
        }
        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
    }
    /* Mark task as complete / ajax*/
    public function mark_complete($id)
    {
        $success = $this->tasks_model->mark_complete($id);
        $message = '';
        if ($success) {
            $message = _l('task_marked_as_complete');
        }
        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
    }
    /* Unmark task as complete / ajax*/
    public function unmark_complete($id)
    {
        $success = $this->tasks_model->unmark_complete($id);
        $message = '';
        if ($success) {
            $message = _l('task_unmarked_as_complete');
        }
        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
    }
    /* Delete task from database */
    public function delete_task($id)
    {
        if (!has_permission('manageTasks')) {
            access_denied('manageTasks');
        }
        $success = $this->tasks_model->delete_task($id);
        $message = _l('problem_deleting', _l('task_lowercase'));

        if ($success) {
            $message = _l('deleted', _l('task'));
        }

        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
    }

    /**
     * Reload all tasks attachments to view
     * @since  Version 1.0.1
     * @param  mixed $taskid taskid
     * @return json
     */
    public function reload_task_attachments($taskid)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->tasks_model->get_task_attachments($taskid));
        }
    }
    /**
     * Remove task attachment
     * @since  Version 1.0.1
     * @param  mixed $id attachment it
     * @return json
     */
    public function remove_task_attachment($id)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode(array(
                'success' => $this->tasks_model->remove_task_attachment($id)
            ));
        }
    }
    /**
     * Upload task attachment
     * @since  Version 1.0.1
     */
    public function upload_file()
    {
        if ($this->input->post()) {
            $taskid = $this->input->post('taskid');
            $file = handle_tasks_attachments($taskid);
            if ($file) {
                $success = $this->tasks_model->add_attachment_to_database($taskid, $file);
                echo json_encode(array(
                    'success' => $success
                ));
            }
        }
    }
}
