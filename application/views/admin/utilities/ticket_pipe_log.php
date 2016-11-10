<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <?php echo render_date_input('activity-log-date','utility_activity_log_filter_by_date','',array('data-column'=>1),array(),'','activity-log-date'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <?php render_datatable(array(
                            _l('ticket_pipe_name'),
                            _l('ticket_pipe_date'),
                            _l('ticket_pipe_email_to'),
                            _l('ticket_pipe_email'),
                            _l('ticket_pipe_subject'),
                            _l('ticket_pipe_message'),
                            _l('ticket_pipe_status'),
                            ),'activity-log'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php init_tail(); ?>
</body>
</html>
