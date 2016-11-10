<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expenses_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get expense(s)
     * @param  mixed $id Optional expense id
     * @return mixed     object or array
     */
    public function get($id = '')
    {
        $this->db->select('*,tblexpensescategories.name as category_name,tblinvoicepaymentsmodes.name as payment_mode_name,tbltaxes.name as tax_name, tblexpenses.id as expenseid');
        $this->db->from('tblexpenses');
        $this->db->join('tblclients', 'tblclients.userid = tblexpenses.clientid', 'left');
        $this->db->join('tblinvoicepaymentsmodes', 'tblinvoicepaymentsmodes.id = tblexpenses.paymentmode', 'left');
        $this->db->join('tbltaxes', 'tbltaxes.id = tblexpenses.tax', 'left');
        $this->db->join('tblexpensescategories', 'tblexpensescategories.id = tblexpenses.category');
        if (is_numeric($id)) {
            $this->db->where('tblexpenses.id', $id);
            return $this->db->get()->row();
        }
        return $this->db->get()->result_array();
    }

    /**
     * Add new expense
     * @param mixed $data All $_POST data
     * @return  mixed
     */
    public function add($data)
    {
        $data['date'] = to_sql_date($data['date']);
        $data['note'] = nl2br($data['note']);

        if (isset($data['billable'])) {
            $data['billable'] = 1;
        } else {
            $data['billable'] = 0;
        }

        if (isset($data['create_invoice_billable'])) {
            $data['create_invoice_billable'] = 1;
        } else {
            $data['create_invoice_billable'] = 0;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }


        if (isset($data['send_invoice_to_customer'])) {
            $data['send_invoice_to_customer'] = 1;
        } else {
            $data['send_invoice_to_customer'] = 0;
        }

        if ($data['repeat_every'] != '') {
            $data['recurring'] = 1;
            if ($data['repeat_every'] == 'custom') {
                $data['repeat_every']     = $data['repeat_every_custom'];
                $data['recurring_type']   = $data['repeat_type_custom'];
                $data['custom_recurring'] = 1;
            } else {
                $_temp                    = explode('-', $data['repeat_every']);
                $data['recurring_type']   = $_temp[1];
                $data['repeat_every']     = $_temp[0];
                $data['custom_recurring'] = 0;
            }
        } else {
            $data['recurring'] = 0;
        }

        unset($data['repeat_type_custom']);
        unset($data['repeat_every_custom']);

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');

        $this->db->insert('tblexpenses', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
             if (isset($custom_fields)) {
                 handle_custom_fields_post($insert_id, $custom_fields);
             }
            logActivity('New Expense Added [' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update expense
     * @param  mixed $data All $_POST data
     * @param  mixed $id   expense id to update
     * @return boolean
     */
    public function update($data, $id)
    {
        $affectedRows = 0;
        $data['date'] = to_sql_date($data['date']);
        $data['note'] = nl2br($data['note']);

        if ($data['repeat_every'] != '') {
            $data['recurring'] = 1;
            if ($data['repeat_every'] == 'custom') {
                $data['repeat_every']     = $data['repeat_every_custom'];
                $data['recurring_type']   = $data['repeat_type_custom'];
                $data['custom_recurring'] = 1;
            } else {
                $_temp                    = explode('-', $data['repeat_every']);
                $data['recurring_type']   = $_temp[1];
                $data['repeat_every']     = $_temp[0];
                $data['custom_recurring'] = 0;
            }
        } else {
            $data['recurring'] = 0;
        }

        unset($data['repeat_type_custom']);
        unset($data['repeat_every_custom']);

         if(isset($data['custom_fields'])){
            $custom_fields = $data['custom_fields'];
            if(handle_custom_fields_post($id,$custom_fields)){
                $affectedRows++;
            }

            unset($data['custom_fields']);
        }


        if (isset($data['create_invoice_billable'])) {
            $data['create_invoice_billable'] = 1;
        } else {
            $data['create_invoice_billable'] = 0;
        }

        if (isset($data['billable'])) {
            $data['billable'] = 1;
        } else {
            $data['billable'] = 0;
        }


        if (isset($data['send_invoice_to_customer'])) {
            $data['send_invoice_to_customer'] = 1;
        } else {
            $data['send_invoice_to_customer'] = 0;
        }


        $this->db->where('id', $id);
        $this->db->update('tblexpenses', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Expense Updated [' . $id . ']');
            $affectedRows++;
        }

        if($affectedRows > 0){
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete expense from database, if used return
     */
    public function delete($id)
    {
        $_expense = $this->get($id);
        if ($_expense->invoiceid !== NULL) {
            return array(
                'invoiced' => true
            );
        }
        $this->db->where('id', $id);
        $this->db->delete('tblexpenses');

        if ($this->db->affected_rows() > 0) {

            // Delete the custom field values
            $this->db->where('relid',$id);
            $this->db->where('fieldto','expenses');
            $this->db->delete('tblcustomfieldsvalues');

            logActivity('Expense Deleted [' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Convert expense to invoice
     * @param  mixed  $id   expense id
     * @param  boolean $cron is request from cron or not
     * @return mixed
     */
    public function convert_to_invoice($id, $cron = false)
    {
        $expense = $this->get($id);

        $new_invoice_data = array();

        $this->load->model('clients_model');

        $client = $this->clients_model->get($expense->clientid);


        $new_invoice_data['clientid']   = $expense->clientid;
        $new_invoice_data['_number']    = get_option('next_invoice_number');
        $new_invoice_data['date']       = _d(date('Y-m-d'));
        if(get_option('invoice_due_after') != 0){
            $new_invoice_data['duedate']    = _d(date('Y-m-d',strtotime('+'.get_option('invoice_due_after') .' DAY',strtotime(date('Y-m-d'
                )))));
        }
        $new_invoice_data['terms']      = get_option('predefined_terms_invoice');
        $new_invoice_data['clientnote'] = get_option('predefined_clientnote_invoice');

        $new_invoice_data['discount_total'] = 0;

        $new_invoice_data['sale_agent'] = 0;
        $new_invoice_data['adjustment'] = 0;
        $new_invoice_data['subtotal']   = $expense->amount;

        $total = $expense->amount;

        if ($expense->tax != 0) {
            $_total = ($expense->amount / 100 * $expense->taxrate);
            $total += $_total;
        }

        $new_invoice_data['total'] = $total;

        $this->load->model('currencies_model');
        $new_invoice_data['currency'] = $this->currencies_model->get_base_currency()->id;

        $new_invoice_data['status']     = 1;
        $new_invoice_data['adminnote']  = 'Converted from expense ID: ' . $expense->expenseid;

        // Since version 1.0.6
        $new_invoice_data['billing_street'] = $client->billing_street;
        $new_invoice_data['billing_city'] =   $client->billing_city;
        $new_invoice_data['billing_state'] = $client->billing_state;
        $new_invoice_data['billing_zip'] = $client->billing_zip;
        $new_invoice_data['billing_country'] = $client->billing_country;

        if(!empty($client->shipping_street)){
            $new_invoice_data['shipping_street'] = $client->shipping_street;
            $new_invoice_data['shipping_city'] = $client->shipping_city;
            $new_invoice_data['shipping_state'] = $client->shipping_state;
            $new_invoice_data['shipping_zip'] = $client->shipping_zip;
            $new_invoice_data['shipping_country'] = $client->shipping_country;
            $new_invoice_data['include_shipping'] = 1;
            $new_invoice_data['show_shipping_on_invoice'] = 1;
        } else {
            $new_invoice_data['include_shipping'] = 0;
            $new_invoice_data['show_shipping_on_invoice'] = 1;
        }

        $this->load->model('payment_modes_model');

        $modes      = $this->payment_modes_model->get();
        $temp_modes = array();

        foreach ($modes as $mode) {
            $temp_modes[] = $mode['id'];
        }

        $new_invoice_data['allowed_payment_modes'] = $temp_modes;

        $new_invoice_data['newitems'][1]['description'] = $expense->name;
        $new_invoice_data['newitems'][1]['long_description'] = $expense->description;
        $new_invoice_data['newitems'][1]['qty'] = 1;
        $new_invoice_data['newitems'][1]['taxid'] = $expense->tax;
        $new_invoice_data['newitems'][1]['rate'] = $expense->amount;
        $new_invoice_data['newitems'][1]['order'] = 1;

        $this->load->model('invoices_model');
        $invoiceid = $this->invoices_model->add($new_invoice_data, $cron, true);

        if ($invoiceid) {
            $this->db->where('id', $expense->expenseid);
            $this->db->update('tblexpenses', array(
                'invoiceid' => $invoiceid
            ));
            logActivity('Expense Converted To Invoice [ExpenseID' . $expense->expenseid . ', InvoiceID: ' . $invoiceid . ', Cron:' . $cron . ']');
            return $invoiceid;
        }

        return false;
    }

    /**
     * Copy expense
     * @param  mixed $id expense id to copy from
     * @return mixed
     */
    public function copy($id)
    {
        $expense_fields   = $this->db->list_fields('tblexpenses');
        $expense          = $this->get($id);
        $new_expense_data = array();
        foreach ($expense_fields as $field) {
            if (isset($expense->$field)) {
                // We dont need the invoiceid field
                if ($field != 'invoiceid' && $field != 'id') {
                    $new_expense_data[$field] = $expense->$field;
                }
            }
        }
        $new_expense_data['addedfrom'] = get_staff_user_id();
        $new_expense_data['dateadded'] = date('Y-m-d H:i:s');

        $this->db->insert('tblexpenses', $new_expense_data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
         // Get the old expense custom field and add to the new
         $custom_fields = get_custom_fields('expenses');
            foreach($custom_fields as $field) {
                $value = get_custom_field_value($id,$field['id'],'expenses');
                if($value == ''){continue;}
                $this->db->insert('tblcustomfieldsvalues',array(
                    'relid'=>$insert_id,
                    'fieldid'=>$field['id'],
                    'fieldto'=>'expenses',
                    'value'=>$value,
                    ));
            }
        logActivity('Expense Copied [ExpenseID' . $id . ', NewExpenseID: ' . $insert_id . ']');
            return $insert_id;
        }

        return false;

    }

    /**
     * Delete Expense attachment
     * @param  mixed $id expense id
     * @return boolean
     */
    public function delete_expense_attachment($id)
    {

        if (delete_dir(EXPENSE_ATTACHMENTS_FOLDER . $id)) {

            $this->db->where('id', $id);
            $this->db->update('tblexpenses', array(
                'attachment' => ''
            ));

            logActivity('Expense Receipt Deleted [ExpenseID' . $id . ']');
            return true;
        }

        return false;
    }

    /* Categories start */
    /**
     * Get expense category
     * @param  mixed $id category id (Optional)
     * @return mixed     object or array
     */
    public function get_category($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblexpensescategories')->row();
        }

        return $this->db->get('tblexpensescategories')->result_array();
    }

    /**
     * Add new expense category
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_category($data)
    {

        $data['description'] = nl2br($data['description']);
        $this->db->insert('tblexpensescategories', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Expense Category Added [ID: ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update expense category
     * @param  mixed $data All $_POST data
     * @param  mixed $id   expense id to update
     * @return boolean
     */
    public function update_category($data, $id)
    {

        $data['description'] = nl2br($data['description']);
        $this->db->where('id', $id);
        $this->db->update('tblexpensescategories', $data);

        if ($this->db->affected_rows() > 0) {
            logActivity('Expense Category Updated [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete expense category from database, if used return array with key referenced
     */
    public function delete_category($id)
    {
        if (is_reference_in_table('category', 'tblexpenses', $id)) {
            return array(
                'referenced' => true
            );
        }
        $this->db->where('id', $id);
        $this->db->delete('tblexpensescategories');

        if ($this->db->affected_rows() > 0) {
            logActivity('Expense Category Deleted [' . $id . ']');
            return true;
        }
        return false;
    }
}
