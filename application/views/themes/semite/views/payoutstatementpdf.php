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

$startDate = date('l jS \of F Y',strtotime($settlement->startDate));
$endDate = date('l jS \of F Y',strtotime($settlement->endDate));

$grossVolume = format_money($settlement->grossVolume,$settlement->currency);
$approvedVolume = format_money($settlement->approvedVolume,$settlement->currency);
$declinedVolume = format_money($settlement->declinedVolume,$settlement->currency);
$discount = format_money($settlement->discount,$settlement->currency);
$processingFee = format_money($settlement->processingFee,$settlement->currency);
$reserveAmount = format_money($settlement->reserveAmount,$settlement->currency);


foreach ($invoiceItems as $invoiceitem){

    $invoiceTotal = $invoiceTotal + $invoiceitem['rate'];
    $invoiceHtml .='<tr>
        <td width="50%"><b>'.$invoiceitem['description'].'</b></td>
        <td>'.format_money($invoiceitem['rate'],$settlement->currency).'</td>
    </tr>';
}

$totalFees = format_money($settlement->discount + $settlement->processingFee + $invoiceTotal,$settlement->currency);

$totalPayout = format_money($settlement->netProcessing-$invoiceTotal,$settlement->currency);
$retrsined = format_money(0,$settlement->currency);

$html = <<<EOF
<style>
$css
</style>
<h3>Merchant Statement [ $client->company ]</h3>
<h4>Billing Statement</h4>
<dl>
  <dt>$client->firstname $client->lastname</dt>
  <dt>$client->company</dt>
  <dt>$client->billing_street</dt>
  <dt>$client->billing_city / $client->billing_state  - $client->billing_zip</dt>
  <dt>Costa Rika</dt>
</dl>
<h4>Merchant Details</h4>
<table cellspacing="0" cellpadding="5" border="0">
    <tr>
        <td width="20%"><b>Date</b></td>
        <td>$settlement->date_added</td>
    </tr>
    <tr>
        <td><b>Merchant</b></td>
        <td>$client->firstname $client->lastname</td>
    </tr>
    <tr>
        <td><b>Account Name</b></td>
        <td>$client->company</td>
    </tr>
    <tr>
        <td><b>Period</b></td>
        <td>$startDate - $endDate</td>
    </tr>
    <tr>
        <td><b>Currency</b></td>
        <td>$settlement->currency</td>
    </tr>
    <tr>
        <td><b>Payment Method</b></td>
        <td>Credit Card Processing</td>
    </tr>
</table>
<h4>Processing Details</h4>
<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="0">
<thead>
    <tr height="30" bgcolor="#3A4656" style="color:#fff;text-align:center">
        <th class="text-center">Total Transaction</th>
        <th class="text-center">Gross Volume</th>
        <th class="text-center">Declined Volume</th>
        <th class="text-center">Approved Volume</th>
    </tr>
</thead>
<tbody>
    <tr>
        <td style="text-align:center">$settlement->saleCount</td>
        <td style="text-align:center">$grossVolume</td>
        <td style="text-align:center">$declinedVolume</td>
        <td style="text-align:center">$approvedVolume</td>
    </tr>
</tbody>
</table>
<h4>Fees</h4>
<table cellspacing="0" cellpadding="5" border="0">
    <tr>
        <td width="50%"><b>Merchant Discount(9%)</b></td>
        <td>$discount</td>
    </tr>
    <tr>
        <td width="50%"><b>Gateway Processing Fee</b></td>
        <td>$processingFee</td>
    </tr>
    $invoiceHtml
</table>
<table cellspacing="0" cellpadding="5" border="0">
    <tr height="30" bgcolor="#3A4656" style="color:#fff;text-align:center">
        <td><b>Total Fee(s)</b></td>
        <td>$totalFees</td>
    </tr>
</table>
<hr/>
<h4>Rolling Reserve</h4>
<table cellspacing="0" cellpadding="5" border="0">
    <tr>
        <td><b>Reserve Amount(10%)</b></td>
        <td>$reserveAmount</td>
    </tr>
</table>
<hr/>
<h4>Merchant Payout Information</h4>
<table cellspacing="0" cellpadding="5" border="0">
    <tr>
        <td width="50%"><b>Previous Account Balance</b></td>
        <td>0</td>
    </tr>
    <tr>
        <td><b>New Account Balance</b></td>
        <td>$approvedVolume</td>
    </tr>
    <tr>
        <td><b>Total Account Balance</b></td>
        <td>$approvedVolume</td>
    </tr>
    <tr>
        <td><b>Total Payout</b></td>
        <td>$totalPayout</td>
    </tr>
    <tr>
        <td><b>Account Balance Retained</b></td>
        <td>$retrained/td>
    </tr>
</table>
<hr/>
<p style="text-align:center">
Thank you for your business!
</p>
EOF;

$pdf->writeHTML($html, true, false, true, false, '');
