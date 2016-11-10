<div class="col-md-12">
    <div class="panel_s">
        <?php
        if(has_customer_permission('terminal')){ ?>

            <div class="panel-heading">
                <h4>Batch Processing</h4>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="control-label" for="input-brn">Enter Batch Referal Number</label>
                    <input type="text" name="brn" id="brn" placeholder="Enter Batch Referal Number" class="form-control" maxlength="32"/>
                    <div class="alert alert-danger alert-validation" style="display: none">Referal Batch Number is required.</div>
                </div>
                <hr/>
                <div style="text-align: center">
                    <button type="button" id="batch_process" class="btn btn-success">Process Batch</button>
                </div>
            </div>
        <?php } ?>
    </div>
</div>