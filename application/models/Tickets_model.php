<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tickets_model extends CRM_Model
{

    private $supportticketpipe = true;
    private $pipenonregisteredreplyonly = false;
    function __construct()
    {
        parent::__construct();
    }

    function pipe_decode_string($string)
    {

        if ($pos = strpos($string, "=?") === false) {
            return $string;
        }
        $newresult = NULL;

        while (!($pos === false)) {
            $newresult .= substr($string, 0, $pos);
            $string   = substr($string, $pos + 2, strlen($string));
            $intpos   = strpos($string, "?");
            $charset  = substr($string, 0, $intpos);
            $enctype  = strtolower(substr($string, $intpos + 1, 1));
            $string   = substr($string, $intpos + 3, strlen($string));
            $endpos   = strpos($string, "?=");
            $mystring = substr($string, 0, $endpos);
            $string   = substr($string, $endpos + 2, strlen($string));

            if ($enctype == "q") {
                $mystring = quoted_printable_decode(str_replace("_", " ", $mystring));
            } else {
                if ($enctype == "b") {
                    $mystring = base64_decode($mystring);
                }
            }
            $newresult .= $mystring;
            $pos = strpos($string, "=?");
        }

        $result = $newresult . $string;
        return $result;
    }


    public function insert_piped_ticket($data)
    {

        if (get_option('email_piping_enabled') == 0) {
            return false;
        }

        $attachments = $data['attachments'];
        $subject     = $data['subject'];
        $message     = $data['body'];
        $name        = $data['fromname'];
        $email = $data['email'];
        $to          = $data['to'];

        $decodestring = $subject . "##||-MESSAGESPLIT-||##" . $message;
        $decodestring = $this->pipe_decode_string($decodestring);
        $decodestring = explode("##||-MESSAGESPLIT-||##", $decodestring);
        $subject      = $decodestring[0];
        $message      = $decodestring[1];
        $raw_message  = $message;


        $mailstatus   = false;
        $spam_filters = $this->db->get('tblticketsspamcontrol')->result_array();

        foreach ($spam_filters as $filter) {
            $type  = $filter['type'];
            $value = $filter['value'];

            if ($type == "sender") {
                if (strtolower($value) == strtolower($email)) {
                    $mailstatus = "Blocked Sender";
                }
            }

            if ($type == "subject") {
                if (strpos("x" . strtolower($subject), strtolower($value))) {
                    $mailstatus = "Blocked Subject";
                }
            }

            if ($type == "phrase") {
                if (strpos("x" . strtolower($message), strtolower($value))) {
                    $mailstatus = "Blocked Phrase";
                }
            }
        }

        // No spam found
        if (!$mailstatus) {
            $pos = strpos($subject, "[Ticket ID: ");

            if ($pos === false) {
            } else {
                $tid = substr($subject, $pos + 12);
                $tid = substr($tid, 0, strpos($tid, "]"));
                $this->db->where('ticketid', $tid);
                $data = $this->db->get('tbltickets')->row();
                $tid  = $data->ticketid;
            }

            $to       = trim($to);
            $toemails = explode(",", $to);
            $deptid   = false;
            $userid   = false;

            foreach ($toemails as $toemail) {
                if (!$deptid) {
                    $this->db->where('email', $toemail);
                    $data = $this->db->get('tbldepartments')->row();

                    if ($data) {
                        $deptid = $data->departmentid;
                        $to     = $data->email;
                    }
                }
            }

            if (!$deptid) {
                $mailstatus = "Department Not Found";
            } else {
                if ($to == $email) {
                    $mailstatus = "Blocked Potential Email Loop";
                } else {
                    $message = trim($message);
                    $this->db->where('active', 1);
                    $this->db->where('email', $email);
                    $result = $this->db->get('tblstaff')->row();

                    if ($result) {
                        if ($tid) {
                            $data            = array();
                            $data['message'] = $message;
                            $data['status']  = 1;

                            if ($userid == false) {
                                $data['name']  = $name;
                                $data['email'] = $email;
                            }

                            $reply_id = $this->add_reply($data, $tid, $result->staffid);
                            if ($reply_id) {
                                $mailstatus = "Ticket Reply Imported Successfully";
                            }
                        } else {
                            $mailstatus = "Ticket ID Not Found";
                        }
                    } else {
                        $this->db->where('email', $email);
                        $result = $this->db->get('tblclients')->row();
                        if ($result) {
                            $userid = $result->userid;
                        }
                        if ($userid == false && get_option('email_piping_only_registered') == '1') {
                            $mailstatus = "Unregistered Email Address";
                        } else {

                            $filterdate = date("YmdHis", mktime(date("H"), date("i") - 15, date("s"), date("m"), date("d"), date("Y")));
                            $query      = 'SELECT count(*) as total FROM tbltickets WHERE date > "' . $filterdate . '" AND (email="' . $this->db->escape($email) . '"';
                            if ($userid) {
                                $query .= " OR userid=" . (int) $userid;
                            }
                            $query .= ")";
                            $result = $this->db->query($query)->row();

                            if (10 < $result->total) {
                                $mailstatus = "Exceeded Limit of 10 Tickets within 15 Minutes";
                            } else {
                                if (isset($tid)) {
                                    $data            = array();
                                    $data['message'] = $message;
                                    $data['status']  = 1;

                                    if ($userid == false) {
                                        $data['name']  = $name;
                                        $data['email'] = $email;
                                    } else {
                                        $data['userid'] = $userid;
                                    }

                                    $reply_id = $this->add_reply($data, $tid);
                                    if ($reply_id) {
                                        $mailstatus = "Ticket Reply Imported Successfully";
                                    }
                                } else {
                                    if (get_option('email_piping_only_registered') == 1 && !$userid) {
                                        $mailstatus = "Blocked Ticket Opening from Unregistered User";
                                    } else {
                                        if (get_option('email_piping_only_replies') == '1') {
                                            $mailstatus = "Only Replies Allowed by Email";
                                        } else {
                                            $data               = array();
                                            $data['department'] = $deptid;
                                            $data['subject']    = $subject;
                                            $data['message']    = $message;
                                            $data['priority']   = get_option('email_piping_default_priority');

                                            if ($userid == false) {
                                                $data['name']  = $name;
                                                $data['email'] = $email;
                                            } else {
                                                $data['userid'] = $userid;
                                            }
                                            $tid        = $this->add($data);
                                            $mailstatus = "Ticket Imported Successfully";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($mailstatus == "") {
            $mailstatus = "Ticket Import Failed";
        } else {
            if (isset($tid)) {
                if (!empty($attachments)) {
                    $ticket_attachments = array();
                    $allowed_extensions = explode('|', get_option('ticket_attachments_file_extensions'));
                    $path               = TICKET_ATTACHMENTS_FOLDER . $tid . '/';
                    foreach ($attachments as $attachment) {
                        $filename      = $attachment["filename"];
                        $filenameparts = explode(".", $filename);
                        $extension     = end($filenameparts);
                        if (in_array($extension, $allowed_extensions)) {
                            $filename = implode(array_slice($filenameparts, 0, 0 - 1));
                            $filename = trim(preg_replace("/[^a-zA-Z0-9-_ ]/", "", $filename));
                            if (!$filename) {
                                $filename = "attachment";
                            }
                            if (!file_exists($path)) {
                                mkdir($path);
                                fopen($path . 'index.html', 'w');
                            }

                            $filename = unique_filename($path, $filename . "." . $extension);
                            $fp       = fopen($path . $filename, "w");
                            fwrite($fp, $attachment["data"]);
                            fclose($fp);

                            array_push($ticket_attachments, array(
                                'file_name' => $filename,
                                'filetype' => get_mime_by_extension($filename)
                            ));
                        }
                    }
                    $this->insert_ticket_attachments_to_database($ticket_attachments, $tid, $reply_id);
                }
            }
        }

        $this->db->insert('tblticketpipelog', array(
            'date' => date('Y-m-d H:i:s'),
            'email_to' => $to,
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'status' => $mailstatus
        ));
    }

    public function get($id = '')
    {

        if (is_numeric($id)) {
            $this->db->where('ticketid', $id);
            return $this->db->get('tbltickets')->row();
        }

        $this->db->order_by('lastreply', 'asc');
        return $this->db->get('tbltickets')->result_array();
    }
    /**
     * Get ticket by id and all data
     * @param  mixed  $id     ticket id
     * @param  mixed $userid Optional - Tickets from USER ID
     * @return object
     */
    function get_ticket_by_id($id, $userid = '')
    {

        $this->db->select('tbltickets.userid,tbltickets.name as from_name,tbltickets.email as ticket_email, tbldepartments.name as department_name, tblpriorities.name as priority_name, statuscolor, tblticketassignments.staffid as assigned, tbltickets.admin, tblservices.name as service_name, service, tblticketstatus.name as status_name,tbltickets.ticketid,subject,tblclients.firstname as user_firstname,.tblclients.lastname as user_lastname,tblstaff.firstname as staff_firstname, tblstaff.lastname as staff_lastname,lastreply,message,status,subject,department,priority,tblclients.email,adminread,clientread,date,tbltickets.ip');
        $this->db->from('tbltickets');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltickets.department', 'left');
        $this->db->join('tblticketstatus', 'tblticketstatus.ticketstatusid = tbltickets.status', 'left');
        $this->db->join('tblservices', 'tblservices.serviceid = tbltickets.service', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbltickets.userid', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbltickets.admin', 'left');
        $this->db->join('tblticketassignments', 'tblticketassignments.ticketid = tbltickets.ticketid', 'left');
        $this->db->join('tblpriorities', 'tblpriorities.priorityid = tbltickets.priority', 'left');
        $this->db->where('tbltickets.ticketid', $id);

        if (is_numeric($userid)) {
            $this->db->where('tbltickets.userid', $userid);
        }
        $ticket = $this->db->get()->row();

        if ($ticket) {
            if ($ticket->admin == null || $ticket->admin == 0) {
                if ($ticket->userid != 0) {
                    $ticket->submitter = $ticket->user_firstname . ' ' . $ticket->user_lastname;
                } else {
                    $ticket->submitter = $ticket->from_name;
                }
            } else {
                $ticket->submitter = $ticket->staff_firstname . ' ' . $ticket->staff_lastname;
            }

        }

        $ticket->attachments = $this->get_ticket_attachments($id);

        return $ticket;
    }

    /**
     * Insert ticket attachments to database
     * @param  array  $attachments array of attachment
     * @param  mixed  $ticketid
     * @param  boolean $replyid If is from reply
     */
    public function insert_ticket_attachments_to_database($attachments, $ticketid, $replyid = false)
    {
        foreach ($attachments as $attachment) {
            $attachment['ticketid']  = $ticketid;
            $attachment['dateadded'] = date('Y-m-d H:i:s');

            if ($replyid !== false && is_int($replyid)) {
                $attachment['replyid'] = $replyid;
            }

            $this->db->insert('tblticketattachments', $attachment);
        }
    }


    /**
     * Get ticket attachments from database
     * @param  mixed $id      ticket id
     * @param  mixed $replyid Optional - reply id if is from from reply
     * @return array
     */
    public function get_ticket_attachments($id, $replyid = '')
    {

        $this->db->where('ticketid', $id);
        if (is_numeric($replyid)) {
            $this->db->where('replyid', $replyid);
        } else {
            $this->db->where('replyid', null);
        }

        $this->db->where('ticketid', $id);
        return $this->db->get('tblticketattachments')->result_array();
    }

    /**
     * Add new reply to ticket
     * @param mixed $data  reply $_POST data
     * @param mixed $id    ticket id
     * @param boolean $admin staff id if is staff making reply
     */
    public function add_reply($data, $id, $admin = null)
    {

        if (isset($data['assign_to_current_user'])) {

            $this->db->where('ticketid', $id);
            $current_assigned = $this->db->get('tblticketassignments')->row();

            if ($current_assigned) {
                $this->db->where('ticketid', $id);
                $this->db->update('tblticketassignments', array(
                    'staffid' => get_staff_user_id()
                ));
            } else {
                $this->db->insert('tblticketassignments', array(
                    'ticketid' => $data['ticketid'],
                    'staffid' => get_staff_user_id()
                ));
            }

            unset($data['assign_to_current_user']);
        }
        $unsetters = array(
            'note',
            'department',
            'priority',
            'subject',
            'assigned',
            'service',
            'status_top',
            'attachments'
        );
        foreach ($unsetters as $unset) {
            if (isset($data[$unset])) {
                unset($data[$unset]);
            }
        }

        if ($admin !== null) {
            $data['admin'] = $admin;
            $status        = $data['status'];
        } else {
            $status = 1;
        }
        if (isset($data['status'])) {
            unset($data['status']);
        }

        $data['ticketid'] = $id;
        $data['date']     = date('Y-m-d H:i:s');

        $data['ip'] = $this->input->ip_address();

        $data['message'] = trim($data['message']);
        $data['message'] = nl2br($data['message']);

        // adminn can have html
        if ($admin == null) {
            $data['message'] = _strip_tags($data['message']);
        }



        if (!isset($data['userid'])) {
            $data['userid'] = 0;
        }



        $_data = do_action('before_ticket_reply_add', array(
            'data' => $data,
            'id' => $id,
            'admin' => $admin
        ));

        $data = $_data['data'];


        $this->db->insert('tblticketreplies', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            logActivity('New Ticket Reply [ReplyID: ' . $insert_id . ']');

            $this->db->where('ticketid', $id);
            $this->db->update('tbltickets', array(
                'lastreply' => date('Y-m-d H:i:s'),
                'status' => $status,
                'adminread' => 0,
                'clientread' => 0
            ));

            $this->load->model('emails_model');
            $this->load->model('clients_model');

            $ticket = $this->get_ticket_by_id($id);
            $userid = $ticket->userid;
            if ($ticket->userid != 0) {
                $email = $this->clients_model->get($userid)->email;
            } else {
                $email = $ticket->ticket_email;
            }

            if ($admin == null) {

                $this->load->model('departments_model');
                $this->load->model('staff_model');
                $staff = $this->staff_model->get('', 1);

                foreach ($staff as $member) {
                    $staff_departments = $this->departments_model->get_staff_departments($member['staffid'], true);
                    if (in_array($ticket->department, $staff_departments)) {
                        $this->emails_model->send_email_template('ticket-reply-to-admin', $member['email'], $data['userid'], false, false, $id);
                    }
                }

            } else {
                $this->emails_model->send_email_template('ticket-reply', $email, $userid, false, false, $id);
            }

            do_action('after_ticket_reply_added', array(
                'data' => $data,
                'id' => $id,
                'admin' => $admin,
                'replyid' => $insert_id
            ));
            return $insert_id;
        }
        return false;
    }

    /**
     *  Delete ticket reply
     * @param   mixed $ticket_id    ticket id
     * @param   mixed $reply_id     reply id
     * @return  boolean
     */
    public function delete_ticket_reply($ticket_id, $reply_id)
    {
        $this->db->where('id', $reply_id);
        $this->db->delete('tblticketreplies');
        if ($this->db->affected_rows() > 0) {
            // Get the reply attachments by passing the reply_id to get_ticket_attachments method
            $attachments = $this->get_ticket_attachments($ticket_id, $reply_id);
            if (count($attachments) > 0) {
                foreach ($attachments as $attachment) {
                    if (unlink(TICKET_ATTACHMENTS_FOLDER . $ticket_id . '/' . $attachment['file_name'])) {
                        $this->db->where('id', $attachment['id']);
                        $this->db->delete('tblticketattachments');
                    }
                }

                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(TICKET_ATTACHMENTS_FOLDER . $ticket_id);
                if (count($other_attachments) == 0) {
                    delete_dir(TICKET_ATTACHMENTS_FOLDER . $ticket_id);
                }
            }

            return true;
        }
        return false;
    }
    /**
     * This functions is used when staff open client ticket
     * @param  mixed $userid client id
     * @param  mixed $id     ticketid
     * @return array
     */
    public function get_user_other_tickets($userid, $id)
    {

        $this->db->select('tbldepartments.name as department_name, tblservices.name as service_name,tblticketstatus.name as status_name,tblstaff.firstname as staff_firstname, tblclients.lastname as staff_lastname,ticketid,subject,firstname,lastname,lastreply');
        $this->db->from('tbltickets');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltickets.department', 'left');
        $this->db->join('tblticketstatus', 'tblticketstatus.ticketstatusid = tbltickets.status', 'left');
        $this->db->join('tblservices', 'tblservices.serviceid = tbltickets.service', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbltickets.userid', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbltickets.admin', 'left');
        $this->db->where('tbltickets.userid', $userid);
        $this->db->where('tbltickets.ticketid !=', $id);

        $tickets = $this->db->get()->result_array();

        $i = 0;
        foreach ($tickets as $ticket) {
            $tickets[$i]['submitter'] = $ticket['firstname'] . ' ' . $ticket['lastname'];
            unset($ticket['firstname']);
            unset($ticket['lastname']);
            $i++;
        }

        return $tickets;
    }

    /**
     * Get all ticket replies
     * @param  mixed  $id     ticketid
     * @param  mixed $userid specific client id
     * @return array
     */
    function get_ticket_replies($id, $userid = '')
    {

        $this->db->select('tblticketreplies.id,tblticketreplies.ip,tblticketreplies.name as from_name,tblticketreplies.email as reply_email, tblticketreplies.admin, tblticketreplies.userid,tblstaff.firstname as staff_firstname,.tblstaff.lastname as staff_lastname,tblclients.firstname as user_firstname,.tblclients.lastname as user_lastname,message,date');
        $this->db->from('tblticketreplies');
        $this->db->join('tblclients', 'tblclients.userid = tblticketreplies.userid', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblticketreplies.admin', 'left');
        $this->db->where('ticketid', $id);
        if (is_numeric($userid)) {
            $this->db->where('tblticketreplies.userid', $userid);
        }
        $this->db->order_by('date', 'asc');
        $replies = $this->db->get()->result_array();
        $i       = 0;
        foreach ($replies as $reply) {
            if ($reply['admin'] !== null || $reply['admin'] != 0) {
                // staff reply
                $replies[$i]['submitter'] = $reply['staff_firstname'] . ' ' . $reply['staff_lastname'];
            } else {
                if ($reply['userid'] != 0) {
                    $replies[$i]['submitter'] = $reply['user_firstname'] . ' ' . $reply['user_lastname'];
                } else {
                    $replies[$i]['submitter'] = $reply['from_name'];
                }
            }
            unset($replies[$i]['staff_firstname']);
            unset($replies[$i]['staff_lastname']);
            unset($replies[$i]['user_firstname']);
            unset($replies[$i]['user_lastname']);
            $replies[$i]['attachments'] = $this->get_ticket_attachments($id, $reply['id']);
            $i++;
        }

        return $replies;
    }

    /**
     * Get ticket notes / admin use
     * @param  mixed $id ticket id
     * @return array
     */
    public function get_ticket_notes($id)
    {

        $this->db->where('ticketid', $id);
        $this->db->join('tblstaff', 'tblstaff.staffid = tblticketnotes.admin', 'left');
        $notes = $this->db->get('tblticketnotes')->result_array();

        $i = 0;
        foreach ($notes as $note) {
            $notes[$i]['note_creator'] = $note['firstname'] . ' ' . $note['lastname'];
            $i++;
        }

        return $notes;
    }

    /**
     * Add new ticket note / admin use
     * @param mixed $data ticket note $_POST data
     */
    public function add_ticket_note($data)
    {
        $data['date']  = date('Y-m-d H:i:s');
        $data['admin'] = get_staff_user_id();
        $data['note']  = nl2br($data['note']);

        $this->db->insert('tblticketnotes', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return true;
        }

        return false;
    }

    /**
     * Delete ticket note
     * @param  mixed $id     ticket id
     * @param  mixed $noteid note id
     * @return boolean
     */
    public function delete_ticket_note($id, $noteid)
    {
        $this->db->where('ticketnoteid', $noteid);
        $this->db->where('ticketid', $id);
        $this->db->delete('tblticketnotes');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
    /**
     * Add new ticket to database
     * @param mixed $data  ticket $_POST data
     * @param mixed $admin If admin adding the ticket passed staff id
     */
    public function add($data, $admin = null)
    {
        if ($admin !== null) {
            $assigned      = $data['assigned'];
            $data['admin'] = $admin;
            unset($data['assigned']);
            unset($data['ticket_client_search']);
        }

        if ($admin == null) {
            // Opened from customer portal otherwise is passed from pipe or admin area
            if (!isset($data['userid'])) {
                $data['userid'] = get_client_user_id();
            }

            $data['status'] = 1;
        }

        /* if(isset($data['attachments'])){
        unset($data['attachments']);
        }*/

        $data['date']      = date('Y-m-d H:i:s');
        $data['ticketkey'] = md5(uniqid(time(), true));
        $data['status']    = 1;

        $data['message'] = trim($data['message']);
        $data['subject'] = trim($data['subject']);
        $data['message'] = nl2br($data['message']);
        // Admin can have html
        if ($admin == null) {
            $data['message'] = _strip_tags($data['message']);
            $data['subject'] = _strip_tags($data['subject']);
        }


        if (!isset($data['userid'])) {
            $data['userid'] = 0;
        }

        $data['ip'] = $this->input->ip_address();
        $_data      = do_action('before_ticket_created', array(
            'data' => $data,
            'admin' => $admin
        ));



        $data = $_data['data'];
        $this->db->insert('tbltickets', $data);

        $ticketid = $this->db->insert_id();

        if ($ticketid) {

            if (isset($assigned) && $assigned !== 'none') {
                $this->db->insert('tblticketassignments', array(
                    'ticketid' => $ticketid,
                    'staffid' => $assigned
                ));
                if ($assigned != get_staff_user_id()) {
                    add_notification(array(
                        'description' => 'Ticket assigned to you - ' . substr($data['subject'], 0, 50) . '...',
                        'touserid' => $assigned,
                        'fromcompany' => 1,
                        'fromuserid' => null,
                        'link' => 'tickets/ticket/' . $ticketid
                    ));
                }
            }

            $this->load->model('emails_model');
            $this->load->model('clients_model');

            if (isset($data['userid']) && $data['userid'] != false) {
                $email = $this->clients_model->get($data['userid'])->email;
            } else {
                $email = $data['email'];
            }

            $template = 'new-ticket-opened-admin';
            if ($admin == null) {
                $template = 'ticket-autoresponse';
                $this->load->model('departments_model');
                $this->load->model('staff_model');
                $staff = $this->staff_model->get('',1);

                foreach ($staff as $member) {
                    $staff_departments = $this->departments_model->get_staff_departments($member['staffid'], true);
                    if (in_array($data['department'], $staff_departments)) {
                      $this->emails_model->send_email_template('new-ticket-created-staff', $member['email'], $data['userid'], false, false, $ticketid);
                    }
                }
            }

            $this->emails_model->send_email_template($template, $email, $data['userid'], false, false, $ticketid);
            do_action('after_ticket_added', $ticketid);
            logActivity('New Ticket Created [ID: ' . $ticketid . ']');
            return $ticketid;
        }

        return false;
    }

    /**
     * Get latest 5 client tickets
     * @param  integer $limit  Optional limit tickets
     * @param  mixed $userid client id
     * @return array
     */
    public function get_client_latests_ticket($limit = 5, $userid = '')
    {

        $this->db->select('tbltickets.userid, statuscolor, tblticketstatus.name as status_name,tbltickets.ticketid, subject, date');
        $this->db->from('tbltickets');
        $this->db->join('tblticketstatus', 'tblticketstatus.ticketstatusid = tbltickets.status', 'left');

        if (is_numeric($userid)) {
            $this->db->where('tbltickets.userid', $userid);
        } else {
            $this->db->where('tbltickets.userid', get_client_user_id());
        }

        $this->db->limit($limit);
        return $this->db->get()->result_array();

    }

    /**
     * Delete ticket from database and all connections
     * @param  mixed $ticketid ticketid
     * @return boolean
     */
    public function delete($ticketid)
    {
        $affectedRows = 0;

        do_action('before_ticket_deleted', $ticketid);

        $this->db->where('ticketid', $ticketid);
        $attachments = $this->db->get('tblticketattachments')->result_array();

        if (count($attachments) > 0) {
            if (is_dir(TICKET_ATTACHMENTS_FOLDER . $ticketid)) {
                if (delete_dir(TICKET_ATTACHMENTS_FOLDER . $ticketid)) {
                    foreach ($attachments as $attachment) {
                        $this->db->where('id', $attachment['id']);
                        $this->db->delete('tblticketattachments');
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
            }
        }

        // Delete replies
        $this->db->where('ticketid', $ticketid);
        $this->db->delete('tblticketreplies');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        // Delete assignments
        $this->db->where('ticketid', $ticketid);
        $this->db->delete('tblticketassignments');
        if ($this->db->affected_rows() == 1) {
            $affectedRows++;
        }

        $this->db->where('ticketid', $ticketid);
        $this->db->delete('tblticketnotes');

        if ($this->db->affected_rows()) {
            $affectedRows++;
        }

        // final delete ticket
        $this->db->where('ticketid', $ticketid);
        $this->db->delete('tbltickets');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            logActivity('Ticket Deleted [ID: ' . $ticketid . ']');
            return true;
        }

        return false;
    }

    /**
     * Update ticket data / admin use
     * @param  mixed $data ticket $_POST data
     * @return boolean
     */
    public function update_single_ticket_settings($data)
    {
        $data    = do_action('before_ticket_settings_updated', $data);
        $current = $this->get_ticket_by_id($data['ticketid']);

        $affectedRows = 0;
        $assigned     = $data['assigned'];
        unset($data['assigned']);

        $this->db->where('ticketid', $data['ticketid']);
        $this->db->update('tbltickets', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        $this->db->where('ticketid', $data['ticketid']);
        $current_assigned = $this->db->get('tblticketassignments')->row();

        if ($current_assigned) {
            if ($current_assigned->staffid != $assigned) {
                if ($assigned !== 'none') {
                    $this->db->where('ticketid', $data['ticketid']);
                    $this->db->update('tblticketassignments', array(
                        'staffid' => $assigned
                    ));
                    if ($this->db->affected_rows() > 0) {
                        add_notification(array(
                            'description' => 'Ticket reassigned to you - ' . substr($data['subject'], 0, 50) . '...',
                            'touserid' => $assigned,
                            'fromcompany' => 1,
                            'fromuserid' => null,
                            'link' => 'tickets/ticket/' . $data['ticketid']
                        ));
                        $affectedRows++;
                    }
                } else {
                    $this->db->where('ticketid', $data['ticketid']);
                    $this->db->delete('tblticketassignments');
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
        } else {
            if ($assigned !== 'none') {
                $this->db->insert('tblticketassignments', array(
                    'ticketid' => $data['ticketid'],
                    'staffid' => $assigned
                ));
                $insert_id = $this->db->insert_id();

                if ($insert_id) {
                    add_notification(array(
                        'description' => 'Ticket assigned to you - ' . substr($data['subject'], 0, 50) . '...',
                        'touserid' => $assigned,
                        'fromcompany' => 1,
                        'fromuserid' => null,
                        'link' => 'tickets/ticket/' . $data['ticketid']
                    ));

                    $affectedRows++;
                }
            }
        }

        if ($affectedRows > 0) {
            logActivity('Ticket Updated [ID: ' . $data['ticketid'] . ']');
            $this->session->set_flashdata('active_tab', true);
            $this->session->set_flashdata('active_tab_settings', true);
            return true;
        }

        return false;
    }

    /**
     * C<ha></ha>nge ticket status
     * @param  mixed $id     ticketid
     * @param  mixed $status status id
     * @return array
     */
    public function change_ticket_status($id, $status)
    {
        $this->db->where('ticketid', $id);
        $this->db->update('tbltickets', array(
            'status' => $status
        ));
        $alert   = 'warning';
        $message = _l('ticket_status_changed_fail');
        if ($this->db->affected_rows() > 0) {
            $alert   = 'success';
            $message = _l('ticket_status_changed_successfuly');
        }

        return array(
            'alert' => $alert,
            'message' => $message
        );
    }

    // Priorities
    /**
     * Get ticket priority by id
     * @param  mixed $id priority id
     * @return mixed     if id passed return object else array
     */
    public function get_priority($id = '')
    {

        if (is_numeric($id)) {
            $this->db->where('priorityid', $id);
            return $this->db->get('tblpriorities')->row();
        }

        return $this->db->get('tblpriorities')->result_array();
    }

    /**
     * Add new ticket priority
     * @param array $data ticket priority data
     */
    public function add_priority($data)
    {
        $this->db->insert('tblpriorities', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            logActivity('New Ticket Priority Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
        }

        return $insert_id;
    }

    /**
     * Update ticket priority
     * @param  array $data ticket priority $_POST data
     * @param  mixed $id   ticket priority id
     * @return boolean
     */
    public function update_priority($data, $id)
    {
        $this->db->where('priorityid', $id);
        $this->db->update('tblpriorities', $data);

        if ($this->db->affected_rows() > 0) {
            logActivity('Ticket Priority Updated [ID: ' . $id . ' Name: ' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete ticket priorit
     * @param  mixed $id ticket priority id
     * @return mixed
     */
    public function delete_priority($id)
    {

        $current = $this->get($id);
        // Check if the priority id is used in tbltickets table
        if (is_reference_in_table('priority', 'tbltickets', $id)) {
            return array(
                'referenced' => true
            );
        }

        $this->db->where('priorityid', $id);
        $this->db->delete('tblpriorities');

        if ($this->db->affected_rows() > 0) {
            logActivity('Ticket Priority Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    // Predefined replies
    /**
     * Get predefined reply  by id
     * @param  mixed $id predefined reply id
     * @return mixed if id passed return object else array
     */
    public function get_predefined_reply($id = '')
    {

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblpredifinedreplies')->row();
        }

        return $this->db->get('tblpredifinedreplies')->result_array();
    }

    /**
     * Add new predefined reply
     * @param array $data predefined reply $_POST data
     */
    public function add_predefined_reply($data)
    {
        $data['message'] = nl2br($data['message']);
        $this->db->insert('tblpredifinedreplies', $data);
        $insertid = $this->db->insert_id();
        logActivity('New Predefined Reply Added [ID: ' . $insertid . ', ' . $data['name'] . ']');
        return $insertid;
    }

    /**
     * Update predefined reply
     * @param  array $data predefined $_POST data
     * @param  mixed $id   predefined reply id
     * @return boolean
     */
    public function update_predefined_reply($data, $id)
    {
        $data['message'] = nl2br($data['message']);
        $this->db->where('id', $id);
        $this->db->update('tblpredifinedreplies', $data);

        if ($this->db->affected_rows() > 0) {
            logActivity('Predefined Reply Updated [ID: ' . $id . ', ' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete predifined reply
     * @param  mixed $id predefined reply id
     * @return boolean
     */
    public function delete_predefined_reply($id)
    {

        $this->db->where('id', $id);
        $this->db->delete('tblpredifinedreplies');

        if ($this->db->affected_rows() > 0) {
            logActivity('Predefined Reply Deleted [' . $id . ']');
            return true;
        }

        return false;
    }

    // Ticket statuses
    /**
     * Get ticket status by id
     * @param  mixed $id status id
     * @return mixed     if id passed return object else array
     */
    public function get_ticket_status($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('ticketstatusid', $id);
            return $this->db->get('tblticketstatus')->row();
        }

        $this->db->order_by('statusorder', 'asc');
        return $this->db->get('tblticketstatus')->result_array();

    }

    /**
     * Add new ticket status
     * @param array ticket status $_POST data
     * @return mixed
     */
    public function add_ticket_status($data)
    {
        $this->db->insert('tblticketstatus', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            logActivity('New Ticket Status Added [ID: ' . $insert_id . ', ' . $data['name'] . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update ticket status
     * @param  array $data ticket status $_POST data
     * @param  mixed $id   ticket status id
     * @return boolean
     */
    public function update_ticket_status($data, $id)
    {
        $this->db->where('ticketstatusid', $id);
        $this->db->update('tblticketstatus', $data);

        if ($this->db->affected_rows() > 0) {
            logActivity('Ticket Status Updated [ID: ' . $id . ' Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete ticket status
     * @param  mixed $id ticket status id
     * @return mixed
     */
    public function delete_ticket_status($id)
    {
        $current = $this->get_ticket_status($id);
        // Default statuses cant be deleted
        if ($current->isdefault == 1) {
            return array(
                'default' => true
            );
            // Not default check if if used in table
        } else if (is_reference_in_table('status', 'tbltickets', $id)) {
            return array(
                'referenced' => true
            );
        }

        $this->db->where('ticketstatusid', $id);
        $this->db->delete('tblticketstatus');

        if ($this->db->affected_rows() > 0) {
            logActivity('Ticket Status Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    // Ticket services
    function get_service($id = '')
    {

        if (is_numeric($id)) {
            $this->db->where('serviceid', $id);
            return $this->db->get('tblservices')->row();
        }

        return $this->db->get('tblservices')->result_array();
    }


    public function add_service($data)
    {
        $this->db->insert('tblservices', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Ticket Service Added [ID: ' . $insert_id . '.' . $data['name'] . ']');
        }

        return $insert_id;
    }

    public function update_service($data, $id)
    {
        $this->db->where('serviceid', $id);
        $this->db->update('tblservices', $data);

        if ($this->db->affected_rows() > 0) {
            logActivity('Ticket Service Updated [ID: ' . $id . ' Name: ' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    public function delete_service($id)
    {
        if (is_reference_in_table('service', 'tbltickets', $id)) {
            return array(
                'referenced' => true
            );
        }

        $this->db->where('serviceid', $id);
        $this->db->delete('tblservices');

        if ($this->db->affected_rows() > 0) {
            logActivity('Ticket Service Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }


    /**
     * @return array
     * Used in home dashboard page
     * Displays weekly ticket openings statistics (chart)
     */
    public function get_weekly_tickets_opening_statistics()
    {
        $this->db->where('CAST(date as DATE) >= "' . date('Y-m-d', strtotime('monday this week', strtotime('last sunday'))) . '" AND CAST(date as DATE) <= "' . date('Y-m-d', strtotime('sunday this week', strtotime('last sunday'))) . '"');
        $tickets = $this->db->get('tbltickets')->result_array();

        $chart = array(
            'labels' => get_weekdays(),
            'datasets' => array(
                array(
                    'label' => 'Tickets',
                    'fillColor' => 'rgba(197, 61, 169, 0.5)',
                    'strokeColor' => '#c53da9',
                    'pointColor' => '#3A4656',
                    'pointStrokeColor' => '#fff',
                    'pointHighlightFill' => '#fff',
                    'pointHighlightStroke' => '#c53da9',
                    'data' => array(
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0
                    )
                )
            )
        );

        foreach ($tickets as $ticket) {

            $ticket_day = date('l', strtotime($ticket['date']));
            $i          = 0;
            foreach ($chart['labels'] as $day) {

                if ($ticket_day == $day) {
                    $chart['datasets'][0]['data'][$i]++;
                }
                $i++;
            }
        }

        return $chart;
    }

    public function add_spam_filter($data)
    {
        $this->db->insert('tblticketsspamcontrol', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return true;
        }

        return false;
    }

    public function edit_spam_filter($data)
    {
        $this->db->where('id', $data['id']);
        unset($data['id']);
        $this->db->update('tblticketsspamcontrol', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function delete_spam_filter($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblticketsspamcontrol');
        if ($this->db->affected_rows() > 0) {

            logActivity('Tickets Spam Filter Deleted');
            return true;
        }

        return false;
    }
}
