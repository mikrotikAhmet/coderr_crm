<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <?php echo form_hidden('custom_view'); ?>
                <?php echo form_hidden('invoices_by_status'); ?>
                <?php echo form_hidden('estimates_by_status'); ?>
                <?php echo form_hidden('contracts_by_type'); ?>
                <div class="panel_s">
                    <div class="panel-body">
                      <a href="<?php echo admin_url('clients/client'); ?>" class="btn btn-info mright5 pull-left display-block">
                        <?php echo _l('new_client'); ?></a>
                        <a href="<?php echo admin_url('clients/import'); ?>" class="btn btn-info pull-left display-block mright5">
                            <?php echo _l('import_customers'); ?></a>
                            <div class="visible-xs">
                                <div class="clearfix"></div>
                            </div>
                            <div class="btn-group pull-left" data-toggle="tooltip" title="<?php echo _l('customer_view_by'); ?>">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-list"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left" style="width:300px;">
                                    <li><a href="#" onclick="dt_custom_view('','.table-clients'); return false;">All</a></li>
                                    <?php foreach($groups as $group){ ?>
                                    <li><a href="#" onclick="dt_custom_view(<?php echo $group['id']; ?>,'.table-clients'); return false;"><?php echo $group['name']; ?></a></li>
                                    <?php } ?>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="#" onclick="sort_customer_invoices_by_status(1); return false;"><?php echo _l('customer_have_invoices_by',_l('invoice_status_unpaid')); ?></a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="sort_customer_invoices_by_status(2); return false;"><?php echo _l('customer_have_invoices_by',_l('invoice_status_paid')); ?></a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="sort_customer_invoices_by_status(3); return false;"><?php echo _l('customer_have_invoices_by',_l('invoice_status_not_paid_completely')); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="sort_customer_invoices_by_status(4); return false;"><?php echo _l('customer_have_invoices_by',_l('invoice_status_overdue')); ?>
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="#" onclick="sort_customer_estimates_by_status(1); return false;">
                                            <?php echo _l('customer_have_estimates_by',_l('estimate_status_draft')); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="sort_customer_estimates_by_status(2); return false;">
                                            <?php echo _l('customer_have_estimates_by',_l('estimate_status_sent')); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="sort_customer_estimates_by_status(3); return false;">
                                            <?php echo _l('customer_have_estimates_by',_l('estimate_status_declined')); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="sort_customer_estimates_by_status(4); return false;">
                                            <?php echo _l('customer_have_estimates_by',_l('estimate_status_accepted')); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="sort_customer_estimates_by_status(5); return false;">
                                            <?php echo _l('customer_have_estimates_by',_l('estimate_status_expired')); ?>
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <?php foreach($contract_types as $type){ ?>
                                <li>
                                    <a href="#" onclick="sort_customer_contracts_by_type('<?php echo $type['id']; ?>'); return false;">
                                        <?php echo _l('customer_have_contracts_by_type',$type['name']); ?>
                                    </a>
                                </li>
                                <?php } ?>

                                </ul>
                            </div>
                            <div class="clearfix"></div>
                            <hr />
                            <div class="row mbot15">
                                <div class="col-md-12">
                                    <h3 class="text-success no-margin"><?php echo _l('customers_summary'); ?></h3>
                                </div>
                                <div class="col-md-2 border-right">
                                    <h3 class="bold"><?php echo total_rows('tblclients',array('active'=>1)); ?></h3>
                                    <span class="text-info bold"><?php echo _l('contract_summary_active'); ?></span>
                                </div>
                                <div class="col-md-2 border-right">
                                    <h3 class="bold"><?php echo total_rows('tblclients',array('active'=>0)); ?></h3>
                                    <span class="text-danger bold"><?php echo _l('customers_summary_inactive'); ?></span>
                                </div>
                                <div class="col-md-2 border-right">
                                    <h3 class="bold"><?php echo total_rows('tblclients',array('company !='=>'')); ?></h3>
                                    <span class="text-success bold"><?php echo _l('customers_summary_companies'); ?></span>
                                </div>
                                <div class="col-md-2 border-right">
                                    <h3 class="bold"><?php echo total_rows('tblclients','company = "" OR company IS NULL'); ?></h3>
                                    <span class="text-info bold"><?php echo _l('customers_summary_individual'); ?></span>
                                </div>
                                <div class="col-md-2">
                                    <h3 class="bold"><?php echo total_rows('tblclients','last_login = DATE('.date('Y-m-d').')'); ?></h3>
                                    <span class="text-info bold"><?php echo _l('customers_summary_logged_in_today'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="clearfix"></div>
                            <?php
                            $table_data = array(
                                _l('clients_list_full_name'),
                                _l('clients_list_email'),
                                _l('clients_list_phone'),
                                _l('customer_groups'),
                                _l('clients_list_last_active'),
                                );
                            $custom_fields = get_custom_fields('customers',array('show_on_table'=>1));
                            foreach($custom_fields as $field){
                                array_push($table_data,$field['name']);
                            }
                            array_push($table_data, _l('options'));
                            render_datatable($table_data,'clients');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php init_tail(); ?>
    <script>
        function sort_customer_invoices_by_status(status){
            $('input[name="invoices_by_status"]').val(status);
            $('.table-clients').DataTable().ajax.reload();
            $('input[name="invoices_by_status"]').val('');
        }
        function sort_customer_estimates_by_status(status){
            $('input[name="estimates_by_status"]').val(status);
            $('.table-clients').DataTable().ajax.reload();
            $('input[name="estimates_by_status"]').val('');
        }
        function sort_customer_contracts_by_type(status){
            $('input[name="contracts_by_type"]').val(status);
            $('.table-clients').DataTable().ajax.reload();
            $('input[name="contracts_by_type"]').val('');
        }
    </script>
</body>
</html>
