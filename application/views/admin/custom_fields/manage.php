<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                    <a href="<?php echo admin_url('custom_fields/field'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_custom_field'); ?></a>
                   </div>
               </div>
               <div class="panel_s">
                   <div class="panel-body">
                    <div class="clearfix"></div>
                    <?php render_datatable(
                        array(
                            _l('id'),
                            _l('custom_field_dt_field_name'),
                            _l('custom_field_dt_field_to'),
                            _l('custom_field_dt_field_type'),
                            _l('custom_field_add_edit_active'),
                            _l('options')
                            ),'custom-fields'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php init_tail(); ?>
</body>
</html>
