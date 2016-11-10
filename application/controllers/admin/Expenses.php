<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expenses extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('expenses_model');
    }

    public function index($id = '', $clientid = '')
    {
        $this->list_expenses($id, $clientid);
    }

    public function list_expenses($id = '', $clientid = '')
    {

        $has_permission = has_permission('manageExpenses');

        if (!$has_permission && !$this->input->is_ajax_request()) {
            access_denied('manageExpenses');
        }

        if ($this->input->is_ajax_request()) {
            // From client profile
            if (is_numeric($clientid)) {
                if (!$has_permission) {
                    echo json_encode(json_decode('{"draw":1,"iTotalRecords":"0","iTotalDisplayRecords":"0","aaData":[]}'));
                    die;
                }
            }

            $aColumns = array(
                'category',
                'amount',
                'date',
                'clientid',
                'reference_no',
                'paymentmode'
            );

            $join = array(
                'LEFT JOIN tblclients ON tblclients.userid = tblexpenses.clientid',
                'LEFT JOIN tblexpensescategories ON tblexpensescategories.id = tblexpenses.category'
            );

            $custom_fields = get_custom_fields('expenses',array('show_on_table'=>1));

            $i = 0;
            foreach($custom_fields as $field){
                    array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
                    array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblexpenses.id = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
                    $i++;
            }


            $where = array();

            if (is_numeric($clientid)) {
                $where = array(
                    'AND clientid=' . $clientid
                );
            }

            if ($this->input->post('custom_view')) {
                $custom_view = $this->input->post('custom_view');
                if ($custom_view == 'invoiced') {
                    array_push($where, 'AND invoiceid IS NOT NULL');
                } else if ($custom_view == 'billable') {
                    array_push($where, 'AND billable = 1');
                } else if ($custom_view == 'non-billable') {
                    array_push($where, 'AND billable = 0');
                } else if ($custom_view == 'unbilled') {
                    array_push($where, 'AND (SELECT 1 from tblinvoices WHERE tblinvoices.id = tblexpenses.invoiceid AND status != 2)');
                } else if ($custom_view == 'recurring') {
                    array_push($where, 'AND recurring = 1');
                } else {
                    // is expense category
                    if (is_numeric($custom_view)) {
                        array_push($where, 'AND category = ' . $custom_view);
                    }
                }
            }

            $sIndexColumn = "id";
            $sTable       = 'tblexpenses';
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'name',
                'tblexpenses.id',
                'company',
                'firstname',
                'lastname',
                'billable',
                'invoiceid'
            ));
            $output       = $result['output'];
            $rResult      = $result['rResult'];
            $this->load->model('currencies_model');
            $this->load->model('payment_modes_model');
            $base_currency_symbol = $this->currencies_model->get_base_currency()->symbol;

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {

                    if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
                        $_data = $aRow[ strafter($aColumns[$i],'as ')];
                    } else {
                        $_data = $aRow[ $aColumns[$i] ];
                    }

                    $company = $aRow['company'];
                    if ($company != '') {
                        $company .= '<small class="text-muted"> ' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</small>';
                    } else {
                        $company = $aRow['firstname'] . ' ' . $aRow['lastname'];
                    }

                    if ($aColumns[$i] == 'category') {
                        if (is_numeric($clientid)) {
                            $_data = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '">' . $aRow['name'] . '</a>';
                        } else {
                            $_data = '<a href="#" onclick="init_expense(' . $aRow['id'] . ');return false;">' . $aRow['name'] . '</a>';
                        }
                        if($aRow['billable'] == 1){
                            if($aRow['invoiceid'] == NULL){
                                $_data .= '<p class="text-danger">'._l('expense_list_unbilled').'</p>';
                            } else {
                                if(total_rows('tblinvoices',array('id'=>$aRow['invoiceid'],'status'=>2)) > 0){
                             $_data .= '<br /><p class="text-success">'._l('expense_list_billed').'</p>';
                                } else {
                                     $_data .= '<p class="text-success">'._l('expense_list_invoice').'</p>';
                                }
                            }
                        }
                    } else if ($aColumns[$i] == 'amount') {
                        $_data = format_money($_data, $base_currency_symbol);
                    } else if ($aColumns[$i] == 'clientid') {
                        $_data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $company . '</a>';
                    } else if ($aColumns[$i] == 'paymentmode') {
                        $_data = '';
                        if ($aRow['paymentmode'] != 0) {
                            $_data = $this->payment_modes_model->get($aRow['paymentmode'])->name;
                        }
                    }
                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }


        $data['expenseid'] = '';
        if (is_numeric($id)) {
            $data['expenseid'] = $id;
        }

        $data['total_unbilled'] = $this->db->query('SELECT count(*) as num_rows FROM tblexpenses WHERE (SELECT 1 from tblinvoices WHERE tblinvoices.id = tblexpenses.invoiceid AND status != 2)')->row()->num_rows;
        $data['categories']     = $this->expenses_model->get_category();
        $data['bodyclass']      = 'small-table';
        $data['title']          = _l('expenses');
        $this->load->view('admin/expenses/manage', $data);
    }
    public function expense($id = '')
    {

        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }

        if ($this->input->post()) {
            if ($id == '') {
                $id = $this->expenses_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('expense')));
                    echo json_encode(array(
                        'url' => admin_url('expenses/list_expenses/' . $id),
                        'expenseid' => $id
                    ));
                    die;
                }
                echo json_encode(array(
                    'url' => admin_url('expenses/expense')
                ));
                die;
            } else {
                $success = $this->expenses_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('expense')));
                }

                echo json_encode(array(
                    'url' => admin_url('expenses/list_expenses/' . $id),
                    'expenseid' => $id
                ));
                die;
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('expense_lowercase'));
        } else {
            $data['expense'] = $this->expenses_model->get($id);
            $title           = _l('edit', _l('expense_lowercase'));
        }

        $this->load->model('clients_model');
        $this->load->model('taxes_model');
        $this->load->model('payment_modes_model');
        $this->load->model('currencies_model');

        $data['customers']     = $this->clients_model->get();
        $data['taxes']         = $this->taxes_model->get();
        $data['categories']    = $this->expenses_model->get_category();
        $data['payment_modes'] = $this->payment_modes_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $data['title'] = $title;
        $this->load->view('admin/expenses/expense', $data);
    }

    public function delete($id)
    {

        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }

        if (!$id) {
            redirect(admin_url('expenses/list_expenses'));
        }

        $response = $this->expenses_model->delete($id);

        if ($response === true) {
            set_alert('success', _l('deleted', _l('expense')));
        } else {
            if (is_array($response) && $response['invoiced'] == true) {
                set_alert('warning', _l('expense_invoice_delete_not_allowed'));
            } else {
                set_alert('warning', _l('problem_deleting', _l('expense_lowercase')));
            }

        }
        redirect(admin_url('expenses/list_expenses'));
    }

    public function copy($id)
    {

        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }

        $new_expense_id = $this->expenses_model->copy($id);

        if ($new_expense_id) {
            set_alert('success', _l('expense_copy_success'));
            redirect(admin_url('expenses/expense/' . $new_expense_id));
        } else {
            set_alert('warning', _l('expense_copy_fail'));
        }

        redirect(admin_url('expenses/list_expenses/' . $id));
    }

    public function convert_to_invoice($id)
    {

        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }
        if (!$id) {
            redirect(admin_url('expenses/list_expenses'));
        }

        $invoiceid = $this->expenses_model->convert_to_invoice($id);

        if ($invoiceid) {
            set_alert('success', _l('expense_converted_to_invoice'));
            redirect(admin_url('invoices/invoice/' . $invoiceid));
        } else {
            set_alert('warning', _l('expense_converted_to_invoice_fail'));
        }

        redirect(admin_url('expenses/list_expenses/' . $id));
    }

    public function get_expense_data_ajax($id)
    {
        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }

        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $expense               = $this->expenses_model->get($id);
        $data['expense']       = $expense;

        if ($expense->billable == 1) {
            if ($expense->invoiceid !== NULL) {
                $this->load->model('invoices_model');
                $data['invoice'] = $this->invoices_model->get($expense->invoiceid);
            }
        }

        $this->load->view('admin/expenses/expense_preview_template', $data);
    }

    public function categories()
    {

        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }

        if ($this->input->is_ajax_request()) {
            $aColumns     = array(
                'name',
                'description'
            );
            $sIndexColumn = "id";
            $sTable       = 'tblexpensescategories';

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), array(), array(
                'id'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name') {
                        $_data = '<a href="' . admin_url('expenses/category/' . $aRow['id']) . '">' . $_data . '</a>';
                    }

                    $row[] = $_data;
                }

                $options = icon_btn('#', 'pencil-square-o','btn-default',array('onclick'=>'edit_category(this,'.$aRow['id'].'); return false;','data-name'=>$aRow['name'],'data-description'=>clear_textarea_breaks($aRow['description'])));
                $row[]   = $options .= icon_btn('admin/expenses/delete_category/' . $aRow['id'], 'remove', 'btn-danger');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
        $data['title'] = _l('expense_categories');
        $this->load->view('admin/expenses/manage_categories', $data);
    }
    public function category()
    {

        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }

        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->expenses_model->add_category($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('expense_category')));
                }
            } else {
                $data = $this->input->post();
                $id = $data['id'];
                unset($data['id']);
                $success = $this->expenses_model->update_category($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('expense_category')));
                }
            }
        }


    }

    public function delete_category($id)
    {

        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }

        if (!$id) {
            redirect(admin_url('expenses/categories'));
        }

        $response = $this->expenses_model->delete_category($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('expense_category_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('expense_category')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('expense_category_lowercase')));
        }

        redirect(admin_url('expenses/categories'));
    }

    public function add_expense_attachment($id)
    {

        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }
        handle_expense_attachments($id);
    }

    public function delete_expense_attachment($id, $preview = '')
    {

        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }
        $success = $this->expenses_model->delete_expense_attachment($id);
        if ($success) {
            set_alert('success', _l('deleted', _l('expense_receipt')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('expense_receipt_lowercase')));
        }

        if ($preview == '') {
            redirect(admin_url('expenses/expense/' . $id));
        } else {
            redirect(admin_url('expenses/list_expenses/' . $id));
        }
    }

}
