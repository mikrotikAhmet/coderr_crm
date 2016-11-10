<div class="col-md-12">
    <div class="panel_s">
        <?php
        if(has_customer_permission('terminal')){ ?>

            <div class="panel-heading">
                <h4>Virtual Terminal</h4>
            </div>
            <div class="panel-body">
                <div class="alert alert-danger alert-validation" id="general-error" style="display: none"></div>
                <?php echo form_open($this->uri->uri_string().'/process', array('class' => 'terminal-form')); ?>
                <input type="hidden" value="<?php echo $merchant->threeds_mode?>" name="threeds"/>
                <input type="hidden" value="<?php echo $merchant->id?>" name="merchant_id"/>
                <?php if ($merchant->threeds_mode) { ?>

                <div class="pull-right">
                    <img src="<?php echo template_assets_url(); ?>images/3ds.jpg" style="width : 50px">

                </div>
                <?php } ?>
                <fieldset>
                    <legend>Payment Information</legend>
                    <div class="col-md-12">
                        <div class="row">
                            <p class="text-info">NOTE : <b>Payment Method</b> is the combination of Authorization + Capture. If you use this method to charge your customer the amount must be min : 10.00 with equavalent currency.</p>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label" for="input-method">Choose your transaction type</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="Charge">Charge Method</option>
                                        <option value="Authorize">Authorize Method</option>
                                        <option value="Payment">Payment Method</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    <button type="button" id="proccess" class="btn btn-success">Process Transaction</button>
                </div>
                <?php if ($merchant->threeds_mode) { ?>
                <hr/>
                <h4>(*) BY CLICKING "SUBMIT" YOU AGREE TO OUR TERMS AND CONDITIONS, ESPECIALLY THOSE OUTLINED BELOW:</h4>
                <span class="text-muted">
                    <p>All online card transactions are processed by SEMITE DOO,BEOGRAD. All transactions require 3D secure approval any card transaction not 3D secure verified SEMITE DOO,BEOGRAD‚ can at their discretion refund and cancel the trade.</p>
                </span>
                <br/>
                <span class="text-danger">
                    <p>All Clients using the online payment portal do so in the knowledge their information is stored and used in compliance with PCI DSS protocols. ALL CLIENTS are reminded that no third party payments to other client trade accounts will be accepted. Clients replenishing their own trade accounts by means of credit or debit card waive the right to request chargeback or refunds of funds from their trade account. By using our online payment portal and a trade is executed you waive your right to refund or chargeback.</p>
                </span>
                <div style="text-align: center">
                    <img src="<?php echo template_assets_url(); ?>images/3dsecure.png">
                </div>
                <?php } ?>
                <?php echo form_close(); ?>
            </div>
        <?php } ?>
    </div>
</div>