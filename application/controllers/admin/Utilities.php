<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
// set max execution time 2 hours / mostly used for exporting PDF
ini_set('max_execution_time', 3600);
class Utilities extends Admin_controller
{
    public $pdf_zip;
    function __construct()
    {
        parent::__construct();
        $this->load->model('utilities_model');
    }

    /* All perfex activity log */
    public function activity_log()
    {
        // Only full admin have permission to activity log
        if (!is_admin()) {
            access_denied('activityLog');
        }

        if ($this->input->is_ajax_request()) {
            $aColumns     = array(
                'description',
                'date',
                'tblactivitylog.staffid'
            );
            $sIndexColumn = "id";
            $sTable       = 'tblactivitylog';

            $join             = array(
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblactivitylog.staffid'
            );
            $additionalSelect = array(
                'firstname',
                'lastname'
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, array(), $additionalSelect, 'ORDER BY date ASC');
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];

                    if ($aColumns[$i] == 'date') {
                        $_data = _dt($_data);
                    } else if ($aColumns[$i] == 'tblactivitylog.staffid') {
                        $_data = $aRow['firstname'] . ' ' . $aRow['lastname'];
                    }

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('utility_activity_log');
        $this->load->view('admin/utilities/activity_log', $data);
    }

        /* All perfex activity log */
    public function pipe_log()
    {
        // Only full admin have permission to activity log
        if (!is_admin()) {
            access_denied('Ticket Pipe Log');
        }

        if ($this->input->is_ajax_request()) {
            $aColumns     = array(
                'name',
                'date',
                'email_to',
                'email',
                'subject',
                'message',
                'status',
            );
            $sIndexColumn = "id";
            $sTable       = 'tblticketpipelog';

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), array(), array(), 'ORDER BY date ASC');
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];

                    if ($aColumns[$i] == 'date') {
                        $_data = _dt($_data);
                    } else if($aColumns[$i] == 'message'){
                        $_data = mb_substr($_data,0,800);
                    }

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('ticket_pipe_log');
        $this->load->view('admin/utilities/ticket_pipe_log', $data);
    }

    /* Calendar functions */
    public function calendar()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            $success = $this->utilities_model->add_new_event($this->input->post());
            $message = '';
            if ($success) {
                $message = _l('utility_calendar_event_added_successfuly');
                echo json_encode(array(
                    'success' => true,
                    'message' => $message
                ));
                die();
            }
        }
        $data['google_ids_calendars'] = $this->misc_model->get_google_calendar_ids();
        $data['google_calendar_api']  = get_option('google_calendar_api_key');
        $data['title']                = 'Calendar';
        $this->load->view('admin/utilities/calendar', $data);
    }

    public function get_calendar_data()
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->utilities_model->get_calendar_data());
            die();
        }
    }

    public function delete_event($id)
    {
        if ($this->input->is_ajax_request()) {
            $event = $this->utilities_model->get_event_by_id($id);

            if ($event->userid != get_staff_user_id()) {
                echo json_encode(array(
                    'success' => false
                ));
                die;
            }
            $success = $this->utilities_model->delete_event($id);
            $message = '';

            if ($success) {
                $message = _l('utility_calendar_event_deleted_successfuly');
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
            die();
        }
    }

    // Moves here from version 1.0.5
    public function media()
    {

        if ($this->input->is_ajax_request()) {
            $media                  = new stdClass();
            $media->recordsTotal    = 0;
            $media->recordsFiltered = 0;
            $media->data            = array();
            $folder                 = list_folders(MEDIA_FOLDER);
            foreach ($folder as $folder) {
                $files = list_files(MEDIA_FOLDER . $folder);
                if ($files) {
                    foreach ($files as $file) {
                        $media->recordsTotal++;
                        $media->recordsFiltered++;

                        $_fullpath = MEDIA_FOLDER . $folder . '/' . $file;
                        $row       = array();
                        $link      = site_url('download/media/' . $folder . '/' . $file);
                        array_push($row, '<i class="' . get_mime_class(get_mime_by_extension($file)) . '"></i> <a href="' . $link . '" target="_blank" download>' . $file . '</a>');
                        array_push($row, date('M dS, Y, g:i a', filemtime($_fullpath)));
                        array_push($row, bytesToSize($_fullpath));
                        array_push($row, get_mime_by_extension($file));


                        $options = '';
                        $image   = @getimagesize($_fullpath) ? true : false;
                        if ($image) {
                            $base64 = base64_encode(file_get_contents($_fullpath));
                            $src    = 'data: ' . get_mime_by_extension($file) . ';base64,' . $base64;
                            ob_start();
?>
                            <button type="button" class="btn btn-info btn-icon" data-placement="top" data-html="true" data-toggle="popover" data-content='<img src="<?php echo $src; ?>" class="img img-responsive mbot20">' data-trigger="focus"><i class="fa fa-eye"></i></button>
                            <?php
                            $options .= ob_get_contents();
                            ob_end_clean();
                        }
                        $options .= '<button type="button" data-toggle="modal" data-return-url="'. admin_url('utilities/media').'" data-original-file-name="'.$file.'" data-filetype="'.get_mime_by_extension($file).'" data-path="'.$_fullpath.'" data-target="#send_file" class="btn btn-info btn-icon"><i class="fa fa-envelope"></i></button>';
                        $options .= '<a href="#" data-remove-file onclick="remove_file(\'' . $file . '\',\'' . $folder . '\',this);return false;" class="btn btn-danger btn-icon"><i class="fa fa-remove"></i></a>';

                        array_push($row, $options);
                        array_push($media->data, $row);
                    }
                }
            }

            echo json_encode($media);
            die();
        }

        $data['title'] = _l('media_heading');
        $this->load->view('admin/utilities/media', $data);
    }

    /* Upload new media file*/
    public function upload_media()
    {
        if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {

            $path        = MEDIA_FOLDER;
            $date        = date('Y-m-d');
            $path        = $path . $date . '/';
            // Get the temp file path
            $tmpFilePath = $_FILES['file']['tmp_name'];


            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                $filename = $_FILES["file"]["name"];

                // Setup our new file path
                if (!file_exists($path)) {
                    mkdir($path);
                    fopen($path . 'index.html', 'w');
                }

                $filename    = unique_filename($path, $filename);
                $newFilePath = $path . $filename;

                // Upload the file into the company uploads dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    echo json_encode(array(
                        'success' => true,
                        'filename' => $filename,
                        'folder' => $date,
                        'size' => getimagesize(MEDIA_FOLDER . $date . '/' . $filename)
                    ));
                    die();
                }
            }
        }
    }

    public function bulk_pdf_exporter()
    {
        if (!has_permission('useBulkPdfExporter')) {
            access_denied('useBulkPdfExporter');
        }
        if ($this->input->post()) {

            if (!is_really_writable(TEMP_FOLDER)) {
                show_error('/temp folder is not writable. You need to change the permissions to 777');
            }

            $type = $this->input->post('export_type');

            if ($type == 'invoices') {

                $status = $this->input->post('invoice_export_status');
                $this->db->select('id');
                $this->db->from('tblinvoices');
                if ($status != 'all') {
                    $this->db->where('status', $status);
                }
                $this->db->order_by('date', 'desc');

            } else if ($type == 'estimates') {

                $status = $this->input->post('estimate_export_status');
                $this->db->select('id');
                $this->db->from('tblestimates');

                if ($status != 'all') {
                    $this->db->where('status', $status);
                }

                $this->db->order_by('date', 'desc');

            } else if ($type == 'payments') {

                $this->db->select('tblinvoicepaymentrecords.id as paymentid');
                $this->db->from('tblinvoicepaymentrecords');
                $this->db->join('tblinvoices', 'tblinvoices.id = tblinvoicepaymentrecords.invoiceid', 'left');
                $this->db->join('tblclients', 'tblclients.userid = tblinvoices.clientid', 'left');

                if ($this->input->post('paymentmode')) {
                    $this->db->where('paymentmode', $this->input->post('paymentmode'));
                }
            } else if($type == 'proposals') {
                $this->db->select('id');
                $this->db->from('tblproposals');
                $status =  $this->input->post('proposal_export_status');

                if ($status != 'all') {
                    $this->db->where('status', $status);
                }

                $this->db->order_by('date', 'desc');
            } else {
                // This may not happend but in all cases :)
                die('No Export Type Selected');
            }

            if ($this->input->post('date-to') && $this->input->post('date-from')) {
                $from_date = to_sql_date($this->input->post('date-from'));
                $to_date   = to_sql_date($this->input->post('date-to'));

                if ($from_date == $to_date) {
                    $this->db->where('date', $from_date);
                } else {
                    $this->db->where('date BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
                }
            }

            $data = $this->db->get()->result_array();

            if (count($data) == 0) {
                set_alert('warning', _l('no_data_found_bulk_pdf_export'));
                redirect(admin_url('utilities/bulk_pdf_exporter'));
            }
            $dir = TEMP_FOLDER . $type;

            if (is_dir($dir)) {
                delete_dir($dir);
            }

            mkdir($dir, 0777);

            if ($type == 'invoices') {
                $this->load->model('invoices_model');
                foreach ($data as $invoice) {
                    $invoice_data    = $this->invoices_model->get($invoice['id']);
                    $this->pdf_zip   = invoice_pdf($invoice_data, $this->input->post('tag'));
                    $_temp_file_name = slug_it(format_invoice_number($invoice_data->id));
                    $file_name       = $dir . '/' . strtoupper($_temp_file_name);

                    $this->pdf_zip->Output($file_name . '.pdf', 'F');
                }
            } else if ($type == 'estimates') {
                foreach ($data as $estimate) {
                    $this->load->model('estimates_model');
                    $estimate_data   = $this->estimates_model->get($estimate['id']);
                    $this->pdf_zip   = estimate_pdf($estimate_data);
                    $_temp_file_name = slug_it(format_estimate_number($estimate_data->id));
                    $file_name       = $dir . '/' . strtoupper($_temp_file_name);
                    $this->pdf_zip->Output($file_name . '.pdf', 'F');
                }

            } else if($type == 'payments') {
                $this->load->model('payments_model');
                $this->load->model('invoices_model');
                foreach ($data as $payment) {
                    $payment_data               = $this->payments_model->get($payment['paymentid']);
                    $payment_data->invoice_data = $this->invoices_model->get($payment_data->invoiceid);
                    $this->pdf_zip              = payment_pdf($payment_data);
                    $file_name                  = $dir;
                    $file_name .= '/' . strtoupper(_l('payment'));
                    $file_name .= '-' . strtoupper($payment_data->paymentid) . '.pdf';
                    $this->pdf_zip->Output($file_name, 'F');
                }
            } else {
                $this->load->model('proposals_model');
                foreach($data as $proposal){
                    $proposal = $this->proposals_model->get($proposal['id']);
                    $this->pdf_zip = proposal_pdf($proposal);
                    $_temp_file_name = slug_it($proposal->subject);
                    $file_name       = $dir . '/' . strtoupper($_temp_file_name);
                    $this->pdf_zip->Output($file_name . '.pdf', 'F');
                }
            }

            $this->load->library('zip');
            $this->zip->read_dir($dir, false);
            // Delete the temp directory for the export type
            delete_dir($dir);
            $this->zip->download(slug_it(get_option('companyname')) . '-' . $type . '.zip');
            $this->zip->clear_data();

        }
        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get();

        $data['title'] = _l('bulk_pdf_exporter');
        $this->load->view('admin/utilities/bulk_pdf_exporter', $data);
    }

    public function remove_media_file()
    {
        if ($this->input->post()) {
            if ($this->input->is_ajax_request()) {
                $filename = $_POST['filename'];
                $folder   = $_POST['folder'];
                if (file_exists(MEDIA_FOLDER . $folder . '/' . $filename)) {
                    unlink(MEDIA_FOLDER . $folder . '/' . $filename);
                }
            }
        }
    }

    /* Database back up functions */
    public function backup()
    {
        if (!is_admin()) {
            access_denied('databaseBackup');
        }
        $data['title'] = _l('utility_backup');
        $this->load->view('admin/utilities/backup', $data);
    }

    public function make_backup_db()
    {
        // Only full admin can make database backup
        if (!is_admin()) {
            access_denied('databaseBackup');
        }

        if (!is_really_writable(BACKUPS_FOLDER)) {
            show_error('/backups folder is not writable. You need to change the permissions to 755');
        }

        $this->load->model('cron_model');
        $success = $this->cron_model->make_backup_db(true);

        if ($success) {
            set_alert('success', _l('backup_success'));
            logActivity('Database Backup [' . $backup_name . ']');
        }

        redirect(admin_url('utilities/backup'));

    }

    public function update_auto_backup_options()
    {
        if (!is_admin()) {
            access_denied('databaseBackup');
        }
        if ($this->input->post()) {
            $_post     = $this->input->post();
            $updated_1 = update_option('auto_backup_enabled', $_post['settings']['auto_backup_enabled']);
            $updated_2 = update_option('auto_backup_every', $this->input->post('auto_backup_every'));
            if ($updated_2 || $updated_1) {
                set_alert('success', _l('auto_backup_options_updated'));
            }
        }
        redirect(admin_url('utilities/backup'));
    }
    public function delete_backup($backup)
    {
        if (!is_admin()) {
            access_denied('databaseBackup');
        }
        if (unlink(BACKUPS_FOLDER . $backup)) {
            set_alert('success', _l('backup_delete'));
        }
        redirect(admin_url('utilities/backup'));
    }


    public function main_menu()
    {
        if (!is_admin()) {
            access_denied('Edit Main Menu');
        }

        $data['permissions']   = $this->roles_model->get_permissions();
        $data['permissions'][] = array(
            'shortname' => 'is_admin',
            'name' => 'Admin'
        );
        $data['title']         = _l('main_menu');
        $this->load->view('admin/utilities/main_menu', $data);
    }

    public function update_aside_menu()
    {

        if (!is_admin()) {
            access_denied('Edit Main Menu');
        }

        $data_inactive = $this->input->post('inactive');
        if ($data_inactive == null) {
            $data_inactive = array();
        }

        $data_active = $this->input->post('active');
        if ($data_active == null) {
            $data_active = array();
        }

        update_option('aside_menu_active', json_encode(array(
            'aside_menu_active' => $data_active
        )));
        update_option('aside_menu_inactive', json_encode(array(
            'aside_menu_inactive' => $data_inactive
        )));
    }

    public function setup_menu()
    {
        if (!is_admin()) {
            access_denied('Edit Setup Menu');
        }
        $data['permissions']   = $this->roles_model->get_permissions();
        $data['permissions'][] = array(
            'shortname' => 'is_admin',
            'name' => 'Admin'
        );
        $data['title']         = _l('setup_menu');
        $this->load->view('admin/utilities/setup_menu', $data);
    }

    public function update_setup_menu()
    {
        if (!is_admin()) {
            access_denied('Edit Setup Menu');
        }

        $data_inactive = $this->input->post('inactive');
        if ($data_inactive == null) {
            $data_inactive = array();
        }

        $data_active = $this->input->post('active');
        if ($data_active == null) {
            $data_active = array();
        }

        update_option('setup_menu_active', json_encode(array(
            'setup_menu_active' => $data_active
        )));
        update_option('setup_menu_inactive', json_encode(array(
            'setup_menu_inactive' => $data_inactive
        )));
    }
}
