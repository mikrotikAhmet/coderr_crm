<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proposals_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('emails_model');
    }

    /**
     * Inserting new proposal function
     * @param mixed $data $_POST data
     */
    public function add($data)
    {

        if (isset($data['allow_comments'])) {
            $data['allow_comments'] = 1;
        } else {
            $data['allow_comments'] = 0;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['addedfrom']   = get_staff_user_id();
        $data['date']   = to_sql_date($this->input->post('date'));

        $data['hash'] = md5(rand() . microtime());
        // Check if the key exists
        $this->db->where('hash', $data['hash']);
        $exists = $this->db->get('tblproposals')->row();

        if ($exists) {
            $data['hash'] = md5(rand() . microtime());
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

        if (!empty($data['open_till'])) {
            $data['open_till'] = to_sql_date($data['open_till']);
        } else {
            unset($data['open_till']);
        }

        $this->db->insert('tblproposals', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }
            $proposal = $this->get($insert_id);
            if ($proposal->assigned != 0) {
                if ($proposal->assigned != get_staff_user_id()) {
                    add_notification(array(
                        'description' => 'Proposal assigned to you - ' . substr($proposal->subject, 0, 50) . '...',
                        'touserid' => $proposal->assigned,
                        'fromuserid' => get_staff_user_id(),
                        'link' => 'proposals/list_proposals/' . $insert_id
                    ));
                }
            }

            logActivity('New Proposal Created [ID:' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update proposal
     * @param  mixed $data $_POST data
     * @param  mixed $id   proposal id
     * @return boolean
     */
    public function update($data, $id)
    {

        $affectedRows     = 0;
        $current_proposal = $this->get($id);

        if (empty($data['rel_type'])) {
            $data['rel_id']   = NULL;
            $data['rel_type'] = '';
        } else {
            if (empty($data['rel_id'])) {
                $data['rel_id']   = NULL;
                $data['rel_type'] = '';
            }
        }

        if (isset($data['allow_comments'])) {
            $data['allow_comments'] = 1;
        } else {
            $data['allow_comments'] = 0;
        }

        $data['date']   = to_sql_date($this->input->post('date'));

        if (!empty($data['open_till'])) {
            $data['open_till'] = to_sql_date($data['open_till']);
        } else {
            $data['open_till'] = NULL;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }

            unset($data['custom_fields']);
        }



        $this->db->where('id', $id);
        $this->db->update('tblproposals', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            $proposal_now = $this->get($id);
            if ($current_proposal->assigned != $proposal_now->assigned) {
                if ($proposal_now->assigned != get_staff_user_id()) {
                    add_notification(array(
                        'description' => 'Proposal assigned to you - ' . substr($proposal_now->subject, 0, 50) . '...',
                        'touserid' => $proposal_now->assigned,
                        'fromuserid' => get_staff_user_id(),
                        'link' => 'proposals/list_proposals/' . $id
                    ));
                }

            }


        }

        if ($affectedRows > 0) {
            logActivity('Proposal Updated [ID:' . $id . ']');
            return true;
        }
        return false;
    }
    /**
     * Get proposals
     * @param  mixed $id proposal id OPTIONAL
     * @return mixed
     */
    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblproposals')->row();
        }

        return $this->db->get('tblproposals')->result_array();
    }

    /**
     * Add proposal comment
     * @param mixed  $data   $_POST comment data
     * @param boolean $client is request coming from the client side
     */
    public function add_comment($data, $client = false)
    {

        if (isset($data['action'])) {
            unset($data['action']);
        }

        $data['dateadded'] = date('Y-m-d H:i:s');
        if ($client == false) {
            $data['staffid'] = get_staff_user_id();
        }

        $data['content'] = nl2br($data['content']);
        $this->db->insert('tblproposalcomments', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $proposal = $this->get($data['proposalid']);
            // Get creator and assigned;
            $this->db->where('staffid', $proposal->addedfrom);
            $this->db->or_where('staffid', $proposal->assigned);
            $staff_proposal = $this->db->get('tblstaff')->result_array();

            if ($client == true) {
                foreach ($staff_proposal as $member) {
                    if (has_permission('manageSales', $member['staffid'])) {
                        add_notification(array(
                            'description' => 'New comment from client on proposal ' . substr($proposal->subject, 0, 50),
                            'touserid' => $member['staffid'],
                            'fromcompany' => 1,
                            'fromuserid' => NULL,
                            'link' => 'proposals/list_proposals/' . $data['proposalid']
                        ));

                        // Send email to admin that client commented
                        $this->emails_model->send_email_template('proposal-comment-to-admin', $member['email'], false, false, false, false, false, false, false, $proposal->id);
                    }
                }
            } else {
                // Send email to client that admin commented
                $this->emails_model->send_email_template('proposal-comment-to-client', $proposal->email, false, false, false, false, false, false, false, $proposal->id);
            }

            return true;
        }

        return false;
    }

    /**
     * Get proposal comments
     * @param  mixed $id proposal id
     * @return array
     */
    public function get_comments($id)
    {
        $this->db->where('proposalid', $id);
        $this->db->order_by('dateadded', 'ASC');
        return $this->db->get('tblproposalcomments')->result_array();
    }

    /**
     * Get proposal single comment
     * @param  mixed $id  comment id
     * @return object
     */
    public function get_comment($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('tblproposalcomments')->row();
    }


    /**
     * Remove proposal comment
     * @param  mixed $id comment id
     * @return boolean
     */
    public function remove_comment($id)
    {
        $comment = $this->get_comment($id);
        $this->db->where('id', $id);
        $this->db->delete('tblproposalcomments');
        if ($this->db->affected_rows() > 0) {
            logActivity('Proposal Comment Removed [ProposalID:' . $comment->proposalid . ', Comment Content: ' . $comment->content . ']');
            return true;
        }

        return false;
    }

    /**
     * Copy proposal
     * @param  mixed $id proposal id
     * @return mixed
     */
    public function copy($id)
    {
        $proposal = $this->get($id);

        $not_copy_fields = array(
            'addedfrom',
            'id',
            'datecreated',
            'hash',
            'status',
            'invoice_id',
            'estimate_id'
        );

        $fields      = $this->db->list_fields('tblproposals');
        $insert_data = array();
        foreach ($fields as $field) {
            if (!in_array($field, $not_copy_fields)) {
                $insert_data[$field] = $proposal->$field;
            }
        }

        $insert_data['addedfrom']   = get_staff_user_id();
        $insert_data['datecreated'] = date('Y-m-d H:i:s');
        $insert_data['status']      = 1;

        $insert_data['hash'] = md5(rand() . microtime());
        // Check if the key exists
        $this->db->where('hash', $insert_data['hash']);
        $exists = $this->db->get('tblproposals')->row();

        if ($exists) {
            $insert_data['hash'] = md5(rand() . microtime());
        }

        // in case open till is expired set new 7 days starting from current date
        if (date('Y-m-d', strtotime($insert_data['open_till']))) {
            $insert_data['open_till'] = date('Y-m-d', strtotime('+7 DAY', strtotime(date('Y-m-d'))));
        }

        $this->db->insert('tblproposals', $insert_data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * Take proposal action (change status) manualy
     * @param  mixed $status status id
     * @param  mixed  $id     proposal id
     * @param  boolean $client is request coming from client side or not
     * @return boolean
     */
    public function mark_action_status($status, $id, $client = false)
    {

        $original_proposal = $this->get($id);
        $this->db->where('id', $id);
        $this->db->update('tblproposals', array(
            'status' => $status
        ));
        if ($this->db->affected_rows() > 0) {

            // Client take action
            if ($client == true) {
                $revert = false;
                // Declined
                if ($status == 2) {
                    $message = 'Proposal Declined';
                } else if ($status == 3) {
                    $message = 'Proposal Accepted';
                    // Accepted
                } else {
                    $revert = true;
                }
                // This is protection that only 3 and 4 statuses can be taken as action from the client side
                if ($revert == true) {
                    $this->db->where('id', $id);
                    $this->db->update('tblproposals', array(
                        'status' => $original_proposal->status
                    ));
                    return false;
                } else {
                    // Get creator and assigned;
                    $this->db->where('staffid', $original_proposal->addedfrom);
                    $this->db->or_where('staffid', $original_proposal->assigned);
                    $staff_proposal = $this->db->get('tblstaff')->result_array();

                    foreach ($staff_proposal as $member) {
                        if (!has_permission('manageSales', $member['staffid'])) {
                            continue;
                        }
                        add_notification(array(
                            'fromcompany' => true,
                            'touserid' => $member['staffid'],
                            'description' => $message . ' - ' . substr($original_proposal->subject, 0, 50) . '...<br />' . $original_proposal->proposal_to,
                            'link' => 'proposals/list_proposals/' . $id
                        ));
                    }

                    $this->load->model('emails_model');

                    // Send thank you to the customer email template
                    if ($status == 3) {
                        // Client declined send template to admin
                        foreach ($staff_proposal as $member) {
                            if (!has_permission('manageSales', $member['staffid'])) {
                                continue;
                            }
                            $this->emails_model->send_email_template('proposal-client-accepted', $member['email'], false, false, false, false, false, false, false, $id);
                        }

                        $this->emails_model->send_email_template('proposal-client-thank-you', $original_proposal->email, false, false, false, false, false, false, false, $id);
                    } else {
                        // Client declined send template to admin
                        foreach ($staff_proposal as $member) {
                            if (!has_permission('manageSales', $member['staffid'])) {
                                continue;
                            }
                            $this->emails_model->send_email_template('proposal-client-declined', $member['email'], false, false, false, false, false, false, false, $id);
                        }
                    }
                }
            } else {
                // in case admin mark as open the the open till date is smaller then current date set open till date 7 days more
                if ((date('Y-m-d', strtotime($original_proposal->open_till)) < date('Y-m-d')) && $status == 1) {
                    $open_till = date('Y-m-d', strtotime('+7 DAY', strtotime(date('Y-m-d'))));
                    $this->db->where('id', $id);
                    $this->db->update('tblproposals', array(
                        'open_till' => $open_till
                    ));
                }
            }

            logActivity('Proposal Status Changes [ProposalID:' . $id . ', Status:' . format_proposal_status($status, '', false) . ',Client Action: ' . (int) $client . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete proposal
     * @param  mixed $id proposal id
     * @return boolean
     */
    public function delete($id)
    {

        $this->db->where('id', $id);
        $this->db->delete('tblproposals');

        if ($this->db->affected_rows() > 0) {
            $this->db->where('proposalid', $id);
            $this->db->delete('tblproposalcomments');
            logActivity('Proposal Deleted [ProposalID:' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Get relation proposal data. Ex lead or customer will return the necesary db fields
     * @param  mixed $rel_id
     * @param  string $rel_type customer/lead
     * @return object
     */
    public function get_relation_data_values($rel_id, $rel_type)
    {
        $data = new StdClass();

        if ($rel_type == 'customer') {
            $this->db->where('userid', $rel_id);
            $_data         = $this->db->get('tblclients')->row();
            $data->address = $_data->address;
            $data->email   = $_data->email;
            $data->phone   = $_data->phonenumber;
            if (!empty($_data->company)) {
                $data->to = $_data->company;
            } else {
                $data->to = $_data->firstname . ' ' . $_data->lastname;
            }

            $this->load->model('clients_model');
            $default_currency = $this->clients_model->get_customer_default_currency($rel_id);

            if ($default_currency != 0) {
                $data->currency = $default_currency;
            }

        } else if ($rel_type = 'lead') {
            $this->db->where('id', $rel_id);
            $_data       = $this->db->get('tblleads')->row();
            $data->phone = $_data->phonenumber;
            $data->to    = $_data->name;
            $data->email = $_data->email;
        }

        return $data;
    }

    /**
     * Sent proposal to email
     * @param  mixed  $id        proposalid
     * @param  string  $template  email template to sent
     * @param  boolean $attachpdf attach proposal pdf or not
     * @return boolean
     */
    public function sent_proposal_to_email($id, $template = '', $attachpdf = true)
    {
        $this->load->model('emails_model');
        $proposal = $this->get($id);

        $pdf = proposal_pdf($proposal);

        if ($attachpdf) {
            $attach = $pdf->Output(slug_it($proposal->subject) . '.pdf', 'S');
            $this->emails_model->add_attachment(array(
                'attachment' => $attach,
                'filename' => slug_it($proposal->subject) . '.pdf',
                'type' => 'application/pdf'
            ));
        }

        $sent = $this->emails_model->send_email_template($template, $proposal->email, false, false, false, false, false, false, false, $id);
        if ($sent) {
            // Set to status sent
            $this->db->where('id', $id);
            $this->db->update('tblproposals', array(
                'status' => 4
            ));
            return true;
        }
        return false;
    }

}
