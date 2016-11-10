<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoices_model extends CRM_Model
{
    private $shipping_fields = array('shipping_street', 'shipping_city', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country');
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get invoice by id
     * @param  mixed $id
     * @return array
     */
    public function get($id = '')
    {
        $this->db->select('*, tblcurrencies.id as currencyid, tblinvoices.id as id, tblcurrencies.name as currency_name');
        $this->db->from('tblinvoices');
        $this->db->join('tblcurrencies', 'tblcurrencies.id = tblinvoices.currency', 'left');

        if (is_numeric($id)) {
            $this->db->where('tblinvoices' . '.id', $id);
            $invoice = $this->db->get()->row();
            if ($invoice) {
                $invoice->items = $this->get_invoice_items($id);
                $i              = 0;
                $this->load->model('payments_model');
                $this->load->model('clients_model');
                $invoice->client = $this->clients_model->get($invoice->clientid);
                if ($invoice->client) {
                    if ($invoice->client->company == '') {
                        $invoice->client->company = $invoice->client->firstname . ' ' . $invoice->client->lastname;
                    }
                }
                $invoice->payments = $this->payments_model->get_invoice_payments($id);
            }
            return $invoice;
        }
        return $this->db->get()->result_array();
    }
    /**
     * Get all invoice items
     * @param  mixed $id invoiceid
     * @return array
     */
    public function get_invoice_items($id)
    {
        $this->db->select('tblinvoiceitems.id,qty,rate,taxrate,tbltaxes.id as taxid,tbltaxes.name as taxname,description as description,long_description,item_order');
        $this->db->from('tblinvoiceitems');
        $this->db->join('tbltaxes', 'tbltaxes.id = tblinvoiceitems.taxid', 'left');
        $this->db->where('tblinvoiceitems.invoiceid', $id);
        $this->db->order_by('tblinvoiceitems.item_order', 'asc');
        return $this->db->get()->result_array();
    }

    public function get_invoice_item($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('tblinvoiceitems')->row();
    }

    /**
     * Get this invoice generated recuring invoices
     * @since  Version 1.0.1
     * @param  mixed $id main invoice id
     * @return array
     */
    public function get_invoice_recuring_invoices($id)
    {
        $this->db->where('is_recurring_from', $id);
        $invoices          = $this->db->get('tblinvoices')->result_array();
        $recuring_invoices = array();
        foreach ($invoices as $invoice) {
            $recuring_invoices[] = $this->get($invoice['id']);
        }
        return $recuring_invoices;
    }

    /**
     * Get invoice total from all statuses
     * @since  Version 1.0.2
     * @param  mixed $data $_POST data
     * @return array
     */
    public function get_invoices_total($data)
    {

        $statuses = array(
            1,
            2,
            3,
            4
        );
        $this->load->model('currencies_model');

        if ((is_using_multiple_currencies() && !isset($data['currency'])) || !isset($data['currency'])) {
            $currencyid = $this->currencies_model->get_base_currency()->id;
        } else if (isset($data['currency'])) {
            $currencyid = $data['currency'];
        }


        $symbol = $this->currencies_model->get_currency_symbol($currencyid);
        $sql    = 'SELECT';
        foreach ($statuses as $invoice_status) {
            $sql .= '(SELECT SUM(total) FROM tblinvoices WHERE status=' . $invoice_status;
            $sql .= ' AND currency =' . $currencyid;
            $sql .= ') as "' . $invoice_status . '",';
        }

        $sql     = substr($sql, 0, -1);
        $result  = $this->db->query($sql)->result_array();
        $_result = array();
        $i       = 0;
        foreach ($result as $key => $val) {
            foreach ($val as $total) {
                $_result[$i]['total']  = $total;
                $_result[$i]['symbol'] = $symbol;
                $i++;
            }
        }

        return $_result;
    }

    /**
     * Insert new invoice to database
     * @param array $data invoiec data
     * @return mixed - false if not insert, invoice ID if succes
     */
    public function add($data, $cron = false, $expense = false)
    {

        $data['number'] = $data['_number'];

        $unsetters = array(
            '_number',
            'currency_symbol',
            'price',
            'taxname',
            'description',
            'long_description',
            'taxid',
            'rate',
            'quantity',
            'item_select',
        );

        foreach ($unsetters as $unseter) {
            if (isset($data[$unseter])) {
                unset($data[$unseter]);
            }
        }


        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $data['hash'] = md5(rand() . microtime());
        // Check if the key exists
        $this->db->where('hash', $data['hash']);
        $exists = $this->db->get('tblinvoices')->row();

        if ($exists) {
            $data['hash'] = md5(rand() . microtime());
        }
        $data['adminnote'] = nl2br($data['adminnote']);
        if (isset($data['terms'])) {
            $data['terms'] = nl2br($data['terms']);
        }
        $data['date'] = to_sql_date($data['date']);
        if (!empty($data['duedate'])) {
            $data['duedate'] = to_sql_date($data['duedate']);
        } else {
            unset($data['duedate']);
        }
        // uncheck sales agent if is not set so the dafault value in base will be NULL
        if ($data['sale_agent'] == '') {
            unset($data['sale_agent']);
        }
        // Since version 1.0.1
        if (isset($data['allowed_payment_modes'])) {
            $data['allowed_payment_modes'] = serialize($data['allowed_payment_modes']);
        } else {
            $data['allowed_payment_modes'] = serialize(array());
        }
        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['year']        = get_option('invoice_year');
        if ($cron == false) {
            $data['addedfrom'] = get_staff_user_id();
        }
        $items = array();
        if (isset($data['newitems'])) {
            $items = $data['newitems'];
            unset($data['newitems']);
        }

        if (!isset($data['include_shipping'])) {
            foreach ($this->shipping_fields as $_s_field) {
                if (isset($data[$_s_field])) {
                    $data[$_s_field] = NULL;
                }
            }
            $data['show_shipping_on_invoice'] = 1;
            $data['include_shipping']         = 0;
        } else {
            // we dont need to overwrite to 1 unless its coming from the main function add
            if ($cron == false && $expense == false) {
                $data['include_shipping'] = 1;
                // set by default for the next time to be checked
                if (isset($data['show_shipping_on_invoice'])) {
                    $data['show_shipping_on_invoice'] = 1;
                } else {
                    $data['show_shipping_on_invoice'] = 0;
                }
            }

            // else its just like they are passed
        }


        if ($data['discount_total'] == 0) {
            $data['discount_type'] = '';
        }

        $_data = do_action('before_invoice_added', array(
            'data' => $data,
            'items' => $items
        ));
        $data  = $_data['data'];
        $items = $_data['items'];

        $this->db->insert('tblinvoices', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }

            // Update next invoice number in settings
            $this->db->where('name', 'next_invoice_number');
            $this->db->set('value', 'value+1', FALSE);
            $this->db->update('tbloptions');
            update_invoice_status($insert_id);
            if (count($items) > 0) {
                foreach ($items as $key => $item) {
                    $this->db->insert('tblinvoiceitems', array(
                        'description' => $item['description'],
                        'long_description' => nl2br($item['long_description']),
                        'qty' => $item['qty'],
                        'rate' => $item['rate'],
                        'taxid' => $item['taxid'],
                        'invoiceid' => $insert_id,
                        'item_order' => $item['order']
                    ));
                }
            }

            if ($cron == false && $expense == false) {
                $this->log_invoice_activity($insert_id, 'created the invoice');
            } else if ($cron == false && $expense == true) {
                $this->log_invoice_activity($insert_id, 'converted to invoice from expense');
            } else if ($cron == true && $expense == false) {
                $this->log_invoice_activity($insert_id, '[Recurring] Invoice created by CRON', true);
            } else {
                $this->log_invoice_activity($insert_id, '[Invoice From Expense] Invoice created by CRON', true);
            }

            do_action('after_invoice_added', $insert_id);
            return $insert_id;
        }

        return false;
    }

    /**
     * Copy invoice
     * @param  mixed $id invoice id to copy
     * @return mixed
     */
    public function copy($id)
    {
        $_invoice = $this->get($id);

        $new_invoice_data             = array();
        $new_invoice_data['clientid'] = $_invoice->clientid;
        $new_invoice_data['_number']  = get_option('next_invoice_number');
        $new_invoice_data['date']     = _d(date('Y-m-d'));

        if ($_invoice->duedate) {
            if (get_option('invoice_due_after') != 0) {
                $new_invoice_data['duedate'] = _d(date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime(date('Y-m-d')))));
            }
        }

        $new_invoice_data['currency']                 = $_invoice->currency;
        $new_invoice_data['subtotal']                 = $_invoice->subtotal;
        $new_invoice_data['total']                    = $_invoice->total;
        $new_invoice_data['adminnote']                = $_invoice->adminnote;
        $new_invoice_data['adjustment']               = $_invoice->adjustment;
        $new_invoice_data['discount_percent']         = $_invoice->discount_percent;
        $new_invoice_data['discount_total']           = $_invoice->discount_total;
        $new_invoice_data['recurring']                = $_invoice->recurring;
        $new_invoice_data['discount_type']            = $_invoice->discount_type;
        $new_invoice_data['terms']                    = $_invoice->terms;
        $new_invoice_data['sale_agent']               = $_invoice->sale_agent;
        // Since version 1.0.6
        $new_invoice_data['billing_street']           = $_invoice->billing_street;
        $new_invoice_data['billing_city']             = $_invoice->billing_city;
        $new_invoice_data['billing_state']            = $_invoice->billing_state;
        $new_invoice_data['billing_zip']              = $_invoice->billing_zip;
        $new_invoice_data['billing_country']          = $_invoice->billing_country;
        $new_invoice_data['shipping_street']          = $_invoice->shipping_street;
        $new_invoice_data['shipping_city']            = $_invoice->shipping_city;
        $new_invoice_data['shipping_state']           = $_invoice->shipping_state;
        $new_invoice_data['shipping_zip']             = $_invoice->shipping_zip;
        $new_invoice_data['shipping_country']         = $_invoice->shipping_country;

        if($_invoice->include_shipping == 1){
            $new_invoice_data['include_shipping']         = $_invoice->include_shipping;
        }

        $new_invoice_data['show_shipping_on_invoice'] = $_invoice->show_shipping_on_invoice;
        // Set to unpaid status automatically
        $new_invoice_data['status']                   = 1;
        $new_invoice_data['clientnote']               = $_invoice->clientnote;
        $new_invoice_data['adminnote']                = '';
        $new_invoice_data['allowed_payment_modes']    = unserialize($_invoice->allowed_payment_modes);

        $new_invoice_data['newitems'] = array();

        $key = 1;
        foreach ($_invoice->items as $item) {
            $new_invoice_data['newitems'][$key]['description']      = $item['description'];
            $new_invoice_data['newitems'][$key]['long_description'] = $item['long_description'];
            $new_invoice_data['newitems'][$key]['qty']              = $item['qty'];
            $new_invoice_data['newitems'][$key]['taxid']            = $item['taxid'];
            $new_invoice_data['newitems'][$key]['rate']             = $item['rate'];
            $new_invoice_data['newitems'][$key]['order']            = $item['item_order'];
            $key++;
        }

        $id = $this->invoices_model->add($new_invoice_data);
        if ($id) {
            $custom_fields = get_custom_fields('invoice');
            foreach ($custom_fields as $field) {
                $value = get_custom_field_value($_invoice->id, $field['id'], 'invoice');
                if ($value == '') {
                    continue;
                }
                $this->db->insert('tblcustomfieldsvalues', array(
                    'relid' => $id,
                    'fieldid' => $field['id'],
                    'fieldto' => 'invoice',
                    'value' => $value
                    ));
            }

            logActivity('Copied Invoice ' . format_invoice_number($_invoice->id));
            return $id;
        }

        return false;
    }

    /**
     * Update invoice data
     * @param  array $data invoice data
     * @param  mixed $id   invoiceid
     * @return boolean
     */
    public function update($data, $id)
    {
        $original_invoice = $this->get($id);

        if($invoice->status == 2){
            return false;
        }
        $affectedRows = 0;
        $prefix       = get_option('invoice_prefix');

        $data['number'] = substr($data['number'], strlen($prefix));
        $data['number'] = trim($data['number']);

        if (get_option('invoice_number_format') == 2) {
            $_temp_number   = explode('/', $data['number']);
            $data['number'] = $_temp_number[1];
        }


        $original_number  = $original_invoice->number;

        unset($data['currency_symbol']);
        unset($data['price']);
        unset($data['taxname']);
        unset($data['taxid']);
        unset($data['isedit']);
        unset($data['description']);
        unset($data['long_description']);
        unset($data['tax']);
        unset($data['rate']);
        unset($data['quantity']);
        unset($data['item_select']);

        $items = array();
        if (isset($data['items'])) {
            $items = $data['items'];
            unset($data['items']);
        }

        $newitems = array();
        if (isset($data['newitems'])) {
            $newitems = $data['newitems'];
            unset($data['newitems']);
        }

        if ($data['adjustment'] == 'NaN') {
            $data['adjustment'] = 0;
        }

        if (!isset($data['include_shipping'])) {
            foreach ($this->shipping_fields as $_s_field) {
                if (isset($data[$_s_field])) {
                    $data[$_s_field] = NULL;
                }
            }
            $data['show_shipping_on_invoice'] = 1;
            $data['include_shipping']         = 0;
        } else {
            $data['include_shipping'] = 1;
            // set by default for the next time to be checked
            if (isset($data['show_shipping_on_invoice'])) {
                $data['show_shipping_on_invoice'] = 1;
            } else {
                $data['show_shipping_on_invoice'] = 0;
            }
        }

        // uncheck sales agent if is not set so the dafault value in base will be NULL
        if ($data['sale_agent'] == '') {
            $data['sale_agent'] = NULL;
        }

        // Since version 1.0.1
        if (isset($data['allowed_payment_modes'])) {
            $data['allowed_payment_modes'] = serialize($data['allowed_payment_modes']);
        } else {
            $data['allowed_payment_modes'] = serialize(array());
        }

        if (isset($data['terms'])) {
            $data['terms'] = nl2br($data['terms']);
        }

        $data['date']    = to_sql_date($data['date']);
        $data['duedate'] = to_sql_date($data['duedate']);

        if ($data['discount_total'] == 0) {
            $data['discount_type'] = '';
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }

            unset($data['custom_fields']);
        }

        $action_data = array(
            'data' => $data,
            'newitems' => $newitems,
            'items' => $items,
            'id' => $id,
            'removed_items' => array()
        );
        if (isset($data['removed_items'])) {
            $action_data['removed_items'] = $data['removed_items'];
        }

        $_data                 = do_action('before_invoice_updated', $action_data);
        $data['removed_items'] = $_data['removed_items'];
        $newitems              = $_data['newitems'];
        $items                 = $_data['items'];
        $data                  = $_data['data'];

        // Delete items checked to be removed from database
        if (isset($data['removed_items'])) {
            foreach ($data['removed_items'] as $remove_item_id) {
                $original_item = $this->get_invoice_item($remove_item_id);
                $this->db->where('invoiceid', $id);
                $this->db->where('id', $remove_item_id);
                $this->db->delete('tblinvoiceitems');
                if ($this->db->affected_rows() > 0) {
                    $this->log_invoice_activity($id, 'removed item <b>' . $original_item->description . '</b>');
                    $affectedRows++;
                }
            }
            unset($data['removed_items']);
        }

        $this->db->where('id', $id);
        $this->db->update('tblinvoices', $data);

        if ($this->db->affected_rows() > 0) {
            update_invoice_status($id);
            $affectedRows++;

            if ($original_number != $data['number']) {
                $this->log_invoice_activity($original_invoice->id, 'Invoice Number Changed: From: ' . format_invoice_number($original_invoice->id) . ' To: ' . format_invoice_number($id));
            }

        }

        $this->load->model('taxes_model');
        if (count($items) > 0) {
            foreach ($items as $key => $item) {
                $invoice_item_id = $item['itemid'];

                $original_item = $this->get_invoice_item($invoice_item_id);

                $this->db->where('id', $invoice_item_id);
                $this->db->update('tblinvoiceitems', array(
                    'item_order' => $item['order']
                ));
                if ($this->db->affected_rows() > 0) {
                    $affectedRows++;
                }
                // Check for invoice item short description change
                $this->db->where('id', $invoice_item_id);
                $this->db->update('tblinvoiceitems', array(
                    'description' => $item['description']
                ));

                if ($this->db->affected_rows() > 0) {
                    $this->log_invoice_activity($id, 'updated item short description from ' . $original_item->description . ' to ' . $item['description']);
                    $affectedRows++;
                }

                // Check for item long description change
                $this->db->where('id', $invoice_item_id);
                $this->db->update('tblinvoiceitems', array(
                    'long_description' => nl2br($item['long_description'])
                ));

                if ($this->db->affected_rows() > 0) {
                    $this->log_invoice_activity($id, 'updated item long description from ' . $original_item->long_description . ' to ' . $item['long_description']);
                    $affectedRows++;
                }
                // Check for tax id change
                $original_tax = $this->taxes_model->get($original_item->taxid);

                $this->db->where('id', $invoice_item_id);
                $this->db->update('tblinvoiceitems', array(
                    'taxid' => $item['taxid']
                ));

                if ($this->db->affected_rows() > 0) {
                    $tax_now = $this->taxes_model->get($item['taxid']);
                    if ($original_tax) {
                        if ($tax_now) {
                            $this->log_invoice_activity($id, 'updated tax (' . $original_tax->name . ') from ' . $original_tax->taxrate . '% to (' . $tax_now->name . ') ' . $tax_now->taxrate . '%');
                        } else {
                            $this->log_invoice_activity($id, 'removed tax (' . $original_tax->name . ') ' . $original_tax->taxrate . '%');
                        }
                    } else {
                        $this->log_invoice_activity($id, 'added tax (' . $tax_now->name . ') ' . $tax_now->taxrate . '%');
                    }
                    $affectedRows++;
                }

                // Check for item rate change
                $this->db->where('id', $invoice_item_id);
                $this->db->update('tblinvoiceitems', array(
                    'rate' => $item['rate']
                ));
                if ($this->db->affected_rows() > 0) {
                    $this->log_invoice_activity($id, 'updated item rate from ' . $original_item->rate . ' to ' . $item['rate']);
                    $affectedRows++;
                }
                // CHeck for invoice quantity change
                $this->db->where('id', $invoice_item_id);
                $this->db->update('tblinvoiceitems', array(
                    'qty' => $item['qty']
                ));

                if ($this->db->affected_rows() > 0) {
                    $this->log_invoice_activity($id, 'updated quantity on item <b>' . $item['description'] . '</b> from ' . $original_item->qty . ' to ' . $item['qty']);
                    $affectedRows++;
                }
            }
        }
        if (count($newitems) > 0) {
            foreach ($newitems as $key => $item) {
                $this->db->insert('tblinvoiceitems', array(
                    'description' => $item['description'],
                    'long_description' => nl2br($item['long_description']),
                    'qty' => $item['qty'],
                    'rate' => $item['rate'],
                    'taxid' => $item['taxid'],
                    'invoiceid' => $id,
                    'item_order' => $item['order']
                ));
                $new_item_added = $this->db->insert_id();
                if ($new_item_added) {
                    $this->log_invoice_activity($id, 'added new item <b>' . $item['description'] . '</b>');
                    $affectedRows++;
                }
            }
        }

        if ($affectedRows > 0) {
            do_action('after_invoice_updated', $id);
            return true;
        }

        return false;
    }

    public function get_attachments($invoiceid, $id = '')
    {

        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('invoiceid', $invoiceid);
        }

        $result = $this->db->get('tblinvoiceattachments');
        if (is_numeric($id)) {
            return $result->row();
        } else {
            return $result->result_array();
        }
    }
    /**
     *  Delete invoice attachment
     * @since  Version 1.0.4
     * @param   mixed $id  attachmentid
     * @return  boolean
     */
    public function delete_attachment($id)
    {
        $attachment = $this->get_attachments('', $id);
        if ($attachment) {
            if (unlink(INVOICE_ATTACHMENTS_FOLDER . $attachment->invoiceid . '/' . $attachment->file_name)) {
                $this->db->where('id', $id);
                $this->db->delete('tblinvoiceattachments');
                $other_attachments = list_files(INVOICE_ATTACHMENTS_FOLDER . $attachment->invoiceid);
                if (count($other_attachments) == 0) {
                    // delete the dir if no other attachments found
                    delete_dir(INVOICE_ATTACHMENTS_FOLDER . $attachment->invoiceid);
                }
            }

            return true;

        }
        return false;
    }
    /**
     * Delete invoice items and all connections
     * @param  mixed $id invoiceid
     * @return boolean
     */
    public function delete($id)
    {

        if (get_option('delete_only_on_last_invoice') == 1) {
            if (!is_last_invoice($id)) {
                return false;
            }
        }

        do_action('before_invoice_deleted', $id);
        $this->db->where('id', $id);
        $this->db->delete('tblinvoices');

        if ($this->db->affected_rows() > 0) {
            // Check if is invoice from expense
            $this->db->where('invoiceid', $id);
            $is_expense_invoice = $this->db->get('tblexpenses')->row();
            if ($is_expense_invoice) {
                $this->db->where('id', $is_expense_invoice->id);
                $this->db->update('tblexpenses', array(
                    'invoiceid' => NULL
                ));
            }
            if (get_option('invoice_number_decrement_on_delete') == 1) {
                $current_next_invoice_number = get_option('next_invoice_number');
                if ($current_next_invoice_number > 1) {
                    // Decrement next invoice number to
                    $this->db->where('name', 'next_invoice_number');
                    $this->db->set('value', 'value-1', FALSE);
                    $this->db->update('tbloptions');
                }
            }

            // if is converted from estimate set the estimate invoice to null
            if(total_rows('tblestimates',array('invoiceid'=>$id)) > 0){
                $this->db->where('invoiceid',$id);
                $estimate = $this->db->get('tblestimates')->row();
                $this->db->where('id',$estimate->id);
                $this->db->update('tblestimates',array(
                    'invoiceid'=>NULL,
                    'invoiced_date'=>NULL
                    ));

                $this->load->model('estimates_model');
                $this->estimates_model->log_estimate_activity($estimate->id,'deleted the created invoice');
            }
            $this->db->where('invoiceid', $id);
            $this->db->delete('tblinvoiceitems');

            $this->db->where('invoiceid', $id);
            $this->db->delete('tblinvoicepaymentrecords');

            $this->db->where('invoiceid', $id);
            $this->db->delete('tblinvoiceactivity');

            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'invoice');
            $this->db->delete('tblcustomfieldsvalues');

            return true;
        }
        return false;
    }
    /**
     * Set invoice to sent when email is successfuly sended to client
     * @param mixed $id invoiceid
     * @param  mixed $cron is request coming from cron
     * @param  mixed $manually is staff manualy marking this invoice as sent
     * @return  boolean
     */
    public function set_invoice_sent($id, $cron = false, $manually = false)
    {
        $this->db->where('id', $id);
        $this->db->update('tblinvoices', array(
            'sent' => 1,
            'datesend' => date('Y-m-d H:i:s')
        ));

        $marked = false;
        if ($this->db->affected_rows() > 0) {
            $marked = true;
        }
        if ($cron == true) {
            $description = 'Invoice sent to client by CRON';
        } else {
            if ($manually == false) {
                $description = get_staff_full_name() . ' sent invoice to client';
            } else {
                $description = get_staff_full_name() . ' marked invoice as sent';
            }
        }
        $this->log_invoice_activity($id, $description, $cron);

        return $marked;
    }
    /**
     * Sent overdue notice to client for this invoice
     * @since  Since Version 1.0.1
     * @param  mxied  $id   invoiceid
     * @param  boolean $cron is request from cron
     * @return boolean
     */
    public function send_invoice_overdue_notice($id, $cron = false)
    {

        $this->load->model('emails_model');
        $invoice = $this->get($id);

        $invoice_number = format_invoice_number($invoice->id);
        $pdf            = invoice_pdf($invoice);
        $attach         = $pdf->Output($invoice_number . '.pdf', 'S');

        $this->emails_model->add_attachment(array(
            'attachment' => $attach,
            'filename' => $invoice_number . '.pdf',
            'type' => 'application/pdf'
        ));

        $send = $this->emails_model->send_email_template('invoice-overdue-notice', $invoice->client->email, $invoice->clientid, $id);

        if ($send) {
            if ($cron == true) {
                $_from = '[CRON]';
            } else {
                $_from = get_staff_full_name();
            }
            $this->db->where('id', $id);
            $this->db->update('tblinvoices', array(
                'last_overdue_reminder' => date('Y-m-d')
            ));

            $this->log_invoice_activity($id, _l('user_sent_overdue_reminder', $_from), $cron);
            return true;
        }

        return false;
    }

    /**
     * Sent invoice to client
     * @param  mixed  $id        invoiceid
     * @param  string  $template  email template to sent
     * @param  boolean $attachpdf attach invoice pdf or not
     * @param  boolean $cron      is request coming from cron
     * @return boolean
     */
    public function sent_invoice_to_client($id, $template = '', $attachpdf = true, $cron = false)
    {

        $this->load->model('emails_model');
        $invoice = $this->get($id);
        if ($template == '') {
            if ($invoice->sent == 0) {
                $template = 'invoice-send-to-client';
            } else {
                $template = 'invoice-already-send';
            }
        }

        $invoice_number = format_invoice_number($invoice->id);
        $pdf            = invoice_pdf($invoice);

        if ($attachpdf) {
            $attach = $pdf->Output($invoice_number . '.pdf', 'S');
            $this->emails_model->add_attachment(array(
                'attachment' => $attach,
                'filename' => $invoice_number . '.pdf',
                'type' => 'application/pdf'
            ));
        }

        if ($this->input->post('email_attachments')) {
            $_other_attachments = $this->input->post('email_attachments');
            foreach ($_other_attachments as $attachment) {
                $_attachment = $this->get_attachments($id, $attachment);
                $this->emails_model->add_attachment(array(
                    'attachment' => INVOICE_ATTACHMENTS_FOLDER . $id . '/' . $_attachment->file_name,
                    'filename' => $_attachment->file_name,
                    'type' => $_attachment->filetype,
                    'read' => true
                ));
            }
        }

        $send = $this->emails_model->send_email_template($template, $invoice->client->email, $invoice->clientid, $id);
        if ($send) {
            $this->set_invoice_sent($id, $cron);
            return true;
        }

        return false;
    }
    /**
     * All invoice activity
     * @param  mixed $id invoiceid
     * @return array
     */
    public function get_invoice_activity($id)
    {
        $this->db->where('invoiceid', $id);
        $this->db->order_by('date', 'asc');
        return $this->db->get('tblinvoiceactivity')->result_array();
    }
    /**
     * Log invoice activity to database
     * @param  mixed $id   invoiceid
     * @param  string $description activity description
     */
    public function log_invoice_activity($id, $description = '', $cron = false, $client = false)
    {
        $staffid = get_staff_user_id();

        if ($cron == true) {
            $staffid = 'Cron Job';
        } else if ($client == true) {
            $staffid = NULL;
        }

        $this->db->insert('tblinvoiceactivity', array(
            'description' => $description,
            'date' => date('Y-m-d H:i:s'),
            'invoiceid' => $id,
            'staffid' => $staffid
        ));
    }
}
