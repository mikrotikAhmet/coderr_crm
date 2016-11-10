<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_items_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get invoice item by ID
     * @param  mixed $id
     * @return mixed - array if not passed id, object if id passed
     */
    public function get($id = '')
    {
        $this->db->select('tblinvoiceitemslist.id as itemid,rate,taxrate,tbltaxes.id as taxid,tbltaxes.name as taxname,description,long_description');
        $this->db->from('tblinvoiceitemslist');
        $this->db->join('tbltaxes', 'tbltaxes.id = tblinvoiceitemslist.tax', 'left');
        if (is_numeric($id)) {
            $this->db->where('tblinvoiceitemslist.id', $id);
            return $this->db->get()->row();
        }
        return $this->db->get()->result_array();
    }

    /**
     * Add new invoice item
     * @param array $data Invoice item data
     * @return boolean
     */
    public function add($data)
    {
        unset($data['itemid']);

        if ($data['tax'] == '') {
            unset($data['tax']);
        }

        $this->db->insert('tblinvoiceitemslist', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            logActivity('New Invoice Item Added [ID:'.$insert_id.', ' . $data['description'] . ']');
            return true;
        }

        return false;
    }
    /**
     * Update invoiec item
     * @param  array $data Invoice data to update
     * @return boolean
     */
    public function edit($data)
    {

        $itemid = $data['itemid'];
        unset($data['itemid']);
        $this->db->where('id', $itemid);
        $this->db->update('tblinvoiceitemslist', $data);

        if ($this->db->affected_rows() > 0) {
            logActivity('Invoice Item Updated [ID: '.$itemid.', ' . $data['description'] . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete invoice item
     * @param  mixed $id
     * @return boolean
     */
    public function delete($id)
    {

        $this->db->where('id', $id);
        $this->db->delete('tblinvoiceitemslist');

        if ($this->db->affected_rows() > 0) {
            logActivity('Invoice Item Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Get invoice items - ajax call for autocomplete when adding invoicei tems
     * @param  mixed $data query
     * @return array
     */
    public function get_all_items_ajax()
    {
        $this->db->select('tblinvoiceitemslist.id as itemid,rate,taxrate,tbltaxes.id as taxid,tbltaxes.name as taxname,description as label,long_description');
        $this->db->from('tblinvoiceitemslist');
        $this->db->join('tbltaxes', 'tbltaxes.id = tblinvoiceitemslist.tax', 'left');
        return $this->db->get()->result_array();
    }
}
