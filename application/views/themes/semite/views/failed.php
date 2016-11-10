<?php if ($threeds) { ?>
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
<p>Your pamyment has been declined.</p>
<br/>
<h5>Reason : </h5>
<p class="text-danger"><b>***<?php echo $reason?></b></p>
<p>Thank you!</p>
<div style="text-align: center">
    <a href="<?php echo site_url()?>terminal" class="btn btn-primary">Back to Virtual Terminal</a>
</div>
<?php if ($threeds) { ?>
    </div>
    </div>
<?php } ?>