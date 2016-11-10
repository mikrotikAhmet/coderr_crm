<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Custom_fields_model extends CRM_Model
{
    private $pdf_fields = array('customers','estimate','invoice','proposal');
    private $client_portal_fields = array('customers','estimate','invoice','proposal','contracts');
    function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  integer (optional)
     * @return object
     * Get single custom field
     */
    public function get($id = false)
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblcustomfields')->row();
        }
        return $this->db->get('tblcustomfields')->result_array();
    }

    /**
     * Add new custom field
     * @param mixed $data All $_POST data
     * @return  boolean
     */
    public function add($data){

        if (isset($data['disabled'])) {
            $data['active'] = 0;
            unset($data['disabled']);
        } else {
            $data['active'] = 1;
        }

        if(isset($data['show_on_pdf'])){
            if(in_array($data['fieldto'],$this->pdf_fields)){
                $data['show_on_pdf'] = 1;
            } else {
                $data['show_on_pdf'] = 0;
            }
        } else {
            $data['show_on_pdf'] = 0;
        }

        if (isset($data['required'])) {
            $data['required'] = 1;
        } else {
            $data['required'] = 0;
        }


        if (isset($data['show_on_table'])) {
            $data['show_on_table'] = 1;
        } else {
            $data['show_on_table'] = 0;
        }


         if(isset($data['show_on_client_portal'])){
            if(in_array($data['fieldto'],$this->client_portal_fields)){
                $data['show_on_client_portal'] = 1;
            } else {
                $data['show_on_client_portal'] = 0;
            }
        } else {
            $data['show_on_client_portal'] = 0;
        }

        if($data['field_order'] == ''){
            $data['field_order'] = 0;
        }

        $this->db->insert('tblcustomfields',$data);
        $insert_id = $this->db->insert_id();
        if($insert_id){
            logActivity('New Custom Field Added [' . $data['name'] . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update custom field
     * @param mixed $data All $_POST data
     * @return  boolean
     */
    public function update($data,$id){

        if (isset($data['disabled'])) {
            $data['active'] = 0;
            unset($data['disabled']);
        } else {
            $data['active'] = 1;
        }

        if (isset($data['required'])) {
            $data['required'] = 1;
        } else {
            $data['required'] = 0;
        }

        if(isset($data['show_on_pdf'])){
            if(in_array($data['fieldto'],$this->pdf_fields)){
                $data['show_on_pdf'] = 1;
            } else {
                $data['show_on_pdf'] = 0;
            }
        } else {
            $data['show_on_pdf'] = 0;
        }


        if($data['field_order'] == ''){
            $data['field_order'] = 0;
        }

         if(isset($data['show_on_client_portal'])){
            if(in_array($data['fieldto'],$this->client_portal_fields)){
                $data['show_on_client_portal'] = 1;
            } else {
                $data['show_on_client_portal'] = 0;
            }
        } else {
            $data['show_on_client_portal'] = 0;
        }

        if (isset($data['show_on_table'])) {
            $data['show_on_table'] = 1;
        } else {
            $data['show_on_table'] = 0;
        }


        $this->db->where('id',$id);
        $this->db->update('tblcustomfields',$data);
        if($this->db->affected_rows() > 0){
            logActivity('Custom Field Updated [' . $data['name'] . ']');
            return true;
        }

        return false;
    }

        /**
     * @param  integer
     * @return boolean
     * Delete Custom fields
     * All values for this custom field will be deleted from database
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblcustomfields');

        if ($this->db->affected_rows() > 0) {
            // Delete the values
            $this->db->where('fieldid',$id);
            $this->db->delete('tblcustomfieldsvalues');

            logActivity('Custom Field Deleted [' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Change custom field status  / active / inactive
     * @param  mixed $id     customfield id
     * @param  integer $status active or inactive
     */
    public function change_custom_field_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update('tblcustomfields', array(
            'active' => $status
        ));
        logActivity('Custom Field Status Changed [FieldID: ' . $id . ' - Active: ' . $status . ']');
    }

    public function get_pdf_allowed_fields(){
        return $this->pdf_fields;
    }
      public function get_client_portal_allowed_fields(){
        return $this->client_portal_fields;
    }
}
