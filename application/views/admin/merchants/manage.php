<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <?php echo form_hidden('custom_view'); ?>
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="<?php echo admin_url('merchants/merchant'); ?>" class="btn btn-info mright5 pull-left display-block">
                            New Merchant</a>
                        <div class="visible-xs">
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                        <hr />
                        <div class="row mbot15">
                            <div class="col-md-12">
                                <h3 class="text-success no-margin">Merchant Summary</h3>
                            </div>
                            <div class="col-md-2 border-right">
                                <h3 class="bold"><?php echo total_rows('tblmerchants',array('live_mode'=>1)); ?></h3>
                                <span class="text-info bold">Live</span>
                            </div>
                            <div class="col-md-2 border-right">
                                <h3 class="bold"><?php echo total_rows('tblmerchants',array('live_mode'=>0)); ?></h3>
                                <span class="text-danger bold">Sandbox</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php render_datatable(array(
                            'Merchant Name',
                            'Merchant Descriptor',
                            'Mode',
                            'Date Added',
                            _l('options'),
                        ),'merchants'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>

    initDataTable('.table-merchants', window.location.href, 'merchants', [1], [1]);

</script>
</body>
</html>