<?php
/* Totals */
$total_transactions = total_rows('tbltransactions',array('merchantid'=>get_merchant_id(get_client_user_id())));
$total_approved = total_rows('tbltransactions',array('status'=>0,'merchantid'=>get_merchant_id(get_client_user_id())));
$total_declined = total_rows('tbltransactions',array('status'=>1,'merchantid'=>get_merchant_id(get_client_user_id())));
$total_enrolled = total_rows('tbltransactions',array('status'=>0,'enrolled'=>1,'merchantid'=>get_merchant_id(get_client_user_id())));
$total_retrived = total_rows('tbltransactions',array('status'=>0,'retrived'=>1,'merchantid'=>get_merchant_id(get_client_user_id())));

/* In Percent */
$percent_approved = ($total_transactions > 0 ? number_format(($total_approved * 100) / $total_transactions,2) : 0);
$percent_declined = ($total_transactions > 0 ? number_format(($total_declined * 100) / $total_transactions,2) : 0);
$percent_enrolled = ($total_approved > 0 ? number_format(($total_enrolled * 100) / $total_approved,2) : 0);
$percent_retrived = ($total_approved > 0 ? number_format(($total_retrived * 100) / $total_approved,2) : 0);

?>
<div class="panel-body">
    <div class="row text-left invoice-quick-info">
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-8">
                    <a href="#">
                        <h5 class="bold">Approved</h5></a>
                </div>
                <div class="col-md-4 text-right bold">
                    <?php echo $total_approved; ?> / <?php echo $total_transactions; ?>
                </div>
                <div class="col-md-12">
                    <div class="progress no-margin">
                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_approved; ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-8">
                    <a href="#">
                        <h5 class="bold">Declined</h5></a>
                </div>
                <div class="col-md-4 text-right bold">
                    <?php echo $total_declined; ?> / <?php echo $total_transactions; ?>
                </div>
                <div class="col-md-12">
                    <div class="progress no-margin">
                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_declined; ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-8">
                    <a href="#">
                        <h5 class="bold">3D-Secure Approved</h5></a>
                </div>
                <div class="col-md-4 text-right bold">
                    <?php echo $total_enrolled; ?> / <?php echo $total_approved; ?>
                </div>
                <div class="col-md-12">
                    <div class="progress no-margin">
                        <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_enrolled; ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-8">
                    <a href="#">
                        <h5 class="bold">Chargeback(s)</h5></a>
                </div>
                <div class="col-md-4 text-right bold">
                    <?php echo $total_retrived; ?> / <?php echo $total_approved; ?>
                </div>
                <div class="col-md-12">
                    <div class="progress no-margin">
                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_retrived; ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>