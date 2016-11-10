<?php init_head(); ?>
<?php
/* In Totals */
$total_transactions = total_rows('tbltransactions',array('merchantid'=>$merchant->id));
$total_approved = total_rows('tbltransactions',array('status'=>0,'merchantid'=>$merchant->id));

/* In Percent */
$percent_acceptance = ($total_transactions > 0 ? number_format(($total_approved * 100) / $total_transactions,2) : 0);
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <?php echo $title; ?>
                    </div>
                    <div class="panel-body">

                        <?php if (isset($merchant)) { ?>
                            <?php echo form_hidden('isedit'); ?>
                            <?php echo form_hidden('id', $merchant->id); ?>
                            <div class="row home-summary">
                                <div class="col-md-5ths col-xs-6 text-center border-right">
                                    <a href="http://map.semitepayment.io/admin/invoices/list_invoices?status=4">
                                        <h2 class="bold no-margin"><?php echo format_money(0)?></h2>
                                        <span class="bold text-muted mtop15 inline-block">Last Transfer</span>
                                    </a>
                                </div>
                                <div class="col-md-5ths col-xs-6 text-center border-right">
                                    <a href="http://map.semitepayment.io/admin/invoices/list_invoices?custom_view=not_sent">
                                        <h2 class="bold no-margin"><?php echo format_money(0)?></h2>
                                        <span class="bold text-muted inline-block mtop15">Next Transfer</span>
                                    </a>
                                </div>
                                <div class="col-md-5ths col-xs-6 text-center border-right">
                                    <a href="http://map.semitepayment.io/admin/invoices/list_invoices?custom_view=not_sent">
                                        <h2 class="bold no-margin"><?php echo $total_transactions?></h2>
                                        <span class="bold text-muted inline-block mtop15">Total Transactions</span>
                                    </a>
                                </div>
                                <div class="col-md-5ths col-xs-6 text-center border-right">
                                    <a href="http://map.semitepayment.io/admin/invoices/list_invoices?custom_view=not_sent">
                                        <h2 class="bold no-margin"><?php echo $percent_acceptance?>%</h2>
                                        <span class="bold text-muted inline-block mtop15">Acceptance Rate</span>
                                    </a>
                                </div>
                                <div class="col-md-5ths col-xs-6 text-center border-right">
                                    <a href="http://map.semitepayment.io/admin/invoices/list_invoices?custom_view=not_sent">
                                        <h2 class="bold no-margin"><?php echo format_money(get_gross_volume($merchant->id))?></h2>
                                        <span class="bold text-muted inline-block mtop15">Gross Volume <b>(<?php echo get_client_default_currency($merchant->id)?>)</b></span>
                                    </a>
                                </div>
                            </div>
                            <br/>
                            <div class="clearfix"></div>
                        <?php } ?>
                        <div>
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="merchant">
                                    <div class="row">
                                        <?php echo form_open($this->uri->uri_string(), array('class' => 'merchant-form')); ?>
                                        <div class="col-md-12">
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li role="presentation" class="active">
                                                    <a href="#merchant_details" aria-controls="merchant_details" role="tab"
                                                       data-toggle="tab">
                                                        Merchant Details
                                                    </a>
                                                </li>
                                                <?php if (isset($merchant)) { ?>
                                                    <li role="presentation" class="">
                                                        <a href="#3dsecure_settings" aria-controls="3dsecure_settings" role="tab"
                                                           data-toggle="tab">
                                                            3D-Secure Settings
                                                        </a>
                                                    </li>
                                                    <li role="presentation" class="">
                                                        <a href="#merchant_transactions" aria-controls="merchant_transactions" role="tab"
                                                           data-toggle="tab">
                                                            Transactions
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                            <div class="tab-content">
                                                <div role="tabpanel" class="tab-pane ptop10 active" id="merchant_details">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <?php $selected = (isset($merchant) ? $merchant->userid : ''); ?>
                                                            <?php echo render_select('userid',$clients,array('userid',array('firstname','lastname'),'company'),'Select Customer',$selected); ?>

                                                            <?php $selected = (isset($merchant) ? $merchant->affiliateid : ''); ?>
                                                            <?php echo render_select('affiliateid',$affiliates,array('affiliateid',array('firstname','lastname')),'Select Affiliate',$selected); ?>

                                                            <?php $value=( isset($merchant) ? $merchant->descriptor : ''); ?>
                                                            <?php echo render_input( 'descriptor', 'Merchant Descriptor',$value); ?>

                                                            <div class="col-md-6 row">
                                                                <div class="row">
                                                                    <div class="col-md-12 mtop10">

                                                                        <?php if (isset($merchant) && $merchant->live_mode) { ?>
                                                                            <div class="radio radio-primary">
                                                                                <input type="radio" name="live_mode" value="1" checked>
                                                                                <label>
                                                                                    Live Mode
                                                                                </label>
                                                                            </div>
                                                                            <div class="radio radio-primary">
                                                                                <input type="radio" name="live_mode" value="0">
                                                                                <label>
                                                                                    Sandbox Mode
                                                                                </label>
                                                                            </div>
                                                                        <?php } else { ?>
                                                                            <div class="radio radio-primary">
                                                                                <input type="radio" name="live_mode" value="1">
                                                                                <label>
                                                                                    Live Mode
                                                                                </label>
                                                                            </div>
                                                                            <div class="radio radio-primary">
                                                                                <input type="radio" name="live_mode" value="0" checked>
                                                                                <label>
                                                                                    Sandbox Mode
                                                                                </label>
                                                                            </div>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">

                                                            <?php $value=( isset($merchant) ? $merchant->api_id : ''); ?>
                                                            <?php echo render_input( 'api_id', 'API ID',$value); ?>

                                                            <?php $value=( isset($merchant) ? $merchant->secret_key : ''); ?>
                                                            <?php echo render_input( 'secret_key', 'Secret Key',$value); ?>

                                                            <button type="button" data-link="<?php echo admin_url('merchants/api_access'); ?>" class="btn btn-defaul pull-right" onclick="generateAccess(this);return false;">Generate API Access&nbsp;&nbsp;<i class="fa fa-refresh"></i></button>

                                                        </div>

                                                        <div class="col-md-5">

                                                            <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#merchant_processor_modal">New Processor</a>
                                                            <div class="clearfix"></div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <?php render_datatable(array(
                                                                        'Processor Name',
                                                                        'Status',
                                                                        _l('options'),
                                                                    ),'merchant-processors'); ?>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane ptop10" id="merchant_transactions">
                                                    <div class="row">
                                                        <div class="panel_s">
                                                            <div class="panel-body">
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label class="control-label" for="input-min">Start Date</label>
                                                                        <input type="text" id="min" placeholder="Start date" class="form-control"/>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label" for="input-max">End Date</label>
                                                                        <input type="text" id="max" placeholder="End date" class="form-control"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label class="control-label" for="input-min">Method</label>
                                                                        <select id="method" class="form-control">
                                                                            <option value=""></option>
                                                                            <option value="Charge">Charge</option>
                                                                            <option value="Refund">Refund</option>
                                                                            <option value="Authorize">Authorize</option>
                                                                            <option value="Capture">Capture</option>
                                                                            <option value="Void">Void</option>
                                                                            <option value="Payment">Payment</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label" for="input-status">Transaction status</label>
                                                                        <select id="status" class="form-control">
                                                                            <option value=""></option>
                                                                            <option value="Approved">Approved</option>
                                                                            <option value="Declined">Declined</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="panel_s">
                                                                <div class="panel-body">
                                                                    <div class="clearfix"></div>
                                                                    <table id="" class="table table-striped-col table-merchant-transactions">
                                                                        <thead>
                                                                        <tr>
                                                                            <th width="5%"></th>
                                                                            <th>Transaction ID</th>
                                                                            <th>Type</th>
                                                                            <th>Card Used</th>
                                                                            <th>Card Type</th>
                                                                            <th>Amount</th>
                                                                            <th>Status</th>
                                                                            <th>Date</th>
                                                                        </tr>
                                                                        </thead>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane ptop10" id="3dsecure_settings">
                                                    <div class="row">
                                                        <div class="col-md-4">



                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-primary mtop20">
                                                    <?php echo _l('submit'); ?>
                                                </button>
                                            </div>
                                        </div>
                                        <?php echo form_close(); ?>

                                        <div class="modal animated fadeIn" id="merchant_processor_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document" style="width: 80%;height: 100%;padding: 0px;margin: 0 auto;">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        <br/>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <?php echo render_select( 'processor',$processors,array( 'id',array( 'name')), 'Processor'); ?>
                                                                <h4>Processor Setup</h4>
                                                                <hr/>
                                                                <div id="processor-setup"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                                                        <button type="button" id="add-merchant-processor" group="button" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    var merchant_id = $('input[name="id"]').val();

    $(document).ready(function() {

        $( "#min" ).datepicker();
        $( "#max" ).datepicker();

        initDataTable('.table-merchant-processors', admin_url + 'merchants/processors/' + merchant_id, 'processors',[1],[1]);

        $.fn.dataTableExt.afnFiltering.push(
            function( oSettings, aData, iDataIndex ) {
                var iFini = document.getElementById('min').value;
                var iFfin = document.getElementById('max').value;
                var iStartDateCol = 7;
                var iEndDateCol = 7;

                iFini=iFini.substring(7,10) + iFini.substring(3,5)+ iFini.substring(0,2);
                iFfin=iFfin.substring(7,10) + iFfin.substring(3,5)+ iFfin.substring(0,2);

                var datofini=aData[iStartDateCol].substring(7,10) + aData[iStartDateCol].substring(3,5)+ aData[iStartDateCol].substring(0,2);
                var datoffin=aData[iEndDateCol].substring(7,10) + aData[iEndDateCol].substring(3,5)+ aData[iEndDateCol].substring(0,2);

                if ( iFini === "" && iFfin === "" )
                {
                    return true;
                }
                else if ( iFini <= datofini && iFfin === "")
                {
                    return true;
                }
                else if ( iFfin >= datoffin && iFini === "")
                {
                    return true;
                }
                else if (iFini <= datofini && iFfin >= datoffin)
                {
                    return true;
                }
                return false;
            }
        );

        $.fn.dataTable.ext.buttons.reload = {
            text: 'Reload',
            action: function ( e, dt, node, config ) {
                dt.ajax.reload();
            }
        };

        $.fn.dataTable.ext.buttons.reset = {
            text: 'Reset',
            action: function ( e, dt, node, config ) {
                $( "#min" ).val('');
                $( "#max" ).val('');

                $("#method option:first").prop('selected','selected');
                $("#status option:first").prop('selected','selected');


                $('#method').change( function() { exRowTable.columns(2).search( this.value).draw() });
                $('#status').change( function() { exRowTable.columns(6).search( this.value).draw() });

                $("#min").change ( function() { exRowTable.draw(); } );
                $("#max").change ( function() { exRowTable.draw(); } );

                $( "#min" ).trigger('change');
                $( "#max" ).trigger('change');

                $( "#method" ).trigger('change');
                $( "#status" ).trigger('change');
                exRowTable.search( '', true ).draw();
            }
        };

        var exRowTable = $('.table-merchant-transactions').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            className: 'btn btn-default',
            buttons: [
                'csv', 'excel', 'pdf', 'print','reload','reset'
            ],
            ajax: admin_url + 'merchants/transactions/'+merchant_id,
            language: {
                processing: '<div class="dt-loader"></div>'
            },
            columns: [{
                'class': 'details-control',
                'orderable': false,
                'data': null,
                'defaultContent': ''
            },
                { 'data': 'id' },
                { 'data': 'method' },
                { 'data': 'card' },
                { 'data': 'type' },
                { 'data': 'settlement' },
                { 'data': 'status' },
                { 'data': 'date_added' },

            ],
            processing: true,
            serverSide: false,
            order: [[7, 'desc']]
        });

        $('#min, #max').change( function() {
            exRowTable.draw();
        } );

        $('#method').change( function() { exRowTable.columns(2).search( this.value).draw() });
        $('#status').change( function() { exRowTable.columns(6).search( this.value).draw() });

        // Add event listener for opening and closing details
        $('.table-merchant-transactions tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = exRowTable.row( tr );

            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                row.child( format(row.data()) ).show();
                getDetails(row.data());
                tr.addClass('shown');
            }
        });


        function format (d) {

            return '<div id="transaction-'+ d.id+'"><h4 class="loading text-center text-muted"><i class="fa fa-spinner fa-spin"></i>  Please wait</h4></div>';

        }

    });

    function getDetails(data) {
        setTimeout(function () {
            $.ajax({
                type: 'POST',
                url: admin_url + 'merchants/transaction/'+data.id,
                data : data,
                dataType: 'html',
                success: function (html) {

                    $('.loading').remove();

                    $('#transaction-'+data.id).html(html);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    if (xhr.status != 0) {
                        alert(xhr.status + "\r\n" +thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                }
            });
        },200);
    }


    $('#merchant_processor_modal').on('hide.bs.modal', function(e) {
        $('select[name=\'processor\']').val('');
        $('select[name="processor"]').trigger('change');
        $('input[name=\'alias\']').val('');
        $('input[name=\'merchantid\']').val('');
        $('input[name=\'id\']').val('');
        $('input[name=\'active\']').val('');
        $('input[name=\'processorid\']').val('');
        $('.table-merchant-processors').DataTable().ajax.reload();
    });

    $('#merchant_processor_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var merchant_processor_id = $(invoker).data('id');
        var processor_id = $(invoker).data('processor-id');

        $('#merchant_processor_modal .add-title').removeClass('hide');
        $('#merchant_processor_modal .edit-title').addClass('hide');
        $('#merchant_processor_modal input').val('');



        // is from the edit button
        if (typeof(merchant_processor_id) !== 'undefined') {
            $('#merchant_processor_modal select[name="processor"]').val(processor_id);
            $('select[name="processor"]').trigger('change');
            $('#merchant_processor_modal .add-title').addClass('hide');
            $('#merchant_processor_modal .edit-title').removeClass('hide');

            $.ajax({
                url: admin_url + 'merchants/load_merchant_processor/'+merchant_processor_id,
                type: 'post',
                dataType: 'json',
                beforeSend: function() {

                },
                complete: function() {

                },
                success: function(json) {
                    $('#merchant_processor_modal input[name="id"]').val(json.id);
                    $('#merchant_processor_modal input[name="active"]').val(json.active);
                    $('#merchant_processor_modal input[name="alias"]').val(json.alias);
                    $('#merchant_processor_modal input[name="mccCode"]').val(json.mccCode);
                    $('#merchant_processor_modal input[name="buyRate"]').val(json.buyRate);
                    $('#merchant_processor_modal input[name="saleRate"]').val(json.saleRate);
                    $('#merchant_processor_modal input[name="rollbackReserve"]').val(json.rollbackReserve);
                    $('#merchant_processor_modal input[name="processingLimit"]').val(json.processingLimit);
                    $('#merchant_processor_modal input[name="transactionLimit"]').val(json.transactionLimit);
                    $('#merchant_processor_modal input[name="transactionFee"]').val(json.transactionFee);
                    $('#merchant_processor_modal input[name="status"][value='+json.status+']').attr("checked",true);

                    if (json.processor_data.visa){
                        $('#merchant_processor_modal input[name="processor_data[visa]"][value='+json.processor_data.visa+']').attr("checked",true);
                    }
                    if (json.processor_data.mastercard){
                        $('#merchant_processor_modal input[name="processor_data[mastercard]"][value='+json.processor_data.mastercard+']').attr("checked",true);
                    }
                    if (json.processor_data.amex){
                        $('#merchant_processor_modal input[name="processor_data[amex]"][value='+json.processor_data.amex+']').attr("checked",true);
                    }
                    if (json.processor_data.discover){
                        $('#merchant_processor_modal input[name="processor_data[discover]"][value='+json.processor_data.discover+']').attr("checked",true);
                    }

                    if (json.processor_data.use_original){
                        $('#merchant_processor_modal input[name="processor_data[use_original]"][value='+json.processor_data.use_original+']').attr("checked",true);
                    }

                    if (json.processor_data.memberId){
                        $('#merchant_processor_modal input[name="processor_data[memberId]"]').val(json.processor_data.memberId);
                        $('#merchant_processor_modal input[name="processor_data[memberGuid]"]').val(json.processor_data.memberGuid);
                    }

                    if (json.processor_data.gateway_username){
                        $('#merchant_processor_modal input[name="processor_data[gateway_username]"]').val(json.processor_data.gateway_username);
                        $('#merchant_processor_modal input[name="processor_data[gateway_password]"]').val(json.processor_data.gateway_password);
                        $('#merchant_processor_modal input[name="processor_data[gateway_service_username]"]').val(json.processor_data.gateway_service_username);
                        $('#merchant_processor_modal input[name="processor_data[gateway_service_password]"]').val(json.processor_data.gateway_service_password);
                        $('#merchant_processor_modal input[name="processor_data[gateway_account_id]"]').val(json.processor_data.gateway_account_id);
                    }

                    if (json.processor_data.sender){
                        $('#merchant_processor_modal input[name="processor_data[sender]"]').val(json.processor_data.sender);
                        $('#merchant_processor_modal input[name="processor_data[channel]"]').val(json.processor_data.channel);
                        $('#merchant_processor_modal input[name="processor_data[login]"]').val(json.processor_data.login);
                        $('#merchant_processor_modal input[name="processor_data[password]"]').val(json.processor_data.password);
                    }

                    if (json.processor_data.merchantId){
                        $('#merchant_processor_modal input[name="processor_data[merchantId]"]').val(json.processor_data.merchantId);
                        $('#merchant_processor_modal input[name="processor_data[serviceKey]"]').val(json.processor_data.serviceKey);
                        $('#merchant_processor_modal input[name="processor_data[clientKey]"]').val(json.processor_data.clientKey);
                    }
                }
            });

        }
    });

    $('#processor').on('change',function(){

        var processor_id = this.value;

        if (processor_id){

            $.ajax({
                url: admin_url + 'merchants/load_processor_form/'+processor_id,
                type: 'post',
                dataType: 'html',
                beforeSend: function() {

                },
                complete: function() {

                },
                success: function(html) {

                    $('#processor-setup').html(html);
                    $('#merchant_processor_modal input[name="processor"]').val(processor_id);
                    $('#merchant_processor_modal input[name="merchantid"]').val(merchant_id);
                }
            });
        } else {
            $('#processor-setup').html('');
        }
    });

    $('#add-merchant-processor').on('click',function(){

        var data = $('#merchant-processor-form').serialize();

        $.ajax({
            url: admin_url + 'merchants/processor/'+merchant_id,
            type: 'post',
            data : data,
            dataType: 'json',
            beforeSend: function() {

            },
            complete: function() {

            },
            success: function(json) {

                if (json.success === true){
                    $('#merchant_processor_modal').modal('hide');
                    return false;
                }
            }
        });

        return false;

    });

    function deleteMerchantProcessor(id){

        swal({
                title: "Are you sure?",
                text: "Merchant Processor will be deleted!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Delete!",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function(isConfirm){

                if (isConfirm) {
                    setTimeout(function () {
                        $.ajax({
                            url: admin_url + 'merchants/delete_merchant_processor/'+id,
                            type: 'post',
                            dataType: 'json',
                            beforeSend: function() {

                            },
                            complete: function() {

                            },
                            success: function(json) {
                                swal({
                                        title: "Success",
                                        text: "Succeeded!",
                                        type: "success"
                                    },
                                    function(isConfirm){
                                        $('.table-merchant-processors').DataTable().ajax.reload();
                                    });

                            }
                        });
                    },2000);

                } else {
                    setTimeout(function () {
                        swal("Cancelled", "Operation cancelled!", "error");
                    },200);
                }

            });
    }
</script>
</body>
</html>

