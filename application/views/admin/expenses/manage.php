<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-5" id="small-table">
                <div class="panel_s">
                    <div class="panel-body">
                       <?php if(has_permission('manageExpenses')){ ?>
                       <a href="<?php echo admin_url('expenses/expense'); ?>" class="btn btn-info"><?php echo _l('new_expense'); ?></a>
                       <?php } ?>
                       <div class="btn-group pull-right mleft4" data-toggle="tooltip" title="<?php echo _l('expenses_list_tooltip'); ?>">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-list"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a href="#" onclick="dt_custom_view('','.table-expenses'); return false;">
                                    <?php echo _l('expenses_list_all'); ?>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#" onclick="dt_custom_view('billable','.table-expenses'); return false;">
                                    <?php echo _l('expenses_list_billable'); ?> (<?php echo total_rows('tblexpenses',array('billable'=>1)); ?>)
                                </a>
                            </li>
                            <li>
                                <a href="#" onclick="dt_custom_view('non-billable','.table-expenses'); return false;">
                                    <?php echo _l('expenses_list_non_billable'); ?> (<?php echo total_rows('tblexpenses',array('billable'=>0)); ?>)
                                </a>
                            </li>
                            <li>
                                <a href="#" onclick="dt_custom_view('invoiced','.table-expenses'); return false;">
                                    <?php echo _l('expenses_list_invoiced'); ?> (<?php echo total_rows('tblexpenses',array('invoiceid !='=>NULL)); ?>)
                                </a>
                            </li>
                            <li>
                                <a href="#" onclick="dt_custom_view('unbilled','.table-expenses'); return false;">
                                    <?php echo _l('expenses_list_unbilled'); ?>
                                    (<?php echo $total_unbilled; ?>)
                                </a>
                            </li>
                            <li>
                                <a href="#" onclick="dt_custom_view('recurring','.table-expenses'); return false;">
                                    <?php echo _l('expenses_list_recurring'); ?>
                                    (<?php echo total_rows('tblexpenses',array('recurring'=>1)); ?>)
                                </a>
                            </li>
                            <li class="divider"></li>
                            <?php foreach($categories as $category){ ?>
                            <li>
                                <a href="#" onclick="dt_custom_view(<?php echo $category['id']; ?>,'.table-expenses'); return false;"><?php echo $category['name']; ?></a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <a href="#" class="btn btn-default pull-right" onclick="toggle_small_view('.table-expenses','#expense'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-bars"></i></a>

                </div>
            </div>
            <?php echo form_hidden('custom_view'); ?>
            <div class="panel_s animated fadeIn">
                <div class="panel-body">

                    <div class="clearfix"></div>
                    <!-- if expenseid found in url -->
                    <?php echo form_hidden('expenseid',$expenseid); ?>
                    <?php
                    $table_data = array(
                        _l('expense_dt_table_heading_category'),
                        _l('expense_dt_table_heading_amount'),
                        _l('expense_dt_table_heading_date'),
                        _l('expense_dt_table_heading_customer'),
                        _l('expense_dt_table_heading_reference_no'),
                        _l('expense_dt_table_heading_payment_mode'),
                        );
                    $custom_fields = get_custom_fields('expenses',array('show_on_table'=>1));
                    foreach($custom_fields as $field){
                        array_push($table_data,$field['name']);
                    }
                    render_datatable($table_data,'expenses'); ?>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div id="expense">

            </div>
        </div>
    </div>
</div>
</div>
<script>var hidden_columns = [3,4,5];</script>
<?php init_tail(); ?>
<script>
    $(document).ready(function(){
        $.each(hidden_columns,function(i,val){
            var column_estimates = $('.table-expenses').DataTable().column( val );
            column_estimates.visible( false );
        });
        Dropzone.autoDiscover = false;
        init_expense();
    });
    function init_expense(id) {
        var _expenseid = $('body').find('input[name="expenseid"]').val();
            // Check if expense id passed from url
            if (_expenseid !== '') {
                id = _expenseid;
            } else {
                if (typeof(id) == 'undefined' || id == '') {
                    return;
                }
            }

            $('body').find('input[name="expenseid"]').val('');
            if (!$('body').hasClass('small-table')) {
                toggle_small_view('.tbl-expenses','#expense');
            }
            $('#expense').load(admin_url + 'expenses/get_expense_data_ajax/' + id);

            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
              $('html, body').animate({
                scrollTop: $('#expense').offset().top + 150
            }, 600);
          }

      }
      function convert_expense(id){
        $.get(admin_url+ 'expenses/get_convert_data/'+id,function(response){
            $('#convert_helper').html(response);
            $('body').find('#convert_to_invoice').modal('show');
        });
    }
</script>
</body>
</html>


