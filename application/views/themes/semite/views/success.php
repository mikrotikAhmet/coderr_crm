<?php if ($threeds) { ?>
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
<p>Your Order number is : <b><?php echo $transaction->trackingCode?></b></p>
<p>You will receive email confirmation shortly to : <b><?php echo $additionalInfo->email?></b></p>
<p>Thank you!</p>
<div style="text-align: center">
    <a href="<?php echo site_url()?>terminal" class="btn btn-primary">Back to Virtual Terminal</a>
</div>
<?php if ($threeds) { ?>
    </div>
    </div>
<?php } ?>