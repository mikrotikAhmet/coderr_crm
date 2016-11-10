<div class="col-md-12">
    <div class="panel_s">
        <?php
        if(has_customer_permission('invoices')){ ?>

            <div class="panel-heading">
                Merchant Dashboard
            </div>
            <div class="panel-body">
                <div style="text-align: center">
                    <img src="<?php echo template_assets_url(); ?>images/checkmark-png-28.png" style="width: 200px">
                    <h1 class="text-success">Your Payment has been succesfully processed!!</h1>
                </div>
                <h4>Dear Customer</h4>
                <p>Your Order number is : <b>654654-2016/220001</b></p>
                <p>You will receive email confirmation shortly to : <b>ahmet.gudenoglu@gmail.com</b></p>
                <p>Thank you!</p>
            </div>
        <?php } ?>
    </div>
</div>