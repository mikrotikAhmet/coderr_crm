<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/25/16
 * Time: 12:33 PM
 */
class Processors_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblprocessors')->row();
        }

        return $this->db->get('tblprocessors')->result_array();
    }

    public function delete_processor($id){

        if(is_reference_in_table('processorid','tblmerchantprocessors_in',$id)){
            return array('referenced'=>true);
        }

        $this->db->where('id',$id);
        $this->db->delete('tblprocessors');

        if($this->db->affected_rows() > 0){
            logActivity('Processor Delete [ID:'.$id.']');
            return true;
        }

        return false;

    }

    public function add_processor($data){
        $this->db->insert('tblprocessors',$data);
        $insert_id = $this->db->insert_id();

        if($insert_id){
            logActivity('New Processor Created [ID:'.$insert_id.', Name:'.$data['name'].']');
            return true;
        }

        return false;
    }

    public function edit_processor($data){
        $this->db->where('id',$data['id']);
        $this->db->update('tblprocessors',array('name'=>$data['name'],'object'=>$data['object'],'status'=>$data['status']));
        if($this->db->affected_rows() > 0){
            logActivity('Processor Updated [ID:'.$data['id'].']');
            return true;
        }
        return false;
    }

}