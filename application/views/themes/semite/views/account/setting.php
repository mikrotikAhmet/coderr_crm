<div class="col-md-12">
    <div class="panel_s">
        <?php
        if(has_customer_permission('gateway')){ ?>

            <div class="panel-heading">
                Account Settings
            </div>
            <div class="panel_s">
                <div class="panel-body">
                    <h3>2-Way Authentication</h3>
                    <div class=form-group">
                        <div class="checkbox checkbox-primary">
                            <input name="twoway" type="checkbox" value="1">
                            <label>
                                2-Way Authentication uses your phone to provide an extra layer of security for your account.<br/><a href="#">learn more...</a>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel_s">
                <div class="panel-body">
                    <h3>Hosted Checkout Page</h3>
                    <p></p>
                </div>
            </div>
        <?php } ?>
    </div>
</div>