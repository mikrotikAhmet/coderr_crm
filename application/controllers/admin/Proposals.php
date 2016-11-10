<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proposals extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('proposals_model');
        $this->load->model('currencies_model');


    }

    public function index($proposal_id = '')
    {
         if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        $this->list_proposals($proposal_id);
    }

    public function list_proposals($proposal_id = '')
    {
         if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        if ($this->input->is_ajax_request()) {
            $aColumns     = array(
                'subject',
                'proposal_to',
                'total',
                'date',
                'open_till',
                'datecreated',
                'status',
                );
            $sIndexColumn = "id";
            $sTable       = 'tblproposals';

            $where = array();
            if($this->input->post('custom_view')){
                $custom_view = $this->input->post('custom_view');
                if(is_numeric($custom_view)){
                    array_push($where,'AND status='.$custom_view);
                } else if($custom_view == 'leads_related'){
                    array_push($where,'AND rel_type="lead"');
                }else if($custom_view == 'customers_related'){
                    array_push($where,'AND rel_type="customer"');
                }else if($custom_view == 'not_related'){
                    array_push($where,'AND rel_type IS NULL');
                }

            }

            $join = array();
            $custom_fields = get_custom_fields('proposal',array('show_on_table'=>1));

            $i = 0;
            foreach($custom_fields as $field){
                array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
                array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblproposals.id = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
                $i++;
            }

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblproposals.id',
                'currency',
                'rel_id',
                'rel_type',
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
                    if ($aColumns[$i] == 'subject') {
                        $_data = '<a href="#" onclick="init_proposal(' . $aRow['id'] . '); return false;">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'status') {
                        $_data = format_proposal_status($aRow['status']);
                    } else if($aColumns[$i] == 'total'){
                        if($aRow['currency'] != 0){
                            $_data = format_money($_data,$this->currencies_model->get_currency_symbol($aRow['currency']));
                        } else {
                            $_data = format_money($_data,$this->currencies_model->get_base_currency($aRow['currency'])->symbol);
                        }

                    } else if($aColumns[$i] == 'open_till' || $aColumns[$i] == 'datecreated'){
                        $_data = _d($_data);
                    } else if($aColumns[$i] == 'proposal_to'){
                         if(!empty($_data)){
                          if(!empty($aRow['rel_id']) && $aRow['rel_id'] != 0){
                            if($aRow['rel_type'] == 'lead'){
                              $_data = '<a href="'.admin_url('leads/lead/'.$aRow['rel_id']).'" target="_blank" data-toggle="tooltip" data-title="'._l('lead').'">'.$_data.'</a>';
                            } else if($aRow['rel_type'] == 'customer'){
                              $_data = '<a href="'.admin_url('clients/client/'.$aRow['rel_id']).'" target="_blank" data-toggle="tooltip" data-title="'._l('client').'">'.$_data.'</a>';
                            }
                          }
                        }
                    }
                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['proposal_id'] = '';
        if (is_numeric($proposal_id)) {
            $data['proposal_id'] = $proposal_id;
        }

        $data['bodyclass'] = 'small-table';
        $data['title']     = _l('proposals');
        $this->load->view('admin/proposals/manage', $data);
    }

    public function proposal_relations($rel_id,$rel_type)
    {
        if ($this->input->is_ajax_request()) {

           if(!has_permission('manageSales')){
                echo json_encode(json_decode('{"draw":1,"iTotalRecords":"0","iTotalDisplayRecords":"0","aaData":[]}'));
                die;
           }
            $aColumns     = array(
                'subject',
                'total',
                'open_till',
                'datecreated',
                'status'
            );
            $sIndexColumn = "id";
            $sTable       = 'tblproposals';

            $custom_fields = get_custom_fields('proposal',array('show_on_table'=>1));
            $join = array();
             $i = 0;
            foreach($custom_fields as $field){
                array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
                array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblproposals.id = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
                $i++;
            }

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, array('AND rel_id = '.$rel_id. ' AND rel_type = "'.$rel_type.'"'), array(
                'tblproposals.id',
                'currency',
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
                    if ($aColumns[$i] == 'subject') {
                        $_data = '<a href="'.admin_url('proposals/list_proposals/'.$aRow['id']).'">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'status') {
                        $_data = format_proposal_status($aRow['status']);
                    } else if($aColumns[$i] == 'open_till' || $aColumns[$i] == 'datecreated'){
                        $_data = _d($_data);
                    } else if($aColumns[$i] == 'total'){
                        if($aRow['currency'] != 0){
                            $_data = format_money($_data,$this->currencies_model->get_currency_symbol($aRow['currency']));
                        } else {
                            $_data = format_money($_data,$this->currencies_model->get_base_currency($aRow['currency'])->symbol);
                        }

                    }
                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    public function proposal($id = '')
    {
         if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        if ($this->input->post()) {
            if ($id == '') {
                $id = $this->proposals_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('proposal')));
                    redirect(admin_url('proposals/list_proposals/' . $id));
                }
            } else {
                $success = $this->proposals_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('proposal')));
                }
                redirect(admin_url('proposals/list_proposals/' . $id));
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('proposal_lowercase'));
        } else {
            $data['proposal'] = $this->proposals_model->get($id);
            $title            = _l('edit', _l('proposal_lowercase'));
        }

        $this->load->model('staff_model');
        $data['staff'] = $this->staff_model->get('', 1);

        $data['currencies'] = $this->currencies_model->get();
        $data['title']      = $title;
        $this->load->view('admin/proposals/proposal', $data);
    }

    public function pdf($id)
    {
         if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }

        if (!$id) {
            redirect(admin_url('proposals/list_proposals'));
        }

        $proposal = $this->proposals_model->get($id);

        $pdf = proposal_pdf($proposal);
        $pdf->Output(slug_it($proposal->subject) . '.pdf', 'D');
    }

    public function get_proposal_data_ajax($id)
    {

         if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }

        if (!$id) {
            die('No proposal found');
        }

        $proposal = $this->proposals_model->get($id);

        if (!$proposal) {
            echo 'Proposal Not Found';
            die;
        }

        $this->load->model('emails_model');
        $data['template'] = $this->emails_model->parse_template('proposal-send-to-customer', false, false, false, false, false, false, false, $id);


        $data['proposal'] = $proposal;
        $this->load->view('admin/proposals/proposals_preview_template', $data);
    }

    public function convert_to_estimate($id){
         if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        if($this->input->post()){

            $this->load->model('estimates_model');
            $estimate_id = $this->estimates_model->add($this->input->post());
            if($estimate_id){
                set_alert('success',_l('proposal_converted_to_estimate_success'));

                $this->db->where('id',$id);
                $this->db->update('tblproposals',array('estimate_id'=>$estimate_id,'status'=>3));

                logActivity('Proposal Converted to Estimate [EstimateID: '.$estimate_id.', ProposalID: '.$id.']');

                redirect(admin_url('estimates/estimate/'.$estimate_id));
            } else {
                set_alert('danger',_l('proposal_converted_to_estimate_fail'));
            }
            redirect(admin_url('proposals/list_proposals/'.$id));
        }
    }

     public function convert_to_invoice($id){
         if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        if($this->input->post()){
            $this->load->model('invoices_model');
            $invoice_id = $this->invoices_model->add($this->input->post());
            if($invoice_id){
                set_alert('success',_l('proposal_converted_to_invoice_success'));

                $this->db->where('id',$id);
                $this->db->update('tblproposals',array('invoice_id'=>$invoice_id,'status'=>3));

                logActivity('Proposal Converted to Invoice [InvoiceID: '.$invoice_id.', ProposalID: '.$id.']');

                redirect(admin_url('invoices/invoice/'.$invoice_id));
            } else {
                set_alert('danger',_l('proposal_converted_to_invoice_fail'));
            }
            redirect(admin_url('proposals/list_proposals/'.$id));
        }
    }

    public function get_invoice_convert_data($id){
         if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get();

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();

        $data['currencies'] = $this->currencies_model->get();

        $this->load->model('invoice_items_model');
        $data['items'] = $this->invoice_items_model->get();

        $this->load->model('clients_model');
        $data['clients'] = $this->clients_model->get();

        $this->load->model('staff_model');
        $data['staff'] = $this->staff_model->get('',1);

        $data['proposal'] = $this->proposals_model->get($id);

        $data['add_items'] = $this->_parse_items($data['proposal']);
        $this->load->view('admin/proposals/invoice_convert_template',$data);
    }

    public function get_estimate_convert_data($id){
         if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();

        $data['currencies'] = $this->currencies_model->get();

        $this->load->model('invoice_items_model');
        $data['items'] = $this->invoice_items_model->get();

        $this->load->model('clients_model');
        $data['clients'] = $this->clients_model->get();

        $this->load->model('staff_model');
        $data['staff'] = $this->staff_model->get('',1);


        $data['proposal'] = $this->proposals_model->get($id);

        $data['add_items'] = $this->_parse_items($data['proposal']);

        $this->load->view('admin/proposals/estimate_convert_template',$data);
    }

    private function _parse_items($proposal){
        $items = array();
        $this->load->helper('simple_html_dom');
        $html = str_get_html($proposal->content);
        if($html){
          foreach($html->find('table[class=proposal-items] tbody > tr') as $tr){
            // The parent tag name
            $parentTag = $tr->parent()->tag;
            $_items = array();
            // Make sure the parent tag is 'tbody'
            if( $parentTag == 'tbody' ){
              $i = 0;
              foreach ($tr->children() as $cell) {
                if($i > 4){
                  break;
                }
                if($i != 4){
                 if($i == 0){
                    $name = 'description';
                 } else if($i == 1){
                    $name = 'long_description';
                 } else if($i == 2){
                    $name = 'qty';
                 } else if ($i == 3){
                    $name = 'rate';
                 }
                 $_items[$name] = trim($cell->plaintext);
               } else {
                $tax_id = trim($cell->{'data-taxid'});
                if(empty($tax_id)){
                  // user changed the tax after inserting the item directly in the table
                  // lets assume that this tax is in database and add the id if found
                  $tax = trim($cell->plaintext);
                  $tax = str_replace('%','',$tax);
                  $this->db->where('taxrate',$tax);
                  $_tax = $this->db->get('tbltaxes')->row();
                  if($_tax){
                    $tax = $_tax->id;
                  } else {
                    $tax = 0;
                  }
                  $_items['taxid'] = $tax;
                } else {
                  $_items['taxid'] = $tax_id;
                }
              }
              $i++;
            }
            $_items['id'] = 0;

            $items[] = $_items;
          }
        }
        }
        return $items;
    }

    /* Send proposal to email */
    public function send_to_email($id)
    {
         if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        $success = $this->proposals_model->sent_proposal_to_email($id, 'proposal-send-to-customer', $this->input->post('attach_pdf'));
        if ($success) {
            set_alert('success', _l('proposal_sent_to_email_success'));
        } else {
            set_alert('danger', _l('proposal_sent_to_email_fail'));
        }
        redirect(admin_url('proposals/list_proposals/' . $id));
    }

    public function copy($id)
    {
         if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }

        $new_id = $this->proposals_model->copy($id);

        if ($new_id) {
            set_alert('success', _l('proposal_copy_success'));
            redirect(admin_url('proposals/proposal/' . $new_id));
        } else {
            set_alert('success', _l('proposal_copy_fail'));
        }

        redirect(admin_url('proposals/list_proposals/' . $id));
    }

    public function mark_action_status($status, $id)
    {
         if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        $success = $this->proposals_model->mark_action_status($status, $id);
        if ($success) {
            set_alert('success', _l('proposal_status_changed_success'));
        } else {
            set_alert('danger', _l('proposal_status_changed_fail'));
        }
        redirect(admin_url('proposals/list_proposals/' . $id));
    }

    public function delete($id)
    {
         if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        $response = $this->proposals_model->delete($id);

        if ($response == true) {
            set_alert('success', _l('deleted', _l('proposal')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('proposal_lowercase')));
        }

        redirect(admin_url('proposals/list_proposals'));
    }

    public function get_proposal_items_template()
    {    if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $this->load->view('admin/proposals/add_items_template', $data);
    }
    public function get_relation_data_values($rel_id, $rel_type)
    {
         if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        echo json_encode($this->proposals_model->get_relation_data_values($rel_id, $rel_type));
    }
    public function add_proposal_comment()
    {
        echo json_encode(array(
            'success' => $this->proposals_model->add_comment($this->input->post())
        ));
    }
    public function get_proposal_comments($id)
    {
        $data['comments'] = $this->proposals_model->get_comments($id);
        $this->load->view('admin/proposals/comments_template', $data);
    }
    public function remove_comment($id)
    {
         if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        echo json_encode(array(
            'success' => $this->proposals_model->remove_comment($id)
        ));
    }
    public function save_proposal_data()
    {
         if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        if ($this->input->post('main-content')) {
            $this->db->where('id', $this->input->post('proposal_id'));
            $this->db->update('tblproposals', array(
                'content' => $this->input->post('main-content')
            ));
        }
    }
}
