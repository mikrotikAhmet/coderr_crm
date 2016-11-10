<?php

$pdf->setJPEGQuality(100);
$custom_pdf_logo_image_url = get_option('custom_pdf_logo_image_url');
if($custom_pdf_logo_image_url == ''){
    $pdf->writeHTMLCell(35, 0, 10, 8, '<a href="'.site_url().'"><img src="'.site_url('uploads/company/'.get_option('company_logo')).'"></a>', 0, 1, false, true, 'L', false);
} else {
    $pdf->writeHTMLCell(35, 0, 10, 8, '<a href="'.site_url().'"><img src="'.$custom_pdf_logo_image_url.'"></a>', 0, 1, false, true, 'L', false);
}
// Get Y position for the separation
$y            = $pdf->getY();
$invoice_info = '<b>' . get_option('invoice_company_name') . '</b><br />';
$invoice_info .= get_option('invoice_company_address') . '<br/>';
$invoice_info .= get_option('invoice_company_city') . ', ';
$invoice_info .= get_option('invoice_company_country_code') . ' ';
$invoice_info .= get_option('invoice_company_postal_code') . ' ';

// Check for company custom fields
$custom_company_fields = get_company_custom_fields();
if(count($custom_company_fields) > 0){
    $invoice_info .= '<br />';
}
foreach($custom_company_fields as $field){
    $invoice_info .= $field['label'] . ': ' . $field['value'] . '<br />';
}

$pdf->writeHTMLCell(91, '', '', $y, $invoice_info, 0, 0, false, true, 'J', true);

// Proposal to
$client_details = '<b>' ._l('proposal_to') . ':</b><br />';
$client_details .= $proposal->proposal_to . '<br />';
$client_details .= $proposal->address . '<br />' . $proposal->phone;

$pdf->writeHTMLCell(99, '', '', '', $client_details, 0, 1, false, true, 'R', true);



$pdf->ln(6);

// Get the proposals css
$css = file_get_contents(FCPATH.'assets/css/proposals.css');

$open_till = '';

if(!empty($proposal->open_till)){
    $open_till = _l('proposal_open_till'). ': ' . _d($proposal->open_till);
}
$proposal_date = _l('proposal_date') . ': ' . _d($proposal->date);

$custom_fields_data = '';

$pdf_custom_fields = get_custom_fields('proposal',array('show_on_pdf'=>1));
foreach($pdf_custom_fields as $field){
    $value = get_custom_field_value($proposal->id,$field['id'],'proposal');
    if($value == ''){continue;}
    $custom_fields_data .= $field['name'] . ': ' .  $value;
}
// Add new line if found custom fields so the custom field can go on the next line
if($custom_fields_data != ''){
    $custom_fields_data = '<br />' . $custom_fields_data;
}
// Theese lines should aways at the end of the document left side. Dont indent these lines
$html = <<<EOF
<style>
$css
</style>
<h1>$proposal->subject</h1>
$total
<br />
$proposal_date
<br />
$open_till
$custom_fields_data
$proposal->content
EOF;

$pdf->writeHTML($html, true, false, true, false, '');
