<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     *  Leads conversions monthly report
     * @param   mixed $month  which month / chart
     * @return  array          chart data
     */
    public function leads_monthly_report($month)
    {
        $result = $this->db->query('select last_status_change from tblleads where MONTH(last_status_change) = ' . $month . ' AND status = 1 and lost = 0')->result_array();


        $month_dates = array();
        $data        = array();
        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, date('Y'));
            if (date('m', $time) == $month) {
                $month_dates[] = _d(date('Y-m-d', $time));
                $data[]        = 0;
            }
        }

        $chart = array(
            'labels' => $month_dates,
            'datasets' => array(
                array(
                    'label' => 'Leads',
                    'fillColor' => 'rgba(197, 61, 169, 0.5)',
                    'strokeColor' => '#c53da9',
                    'pointColor' => '#3A4656',
                    'pointStrokeColor' => '#fff',
                    'pointHighlightFill' => '#fff',
                    'pointHighlightStroke' => '#c53da9',
                    'data' => $data
                )
            )
        );
        foreach ($result as $lead) {
            $i = 0;
            foreach ($chart['labels'] as $date) {
                if (_d($lead['last_status_change']) == $date) {
                    $chart['datasets'][0]['data'][$i]++;
                }
                $i++;
            }
        }
        return $chart;
    }

    /**
     * Chart leads weeekly report
     * @return array  chart data
     */
    public function leads_this_week_report()
    {
        $this->db->where('CAST(last_status_change as DATE) >= "' . date('Y-m-d', strtotime('monday this week', strtotime('last sunday'))) . '" AND CAST(last_status_change as DATE) <= "' . date('Y-m-d', strtotime('sunday this week', strtotime('last sunday'))) . '" AND status = 1 and lost = 0');
        $weekly = $this->db->get('tblleads')->result_array();
        $colors = get_system_favourite_colors();

        $pies = array(
            array(
                'value' => 0,
                'color' => $colors[0],
                'highlight' => '#eef2f4',
                'label' => _l('wd_monday'),
            ),
            array(
                'value' => 0,
                'color' => $colors[1],
                'highlight' => '#eef2f4',
                'label' => _l('wd_tuesday'),
            ),
            array(
                'value' => 0,
                'color' => $colors[2],
                'highlight' => '#eef2f4',
                'label' => _l('wd_thursday'),
            ),
            array(
                'value' => 0,
                'color' => $colors[3],
                'highlight' => '#eef2f4',
                'label' => _l('wd_wednesday'),
            ),
            array(
                'value' => 0,
                'color' => $colors[4],
                'highlight' => '#eef2f4',
                'label' => _l('wd_friday'),
            ),
            array(
                'value' => 0,
                'color' => $colors[5],
                'highlight' => '#eef2f4',
                'label' => _l('wd_saturday'),
            ),
            array(
                'value' => 0,
                'color' => $colors[6],
                'highlight' => '#eef2f4',
                'label' => _l('wd_sunday'),
            )
        );

        foreach ($weekly as $weekly) {
            $lead_status_day = date('l', strtotime($weekly['last_status_change']));
            $i               = 0;
            foreach ($pies as $pie) {
                if ($lead_status_day == $pie['label']) {
                    $pies[$i]['value']++;
                }
                $i++;
            }
        }

        return $pies;
    }

    /**
     * Lead conversion by sources report / chart
     * @return arrray chart data
     */
    public function leads_sources_report()
    {
        $this->load->model('leads_model');
        $sources = $this->leads_model->get_source();

        $chart = array(
            'labels' => array(),
            'datasets' => array(
                array(
                    'label' => 'Leads',
                    'fillColor' => 'rgba(124, 179, 66, 0.5)',
                    'strokeColor' => '#7cb342',
                    'pointColor' => '#7cb342',
                    'pointStrokeColor' => '#fff',
                    'pointHighlightFill' => '#fff',
                    'pointHighlightStroke' => '#7cb342',
                    'data' => array()
                )
            )
        );

        foreach ($sources as $source) {
            array_push($chart['labels'], $source['name']);
            array_push($chart['datasets'][0]['data'], total_rows('tblleads', array(
                'source' => $source['id'],
                'status' => 1,
                'lost' => 0
            )));
        }
        return $chart;
    }

    public function report_by_customer_groups(){
        $months_report = $this->input->post('months_report');
        $this->load->model('clients_model');
        $groups = $this->clients_model->get_groups();

        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                $custom_date_select = 'tblinvoicepaymentrecords.date > DATE_SUB(now(), INTERVAL ' . $months_report . ' MONTH)';
            } else if ($months_report == 'custom') {
                $from_date = to_sql_date($this->input->post('report_from'));
                $to_date   = to_sql_date($this->input->post('report_to'));
                if ($from_date == $to_date) {
                    $custom_date_select = 'tblinvoicepaymentrecords.date ="' . $from_date . '"';
                } else {
                    $custom_date_select = '(tblinvoicepaymentrecords.date BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
                }
            }
            $this->db->where($custom_date_select);
        }

        $this->db->select('amount,tblinvoicepaymentrecords.date,tblinvoices.clientid,(SELECT GROUP_CONCAT(name) FROM tblcustomersgroups LEFT JOIN tblcustomergroups_in ON tblcustomergroups_in.groupid = tblcustomersgroups.id WHERE customer_id = tblinvoices.clientid) as groups');
        $this->db->from('tblinvoicepaymentrecords');
        $this->db->join('tblinvoices','tblinvoices.id = tblinvoicepaymentrecords.invoiceid');
        $this->db->where('tblinvoices.clientid IN (select customer_id FROM tblcustomergroups_in)');

        $by_currency = $this->input->post('report_currency');
        if ($by_currency) {
            $this->db->where('currency', $by_currency);
        }

        $payments = $this->db->get()->result_array();

        $data           = array();
        $data['temp']   = array();
        $data['total']  = array();
        $data['labels'] = array();

        foreach ($groups as $group) {
            if (!isset($data['groups'][$group['name']])) {
                $data['groups'][$group['name']] = $group['name'];
            }
        }
        foreach ($data['groups'] as $group) {
            foreach ($payments as $payment) {
                $p_groups = explode(',',$payment['groups']);
                foreach($p_groups as $p_group){
                    if($p_group == $group){
                       $data['temp'][$group][] = $payment['amount'];
                   }
               }
           }
           array_push($data['labels'], $group);
           if(isset($data['temp'][$group])){
                $data['total'][] = array_sum($data['temp'][$group]);
           }
      }

        $chart = array(
            'labels' => $data['labels'],
            'datasets' => array(
                array(
                    'label' => 'Groups',
                    'fillColor' => 'rgba(197, 61, 169, 0.5)',
                    'strokeColor' => '#c53da9',
                    'pointColor' => '#3A4656',
                    'pointStrokeColor' => '#fff',
                    'pointHighlightFill' => '#fff',
                    'pointHighlightStroke' => '#c53da9',
                    'data' => $data['total']
                )
            )
        );

        return $chart;
    }
    /**
     * Total income report / chart
     * @return array chart data
     */
    public function total_income_report()
    {

        $months_report = $this->input->post('months_report');

        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                $custom_date_select = 'tblinvoicepaymentrecords.date > DATE_SUB(now(), INTERVAL ' . $months_report . ' MONTH)';
            } else if ($months_report == 'custom') {
                $from_date = to_sql_date($this->input->post('report_from'));
                $to_date   = to_sql_date($this->input->post('report_to'));
                if ($from_date == $to_date) {
                    $custom_date_select = 'tblinvoicepaymentrecords.date ="' . $from_date . '"';
                } else {
                    $custom_date_select = '(tblinvoicepaymentrecords.date BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
                }
            }
            $this->db->where($custom_date_select);
        }

        $this->db->select('amount,tblinvoicepaymentrecords.date');
        $this->db->from('tblinvoicepaymentrecords');
        $this->db->join('tblinvoices','tblinvoices.id = tblinvoicepaymentrecords.invoiceid');

        $by_currency = $this->input->post('report_currency');
        if ($by_currency) {
            $this->db->where('currency', $by_currency);
        }

        $payments = $this->db->get()->result_array();

        $data           = array();
        $data['months'] = array();
        $data['temp']   = array();
        $data['total']  = array();
        $data['labels'] = array();



        foreach ($payments as $payment) {
            $month   = date('m', strtotime($payment['date']));
            $dateObj = DateTime::createFromFormat('!m', $month);
            $month   = $dateObj->format('F');
            if (!isset($data['months'][$month])) {
                $data['months'][$month] = $month;
            }
        }

        usort($data['months'], function($a, $b)
        {
            $month1 = date_parse($a);
            $month2 = date_parse($b);
            return $month1["month"] - $month2["month"];
        });

        foreach ($data['months'] as $month) {
            foreach ($payments as $payment) {
                $_month  = date('m', strtotime($payment['date']));
                $dateObj = DateTime::createFromFormat('!m', $_month);
                $_month  = $dateObj->format('F');
                if ($month == $_month) {
                    $data['temp'][$month][] = $payment['amount'];
                }
            }
            array_push($data['labels'], $month);
            $data['total'][] = array_sum($data['temp'][$month]);
        }

        $chart = array(
            'labels' => $data['labels'],
            'datasets' => array(
                array(
                    'label' => 'Report',
                    'fillColor' => 'rgba(40,184,218,1)',
                    'strokeColor' => "rgba(151,187,205,0.8)",
                    'highlightFill' => "rgba(151,187,205,0.75)",
                    'highlightStroke' => "rgba(151,187,205,1)",
                    'data' => $data['total']
                )
            )
        );

        return $chart;
    }

    public function get_distinct_expense_years()
    {
        return $this->db->query('SELECT DISTINCT(YEAR(date)) as year FROM tblexpenses')->result_array();
    }

    public function get_expenses_vs_income_report()
    {
        $months_labels  = array();
        $total_expenses = array();
        $total_income   = array();

        // Months fix
        $i = 0;
        for ($m = 1; $m <= 12; $m++) {

            array_push($months_labels, _l(date('F', mktime(0, 0, 0, $m,1))));

            $this->db->select('amount,taxrate,tblexpenses.tax')->from('tblexpenses')->join('tbltaxes', 'tbltaxes.id = tblexpenses.tax', 'left')->where('MONTH(date)', $m)->where('YEAR(date)', date('Y'))->where('billable',0);
            $expenses = $this->db->get()->result_array();
            if (!isset($total_expenses[$i])) {
                $total_expenses[$i] = array();
            }

            if (count($expenses) > 0) {
                foreach ($expenses as $expense) {
                    $total = $expense['amount'];
                    // Check if tax is applied
                    if ($expense['tax'] != 0) {
                        $total += ($total / 100 * $expense['taxrate']);
                    }
                    $total_expenses[$i][] = $total;
                }
            } else {
                $total_expenses[$i][] = 0;
            }

            $total_expenses[$i] = array_sum($total_expenses[$i]);

            // Calculate the income
            $this->db->select('amount');
            $this->db->from('tblinvoicepaymentrecords');
            $this->db->join('tblinvoices', 'tblinvoices.id = tblinvoicepaymentrecords.invoiceid');
            $this->db->where('MONTH(tblinvoicepaymentrecords.date)', $m);
            $payments = $this->db->get()->result_array();

            if (!isset($total_income[$m])) {
                $total_income[$i] = array();
            }
            if (count($payments) > 0) {
                foreach ($payments as $payment) {
                    $total_income[$i][] = $payment['amount'];
                }
            } else {
                $total_income[$i][] = 0;
            }

            $total_income[$i] = array_sum($total_income[$i]);
             $i++;
        }


        $chart = array(
            'labels' => $months_labels,
            'datasets' => array(
                array(
                    'label' => 'Income',
                    'fillColor' => 'rgba(40,184,218,1)',
                    'strokeColor' => "rgba(151,187,205,0.8)",
                    'highlightFill' => "rgba(151,187,205,0.75)",
                    'highlightStroke' => "rgba(151,187,205,1)",
                    'data' => $total_income
                ),
                array(
                    'label' => 'Expenses',
                    'fillColor' => 'rgba(252,45,66,1)',
                    'strokeColor' => "rgba(220,24,44,1)",
                    'highlightFill' => "rgba(220,24,44,1)",
                    'highlightStroke' => "rgba(220,24,44,1)",
                    'data' => $total_expenses
                )
            )
        );

        return $chart;
    }
}
