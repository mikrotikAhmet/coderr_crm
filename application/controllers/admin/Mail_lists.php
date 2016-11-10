<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mail_lists extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('mail_lists_model');
        if (!has_permission('manageMailLists')) {
            access_denied('manageMailLists');
        }
    }
    /* List all mail lists */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns         = array(
                'listid',
                'name',
                'tblemaillists.datecreated'
                );
            $additionalSelect = array(
                'firstname',
                'lastname',
                'staffid'
                );

            $join = array(
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblemaillists.creator'
                );

            $sIndexColumn = "listid";
            $sTable       = 'tblemaillists';

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, array(), $additionalSelect);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name') {
                        $_data = '<a href="' . admin_url('mail_lists/view/' . $aRow['listid']) . '">' . $_data . '</a>';
                        $_data .= '<p>Total emails: ' . total_rows('tbllistemails', 'listid=' . $aRow['listid']) . '</p>';
                    } else if ($aColumns[$i] == 'tblemaillists.datecreated') {
                        $_data = _dt($_data);
                    }
                    $row[] = $_data;
                }

                $options = icon_btn('admin/mail_lists/view/' . $aRow['listid'], 'eye');
                $options .= icon_btn('admin/mail_lists/mail_list/' . $aRow['listid'], 'pencil-square-o');
                $row[] = $options .= icon_btn('admin/mail_lists/delete/' . $aRow['listid'], 'remove', 'btn-danger');

                array_splice($row, 3, 0, '<a href="' . admin_url('staff/member/' . $aRow['staffid']) . '">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>');

                $output['aaData'][] = $row;
            }

            $staff_mail_list_row   = array(
                '--',
                '<a href="' . site_url("admin/mail_lists/view/staff") . '" data-toggle="tooltip" title="'._l('cant_edit_mail_list').'">Staff</a>',
                '--',
                '--',
                '<a href="' . site_url("admin/mail_lists/view/staff") . '" class="btn btn-default btn-icon" ><i class="fa fa-eye"></i>'
                );
            $clients_mail_list_row = array(
                '--',
                '<a href="' . site_url("admin/mail_lists/view/clients") . '" data-toggle="tooltip" title="'._l('cant_edit_mail_list').'">Clients</a>',
                '--',
                '--',
                '<a href="' . site_url("admin/mail_lists/view/clients") . '" class="btn btn-default btn-icon" ><i class="fa fa-eye"></i>'
                );

            // Add clients and staff mail lists to top always
            array_unshift($output['aaData'], $staff_mail_list_row);
            array_unshift($output['aaData'], $clients_mail_list_row);
            echo json_encode($output);
            die();
        }

        $data['title'] = _l('mail_lists');
        $this->load->view('admin/mail_lists/manage', $data);
    }

    /* Add or update mail list */
    public function mail_list($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {
                $id = $this->mail_lists_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly',_l('mail_list')));
                    redirect(admin_url('mail_lists/mail_list/' . $id));
                }
            } else {
                $success = $this->mail_lists_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly',_l('mail_list')),'refresh');
                }

                redirect(admin_url('mail_lists/mail_list/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new',_l('mail_list_lowercase'));
        } else {
            $list                  = $this->mail_lists_model->get($id);
            $data['list']          = $list;
            $data['custom_fields'] = $this->mail_lists_model->get_list_custom_fields($list->listid);
            $title                 = _l('edit',_l('mail_list_lowercase')) . ' ' . $list->name;
        }
        $data['title'] = $title;
        $this->load->view('admin/mail_lists/list', $data);
    }
    /* View mail list all added emails */
    public function view($id)
    {

        if (!$id) {
            redirect(admin_url('mail_lists'));
        }

        if(is_numeric($id)){
            $data['custom_fields'] = $this->mail_lists_model->get_list_custom_fields($id);
        }

        if ($this->input->is_ajax_request()) {

            if(is_numeric($id)){

                $aColumns = array(
                    'email',
                    'dateadded'
                    );
                 if (count($data['custom_fields']) > 0) {
                      foreach ($data['custom_fields'] as $field) {
                        array_push($aColumns,'(SELECT value FROM tblmaillistscustomfieldvalues LEFT JOIN tblmaillistscustomfields ON tblmaillistscustomfields.customfieldid = '.$field['customfieldid'].' WHERE tblmaillistscustomfieldvalues.customfieldid ="'.$field['customfieldid'].'" AND (tblmaillistscustomfieldvalues.emailid = tbllistemails.emailid))');
                      }
                 }
                $sIndexColumn = "emailid";
                $sTable       = 'tbllistemails';

                $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), array(
                    'WHERE listid =' . $id
                    ), array(
                    'emailid'
                    ));
                $output  = $result['output'];
                $rResult = $result['rResult'];

                foreach ($rResult as $aRow) {
                    $row = array();
                    for ($i = 0; $i < count($aColumns); $i++) {
                        $_data = $aRow[$aColumns[$i]];
                        if ($aColumns[$i] == 'dateadded') {
                            $_data = _dt($_data);
                        }
                        $row[] = $_data;
                    }

                    $row[] = icon_btn('admin/mail_lists/delete/' . $aRow['emailid'], 'remove', 'btn-danger', array(
                        'onclick' => 'remove_email_from_mail_list(this,' . $aRow['emailid'] . '); return false;'
                        ));
                    $output['aaData'][] = $row;
                }

            } else if($id == 'clients' || $id == 'staff'){
                    $aColumns = array( 'email','datecreated');

                    $sIndexColumn = "userid";
                    $sTable = 'tblclients';

                    if($id == 'staff'){
                        $sIndexColumn = 'staffid';
                        $sTable = 'tblstaff';
                    }

                    $result = data_tables_init($aColumns,$sIndexColumn,$sTable);
                    $output = $result['output'];
                    $rResult = $result['rResult'];

                    foreach ( $rResult as $aRow )
                    {
                        $row = array();
                        for ( $i=0 ; $i<count($aColumns) ; $i++ )
                        {
                            $_data = $aRow[ $aColumns[$i] ];
                            if($aColumns[$i] == 'datecreated'){
                                $_data = _dt($_data);
                            }

                            // No delete option
                            $row[] = $_data;
                        }

                        $row[]  = '';

                        $output['aaData'][] = $row;
                    }
                }
            echo json_encode($output);
            die();
        }

        if ($id == 'staff' || $id == 'clients') {

            $list  = new stdClass();
            $title = _l('clients_mail_lists');
            $this->load->model('clients_model');
            $member = $this->clients_model->get();

            if ($id == 'staff') {
                $title  = _l('staff_mail_lists');
                $member = $this->load->model('staff_model');
                $member = $this->staff_model->get();
            }

            $list->emails = array();
            $i            = 0;
            foreach ($member as $email) {
                $list->emails[$i]['dateadded'] = $email['datecreated'];
                $list->emails[$i]['email']     = $email['email'];
                $i++;
            }

            $data['list']  = $list;
            $data['title'] = $title;
            $fixed_list    = true;

        } else {
            $list          = $this->mail_lists_model->get_data_for_view_list($id);
            $data['title'] = $list->name;
            $data['list']  = $list;
            $fixed_list    = false;
        }

        $data['fixedlist'] = $fixed_list;
        $this->load->view('admin/mail_lists/list_view', $data);
    }

    /* Add single email to mail list / ajax*/
    public function add_email_to_list()
    {
        if ($this->input->post()) {
            if ($this->input->is_ajax_request()) {
                echo json_encode($this->mail_lists_model->add_email_to_list($this->input->post()));
                die();
            }
        }
    }

    /* Remove single email from mail list / ajax */
    public function remove_email_from_mail_list($emailid)
    {
        if (!$emailid) {
            echo json_encode(array(
                'success' => false
                ));
            die();
        }

        echo json_encode($this->mail_lists_model->remove_email_from_mail_list($emailid));
        die();
    }
    /* Remove mail list custom field */
    public function remove_list_custom_field($fieldid)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->mail_lists_model->remove_list_custom_field($fieldid));
            die();
        }
    }

    /* Import .xls file with emails */
    public function import_emails()
    {
        require_once(APPPATH . 'third_party/Excel_reader/php-excel-reader/excel_reader2.php');
        require_once(APPPATH . 'third_party/Excel_reader/SpreadsheetReader.php');

        $filename = uniqid() . '_' . $_FILES["file_xls"]["name"];
        $temp_url = TEMP_FOLDER . $filename;
        if (move_uploaded_file($_FILES["file_xls"]["tmp_name"], $temp_url)) {

            try {
                $xls_emails = new SpreadsheetReader($temp_url);
            }
            catch (Exception $e) {
                die('Error loading file "' . pathinfo($temp_url, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
            $total_duplicate_emails = 0;
            $total_invalid_address  = 0;
            $total_added_emails     = 0;
            $mails_failed_to_insert = 0;

            $listid                 = $_POST['listid'];
            foreach ($xls_emails as $email) {
                if (isset($email[0]) && $email[0] !== '') {
                    $data['email'] = $email[0];
                    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                        $total_invalid_address++;
                        continue;
                    }

                    $data['listid'] = $listid;
                    if (count($email) > 1) {
                        $custom_fields       = $this->mail_lists_model->get_list_custom_fields($listid);
                        $total_custom_fields = count($custom_fields);
                        for ($i = 0; $i < $total_custom_fields; $i++) {
                            if ($email[$i + 1] !== '') {
                                $data['customfields'][$custom_fields[$i]['customfieldid']] = $email[$i + 1];
                            }
                        }
                    }

                    $success = $this->mail_lists_model->add_email_to_list($data);

                    if ($success['success'] == false && $success['duplicate'] == true) {
                        $total_duplicate_emails++;
                    } else if ($success['success'] == false) {
                        $mails_failed_to_insert++;
                    } else {
                        $total_added_emails++;
                    }
                }
                if ($total_added_emails > 0 && $mails_failed_to_insert == 0) {
                    $_alert_type = 'success';
                } else if ($total_added_emails == 0 && $mails_failed_to_insert > 0) {
                    $_alert_type = 'danger';
                } else if ($total_added_emails > 0 && $mails_failed_to_insert > 0) {
                    $_alert_type = 'warning';
                } else {
                    $_alert_type = 'success';
                }
            }
            // Delete uploaded file
            unlink($temp_url);
            set_alert($_alert_type,
                 _l('mail_list_total_imported',$total_added_emails) . '<br />'.
                 _l('mail_list_total_duplicate',$total_duplicate_emails) .'<br />'.
                 _l('mail_list_total_failed_to_insert',$mails_failed_to_insert) .'<br />'.
                 _l('mail_list_total_invalid',$total_invalid_address));

        } else {
            set_alert('danger', _l('error_uploading_file'));
        }
        redirect(admin_url('mail_lists/view/' . $listid));
    }

    /* Delete mail list from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('mail_lists'));
        }

        $success = $this->mail_lists_model->delete($id);
        if ($success) {
            set_alert('success', _l('deleted',_l('mail_list')));
        }
        redirect(admin_url('mail_lists'));

    }
}
