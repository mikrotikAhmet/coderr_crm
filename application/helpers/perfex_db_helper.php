<?php

/**
 * Check if field is used in table
 * @param  string  $field column
 * @param  string  $table table name to check
 * @param  integer  $id   ID used
 * @return boolean
 */
function is_reference_in_table($field, $table, $id)
{
    $CI =& get_instance();
    $CI->db->where($field, $id);
    $row = $CI->db->get($table)->row();

    if ($row) {
        return true;
    }

    return false;
}

/*
 * Get Default Currency
 */
function get_default_currency(){

    $CI =& get_instance();

    $CI->db->where('isdefault',1);
    $defaultCurrency = $CI->db->get('tblcurrencies')->row();

    return $defaultCurrency;

}

/**
 * Check if the user is lead creator
 * @since  Version 1.0.4
 * @param  mixed  $leadid leadid
 * @param  mixed  $id staff id (Optional)
 * @return boolean
 */
function is_lead_creator($leadid, $id = '')
{

    if (!is_numeric($id)) {
        $id = get_staff_user_id();
    }

    $is = total_rows('tblleads', array(
        'addedfrom' => $id,
        'id' => $leadid
    ));
    if ($is > 0) {
        return true;
    }
    return false;
}

/**
 * Get client id by lead id
 * @since  Version 1.0.1
 * @param  mixed $id lead id
 * @return mixed     client id
 */
function get_client_id_by_lead_id($id)
{
    $CI =& get_instance();
    $CI->db->select('userid')->from('tblclients')->where('leadid', $id);
    return $CI->db->get()->row()->userid;
}
/**
 * Add option in table
 * @since  Version 1.0.1
 * @param string $name  option name
 * @param string $value option value
 */
function add_option($name, $value = '')
{
    $CI =& get_instance();
    $exists = total_rows('tbloptions', array(
        'name' => $name
    ));

    if ($exists == 0) {
        $CI->db->insert('tbloptions', array(
            'name' => $name,
            'value' => $value
        ));
        $insert_id = $CI->db->insert_id();
        if ($insert_id) {
            return true;
        }
        return false;
    }

    return false;
}

/**
 * Get option value
 * @param  string $name Option name
 * @return mixed
 */
function get_option($name)
{
    $CI =& get_instance();
    $CI->load->library('perfex_base');
    return $CI->perfex_base->get_option($name);
}
/**
 * Get option value from database
 * @param  string $name Option name
 * @return mixed
 */
function update_option($name, $value)
{
    $CI =& get_instance();
    $CI->db->where('name', $name);
    $CI->db->update('tbloptions', array(
        'value' => $value
    ));

    if ($CI->db->affected_rows() > 0) {
        return true;
    }

    return false;
}
/**
 * Delete option
 * @since  Version 1.0.4
 * @param  mixed $id option id
 * @return boolean
 */
function delete_option($id)
{

    $CI =& get_instance();
    $CI->db->where('id', $id);
    $CI->db->delete('tbloptions');

    if ($CI->db->affected_rows() > 0) {
        return true;
    }

    return false;
}
/**
 * Get manually added company custom fields
 * @since Version 1.0.4
 * @return array
 */
function get_company_custom_fields()
{
    $CI =& get_instance();
    $CI->db->like('name', 'custom_company_field_', 'after');
    $fields = $CI->db->get('tbloptions')->result_array();

    $i = 0;
    foreach ($fields as $field) {
        $fields[$i]['label'] = str_replace('custom_company_field_', '', $field['name']);
        $fields[$i]['label'] = str_replace('_', ' ', $fields[$i]['label']);
        $fields[$i]['label'] = ucwords($fields[$i]['label']);
        $i++;
    }
    return $fields;
}

/**
 * Get staff full name
 * @param  string $userid Optional
 * @return string Firstname and Lastname
 */
function get_staff_full_name($userid = '')
{
    $_userid = get_staff_user_id();
    if ($userid !== '') {
        $_userid = $userid;
    }

    $CI =& get_instance();
    $CI->db->where('staffid', $_userid);
    $staff = $CI->db->select('firstname,lastname')->from('tblstaff')->get()->row();
    return $staff->firstname . ' ' . $staff->lastname;
}

/**
 * When ticket will be opened automatically set to open
 * @param integer  $current Current status
 * @param integer  $id      ticketid
 * @param boolean $admin   Admin opened or client opened
 */
function set_ticket_open($current, $id, $admin = true)
{

    if ($current == 1) {
        return;
    }

    $CI =& get_instance();
    $CI->db->where('ticketid', $id);
    $field = 'adminread';

    if ($admin == false) {
        $field = 'clientread';
    }

    $CI->db->update('tbltickets', array(
        $field => 1
    ));
}

/**
 * Get department email address
 * @param  mixed $id department id
 * @return mixed
 */
function get_department_email($id)
{
    $CI =& get_instance();
    $CI->db->where('departmentid', $id);
    return $CI->db->get('tbldepartments')->row()->email;
}
/**
 * Log Activity for everything
 * @param  string $description Activity Description
 * @param  integer $staffid    Who done this activity
 */
function logActivity($description, $staffid = NULL, $cron = false)
{
    $CI =& get_instance();
    $log = array(
        'description' => $description,
        'date' => date('Y-m-d H:i:s')
    );

    if ($cron == false) {
        if ($staffid != NULL && is_numeric($staffid)) {
            $log['staffid'] = $staffid;
        } else {
            if (is_staff_logged_in()) {
                $log['staffid'] = get_staff_user_id();
            } else {
                $log['staffid'] = NULL;
            }

        }
    } else {
        $log['staffid'] = _l('activity_log_when_cron_job');
    }

    $CI->db->insert('tblactivitylog', $log);
}


function add_main_menu_item($options = array(), $parent = ''){
    $default_options = array('name','permission','icon','url','id','custom');
    $data = array();
    for($i = 0; $i < count($default_options);$i++){
        if(isset($options[$default_options[$i]])){
            $data[$default_options[$i]] = $options[$default_options[$i]];
        } else {
            $data[$default_options[$i]] = '';
        }
    }

    $menu = get_option('aside_menu_active');
    $menu = json_decode($menu);
    // check if the id exists
    if($data['id'] == ''){
        $data['id'] = slug_it($data['name']);
    }

     $total_exists = 0;
     foreach($menu->aside_menu_active as $item){
        if($item->id == $data['id']){
            $total_exists++;
        }
     }

     if($total_exists > 0){
        $data['id'] = $data['id'] .'-' . ($total_exists + 1);
     }

    if($parent == ''){
        array_push($menu->aside_menu_active,$data);
    } else {
        $i = 0;
        foreach($menu->aside_menu_active as $item){
            if($item->id == $parent){
                if(!isset($item->children)){
                    $menu->aside_menu_active[$i]->children = array();
                    $menu->aside_menu_active[$i]->children[] = $data;
                    break;
                } else {
                    $menu->aside_menu_active[$i]->children[] = $data;
                    break;
                }
            }
            $i++;
        }
    }
    if(update_option('aside_menu_active',json_encode($menu))){
        return true;
    }

    return false;
}

function add_setup_menu_item($options = array(), $parent = ''){
    $default_options = array('name','permission','icon','url','id','custom');
    $data = array();
    for($i = 0; $i < count($default_options);$i++){
        if(isset($options[$default_options[$i]])){
            $data[$default_options[$i]] = $options[$default_options[$i]];
        } else {
            $data[$default_options[$i]] = '';
        }
    }

    if($data['id'] == ''){
        $data['id'] = slug_it($data['name']);
    }

    $menu = get_option('setup_menu_active');

    $menu = json_decode($menu);

     // check if the id exists
    if($data['id'] == ''){
        $data['id'] = slug_it($data['name']);
    }

     $total_exists = 0;
     foreach($menu->setup_menu_active as $item){
        if($item->id == $data['id']){
            $total_exists++;
        }
     }

     if($total_exists > 0){
        $data['id'] = $data['id'] .'-' . ($total_exists + 1);
     }

    if($parent == ''){
        array_push($menu->setup_menu_active,$data);
    } else {
        $i = 0;
        foreach($menu->setup_menu_active as $item){
            if($item->id == $parent){
                if(!isset($item->children)){
                    $menu->setup_menu_active[$i]->children = array();
                    $menu->setup_menu_active[$i]->children[] = $data;

                    break;
                } else {
                    $menu->setup_menu_active[$i]->children[] = $data;
                    break;
                }
            }
            $i++;
        }
    }

    if(update_option('setup_menu_active',json_encode($menu))){
        return true;
    }

    return false;
}


/**
 * Add user notifications
 * @param array $values array of values [description,fromuserid,touserid,fromcompany,isread]
 */
function add_notification($values)
{

    $CI =& get_instance();

    foreach ($values as $key => $value) {
        $data[$key] = $value;
    }

    $data['fromuserid'] = get_staff_user_id();
    if (isset($data['fromcompany'])) {
        unset($data['fromuserid']);
    }

    $data['date'] = date('Y-m-d H:i:s');
    $CI->db->insert('tblnotifications', $data);
}
/**
 * Helper function to get text question answers
 * @param  integer $questionid
 * @param  itneger $surveyid
 * @return array
 */
function get_text_question_answers($questionid, $surveyid)
{
    $CI =& get_instance();
    $CI->db->select('answer,resultid');
    $CI->db->from('tblsurveyresults');
    $CI->db->where('questionid', $questionid);
    $CI->db->where('surveyid', $surveyid);
    return $CI->db->get()->result_array();
}

/**
 * Helper function to get all knowledbase groups
 * @return array
 */
function get_kb_groups()
{
    $CI =& get_instance();
    return $CI->db->get('tblknowledgebasegroups')->result_array();
}
/**
 * Get all countries stored in database
 * @return array
 */
function get_all_countries()
{
    $CI =& get_instance();
    return $CI->db->get('tblcountries')->result_array();
}
/**
 * Get country short name by passed id
 * @param  mixed $id county id
 * @return mixed
 */
function get_country_short_name($id){

    $CI = &get_instance();
    $CI->db->where('country_id',$id);
    $country = $CI->db->get('tblcountries')->row();

    if($country){
        return $country->iso2;
    }

    return '';
}
/**
 * Helper function to get all knowledge base groups in the parents groups
 * @param  boolean $include_inactive inactive groups all articles if passed true
 * @return array
 */
function get_all_knowledge_base_articles_grouped()
{
    $CI =& get_instance();

    $CI->load->model('knowledge_base_model');
    $groups = $CI->knowledge_base_model->get_kbg('',1);

    $i = 0;
    foreach($groups as $group){
        $CI->db->select('slug,subject,description,tblknowledgebase.active as active_article,articlegroup,articleid');
        $CI->db->from('tblknowledgebase');
        $CI->db->where('articlegroup',$group['groupid']);
        $CI->db->order_by('article_order','asc');
        $articles = $CI->db->get()->result_array();
        if(count($articles) == 0){
            unset($groups[$i]);
            $i++;
            continue;
        }
        $groups[$i]['articles'] = $articles;
        $i++;
    }

    return $groups;
}

/**
 * Helper function to get all announcements for user
 * @param  boolean $staff Is this client or staff
 * @return array
 */
function get_announcements_for_user($staff = true)
{

    if (!is_logged_in()) {
        return array();
    }

    $CI =& get_instance();

    $CI->db->select('firstname,lastname,announcementid,name,message,showtousers,showtostaff,showname,tblannouncements.dateadded,tblannouncements.userid')->join('tblstaff', 'tblstaff.staffid = tblannouncements.userid', 'left')->from('tblannouncements');
    $announcements = $CI->db->get()->result_array();

    $i = 0;
    foreach ($announcements as $annoucement) {
        if ($staff == true) {
            if ($annoucement['showtostaff'] != 1) {
                unset($announcements[$i]);
            }
        } else {
            if ($annoucement['showtousers'] != 1) {
                unset($announcements[$i]);
            }
        }
        $i++;
    }

    // Refresh array keys
    $announcements = array_values($announcements);

    if ($staff == true) {
        $userid = get_staff_user_id();
    } else {
        $userid = get_client_user_id();
    }

    $i = 0;
    foreach ($announcements as $announcement) {
        $CI->db->where('announcementid', $announcement['announcementid']);
        $CI->db->where('staff', $staff);
        $CI->db->where('userid', $userid);
        $dismissed = $CI->db->get('tbldismissedannouncements')->row();

        if ($dismissed) {
            unset($announcements[$i]);
        }

        $i++;
    }


    return $announcements;
}

/**
 * Count total rows on table based on params
 * @param  string $table Table from where to count
 * @param  array  $where
 * @return mixed  Total rows
 */
function total_rows($table, $where = array())
{
    $CI =& get_instance();
    if (is_array($where)) {
        if (sizeof($where) > 0) {
            $CI->db->where($where);
        }
    } else if (strlen($where) > 0) {
        $CI->db->where($where);
    }


    return $CI->db->count_all_results($table);

}

function get_formatted_revenue($total_revenue){

    $revenue = 0;

    if ($total_revenue > 1000000000000) {
        $revenue = round($total_revenue / 1000000000000, 1) . 'T';
    } elseif ($total_revenue > 1000000000) {
        $revenue = round($total_revenue / 1000000000, 1) . 'B';
    } elseif ($total_revenue > 1000000) {
        $revenue = round($total_revenue / 1000000, 1) . 'M';
    } elseif ($total_revenue > 1000) {
        $revenue = round($total_revenue / 1000, 1) . 'K';
    } else {
        $revenue = format_money($total_revenue);
    }

    return $revenue;
}

/**
 * Sum total from table
 * @param  string $table table name
 * @param  array  $attr  attributes
 * @return mixed
 */
function sum_from_table($table, $attr = array())
{
    if (!isset($attr['field'])) {
        show_error('sum_from_table(); function expect field to be passed.');
    }
    $where = '';
    if (isset($attr['where']) && is_array($attr['where'])) {
        $i = 0;
        foreach ($attr['where'] as $key => $val) {
            if ($i == 0) {
                $where .= ' WHERE ' . $key . '="' . $val . '"';
            } else {
                $where .= ' AND ' . $key . '="' . $val . '"';
            }
            $i++;
        }
    }
    $CI =& get_instance();
    $result = $CI->db->query('SELECT sum(' . $attr['field'] . ') as total FROM ' . $table . '' . $where . '')->row();
    return $result->total;
}


/**
 * General function for all datatables, performs search,additional select,join,where,orders
 * @param  array $aColumns           table columns
 * @param  mixed $sIndexColumn       main column in table for bettter performing
 * @param  string $sTable            table name
 * @param  array  $join              join other tables
 * @param  array  $where             perform where in query
 * @param  array  $additionalSelect  select additional fields
 * @param  string $orderby
 * @return array
 */
function data_tables_init($aColumns, $sIndexColumn, $sTable, $join = array(), $where = array(), $additionalSelect = array(), $orderby = '', $groupBy = '')
{

    $CI =& get_instance();
    $__post = $CI->input->post();
    /*
     * Paging
     */
    $sLimit = "";

    if ((is_numeric($CI->input->post('start'))) && $CI->input->post('length') != '-1') {
        $sLimit = "LIMIT " . intval($CI->input->post('start')) . ", " . intval($CI->input->post('length'));
    }

    $_aColumns = array();
    foreach ($aColumns as $column) {
        // if found only one dot
        if (substr_count($column, '.') == 1 && strpos($column,'as') === false) {
            $_column = explode('.', $column);
            if (isset($_column[1])) {
                if (_startsWith($_column[0], 'tbl')) {
                    $_prefix = prefixed_table_fields_wildcard($_column[0], $_column[0], $_column[1]);

                    array_push($_aColumns, $_prefix);
                } else {
                    array_push($_aColumns, $column);
                }
            } else {
                array_push($_aColumns, $_column[0]);

            }
        } else {
            array_push($_aColumns, $column);
        }
    }

    /*
     * Ordering
     */
    $sOrder = "";
    if ($CI->input->post('order') && (!$CI->input->post('custom_sort_by') && !$CI->input->post('custom_view'))) {
        $sOrder = "ORDER BY  ";

        $sOrder .= $aColumns[intval($__post['order'][0]['column'])];

         $__order_column = $sOrder;
         if(strpos($__order_column,'as') !== false){
                $sOrder = strbefore($__order_column,' as');
         }

        $_order = strtoupper($__post['order'][0]['dir']);
        if($_order == 'ASC'){
            $sOrder .= ' ASC';
        } else {
            $sOrder .= ' DESC';
        }
        $sOrder .= ', ';

        $sOrder = substr_replace($sOrder, "", -2);
        if ($sOrder == "ORDER BY") {
            $sOrder = "";
        }

        if($sOrder == '' && $orderby != ''){
            $sOrder = $orderby;
        }

    } else if ($CI->input->post('custom_sort_by')) {
        $sort = $CI->input->post('custom_sort_by');
        if ($sort == 'priority') {
            $sOrder = "ORDER BY CASE Priority
            WHEN 'Urgent' THEN 1
            WHEN 'High' THEN 2
            WHEN 'Medium' THEN 3
            WHEN 'Low' THEN 4
            END";
        } else {
            $sOrder = 'ORDER BY ' . $sort . ' DESC';
        }

    } else {
        $sOrder = $orderby;
    }



    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    $sWhere = "";
    if ((isset($__post['search'])) && $__post['search']['value'] != "") {

        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            $__search_column = $aColumns[$i];
            if(strpos($__search_column,'as') !== false){
                $__search_column = strbefore($__search_column,' as');
            }
            if (($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == "true") {
                $sWhere .= $__search_column . " LIKE '%" . $__post['search']['value'] . "%' OR ";
            }
        }
        if (count($additionalSelect) > 0) {
            foreach ($additionalSelect as $searchAdditionalField) {
                $sWhere .= $searchAdditionalField . " LIKE '%" . $__post['search']['value'] . "%' OR ";
            }
        }

        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';

    } else {
        // Check for custom filtering
        $searchFound = 0;
        $sWhere      = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            if (($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == "true") {
                $_search = $__post['columns'][$i]['search']['value'];
                if ($_search != '') {

                    $valid_date = (bool) strtotime($_search);

                    if ($valid_date) {
                        $_search = to_sql_date($_search);
                    }

                    $sWhere .= $aColumns[$i] . " LIKE '%" . $_search . "%' OR ";

                    if (count($additionalSelect) > 0) {
                        foreach ($additionalSelect as $searchAdditionalField) {
                            $sWhere .= $searchAdditionalField . " LIKE '%" . $_search . "%' OR ";
                        }
                    }
                    $searchFound++;
                }

            }
        }

        if ($searchFound > 0) {
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        } else {
            $sWhere = '';
        }
    }
    /*
     * SQL queries
     * Get data to display
     */

    $_additionalSelect = '';
    if (count($additionalSelect) > 0) {
        $_additionalSelect = ',' . implode(',', $additionalSelect);
    }


    $where = implode(' ', $where);
    if ($sWhere == '') {
        if (_startsWith($where, 'AND')) {
            $where = substr($where, 3);
            $where = 'WHERE' . $where;
        }
    } else {
        if (_startsWith($sWhere, 'WHERE') && ($where != '' && _startsWith($where, 'WHERE'))) {
            $where = substr($where, 5);
            $where = 'AND' . $where;
        }
    }

    $sQuery = "
    SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $_aColumns)) . " " . $_additionalSelect . "
    FROM   $sTable
    " . implode(' ', $join) . "
    $sWhere
    " . $where . "
    $groupBy
    $sOrder
    $sLimit
    ";

    $rResult = $CI->db->query($sQuery)->result_array();
    /* Data set length after filtering */
    $sQuery  = "
    SELECT FOUND_ROWS()
    ";

    $_query         = $CI->db->query($sQuery)->result_array();
    $iFilteredTotal = $_query[0]['FOUND_ROWS()'];

    if (_startsWith($where, 'AND')) {
        $where = 'WHERE ' . substr($where, 3);
    }
    /* Total data set length */
    $sQuery = "
    SELECT COUNT(" . $sTable . '.' . $sIndexColumn . ")
    FROM $sTable " . implode(' ', $join) . ' ' . $where;

    $_query = $CI->db->query($sQuery)->result_array();
    $iTotal = $_query[0]['COUNT(' . $sTable . '.' . $sIndexColumn . ')'];
    /*
     * Output
     */
    $output = array(
        "draw" => $__post['draw'] ? intval($__post['draw']) : 0,
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );

    return array(
        'rResult' => $rResult,
        'output' => $output
    );
}

/**
 * Prefix field name with table ex. table.column
 * @param  string $table
 * @param  string $alias
 * @param  string $field field to check
 * @return string
 */
function prefixed_table_fields_wildcard($table, $alias, $field)
{
    $CI =& get_instance();

    $columns = $CI->db->query("SHOW COLUMNS FROM $table")->result_array();

    $field_names = array();
    foreach ($columns as $column) {
        $field_names[] = $column["Field"];
    }

    $prefixed = array();
    foreach ($field_names as $field_name) {
        if ($field == $field_name) {
            $prefixed[] = "`{$alias}`.`{$field_name}` AS `{$alias}.{$field_name}`";
        }
    }

    return implode(", ", $prefixed);
}

/**
 * Render custom fields for particular area
 * @param  string  $belongs_to belongs to ex.leads,customers,staff
 * @param  boolean $rel_id     the main ID from the table
 * @return string
 */
function render_custom_fields($belongs_to, $rel_id = false,$where = array())
{

    $CI =& get_instance();
    $CI->db->where('active', 1);
    $CI->db->where('fieldto', $belongs_to);
    if(count($where) > 0){
        $CI->db->where($where);
    }
    $CI->db->order_by('field_order', 'DESC');
    $fields = $CI->db->get('tblcustomfields')->result_array();

    $_field = '';
    if (count($fields)) {
        foreach ($fields as $field) {
            $value = '';
            if ($rel_id !== false) {
                $value = get_custom_field_value($rel_id, $field['id'], $belongs_to);
            }
            $_input_attrs = array();
            if ($field['required'] == 1) {
                $_input_attrs['data-custom-field-required'] = true;
            }

            $field_name = ucfirst($field['name']);
            if ($field['type'] == 'input') {
                $_field .= render_input('custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']', $field_name, $value, 'text', $_input_attrs);
            } else if ($field['type'] == 'date_picker') {
                $_field .= render_date_input('custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']', $field_name, $value, $_input_attrs);

            } else if ($field['type'] == 'textarea') {
                $_field .= render_textarea('custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']', $field_name, $value, $_input_attrs);
            } else if ($field['type'] == 'select') {

                 $_select_required = '';
                   if ($field['required'] == 1) {
                        $_select_required = 'data-custom-field-required="true"';
                   }
                $_field .= '<div class="form-group">';
                $_field .= '<label>' . $field_name . '</label>';
                $_field .= '<select '.$_select_required.' name="custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']" class="selectpicker form-control" data-width="100%">';
                $_field .= '<option value=""></option>';
                $options = explode(',', $field['options']);
                foreach ($options as $option) {
                    $option   = trim($option);
                    $selected = '';
                    if ($option == $value) {
                        $selected = ' selected';
                    }
                    $_field .= '<option value="' . $option . '"' . $selected . ''.set_select('custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']',$option).'>' . ucfirst($option) . '</option>';
                }
                $_field .= '</select>';
                $_field .= '</div>';
            }

            $_field .= form_error('custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']');
        }
    }

    return $_field;
}
/**
 * Get custom fields
 * @param  [type] $field_to belongs to ex.leads,customers,staff
 * @return array
 */
function get_custom_fields($field_to, $where = array())
{
    $CI =& get_instance();
    $CI->db->where('fieldto', $field_to);
    if (count($where) > 0) {
        $CI->db->where($where);
    }
    $CI->db->where('active',1);
    $CI->db->order_by('field_order','asc');
    return $CI->db->get('tblcustomfields')->result_array();
}
/**
 * Get custom field value
 * @param  mixed $rel_id   the main ID from the table
 * @param  mixed $field_id field id
 * @param  string $field_to belongs to ex.leads,customers,staff
 * @return mixed
 */
function get_custom_field_value($rel_id, $field_id, $field_to)
{
    $CI =& get_instance();
    $CI->db->where('relid', $rel_id);
    $CI->db->where('fieldid', $field_id);
    $CI->db->where('fieldto', $field_to);
    $row    = $CI->db->get('tblcustomfieldsvalues')->row();
    $result = '';
    if ($row) {
        $result = $row->value;
    }
    return $result;
}
/**
 * Check for custom fields, update on $_POST
 * @param  mixed $rel_id        the main ID from the table
 * @param  array $custom_fields all custom fields with id and values
 * @return boolean
 */
function handle_custom_fields_post($rel_id, $custom_fields)
{

    $affectedRows = 0;
    $CI =& get_instance();
    foreach ($custom_fields as $key => $fields) {
        foreach ($fields as $field_id => $field_value) {
            $CI->db->where('relid', $rel_id);
            $CI->db->where('fieldid', $field_id);
            $CI->db->where('fieldto', $key);
            $row = $CI->db->get('tblcustomfieldsvalues')->row();
            if ($row) {
                // Make necessary checkings for fields
                $CI->db->where('id', $field_id);
                $field_checker = $CI->db->get('tblcustomfields')->row();
                if ($field_checker->type == 'date_picker') {
                    $field_value = to_sql_date($field_value);
                } else if ($field_checker->type == 'textarea') {
                    $field_value = nl2br($field_value);
                }
            }
            if ($row) {
                $CI->db->where('id', $row->id);
                $CI->db->update('tblcustomfieldsvalues', array(
                    'value' => $field_value
                ));
                if ($CI->db->affected_rows() > 0) {
                    $affectedRows++;
                }
            } else {
                $CI->db->insert('tblcustomfieldsvalues', array(
                    'relid' => $rel_id,
                    'fieldid' => $field_id,
                    'fieldto' => $key,
                    'value' => $field_value

                ));
                $insert_id = $CI->db->insert_id();
                if ($insert_id) {
                    $affectedRows++;
                }
            }
        }
    }
    if ($affectedRows > 0) {
        return true;
    }

    return false;
}

/**
 * Get client default language
 * @param  mixed $clientid
 * @return mixed
 */
function get_client_default_language($clientid = ''){

    if(!is_numeric($clientid)){
        $clientid = get_client_user_id();
    }

    $CI = &get_instance();
    $CI->db->select('default_language');
    $CI->db->from('tblclients');
    $CI->db->where('userid',$clientid);
    $client = $CI->db->get()->row();
    if($client){
        return $client->default_language;
    }
    return '';
}
/**
 * Get staff default language
 * @param  mixed $staffid
 * @return mixed
 */
function get_staff_default_language($staffid = ''){

    if(!is_numeric($staffid)){
        $staffid = get_staff_user_id();
    }

    $CI = &get_instance();
    $CI->db->select('default_language');
    $CI->db->from('tblstaff');
    $CI->db->where('staffid',$staffid);
    $staff = $CI->db->get()->row();

    if($staff){
        return $staff->default_language;
    }
    return '';
}

function get_client_default_currency($merchantid){

    $CI =& get_instance();
    $CI->db->where('id',$merchantid);
    $merchant = $CI->db->get('tblmerchants')->row();


    $CI->db->where('userid',$merchant->userid);
    $client = $CI->db->get('tblclients')->row();

    $CI->db->where('id',$client->default_currency);
    $currencies = $CI->db->get('tblcurrencies')->row();

    return $currencies->name;

}

function get_gross_volume($merchantid){

    $CI = &get_instance();

    $sql = "SELECT SUM(settlement) as grossVolume FROM tbltransactions WHERE method IN('Payment','Capture') AND status = '0' AND merchantid = '".(int) $merchantid."'";

    $grossVolume = $CI->db->query($sql)->row();

    return $grossVolume->grossVolume;


}

function get_cc_count_today($userid=0){

    $ci = &get_instance();

    $sql = "SELECT COUNT(*) AS total FROM `tbltransactions` WHERE method IN('Payment','Capture') AND status = '0' AND (payment = '1' OR captured = '1') AND DATE(date_added) = DATE(NOW())";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_cc_count_week($userid=0){

    $ci = &get_instance();

    $sql = "SELECT COUNT(*) AS total FROM `tbltransactions` WHERE method IN('Payment','Capture') AND status = '0' AND (payment = '1' OR captured = '1') AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-7 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_cc_count_month($userid=0){

    $ci = &get_instance();

    $sql = "SELECT COUNT(*) AS total FROM `tbltransactions` WHERE method IN('Payment','Capture') AND status = '0' AND (payment = '1' OR captured = '1') AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-30 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_cc_count_year($userid=0){

    $ci = &get_instance();

    $sql = "SELECT COUNT(*) AS total FROM `tbltransactions` WHERE method IN('Payment','Capture') AND status = '0' AND (payment = '1' OR captured = '1') AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-365 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_revenue_count_today($userid=0){

    $ci = &get_instance();

    $sql = "SELECT SUM(settlement) AS total FROM `tbltransactions` WHERE method IN('Payment','Capture') AND status = '0' AND (payment = '1' OR captured = '1') AND DATE(date_added) = DATE(NOW())";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_revenue_count_week($userid=0){

    $ci = &get_instance();

    $sql = "SELECT SUM(settlement) AS total FROM `tbltransactions` WHERE method IN('Payment','Capture') AND status = '0' AND (payment = '1' OR captured = '1') AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-7 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_revenue_count_month($userid=0){

    $ci = &get_instance();

    $sql = "SELECT SUM(settlement) AS total FROM `tbltransactions` WHERE method IN('Payment','Capture') AND status = '0' AND (payment = '1' OR captured = '1') AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-30 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_revenue_count_year($userid=0){

    $ci = &get_instance();

    $sql = "SELECT SUM(settlement) AS total FROM `tbltransactions` WHERE method IN('Payment','Capture') AND status = '0' AND (payment = '1' OR captured = '1') AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-365 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_refund_count_today($userid=0){

    $ci = &get_instance();

    $sql = "SELECT COUNT(*) AS total FROM `tbltransactions` WHERE method IN('Payment','Capture') AND status = '0' AND refunded = '1' AND DATE(date_added) = DATE(NOW())";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_refund_count_week($userid=0){

    $ci = &get_instance();

    $sql = "SELECT COUNT(*) AS total FROM `tbltransactions` WHERE method IN('Payment','Capture') AND  status = '0' AND refunded = '1' AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-7 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_refund_count_month($userid=0){

    $ci = &get_instance();

    $sql = "SELECT COUNT(*) AS total FROM `tbltransactions` WHERE method IN('Payment','Capture') AND  status = '0' AND refunded = '1' AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-30 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_refund_count_year($userid=0){

    $ci = &get_instance();

    $sql = "SELECT COUNT(*) AS total FROM `tbltransactions` WHERE method IN('Payment','Capture') AND  status = '0' AND refunded = '1' AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-365 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_refund_revenue_today($userid=0){

    $ci = &get_instance();

    $sql = "SELECT SUM(settlement) AS total FROM `tbltransactions` WHERE method IN('Payment','Capture') AND  status = '0' AND refunded = '1' AND DATE(date_added) = DATE(NOW())";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_refund_revenue_week($userid=0){

    $ci = &get_instance();

    $sql = "SELECT SUM(settlement) AS total FROM `tbltransactions` WHERE method IN('Payment','Capture') AND  status = '0' AND refunded = '1' AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-7 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_refund_revenue_month($userid=0){

    $ci = &get_instance();

    $sql = "SELECT SUM(settlement) AS total FROM `tbltransactions` WHERE method IN('Payment','Capture') AND  status = '0' AND refunded = '1' AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-30 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_refund_revenue_year($userid=0){

    $ci = &get_instance();

    $sql = "SELECT SUM(settlement) AS total FROM `tbltransactions` WHERE method IN('Payment','Capture') AND  status = '0' AND refunded = '1' AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-365 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_decline_count_today($userid=0){

    $ci = &get_instance();

    $sql = "SELECT COUNT(*) AS total FROM `tbltransactions` WHERE status = '1' AND DATE(date_added) = DATE(NOW())";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_decline_count_week($userid=0){

    $ci = &get_instance();

    $sql = "SELECT COUNT(*) AS total FROM `tbltransactions` WHERE status = '1' AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-7 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_decline_count_month($userid=0){

    $ci = &get_instance();

    $sql = "SELECT COUNT(*) AS total FROM `tbltransactions` WHERE status = '1' AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-30 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_decline_count_year($userid=0){

    $ci = &get_instance();

    $sql = "SELECT COUNT(*) AS total FROM `tbltransactions` WHERE status = '1' AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-365 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_decline_revenue_today($userid=0){

    $ci = &get_instance();

    $sql = "SELECT SUM(settlement) AS total FROM `tbltransactions` WHERE status = '1' AND DATE(date_added) = DATE(NOW())";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_decline_revenue_week($userid=0){

    $ci = &get_instance();

    $sql = "SELECT SUM(settlement) AS total FROM `tbltransactions` WHERE status = '1' AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-7 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_decline_revenue_month($userid=0){

    $ci = &get_instance();

    $sql = "SELECT SUM(settlement) AS total FROM `tbltransactions` WHERE status = '1' AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-30 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_decline_revenue_year($userid=0){

    $ci = &get_instance();

    $sql = "SELECT SUM(settlement) AS total FROM `tbltransactions` WHERE status = '1' AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-365 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_chargeback_count_today($userid=0){

    $ci = &get_instance();

    $sql = "SELECT COUNT(*) AS total FROM `tbltransactions` WHERE status = '0' AND method IN('Payment','Capture') AND retrived = '1' AND DATE(date_added) = DATE(NOW())";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_chargeback_count_week($userid=0){

    $ci = &get_instance();

    $sql = "SELECT COUNT(*) AS total FROM `tbltransactions` WHERE status = '0' AND method IN('Payment','Capture') AND retrived = '1' AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-7 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_chargeback_count_month($userid=0){

    $ci = &get_instance();

    $sql = "SELECT COUNT(*) AS total FROM `tbltransactions` WHERE status = '0' AND method IN('Payment','Capture') AND retrived = '1' AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-30 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_chargeback_count_year($userid=0){

    $ci = &get_instance();

    $sql = "SELECT COUNT(*) AS total FROM `tbltransactions` WHERE status = '0' AND method IN('Payment','Capture') AND retrived = '1' AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-365 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_chargeback_revenue_today($userid=0){

    $ci = &get_instance();

    $sql = "SELECT SUM(settlement) AS total FROM `tbltransactions` WHERE status = '0' AND method IN('Payment','Capture') AND retrived = '1' AND DATE(date_added) = DATE(NOW())";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_chargeback_revenue_week($userid=0){

    $ci = &get_instance();

    $sql = "SELECT SUM(settlement) AS total FROM `tbltransactions` WHERE status = '0' AND method IN('Payment','Capture') AND retrived = '1' AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-7 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_chargeback_revenue_month($userid=0){

    $ci = &get_instance();

    $sql = "SELECT SUM(settlement) AS total FROM `tbltransactions` WHERE status = '0' AND method IN('Payment','Capture') AND retrived = '1' AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-30 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }

    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_chargeback_revenue_year($userid=0){

    $ci = &get_instance();

    $sql = "SELECT SUM(settlement) AS total FROM `tbltransactions` WHERE status = '0' AND method IN('Payment','Capture') AND retrived = '1' AND DATE(date_added) >= DATE('" . date('Y-m-d', strtotime('-365 days')) . "')";

    if ($userid){
        $sql .= " AND userid = '".(int) $userid."'";
    }
    $total = $ci->db->query($sql)->row();

    return $total->total;

}

function get_merchant_id(){

    $ci = &get_instance();

    $ci->db->where('userid',get_client_user_id());
    $merchant = $ci->db->get('tblmerchants')->row();

    return $merchant->id;

}