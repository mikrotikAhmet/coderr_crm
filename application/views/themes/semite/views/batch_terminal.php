<div class="col-md-12">
    <div class="panel_s">
        <?php
        if(has_customer_permission('batch')){ ?>

            <div class="panel-heading">
                <h4>Batching Terminal (Offline Payment)</h4>
            </div>
            <div class="panel-body">
                <div class="alert alert-danger alert-validation" id="general-error" style="display: none"></div>
                <?php echo form_open($this->uri->uri_string().'/process', array('class' => 'terminal-form')); ?>
                <input type="hidden" value="<?php echo $merchant->threeds_mode?>" name="threeds"/>
                <input type="hidden" value="<?php echo $merchant->id?>" name="merchant_id"/>
                <fieldset>
                    <legend>Payment Information</legend>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="input-ccn">Credit Card Number</label>
                            <input type="text" name="creditCard[cardNumber]" id="creditCard" placeholder="Credit Card Number" class="form-control" maxlength="16"/>
                            <div class="alert alert-danger alert-validation" style="display: none">Invalid Credit Card Number.</div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="control-label" for="input-expmonth">Exp Month(MM) </label>
                                    <input type="text" name="creditCard[cardExpiryMonth]" placeholder="Exp Month(MM)" class="form-control"/>
                                    <div class="alert alert-danger alert-validation" style="display: none">The Expiry Month field is required.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="control-label" for="input-expyear">Exp Year(YYYY) </label>
                                    <input type="text" name="creditCard[cardExpiryYear]" placeholder="Exp Year(YYYY)" class="form-control"/>
                                    <div class="alert alert-danger alert-validation" style="display: none">The Expiry Year field is required.</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="input-min">Cardholder Name</label>
                            <input type="text" name="creditCard[cardholder]" placeholder="Cardholder Name" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="input-sc">Security Code</label>
                            <input type="text" name="creditCard[cardCvv]" placeholder="Security Code" class="form-control"/>
                            <div class="alert alert-danger alert-validation" style="display: none">The Card CVV field seemes like not valid.</div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="input-country">Country</label>
                            <select name="countryId" id="countryId" class="form-control">
                                <option value=""></option>
                                <?php foreach (get_all_countries() as $country) { ?>
                                    <option value="<?php echo $country['numcode']?>"><?php echo $country['short_name']?></option>
                                <?php } ?>
                            </select>
                            <div class="alert alert-danger alert-validation" style="display: none">Please select Country.</div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="input-country">Currency</label>
                            <select name="currencyId" id="currencyId" class="form-control" data-live-search="true">
                                <option value=""></option>
                                <?php foreach (Translator::getCurrencies() as $key=>$currency) { ?>
                                    <option value="<?php echo $currency?>"><?php echo $key?></option>
                                <?php } ?>
                            </select>
                            <div class="alert alert-danger alert-validation" style="display: none">Please select Currency.</div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Order Info</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label" for="input-amount">Amount of Sale</label>
                            <span class="text-muted text-info" style="font-size: 10px"><b>Use (.) for decimals. Ex:(9.99)</b></span>
                            <input type="text" name="amount" placeholder="Amount of Sale" class="form-control"/>
                            <div class="alert alert-danger alert-validation" style="display: none">The Amount field is required.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label" for="input-tracking">Invoice Number/Order Number/Tracking Member Code</label>
                            <input type="text" name="trackingMemberCode" placeholder="Invoice Number/Order Number" class="form-control"/>
                            <div class="alert alert-danger alert-validation" style="display: none">The Invoice Number / Order Number  field is required.</div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Additional Info</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label" for="input-phone">Phone</label>
                            <input type="text" name="additionalInfo[phone]" placeholder="Phone" class="form-control"/>
                            <div class="alert alert-danger alert-validation" style="display: none">Phone seems not valid.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label" for="input-email">Email</label>
                            <input type="text" name="additionalInfo[email]" placeholder="Email" class="form-control"/>
                            <div class="alert alert-danger alert-validation" style="display: none">The Email seems to be invalid.</div>
                        </div>
                    </div>
                </fieldset>
                <hr/>
                <div style="text-align: center">
                    <button type="button" id="proccess_batch" class="btn btn-success">Process Transaction</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        <?php } ?>
    </div>
</div>