<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        if (!has_permission('watchReports')) {
            access_denied('watchReports');
        }
        $this->load->model('reports_model');
    }

    /* No access on this url */
    public function index()
    {
        redirect(site_url('admin'));
    }

    /* See knowledge base article reports*/
    public function knowledge_base_articles()
    {
        if (get_option('use_knowledge_base') == 0) {
            redirect(site_url('admin'));
        }

        $this->load->model('knowledge_base_model');
        $data['groups'] = $this->knowledge_base_model->get_kbg();
        $data['title']  = _l('kb_reports');
        $this->load->view('admin/reports/knowledge_base_articles', $data);
    }

    /* Rerport leads conversions */
    public function leads()
    {
        $this->load->model('leads_model');
        $data['statuses'] = $this->leads_model->get_status();
        $this->load->view('admin/reports/leads', $data);
    }

    /* Sales reportts */
    public function sales()
    {
        if (is_using_multiple_currencies()) {
            $this->load->model('currencies_model');
            $data['currencies'] = $this->currencies_model->get();
        }

        $data['title'] = _l('sales_reports');
        $this->load->view('admin/reports/sales', $data);
    }

    /* Customer report */
    public function customers_report()
    {
        if ($this->input->is_ajax_request()) {
        	$this->load->model('currencies_model');
            $months_report = $this->input->post('report_months');
            $select        = array(
                'company',
                '(SELECT COUNT(clientid) FROM tblinvoices WHERE tblinvoices.clientid = tblclients.userid)',
                '(SELECT SUM(subtotal) FROM tblinvoices WHERE tblinvoices.clientid = tblclients.userid)',
                '(SELECT SUM(total) FROM tblinvoices WHERE tblinvoices.clientid = tblclients.userid)'
            );

            if ($months_report != '') {
                $custom_date_select = '';
                if (is_numeric($months_report)) {
                    $custom_date_select = 'AND date > DATE_SUB(now(), INTERVAL ' . $months_report . ' MONTH)';
                } else if ($months_report == 'custom') {
                    $from_date = to_sql_date($this->input->post('report_from'));
                    $to_date   = to_sql_date($this->input->post('report_to'));

                    if ($from_date == $to_date) {
                        $custom_date_select = 'AND date ="' . $from_date . '"';
                    } else {
                        $custom_date_select = 'AND (date BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
                    }
                }
                if ($custom_date_select != '') {
                    $i = 0;
                    foreach ($select as $_select) {
                        if ($i !== 0) {
                            $_temp = substr($_select, 0, -1);
                            $_temp .= ' ' . $custom_date_select . ')';
                            $select[$i] = $_temp;
                        }
                        $i++;
                    }
                }
            }

            $by_currency = $this->input->post('report_currency');
            $currency = $this->currencies_model->get_base_currency();
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);
            if($by_currency){
				     $i = 0;
                    foreach ($select as $_select) {
                        if ($i !== 0) {
                            $_temp = substr($_select, 0, -1);
                            $_temp .= ' AND currency =' . $by_currency . ')';
                            $select[$i] = $_temp;
                        }
                        $i++;
                    }

            $currency = $this->currencies_model->get($by_currency);
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);
			}


            $aColumns     = $select;
            $sIndexColumn = "userid";
            $sTable       = 'tblclients';

            $where = array();
            $i = 0;
            foreach($select as $_select){
            	if($i > 0){
            		if($i == 1){
            		 $where[] = 'WHERE ' . $_select . '' .' !=0';
            		} else {
            		 $where[] = 'AND ' . $_select . '' .' !=0';
            		}
            	}
            	$i++;
            }

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable,array(), $where, array(
                'firstname',
                'lastname',
                'userid'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            $x = 0;
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {

                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'company') {
                        $company = $aRow['company'];
                        if ($company != '') {
                            $company .= '<small class="text-muted"> ' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</small>';
                        } else {
                            $company = $aRow['firstname'] . ' ' . $aRow['lastname'];
                        }

                        $_data = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '">' . $company . '</a>';
                    } else if ($aColumns[$i] == $select[2] || $aColumns[$i] == $select[3]) {

                        if ($_data == null) {
                            $_data = 0;
                        }

                        $_data = format_money($_data, $currency_symbol);
                    }
                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
                $x++;
            }

            echo json_encode($output);
            die();
        }
    }

    public function expenses($current_year = ''){
        $data['current_year'] = $current_year;
        if($current_year == ''){
            $data['current_year'] = date('Y');
        }

        $data['expense_years'] = $this->reports_model->get_distinct_expense_years();
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $this->load->model('expenses_model');
        $data['categories'] = $this->expenses_model->get_category();
        $data['title'] = 'Expenses Report';
        $this->load->view('admin/reports/expenses',$data);
    }
     public function expenses_vs_income(){
        $data['chart_expenses_vs_income_values'] = json_encode($this->reports_model->get_expenses_vs_income_report());
        $data['title'] = 'Expense vs Income';
        $this->load->view('admin/reports/expenses_vs_income',$data);
    }
    /* Total income report / ajax chart*/
    public function total_income_report()
    {
        echo json_encode($this->reports_model->total_income_report());
    }
    public function report_by_customer_groups(){
         echo json_encode($this->reports_model->report_by_customer_groups());
    }
    /* Leads conversion monthly report / ajax chart*/
    public function leads_monthly_report($month)
    {
        echo json_encode($this->reports_model->leads_monthly_report($month));
    }
    /* Leads conversion current week / ajax chart*/
    public function leads_this_week_report()
    {
        echo json_encode($this->reports_model->leads_this_week_report());
    }
    /* Leads conversion by source / ajax chart*/
    public function leads_sources_report()
    {
        echo json_encode($this->reports_model->leads_sources_report());
    }
}
