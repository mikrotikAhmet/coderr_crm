<div class="col-sm-8">
    <ul class="list-group">
        <li class="list-group-item">
            <span class="badge"><?php echo $transaction->transactionid?></span>
            Transaction ID
        </li>
        <li class="list-group-item">
            <span class="badge"><?php echo format_money($transaction->settlement,$currency->getNameById($client->default_currency))?></span>
            Amount Processed
        </li>
        <li class="list-group-item">
            <span class="badge"><?php echo $transaction->transactionguid?></span>
            Authorization ID
        </li>
        <?php if ($transaction->enrolled) { ?>
            <li class="list-group-item">
                <span class="badge"><?php echo $transaction->xid?></span>
                Xid
            </li>
        <?php } ?>
    </ul>
</div>
<div class="col-sm-4">
    <ul class="list-group">
        <li class="list-group-item">
            <span class="badge"><?php echo $transaction->trackingCode?></span>
            Member Tracking Number
        </li>
        <?php if ($transaction->referenceid) { ?>
        <li class="list-group-item">
            <span class="badge"><?php echo $transaction->referenceid?></span>
            Reference Transaction ID
        </li>
        <?php } ?>
        <li class="list-group-item">
            <span class="badge"><?php echo ($transaction->enrolled ? 'Yes' : 'No')?></span>
            3DS
        </li>
        <li class="list-group-item">
            <span class="badge"><?php echo $transaction->card?></span>
            Card number
        </li>
        <li class="list-group-item">
            <span class="badge"><?php echo $transaction->type?></span>
            Card type
        </li>
        <li class="list-group-item text-center">
            <?php if ($transaction->status == 0 && $transaction->refunded == 0 && ($transaction->payment == 1 || $transaction->captured == 1)) { ?>
                <?php if ($transaction->payment==1) { ?>
                    <button class="btn btn-default refund" id="refund-<?php echo $transaction->transactionid?>"><i class="fa fa-reply"></i> Refund</button>
                <?php } elseif ($transaction->captured == 1 && $transaction->refunded == 0 && $transaction->method == "Capture") { ?>
                    <button class="btn btn-default refund" id="refund-<?php echo ($useOriginal && $transaction->method == "Capture" ? $transaction->transactionid : $transaction->referenceid)?>"><i class="fa fa-reply"></i> Refund Captured</button>
                <?php } elseif ($transaction->captured == 1 && $transaction->method == "Authorize") { ?>
                        <span class="label label-success inline-block fa fa-check">Captured</span>
                    <?php } ?>
            <?php }  elseif ($transaction->refunded == 1) { ?>
                <span class="label label-success inline-block fa fa-check">Refunded</span>
            <?php } ?>
            <?php if ($transaction->status == 0 && $transaction->authorized == 1 && ($transaction->captured == 0 && $transaction->voided == 0)) { ?>
                <button class="btn btn-success capture" id="capture-<?php echo $transaction->transactionid?>"><i class="fa fa-check"></i> Capture</button>
                <button class="btn btn-danger void" id="void-<?php echo $transaction->transactionid?>"><i class="fa fa-close"></i> Void</button>
            <?php } ?>
            <?php if ($transaction->status == 0 && $transaction->authorized == 1 && ($transaction->captured == 0 && $transaction->voided == 1)) { ?>
                <span class="label label-success inline-block fa fa-check">Voided</span>
            <?php } ?>
        </li>
    </ul>
</div>
<script>
    $('.refund').on('click',function(){

        var str = this.id;

        var res = str.split("-");;

        var data = $('#form-detail-'+res[1]).serialize();

        swal({
                title: "Are you sure?",
                text: "Transaction amount will be refunded!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Yes, refund!",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function(isConfirm){

                if (isConfirm) {
                    setTimeout(function () {
                        $.ajax({
                            url: site_url + 'transactions/refund/'+res[1],
                            type: 'POST',
                            dataType: 'json',
                            data: data,
                            beforeSend: function () {

                            },
                            complete: function () {

                            },
                            success: function (json) {

                                var message = json.Message;

                                if (json.hasOwnProperty('code')) {

                                    message = json.Message
                                }

                                if (json.hasOwnProperty('code') || json.Result == "1") {

                                    swal({
                                            title: "Error",
                                            text: message,
                                            type: "error"
                                        },
                                        function (isConfirm) {
                                            $('#transaction-log').DataTable().ajax.reload();
                                        });

                                } else {
                                    swal({
                                            title: "Success",
                                            text: message,
                                            type: "success"
                                        },
                                        function (isConfirm) {
                                            $('#transaction-log').DataTable().ajax.reload();
                                        });
                                }

                            }
                        });
                    },2000);

                } else {
                    setTimeout(function () {
                        swal("Cancelled", "Operation cancelled!", "error");
                    },200);
                }

            });
    });

    $('.void').on('click',function(){

        var str = this.id;

        var res = str.split("-");;

        var data = $('#form-detail-'+res[1]).serialize();

        swal({
                title: "Are you sure?",
                text: "Transaction wil be voided!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Yes, void!",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function(isConfirm){

                if (isConfirm) {
                    setTimeout(function () {
                        $.ajax({
                            url: site_url + 'transactions/void/'+res[1],
                            type: 'POST',
                            dataType: 'json',
                            data: data,
                            beforeSend: function () {

                            },
                            complete: function () {

                            },
                            success: function (json) {

                                var message = json.Message;

                                if (json.hasOwnProperty('code')) {

                                    message = json.Message
                                }

                                if (json.hasOwnProperty('code') || json.Result == "1") {

                                    swal({
                                            title: "Error",
                                            text: message,
                                            type: "error"
                                        },
                                        function (isConfirm) {
                                            $('#transaction-log').DataTable().ajax.reload();
                                        });

                                } else {
                                    swal({
                                            title: "Success",
                                            text: message,
                                            type: "success"
                                        },
                                        function (isConfirm) {
                                            $('#transaction-log').DataTable().ajax.reload();
                                        });
                                }

                            }
                        });
                    },2000);

                } else {
                    setTimeout(function () {
                        swal("Cancelled", "Operation cancelled!", "error");
                    },200);
                }

            });
    });

    $('.capture').on('click',function(){

        var str = this.id;

        var res = str.split("-");;

        var data = $('#form-detail-'+res[1]).serialize();

        swal({
                title: "Are you sure?",
                text: "Transaction wil be captured!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Yes, capture!",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function(isConfirm){

                if (isConfirm) {
                    setTimeout(function () {
                        $.ajax({
                            url: site_url + 'transactions/capture/'+res[1],
                            type: 'POST',
                            dataType: 'json',
                            data: data,
                            beforeSend: function () {

                            },
                            complete: function () {


                            },
                            success: function (json) {

                                var message = json.Message;

                                if (json.hasOwnProperty('code')) {

                                    message = json.Message
                                }

                                if (json.hasOwnProperty('code') || json.Result == "1") {

                                        swal({
                                                title: "Error",
                                                text: message,
                                                type: "error"
                                            },
                                            function (isConfirm) {
                                                $('#transaction-log').DataTable().ajax.reload();
                                            });

                                    } else {
                                        swal({
                                                title: "Success",
                                                text: message,
                                                type: "success"
                                            },
                                            function (isConfirm) {
                                                $('#transaction-log').DataTable().ajax.reload();
                                            });
                                    }
                                }
                        });
                    },2000);

                } else {
                    setTimeout(function () {
                        swal("Cancelled", "Operation cancelled!", "error");
                    },200);
                }

            });
    });
</script>