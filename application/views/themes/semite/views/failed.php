<?php if ($secure) { ?>
    <div class="panel_s">
    <div class="panel-heading">
        Merchant Dashboard
    </div>
    <div class="panel-body">
<?php } ?>
<div style="text-align: center">
    <img src="<?php echo template_assets_url(); ?>images/cancelled.png" style="width: 200px">
    <h1 class="text-danger">Your Payment has been Declined.</h1>
</div>
<h4>Dear Customer</h4>
<p>Your payment has been declined.</p>
    <p>Your Transaction ID is : <b><?php echo $webhookresponse['SemiteId']?></b></p>
    <p>Your Transaction GUID is : <b><?php echo $webhookresponse['SemiteGuid']?></b></p>
    <p>Customer Tracking code is : <b><?php echo $webhookrequest['trackingMemberCode']?></b></p>
<br/>
<h5>Reason : </h5>
<p class="text-danger"><b>***<?php echo $reason?></b></p>
<p>Thank you!</p>
<div style="text-align: center">
    <a href="<?php echo site_url()?>terminal" class="btn btn-primary">Back to Virtual Terminal</a>
</div>
<?php if ($secure) { ?>
    </div>
    </div>
<?php } ?>