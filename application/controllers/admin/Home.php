<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Home extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('home_model');
    }
    /* This is admin home view */
    public function index()
    {
        $this->load->model('departments_model');
        $this->load->model('todo_model');
        $data['departments']               = $this->departments_model->get();
        $data['todos']                     = $this->todo_model->get_todo_items(0, 4);
        $data['todos_finished']            = $this->todo_model->get_todo_items(1, 4);
        $data['upcoming_events_next_week'] = $this->home_model->get_upcoming_events_next_week();
        $data['upcoming_events']           = $this->home_model->get_upcoming_events();
        $total_posts                       = total_rows('tblposts');

        // Total pages for newsfeed
        $data['total_pages']               = $total_posts / 10;
        $data['title']                     = _l('dashboard_string');

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        // Tickets charts
        $data['tickets_reply_by_status'] = json_encode($this->home_model->tickets_awaiting_reply_by_status());
        $data['tickets_awaiting_reply_by_department'] = json_encode($this->home_model->tickets_awaiting_reply_by_department());

        $data['google_ids_calendars'] = $this->misc_model->get_google_calendar_ids();

        $data['bodyclass'] = 'home';
        $this->load->view('admin/home', $data);
    }


    /* Chart weekly payments statistics on home page / ajax */
    public function weekly_payments_statistics($currency)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->home_model->get_weekly_payments_statistics($currency));
            die();
        }
    }
}
