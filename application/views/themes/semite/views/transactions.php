<div class="col-md-2">
    <div class="panel_s">
        <div class="panel-heading">
            Filter Transactions
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="control-label" for="input-min">Start Date</label>
                <input type="text" id="min" placeholder="Start date" class="form-control"/>
            </div>
            <div class="form-group">
                <label class="control-label" for="input-max">End Date</label>
                <input type="text" id="max" placeholder="End date" class="form-control"/>
            </div>
            <div class="form-group">
                <label class="control-label" for="input-min">Method</label>
                <select name="method" id="method" class="form-control">
                    <option value=""></option>
                    <option value="charge">Charge</option>
                    <option value="refund">Refund</option>
                    <option value="authorize">Authorize</option>
                    <option value="capture">Capture</option>
                    <option value="void">Void</option>
                    <option value="payment">Payment</option>
                </select>
            </div>
            <div class="form-group">
                <label class="control-label" for="input-status">Transaction status</label>
                <select name="status" id="status" class="form-control">
                    <option value=""></option>
                    <option value="Approved">Approved</option>
                    <option value="Declined">Declined</option>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6"><button type="button" class="btn btn-primary btn-sm reset-filter">Reset</button></div>
                <div class="col-md-6"><button type="button" class="btn btn-default btn-sm refresh-data pull-right">Reload</button></div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-10">
    <div class="panel_s">
        <div class="panel-body">
            <?php if (!$transactions) { ?>
                <h3 class="text-muted">All transactions</h3>
                <p>No transactions were made</p>
            <?php } else { ?>
                <?php get_template_part('transaction_list');?>
            <?php } ?>
        </div>
    </div>
</div>