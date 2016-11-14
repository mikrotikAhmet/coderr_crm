<?php if ($secure) { ?>
    <div class="panel_s">
    <div class="panel-heading">
        Merchant Dashboard
    </div>
    <div class="panel-body">
<?php } ?>
<div style="text-align: center">
    <img src="<?php echo template_assets_url(); ?>images/checkmark-png-28.png" style="width: 200px">
    <h1 class="text-success">Your Payment has been succesfully processed!!</h1>
</div>
<h4>Dear Customer</h4>
<p>Your Transaction ID is : <b><?php echo $webhookresponse['SemiteId']?></b></p>
    <p>Your Transaction GUID is : <b><?php echo $webhookresponse['SemiteGuid']?></b></p>
    <p>Customer Tracking code is : <b><?php echo $webhookrequest['trackingMemberCode']?></b></p>
<p>Thank you!</p>
<div style="text-align: center">
    <a href="<?php echo site_url()?>terminal" class="btn btn-primary">Back to Virtual Terminal</a>
</div>
<?php if ($secure) { ?>
    </div>
    </div>
<?php } ?>