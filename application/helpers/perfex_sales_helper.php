<?php

// Since Version 1.0.1
/**
 * Check if company using invoice with different currencies
 * @return boolean
 */
function is_using_multiple_currencies($table = 'tblinvoices')
{
    $CI =& get_instance();

    $CI->load->model('currencies_model');
    $currencies = $CI->currencies_model->get();

    $total_currencies_used = 0;

    foreach ($currencies as $currency) {
        $CI->db->where('currency', $currency['id']);
        $total = $CI->db->count_all_results($table);
        if ($total > 0) {
            $total_currencies_used++;
        }
    }

    if ($total_currencies_used > 1) {
        return true;
    } else if ($total_currencies_used == 0) {
        return false;
    }

    return false;
}
/**
 * Check if client have invoices with multiple currencies
 * @return booelan
 */
function is_client_using_multiple_currencies($clientid = '',$table = 'tblinvoices')
{
    if ($clientid == '') {
        $clientid = get_client_user_id();
    }

    $CI =& get_instance();

    $CI->load->model('currencies_model');
    $currencies = $CI->currencies_model->get();

    $total_currencies_used = 0;
    foreach ($currencies as $currency) {
        $CI->db->where('currency', $currency['id']);
        $CI->db->where('clientid', $clientid);
        $total = $CI->db->count_all_results($table);
        if ($total > 0) {
            $total_currencies_used++;
        }
    }

    if ($total_currencies_used > 1) {
        return true;
    } else if ($total_currencies_used == 0) {
        return false;
    }
    return false;
}
/**
 * Get invoice total left for paying if not payments found the original total from the invoice will be returned
 * @since  Version 1.0.1
 * @param  mixed $id     invoice id
 * @param  mixed $invoice_total
 * @return mixed  total left
 */
function get_invoice_total_left_to_pay($id,$invoice_total){
    $CI = &get_instance();
    $CI->load->model('payments_model');
    $payments = $CI->payments_model->get_invoice_payments($id);

    foreach($payments as $payment){
        $invoice_total -= $payment['amount'];
    }

    return $invoice_total;
}
/**
 * Check invoice restrictions - hash, clientid
 * @since  Version 1.0.1
 * @param  mixed $id   invoice id
 * @param  string $hash invoice hash
 */
function check_invoice_restrictions($id, $hash)
{
    $CI =& get_instance();
    $CI->load->model('invoices_model');
    if (!$hash || !$id) {
        die('No invoice specified');
    }

    if (!is_client_logged_in() && !is_staff_logged_in()) {
        if (get_option('view_invoice_only_logged_in') == 1) {
            redirect(site_url('clients/login'));
        }
    }

    $invoice = $CI->invoices_model->get($id);
    if (!$invoice) {
        die('Invoice not found');
    }

    if ($invoice->hash != $hash) {
        die;
    }

    // Do one more check
    if (!is_staff_logged_in()) {
      if (get_option('view_invoice_only_logged_in') == 1) {
        if ($invoice->clientid != get_client_user_id()) {
            die;
        }
      }
    }

}

/**
 * Check estimate restrictions - hash, clientid
 * @since  Version 1.0.1
 * @param  mixed $id   estimate id
 * @param  string $hash estimate hash
 */
function check_estimate_restrictions($id, $hash)
{
    $CI =& get_instance();
    $CI->load->model('estimates_model');
    if (!$hash || !$id) {
        die('No estimate specified');
    }

    if (!is_client_logged_in() && !is_staff_logged_in()) {
        if (get_option('view_estimate_only_logged_in') == 1) {
            redirect(site_url('clients/login'));
        }
    }

    $estimate = $CI->estimates_model->get($id);
    if (!$estimate) {
        die('Estimate not found');
    }

    if ($estimate->hash != $hash) {
        die;
    }

    // Do one more check
    if (!is_staff_logged_in()) {
      if (get_option('view_estimate_only_logged_in') == 1) {
        if ($estimate->clientid != get_client_user_id()) {
            die;
        }
      }
    }

}

function check_proposal_restrictions($id, $hash)
{
    $CI =& get_instance();
    $CI->load->model('proposals_model');
    if (!$hash || !$id) {
        die('No proposal specified');
    }

    $proposal = $CI->proposals_model->get($id);
    if (!$proposal) {
        die('Proposal not found');
    }

    if ($proposal->hash != $hash) {
        die;
    }
}

/**
 * Forat number with 2 decimals
 * @param  mixed $total
 * @return string
 */
function _format_number($total)
{

    if (!is_numeric($total)) {
        return false;
    }
    $decimal_separator  = get_option('decimal_separator');
    $thousand_separator = get_option('thousand_separator');
    return number_format($total, 2, $decimal_separator, $thousand_separator);
}

/**
 * Format money with 2 decimal based on symbol
 * @param  mixed $total
 * @param  string $symbol Money symbol
 * @return string
 */
function format_money($total, $symbol = '')
{
    if (!is_numeric($total) && $total != 0) {
        return false;
    }

    $decimal_separator  = get_option('decimal_separator');
    $thousand_separator = get_option('thousand_separator');
    $currency_placement = get_option('currency_placement');

    if ($currency_placement === 'after') {
        $_formated = number_format($total, 2, $decimal_separator, $thousand_separator) . ' ' . $symbol;
    } else {
        $_formated = $symbol . ' ' . number_format($total, 2, $decimal_separator, $thousand_separator);
    }

    return $_formated;
}
/**
 * Format invoice status
 * @param  integer  $status
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_invoice_status($status, $classes = '', $label = true)
{
    if ($status == 1) {
        $status      = _l('invoice_status_unpaid');
        $label_class = 'danger';
    } else if ($status == 2) {
        $status      = _l('invoice_status_paid');
        $label_class = 'success';
    } else if ($status == 3) {
        $status      = _l('invoice_status_not_paid_completely');
        $label_class = 'warning';
    } else {
        // status 4
        $status      = _l('invoice_status_overdue');
        $label_class = 'warning';
    }

    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' inline-block">' . $status . '</span>';
    } else {
        return $status;
    }
}

/**
 * Format estimate status
 * @param  integer  $status
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_estimate_status($status, $classes = '', $label = true)
{
    if ($status == 1) {
        $status      = _l('estimate_status_draft');
        $label_class = 'default';
    } else if ($status == 2) {
        $status      = _l('estimate_status_sent');
        $label_class = 'info';
    } else if ($status == 3) {
        $status      = _l('estimate_status_declined');
        $label_class = 'danger';
    } else if ($status == 4) {
        $status      = _l('estimate_status_accepted');
        $label_class = 'success';
    } else {
        // status 5
        $status      = _l('estimate_status_expired');
        $label_class = 'warning';
    }

    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' inline-block">' . $status . '</span>';
    } else {
        return $status;
    }
}

function format_proposal_status($status, $classes = '', $label = true)
{
    if ($status == 1) {
        $status      = _l('proposal_status_open');
        $label_class = 'default';
    } else if ($status == 2) {
        $status      = _l('proposal_status_declined');
        $label_class = 'danger';
    } else if ($status == 3) {
        $status      = _l('proposal_status_accepted');
        $label_class = 'success';
    } else if ($status == 4) {
        $status      = _l('proposal_status_sent');
        $label_class = 'info';
    } else {
       return $status;
    }

    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' inline-block">' . $status . '</span>';
    } else {
        return $status;
    }
}


/**
 * Update invoice status
 * @param  mixed $id invoice id
 * @return mixed invoice updates status / if no update return false
 */
function update_invoice_status($id, $cron = false)
{
    $CI =& get_instance();

    $CI->load->model('payments_model');
    $CI->load->model('invoices_model');
    $payments = $CI->payments_model->get_invoice_payments($id);

    $invoice         = $CI->invoices_model->get($id);
    $original_status = $invoice->status;
    $total_payments  = array();

    $status = 1;
    // Check if the first payments is equal to invoice total
    if (isset($payments[0])) {
        if ($payments[0]['amount'] == $invoice->total) {
            // Paid status
            $status = 2;
        } else {
            foreach ($payments as $payment) {
                array_push($total_payments, $payment['amount']);
            }

            $total = array_sum($total_payments);
            if ($total == $invoice->total) {
                // Paid status
                $status = 2;
            } else if ($total == 0) {
                // Unpaid status
                $status = 1;
            } else {
                if ($invoice->duedate != null) {
                    if (date('Y-m-d', strtotime($invoice->duedate)) < date('Y-m-d')) {
                        // Overdue status
                        $status = 4;

                    } else if($total > 0){
                        // Not paid completely status
                        $status = 3;
                    }
                } else {
                    // Not paid completely status
                    $status = 3;
                }
            }
        }
    } else {
        if ($invoice->duedate != null) {
            if (date('Y-m-d', strtotime($invoice->duedate)) < date('Y-m-d')) {
                // Overdue status
                $status = 4;
            }
        }
    }
    $CI->db->where('id', $id);
    $CI->db->update('tblinvoices', array(
        'status' => $status
    ));

    if ($CI->db->affected_rows() > 0) {

        $_from_cron = '';
        if ($cron == true) {
            $_from_cron = '[CRON]';
        }
        logActivity('- Invoice Status Updated ' . $_from_cron . ' [Invoice Number: ' . format_invoice_number($invoice->id) . ', From: ' . format_invoice_status($original_status, '', false) . ' To: ' . format_invoice_status($status, '', false) . ']', NULL, $cron);
        $CI->invoices_model->log_invoice_activity($invoice->id, '' . $_from_cron . ' Invoice Status Updated: From: ' . format_invoice_status($original_status, '',false) . ' To: ' . format_invoice_status($status, '',false
            ) . '', $cron);
        return $status;
    }

    return false;
}

/**
 * Check if the give invoice id is last invoice
 * @param  mixed  $id invoice id
 * @return boolean
 */
function is_last_invoice($id)
{
    $year = get_option('invoice_year');
    $CI =& get_instance();
    $CI->db->select('id')->from('tblinvoices')->where('year', $year)->order_by('id', 'desc')->limit(1);
    $query = $CI->db->get();

    $last_invoice_id = $query->row()->id;
    if ($last_invoice_id == $id) {
        return true;
    }

    return false;
}
/**
 * Check if the give estimate id is last invoice
 * @since Version 1.0.2
 * @param  mixed  $id estimateid
 * @return boolean
 */
function is_last_estimate($id)
{

    $year = get_option('estimate_year');
    $CI =& get_instance();
    $CI->db->select('id')->from('tblestimates')->where('year', $year)->order_by('id', 'desc')->limit(1);
    $query = $CI->db->get();

    $last_estimate_id = $query->row()->id;
    if ($last_estimate_id == $id) {
        return true;
    }

    return false;
}
/**
 * Format invoice number based on description
 * @param  mixed $id
 * @return string
 */
function format_invoice_number($id)
{   $CI = &get_instance();
    $CI->db->select('year,number')->from('tblinvoices')->where('id',$id);
    $invoice = $CI->db->get()->row();
    $format = get_option('invoice_number_format');
    if ($format == 1) {
        // Number based
        return get_option('invoice_prefix') . str_pad($invoice->number, get_option('number_padding_invoice_and_estimate'), '0', STR_PAD_LEFT);
    } else if ($format == 2) {
        return get_option('invoice_prefix') . $invoice->year . '/' . str_pad($invoice->number, get_option('number_padding_invoice_and_estimate'), '0', STR_PAD_LEFT);
    }
    return $number;
}
/**
 * Format estimate number based on description
 * @since  Version 1.0.2
 * @param  mixed $id
 * @return string
 */
function format_estimate_number($id)
{
    $CI = &get_instance();
    $CI->db->select('year,number')->from('tblestimates')->where('id',$id);
    $estimate = $CI->db->get()->row();
    $format = get_option('estimate_number_format');
    if ($format == 1) {
        // Number based
        return get_option('estimate_prefix') . str_pad($estimate->number, get_option('number_padding_invoice_and_estimate'), '0', STR_PAD_LEFT);
    } else if ($format == 2) {
        return get_option('estimate_prefix') . $estimate->year . '/' . str_pad($estimate->number, get_option('number_padding_invoice_and_estimate'), '0', STR_PAD_LEFT);
    }
    return $number;
}

/**
 * Helper function to get tax by passedid
 * @param  integer $id taxid
 * @return object
 */
function get_tax_by_id($id)
{
    $CI =& get_instance();
    $CI->db->where('id', $id);
    return $CI->db->get('tbltaxes')->row();
}
/**
 * Check if payment mode is allowed for specific invoice
 * @param  mixed  $id payment mode id
 * @param  mixed  $invoiceid invoice id
 * @return boolean
 */
function is_payment_mode_allowed_for_invoice($id, $invoiceid)
{
    $CI =& get_instance();

    $CI->db->select('tblcurrencies.name as currency_name,allowed_payment_modes')->from('tblinvoices')->join('tblcurrencies', 'tblcurrencies.id = tblinvoices.currency', 'left')->where('tblinvoices.id', $invoiceid);
    $invoice = $CI->db->get()->row();

    $allowed_modes = $invoice->allowed_payment_modes;

    if (!is_null($allowed_modes)) {
        $allowed_modes = unserialize($allowed_modes);
        if (count($allowed_modes) == 0) {
            return false;
        } else {
            foreach ($allowed_modes as $mode) {
                if ($mode == $id) {
                // is offline payment mode
                  if(is_numeric($id)){
                    return true;
                }
                // check currencies
                $currencies = explode(',', get_option('paymentmethod_' . $id . '_currencies'));
                foreach ($currencies as $currency) {
                    $currency = trim($currency);
                    if (strtoupper($currency) == strtoupper($invoice->currency_name)) {
                        return true;
                    }
                }
                return false;
            }
        }
    }
} else {
    return false;
}
return false;
}
/**
 * Check if invoice mode exists in invoice
 * @since  Version 1.0.1
 * @param  array  $modes     all invoice modes
 * @param  mixed  $invoiceid invoice id
 * @param  boolean $offline   should check offline or online modes
 * @return boolean
 */
function found_invoice_mode($modes, $invoiceid, $offline = true)
{
    $CI =& get_instance();

    $CI->db->select('tblcurrencies.name as currency_name,allowed_payment_modes')->from('tblinvoices')->join('tblcurrencies', 'tblcurrencies.id = tblinvoices.currency', 'left')->where('tblinvoices.id', $invoiceid);
    $invoice = $CI->db->get()->row();

    if (!is_null($invoice->allowed_payment_modes)) {
        $invoice->allowed_payment_modes = unserialize($invoice->allowed_payment_modes);

        if (count($invoice->allowed_payment_modes) == 0) {
            return false;
        } else {
            foreach ($modes as $mode) {
                if ($offline == true) {
                    if (is_numeric($mode['id'])) {
                        foreach ($invoice->allowed_payment_modes as $allowed_mode) {
                            if ($allowed_mode == $mode['id']) {
                                return true;
                            }
                        }
                    }
                } else {
                    if (!is_numeric($mode['id']) && !empty($mode['id'])) {
                        foreach ($invoice->allowed_payment_modes as $allowed_mode) {
                            if ($allowed_mode == $mode['id']) {
                                // Check for currencies
                                $currencies = explode(',', get_option('paymentmethod_' . $mode['id'] . '_currencies'));
                                foreach ($currencies as $currency) {
                                    $currency = trim($currency);
                                    if (strtoupper($currency) == strtoupper($invoice->currency_name)) {
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return false;
}
function load_pdf_language($clientid){
    $CI = &get_instance();
    $lang = get_option('active_language');
    // When cron or email sending pdf document the pdfs need to be on the client language
    $language = get_client_default_language($clientid);
    if(DEFINED('CRON') || DEFINED('EMAIL_TEMPLATE_SEND')){
        if(!empty($language)){
            $lang = $language;
        }
    } else {
        if(get_option('output_client_pdfs_from_admin_area_in_client_language') == 1){
            if(!empty($language)){
                $lang = $language;
            }
        }
    }
    if(file_exists(APPPATH .'language/'.$lang)){
        $CI->lang->load($lang.'_lang',$lang);
    }
}
/**
 * Prepare general invoice pdf
 * @param  object $invoice Invoice as object with all necessary fields
 * @return mixed object
 */
function invoice_pdf($invoice,$tag = '')
{
    $CI =& get_instance();
    load_pdf_language($invoice->clientid);
    $CI->load->library('pdf');
    $invoice_number = format_invoice_number($invoice->id);
    $pdf            = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetTitle($invoice_number);
    $CI->pdf->SetMargins(PDF_MARGIN_LEFT, 26, PDF_MARGIN_RIGHT);
    $CI->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $CI->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $CI->pdf->SetAutoPageBreak(TRUE, 30);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont('freesans', '', 10);
    $pdf->AddPage();
    $status = $invoice->status;
    include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/invoicepdf.php');
    return $pdf;
}
/**
 * Generate payment pdf
 * @since  Version 1.0.1
 * @param  object $payment All payment data
 * @return mixed object
 */
function payment_pdf($payment,$tag = '')
{

    $CI =& get_instance();
    load_pdf_language($payment->invoice_data->clientid);

    $CI->load->library('pdf');
    $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetTitle(_l('payment') . '#-' . $payment->id);
    $CI->pdf->SetMargins(PDF_MARGIN_LEFT, 26, PDF_MARGIN_RIGHT);
    $CI->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $CI->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $CI->pdf->SetAutoPageBreak(TRUE, 30);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont('freesans', '', 10);
    $pdf->AddPage();
    include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/paymentpdf.php');
    return $pdf;
}

/**
 * Prepare general estimate pdf
 * @since  Version 1.0.2
 * @param  object $estimate estimate as object with all necessary fields
 * @return mixed object
 */
function estimate_pdf($estimate,$tag = '')
{
    $CI =& get_instance();
    load_pdf_language($estimate->clientid);
    $CI->load->library('pdf');
    $estimate_number = format_estimate_number($estimate->id);
    $pdf            = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetTitle($estimate_number);
    $CI->pdf->SetMargins(PDF_MARGIN_LEFT, 26, PDF_MARGIN_RIGHT);
    $CI->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $CI->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $CI->pdf->SetAutoPageBreak(TRUE, 30);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont('freesans', '', 10);
    $pdf->AddPage();
    $status = $estimate->status;
    include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/estimatepdf.php');
    return $pdf;
}
function proposal_pdf($proposal){

    $CI =& get_instance();

    if($proposal->rel_id != NULL && $proposal->rel_type == 'customer'){
        load_pdf_language($proposal->rel_id);
    }

    $CI->load->library('pdf');
    $pdf            = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);

    $CI->pdf->SetMargins(PDF_MARGIN_LEFT, 26, PDF_MARGIN_RIGHT);
    $CI->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $CI->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $CI->pdf->SetAutoPageBreak(TRUE, 30);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont('freesans', '', 10);
    $pdf->AddPage();

    $CI->load->model('currencies_model');

    $total = '';
    if($proposal->total != 0){
        $total = format_money($proposal->total,$CI->currencies_model->get($proposal->currency)->symbol);
        $total = _l('proposal_total') . ': ' .$total;
    }
    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $proposal->content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<br><br><div>$1</div><br><br>', $proposal->content);
    // Add cellpadding to all tables inside the html
    $proposal->content = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="4">', $proposal->content);
    // Remove white spaces cased by the html editor ex. <td>  item</td>
    $proposal->content = preg_replace('/[\t\n\r\0\x0B]/', '', $proposal->content);
    $proposal->content = preg_replace('/([\s])\1+/', ' ', $proposal->content);
    include(APPPATH . 'views/themes/' . active_clients_theme() . '/views/proposalpdf.php');

    return $pdf;
}
