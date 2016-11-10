<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CRM_Model extends CI_Model {

	function __construct(){
	parent::__construct();
    	if($this->config->item('installed') == 1){
          $this->db->reconnect();
          $timezone = get_option('default_timezone');
          date_default_timezone_set($timezone);
        }
  }
}
