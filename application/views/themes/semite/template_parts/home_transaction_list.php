<div class="row">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-heading">
                <h3 class="text-muted">Last 10 transactions</h3>
            </div>
            <div class="panel-body">
                <table id="transaction-log" class="table table-striped-col">
                    <thead>
                    <tr>
                        <th width="5%"></th>
                        <th>Transaction ID</th>
                        <th>Transaction Date</th>
                        <th>Type</th>
                        <th>Card Used</th>
                        <th>Card Type</th>
                        <th class="">Amount</th>
                        <th class="">Status</th>
                        <th class="text-center">Is Secure</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="panel-footer">
                <a href="<?php echo site_url('transactions')?>" class="btn btn-link">All transactions</a>
            </div>
        </div>
    </div>
</div>