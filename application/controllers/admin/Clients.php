<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clients extends Admin_controller {
	private $not_importable_clients_fields = array('userid','password','datecreated','last_ip','last_login','last_password_change','active','new_pass_key','new_pass_key_requested','leadid','default_currency');
	public $pdf_zip;
	function __construct(){
		parent::__construct();
		$this->load->model('clients_model');
	}

	/* List all clients */
	public function index()
	{
		if($this->input->is_ajax_request()){

			$custom_fields = get_custom_fields('customers',array('show_on_table'=>1));

			$aColumns = array('firstname','email','phonenumber','(SELECT GROUP_CONCAT(name) FROM tblcustomersgroups LEFT JOIN tblcustomergroups_in ON tblcustomergroups_in.groupid = tblcustomersgroups.id WHERE customer_id = tblclients.userid)','tblclients.active');

			$join = array();
			$i = 0;
			foreach($custom_fields as $field){
				array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
				array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblclients.userid = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
				$i++;
			}


			$sIndexColumn = "userid";
			$sTable = 'tblclients';

			$where = array();

			if($this->input->post('custom_view')){
				 $custom_view = $this->input->post('custom_view');
				 // view by goups
				if (is_numeric($custom_view)) {
                        array_push($where, 'AND userid IN (SELECT customer_id FROM tblcustomergroups_in WHERE groupid = '.$custom_view.')');
                }
			} else if($this->input->post('invoices_by_status')){
				$status = $this->input->post('invoices_by_status');
				array_push($where, 'AND userid IN (SELECT clientid FROM tblinvoices WHERE status = '.$status.')');
			} else if($this->input->post('estimates_by_status')){
				$status = $this->input->post('estimates_by_status');
				array_push($where, 'AND userid IN (SELECT clientid FROM tblestimates WHERE status = '.$status.')');
			} else if($this->input->post('contracts_by_type')){
				$type = $this->input->post('contracts_by_type');
				array_push($where, 'AND userid IN (SELECT client FROM tblcontracts WHERE contract_type = '.$type.')');
			}


			$result = data_tables_init($aColumns,$sIndexColumn,$sTable,$join,$where,array('lastname','company','userid'));
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

					if($i == 3){
						if($_data != ''){
							$groups = explode(',',$_data);
							$_data = '';
							foreach($groups as $group){
								$_data .= '<span class="label label-info pull-left mright5">'.  $group . '</span>';
							}
						}
					}
					if ($aColumns[$i] == 'tblclients.active') {
						$checked = '';
						if($aRow['tblclients.active'] == 1){
							$checked = 'checked';
						}
						$_data = '<input type="checkbox" class="switch-box input-xs" data-size="mini" data-id="'.$aRow['userid'].'" data-switch-url="admin/clients/change_client_status" '.$checked.'>';
						// For exporting
						$_data .=  '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) .'</span>';
					} else if ($aColumns[$i] == 'firstname'){
						$_data = $_data . ' ' . $aRow['lastname'];
						if($aRow['company'] != '' || $aRow['company'] !== NULL){
							$_data .= '<br /><span class="text-muted">'.$aRow['company'].'</span>';
						}
						$_data = '<a href="'.admin_url('clients/client/'.$aRow['userid']).'">'.$_data.'</a>';
					} else if($aColumns[$i] == 'phonenumber'){
						$_data = '<a href="tel:'.$_data.'">'.$_data.'</a>';
					} else if($aColumns[$i] == 'email'){
						$_data = '<a href="mailto:'.$_data.'">'.$_data.'</a>';
					}

					$row[] = $_data;
				}

				$options = icon_btn('admin/clients/client/'.$aRow['userid'],'pencil-square-o');
				$row[]  = $options .= icon_btn('admin/clients/delete/'.$aRow['userid'],'remove','btn-danger btn-delete-customer',array('data-toggle'=>'tooltip','title'=>_l('client_delete_tooltip')));

				$output['aaData'][] = $row;
			}

			echo json_encode( $output );
			die();
		}

		$this->load->model('contracts_model');
		$data['contract_types'] = $this->contracts_model->get_contract_types();
		$data['groups'] = $this->clients_model->get_groups();
		$data['title'] = _l('clients');
		$this->load->view('admin/clients/manage',$data);
	}
	/* Edit client or add new client*/
	public function client($id = ''){

		if($this->input->post() && !$this->input->is_ajax_request()){

			if(!has_permission('manageClients')){
				access_denied('manageClients');
			}

			if($id == ''){
				$id = $this->clients_model->add($this->input->post());
				if($id){
					set_alert('success', _l('added_successfuly',_l('client')));
					redirect(admin_url('clients/client/'.$id));
				}
			} else {
				$success = $this->clients_model->update($this->input->post(),$id);
				if(is_array($success)){
					if(isset($success['set_password_email_sent'])) {
						set_alert('success', _l('set_password_email_sent_to_client'));
					} else if(isset($success['set_password_email_sent_and_profile_updated'])){
						set_alert('success', _l('set_password_email_sent_to_client_and_profile_updated'));
					}
				} else if($success == true){
					set_alert('success', _l('updated_successfuly',_l('client')));
				}
				redirect(admin_url('clients/client/'.$id));
			}
		}

		if($id == ''){
			$title = _l('add_new',_l('client_lowercase'));
		} else {
			$client = $this->clients_model->get($id);

			if(!$client){
				blank_page('Client Not Found');
			}

			$this->load->model('payment_modes_model');
			$data['payment_modes'] = $this->payment_modes_model->get();
			$data['client'] = $client;
			$title = $client->firstname . ' ' .$client->lastname;
			// Get all active staff members (used to add reminder)
			$this->load->model('staff_model');
			$data['members'] = $this->staff_model->get('',1);
			if($this->input->is_ajax_request()){
				AdminTicketsTable(array('userid'=>$id));
			}
			$data['customer_groups'] = $this->clients_model->get_customer_groups($id);
		}

		$data['groups'] = $this->clients_model->get_groups();
		$this->load->model('currencies_model');
		$data['currencies'] = $this->currencies_model->get();
		$data['user_notes'] = $this->misc_model->get_user_notes($id,0);
		$data['title'] = $title;
		$this->load->view('admin/clients/client',$data);
	}

	public function get_attachments($id){
		$data['clientid'] = $id;
		$data['attachments'] = $this->clients_model->get_all_customer_attachments($id);
		$this->load->view('admin/clients/attachments_template',$data);
	}

	public function upload_attachment($id){
		handle_client_attachments_upload($id);
	}

	public function delete_attachment($id){
		echo json_encode(array('success'=>$this->clients_model->delete_attachment($id)));
	}

	/* Delete client */
	public function delete($id){

		if(!has_permission('manageClients')){
			access_denied('manageClients');
		}

		if(!$id){
			redirect(admin_url('clients'));
		}

		$response = $this->clients_model->delete($id);

		if(is_array($response) && isset($response['referenced'])){
			set_alert('warning',_l('is_referenced',_l('client_lowercase')));
		} else if($response == true){
			set_alert('success', _l('deleted',_l('client')));
		} else {
			set_alert('warning', _l('problem_deleting',_l('client_lowercase')));
		}

		redirect(admin_url('clients'));
	}

	 /* Staff can login as client */
    public function login_as_client($id)
    {
        $this->clients_model->login_as_client($id);
    }


	public function get_customer_default_currency($id){
		echo json_encode($this->clients_model->get_customer_default_currency($id));
	}
	public function get_customer_billing_and_shipping_details($id){
		echo json_encode($this->clients_model->get_customer_billing_and_shipping_details($id));
	}

    public function get_customer_shipping_details($id){
		echo json_encode($this->clients_model->get_customer_shipping_details($id));
	}
	/* Change client status / active / inactive */
	public function change_client_status($id,$status){

		if(!has_permission('manageClients')){
			access_denied('manageClients');
		}

		if($this->input->is_ajax_request()){
			$this->clients_model->change_client_status($id,$status);
		}
	}
	/* Get client by id - used in ajax request */
	public function get_client_by_id_ajax($id){
		if($this->input->is_ajax_request()){
			if(!$id){
				redirect(admin_url('clients'));
			}
			if($this->input->is_ajax_request()){
				echo json_encode($this->clients_model->get($id));
			}
		}
	}
	/* Since version 1.0.2 zip client invoices */
	public function zip_invoices($id){
		if(!has_permission('manageClients') || !has_permission('manageSales')){
			access_denied('manageClients');
		}
		if($this->input->post()){


			$status = $this->input->post('invoice_zip_status');
			$zip_file_name = $this->input->post('file_name');

			if($this->input->post('zip-to') && $this->input->post('zip-from')){
				$from_date = to_sql_date($this->input->post('zip-from'));
				$to_date   = to_sql_date($this->input->post('zip-to'));

				if ($from_date == $to_date) {
					$this->db->where('date',$from_date);
				} else {
					$this->db->where('date BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
				}
			}

			$this->db->select('id');
			$this->db->from('tblinvoices');
			if($status != 'all'){
				$this->db->where('status',$status);
			}
			$this->db->where('clientid',$id);
			$this->db->order_by('date','desc');
			$invoices = $this->db->get()->result_array();

			$this->load->model('invoices_model');
			$this->load->helper('file');

			if(!is_really_writable(TEMP_FOLDER)){
				show_error('/temp folder is not writable. You need to change the permissions to 777');
			}

			$dir = TEMP_FOLDER . $zip_file_name;
			if(is_dir($dir)){
				delete_dir($dir);
			}

			if(count($invoices) == 0){
				set_alert('warning',_l('client_zip_no_data_found',_l('invoices')));
				redirect(admin_url('clients/client/'.$id));
			}

			mkdir($dir, 0777);

			foreach($invoices as $invoice){
				$invoice_data = $this->invoices_model->get($invoice['id']);
				$this->pdf_zip = invoice_pdf($invoice_data);
				$_temp_file_name = slug_it(format_invoice_number($invoice_data->id));
				$file_name = $dir . '/' . strtoupper($_temp_file_name);

				$this->pdf_zip->Output($file_name . '.pdf','F');
			}

			$this->load->library('zip');
			// Read the invoices
			$this->zip->read_dir($dir,false);
			// Delete the temp directory for the client
			delete_dir($dir);

			$this->zip->download(slug_it(get_option('companyname')).'-invoices-'.$zip_file_name.'.zip');
			$this->zip->clear_data();
		}
	}

	/* Since version 1.0.2 zip client invoices */
	public function zip_estimates($id){
		if(!has_permission('manageClients') || !has_permission('manageSales')){
			access_denied('manageClients');
		}
		if($this->input->post()){

			$status = $this->input->post('estimate_zip_status');
			$zip_file_name = $this->input->post('file_name');

			if($this->input->post('zip-to') && $this->input->post('zip-from')){
				$from_date = to_sql_date($this->input->post('zip-from'));
				$to_date   = to_sql_date($this->input->post('zip-to'));

				if ($from_date == $to_date) {
					$this->db->where('date',$from_date);
				} else {
					$this->db->where('date BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
				}
			}

			$this->db->select('id');
			$this->db->from('tblestimates');

			if($status != 'all'){
				$this->db->where('status',$status);
			}
			$this->db->where('clientid',$id);
			$this->db->order_by('date','desc');
			$estimates = $this->db->get()->result_array();

			$this->load->helper('file');

			if(!is_really_writable(TEMP_FOLDER)){
				show_error('/temp folder is not writable. You need to change the permissions to 777');
			}

			$this->load->model('estimates_model');
			$dir = TEMP_FOLDER . $zip_file_name;

			if(is_dir($dir)){
			delete_dir($dir);
		}

		if(count($estimates) == 0){
			set_alert('warning',_l('client_zip_no_data_found',_l('estimates')));
			redirect(admin_url('clients/client/'.$id));
		}

		mkdir($dir, 0777);

		foreach($estimates as $estimate){
				$estimate_data = $this->estimates_model->get($estimate['id']);
				$this->pdf_zip = estimate_pdf($estimate_data);
				$_temp_file_name = slug_it(format_estimate_number($estimate_data->id));
				$file_name = $dir . '/' . strtoupper($_temp_file_name);
				$this->pdf_zip->Output($file_name . '.pdf','F');
		}

			$this->load->library('zip');
			// Read the invoices
			$this->zip->read_dir($dir,false);
			// Delete the temp directory for the client
			delete_dir($dir);
			$this->zip->download(slug_it(get_option('companyname')).'-estimates-'.$zip_file_name.'.zip');
			$this->zip->clear_data();
		}
	}

	public function zip_payments($id){

		if(!$id){
			die('No user id');
		}

		if(!has_permission('manageClients') || !has_permission('manageSales')){
			access_denied('manageClients');
		}

		if($this->input->post('zip-to') && $this->input->post('zip-from')){
			$from_date = to_sql_date($this->input->post('zip-from'));
			$to_date   = to_sql_date($this->input->post('zip-to'));

			if ($from_date == $to_date) {
				$this->db->where('date',$from_date);
			} else {
				$this->db->where('date BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
			}
		}

		$this->db->select('tblinvoicepaymentrecords.id as paymentid');
		$this->db->from('tblinvoicepaymentrecords');
		$this->db->where('tblclients.userid',$id);
		$this->db->join('tblinvoices','tblinvoices.id = tblinvoicepaymentrecords.invoiceid','left');
		$this->db->join('tblclients','tblclients.userid = tblinvoices.clientid','left');

		if($this->input->post('paymentmode')){
			$this->db->where('paymentmode',$this->input->post('paymentmode'));
		}
		$payments = $this->db->get()->result_array();

		$zip_file_name = $this->input->post('file_name');

		$this->load->helper('file');

		if(!is_really_writable(TEMP_FOLDER)){
			show_error('/temp folder is not writable. You need to change the permissions to 777');
		}

		$dir = TEMP_FOLDER . $zip_file_name;
		if(is_dir($dir)){
			delete_dir($dir);
		}
		if(count($payments) == 0){
			set_alert('warning',_l('client_zip_no_data_found',_l('payments')));
			redirect(admin_url('clients/client/'.$id));
		}

		mkdir($dir, 0777);

		$this->load->model('payments_model');
		$this->load->model('invoices_model');
		foreach($payments as $payment){
			$payment_data = $this->payments_model->get($payment['paymentid']);
			$payment_data->invoice_data = $this->invoices_model->get($payment_data->invoiceid);
			$this->pdf_zip = payment_pdf($payment_data);
			$file_name = $dir;
			$file_name .= '/' . strtoupper(_l('payment'));
			$file_name .= '-' . strtoupper($payment_data->paymentid) . '.pdf';
			$this->pdf_zip->Output($file_name, 'F');
		}

		$this->load->library('zip');
		// Read the invoices
		$this->zip->read_dir($dir,false);
		// Delete the temp directory for the client
		delete_dir($dir);
		$this->zip->download(slug_it(get_option('companyname')).'-payments-'.$zip_file_name.'.zip');
		$this->zip->clear_data();
	}

	public function import(){

	$simulate_data = array();
        $total_imported = 0;
        if($this->input->post()){
          if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
             // Get the temp file path
            $tmpFilePath = $_FILES['file_csv']['tmp_name'];
             // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
              // Setup our new file path
                $newFilePath = TEMP_FOLDER . $_FILES['file_csv']['name'];
                if (!file_exists(TEMP_FOLDER)) {
                    mkdir(TEMP_FOLDER,777);
                }
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {

                    $import_result = true;
                    $fd = fopen($newFilePath,'r');
                    $rows = array();
                    while($row = fgetcsv($fd)){
                        $rows[] = $row;
                    }

                    fclose($fd);
                    if(count($rows) <= 1){
                        set_alert('warning','Not enought rows for importing');
                        redirect(admin_url('clients/import'));
                    }
                    unset($rows[0]);
                     if($this->input->post('simulate')){
                        if(count($rows) > 500){
                            set_alert('warning','Recommended splitting the CSV file into smaller files. Our recomendation is 500 row, your CSV file has ' .count($rows));
                        }
                    }
                    $db_temp_fields = $this->db->list_fields('tblclients');
                    $db_fields = array();
                    foreach($db_temp_fields as $field){
                           if(in_array($field,$this->not_importable_clients_fields)){continue;}
                           $db_fields[] = $field;
                    }

                    $custom_fields = get_custom_fields('customers');
                    $_row_simulate = 0;

                    $required = array('firstname','lastname','email');

                    foreach($rows as $row){
                       // do for db fields
                       $insert = array();
                       $duplicate = false;
                       for($i=0;$i<count($db_fields);$i++){
                       	if(!isset($row[$i])){continue;}

                       	if($db_fields[$i] == 'email'){
                       		$email_exists = total_rows('tblclients',array('email'=>$row[$i]));
                       		// dont insert duplicate emails
                       		if($email_exists > 0){$duplicate = true;}
                       	}
                        // Avoid errors on required fields;
                        if(in_array($db_fields[$i],$required) && $row[$i] == ''){
                            $row[$i] = '/';
                        } else if($db_fields[$i] == 'country'){
                        if($row[$i] != ''){
                        	$this->db->where('iso2',$row[$i]);
                        	$this->db->or_where('short_name',$row[$i]);
                        	$this->db->or_where('long_name',$row[$i]);
                        	$country = $this->db->get('tblcountries')->row();
                        	if($country){
	                        	$row[$i] = $country->country_id;
                        	} else {
                        		$row[$i] = 0;
                        	}
                        } else {
                        	$row[$i] = 0;
                        }
                        }

                       $insert[$db_fields[$i]] = $row[$i];
                       }
                       if($duplicate == true){
                       	continue;
                       }
                       if(count($insert) > 0){
                            $total_imported++;
                            $insert['datecreated'] = date('Y-m-d H:i:s');
                            if($this->input->post('default_pass_all')){
                            	$this->load->helper('phpass');
						        $hasher              = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
						        $data['password']    = $hasher->HashPassword($this->input->post('default_pass_all'));
                            }

                            if(!$this->input->post('simulate')){
                                $this->db->insert('tblclients',$insert);
                                $clientid = $this->db->insert_id();
                                if($clientid){
                                	if($this->input->post('groups_in[]')){
                                		$groups_in = $this->input->post('groups_in[]');
                                		foreach ($groups_in as $group) {
                                			$this->db->insert('tblcustomergroups_in', array(
                                				'customer_id' => $clientid,
                                				'groupid' => $group
                                				));
                                		}
                                	}
                                }
                            } else {
                                $simulate_data[$_row_simulate] = $insert;
                                $clientid = true;
                            }
                            if($clientid){
                                $insert = array();
                                foreach($custom_fields as $field){
                                 if(!$this->input->post('simulate')){
                                       if($row[$i] == ''){continue;}
                                       $this->db->insert('tblcustomfieldsvalues',array(
                                            'relid'=>$clientid,
                                            'fieldid'=>$field['id'],
                                            'value'=>$row[$i],
                                            'fieldto'=>'customers'
                                        ));
                                  } else {
                                    $simulate_data[$_row_simulate][$field['name']] = $row[$i];
                                  }
                                   $i++;
                                }
                            }
                        }
                        $_row_simulate++;
                        if($this->input->post('simulate') && $_row_simulate >= 100){
                            break;
                        }
                   }
                   unlink($newFilePath);
               }
           } else {
            set_alert('warning',_l('import_upload_failed'));
           }
       }
   }
   if(count($simulate_data) > 0 ){
        $data['simulate'] = $simulate_data;
   }
   if(isset($import_result)){
        set_alert('success',_l('import_total_imported',$total_imported));
   }

	   $data['groups'] = $this->clients_model->get_groups();
	   $data['not_importable'] = $this->not_importable_clients_fields;
	   $data['title'] = 'Import';
	   $this->load->view('admin/clients/import',$data);
	}

	public function groups(){
		if($this->input->is_ajax_request()){
				$aColumns = array('name');

				$sIndexColumn = "id";
				$sTable = 'tblcustomersgroups';

				$result = data_tables_init($aColumns,$sIndexColumn,$sTable,array(),array(),array('id'));
				$output = $result['output'];
				$rResult = $result['rResult'];

				foreach ( $rResult as $aRow )
				{
					$row = array();
					for ( $i=0 ; $i<count($aColumns) ; $i++ )
					{
						$_data = $aRow[ $aColumns[$i] ];
						$row[] = $_data;
					}
					$options = icon_btn('#','pencil-square-o','btn-default',array('data-toggle'=>'modal','data-target'=>'#customer_group_modal','data-id'=>$aRow['id']));
					$row[]  = $options .= icon_btn('admin/clients/delete_group/'.$aRow['id'],'remove','btn-danger');

					$output['aaData'][] = $row;
				}

				echo json_encode( $output );
				die();
		}
		$data['title'] = _l('customer_groups');
		$this->load->view('admin/clients/groups_manage',$data);
	}


	public function group(){
		if($this->input->is_ajax_request()){
			$data = $this->input->post();
			if($data['id'] == ''){

				unset($data['id']);

				$success = $this->clients_model->add_group($data);
				$message = '';
				if($success == true){
					$message =  _l('added_successfuly',_l('customer_group'));
				}
				echo json_encode(array('success'=>$success,'message'=>$message));
			} else {
				$success = $this->clients_model->edit_group($data);
				$message = '';
				if($success == true){
					$message = _l('updated_successfuly',_l('customer_group'));
				}
				echo json_encode(array('success'=>$success,'message'=>$message));
			}
		}
	}

	public function delete_group($id){

		if(!$id){
			redirect(admin_url('clients/groups'));
		}

		$response = $this->clients_model->delete_group($id);
		if(is_array($response) && isset($response['referenced'])){
			set_alert('warning',_l('is_referenced',_l('customer_group_lowercase')));
		} else if($response == true){
			set_alert('success', _l('deleted',_l('customer_group')));
		} else {
			set_alert('warning', _l('problem_deleting',_l('customer_group_lowercase')));
		}

		redirect(admin_url('clients/groups'));

	}

}
