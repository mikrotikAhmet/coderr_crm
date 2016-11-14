<form   enctype="multipart/form-data" id="merchant-processor-form" class="" novalidate="novalidate">
    <input type="hidden" name="merchantid" value="<?php echo $merchant_processor->merchantid?>"/>
    <input type="hidden" name="id" value="<?php echo $merchant_processor->id?>"/>
    <input type="hidden" name="processorid" value="<?php echo $processor->id?>"/>
    <input type="hidden" name="active" value="<?php echo $merchant_processor->active?>"/>
    <div class="col-md-12">
        <div class="col-md-3">
            <fieldset>
                <legend>API Credentials</legend>
                <div class="form-group">
                    <label class="control-label" for="processor-alias">Alias</label>
                    <input type="text" name="alias" id="processor-alias" class="form-control" value="<?php echo $merchant_processor->alias?>"/>
                </div>
                <div class="form-group">
                    <label class="control-label" for="processor-gateway_username">Gateway Username</label>
                    <input type="text" name="processor_data[gateway_username]" id="processor-gateway_username" class="form-control" value=""/>
                </div>
                <div class="form-group">
                    <label class="control-label" for="processor-gateway_password">Gateway Password</label>
                    <input type="text" name="processor_data[gateway_password]" id="processor-gateway_password" class="form-control" value=""/>
                </div>
                <div class="form-group">
                    <label class="control-label" for="processor-gateway_service_username">Gateway Service Username</label>
                    <input type="text" name="processor_data[gateway_service_username]" id="processor-gateway_service_username" class="form-control" value=""/>
                </div>
                <div class="form-group">
                    <label class="control-label" for="processor-gateway_service_password">Gateway Service Password</label>
                    <input type="text" name="processor_data[gateway_service_password]" id="processor-gateway_service_password" class="form-control" value=""/>
                </div>
                <div class="form-group">
                    <label class="control-label" for="processor-gateway_account_id">Gateway Account ID</label>
                    <input type="text" name="processor_data[gateway_account_id]" id="processor-gateway_account_id" class="form-control" value=""/>
                </div>
            </fieldset>
        </div>
        <div class="col-md-3">
            <fieldset>
                <legend>Processor Settings</legend>
                <div class="form-group">
                    <label class="control-label" for="mcc-code">MCC Code</label>
                    <input type="text" name="mccCode" id="mcc-code" class="form-control" value="<?php echo ($merchant_processor->mccCode ? $merchant_processor->mccCode : 0)?>"/>
                </div>
                <div class="form-group">
                    <label class="control-label" for="buy-rate">Buy Rate</label>
                    <input type="text" name="buyRate" id="buy-rate" class="form-control" value="<?php echo ($merchant_processor->buyRate ? $merchant_processor->buyRate : 0)?>"/>
                </div>
                <div class="form-group">
                    <label class="control-label" for="sale-rate">Sale Rate</label>
                    <input type="text" name="saleRate" id="sale-rate" class="form-control" value="<?php echo ($merchant_processor->saleRate ? $merchant_processor->saleRate : 0)?>"/>
                </div>
                <div class="form-group">
                    <label class="control-label" for="rollback-reserve">Rollback Reserve</label>
                    <input type="text" name="rollbackReserve" id="rollback-reserve" class="form-control" value="<?php echo ($merchant_processor->rollbackReserve ? $merchant_processor->rollbackReserve : 0)?>"/>
                </div>
                <div class="form-group">
                    <label class="control-label" for="processing-limit">Processing Limit</label>
                    <input type="text" name="processingLimit" id="processing_limit" class="form-control" value="<?php echo ($merchant_processor->processingLimit ? $merchant_processor->processingLimit : 0)?>"/>
                </div>
                <div class="form-group">
                    <label class="control-label" for="transaction-limit">Transaction Limit</label>
                    <input type="text" name="transactionLimit" id="transaction_limit" class="form-control" value="<?php echo ($merchant_processor->transactionLimit ? $merchant_processor->transactionLimit : 0)?>"/>
                </div>
            </fieldset>
        </div>
        <div class="col-md-2">
            <fieldset>
                <legend>Card Acceptance</legend>
                <div class=form-group">
                    <div class="checkbox checkbox-primary">
                        <input name="processor_data[visa]" type="checkbox" value="1">
                        <label>
                            Visa
                        </label>
                    </div>
                    <div class="checkbox checkbox-primary">
                        <input name="processor_data[mastercard]" type="checkbox" value="1">
                        <label>
                            Mastercard
                        </label>
                    </div>
                    <div class="checkbox checkbox-primary">
                        <input name="processor_data[amex]" type="checkbox" value="1">
                        <label>
                            American Express (AMEX)
                        </label>
                    </div>
                    <div class="checkbox checkbox-primary">
                        <input name="processor_data[discover]" type="checkbox" value="1">
                        <label>
                            Discover
                        </label>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <legend>Processing Definition</legend>
                <div class="checkbox checkbox-primary">
                    <input name="processor_data[use_original]" type="checkbox" value="1">
                    <label>
                        Original Transaction
                    </label>
                </div>
            </fieldset>
            <fieldset>
                <legend>3D-Secure Settings</legend>
                <div class="checkbox checkbox-primary">
                    <input name="processor_data[secure]" type="checkbox" value="1">
                    <label>
                        Enable 3D-Secure
                    </label>
                </div>
                <div class="form-group">
                    <label class="control-label" for="processor-3ds">3D-Secure MID</label>
                    <input type="text" name="processor_data[secure_id]" id="processor-secure_id" class="form-control" value=""/>
                </div>
            </fieldset>
        </div>
        <div class="col-md-2">
            <fieldset>
                <legend>Processor Fees</legend>
                <div class=form-group">
                    <div class="form-group">
                        <label class="control-label" for="transaction-fee">Transaction Fee</label>
                        <input type="text" name="transactionFee" id="transactionFee" class="form-control" value="<?php echo ($merchant_processor->transactionFee ? $merchant_processor->transactionFee : 0)?>"/>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</form>
<script>

</script>