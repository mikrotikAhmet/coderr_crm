<?php
//$pdf->setJPEGQuality(100);
//$custom_pdf_logo_image_url = get_option('custom_pdf_logo_image_url');
//if($custom_pdf_logo_image_url == ''){
//    $pdf->writeHTMLCell(35, 0, 10, 8, '<a href="'.site_url().'"><img src="'.site_url('uploads/company/'.get_option('company_logo')).'"></a>', 0, 1, false, true, 'L', false);
//} else {
//    $pdf->writeHTMLCell(35, 0, 10, 8, '<a href="'.site_url().'"><img src="'.$custom_pdf_logo_image_url.'"></a>', 0, 1, false, true, 'L', false);
//}
// Get Y position for the separation
$y            = $pdf->getY();
// Get the proposals css
$css = file_get_contents(FCPATH.'assets/css/proposals.css');
// Theese lines should aways at the end of the document left side. Dont indent these lines

$GrossProcessing = 0;
$ProcessingFee = 0;
$ReservedAmount = 0;
$RefundsVoids = 0;
$Chargebacks = 0;
$NetProcessing = 0;
$SaleCount = 0;
$RefundVoidCount = 0;
$ChargebackCount = 0;


foreach ($summary as $overview_summary){


    $GrossProcessing = $GrossProcessing + $overview_summary['gross_processing'];
    $ProcessingFee = $ProcessingFee + $overview_summary['processing_fee'];
    $ReservedAmount = $ReservedAmount + $overview_summary['reserved_amount'];
    $RefundsVoids = $RefundsVoids + $overview_summary['refunds_voids'];
    $Chargebacks = $Chargebacks + $overview_summary['chargebacks'];
    $NetProcessing = $NetProcessing + $overview_summary['net_processing'];
    $SaleCount = $SaleCount + $overview_summary['sale_count'];
    $RefundVoidCount = $RefundVoidCount + $overview_summary['refund_void_count'];
    $ChargebackCount = $ChargebackCount + $overview_summary['chargeback_count'];

    $html_summary .='<tr height="30" style="text-align:center">';
    $html_summary .='<td>'.$overview_summary['reporting_period'].'</td>';
    $html_summary .='<td>'.format_money($currency->convert($overview_summary['gross_processing'],$client_currency->name,$statement_currency)).'</td>';
    $html_summary .='<td>'.format_money($currency->convert($overview_summary['processing_fee'],$client_currency->name,$statement_currency)).'</td>';
    $html_summary .='<td>'.format_money($currency->convert($overview_summary['reserved_amount'],$client_currency->name,$statement_currency)).'</td>';
    $html_summary .='<td>'.format_money($currency->convert($overview_summary['refunds_voids'],$client_currency->name,$statement_currency)).'</td>';
    $html_summary .='<td>'.format_money($currency->convert($overview_summary['chargebacks'],$client_currency->name,$statement_currency)).'</td>';
    $html_summary .='<td>'.format_money($currency->convert($overview_summary['net_processing'],$client_currency->name,$statement_currency)).'</td>';
    $html_summary .='<td>'.$overview_summary['sale_count'].'</td>';
    $html_summary .='<td>'.$overview_summary['refund_void_count'].'</td>';
    $html_summary .='<td>'.$overview_summary['chargeback_count'].'</td>';
    $html_summary .='</tr>';
}

$total_summary .='<tr height="30" bgcolor="#3A4656" style="color:#fff;text-align:center">';
$total_summary .='<td>Total</td>';
$total_summary .='<td>'.format_money($currency->convert($GrossProcessing,$client_currency->name,$statement_currency)).'</td>';
$total_summary .='<td>'.format_money($currency->convert($ProcessingFee,$client_currency->name,$statement_currency)).'</td>';
$total_summary .='<td>'.format_money($currency->convert($ReservedAmount,$client_currency->name,$statement_currency)).'</td>';
$total_summary .='<td>'.format_money($currency->convert($RefundsVoids,$client_currency->name,$statement_currency)).'</td>';
$total_summary .='<td>'.format_money($currency->convert($Chargebacks,$client_currency->name,$statement_currency)).'</td>';
$total_summary .='<td>'.format_money($currency->convert($NetProcessing,$client_currency->name,$statement_currency)).'</td>';
$total_summary .='<td>'.$SaleCount.'</td>';
$total_summary .='<td>'.$RefundVoidCount.'</td>';
$total_summary .='<td>'.$ChargebackCount.'</td>';
$total_summary .='</tr>';

$html = <<<EOF
<style>
$css
</style>
<h3>Merchant Statement [ $client->company ]</h3>
<table class="table">
    <tr height="30">
        <td>Statement Date:</td>
        <td>$statementDate</td>
    </tr>
    <tr height="30">
        <td>Statement Period:</td>
        <td>$statement->startDate - $statement->endDate</td>
    </tr>
    <tr height="30">
        <td>Default Merchant Currency:</td>
        <td>$client_currency->name</td>
    </tr>
    <tr height="30">
        <td>Statement Currency:</td>
        <td>$statement_currency</td>
    </tr>
</table>
<h3>Account Summary</h3>
<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="0">
<thead>
    <tr height="30" bgcolor="#3A4656" style="color:#fff;text-align:center">
        <th class="text-center">Sales</th>
        <th class="text-center">Sales Volume</th>
        <th class="text-center">R / V</th>
        <th class="text-center">R / V Volume</th>
        <th class="text-center">R / V %</th>
        <th class="text-center">CB</th>
        <th class="text-center">CB Volume</th>
        <th class="text-center">CB %</th>
    </tr>
</thead>
<tbody>
    <tr>
        <td style="text-align:center">$statement->cc_count</td>
        <td style="text-align:center">$statement->cc_volume</td>
        <td style="text-align:center">$statement->cc_refunded_voided</td>
        <td style="text-align:center">$statement->cc_refunded_voided_volume</td>
        <td style="text-align:center">$statement->percent_cc_refunded_voided</td>
        <td style="text-align:center">$statement->cc_chargeback</td>
        <td style="text-align:center">$statement->cc_chargeback_volume</td>
        <td style="text-align:center">$statement->percent_cc_chargeback</td>
    </tr>
</tbody>
</table>
<h3>Recap</h3>
<table class="table">
    <tbody>
    <tr height="30">
        <td>Transaction Volume</td>
        <td>$statement->cc_volume</td>
    </tr>
    <tr height="30">
        <td>Refunds / Voids</td>
        <td>$statement->cc_refunded_voided_volume</td>
    </tr>
    <tr height="30">
        <td>Chargebacks</td>
        <td>$statement->cc_chargeback_volume</td>
    </tr>
    </tbody>
</table>
<h3>Recap Overview</h3>
<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="0">
    <tr height="30" bgcolor="#3A4656" style="color:#fff;">
        <td>Net Amount</td>
        <td>$statement->cc_net_volume</td>
    </tr>
</table>
<h3>General Overview</h3>
<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="0">
    <thead>
    <tr height="30" bgcolor="#3A4656" style="color:#fff;text-align:center">
        <th class="text-center">Reporting Period</th>
        <th class="text-center">Gross Processing</th>
        <th class="text-center">Processing Fees</th>
        <th class="text-center">Reserve Amount</th>
        <th class="text-center">Refunds / Voids</th>
        <th class="text-center">Chargebacks</th>
        <th class="text-center">Net Processing (R / V & CB)</th>
        <th class="text-center">Sale Count</th>
        <th class="text-center">Refund / Void Count</th>
        <th class="text-center">Chargeback Items</th>
    </tr>
    </thead>
    <tbody>
    $html_summary
    </tbody>
    <tfoot>
    $total_summary
    </tfoot>
</table>
<hr/>
<p style="text-align:center">
Thank you for your business!
</p>
EOF;




$pdf->writeHTML($html, true, false, true, false, '');
