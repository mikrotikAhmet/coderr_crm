<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/22/16
 * Time: 11:39 AM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Affiliates extends Admin_controller {

    function __construct(){
        parent::__construct();
        $this->load->model('affiliates_model');
    }

    /* List all affiliates */
    public function index()
    {
        if($this->input->is_ajax_request()){

            $custom_fields = get_custom_fields('affiliates',array('show_on_table'=>1));

            $aColumns = array('firstname','email','phonenumber','(SELECT GROUP_CONCAT(name) FROM tblaffiliatesgroups LEFT JOIN tblaffiliategroups_in ON tblaffiliategroups_in.groupid = tblaffiliatesgroups.id WHERE affiliate_id = tblaffiliates.affiliateid)','tblaffiliates.active');

            $join = array();
            $i = 0;
            foreach($custom_fields as $field){
                array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
                array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblaffiliates.affiliateid = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
                $i++;
            }


            $sIndexColumn = "affiliateid";
            $sTable = 'tblaffiliates';

            $where = array();

            if($this->input->post('custom_view')){
                $custom_view = $this->input->post('custom_view');
                // view by goups
                if (is_numeric($custom_view)) {
                    array_push($where, 'AND affiliateid IN (SELECT affiliate_id FROM tblaffiliategroups_in WHERE groupid = '.$custom_view.')');
                }
            }


            $result = data_tables_init($aColumns,$sIndexColumn,$sTable,$join,$where,array('lastname','company','affiliateid'));
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
                    if ($aColumns[$i] == 'tblaffiliates.active') {
                        $checked = '';
                        if($aRow['tblaffiliates.active'] == 1){
                            $checked = 'checked';
                        }
                        $_data = '<input type="checkbox" class="switch-box input-xs" data-size="mini" data-id="'.$aRow['affiliateid'].'" data-switch-url="admin/affiliates/change_affiliate_status" '.$checked.'>';
                        // For exporting
                        $_data .=  '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) .'</span>';
                    } else if ($aColumns[$i] == 'firstname'){
                        $_data = $_data . ' ' . $aRow['lastname'];
                        if($aRow['company'] != '' || $aRow['company'] !== NULL){
                            $_data .= '<br /><span class="text-muted">'.$aRow['company'].'</span>';
                        }
                        $_data = '<a href="'.admin_url('affiliates/affiliate/'.$aRow['affiliateid']).'">'.$_data.'</a>';
                    } else if($aColumns[$i] == 'phonenumber'){
                        $_data = '<a href="tel:'.$_data.'">'.$_data.'</a>';
                    } else if($aColumns[$i] == 'email'){
                        $_data = '<a href="mailto:'.$_data.'">'.$_data.'</a>';
                    }

                    $row[] = $_data;
                }

                $options = icon_btn('admin/affiliates/affiliate/'.$aRow['affiliateid'],'pencil-square-o');
                $row[]  = $options .= icon_btn('admin/affiliates/delete/'.$aRow['affiliateid'],'remove','btn-danger btn-delete-affiliate',array('data-toggle'=>'tooltip','title'=>_l('affiliate_delete_tooltip')));

                $output['aaData'][] = $row;
            }

            echo json_encode( $output );
            die();
        }

        $data['groups'] = $this->affiliates_model->get_groups();
        $data['title'] = _l('affiliates');
        $this->load->view('admin/affiliates/manage',$data);
    }

    /* Edit affiliate or add new affiliate*/
    public function affiliate($id = ''){

        if($this->input->post() && !$this->input->is_ajax_request()){

            if(!has_permission('manageAffiliates')){
                access_denied('manageAffiliates');
            }

            if($id == ''){
                $id = $this->affiliates_model->add($this->input->post());
                if($id){
                    set_alert('success', _l('added_successfuly',_l('affiliate')));
                    redirect(admin_url('affiliates/affiliate/'.$id));
                }
            } else {
                $success = $this->affiliates_model->update($this->input->post(),$id);
                if(is_array($success)){
                    if(isset($success['set_password_email_sent'])) {
                        set_alert('success', _l('set_password_email_sent_to_affiliate'));
                    } else if(isset($success['set_password_email_sent_and_profile_updated'])){
                        set_alert('success', _l('set_password_email_sent_to_affiliate_and_profile_updated'));
                    }
                } else if($success == true){
                    set_alert('success', _l('updated_successfuly',_l('affiliate')));
                }
                redirect(admin_url('affiliates/affiliate/'.$id));
            }
        }

        if($id == ''){
            $title = _l('add_new',_l('affiliate_lowercase'));
        } else {
            $affiliate = $this->affiliates_model->get($id);

            if(!$affiliate){
                blank_page('affiliate Not Found');
            }

            $data['affiliate'] = $affiliate;
            $title = $affiliate->firstname . ' ' .$affiliate->lastname;
            // Get all active staff members (used to add reminder)
            $this->load->model('staff_model');
            $data['members'] = $this->staff_model->get('',1);
            $data['affiliate_groups'] = $this->affiliates_model->get_affiliate_groups($id);
        }

        $data['groups'] = $this->affiliates_model->get_groups();
        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $data['user_notes'] = $this->misc_model->get_user_notes($id,0);
        $data['title'] = $title;
        $this->load->view('admin/affiliates/affiliate',$data);
    }

    /* Delete lead from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('affiliates'));
        }

        if (!is_admin()) {
            die;
        }

        $response = $this->affiliates_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', 'affiliate'));
        } else if ($response === true) {
            set_alert('success', _l('deleted', 'Affiliate'));
        } else {
            set_alert('warning', _l('problem_deleting', 'affiliate'));
        }

        redirect(admin_url('affiliates'));
    }

    public function groups(){
        if($this->input->is_ajax_request()){
            $aColumns = array('name');

            $sIndexColumn = "id";
            $sTable = 'tblaffiliatesgroups';

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
                $options = icon_btn('#','pencil-square-o','btn-default',array('data-toggle'=>'modal','data-target'=>'#affiliate_group_modal','data-id'=>$aRow['id']));
                $row[]  = $options .= icon_btn('admin/affiliates/delete_group/'.$aRow['id'],'remove','btn-danger');

                $output['aaData'][] = $row;
            }

            echo json_encode( $output );
            die();
        }
        $data['title'] = _l('affiliate_groups');
        $this->load->view('admin/affiliates/groups_manage',$data);
    }

    public function get_attachments($id){
        $data['id'] = $id;
        $data['attachments'] = $this->affiliates_model->get_all_affiliate_attachments($id);
        $this->load->view('admin/affiliates/attachments_template',$data);
    }

    public function upload_attachment($id){
        handle_affiliate_attachments_upload($id);
    }

    public function delete_attachment($id){
        echo json_encode(array('success'=>$this->affiliates_model->delete_attachment($id)));
    }

    /* Change client status / active / inactive */
    public function change_affiliate_status($id,$status){

        if(!has_permission('manageAffiliates')){
            access_denied('manageAffiliates');
        }

        if($this->input->is_ajax_request()){
            $this->affiliates_model->change_affiliate_status($id,$status);
        }
    }


    public function group(){
        if($this->input->is_ajax_request()){
            $data = $this->input->post();
            if($data['id'] == ''){

                unset($data['id']);

                $success = $this->affiliates_model->add_group($data);
                $message = '';
                if($success == true){
                    $message =  _l('added_successfuly',_l('affiliate_group'));
                }
                echo json_encode(array('success'=>$success,'message'=>$message));
            } else {
                $success = $this->affiliates_model->edit_group($data);
                $message = '';
                if($success == true){
                    $message = _l('updated_successfuly',_l('affiliate_group'));
                }
                echo json_encode(array('success'=>$success,'message'=>$message));
            }
        }
    }

    public function delete_group($id){

        if(!$id){
            redirect(admin_url('affiliates/groups'));
        }

        $response = $this->affiliates_model->delete_group($id);
        if(is_array($response) && isset($response['referenced'])){
            set_alert('warning',_l('is_referenced',_l('affiliate_group_lowercase')));
        } else if($response == true){
            set_alert('success', _l('deleted',_l('affiliate_group')));
        } else {
            set_alert('warning', _l('problem_deleting',_l('affiliate_group_lowercase')));
        }

        redirect(admin_url('affiliates/groups'));

    }

    public function get_clients($id = ''){

        if($this->input->is_ajax_request()){
            $aColumns = array('firstname');

            $sIndexColumn = "userid";
            $sTable = 'tblclients';


            $result = data_tables_init($aColumns,$sIndexColumn,$sTable,array(),array(),array('userid','lastname','company','affiliateid'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ( $rResult as $aRow )
            {

                #TODO0

                if ($aRow['affiliateid'] != 2){

                    continue;
                }

                $row = array();
                for ( $i=0 ; $i<count($aColumns) ; $i++ )
                {
                    $_data = $aRow[ $aColumns[$i] ];

                    if ($aColumns[$i] == 'firstname') {
                        $_data = $_data . ' ' . $aRow['lastname'];
                        if ($aRow['company'] != '' || $aRow['company'] !== NULL) {
                            $_data .= '<br /><span class="text-muted">' . $aRow['company'] . '</span>';
                        }
                        $_data = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '">' . $_data . '</a>';
                    }
                    $row[] = $_data;
                }

                $row[]  = icon_btn('admin/clients/client/'.$aRow['userid'],'eye','btn-primary');

                $output['aaData'][] = $row;
            }

            echo json_encode( $output );
            die();
        }
        $data['title'] = _l('affiliate_groups');
        $this->load->view('admin/affiliates/groups_manage',$data);
    }

}