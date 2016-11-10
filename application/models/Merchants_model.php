<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/25/16
 * Time: 3:11 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Merchants_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('clients_model');
    }

    /**
     * @param  mixed $id merchant id (optional)
     * @param  integer $active (optional) get all active or inactive
     * @return mixed
     * Get merchant object based on passed merchantid if not passed merchantid return array of all merchants
     */
    public function get($id = false, $active = '')
    {

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $merchant = $this->db->get('tblmerchants')->row();

            return $merchant;
        }

        $this->db->order_by('date_added', 'desc');
        return $this->db->get('tblmerchants')->result_array();
    }

    /**
     * @param array $_POST data
     * @return integer Insert ID
     * Add new merchant to database
     */
    public function add($data)
    {
        // First check for all cases if the email exists.
        $this->db->where('userid', $data['userid']);
        $merchant = $this->db->get('tblmerchants')->row();
        if ($merchant) {
            die('Merchant already exists');
        }

        $data['date_added'] = date('Y-m-d H:i:s');

        $data = do_action('before_merchant_added', $data);

        $this->db->insert('tblmerchants', $data);
        $id = $this->db->insert_id();
        if ($id) {

            do_action('after_merchant_added', $id);

            $client = $this->clients_model->get($data['userid']);

            $_new_client_log = $client->firstname . ' ' . $client->lastname;
            $_is_staff       = NULL;

            if (is_staff_logged_in()) {
                $_new_client_log .= ' From Staff: ' . get_staff_user_id();
                $_is_staff = get_staff_user_id();
            }

            logActivity('New Merchant Created [' . $_new_client_log . ']', $_is_staff);

        }


        return $id;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update client informations
     */
    public function update($data, $id,$client_request = false)
    {


        $affectedRows = 0;

        // First check for all cases if the email exists.
        $this->db->where('userid', $data['userid']);
        $client = $this->db->get('tblclients')->row();


        $_data = do_action('before_merchant_updated', array(
            'id' => $id,
            'data' => $data
        ));

        $data = $_data['data'];

        $data['date_added'] = date('Y-m-d H:i:s');


        $this->db->where('id', $id);
        $this->db->update('tblmerchants', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            do_action('after_merchant_updated', $id);


            $_update_client_log = $client->firstname . ' ' . $client->lastname;
            $_is_staff          = NULL;

            if (is_staff_logged_in()) {
                $_update_client_log .= ' From Staff: ' . get_staff_user_id();
                $_is_staff = get_staff_user_id();
            }
        }

        if($affectedRows > 0){
            logActivity('Merchant Info Updated [' . $_update_client_log . ']', $_is_staff);
            return true;
        }

        return false;
    }


    /**
     * @param  integer ID
     * @return boolean
     * Delete merchant, also deleting rows from, merchant processors, transactions, payouts
     */
    public function delete($id)
    {

        $affectedRows = 0;
        do_action('before_merchant_deleted', $id);


        $this->db->where('id', $id);
        $this->db->delete('tblmerchants');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            do_action('after_merchant_deleted');
            logActivity('Merchant Deleted [' . $id . ']');
            return true;
        }
        return;
    }

    public function add_merchant_processor($data){


        $this->db->insert('tblmerchantprocessors',$data);
        $insert_id = $this->db->insert_id();

        if($insert_id){
            logActivity('New Merchant Processor Created [ID:'.$insert_id.', Name:'.$data['alias'].']');
            return true;
        }

        return false;
    }

    public function edit_merchant_processor($data){


        $this->db->where('id',$data['id']);
        $this->db->update('tblmerchantprocessors',array('processorid'=>$data['processorid'],
                'alias'=>$data['alias'],
                'processor_data'=>json_encode($data['processor_data']),
                'mccCode'=>$data['mccCode'],
                'buyRate'=>$data['buyRate'],
                'saleRate'=>$data['saleRate'],
                'rollbackReserve'=>$data['rollbackReserve'],
                'processingLimit'=>$data['processingLimit'],
                'transactionLimit'=>$data['transactionLimit'],
                'transactionFee'=>$data['transactionFee'],
                'active'=>(int) $data['active'],
            )
        );
        if($this->db->affected_rows() > 0){
            logActivity('merchant Processor Updated [ID:'.$data['id'].']');
            return true;
        }
        return false;
    }

    public function delete_merchant_processor($id){

        $this->db->where('id',$id);
        $this->db->delete('tblmerchantprocessors');

        return false;

    }

    public function get_merchant_processor($id = ''){

        if(is_numeric($id)){

            $this->db->where('id',$id);
            return $this->db->get('tblmerchantprocessors')->row();
        }

        return $this->db->get('tblmerchantprocessors')->result_array();
    }

    /**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update merchant processor status Active/Inactive
     */
    public function change_processor_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update('tblmerchantprocessors', array(
            'active' => $status
        ));

        if ($this->db->affected_rows() > 0) {
            logActivity('Merchant Processor Status Changed [ClientID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');
            return true;
        }

        return false;
    }

}