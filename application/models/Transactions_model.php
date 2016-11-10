<?php
/**
 * Created by PhpStorm.
 * User: smt2016
 * Date: 18.9.16.
 * Time: 02.34
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
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
            $transaction = $this->db->get('tbltransactions')->row();

            return $transaction;
        }

        $this->db->order_by('id', 'desc');
        return $this->db->get('tbltransactions')->result_array();
    }

    /**
     * @param  mixed $id merchant id (optional)
     * @param  integer $active (optional) get all active or inactive
     * @return mixed
     * Get merchant object based on passed merchantid if not passed merchantid return array of all merchants
     */
    public function gettrx($id = false, $active = '')
    {

        if (is_numeric($id)) {
            $this->db->where('transactionid', $id);
            $transaction = $this->db->get('tbltransactions')->row();

            return $transaction;
        }

        $this->db->order_by('transactionid', 'desc');
        return $this->db->get('tbltransactions')->result_array();
    }
}