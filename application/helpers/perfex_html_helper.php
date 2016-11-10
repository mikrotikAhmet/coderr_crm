<?php
/**
 * Remove <br /> html tags from string to show in textarea with new linke
 * @param  string $text
 * @return string formated text
 */
function clear_textarea_breaks($text)
{
    $_text  = '';
    $_text  = $text;
    $breaks = array(
        "<br />",
        "<br>",
        "<br/>"
    );
    $_text  = str_ireplace($breaks, "", $_text);
    $_text  = trim($_text);
    return $_text;
}
/**
 * For more readable code created this function to render only yes or not values for settings
 * @param  string $option_value option from database to compare
 * @param  string $label        input label
 * @param  string $tooltip      tooltip
 */
function render_yes_no_option($option_value, $label, $tooltip = '')
{
    ob_start();
    if ($tooltip != '') {
        $tooltip = ' data-toggle="tooltip" title="' . _l($tooltip) . '"';
    }
?>
        <div class="form-group"<?php
    echo $tooltip;
?>>
            <label for="<?php
    echo $option_value;
?>" class="control-label clearfix"><?php
    echo _l($label);
?></label>
            <div class="radio radio-primary radio-inline">
                <input type="radio" name="settings[<?php
    echo $option_value;
?>]" value="1" <?php
    if (get_option($option_value) == '1') {
        echo 'checked';
    }
?>>
                <label><?php
    echo _l('settings_yes');
?></label>
            </div>
            <div class="radio radio-primary radio-inline">
                <input type="radio" name="settings[<?php
    echo $option_value;
?>]" value="0" <?php
    if (get_option($option_value) == '0') {
        echo 'checked';
    }
?>>
                <label><?php
    echo _l('settings_no');
?></label>
            </div>
        </div>
        <?php
    $settings = ob_get_contents();
    ob_end_clean();
    echo $settings;
}

function init_relation_tasks_table($table_attributes = array())
{
    $table_data = array(
        _l('tasks_dt_name'),
        _l('tasks_dt_datestart')
    );

    $custom_fields = get_custom_fields('tasks', array(
        'show_on_table' => 1
    ));
    foreach ($custom_fields as $field) {
        array_push($table_data, $field['name']);
    }
    $table = render_datatable($table_data, 'rel-tasks', array(), $table_attributes);
    $table .= '<div class="modal fade task-related-modal-single" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>';
    return $table;
}

function get_relation_data($type, $rel_id = '')
{

    $CI =& get_instance();
    $data = array();
    if ($type == 'customer' || $type == 'customers') {
        $CI->load->model('clients_model');
        $data = $CI->clients_model->get($rel_id, 1);
    } else if ($type == 'invoice') {
        $CI->load->model('invoices_model');
        $data = $CI->invoices_model->get($rel_id);
    } else if ($type == 'estimate') {
        $CI->load->model('estimates_model');
        $data = $CI->estimates_model->get($rel_id);
    } else if ($type == 'contract' || $type == 'contracts') {
        $CI->load->model('contracts_model');
        $data = $CI->contracts_model->get($rel_id);
    } else if ($type == 'ticket') {
        $CI->load->model('tickets_model');
        $data = $CI->tickets_model->get($rel_id);
    } else if ($type == 'expense' || $type == 'expenses') {
        $CI->load->model('expenses_model');
        $data = $CI->expenses_model->get($rel_id);
    } else if ($type == 'lead' || $type == 'leads') {
        $CI->load->model('leads_model');
        $data = $CI->leads_model->get($rel_id);
    } else if ($type == 'proposal') {
        $CI->load->model('proposals_model');
        $data = $CI->proposals_model->get($rel_id);
    } else if ($type == 'tasks') {
        $CI->load->model('tasks_model');
        $data = $CI->tasks_model->get($rel_id);
    } else if ($type == 'staff') {
        $CI->load->model('staff_model');
        $data = $CI->staff_model->get($rel_id);
    }

    return $data;
}

function get_relation_values($relation, $type)
{
    if ($relation == '') {
        return;
    }
    $name = '';
    $id   = '';
    $link = '';
    if ($type == 'customer' || $type == 'customers') {
        if (is_array($relation)) {
            $id   = $relation['userid'];
            $name = $relation['firstname'] . ' ' . $relation['lastname'];
        } else {
            $id   = $relation->userid;
            $name = $relation->firstname . ' ' . $relation->lastname;
        }
        $link = admin_url('clients/client/' . $id);
    } else if ($type == 'invoice') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = format_invoice_number($id);
        } else {
            $id   = $relation->id;
            $name = format_invoice_number($id);
        }
        $link = admin_url('invoices/list_invoices/' . $id);
    } else if ($type == 'estimate') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = format_estimate_number($id);
        } else {
            $id   = $relation->id;
            $name = format_estimate_number($id);
        }
        $link = admin_url('estimates/list_estimates/' . $id);
    } else if ($type == 'contract' || $type == 'contracts') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = $relation['subject'];
        } else {
            $id   = $relation->id;
            $name = $relation->subject;
        }
        $link = admin_url('contracts/contract/' . $id);
    } else if ($type == 'ticket') {
        if (is_array($relation)) {
            $id   = $relation['ticketid'];
            $name = '#' . $relation['ticketid'];
        } else {
            $id   = $relation->ticketid;
            $name = '#' . $relation->ticketid;
        }
        $link = admin_url('tickets/ticket/' . $id);
    } else if ($type == 'expense' || $type == 'expenses') {
        if (is_array($relation)) {
            $id   = $relation['expenseid'];
            $name = $relation['category_name'] . ' - ' . _format_number($relation['amount']);

        } else {
            $id   = $relation->expenseid;
            $name = $relation->category_name . ' - ' . _format_number($relation->amount);
        }
        $link = admin_url('expenses/list_expenses/' . $id);
    } else if ($type == 'lead' || $type == 'leads') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = $relation['name'];
            if ($relation['email'] != '') {
                $name .= ' - ' . $relation['email'];
            }
        } else {
            $id   = $relation->id;
            $name = $relation->name;
            if ($relation->email != '') {
                $name .= ' - ' . $relation->email;
            }
        }
        $link = admin_url('leads/lead/' . $id);
    } else if ($type == 'proposal') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = $relation['subject'];
        } else {
            $id   = $relation->id;
            $name = $relation->subject;
        }
        $link = admin_url('proposals/proposal/' . $id);
    } else if ($type == 'tasks') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = $relation['name'];
        } else {
            $id   = $relation->id;
            $name = $relation->name;
        }
        $link = admin_url('tasks/list_tasks/' . $id);
    } else if ($type == 'staff') {

        if (is_array($relation)) {
            $id   = $relation['staffid'];
            $name = $relation['firstname'] . ' ' . $relation['lastname'];
        } else {
            $id   = $relation->staffid;
            $name = $relation->firstname . ' ' . $relation->lastname;
        }
        $link = admin_url('profile/' . $id);

    }

    return array(
        'name' => $name,
        'id' => $id,
        'link' => $link
    );
}

function init_relation_options($data, $type, $rel_id = '')
{
    echo '<option value=""></option>';
    foreach ($data as $relation) {
        $selected        = '';
        $relation_values = get_relation_values($relation, $type);
        if ($rel_id == $relation_values['id']) {
            $selected = ' selected';
        }
        echo '<option value="' . $relation_values['id'] . '"' . $selected . '>' . $relation_values['name'] . '</option>';
    }
}
function render_input($name, $label = '', $value = '', $type = 'text', $input_attrs = array(), $form_group_attr = array(), $form_group_class = '', $input_class = '')
{
    $input            = '';
    $_form_group_attr = '';
    $_input_attrs     = '';

    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }

        $_input_attrs .= $key . '=' . '"' . $val . '"';
    }
    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }

        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }

    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }

    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $_label = _l($label);

        if (mb_strpos($_label, 'translate_not_found_') !== false) {
            $_label = $label;
        }
        $input .= '<label for="' . $name . '" class="control-label">' . $_label . '</label>';
    }

    $input .= '<input type="' . $type . '" id="' . $name . '" name="' . $name . '" class="form-control' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name,$value) . '">';
    $input .= '</div>';

    return $input;
}
function render_date_input($name, $label = '', $value = '', $input_attrs = array(), $form_group_attr = array(), $form_group_class = '', $input_class = '')
{
    $input            = '';
    $_form_group_attr = '';
    $_input_attrs     = '';

    foreach ($input_attrs as $key => $val) {

        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }


        $_input_attrs .= $key . '=' . '"' . $val . '"';
    }

    foreach ($form_group_attr as $key => $val) {

        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }

        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }

    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }
    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {

        $_label = _l($label);

        if (mb_strpos($_label, 'translate_not_found_') !== false) {
            $_label = $label;
        }


        $input .= '<label for="' . $name . '" class="control-label">' . $_label . '</label>';
    }

    $input .= '<div class="input-group date">';
    $input .= '<div class="input-group-addon">
               <i class="fa fa-calendar calendar-icon"></i>
               </div>';
    $input .= '<input type="text" id="' . $name . '" name="' . $name . '" class="form-control datepicker' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name,_d($value)) . '">';
    $input .= '</div>';
    $input .= '</div>';

    return $input;
}
function render_textarea($name, $label = '', $value = '', $textarea_attrs = array(), $form_group_attr = array(), $form_group_class = '', $textarea_class = '')
{
    $textarea         = '';
    $_form_group_attr = '';
    $_textarea_attrs  = '';

    if (!isset($textarea_attrs['rows'])) {
        $textarea_attrs['rows'] = 4;
    }

    foreach ($textarea_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }

        $_textarea_attrs .= $key . '=' . '"' . $val . '"';
    }

    foreach ($form_group_attr as $key => $val) {

        if ($key == 'title') {
            $val = _l($val);
        }

        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }

    if (!empty($textarea_class)) {
        $textarea_class = ' ' . $textarea_class;
    }

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }

    $textarea .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {

        $_label = _l($label);
        if (mb_strpos($_label, 'translate_not_found_') !== false) {
            $_label = $label;
        }
        $textarea .= '<label for="' . $name . '" class="control-label">' . $_label . '</label>';
    }
    $textarea .= '<textarea id="' . $name . '" name="' . $name . '" class="form-control' . $textarea_class . '" ' . $_textarea_attrs . '>' . set_value($name,clear_textarea_breaks($value)) . '</textarea>';
    $textarea .= '</div>';

    return $textarea;

}
function render_select($name, $options, $option_attrs = array(), $label = '', $selected = '', $select_attrs = array(), $form_group_attr = array(), $form_group_class = '', $select_class = '')
{
    $select           = '';
    $_form_group_attr = '';
    $_select_attrs    = '';


    if (!isset($select_attrs['data-width'])) {
        $select_attrs['data-width'] = '100%';
    }
    foreach ($select_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_select_attrs .= $key . '=' . '"' . $val . '"';
    }

    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }

    if (!empty($select_class)) {
        $select_class = ' ' . $select_class;
    }

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }

    $select .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $_label = _l($label);
        if (mb_strpos($_label, 'translate_not_found_') !== false) {
            $_label = $label;
        }
        $select .= '<label for="' . $name . '" class="control-label">' . $_label . '</label>';
    }

    $select .= '<select id="' . $name . '" name="' . $name . '" class="selectpicker' . $select_class . '" ' . $_select_attrs . ' data-live-search="true">';
    $select .= '<option value=""></option>';

    foreach ($options as $option) {

        $val       = '';
        $_selected = '';

        $key = '';

        if (isset($option[$option_attrs[0]]) && !empty($option[$option_attrs[0]])) {
            $key = $option[$option_attrs[0]];
        }

        if (!is_array($option_attrs[1])) {
            $val = $option[$option_attrs[1]];
        } else {
            foreach ($option_attrs[1] as $_val) {
                $val .= $option[$_val] . ' ';
            }
        }
        $val           = trim($val);
        $data_sub_text = '';
        if (!is_array($selected)) {
            if ($selected != '') {
                if ($selected == $key) {
                    $_selected = ' selected';
                }
            }
        } else {
            foreach ($selected as $id) {
                if ($key == $id) {
                    $_selected = ' selected';
                }
            }
        }

        if (isset($option_attrs[2])) {
            $data_sub_text = ' data-subtext=' . '"' . $option[$option_attrs[2]] . '"';
        }
        $select .= '<option value="' . $key . '"' . $_selected . '' . $data_sub_text . '>' . $val . '</option>';
    }
    $select .= '</select>';
    $select .= '</div>';

    return $select;
}
/**
 * Init admin head
 * @param  boolean $aside should include aside
 */
function init_head($aside = true)
{
    $CI =& get_instance();
    $CI->load->view('admin/includes/head');
    $CI->load->view('admin/includes/header');
    $CI->load->view('admin/includes/customizer-sidebar');
    if ($aside == true) {
        $CI->load->view('admin/includes/aside');
    }

}
/**
 * Init admin footer/tails
 */
function init_tail()
{
    $CI =& get_instance();
    $CI->load->view('admin/includes/scripts');
}

/**
 * Render table used for datatables
 * @param  array  $headings           [description]
 * @param  string $class              table class / added prefix table-$class
 * @param  array  $additional_classes
 * @return string                     formated table
 */
function render_datatable($headings = array(), $class = '', $additional_classes = array(''), $table_attributes = array())
{
    $_additional_classes = '';
    $_table_attributes   = '';
    if (count($additional_classes) > 0) {
        $_additional_classes = ' ' . implode(' ', $additional_classes);
    }


    $CI =& get_instance();
    $CI->load->library('user_agent');
    $browser = $CI->agent->browser();
    $IEfix   = '';
    if ($browser == 'Internet Explorer') {
        $IEfix = ' ie-dt-fix';
    }

    foreach ($table_attributes as $key => $val) {
        $_table_attributes .= $key . '=' . '"' . $val . '"';
    }


    $_hide_header = '';
    if (in_array('hide-header', $additional_classes)) {
        $_hide_header = 'dt-hide-header';
    }
    $table = '<div class="table-responsive mtop15' . $IEfix . '"><table ' . $_table_attributes . ' class="table table-striped table-' . $class . ' animated fadeIn' . $_additional_classes . '">';
    $table .= '<thead class="' . $_hide_header . '">';
    $table .= '<tr>';

    foreach ($headings as $heading) {
        $table .= '<th>' . $heading . '</th>';
    }

    $table .= '</tr>';
    $table .= '</thead>';
    $table .= '<tbody></tbody>';
    $table .= '</table></div>';

    echo $table;

}
/**
 * Get company logo from company uploads folder
 * @param  string $url     href url of image
 * @param  string $classes Additional classes on href
 */
function get_company_logo($url = '', $classes = '')
{
    $company_logo = get_option('company_logo');
    $company_name = get_option('companyname');

    if ($url == '') {
        $url = site_url();
    } else {
        $url = site_url($url);
    }

    if ($company_logo != '') {
?>
       <a href="<?php
        echo $url;
?>" class="<?php
        echo $classes;
?> logo">
           <img src="<?php
        echo site_url('uploads/company/' . $company_logo);
?>" alt="<?php
        echo $company_name;
?>"></a>
           <?php
    } else if ($company_name != '') {
?>
           <a href="<?php
        echo $url;
?>" class="<?php
        echo $classes;
?> logo"><?php
        echo $company_name;
?></a>
           <?php
    } else {
        echo '';
    }
}

/**
 * Get staff profile image
 * @param  boolean $id      staff ID
 * @param  array   $classes Additional image classes
 * @param  string  $type    small/thumb
 * @return string           Image link
 */
function staff_profile_image($id = false, $classes = array('staff-profile-image'), $type = 'small',$img_attrs = array())
{

    $CI =& get_instance();

    $CI->db->select('profile_image,firstname,lastname');
    $CI->db->where('staffid', $id);
    $result = $CI->db->get('tblstaff')->row();

     $_attributes = '';
    foreach ($img_attrs as $key => $val) {
        $_attributes .= $key . '=' . '"' . $val . '" ';
    }

    if ($result->profile_image !== null) {
        $profile_image = '<img '.$_attributes.' src="' . site_url('uploads/staff_profile_images/' . $id . '/' . $type . '_' . $result->profile_image) . '" class="' . implode(' ', $classes) . '" alt="' . $result->firstname . ' ' . $result->lastname . '" />';
    } else {
        $profile_image = '<img src="' . site_url('assets/images/user-placeholder.jpg') . '" '.$_attributes.' class="' . implode(' ', $classes) . '" alt="' . $result->firstname . ' ' . $result->lastname . '" />';
    }

    return $profile_image;
}
/**
 * Generate small icon button / font awesome
 * @param  string $url        href url
 * @param  string $type       icon type
 * @param  string $class      button class
 * @param  array  $attributes additional attributes
 * @return string
 */
function icon_btn($url = '', $type = '', $class = 'btn-default', $attributes = array())
{

    $_attributes = '';
    foreach ($attributes as $key => $val) {
        $_attributes .= $key . '=' . '"' . $val . '" ';
    }

    $_url = '#';
    if($url !== '#'){
        $_url = site_url($url);
    }
    return '<a href="' . $_url . '" class="btn ' . $class . ' btn-icon" ' . $_attributes . '><i class="fa fa-' . $type . '"></i></a>';
}
/**
 * Render admin tickets table structure
 */
function AdminTicketsTableStructure()
{
    ob_start();
?>
    <div class="table-responsive mtop15">
    <table class="table tickets-table animated fadeIn">
      <thead>
        <tr>
         <th><?php
    echo _l('id');
?></th>
         <th><?php
    echo _l('ticket_dt_subject');
?></th>
         <th><?php
    echo _l('ticket_dt_department');
?></th>
         <?php
    if (get_option('services') == 1) {
?>
         <th><?php
        echo _l('ticket_dt_service');
?></th>
         <?php
    }
?>
         <th><?php
    echo _l('ticket_dt_submitter');
?></th>
         <th><?php
    echo _l('ticket_dt_status');
?></th>
         <th><?php
    echo _l('ticket_dt_priority');
?></th>
         <th><?php
    echo _l('ticket_dt_last_reply');
?></th>
<th><?php
    echo _l('ticket_date_created');
?></th>
         <th><?php
    echo _l('options');
?></th>
     </tr>
 </thead>
 <tbody>
 </tbody>
</table>
</div>
<?php
    $table = ob_get_contents();
    ob_end_clean();
    return $table;
}
/**
 * Render admin tickets datatables
 * @param mixed $vars array
 */
function AdminTicketsTable($vars)
{
    extract($vars);
    $aColumns         = array(
        'tbltickets.ticketid',
        'subject',
        'tbldepartments.name',
        'tblclients.firstname',
        'tblticketstatus.name',
        'tblpriorities.name',
        'lastreply',
        'tbltickets.date'
    );
    $additionalSelect = array(
        'adminread',
        'tblclients.lastname',
        'tbltickets.userid',
        'tblticketassignments.staffid',
        'statuscolor',
        'tbltickets.name',
        'tbltickets.email',
        'tbltickets.userid'
    );

    if (get_option('services') == 1) {
        array_splice($aColumns, 3, 0, 'tblservices.name');
    }

    $join = array(
        'LEFT JOIN tblservices ON tblservices.serviceid = tbltickets.service',
        'LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbltickets.department',
        'LEFT JOIN tblticketstatus ON tblticketstatus.ticketstatusid = tbltickets.status',
        'LEFT JOIN tblclients ON tblclients.userid = tbltickets.userid',
        'LEFT JOIN tblticketassignments ON tblticketassignments.ticketid = tbltickets.ticketid',
        'LEFT JOIN tblstaff ON tblstaff.staffid = tblticketassignments.staffid',
        'LEFT JOIN tblpriorities ON tblpriorities.priorityid = tbltickets.priority'
    );

    $where = array();
    if (isset($status) && is_numeric($status)) {
        array_push($where, 'AND status = ' . $status);
    }

    if (isset($userid)) {
        array_push($where, 'AND tbltickets.userid = ' . $userid);
    } else if(isset($by_email)){
        array_push($where, 'AND tbltickets.email = "'.$by_email.'"');
    }

    if (isset($where_not_ticket_id)) {
        array_push($where, 'AND tbltickets.ticketid != ' . $where_not_ticket_id);
    }

    // If userid is set, the the view is in client profile, should be shown all tickets
    if (!is_admin() && !isset($userid)) {
        if (get_option('staff_access_only_assigned_departments') == 1) {
            $CI =& get_instance();
            $CI->load->model('departments_model');
            $departments = $CI->departments_model->get_staff_departments(get_staff_user_id(), true);
            if (count($departments) > 0) {
                array_push($where, 'AND department IN (SELECT departmentid FROM tblstaffdepartments WHERE departmentid IN (' . implode(',', $departments) . '))');
            }
        }
    }

    $sIndexColumn = 'ticketid';
    $sTable       = 'tbltickets';

    $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
    $output  = $result['output'];
    $rResult = $result['rResult'];


    foreach ($rResult as $aRow) {
        $row = array();

        for ($i = 0; $i < count($aColumns); $i++) {
            $_data = $aRow[$aColumns[$i]];
            if ($aColumns[$i] == 'lastreply') {
                if ($aRow[$aColumns[$i]] == NULL) {
                    $_data = _l('ticket_no_reply_yet');
                } else {
                    $_data = time_ago_specific($aRow[$aColumns[$i]]);
                }
            } else if ($aColumns[$i] == 'subject' || $aColumns[$i] == 'tbltickets.ticketid') {
                $_data = '<a href="' . admin_url('tickets/ticket/' . $aRow['tbltickets.ticketid']) . '">' . $_data . '</a>';
                // Ticket is assigned
                if ($aRow['staffid'] !== NULL) {
                    if ($aColumns[$i] != 'tbltickets.ticketid') {
                        $_data .= '<br /><a href="' . admin_url('profile/' . $aRow['staffid']) . '" data-toggle="tooltip" title="' . get_staff_full_name($aRow['staffid']) . '" class="mtop10 inline-block">' . staff_profile_image($aRow['staffid'], array(
                            'staff-profile-image-small'
                        )) . '</a>';
                    }
                }

            } else if ($aColumns[$i] == 'tblclients.firstname') {
                if ($aRow['userid'] != 0) {
                    $_data = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '">' . $_data . ' ' . $aRow['lastname'] . '</a>';
                } else {
                    $_data = $aRow['name'];
                }
            } else if ($aColumns[$i] == 'tblticketstatus.name') {
                $_data = '<span class="label pull-left" style="border:1px solid ' . $aRow["statuscolor"] . '; color:' . $aRow['statuscolor'] . '">' . $_data . '</span>';
            } else if ($aColumns[$i] == 'tbltickets.date') {
                $_data = _dt($_data);
            }

            $row[] = $_data;

            if ($aRow['adminread'] == 0) {
                $row['DT_RowClass'] = 'text-danger bold';
            }
        }

        $options = icon_btn('admin/tickets/ticket/' . $aRow['tbltickets.ticketid'], 'pencil-square-o');
        $row[]   = $options .= icon_btn('admin/tickets/delete/' . $aRow['tbltickets.ticketid'], 'remove', 'btn-danger');

        $output['aaData'][] = $row;
    }

    echo json_encode($output);
    die();
}
/**
 * Callback for check_for_links
 */
function _make_url_clickable_cb($matches)
{
    $ret = '';
    $url = $matches[2];
    if (empty($url))
        return $matches[0];
    // removed trailing [.,;:] from URL
    if (in_array(substr($url, -1), array(
        '.',
        ',',
        ';',
        ':'
    )) === true) {
        $ret = substr($url, -1);
        $url = substr($url, 0, strlen($url) - 1);
    }
    return $matches[1] . "<a href=\"$url\" rel=\"nofollow\" target='_blank'>$url</a>" . $ret;
}
/**
 * Callback for check_for_links
 */
function _make_web_ftp_clickable_cb($matches)
{
    $ret  = '';
    $dest = $matches[2];
    $dest = 'http://' . $dest;
    if (empty($dest))
        return $matches[0];
    // removed trailing [,;:] from URL
    if (in_array(substr($dest, -1), array(
        '.',
        ',',
        ';',
        ':'
    )) === true) {
        $ret  = substr($dest, -1);
        $dest = substr($dest, 0, strlen($dest) - 1);
    }
    return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\" target='_blank'>$dest</a>" . $ret;
}
/**
 * Callback for check_for_links
 */
function _make_email_clickable_cb($matches)
{
    $email = $matches[2] . '@' . $matches[3];
    return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
}
/**
 * Check for links/emails/ftp in string to wrap in href
 * @param  string $ret
 * @return string      formatted string with href in any found
 */
function check_for_links($ret)
{
    $ret = ' ' . $ret;
    // in testing, using arrays here was found to be faster
    $ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_url_clickable_cb', $ret);
    $ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_web_ftp_clickable_cb', $ret);
    $ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret);
    // this one is not in an array because we need it to run last, for cleanup of accidental links within links
    $ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
    $ret = trim($ret);
    return $ret;
}

/**
 * Strip tags
 * @param  string $html string to strip tags
 * @return string
 */
function _strip_tags($html)
{
    return strip_tags($html, '<br>,<em>,<p>,<ul>,<li>,<h3>,<pre>,<code>,<a>,<img>,<ol>,<strong>,<blockquote>');
}

/**
 * Get mime class by mime - admin system function
 * @param  string $mime file mime type
 * @return string
 */
function get_mime_class($mime)
{
    if (empty($mime) || is_null($mime)) {
        return 'mime mime-file';
    }
    $_temp_mime = explode('/', $mime);
    $part1      = $_temp_mime[0];
    $part2      = $_temp_mime[1];

    // Image
    if ($part1 == 'image') {
        if (strpos($part2, 'photoshop') !== false) {
            return 'mime mime-photoshop';
        }
        ;
        return 'mime mime-image';
    }

    // Audio
    else if ($part1 == 'audio') {
        return 'mime mime-audio';
    }

    // Video
    else if ($part1 == 'video') {
        return 'mime mime-video';
    }

    // Text
    else if ($part1 == 'text') {
        return 'mime mime-file';
    }

    // Applications
    else if ($part1 == 'application') {

        // Pdf
        if ($part2 == 'pdf') {
            return 'mime mime-pdf';
        }

        // Ilustrator
        else if ($part2 == 'illustrator') {
            return 'mime mime-illustrator';
        }

        // Zip
        else if ($part2 == 'zip' || $part2 == 'gzip' || strpos($part2, 'tar') !== false || strpos($part2, 'compressed') !== false) {

            return 'mime mime-zip';
        }

        // PowerPoint
        else if (strpos($part2, 'powerpoint') !== false || strpos($part2, 'presentation') !== false) {

            return 'mime mime-powerpoint ';
        }

        // Excel
        else if (strpos($part2, 'excel') !== false || strpos($part2, 'sheet') !== false) {

            return 'mime mime-excel';
        }

        // Word
        else if ($part2 == 'msword' || $part2 == 'rtf' || strpos($part2, 'document') !== false) {

            return 'mime mime-word';
        }

        // Else
        else {
            return 'mime mime-file';
        }
    }

    // Else
    else {
        return 'mime mime-file';
    }
}
