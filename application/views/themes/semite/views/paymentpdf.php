<?php

if($tag != ''){
    $pdf->SetFillColor(240,240,240);
    $pdf->SetDrawColor(245,245,245);
    $pdf->SetXY(0,0);
    $pdf->SetFont('freesans','B',15);
    $pdf->SetTextColor(0);
    $pdf->SetLineWidth(0.75);
    $pdf->StartTransform();
    $pdf->Rotate(-35,109,235);
    $pdf->Cell(100,1,strtoupper($tag),'TB',0,'C','1');
    $pdf->StopTransform();
    $pdf->SetFont('freesans','',10);
    $pdf->setX(10);
    $pdf->setY(23);
}

// Get Y position for the separation
$y            = $pdf->getY();
$invoice_info = '<b>' . get_option('invoice_company_name') . '</b><br />';
$invoice_info .= get_option('invoice_company_address') . '<br/>';
$invoice_info .= get_option('invoice_company_city') . ', ';
$invoice_info .= get_option('invoice_company_country_code') . ' ';
$invoice_info .= get_option('invoice_company_postal_code') . ' ';

// check for company custom fields
$custom_company_fields = get_company_custom_fields();
if (count($custom_company_fields) > 0) {
    $invoice_info .= '<br />';
}
foreach ($custom_company_fields as $field) {
    $invoice_info .= $field['label'] . ': ' . $field['value'] . '<br />';
}

$pdf->writeHTMLCell(91, '', '', $y, $invoice_info, 0, 0, false, true, 'J', true);

// Bill to
$client_details = '';
$client_details .= $payment->invoice_data->client->company . '<br />';
$client_details .= $payment->invoice_data->billing_street . '<br />' . $payment->invoice_data->billing_city . ', ' . $payment->invoice_data->billing_city .'<br />'.
get_country_short_name($payment->invoice_data->billing_country) . ',' . $payment->invoice_data->billing_zip . '<br />';
if (!empty($payment->invoice_data->client->vat)) {
    $client_details .= _l('invoice_vat') . ' ' . $payment->invoice_data->client->vat . '<br />';
}

$inserted_customer_custom_field = false;
// check for estimate custom fields which is checked show on pdf
$pdf_custom_fields              = get_custom_fields('customers', array(
    'show_on_pdf' => 1
));
foreach ($pdf_custom_fields as $field) {
    $value = get_custom_field_value($payment->invoice_data->client->userid, $field['id'], 'customers');
    if ($value == '') {
        continue;
    }
    $inserted_customer_custom_field = true;
    $client_details .= $field['name'] . ': ' . $value . '<br />';
}
if ($inserted_customer_custom_field) {
    $client_details .= '<br />';
}
// Write the client details
$pdf->writeHTMLCell(99, '', '', '', $client_details, 0, 1, false, true, 'R', true);
$pdf->SetFontSize(15);
$pdf->Ln(5);
$pdf->Cell(0, 0, strtoupper(_l('payment_receipt')), 0, 1, 'C', 0, '', 0);
$pdf->SetFontSize(10);
$pdf->Ln(15);
$pdf->Cell(0, 0, _l('payment_date') . ' ' . _d($payment->date), 0, 1, 'L', 0, '', 0);
$pdf->Ln(2);
$pdf->writeHTMLCell(80, '', '', '', '<hr/>', 0, 1, false, true, 'L', true);
$pdf->Cell(0, 0, _l('payment_view_mode') . ' ' . $payment->name, 0, 1, 'L', 0, '', 0);
if(!empty($payment->transactionid)) {
    $pdf->Ln(2);
    $pdf->writeHTMLCell(80, '', '', '', '<hr/>', 0, 1, false, true, 'L', true);
    $pdf->Cell(0, 0, _l('payment_transaction_id') . ' ' . $payment->transactionid, 0, 1, 'L', 0, '', 0);
}
$pdf->Ln(2);
$pdf->writeHTMLCell(80, '', '', '', '<hr />', 0, 1, false, true, 'L', true);
$pdf->SetFillColor(37, 155, 36);
$pdf->SetTextColor(255);
$pdf->SetFontSize(12);
$pdf->Ln(3);
$pdf->Cell(80, 10, _l('payment_total_amount'), 0, 1, 'C', '1');
$pdf->SetFontSize(11);
$pdf->Cell(80, 10, format_money($payment->amount, $payment->invoice_data->symbol), 0, 1, 'C', '1');
$pdf->Ln(5);
// The Table
$pdf->Ln(5);
$pdf->SetTextColor(0);
$pdf->SetFont('freesans', 'B', 14);
$pdf->Cell(0, 0, _l('payment_for_string'), 0, 1, 'L', 0, '', 0);
$pdf->SetFont('freesans', '', 10);
$pdf->Ln(5);
// Header
$tblhtml = '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="0">
<tr height="30" style="color:#fff;" bgcolor="#3A4656">
    <th width="25%;">' . _l('payment_table_invoice_number') . '</th>
    <th width="25%;">' . _l('payment_table_invoice_date') . '</th>
    <th width="25%;">' . _l('payment_table_invoice_amount_total') . '</th>
    <th width="25%;">' . _l('payment_table_payment_amount_total') . '</th>
</tr>';
$tblhtml .= '<tbody>';
$tblhtml .= '<tr>';
$tblhtml .= '<td>' . format_invoice_number($payment->invoice_data->id) . '</td>';
$tblhtml .= '<td>' . _d($payment->invoice_data->date) . '</td>';
$tblhtml .= '<td>' . format_money($payment->invoice_data->total, $payment->invoice_data->symbol) . '</td>';
$tblhtml .= '<td>' . format_money($payment->amount, $payment->invoice_data->symbol) . '</td>';
$tblhtml .= '</tr>';
$tblhtml .= '</tbody>';
$tblhtml .= '</table>';
$pdf->writeHTML($tblhtml, true, false, false, false, '');
