<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Taxes_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get tax by id
     * @param  mixed $id tax id
     * @return mixed     if id passed return object else array
     */
    public function get($id = '')
    {

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tbltaxes')->row();
        }

        return $this->db->get('tbltaxes')->result_array();
    }

    /**
     * Add new tax
     * @param array $data tax data
     * @return boolean
     */
    public function add($data)
    {
        unset($data['taxid']);
        $this->db->insert('tbltaxes', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Tax Added [ID: '.$insert_id.', ' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * Edit tax
     * @param  array $data tax data
     * @return boolean
     */
    public function edit($data)
    {

        $taxid = $data['taxid'];
        unset($data['taxid']);
        $this->db->where('id', $taxid);
        $this->db->update('tbltaxes', $data);

        if ($this->db->affected_rows() > 0) {
            logActivity('Tax Updated [ID: '.$taxid.', ' . $data['name'] . ']');
            return true;
        }

        return false;
    }
    /**
     * Delete tax from database
     * @param  mixed $id tax id
     * @return boolean
     */
    public function delete($id)
    {
        if (is_reference_in_table('tax', 'tblinvoiceitemslist', $id)) {
            return array(
                'referenced' => true
            );
        }

        $this->db->where('id', $id);
        $this->db->delete('tbltaxes');

        if ($this->db->affected_rows() > 0) {
            logActivity('Tax Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }
}
