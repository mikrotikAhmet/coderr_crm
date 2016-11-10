<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Currencies_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  integer ID (optional)
     * @return mixed
     * Get currency object based on passed id if not passed id return array of all currencies
     */
    public function get($id = false)
    {

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblcurrencies')->row();
        }

        return $this->db->get('tblcurrencies')->result_array();
    }

    /**
     * @param array $_POST data
     * @return boolean
     */
    public function add($data)
    {
        unset($data['currencyid']);
        $this->db->insert('tblcurrencies', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Currency Added [ID: ' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  array $_POST data
     * @return boolean
     * Update currency values
     */
    public function edit($data)
    {

        $currencyid = $data['currencyid'];
        unset($data['currencyid']);
        $this->db->where('id', $currencyid);
        $this->db->update('tblcurrencies', $data);

        if ($this->db->affected_rows() > 0) {
            logActivity('Currency Updated [' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete currency from database, if used return array with key referenced
     */
    public function delete($id)
    {
        if (is_reference_in_table('currency', 'tblinvoices', $id)) {
            return array(
                'referenced' => true
            );
        }

        $currency = $this->get($id);
        if($currency->isdefault == 1){
            return array(
                'is_default' => true
            );
        }

        $this->db->where('id', $id);
        $this->db->delete('tblcurrencies');

        if ($this->db->affected_rows() > 0) {
            logActivity('Currency Deleted [' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return boolean
     * Make currency your base currency for better using reports if found invoices with more then 1 currency
     */
    public function make_base_currency($id,$force = false){

        $this->db->where('id', $id);
        $this->db->update('tblcurrencies', array(
            'isdefault' => 1,
            'value'=>1,
            'date_modified'=>date('Y-m-d H:i:s')
        ));

        $new_default = $this->get($id);

        if ($this->db->affected_rows() > 0) {

            $currencies = $this->db->query("SELECT * FROM tblcurrencies WHERE id != '".(int) $id."'");


            foreach ($currencies->result_array() as $currency){

                $this->db->where('id =', $currency['id']);
                $this->db->update('tblcurrencies', array(
                    'isdefault' => 0,
                    'value'=>$this->refresh($new_default->name,$currency['name']),
                    'date_modified'=>date('Y-m-d H:i:s')
                ));
            }

            return false;
        }
        return false;

    }

    public function refresh($new_default,$currency)
    {

// Get cURL connection done
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'http://download.finance.yahoo.com/d/quotes.csv?s='.$new_default. $currency . '=X&f=sl1&e=.csv');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

        $content = curl_exec($curl);

        curl_close($curl);

        $lines = explode("\n", trim($content));

        foreach ($lines as $line) {

            $currency = utf8_substr($line, 4, 3);
            $value = utf8_substr($line, 11, 6);

            if ((float)$value) {

                return $value;
            }

        }
    }

    /**
     * @return object
     * Get base currency
     */
    public function get_base_currency()
    {
        $this->db->where('isdefault', 1);
        return $this->db->get('tblcurrencies')->row();
    }

    /**
     * @param  integer ID
     * @return string
     * Get the symbol from the currency
     */
    public function get_currency_symbol($id)
    {
        if(!is_numeric($id)){
            $id = $this->get_base_currency()->id;
        }
        $this->db->select('symbol');
        $this->db->from('tblcurrencies');
        $this->db->where('id', $id);
        return $this->db->get()->row()->symbol;
    }
}
