<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Goals extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('goals_model');
        if (!has_permission('manageGoals')) {
            access_denied('manageGoals');
        }
    }

    /* List all announcements */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'subject',
                'achievement',
                'start_date',
                'end_date',
                'goal_type'
            );

            $sIndexColumn = "id";
            $sTable       = 'tblgoals';

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), array(), array(
                'id'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'subject') {
                        $_data = '<a href="' . admin_url('goals/goal/' . $aRow['id']) . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'start_date' || $aColumns[$i] == 'end_date') {
                        $_data = _d($_data);
                    } else if ($aColumns[$i] == 'goal_type') {
                        $_data = format_goal_type($_data);
                    }
                    $row[] = $_data;
                }
                ob_start();
                $achievement          = $this->goals_model->calculate_goal_achievement($aRow['id']);
                $percent              = $achievement['percent'];
                $progress_bar_percent = $achievement['progress_bar_percent']; ?>
                <input type="hidden" value="<?php echo $progress_bar_percent;?>" name="percent">
                 <div class="goal-progress" data-reverse="true">
                   <strong class="goal-percent"><?php echo $percent; ?>%</strong>
                 </div>
                <?php
                $progress = ob_get_contents();
                ob_end_clean();
                $row[]   = $progress;
                $options = icon_btn('admin/goals/goal/' . $aRow['id'], 'pencil-square-o');
                $row[]   = $options .= icon_btn('admin/goals/delete/' . $aRow['id'], 'remove', 'btn-danger');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('goals_tracking');
        $this->load->view('admin/goals/manage', $data);
    }

    public function goal($id = '')
    {

        if ($this->input->post()) {
            if ($id == '') {
                $id = $this->goals_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('goal')));
                    redirect(admin_url('goals/goal/' . $id));
                }
            } else {
                $success = $this->goals_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('goal')));
                }
                redirect(admin_url('goals/goal/' . $id));
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('goal_lowercase'));
        } else {
            $data['goal']        = $this->goals_model->get($id);
            $data['achievement'] = $this->goals_model->calculate_goal_achievement($id);
            $title               = _l('edit', _l('goal_lowercase'));
        }


        $this->load->model('contracts_model');
        $data['contract_types'] = $this->contracts_model->get_contract_types();

        $data['title'] = $title;
        $this->load->view('admin/goals/goal', $data);
    }
    /* Delete announcement from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('goals'));
        }

        $response = $this->goals_model->delete($id);

        if ($response == true) {
            set_alert('success', _l('deleted', _l('goal')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('goal_lowercase')));
        }

        redirect(admin_url('goals'));
    }

    public function notify($id, $notify_type)
    {
        if (!$id) {
            redirect(admin_url('goals'));
        }

        $success = $this->goals_model->notify_staff_members($id, $notify_type);
        if ($success) {
            set_alert('success', _l('goal_notify_staff_notified_manualy_success'));
        } else {
            set_alert('warning', _l('goal_notify_staff_notified_manualy_fail'));
        }

        redirect(admin_url('goals/goal/' . $id));
    }
}
