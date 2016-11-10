<div class="col-sm-6">
    <ul class="list-group">
        <li class="list-group-item">
            <span class="badge"><?php echo $transaction->transactionid?></span>
            Transaction ID
        </li>
        <?php if ($transaction->referenceid) { ?>
            <li class="list-group-item">
                <span class="badge"><?php echo $transaction->referenceid?></span>
                Reference Transaction ID
            </li>
        <?php } ?>
        <?php if ($transaction->enrolled) { ?>
            <li class="list-group-item">
                <span class="badge"><?php echo $transaction->xid?></span>
                Xid
            </li>
        <?php } ?>
    </ul>
</div>
