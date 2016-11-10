<?php
header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') OR exit('No direct script access allowed');

class Leads extends Admin_controller
{
    private $not_importable_leads_fields = array('id', 'source', 'assigned', 'status', 'dateadded', 'last_status_change', 'addedfrom', 'leadorder', 'date_converted', 'lost', 'junk', 'is_imported_from_email_integration', 'email_integration_uid', 'is_public');

    function __construct()
    {
        parent::__construct();
        $this->load->model('leads_model');
    }

    /* List all leads canban and table */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->get('canban')) {
                $data['statuses'] = $this->leads_model->get_status();
                echo $this->load->view('admin/leads/kan-ban', $data, true);
                die();
            } else if ($this->input->get('table_leads')) {

                $custom_fields = get_custom_fields('leads',array('show_on_table'=>1));

                $aColumns     = array(
                    'tblleads.name',
                    'tblleads.email',
                    'tblleads.phonenumber',
                    'assigned',
                    'tblleadsstatus.name',
                    'lastcontact'
                );
                $sIndexColumn = "id";
                $sTable       = 'tblleads';

                $join = array(
                    'LEFT JOIN tblstaff ON tblstaff.staffid = tblleads.assigned',
                    'LEFT JOIN tblleadsstatus ON tblleadsstatus.id = tblleads.status'
                );

                $i = 0;
                foreach($custom_fields as $field){
                    array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
                    array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblleads.id = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
                    $i++;
                }

                $where = array();

                if ($this->input->post('custom_view')) {
                    $custom_view = $this->input->post('custom_view');
                    // is status
                    if (is_numeric($custom_view)) {
                        array_push($where, 'AND status =' . $custom_view);
                    } else if ($custom_view == 'lost') {
                        array_push($where, 'AND lost = 1');
                    } else if ($custom_view == 'junk') {
                        array_push($where, 'AND junk = 1');
                    } else if ($custom_view == 'not_assigned') {
                        array_push($where, 'AND assigned = 0');
                    }
                }

                if ($this->input->post('assigned')) {
                    $by_assigned = $this->input->post('assigned');
                    array_push($where, 'AND assigned =' . $by_assigned);
                }

                if (!is_admin()) {
                    array_push($where, 'AND (assigned =' . get_staff_user_id() . ' OR addedfrom = ' . get_staff_user_id() . ' OR assigned = 0 OR is_public = 1)');
                }
                $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                    'firstname',
                    'lastname',
                    'tblleads.id',
                    'junk',
                    'lost',
                    'isdefault',
                    'color'
                ));
                $output  = $result['output'];
                $rResult = $result['rResult'];

                foreach ($rResult as $aRow) {
                    $row = array();
                    for ($i = 0; $i < count($aColumns); $i++) {

                    if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
                        $_data = $aRow[ strafter($aColumns[$i],'as ')];
                    } else {
                        $_data = $aRow[ $aColumns[$i] ];
                    }

                        if ($aColumns[$i] == 'tblleads.name') {
                            $_data = '<a href="' . admin_url('leads/lead/' . $aRow['id']) . '">' . $_data . '</a>';
                        } else if ($aColumns[$i] == 'lastcontact') {
                            if ($_data == '0000-00-00 00:00:00' || !is_date($_data)) {
                                $_data = '';
                            } else {
                                $_data = '<span data-toggle="tooltip" title="' . _dt($_data) . '">' . time_ago_specific($_data) . '</span>';
                            }
                        } else if ($aColumns[$i] == 'assigned') {
                            if ($aRow['assigned'] != 0) {
                                $_data = staff_profile_image($aRow['assigned'], array(
                                    'staff-profile-image-small'
                                )) . ' ' . $aRow['firstname'] . ' ' . $aRow['lastname'];
                            } else {
                                $_data = '';
                            }
                        } else if ($aColumns[$i] == 'tblleadsstatus.name') {
                            if ($aRow['tblleadsstatus.name'] == null) {
                                if ($aRow['lost'] == 1) {
                                    $_data = '<span class="label label-danger pull-left">' . _l('lead_lost') . '</span>';
                                } else if ($aRow['junk'] == 1) {
                                    $_data = '<span class="label label-warning pull-left">' . _l('lead_junk') . '</span>';
                                }
                            } else {

                                $_label_class = '';
                                if (empty($aRow['color'])) {
                                    $_label_class = 'default';
                                }

                                $_data = '<span class="label label-' . $_label_class . ' pull-left" style="color:' . $aRow['color'] . ';border:1px solid ' . $aRow['color'] . '">' . $_data . '</span>';
                            }
                        } else if ($aColumns[$i] == 'tblleads.phonenumber') {
                            $_data = '<a href="tel:' . $_data . '">' . $_data . '</a>';
                        } else if ($aColumns[$i] == 'tblleads.email') {
                            $_data = '<a href="mailto:' . $_data . '">' . $_data . '</a>';
                        }

                        $row[] = $_data;
                    }
                    $options = icon_btn('admin/leads/lead/' . $aRow['id'], 'pencil-square-o');

                    if (is_lead_creator($aRow['id']) || is_admin()) {
                        $options .= icon_btn('admin/leads/delete/' . $aRow['id'], 'remove', 'btn-danger');
                    }

                    $row[] = $options;
                    if ($aRow['assigned'] == get_staff_user_id()) {
                        $row['DT_RowClass'] = 'alert-info';
                    }
                    $output['aaData'][] = $row;
                }

                echo json_encode($output);
                die();

            }
        }

        $this->load->model('staff_model');
        $data['staff']     = $this->staff_model->get('', 1);
        $data['bodyclass'] = 'kan-ban-body';
        $data['statuses']  = $this->leads_model->get_status();
        $data['title']     = _l('leads');
        $this->load->view('admin/leads/manage_leads', $data);
    }
    /* Add or update lead */
    public function lead($id = '')
    {

        $leadData = $this->input->post();

        if (empty($leadData['affiliate'])){

            unset($leadData['affiliate']);
        }


        if (is_numeric($id)) {
            $lead = $this->leads_model->get($id);
            if (!is_admin()) {
                if (($lead->assigned != get_staff_user_id() && $lead->addedfrom != get_staff_user_id() && $lead->is_public != 1)) {
                    access_denied('Access not connected lead');
                }
            }
        }

        if ($this->input->post()) {

            if ($id == '') {

                $id = $this->leads_model->add($leadData);
                if ($id) {
                    // coming from leads kan ban model
                    if (!$this->input->is_ajax_request()) {
                        set_alert('success', _l('added_successfuly', _l('lead')));
                        redirect(admin_url('leads/lead/' . $id));
                    }
                }
            } else {
                $success = $this->leads_model->update($leadData, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('lead')));
                }
                redirect(admin_url('leads/lead/' . $id));
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('lead_lowercase'));
        } else {

            if (!$lead) {
                blank_page(_l('lead_not_found'));
            }
            $data['lead']          = $lead;
            $data['mail_activity'] = $this->leads_model->get_mail_activity($id);

            $data['notes']        = $this->leads_model->get_lead_notes($id);
            $title                = _l('edit', _l('lead_lowercase')) . ' ' . $lead->name;
            $data['activity_log'] = $this->leads_model->get_lead_activity_log($id);
        }

        $data['affiliates'] = $this->db->get('tblaffiliates')->result_array();

        $data['statuses'] = $this->leads_model->get_status();
        $data['sources']  = $this->leads_model->get_source();
        $this->load->model('staff_model');
        $data['members'] = $this->staff_model->get();
        $data['title']   = $title;
        $this->load->view('admin/leads/lead', $data);
    }
    /* Delete lead from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('sources'));
        }

        if (!is_lead_creator($id) && !is_admin()) {
            die;
        }

        $response = $this->leads_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('lead_lowercase')));
        } else if ($response === true) {
            set_alert('success', _l('deleted', _l('lead')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('lead_lowercase')));
        }

        redirect(admin_url('leads'));
    }

    public function mark_as_lost($id)
    {

        $success = $this->leads_model->mark_as_lost($id);
        if ($success) {
            set_alert('success', _l('lead_marked_as_lost'));
        }

        redirect(admin_url('leads/lead/' . $id));

    }

    public function unmark_as_lost($id)
    {

        $success = $this->leads_model->unmark_as_lost($id);
        if ($success) {
            set_alert('success', _l('lead_unmarked_as_lost'));
        }

        redirect(admin_url('leads/lead/' . $id));

    }

    public function mark_as_junk($id)
    {

        $success = $this->leads_model->mark_as_junk($id);
        if ($success) {
            set_alert('success', _l('lead_marked_as_junk'));
        }

        redirect(admin_url('leads/lead/' . $id));

    }

    public function unmark_as_junk($id)
    {

        $success = $this->leads_model->unmark_as_junk($id);
        if ($success) {
            set_alert('success', _l('lead_unmarked_as_junk'));
        }

        redirect(admin_url('leads/lead/' . $id));

    }

    public function add_lead_attachment()
    {
        $leadid = $this->input->post('leadid');
        echo json_encode(handle_lead_attachments($leadid));
    }
    public function get_lead_attachments($id)
    {
        $data['attachments'] = $this->leads_model->get_lead_attachments($id);
        $this->load->view('admin/leads/leads_attachments_template', $data);
    }
    public function delete_attachment($id)
    {
        echo json_encode(array(
            'success' => $this->leads_model->delete_lead_attachment($id)
        ));
    }
    public function delete_lead_note($id){
           echo json_encode(array(
            'success' => $this->leads_model->delete_lead_note($id)
        ));
    }
    // Sources
    /* Manage leads sources */
    public function sources()
    {
        if (!is_admin()) {
            access_denied('Leads Sources');
        }

        $data['sources'] = $this->leads_model->get_source();
        $data['title']   = 'Leads sources';
        $this->load->view('admin/leads/manage_sources', $data);
    }
    /* Add or update leads sources */
    public function source()
    {

        if (!is_admin()) {
            access_denied('Leads Sources');
        }

        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->leads_model->add_source($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('lead_source')));
                }
            } else {
                $data = $this->input->post();
                $id = $data['id'];
                unset($data['id']);
                $success = $this->leads_model->update_source($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('lead_source')));
                }
            }
        }
    }
    /* Delete leads source */
    public function delete_source($id)
    {

        if (!is_admin()) {
            access_denied('Delete Lead Source');
        }

        if (!$id) {
            redirect(admin_url('leads/sources'));
        }

        $response = $this->leads_model->delete_source($id);

        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('lead_source_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('lead_source')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('lead_source_lowercase')));
        }

        redirect(admin_url('leads/sources'));
    }
    // Statuses
    /* View leads statuses */
    public function statuses()
    {

        if (!is_admin()) {
            access_denied('Leads Statuses');
        }
        $data['statuses'] = $this->leads_model->get_status();
        $data['title']    = 'Leads statuses';
        $this->load->view('admin/leads/manage_statuses', $data);
    }
    /* Add or update leads status */
    public function status()
    {
        if (!is_admin()) {
            access_denied('Leads Statuses');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->leads_model->add_status($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('lead_status')));
                }
            } else {
                $data = $this->input->post();
                $id = $data['id'];
                unset($data['id']);
                $success = $this->leads_model->update_status($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('lead_status')));
                }
            }
        }
    }

    /* Delete leads status from databae */
    public function delete_status($id)
    {

        if (!is_admin()) {
            access_denied('Leads Statuses');
        }

        if (!$id) {
            redirect(admin_url('leads/statuses'));
        }

        $response = $this->leads_model->delete_status($id);

        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('lead_status_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('lead_status')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('lead_status_lowercase')));
        }

        redirect(admin_url('leads/statuses'));
    }
    /* Add new lead note */
    public function add_note()
    {
        if ($this->input->post()) {
            $success = $this->leads_model->add_note($this->input->post());
            if ($success) {
                set_alert('success', _l('lead_noted_added_successfuly'));
            }

            redirect(admin_url('leads/lead/' . $this->input->post('leadid')));

        }
    }

    /**
     * Convert lead to client
     * @since  version 1.0.1
     * @return mixed
     */
    public function convert_to_client()
    {
        if ($this->input->post()) {


            $this->load->model('clients_model');
            $data                = $this->input->post();
            $original_lead_email = $data['original_lead_email'];
            unset($data['original_lead_email']);

            if (isset($data['merge_db_fields'])) {
                $merge_db_fields = $data['merge_db_fields'];
                unset($data['merge_db_fields']);
            }

            if (isset($data['include_leads_custom_fields'])) {
                $include_leads_custom_fields = $data['include_leads_custom_fields'];
                unset($data['include_leads_custom_fields']);
            }

            $affiliateid = 0;

            if (isset($data['affiliate'])) {
                $data['affiliateid'] = $data['affiliate'];
                unset($data['affiliate']);
            }

            $id = $this->clients_model->add($data);
            if ($id) {

                // Add all permissions to this lead-client
                $permissions = $this->perfex_base->get_customer_permissions();
                foreach ($permissions as $permission) {
                    $this->db->insert('tblcustomerpermissions', array(
                        'permission_id' => $permission['id'],
                        'userid' => $id,
                        ));
                }

                $this->leads_model->log_lead_activity($data['leadid'], get_staff_full_name() . ' Converted this lead to client. ClientID: ' . $id);

                $default_status = $this->leads_model->get('',true);
                $this->db->where('id', $data['leadid']);
                $this->db->update('tblleads', array(
                    'date_converted' => date('Y-m-d H:i:s'),
                    'status'=>$default_status->id
                ));
                // Check if lead email is different then client email
                $client = $this->clients_model->get($id);

                if ($client->email != $original_lead_email) {
                    if ($original_lead_email != '') {
                        $this->leads_model->log_lead_activity($data['leadid'], '<span data-toggle="tooltip" title="Lead email was not the same with client email when this lead is converted to client.">Lead email automatically changed.First lead email was: ' . $original_lead_email . '. Added to clients as ' . $client->email . '</span>');
                    }
                }

                if (isset($include_leads_custom_fields)) {
                    foreach ($include_leads_custom_fields as $fieldid => $value) {
                        // checked dont merge
                        if ($value == 3) {
                            continue;
                        }

                        // get the value of this leads custom fiel
                        $this->db->where('relid', $data['leadid']);
                        $this->db->where('fieldto', 'leads');
                        $this->db->where('fieldid', $fieldid);
                        $lead_custom_field_value = $this->db->get('tblcustomfieldsvalues')->row()->value;

                        if ($value == 1) {

                            $this->db->where('id', $fieldid);
                            $field = $this->db->get('tblcustomfields')->row();
                            // check if this field exists for custom fields
                            $this->db->where('fieldto', 'customers');
                            $this->db->where('name', $field->name);
                            $exists = $this->db->get('tblcustomfields')->row();

                            $copy_custom_field_id = NULL;
                            if ($exists) {
                                $copy_custom_field_id = $exists->id;
                            } else {
                                // there is no name with the same custom field for leads at the custom side create the custom field now
                                $this->db->insert('tblcustomfields', array(
                                    'fieldto' => 'customers',
                                    'name' => $field->name,
                                    'required' => $field->required,
                                    'type' => $field->type,
                                    'options' => $field->options,
                                    'field_order' => $field->field_order,
                                    'active' => $field->active,
                                    'show_on_pdf' => $field->show_on_pdf
                                ));

                                $new_customer_field_id = $this->db->insert_id();
                                if ($new_customer_field_id) {
                                    $copy_custom_field_id = $new_customer_field_id;
                                }
                            }
                            if ($copy_custom_field_id != NULL) {
                                $this->db->insert('tblcustomfieldsvalues', array(
                                    'relid' => $id,
                                    'fieldid' => $copy_custom_field_id,
                                    'fieldto' => 'customers',
                                    'value' => $lead_custom_field_value
                                ));
                            }
                        } else {
                            if (isset($merge_db_fields)) {
                                $db_field = $merge_db_fields[$fieldid];

                                // in case user dont select anything from the db fields
                                if ($db_field == '') {
                                    continue;
                                }
                                if($db_field == 'country'){
                                   $this->db->where('iso2',$lead_custom_field_value);
                                   $this->db->or_where('short_name',$lead_custom_field_value);
                                   $this->db->or_like('long_name',$lead_custom_field_value);
                                   $country = $this->db->get('tblcountries')->row();
                                   if($country){
                                     $lead_custom_field_value = $country->country_id;
                                 } else {
                                     $lead_custom_field_value = 0;
                                 }
                             } else {
                                 $lead_custom_field_value = 0;
                             }

                                $this->db->where('userid', $id);
                                $this->db->update('tblclients', array(
                                    $db_field => $lead_custom_field_value
                                ));
                            }
                        }
                    }
                }

                // set the lead to status client in case is not status client
                $this->db->where('isdefault',1);
                $status_client_id = $this->db->get('tblleadsstatus')->row()->id;
                $this->db->where('id',$data['leadid']);
                $this->db->update('tblleads',array('status'=>$status_client_id));

                set_alert('success', _l('lead_to_client_base_converted_success'));
                logActivity('Created Lead Client Profile [LeadID: ' . $data['leadid'] . ', ClientID: ' . $id . ']');
                redirect(admin_url('clients/client/' . $id));
            }
        }
    }

    // Ajax
    /* Used in canban when dragging */
    public function update_can_ban_lead_status()
    {
        if ($this->input->post()) {
            if ($this->input->is_ajax_request()) {
                $success = $this->leads_model->update_can_ban_lead_status($this->input->post());
                $message = '';
                if (!is_array($success)) {
                    if ($success) {
                        $message = _l('lead_status_updated');
                    }
                } else if ($success['is_converted_to_client']) {
                    $success = false;
                    $message = _l('lead_is_client_cant_change_status_canban');
                }

                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
                die();
            }
        }
    }

    public function update_status_order()
    {
        if ($this->input->post()) {
            $this->leads_model->update_status_order();
        }
    }

    public function test_email_integration()
    {

        if (!is_admin()) {
            access_denied('Leads Test Email Integration');
        }

        require_once(APPPATH . 'third_party/php-imap/Imap.php');
        $mail       = $this->leads_model->get_email_integration();
        $mailbox    = $mail->imap_server;
        $username   = $mail->email;
        $password   = $mail->password;
        $encryption = $mail->encryption;
        // open connection
        $imap       = new Imap($mailbox, $username, $password, $encryption);

        if ($imap->isConnected() === false) {
            set_alert('danger', _l('lead_email_connection_not_ok') . '<br /><b>' . $imap->getError() . '</b>');
        } else {
            set_alert('success', _l('lead_email_connection_ok'));
        }

        redirect(admin_url('leads/email_integration'));
    }

    public function email_integration()
    {

        if (!is_admin()) {
            access_denied('Leads Email Intregration');
        }
        if ($this->input->post()) {
            $success = $this->leads_model->update_email_integration($this->input->post());
            if ($success) {
                set_alert('success', _l('leads_email_integration_updated'));
            }

            redirect(admin_url('leads/email_integration'));
        }

        $data['roles']    = $this->roles_model->get();
        $data['sources']  = $this->leads_model->get_source();
        $data['statuses'] = $this->leads_model->get_status();
        $this->load->model('staff_model');
        $data['members'] = $this->staff_model->get('', 1);
        $data['title']   = _l('leads_email_integration');

        $data['mail'] = $this->leads_model->get_email_integration();
        $this->load->view('admin/leads/email_integration', $data);
    }

    public function change_status_color()
    {
        if ($this->input->post()) {
            $this->leads_model->change_status_color($this->input->post());
        }
    }

    public function get_lead_kan_ban_content($id)
    {
        $lead             = $this->leads_model->get($id);
        $data['lead']     = $lead;
        $data['statuses'] = $this->leads_model->get_status();
        $data['affiliates'] = $this->db->get('tblaffiliates')->result_array();
        $data['sources']  = $this->leads_model->get_source();
        $this->load->model('staff_model');
        $data['members']       = $this->staff_model->get();
        $data['mail_activity'] = $this->leads_model->get_mail_activity($id);
        $data['notes']         = $this->leads_model->get_lead_notes($id);
        $data['activity_log']  = $this->leads_model->get_lead_activity_log($id);
        $this->load->view('admin/leads/kan-ban-lead-content', $data);
    }

    public function import()
    {
        $simulate_data  = array();
        $total_imported = 0;
        if ($this->input->post()) {
            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    // Setup our new file path
                    $newFilePath = TEMP_FOLDER . $_FILES['file_csv']['name'];
                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 777);
                    }

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {

                        $import_result = true;
                        $fd            = fopen($newFilePath, 'r');
                        $rows          = array();
                        while ($row = fgetcsv($fd)) {
                            $rows[] = $row;
                        }
                        fclose($fd);
                        if (count($rows) <= 1) {
                            set_alert('warning', 'Not enought rows for importing');
                            redirect(admin_url('leads/import'));
                        }
                        unset($rows[0]);
                        if ($this->input->post('simulate')) {
                            if (count($rows) > 500) {
                                set_alert('warning', 'Recommended splitting the CSV file into smaller files. Our recomendation is 500 row, your CSV file has ' . count($rows));
                            }
                        }
                        $db_temp_fields = $this->db->list_fields('tblleads');
                        $db_fields      = array();
                        foreach ($db_temp_fields as $field) {
                            if (in_array($field, $this->not_importable_leads_fields)) {
                                continue;
                            }
                            $db_fields[] = $field;
                        }

                        $custom_fields = get_custom_fields('leads');
                        $_row_simulate = 0;
                        foreach ($rows as $row) {
                            // do for db fields
                            $insert = array();
                            for ($i = 0; $i < count($db_fields); $i++) {
                                if (!isset($row[$i])) {
                                    continue;
                                }
                                // Avoid errors on nema field. is required in database
                                if ($db_fields[$i] == 'name' && $row[$i] == '') {
                                    $row[$i] = '/';
                                }
                                $insert[$db_fields[$i]] = $row[$i];
                            }
                            if (count($insert) > 0) {
                                $total_imported++;
                                $insert['dateadded'] = date('Y-m-d H:i:s');
                                $insert['addedfrom'] = get_staff_user_id();

                                $insert['status'] = $this->input->post('status');
                                $insert['source'] = $this->input->post('source');

                                if (!$this->input->post('simulate')) {
                                    $this->db->insert('tblleads', $insert);
                                    $leadid = $this->db->insert_id();
                                } else {
                                    $simulate_data[$_row_simulate] = $insert;
                                    $leadid                        = true;
                                }
                                if ($leadid) {
                                    $insert = array();
                                    foreach ($custom_fields as $field) {
                                        if (!$this->input->post('simulate')) {
                                            if ($row[$i] == '') {
                                                continue;
                                            }
                                            $this->db->insert('tblcustomfieldsvalues', array(
                                                'relid' => $leadid,
                                                'fieldid' => $field['id'],
                                                'value' => $row[$i],
                                                'fieldto' => 'leads'
                                            ));
                                        } else {
                                            $simulate_data[$_row_simulate][$field['name']] = $row[$i];
                                        }
                                        $i++;
                                    }
                                }
                            }
                            $_row_simulate++;
                            if ($this->input->post('simulate') && $_row_simulate >= 100) {
                                break;
                            }
                        }
                        unlink($newFilePath);
                    }
                } else {
                    set_alert('warning', _l('import_upload_failed'));
                }
            }
        }
        $this->load->model('leads_model');
        $data['statuses'] = $this->leads_model->get_status();
        $data['sources']  = $this->leads_model->get_source();
        if (count($simulate_data) > 0) {
            $data['simulate'] = $simulate_data;
        }
        if (isset($import_result)) {
            set_alert('success', _l('import_total_imported', $total_imported));
        }
        $data['not_importable'] = $this->not_importable_leads_fields;
        $data['title']          = 'Import';
        $this->load->view('admin/leads/import', $data);
    }

}
