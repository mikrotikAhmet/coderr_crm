<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cron extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set(get_option('default_timezone'));
        $this->load->model('cron_model');

    }

    public function index()
    {
        $last_cron_run = get_option('last_cron_run');
        if(time() > ($last_cron_run + 300) || $last_cron_run == ''){
           $this->cron_model->run();
        }
   }
}
