<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-5" id="small-table">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if(has_permission('manageTasks')){ ?>
                        <a href="#" onclick="new_task(); return false;" class="btn btn-info pull-left new"><?php echo _l('new_task'); ?></a>
                        <?php } ?>
                        <a href="#" class="btn btn-default pull-right mleft10" onclick="toggle_small_view('.table-tasks','#task'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-bars"></i></a>
                        <div class="btn-group pull-right" data-toggle="tooltip" title="Sort By">
                            <button type="button" class="btn btn-default mleft10 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-sort"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a href="#" onclick="tasks_sort_by('finished'); return false;">
                                    <?php echo _l('task_sort_finished'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" onclick="tasks_sort_by('priority'); return false;"><?php echo _l('task_sort_priority'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" onclick="tasks_sort_by('startdate'); return false;">
                                    <?php echo _l('task_sort_startdate'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" onclick="tasks_sort_by('dateadded'); return false;">
                                    <?php echo _l('task_sort_dateadded'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" onclick="tasks_sort_by('duedate'); return false;">
                                    <?php echo _l('task_sort_duedate'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-group pull-right" data-toggle="tooltip" title="View Tasks">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-list"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" onclick="dt_custom_view('','.table-tasks'); return false;">
                                    <?php echo _l('task_list_all'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" onclick="dt_custom_view('finished','.table-tasks'); return false;">
                                    <?php echo _l('task_list_finished'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" onclick="dt_custom_view('unfinished','.table-tasks'); return false;">
                                    <?php echo _l('task_list_unfinished'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" onclick="dt_custom_view('not_assigned','.table-tasks'); return false;">
                                    <?php echo _l('task_list_not_assigned'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" onclick="dt_custom_view('due_date_passed','.table-tasks'); return false;">
                                    <?php echo _l('task_list_duedate_passed'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="panel_s animated fadeIn">
                <div class="panel-body">
                    <?php echo form_hidden('tasks_sort_by'); ?>
                    <?php echo form_hidden('custom_view',$custom_view); ?>

                    <div class="clearfix"></div>
                    <?php
                    $table_data = array(_l('tasks_dt_name'),_l('tasks_dt_datestart'),_l('task_relation'),_l('tasks_dt_finished'));
                    $custom_fields = get_custom_fields('tasks',array('show_on_table'=>1));
                    foreach($custom_fields as $field){
                        array_push($table_data,$field['name']);
                    }

                    render_datatable($table_data,'tasks'); ?>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div id="task" class="animated">

            </div>
        </div>
    </div>
</div>
</div>
<script>var hidden_columns = [3];</script>
<script>
    taskid = '<?php echo $taskid; ?>';
</script>
<?php init_tail(); ?>
<script>
    $(document).ready(function(){
        $.each(hidden_columns,function(i,val){
            var column_tasks = $('.table-tasks').DataTable().column( val );
            column_tasks.visible( false );
        });
    });
</script>
</body>
</html>
