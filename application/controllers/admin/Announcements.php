<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Announcements extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('announcements_model');
    }

    /* List all announcements */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns     = array(
                'name'
                );
            $sIndexColumn = "announcementid";
            $sTable       = 'tblannouncements';

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable,array(),array(),array('announcementid'));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name') {
                        $_data = '<a href="' . admin_url('announcements/announcement/' . $aRow['announcementid']) . '">' . $_data . '</a>';
                    }
                    $row[] = $_data;
                }

                $options = icon_btn('admin/announcements/announcement/' . $aRow['announcementid'], 'pencil-square-o');
                $row[]   = $options .= icon_btn('admin/announcements/delete/' . $aRow['announcementid'], 'remove', 'btn-danger');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
        $data['title'] = _l('announcements');
        $this->load->view('admin/announcements/manage', $data);
    }

    /* Edit announcement or add new if passed id */
    public function announcement($id = '')
    {

        if ($this->input->post()) {
            if ($id == '') {
                $id = $this->announcements_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly',_l('announcement')));
                    redirect(admin_url('announcements/announcement/' . $id));
                }
            } else {
                $success = $this->announcements_model->update($this->input->post(), $id);
                if ($success) {
                     set_alert('success', _l('updated_successfuly',_l('announcement')));

                }

                redirect(admin_url('announcements/announcement/' . $id));
            }
        }

        if ($id == '') {
            $title = _l('add_new',_l('announcement_lowercase'));
        } else {
            $data['announcement'] = $this->announcements_model->get($id);
            $title                = _l('edit',_l('announcement_lowercase'));
        }

        $data['title'] = $title;
        $this->load->view('admin/announcements/announcement', $data);
    }

    /* Delete announcement from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('announcements'));
        }

        $response = $this->announcements_model->delete($id);

        if ($response == true) {
            set_alert('success', _l('deleted',_l('announcement')));
        } else {
            set_alert('warning', _l('problem_deleting',_l('announcement_lowercase')));
        }

        redirect(admin_url('announcements'));
    }
}
