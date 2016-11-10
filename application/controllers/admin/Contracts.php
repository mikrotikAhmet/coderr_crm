<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contracts extends Admin_controller {

	function __construct(){
		parent::__construct();
		$this->load->model('contracts_model');
	}
	/* List all contracts */
	public function index($clientid = false)
	{
		$has_permission = has_permission('manageContracts');

		if(!$has_permission && !$this->input->is_ajax_request()){
			access_denied('manageContracts');
		}

		if($this->input->is_ajax_request()){

			 // From client profile
			if(is_numeric($clientid)){
				if(!has_permission('manageContracts')){
					echo json_encode(json_decode('{"draw":1,"iTotalRecords":"0","iTotalDisplayRecords":"0","aaData":[]}'));
					die;
				}
			}
			$aColumns = array('subject', 'client', 'contract_type', 'datestart','dateend');
			$sIndexColumn = "id";
			$sTable = 'tblcontracts';
			$additionalSelect = array('firstname','lastname','company','tblcontracts.id','tblcontracttypes.name','trash');
			$join = array(
				'LEFT JOIN tblclients ON tblclients.userid = tblcontracts.client',
				'LEFT JOIN tblcontracttypes ON tblcontracttypes.id = tblcontracts.contract_type'
				);

			$custom_fields = get_custom_fields('contracts',array('show_on_table'=>1));

			$i = 0;
			foreach($custom_fields as $field){
				array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
				array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblcontracts.id = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
				$i++;
			}

			$where = array();
			if(is_numeric($clientid)){
				$where = array('WHERE client='.$clientid);
			}

			if(!$this->input->post('custom_view')){
				array_push($where,'AND trash = 0');
			} else {
				$custom_view = $this->input->post('custom_view');
				if($custom_view == 'trash'){
					array_push($where,'AND trash = 1');
				} else if($custom_view == 'expired') {
					array_push($where,'AND dateend IS NOT NULL AND dateend >"'.date('Y-m-d').'"');
				} else if($custom_view == 'without_dateend'){
					array_push($where,'AND dateend IS NULL');
				} else {
					// is contract type (id)
					if(is_numeric($custom_view)){
						array_push($where,'AND contract_type ='. $custom_view);
					}

					// else is ALL contracts we dont need conditions
				}
			}

			$result = data_tables_init($aColumns,$sIndexColumn,$sTable,$join,$where,$additionalSelect);

			$output = $result['output'];
			$rResult = $result['rResult'];

			foreach ( $rResult as $aRow )
			{
				$row = array();
				for ( $i=0 ; $i<count($aColumns) ; $i++ )
				{
					if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
						$_data = $aRow[ strafter($aColumns[$i],'as ')];
					} else {
						$_data = $aRow[ $aColumns[$i] ];
					}
					if($aColumns[$i] == 'client'){
						$client_name = $aRow['firstname'] . ' ' . $aRow['lastname'];

						if($aRow['company'] != NULL){
							$client_name .= ' ('.$aRow['company'].')';
						}

						$_data = '<a href="'.admin_url('clients/client/'.$aRow['client']).'">'. $client_name . '</a>';
					} else if($aColumns[$i] == 'dateend' || $aColumns[$i] == 'datestart'){
						$_data = _d($_data);
					} else if($aColumns[$i] == 'subject'){
						$_data = '<a href="'.admin_url('contracts/contract/'.$aRow['id']).'">'.$_data.'</a>';
						if($aRow['trash'] == 1){
							$_data .= '<span class="label label-danger pull-right">'._l('contract_trash').'</span>';
						}
					} else if($aColumns[$i] == 'contract_type'){
						$_data = $aRow['name'];
					}

					$row[] = $_data;
				}

				$options = icon_btn('admin/contracts/contract/'.$aRow['id'],'pencil-square-o');
				$options .= icon_btn('admin/contracts/delete/'.$aRow['id'],'remove','btn-danger');

				$row[] = $options;

				if(!empty($aRow['dateend'])){
					$_date_end = date('Y-m-d',strtotime($aRow['dateend']));
				if($_date_end < date('Y-m-d')){
					$row['DT_RowClass'] = 'alert-danger';
				}
				}

				$output['aaData'][] = $row;
			}

			echo json_encode( $output );
			die();
		}

		$data['chart_types'] = json_encode($this->contracts_model->get_contracts_types_chart_data());
		$data['chart_types_values'] = json_encode($this->contracts_model->get_contracts_types_values_chart_data());
		$data['contract_types'] = $this->contracts_model->get_contract_types();

		$data['title'] = _l('contracts');
		$this->load->view('admin/contracts/manage',$data);
	}

	/* Edit contract or add new contract */
	public function contract($id = ''){

		if(!has_permission('manageContracts')){
			access_denied('manageContracts');
		}

		if($this->input->post()){

			if($id == ''){
				$id = $this->contracts_model->add($this->input->post());
				if($id){
					set_alert('success', _l('added_successfuly',_l('contract')));
					redirect(admin_url('contracts/contract/' . $id));
				}
			} else {
				$success = $this->contracts_model->update($this->input->post(),$id);
				if($success){
					set_alert('success', _l('updated_successfuly',_l('contract')));
				}
				redirect(admin_url('contracts/contract/' . $id));
			}
		}

		if($id == ''){
			$title = _l('add_new',_l('contract_lowercase'));
		} else {
			$data['contract'] = $this->contracts_model->get($id);
			$data['contract_renewal_history'] = $this->contracts_model->get_contract_renewal_history($id);

			if(!$data['contract']){
				blank_page(_l('contract_not_found'));
			}

			$title = _l('edit',_l('contract_lowercase'));
		}

		$this->load->model('currencies_model');
		$data['base_currency'] = $this->currencies_model->get_base_currency();
		$data['types'] = $this->contracts_model->get_contract_types();
		$this->load->model('clients_model');
		$data['clients'] = $this->clients_model->get();
		$data['title'] = $title;
		$this->load->view('admin/contracts/contract',$data);
	}

	public function renew(){
		if(!has_permission('manageContracts')){
			access_denied('manageContracts');
		}
		if($this->input->post()){
			$data = $this->input->post();
			$success = $this->contracts_model->renew($data);
			if($success){
				set_alert('success',_l('contract_renewed_successfuly'));
			} else {
				set_alert('warning',_l('contract_renewed_fail'));
			}

			redirect(admin_url('contracts/contract/'.$data['contractid']));
		}
	}
	public function delete_renewal($renewal_id,$contractid){

		if(!has_permission('manageContracts')){
			access_denied('manageContracts');
		}

		$success = $this->contracts_model->delete_renewal($renewal_id,$contractid);
		if($success){
			set_alert('success',_l('contract_renewal_deleted'));
		} else {
			set_alert('warning',_l('contract_renewal_delete_fail'));
		}

		redirect(admin_url('contracts/contract/'.$contractid));
	}
	/* Delete contract from database */
	public function delete($id){

		if(!has_permission('manageContracts')){
			access_denied('manageContracts');
		}

		if(!$id){
			redirect(admin_url('contracts'));
		}
		$response = $this->contracts_model->delete($id);

		if($response == true){
			set_alert('success', _l('deleted',_l('contract')));
		} else {
			set_alert('warning', _l('problem_deleting',_l('contract_lowercase')));
		}
		redirect(admin_url('contracts'));
	}

	/* Manage contract types Since Version 1.0.3 */
	public function type($id = ''){

		if(!has_permission('manageContracts')){
			access_denied('manageContracts');
		}

		if($this->input->post()){
			if(!$this->input->post('id')){
				$id = $this->contracts_model->add_contract_type($this->input->post());
				if($id){
					$success = true;
					$message = _l('added_successfuly',_l('contract_type'));
				}
				echo json_encode(array('success'=>$success,'message'=>$message));
			} else {
				$data = $this->input->post();
				$id = $data['id'];
				unset($data['id']);
				$success = $this->contracts_model->update_contract_type($data,$id);
				$message= '';
				if($success){
					$message = _l('updated_successfuly',_l('contract_type'));
				}
				echo json_encode(array('success'=>$success,'message'=>$message));

			}
		}

	}

	public function types(){
		if(!has_permission('manageContracts')){
			access_denied('manageContracts');
		}
		if ($this->input->is_ajax_request()) {
			$aColumns     = array(
				'name'
				);
			$sIndexColumn = "id";
			$sTable       = 'tblcontracttypes';

			$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,array(),array(),array('id'));
			$output  = $result['output'];
			$rResult = $result['rResult'];

			foreach ($rResult as $aRow) {
				$row = array();
				for ($i = 0; $i < count($aColumns); $i++) {
					$_data = $aRow[$aColumns[$i]];
					if ($aColumns[$i] == 'name') {
						$_data = '<a href="' . admin_url('contracts/type/' . $aRow['id']) . '">' . $_data . '</a> '. '<span class="badge">'.total_rows('tblcontracts',array('contract_type'=>$aRow['id'])).'</span>';
					}
					$row[] = $_data;
				}

				$options = icon_btn('#', 'pencil-square-o','btn-default',array('onclick'=>'edit_type(this,'.$aRow['id'].'); return false;','data-name'=>$aRow['name']));
				$row[]   = $options .= icon_btn('admin/contracts/delete_contract_type/' . $aRow['id'], 'remove', 'btn-danger');

				$output['aaData'][] = $row;
			}

			echo json_encode($output);
			die();
		}
		$data['title'] = _l('contract_types');
		$this->load->view('admin/contracts/manage_types', $data);
	}
	    /* Delete announcement from database */
    public function delete_contract_type($id)
    {
        if (!$id) {
            redirect(admin_url('contracts/types'));
        }
		$response = $this->contracts_model->delete_contract_type($id);

		if(is_array($response) && isset($response['referenced'])){
			set_alert('warning',_l('is_referenced',_l('contract_type_lowercase')));
		} else if($response == true){
			set_alert('success', _l('deleted',_l('contract_type')));
		} else {
			set_alert('warning', _l('problem_deleting',_l('contract_type_lowercase')));
		}

		redirect(admin_url('contracts/types'));
    }
    public function add_contract_attachment($id){
	    handle_contract_attachment($id);
    }

     public function get_contract_attachments($id)
    {
        $data['attachments'] = $this->contracts_model->get_contract_attachments('',$id);
        $this->load->view('admin/contracts/contract_attachments_template', $data);
    }

    public function delete_contract_attachment($attachment_id){
    	 echo json_encode(array(
            'success' => $this->contracts_model->delete_contract_attachments($attachment_id)
        ));
    }
}
