<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <?php echo form_hidden('custom_view'); ?>
                <div class="panel_s">
                    <div class="panel-body">
                      <a href="<?php echo admin_url('affiliates/affiliate'); ?>" class="btn btn-info mright5 pull-left display-block">
                        <?php echo _l('new_affiliate'); ?></a>
                            <div class="visible-xs">
                                <div class="clearfix"></div>
                            </div>
                            <div class="btn-group pull-left" data-toggle="tooltip" title="<?php echo _l('affiliate_view_by'); ?>">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-list"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left" style="width:300px;">
                                    <li><a href="#" onclick="dt_custom_view('','.table-affiliates'); return false;">All</a></li>
                                    <?php foreach($groups as $group){ ?>
                                    <li><a href="#" onclick="dt_custom_view(<?php echo $group['id']; ?>,'.table-affiliates'); return false;"><?php echo $group['name']; ?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div class="clearfix"></div>
                            <hr />
                            <div class="row mbot15">
                                <div class="col-md-12">
                                    <h3 class="text-success no-margin"><?php echo _l('affiliates_summary'); ?></h3>
                                </div>
                                <div class="col-md-2 border-right">
                                    <h3 class="bold"><?php echo total_rows('tblaffiliates',array('active'=>1)); ?></h3>
                                    <span class="text-info bold"><?php echo _l('contract_summary_active'); ?></span>
                                </div>
                                <div class="col-md-2 border-right">
                                    <h3 class="bold"><?php echo total_rows('tblaffiliates',array('active'=>0)); ?></h3>
                                    <span class="text-danger bold"><?php echo _l('customers_summary_inactive'); ?></span>
                                </div>
                                <div class="col-md-2 border-right">
                                    <h3 class="bold"><?php echo total_rows('tblaffiliates',array('company !='=>'')); ?></h3>
                                    <span class="text-success bold"><?php echo _l('customers_summary_companies'); ?></span>
                                </div>
                                <div class="col-md-2 border-right">
                                    <h3 class="bold"><?php echo total_rows('tblaffiliates','company = "" OR company IS NULL'); ?></h3>
                                    <span class="text-info bold"><?php echo _l('customers_summary_individual'); ?></span>
                                </div>
                                <div class="col-md-2">
                                    <h3 class="bold"><?php echo total_rows('tblaffiliates','last_login = DATE('.date('Y-m-d').')'); ?></h3>
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
                                _l('affiliates_list_full_name'),
                                _l('affiliates_list_email'),
                                _l('affiliates_list_phone'),
                                _l('affiliate_groups'),
                                _l('affiliates_list_last_active'),
                                );
                            $custom_fields = get_custom_fields('customers',array('show_on_table'=>1));
                            foreach($custom_fields as $field){
                                array_push($table_data,$field['name']);
                            }
                            array_push($table_data, _l('options'));
                            render_datatable($table_data,'affiliates');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php init_tail(); ?>
    <script>
    </script>
</body>
</html>
