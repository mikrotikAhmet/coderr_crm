<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Update all settings
     * @param  array $data all settings
     * @return integer
     */
    public function update($data)
    {

        $affectedRows = 0;
        $data = do_action('before_settings_updated', $data);
        foreach ($data['settings'] as $name => $val) {

           // Check if the option exists
            $this->db->where('name',$name);
            $exists = $this->db->count_all_results('tbloptions');
            if($exists == 0){
                continue;
            }
            if ($name == 'email_signature') {
                $val = nl2br($val);
            }

            $this->db->where('name', $name);
            $this->db->update('tbloptions', array(
                'value' => $val
            ));

            if($this->db->affected_rows() > 0){
                $affectedRows++;
            }
        }

        if ($affectedRows > 0) {
            logActivity('Settings Updated []');
        }
        return $affectedRows;
    }

    public function add_new_company_pdf_field($data){
        $field = 'custom_company_field_' . trim($data['field']);
        $field =  preg_replace('/\s+/', '_', $field);
        $field = strtolower($field);

        if(add_option($field,$data['value'])){
            return true;
        }
        return false;
    }
}
