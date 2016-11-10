<?php
header('Content-Type: text/html; charset=utf-8');
/**
 * Update the config variable to installed / used in update and install
 * @since  Version 1.0.2
 * @param  string $config_path config path
 * @return boolean
 */
function update_config_installed()
{
    $CI =& get_instance();
    $config_path = APPPATH . 'config/config.php';
    $CI->load->helper('file');

    @chmod($config_path, FILE_WRITE_MODE);
    $config_file = read_file($config_path);
    $config_file = trim($config_file);
    $config_file = str_replace("\$config['installed'] = false;", "\$config['installed'] = true;", $config_file);
    $config_file = str_replace("\$config['base_url'] = '';", "\$config['base_url'] = '".site_url()."';", $config_file);

    if (!$fp = fopen($config_path, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
        return FALSE;
    }

    flock($fp, LOCK_EX);
    fwrite($fp, $config_file, strlen($config_file));
    flock($fp, LOCK_UN);
    fclose($fp);

    @chmod($config_path, FILE_READ_MODE);
    return TRUE;
}
/**
 * Available date formats
 * @return array
 */
function get_available_date_formats()
{
    $date_formats = array(
        'Y-m-d|yyyy-mm-dd' => 'yyyy-mm-dd',
        'm/d/Y|mm/dd/yyyy' => 'mm/dd/yyyy',
        'Y/m/d|yyyy/mm/dd' => 'yyyy/mm/dd',
        'd.m.Y|dd.mm.yyyy' => 'dd.mm.yyyy'
    );

    return do_action('get_available_date_formats', $date_formats);
}

/**
 * Get current date format from options
 * @return string
 */
function get_current_date_format()
{
    $format = get_option('dateformat');
    $format = explode('|', $format);
    return $format[0];
}

/**
 * Check if current user is admin
 * @param  mixed $staffid
 * @return boolean if user is not admin
 */
function is_admin($staffid = '')
{
    $_staffid = get_staff_user_id();
    if (is_numeric($staffid)) {
        $_staffid = $staffid;
    }
    $CI =& get_instance();
    $CI->db->select('1');
    $CI->db->where('admin', 1);
    $CI->db->where('staffid', $_staffid);
    return $CI->db->get('tblstaff')->row();
}
/**
 * Is user logged in
 * @return boolean
 */
function is_logged_in()
{
    $CI =& get_instance();
    if (!$CI->session->has_userdata('client_logged_in') && !$CI->session->has_userdata('staff_logged_in')) {
        return false;
    }
    return true;
}
/**
 * Is client logged in
 * @return boolean
 */
function is_client_logged_in()
{
    $CI =& get_instance();
    if ($CI->session->has_userdata('client_logged_in') && $CI->session->get_userdata('client_logged_in') != false) {
        return true;
    }
    return false;
}
/**
 * Is staff logged in
 * @return boolean
 */
function is_staff_logged_in()
{
    $CI =& get_instance();
    if ($CI->session->has_userdata('staff_logged_in')) {
        return true;
    }
    return false;
}
/**
 * Return logged staff User ID from session
 * @return mixed
 */
function get_staff_user_id()
{
    $CI =& get_instance();
    if (!$CI->session->has_userdata('staff_logged_in')) {
        return false;
    }

    return $CI->session->userdata('staff_user_id');
}
/**
 * Return logged client User ID from session
 * @return mixed
 */
function get_client_user_id()
{
    $CI =& get_instance();
    if (!$CI->session->has_userdata('client_logged_in')) {
        return false;
    }
    return $CI->session->userdata('client_user_id');
}

/**
 * Get admin url
 * @param string url to append (Optional)
 * @return string admin url
 */
function admin_url($url = '')
{
    if ($url == '') {
        return site_url(ADMIN_URL) . '/';
    } else {
        return site_url(ADMIN_URL . '/' . $url);
    }
}
/**
 * Outputs language string based on passed line
 * @since  Version 1.0.1
 * @param  string $line  language line string
 * @param  string $label sprint_f label
 * @return string        formated language
 */
function _l($line, $label = '')
{
    $CI =& get_instance();
    $_line = sprintf($CI->lang->line($line), $label);
    if ($_line !== '') {
        return $_line;
    }
    // dont change this line
    return 'translate_not_found_' . $line;
}

/**
 * Set session alert / flashdata
 * @param string $type    Alert type
 * @param string $message Alert message
 */

function set_alert($type, $message)
{
    $CI =& get_instance();
    $CI->session->set_flashdata('message-' . $type, $message);
}
/**
 * Redirect to blank page
 * @param  string $message Alert message
 * @param  string $alert   Alert type
 */
function blank_page($message = '', $alert = 'danger')
{
    set_alert($alert, $message);
    redirect(admin_url('not_found'));
}
/**
 * Set debug message - message wont be hidden in X seconds from javascript
 * @since  Version 1.0.1
 * @param string $message debug message
 */
function set_debug_alert($message)
{
    $CI =& get_instance();
    $CI->session->set_flashdata('debug', $message);
}
/**
 * Format date to selected dateformat
 * @param  date $date Valid date
 * @return date/string
 */
function _d($date)
{
    if (!is_date($date)) {
        return $date;
    }

    $format = get_current_date_format();

    if (strpos($date, ' ') === true) {
        $_date = new DateTime($date);
        $_date = $_date->format($format . ' H:i:s');
        if (is_date($_date)) {
            return $_date;
        }
        return $date;
    }
    $_date = new DateTime($date);
    $_date = $_date->format($format);

    if (is_date($_date)) {
        return $_date;
    }

    return $date;
}
/**
 * Format datetime to selected datetime format
 * @param  datetime $date datetime date
 * @return datetime/string
 */
function _dt($date)
{
    if (!is_date($date)) {
        return $date;
    }

    $_date = new DateTime($date);
    $_date = $_date->format(get_current_date_format() . ' H:i:s');
    if (is_date($_date)) {
        return $_date;
    }
    return $date;
}
/**
 * Convert string to sql date based on current date format from options
 * @param  string $date date string
 * @return mixed
 */
function to_sql_date($date)
{
    if ($date == '') {
        return;
    }
    return DateTime::createFromFormat(get_current_date_format(), $date)->format('Y-m-d');
}

/**
 * Check if passed string is valid date
 * @param  string  $date
 * @return boolean
 */
function is_date($date)
{
    return (bool) strtotime($date);
}

/**
 * Get weekdays as array
 * @return array
 */
function get_weekdays()
{
    return array(
        _l('wd_monday'),
        _l('wd_tuesday'),
        _l('wd_wednesday'),
        _l('wd_thursday'),
        _l('wd_friday'),
        _l('wd_saturday'),
        _l('wd_sunday')
    );
}

/**
 * Format datetime to time ago with specific hours mins and seconds
 * @param  datetime $lastreply
 * @param  string $from      Optional
 * @return mixed
 */
function time_ago_specific($date, $from = "now")
{
    $datetime   = strtotime("now");
    $date2      = strtotime("" . $date);
    $holdtotsec = $datetime - $date2;
    $holdtotmin = ($datetime - $date2) / 60;
    $holdtothr  = ($datetime - $date2) / 3600;
    $holdtotday = intval(($datetime - $date2) / 86400);
    $str        = '';
    if (0 < $holdtotday) {
        $str .= $holdtotday . "d ";
    }

    $holdhr = intval($holdtothr - $holdtotday * 24);
    $str .= $holdhr . "h ";
    $holdmr = intval($holdtotmin - ($holdhr * 60 + $holdtotday * 1440));
    $str .= $holdmr . "m";
    return $str;
}
/**
 * Short Time ago function
 * @param  datetime $time_ago
 * @return mixed
 */
function time_ago($time_ago)
{
    $time_ago     = strtotime($time_ago);
    $cur_time     = time();
    $time_elapsed = $cur_time - $time_ago;
    $seconds      = $time_elapsed;
    $minutes      = round($time_elapsed / 60);
    $hours        = round($time_elapsed / 3600);
    $days         = round($time_elapsed / 86400);
    $weeks        = round($time_elapsed / 604800);
    $months       = round($time_elapsed / 2600640);
    $years        = round($time_elapsed / 31207680);
    // Seconds
    if ($seconds <= 60) {
        return _l('time_ago_just_now');
    }
    //Minutes
    else if ($minutes <= 60) {
        if ($minutes == 1) {
            return _l('time_ago_minute');
        } else {
            return _l('time_ago_minutes', $minutes);
        }
    }
    //Hours
    else if ($hours <= 24) {
        if ($hours == 1) {
            return _l('time_ago_hour');
        } else {
            return _l('time_ago_hours', $hours);
        }
    }
    //Days
    else if ($days <= 7) {
        if ($days == 1) {
            return _l('time_ago_yesterday');
        } else {
            return _l('time_ago_days', $days);
        }
    }
    //Weeks
    else if ($weeks <= 4.3) {
        if ($weeks == 1) {
            return _l('time_ago_week');
        } else {
            return _l('time_ago_weeks', $weeks);
        }
    }
    //Months
    else if ($months <= 12) {
        if ($months == 1) {
            return _l('time_ago_month');
        } else {
            return _l('time_ago_months', $months);
        }
    }
    //Years
    else {
        if ($years == 1) {
            return _l('time_ago_year');
        } else {
            return _l('time_ago_years', $years);
        }
    }
}
/**
 * String starts with
 * @param  string $haystack
 * @param  string $needle
 * @return boolean
 */
if (!function_exists('_startsWith')) {
    function _startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }
}
/**
 * String ends with
 * @param  string $haystack
 * @param  string $needle
 * @return boolean
 */
if (!function_exists('endsWith')) {
    function endsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }
}



/**
 * List folder on a specific path
 * @param  stirng $path
 * @return array
 */
function list_folders($path)
{
    $folders = array();
    foreach (new DirectoryIterator($path) as $file) {

        if ($file->isDot())
            continue;

        if ($file->isDir()) {
            array_push($folders, $file->getFilename());
        }
    }
    return $folders;
}
/**
 * List files in a specific folder
 * @param  string $dir directory to list files
 * @return array
 */
function list_files($dir)
{
    $ignored = array(
        '.',
        '..',
        '.svn',
        '.htaccess',
        'index.html'
    );

    $files = array();
    foreach (scandir($dir) as $file) {
        if (in_array($file, $ignored))
            continue;
        $files[$file] = filectime($dir . '/' . $file);
    }

    arsort($files);
    $files = array_keys($files);

    return ($files) ? $files : array();
}
/**
 * Convert bytes of files to readable seize
 * @param  string $path file path
 * @return mixed
 */
function bytesToSize($path)
{
    $bytes = sprintf('%u', filesize($path));

    if ($bytes > 0) {
        $unit  = intval(log($bytes, 1024));
        $units = array(
            'B',
            'KB',
            'MB',
            'GB'
        );

        if (array_key_exists($unit, $units) === true) {
            return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
        }
    }

    return $bytes;
}
/**
 * Get string after specific charcter/word
 * @param  string $string    string from where to get
 * @param  substring $substring search for
 * @return string
 */
function strafter($string, $substring)
{
    $pos = strpos($string, $substring);
    if ($pos === false)
        return $string;
    else
        return (substr($string, $pos + strlen($substring)));
}
/**
 * Get string before specific charcter/word
 * @param  string $string    string from where to get
 * @param  substring $substring search for
 * @return string
 */
function strbefore($string, $substring)
{
    $pos = strpos($string, $substring);
    if ($pos === false)
        return $string;
    else
        return (substr($string, 0, $pos));
}

/**
 * Check if staff user has permission
 * @param  string  $permission permission shortname
 * @param  mixed  $staffid if you want to check for particular staff
 * @return boolean
 */
function has_permission($permission, $staffid = '')
{
    $CI =& get_instance();

    // check for passed is_admin function
    if (function_exists($permission) && is_callable($permission)) {
        return call_user_func($permission, $staffid);
    }

    if (is_admin($staffid)) {
        return true;
    }

    $_userid = get_staff_user_id();

    if ($staffid != '') {
        $_userid = $staffid;
    }

    $CI->db->select('permissionid');
    $CI->db->where('shortname', $permission);
    $permission = $CI->db->get('tblpermissions')->row();

    $CI->db->select('1');
    $CI->db->from('tblstaffpermissions');
    $CI->db->where('permissionid', $permission->permissionid);
    $CI->db->where('staffid', $_userid);
    $perm = $CI->db->get()->row();
    if ($perm) {
        return true;
    }
    return false;
}

function has_customer_permission($permission,$userid = ''){

     $CI =& get_instance();
     $CI->load->library('perfex_base');
     $permissions = $CI->perfex_base->get_customer_permissions();

     $_userid = get_client_user_id();

     if ($userid != '') {
        $_userid = $userid;
     }

     foreach($permissions as $_permission){
        if($_permission['short_name'] == $permission){
            if(total_rows('tblcustomerpermissions',array('permission_id'=>$_permission['id'],'userid'=>$_userid)) > 0){
                return true;
            }
        }
     }

     return false;
}

function get_goal_types()
{
    $types = array(
        array(
            'key' => 1,
            'lang_key' => 'goal_type_total_income',
            'subtext' => 'goal_type_income_subtext'
        ),
        array(
            'key' => 2,
            'lang_key' => 'goal_type_convert_leads'
        ),
        array(
            'key' => 3,
            'lang_key' => 'goal_type_increase_customers_without_leads_conversions',
            'subtext' => 'goal_type_increase_customers_without_leads_conversions_subtext'
        ),
        array(
            'key' => 4,
            'lang_key' => 'goal_type_increase_customers_with_leads_conversions',
            'subtext' => 'goal_type_increase_customers_with_leads_conversions_subtext'
        ),
        array(
            'key' => 5,
            'lang_key' => 'goal_type_make_contracts_by_type_calc_database',
            'subtext' => 'goal_type_make_contracts_by_type_calc_database_subtext'
        ),
        array(
            'key' => 7,
            'lang_key' => 'goal_type_make_contracts_by_type_calc_date',
            'subtext' => 'goal_type_make_contracts_by_type_calc_date_subtext'
        ),
        array(
            'key' => 6,
            'lang_key' => 'goal_type_total_estimates_converted',
            'subtext' => 'goal_type_total_estimates_converted_subtext'
        )

    );

    return do_action('get_goal_types', $types);
}
function format_goal_type($key)
{
    foreach (get_goal_types() as $type) {
        if ($type['key'] == $key) {
            return _l($type['lang_key']);
        }
    }
    return $type;
}
/**
 * Redirect to access danied page and log activity
 * @param  string $permission If permission based to check where user tried to acces
 */
function access_denied($permission = '')
{
    set_alert('danger', _l('access_denied'));
    logActivity('Tried to access page where dont have permission [' . $permission . ']');
    redirect(admin_url('access_denied'));
}

/**
 * Replace Last Occurence of a String in a String
 * @since  Version 1.0.1
 * @param  string $search  string to be replaced
 * @param  string $replace replace with
 * @param  string $subject [the string to search
 * @return string
 */
function str_lreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}
/**
 * Copy directory and all contents
 * @since  Version 1.0.2
 * @param  string  $source      string
 * @param  string  $dest        destionation
 * @param  integer $permissions folder permissions
 * @return boolean
 */
function xcopy($source, $dest, $permissions = 0755)
{
    // Check for symlinks
    if (is_link($source)) {
        return symlink(readlink($source), $dest);
    }

    // Simple copy for a file
    if (is_file($source)) {
        return copy($source, $dest);
    }

    // Make destination directory
    if (!is_dir($dest)) {
        mkdir($dest, $permissions);
    }

    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep copy directories
        xcopy("$source/$entry", "$dest/$entry", $permissions);
    }

    // Clean up
    $dir->close();
    return true;
}

/**
 * Delete directory
 * @param  string $dirPath dir
 * @return boolean
 */
function delete_dir($dirPath)
{
    if (!is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            delete_dir($file);
        } else {
            unlink($file);
        }
    }
    if (rmdir($dirPath)) {
        return true;
    }

    return false;
}

/**
 * Is internet connection open
 * @param  string  $domain
 * @return boolean
 */
function is_connected($domain = 'www.perfexcrm.com')
{
    $connected = @fsockopen($domain, 80);
    //website, port  (try 80 or 443)
    if ($connected) {
        $is_conn = true; //action when connected
        fclose($connected);
    } else {
        $is_conn = false; //action in connection failure
    }
    return $is_conn;
}


/**
 * Is file image
 * @param  string  $path file path
 * @return boolean
 */
function is_image($path)
{
    $image      = @getimagesize($path);
    $image_type = $image[2];

    if (in_array($image_type, array(
        IMAGETYPE_GIF,
        IMAGETYPE_JPEG,
        IMAGETYPE_PNG,
        IMAGETYPE_BMP
    ))) {
        return true;
    }
    return false;
}

/**
 * Get file extension by filename
 * @param  string $file_name file name
 * @return mixed
 */
function get_file_extension($file_name)
{
    return substr(strrchr($file_name, '.'), 1);
}
/**
 * Unique filename based on folder
 * @since  Version 1.0.1
 * @param  string $dir      directory to compare
 * @param  string $filename filename
 * @return string           the unique filename
 */
function unique_filename($dir, $filename)
{

    // Separate the filename into a name and extension.
    $info     = pathinfo($filename);
    $ext      = !empty($info['extension']) ? '.' . $info['extension'] : '';
    $filename = sanitize_file_name($filename);

    $number = '';

    // Change '.ext' to lower case.
    if ($ext && strtolower($ext) != $ext) {
        $ext2      = strtolower($ext);
        $filename2 = preg_replace('|' . preg_quote($ext) . '$|', $ext2, $filename);

        // Check for both lower and upper case extension or image sub-sizes may be overwritten.
        while (file_exists($dir . "/$filename") || file_exists($dir . "/$filename2")) {
            $filename  = str_replace(array(
                "-$number$ext",
                "$number$ext"
            ), "-$new_number$ext", $filename);
            $filename2 = str_replace(array(
                "-$number$ext2",
                "$number$ext2"
            ), "-$new_number$ext2", $filename2);
            $number    = $new_number;
        }
        return $filename2;
    }

    while (file_exists($dir . "/$filename")) {
        if ('' == "$number$ext") {
            $filename = "$filename-" . ++$number;
        } else {
            $filename = str_replace(array(
                "-$number$ext",
                "$number$ext"
            ), "-" . ++$number . $ext, $filename);
        }
    }
    return $filename;
}

/**
 * Sanitize file name
 * @param  string $filename filename
 * @return mixed
 */
function sanitize_file_name($filename)
{

    $special_chars = array(
        "?",
        "[",
        "]",
        "/",
        "\\",
        "=",
        "<",
        ">",
        ":",
        ";",
        ",",
        "'",
        "\"",
        "&",
        "$",
        "#",
        "*",
        "(",
        ")",
        "|",
        "~",
        "`",
        "!",
        "{",
        "}",
        "%",
        "+",
        chr(0)
    );

    $filename = str_replace($special_chars, '', $filename);
    $filename = str_replace(array(
        '%20',
        '+'
    ), '-', $filename);
    $filename = preg_replace('/[\r\n\t -]+/', '-', $filename);
    $filename = trim($filename, '.-_');

    // Split the filename into a base and extension[s]
    $parts = explode('.', $filename);

    // Return if only one extension
    if (count($parts) <= 2) {
        return $filename;
    }

    // Process multiple extensions
    $filename  = array_shift($parts);
    $extension = array_pop($parts);
    /*
     * Loop over any intermediate extensions. Postfix them with a trailing underscore
     * if they are a 2 - 5 character long alpha string not in the extension whitelist.
     */
    foreach ((array) $parts as $part) {
        $filename .= '.' . $part;

        if (preg_match("/^[a-zA-Z]{2,5}\d?$/", $part)) {
            $allowed  = false;
            $ext_preg = '!^(' . $ext_preg . ')$!i';
            if (preg_match($ext_preg, $part)) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed)
            $filename .= '_';
    }

    $filename .= '.' . $extension;
    return $filename;
}

/**
 * Get system favourite colors
 * @return array
 */
function get_system_favourite_colors(){
    // dont delete any of these colors are used all over the system
    $colors = array(
        '#28B8DA',
        '#c53da9',
        '#757575',
        '#8e24aa',
        '#d81b60',
        '#0288d1',
        '#7cb342',
        '#fb8c00',
        );

    $colors = do_action('get_kan_ban_colors',$colors);
    return $colors;
}


/**
 * Slug function
 * @param  string $str
 * @param  array  $options Additional Options
 * @return mixed
 */
function slug_it($str, $options = array())
{
    // Make sure string is in UTF-8 and strip invalid UTF-8 characters
    $str = mb_convert_encoding((string) $str, 'UTF-8', mb_list_encodings());

    $defaults = array(
        'delimiter' => '-',
        'limit' => null,
        'lowercase' => true,
        'replacements' => array(
            '
            /\b(ѓ)\b/i' => 'gj',
            '/\b(ч)\b/i' => 'ch',
            '/\b(ш)\b/i' => 'sh',
            '/\b(љ)\b/i' => 'lj'
        ),
        'transliterate' => true
    );

    // Merge options
    $options = array_merge($defaults, $options);

    $char_map = array(
        // Latin
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Å' => 'A',
        'Æ' => 'AE',
        'Ç' => 'C',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ð' => 'D',
        'Ñ' => 'N',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        'Ő' => 'O',
        'Ø' => 'O',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ű' => 'U',
        'Ý' => 'Y',
        'Þ' => 'TH',
        'ß' => 'ss',
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'æ' => 'ae',
        'ç' => 'c',
        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ð' => 'd',
        'ñ' => 'n',
        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ő' => 'o',
        'ø' => 'o',
        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'ű' => 'u',
        'ý' => 'y',
        'þ' => 'th',
        'ÿ' => 'y',

        // Latin symbols
        '©' => '(c)',

        // Greek
        'Α' => 'A',
        'Β' => 'B',
        'Γ' => 'G',
        'Δ' => 'D',
        'Ε' => 'E',
        'Ζ' => 'Z',
        'Η' => 'H',
        'Θ' => '8',
        'Ι' => 'I',
        'Κ' => 'K',
        'Λ' => 'L',
        'Μ' => 'M',
        'Ν' => 'N',
        'Ξ' => '3',
        'Ο' => 'O',
        'Π' => 'P',
        'Ρ' => 'R',
        'Σ' => 'S',
        'Τ' => 'T',
        'Υ' => 'Y',
        'Φ' => 'F',
        'Χ' => 'X',
        'Ψ' => 'PS',
        'Ω' => 'W',
        'Ά' => 'A',
        'Έ' => 'E',
        'Ί' => 'I',
        'Ό' => 'O',
        'Ύ' => 'Y',
        'Ή' => 'H',
        'Ώ' => 'W',
        'Ϊ' => 'I',
        'Ϋ' => 'Y',
        'α' => 'a',
        'β' => 'b',
        'γ' => 'g',
        'δ' => 'd',
        'ε' => 'e',
        'ζ' => 'z',
        'η' => 'h',
        'θ' => '8',
        'ι' => 'i',
        'κ' => 'k',
        'λ' => 'l',
        'μ' => 'm',
        'ν' => 'n',
        'ξ' => '3',
        'ο' => 'o',
        'π' => 'p',
        'ρ' => 'r',
        'σ' => 's',
        'τ' => 't',
        'υ' => 'y',
        'φ' => 'f',
        'χ' => 'x',
        'ψ' => 'ps',
        'ω' => 'w',
        'ά' => 'a',
        'έ' => 'e',
        'ί' => 'i',
        'ό' => 'o',
        'ύ' => 'y',
        'ή' => 'h',
        'ώ' => 'w',
        'ς' => 's',
        'ϊ' => 'i',
        'ΰ' => 'y',
        'ϋ' => 'y',
        'ΐ' => 'i',

        // Turkish
        'Ş' => 'S',
        'İ' => 'I',
        'Ç' => 'C',
        'Ü' => 'U',
        'Ö' => 'O',
        'Ğ' => 'G',
        'ş' => 's',
        'ı' => 'i',
        'ç' => 'c',
        'ü' => 'u',
        'ö' => 'o',
        'ğ' => 'g',

        // Russian
        'А' => 'A',
        'Б' => 'B',
        'В' => 'V',
        'Г' => 'G',
        'Д' => 'D',
        'Е' => 'E',
        'Ё' => 'Yo',
        'Ж' => 'Zh',
        'З' => 'Z',
        'И' => 'I',
        'Й' => 'J',
        'К' => 'K',
        'Л' => 'L',
        'М' => 'M',
        'Н' => 'N',
        'О' => 'O',
        'П' => 'P',
        'Р' => 'R',
        'С' => 'S',
        'Т' => 'T',
        'У' => 'U',
        'Ф' => 'F',
        'Х' => 'H',
        'Ц' => 'C',
        'Ч' => 'Ch',
        'Ш' => 'Sh',
        'Щ' => 'Sh',
        'Ъ' => '',
        'Ы' => 'Y',
        'Ь' => '',
        'Э' => 'E',
        'Ю' => 'Yu',
        'Я' => 'Ya',
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'yo',
        'ж' => 'zh',
        'з' => 'z',
        'и' => 'i',
        'й' => 'j',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'h',
        'ц' => 'c',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'sh',
        'ъ' => '',
        'ы' => 'y',
        'ь' => '',
        'э' => 'e',
        'ю' => 'yu',
        'я' => 'ya',

        // Ukrainian
        'Є' => 'Ye',
        'І' => 'I',
        'Ї' => 'Yi',
        'Ґ' => 'G',
        'є' => 'ye',
        'і' => 'i',
        'ї' => 'yi',
        'ґ' => 'g',

        // Czech
        'Č' => 'C',
        'Ď' => 'D',
        'Ě' => 'E',
        'Ň' => 'N',
        'Ř' => 'R',
        'Š' => 'S',
        'Ť' => 'T',
        'Ů' => 'U',
        'Ž' => 'Z',
        'č' => 'c',
        'ď' => 'd',
        'ě' => 'e',
        'ň' => 'n',
        'ř' => 'r',
        'š' => 's',
        'ť' => 't',
        'ů' => 'u',
        'ž' => 'z',

        // Polish
        'Ą' => 'A',
        'Ć' => 'C',
        'Ę' => 'e',
        'Ł' => 'L',
        'Ń' => 'N',
        'Ó' => 'o',
        'Ś' => 'S',
        'Ź' => 'Z',
        'Ż' => 'Z',
        'ą' => 'a',
        'ć' => 'c',
        'ę' => 'e',
        'ł' => 'l',
        'ń' => 'n',
        'ó' => 'o',
        'ś' => 's',
        'ź' => 'z',
        'ż' => 'z',

        // Latvian
        'Ā' => 'A',
        'Č' => 'C',
        'Ē' => 'E',
        'Ģ' => 'G',
        'Ī' => 'i',
        'Ķ' => 'k',
        'Ļ' => 'L',
        'Ņ' => 'N',
        'Š' => 'S',
        'Ū' => 'u',
        'Ž' => 'Z',
        'ā' => 'a',
        'č' => 'c',
        'ē' => 'e',
        'ģ' => 'g',
        'ī' => 'i',
        'ķ' => 'k',
        'ļ' => 'l',
        'ņ' => 'n',
        'š' => 's',
        'ū' => 'u',
        'ž' => 'z'
    );

    // Make custom replacements
    $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

    // Transliterate characters to ASCII
    if ($options['transliterate']) {
        $str = str_replace(array_keys($char_map), $char_map, $str);
    }

    // Replace non-alphanumeric characters with our delimiter
    $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

    // Remove duplicate delimiters
    $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

    // Truncate slug to max. characters
    $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

    // Remove delimiter from ends
    $str = trim($str, $options['delimiter']);

    return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}
/**
 * Get timezones list
 * @return array timezones
 */
function get_timezones_list()
{
    return $timezones = array(
        'Pacific/Midway' => "(GMT-11:00) Midway Island",
        'US/Samoa' => "(GMT-11:00) Samoa",
        'US/Hawaii' => "(GMT-10:00) Hawaii",
        'US/Alaska' => "(GMT-09:00) Alaska",
        'US/Pacific' => "(GMT-08:00) Pacific Time (US &amp; Canada)",
        'America/Tijuana' => "(GMT-08:00) Tijuana",
        'US/Arizona' => "(GMT-07:00) Arizona",
        'US/Mountain' => "(GMT-07:00) Mountain Time (US &amp; Canada)",
        'America/Chihuahua' => "(GMT-07:00) Chihuahua",
        'America/Mazatlan' => "(GMT-07:00) Mazatlan",
        'America/Mexico_City' => "(GMT-06:00) Mexico City",
        'America/Monterrey' => "(GMT-06:00) Monterrey",
        'Canada/Saskatchewan' => "(GMT-06:00) Saskatchewan",
        'US/Central' => "(GMT-06:00) Central Time (US &amp; Canada)",
        'US/Eastern' => "(GMT-05:00) Eastern Time (US &amp; Canada)",
        'US/East-Indiana' => "(GMT-05:00) Indiana (East)",
        'America/Bogota' => "(GMT-05:00) Bogota",
        'America/Lima' => "(GMT-05:00) Lima",
        'America/Caracas' => "(GMT-04:30) Caracas",
        'Canada/Atlantic' => "(GMT-04:00) Atlantic Time (Canada)",
        'America/La_Paz' => "(GMT-04:00) La Paz",
        'America/Santiago' => "(GMT-04:00) Santiago",
        'Canada/Newfoundland' => "(GMT-03:30) Newfoundland",
        'America/Buenos_Aires' => "(GMT-03:00) Buenos Aires",
        'Greenland' => "(GMT-03:00) Greenland",
        'Atlantic/Stanley' => "(GMT-02:00) Stanley",
        'Atlantic/Azores' => "(GMT-01:00) Azores",
        'Atlantic/Cape_Verde' => "(GMT-01:00) Cape Verde Is.",
        'Africa/Casablanca' => "(GMT) Casablanca",
        'Europe/Dublin' => "(GMT) Dublin",
        'Europe/Lisbon' => "(GMT) Lisbon",
        'Europe/London' => "(GMT) London",
        'Africa/Monrovia' => "(GMT) Monrovia",
        'Europe/Amsterdam' => "(GMT+01:00) Amsterdam",
        'Europe/Belgrade' => "(GMT+01:00) Belgrade",
        'Europe/Berlin' => "(GMT+01:00) Berlin",
        'Europe/Bratislava' => "(GMT+01:00) Bratislava",
        'Europe/Brussels' => "(GMT+01:00) Brussels",
        'Europe/Budapest' => "(GMT+01:00) Budapest",
        'Europe/Copenhagen' => "(GMT+01:00) Copenhagen",
        'Europe/Ljubljana' => "(GMT+01:00) Ljubljana",
        'Europe/Madrid' => "(GMT+01:00) Madrid",
        'Europe/Paris' => "(GMT+01:00) Paris",
        'Europe/Prague' => "(GMT+01:00) Prague",
        'Europe/Rome' => "(GMT+01:00) Rome",
        'Europe/Sarajevo' => "(GMT+01:00) Sarajevo",
        'Europe/Skopje' => "(GMT+01:00) Skopje",
        'Europe/Stockholm' => "(GMT+01:00) Stockholm",
        'Europe/Vienna' => "(GMT+01:00) Vienna",
        'Europe/Warsaw' => "(GMT+01:00) Warsaw",
        'Europe/Zagreb' => "(GMT+01:00) Zagreb",
        'Europe/Athens' => "(GMT+02:00) Athens",
        'Europe/Bucharest' => "(GMT+02:00) Bucharest",
        'Africa/Cairo' => "(GMT+02:00) Cairo",
        'Africa/Harare' => "(GMT+02:00) Harare",
        'Europe/Helsinki' => "(GMT+02:00) Helsinki",
        'Europe/Istanbul' => "(GMT+02:00) Istanbul",
        'Asia/Jerusalem' => "(GMT+02:00) Jerusalem",
        'Europe/Kiev' => "(GMT+02:00) Kyiv",
        'Europe/Minsk' => "(GMT+02:00) Minsk",
        'Europe/Riga' => "(GMT+02:00) Riga",
        'Europe/Sofia' => "(GMT+02:00) Sofia",
        'Europe/Tallinn' => "(GMT+02:00) Tallinn",
        'Europe/Vilnius' => "(GMT+02:00) Vilnius",
        'Asia/Baghdad' => "(GMT+03:00) Baghdad",
        'Asia/Kuwait' => "(GMT+03:00) Kuwait",
        'Africa/Nairobi' => "(GMT+03:00) Nairobi",
        'Asia/Riyadh' => "(GMT+03:00) Riyadh",
        'Europe/Moscow' => "(GMT+03:00) Moscow",
        'Asia/Tehran' => "(GMT+03:30) Tehran",
        'Asia/Baku' => "(GMT+04:00) Baku",
        'Europe/Volgograd' => "(GMT+04:00) Volgograd",
        'Asia/Muscat' => "(GMT+04:00) Muscat",
        'Asia/Tbilisi' => "(GMT+04:00) Tbilisi",
        'Asia/Yerevan' => "(GMT+04:00) Yerevan",
        'Asia/Kabul' => "(GMT+04:30) Kabul",
        'Asia/Karachi' => "(GMT+05:00) Karachi",
        'Asia/Tashkent' => "(GMT+05:00) Tashkent",
        'Asia/Kolkata' => "(GMT+05:30) Kolkata",
        'Asia/Kathmandu' => "(GMT+05:45) Kathmandu",
        'Asia/Yekaterinburg' => "(GMT+06:00) Ekaterinburg",
        'Asia/Almaty' => "(GMT+06:00) Almaty",
        'Asia/Dhaka' => "(GMT+06:00) Dhaka",
        'Asia/Novosibirsk' => "(GMT+07:00) Novosibirsk",
        'Asia/Bangkok' => "(GMT+07:00) Bangkok",
        'Asia/Jakarta' => "(GMT+07:00) Jakarta",
        'Asia/Krasnoyarsk' => "(GMT+08:00) Krasnoyarsk",
        'Asia/Chongqing' => "(GMT+08:00) Chongqing",
        'Asia/Hong_Kong' => "(GMT+08:00) Hong Kong",
        'Asia/Kuala_Lumpur' => "(GMT+08:00) Kuala Lumpur",
        'Australia/Perth' => "(GMT+08:00) Perth",
        'Asia/Singapore' => "(GMT+08:00) Singapore",
        'Asia/Taipei' => "(GMT+08:00) Taipei",
        'Asia/Ulaanbaatar' => "(GMT+08:00) Ulaan Bataar",
        'Asia/Urumqi' => "(GMT+08:00) Urumqi",
        'Asia/Irkutsk' => "(GMT+09:00) Irkutsk",
        'Asia/Seoul' => "(GMT+09:00) Seoul",
        'Asia/Tokyo' => "(GMT+09:00) Tokyo",
        'Australia/Adelaide' => "(GMT+09:30) Adelaide",
        'Australia/Darwin' => "(GMT+09:30) Darwin",
        'Asia/Yakutsk' => "(GMT+10:00) Yakutsk",
        'Australia/Brisbane' => "(GMT+10:00) Brisbane",
        'Australia/Canberra' => "(GMT+10:00) Canberra",
        'Pacific/Guam' => "(GMT+10:00) Guam",
        'Australia/Hobart' => "(GMT+10:00) Hobart",
        'Australia/Melbourne' => "(GMT+10:00) Melbourne",
        'Pacific/Port_Moresby' => "(GMT+10:00) Port Moresby",
        'Australia/Sydney' => "(GMT+10:00) Sydney",
        'Asia/Vladivostok' => "(GMT+11:00) Vladivostok",
        'Asia/Magadan' => "(GMT+12:00) Magadan",
        'Pacific/Auckland' => "(GMT+12:00) Auckland",
        'Pacific/Fiji' => "(GMT+12:00) Fiji"
    );
}
