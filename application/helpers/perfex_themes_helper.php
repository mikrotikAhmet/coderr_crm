<?php
/**
 * Get current template assets url
 * @return string Assets url
 */
function template_assets_url()
{
    return site_url('assets/themes/' . get_option('clients_default_theme')) . '/';
}

/**
 * Current theme view part
 * @param  string $name file name
 * @param  array  $data variables passed to view
 */
function get_template_part($name, $data = array())
{
    $CI =& get_instance();
    $CI->load->view('themes/' . get_option('clients_default_theme') . '/' . 'template_parts/' . $name);
}

function ClientTicketsTable($vars)
{
    extract($vars);
    $aColumns         = array(
        'ticketid',
        'subject',
        'tbldepartments.name',
        'tblticketstatus.name',
        'lastreply'
    );
    $additionalSelect = array(
        'clientread',
        'tblclients.lastname',
        'tbltickets.userid',
        'statuscolor'
    );

    if (get_option('services') == 1) {
        array_splice($aColumns, 3, 0, 'tblservices.name');
    }

    $join = array(
        'LEFT JOIN tblservices ON tblservices.serviceid = tbltickets.service',
        'LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbltickets.department',
        'LEFT JOIN tblticketstatus ON tblticketstatus.ticketstatusid = tbltickets.status',
        'LEFT JOIN tblclients ON tblclients.userid = tbltickets.userid'
    );

    $where = array();
    if (isset($status) && is_numeric($status)) {
        $where = array(
            'AND status = ' . $status
        );
    }


    if (isset($userid)) {
        array_push($where, 'AND tbltickets.userid = ' . $userid);
    }

    $sIndexColumn = "ticketid";
    $sTable       = 'tbltickets';
    $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
    $output       = $result['output'];
    $rResult      = $result['rResult'];

    foreach ($rResult as $aRow) {
        $row = array();
        for ($i = 0; $i < count($aColumns); $i++) {
            $_data = $aRow[$aColumns[$i]];
            if ($aColumns[$i] == 'lastreply') {
                if ($aRow[$aColumns[$i]] == NULL) {
                    $_data = 'No reply yet';
                } else {
                    $_data = time_ago($aRow[$aColumns[$i]]);
                }
            } else if ($aColumns[$i] == 'tblticketstatus.name') {
                $_data = '<span class="label pull-right" style="background:' . $aRow["statuscolor"] . '">' . $_data . '</span>';
            } else if ($aColumns[$i] == 'subject' || $aColumns[$i == 'ticketid']) {
                $_data = '<a href="' . site_url('clients/ticket/' . $aRow['ticketid']) . '">' . $_data . '</a>';
            }
            $row[] = $_data;

            if ($aRow['clientread'] == 0) {
                $row['DT_RowClass'] = 'text-danger bold';
            }
        }
        $output['aaData'][] = $row;
    }

    echo json_encode($output);
    die();
}

/**
 * Get all client themes in themes folder
 * @return array
 */
function get_all_client_themes()
{
    return list_folders(APPPATH . 'views/themes/');
}
/**
 * Get active client theme
 * @return mixed
 */
function active_clients_theme()
{
    $CI = &get_instance();
    if($CI->config->item('installed') == false){
        return '';
    }

    $theme = get_option('clients_default_theme');

    if ($theme == '') {
        show_error('Default theme is not set');
    }
    if (!is_dir(APPPATH . 'views/themes/' . $theme)) {
        show_error('Theme does not exists');
    }
    return $theme;
}
