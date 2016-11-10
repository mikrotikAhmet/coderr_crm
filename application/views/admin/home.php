<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="dashboard">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#application" aria-controls="application" role="tab" data-toggle="tab">
                                    Application Overview
                                </a>
                            </li>
                            <?php if(has_permission('manageGateway')){ ?>
                            <li role="presentation" class="">
                                <a href="#gateway" aria-controls="gateway" role="tab" data-toggle="tab">
                                    Gateway Overview
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                        <div class="tab-content">
                            <!-- Tab 1 -->
                            <div role="tabpanel" class="tab-pane ptop10 active" id="application">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel_s">
                                            <div class="panel-body">
                                                <div class="row home-summary">
                                                    <div class="col-md-5ths col-xs-6 text-center border-right">
                                                        <a href="<?php echo admin_url('invoices/list_invoices?status=4'); ?>">
                                                            <h2 class="bold no-margin"><?php echo total_rows('tblinvoices',array('status'=>4)); ?>  </h2>
                                                            <span class="bold text-danger mtop15 inline-block"><i class="fa fa-clock-o"></i>  <?php echo _l('home_invoice_overdue'); ?></span>
                                                        </a>
                                                    </div>
                                                    <div class="col-md-5ths col-xs-6 text-center border-right">
                                                        <a href="<?php echo admin_url('invoices/list_invoices?custom_view=not_sent'); ?>">
                                                            <h2 class="bold no-margin"><?php echo total_rows('tblinvoices',array('sent'=>0,'status !='=>2)); ?></h2>
                                                            <span class="bold text-muted inline-block mtop15"><i class="fa fa-envelope"></i> <?php echo _l('home_invoice_not_sent'); ?></span>
                                                        </a>
                                                    </div>
                                                    <div class="col-md-5ths col-xs-6 text-center border-right">
                                                        <a href="<?php echo admin_url('invoices/list_invoices?status=3'); ?>">
                                                            <h2 class="bold no-margin"><?php echo total_rows('tblinvoices',array('status'=>3)); ?></h2>
                                                            <span class="bold text-info mtop15 inline-block"><i class="fa fa-balance-scale"></i> <?php echo _l('home_invoice_partialy_paid'); ?></span>
                                                        </a>
                                                    </div>
                                                    <div class="col-md-5ths col-xs-6 text-center border-right">
                                                        <a href="<?php echo admin_url('invoices/list_invoices?status=2'); ?>">
                                                            <h2 class="bold no-margin"><?php echo total_rows('tblinvoices',array('status'=>2)); ?></h2>
                                                            <span class="bold text-success mtop15 inline-block"><i class="fa fa-check"></i> <?php echo _l('home_invoice_paid'); ?></span>
                                                        </a>
                                                    </div>
                                                    <div class="col-md-5ths col-xs-6 text-center">
                                                        <a href="<?php echo admin_url('payments?custom_view=today'); ?>">
                                                            <h2 class="bold no-margin"><?php echo total_rows('tblinvoicepaymentrecords',array('DATE(date)'=>date('Y-m-d'))); ?></h2>
                                                            <span class="bold text-success mtop15 inline-block"><i class="fa fa-calendar-check-o"></i> <?php echo _l('home_payments_received_today'); ?></span>
                                                        </a>
                                                    </div>
                                                    <div class="clearfix">  </div>
                                                    <hr class="home-summary-separator"/>
                                                    <div class="col-md-5ths col-xs-6 text-center border-right">
                                                        <a href="<?php echo admin_url('estimates/list_estimates?status=5'); ?>">
                                                            <h2 class="bold no-margin"><?php echo total_rows('tblestimates',array('status'=>5)); ?></h2>
                                                            <span class="bold text-warning mtop15 inline-block"><i class="fa fa-clock-o"></i> <?php echo _l('home_expired_estimates'); ?></span>
                                                        </a>
                                                    </div>
                                                    <div class="col-md-5ths col-xs-6 text-center border-right">
                                                        <a href="<?php echo admin_url('estimates/list_estimates?status=3'); ?>">
                                                            <h2 class="bold no-margin"><?php echo total_rows('tblestimates',array('status'=>3)); ?></h2>
                                                            <span class="bold mtop15 text-danger inline-block"><i class="fa fa-times"></i> <?php echo _l('home_estimate_declined'); ?></span>
                                                        </a>
                                                    </div>
                                                    <div class="col-md-5ths col-xs-6 text-center border-right">
                                                        <a href="<?php echo admin_url('estimates/list_estimates?status=2'); ?>">
                                                            <h2 class="bold no-margin"><?php echo total_rows('tblestimates',array('status'=>2)); ?></h2>
                                                            <span class="bold text-info mtop15 inline-block"><i class="fa fa-envelope-o"></i> <?php echo _l('home_estimate_sent'); ?></span>
                                                        </a>
                                                    </div>
                                                    <div class="col-md-5ths col-xs-6 text-center border-right">
                                                        <a href="<?php echo admin_url('estimates/list_estimates?status=4'); ?>">
                                                            <h2 class="bold no-margin"><?php echo total_rows('tblestimates',array('status'=>4)); ?></h2>
                                                            <span class="bold text-success mtop15 inline-block"><i class="fa fa-check"></i> <?php echo _l('home_estimate_accepted'); ?></span>
                                                        </a>
                                                    </div>
                                                    <div class="col-md-5ths col-xs-6 text-center">
                                                        <a href="<?php echo admin_url('tasks/list_tasks?custom_view=unfinished'); ?>">
                                                            <h2 class="bold no-margin"><?php echo total_rows('tblstafftasks',array('finished'=>0)); ?></h2>
                                                            <span class="bold text-warning mtop15 inline-block"><i class="fa fa-times"></i> <?php echo _l('home_unfinished_tasks'); ?></span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if(has_permission('manageSales')){ ?>
                                        <div class="col-md-7">
                                            <div class="panel_s">
                                                <div class="panel-body">
                                                    <div class="col-md-12">
                                                        <p class="text-muted pull-left"><?php echo _l('home_weekly_payment_records'); ?></p>
                                                        <?php if(has_permission('watchReports')){ ?>
                                                            <a href="<?php echo admin_url('reports/sales'); ?>" class="pull-right"><?php echo _l('home_stats_full_report'); ?></a>
                                                            <div class="clearfix"></div>
                                                        <?php } ?>
                                                        <div class="clearfix"></div>
                                                        <?php if (is_using_multiple_currencies()) { ?>
                                                            <select class="selectpicker pull-left mbot15" name="currency">
                                                                <?php foreach($currencies as $currency){
                                                                    $selected = '';
                                                                    if($currency['isdefault'] == 1){
                                                                        $selected = 'selected';
                                                                    }
                                                                    ?>
                                                                    <option value="<?php echo $currency['id']; ?>" <?php echo $selected; ?> data-subtext="<?php echo $currency['name']; ?>"><?php echo $currency['symbol']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        <?php } ?>
                                                        <canvas class="chart" id="weekly-payment-statistics" style="height:300px;"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <div class="col-md-5">
                                        <div class="panel_s">
                                            <div class="panel-body home-activity">
                                                <ul class="nav nav-tabs" role="tablist">
                                                    <?php if(is_admin()){ ?>
                                                        <li role="presentation" class="active">
                                                            <a href="#home_tab_activity" aria-controls="home_tab_activity" role="tab" data-toggle="tab">
                                                                <?php echo _l('home_latest_activity'); ?>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <li role="presentation" class="<?php if(!is_admin()){ echo 'active'; } ?>">
                                                        <a href="#home_tab_tasks" aria-controls="home_tab_tasks" role="tab" data-toggle="tab">
                                                            <?php echo _l('home_my_tasks'); ?>
                                                        </a>
                                                    </li>
                                                    <li role="presentation">
                                                        <a href="#home_tab_todo" aria-controls="home_tab_todo" role="tab" data-toggle="tab">
                                                            <?php echo _l('home_my_todo_items'); ?>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content home-activity-wrap">
                                                    <?php if(is_admin()){ ?>
                                                        <div role="tabpanel" class="tab-pane active" id="home_tab_activity">
                                                            <a href="<?php echo admin_url('utilities/activity_log'); ?>" class="btn btn-info btn-sm"><?php echo _l('home_widget_view_all'); ?></a>
                                                            <div class="clearfix"></div>
                                                            <hr />
                                                            <ul class="latest-activity">
                                                                <?php
                                                                $this->db->limit(50);
                                                                $activity_log = $this->db->get('tblactivitylog')->result_array();
                                                                foreach($activity_log as $log){   ?>
                                                                    <div class="media">
                                                                        <?php if($log['staffid'] != 0){ ?>
                                                                            <div class="media-left">
                                                                                <a href="<?php echo admin_url('profile/'.$log["staffid"]); ?>">
                                                                                    <?php echo staff_profile_image($log['staffid'],array('staff-profile-image-small','media-object'),'small',array('data-toggle'=>'tooltip','data-title'=>get_staff_full_name($log['staffid']),'data-placement'=>'right')); ?>&nbsp;
                                                                                </a>
                                                                            </div>
                                                                        <?php } ?>
                                                                        <div class="media-body text-purple">
                                                                            <?php echo $log['description']; ?><small class="text-muted display-block"><?php echo _dt($log['date']); ?></small>
                                                                            <hr />
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    <?php } ?>
                                                    <div role="tabpanel" class="tab-pane <?php if(!is_admin()){ echo 'active'; } ?>" id="home_tab_tasks">
                                                        <a href="<?php echo admin_url('tasks/list_tasks'); ?>" class="btn btn-info btn-sm"><?php echo _l('home_widget_view_all'); ?></a>
                                                        <div class="clearfix"></div>
                                                        <hr />
                                                        <?php
                                                        $this->db->where('(id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid = ' . get_staff_user_id() . ') OR id IN (SELECT taskid FROM tblstafftasksfollowers WHERE staffid = ' . get_staff_user_id() . ') OR addedfrom=' . get_staff_user_id() . ')');
                                                        $this->db->where('finished',0);
                                                        $this->db->limit(15);

                                                        $tasks = $this->db->get('tblstafftasks')->result_array();
                                                        foreach($tasks as $task){ ?>
                                                            <div class="widget-task">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <a href="#" class="pull-left" onclick="mark_complete(2,this); return false;" data-placement="right" data-toggle="tooltip" title="<?php echo _l('task_single_mark_as_complete'); ?>"><i class="fa fa-check task-icon task-unfinished-icon"></i></a>
                                                                        <a href="#" onclick="init_task_related_modal(<?php echo $task['id']; ?>); return false;"><?php echo $task['name']; ?></a>
                                                                        <div class="clearfix mtop10"></div>
                                                                        <?php echo substr(strip_tags($task['description']),0,150) . '...' ?>
                                                                    </div>
                                                                    <div class="col-md-12 mtop10">
            <span class="label <?php if(total_rows('tbltaskchecklists',array('finished'=>0,'taskid'=>$task['id'])) == 0){echo 'label-default-light';}else{echo 'label-danger';} ?> pull-left mright5">
               <i class="fa fa-th-list"></i> <?php echo total_rows('tbltaskchecklists',array('finished'=>0,'taskid'=>$task['id'])); ?>
           </span>
           <span class="label label-default-light pull-left mright5">
               <i class="fa fa-paperclip"></i> <?php echo total_rows('tblstafftasksattachments',array('taskid'=>$task['id'])); ?>
           </span>
           <span class="label label-default-light pull-left">
               <i class="fa fa-comments"></i> <?php echo total_rows('tblstafftaskcomments',array('taskid'=>$task['id'])); ?>
           </span>
                                                                    </div>
                                                                </div>
                                                                <hr />
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div role="tabpanel" class="tab-pane" id="home_tab_todo">
                                                        <a href="<?php echo admin_url('todo'); ?>" class="btn btn-info btn-sm mbot20"><?php echo _l('home_widget_view_all'); ?></a>
                                                        <div class="clearfix"></div>
                                                        <div class="todo-body">
                                                            <?php $total_todos = count($todos); ?>

                                                            <h4 class="todo-title text-warning"><i class="fa fa-warning"></i> <?php echo _l('home_latest_todos'); ?></h4>
                                                            <ul class="list-unstyled todo unfinished-todos todos-sortable sortable">
                                                                <?php foreach($todos as $todo) { ?>
                                                                    <li>
                                                                        <?php echo form_hidden('todo_order',$todo['item_order']); ?>
                                                                        <?php echo form_hidden('finished',0); ?>
                                                                        <div class="checkbox checkbox-default todo-checkbox">
                                                                            <input type="checkbox" name="todo_id" value="<?php echo $todo['todoid']; ?>">
                                                                            <label></label>
                                                                        </div>
                                                                        <span class="todo-description"><?php echo $todo['description']; ?><a href="#" onclick="delete_todo_item(this,<?php echo $todo['todoid']; ?>)" class="pull-right todo-delete"><i class="fa fa-remove"></i></a></span>
                                                                        <small class="todo-date"><?php echo $todo['dateadded']; ?></small>
                                                                    </li>
                                                                <?php } ?>
                                                                <li class="padding no-todos ui-state-disabled <?php if($total_todos > 0){echo 'hide';} ?>"><?php echo _l('home_no_latest_todos'); ?></li>
                                                            </ul>
                                                            <?php $total_finished_todos = count($todos_finished); ?>
                                                            <h4 class="todo-title text-success"><i class="fa fa-check"></i> <?php echo _l('home_latest_finished_todos'); ?></h4>
                                                            <ul class="list-unstyled todo finished-todos todos-sortable sortable" >
                                                                <?php foreach($todos_finished as $todo_finished){ ?>
                                                                    <li>
                                                                        <?php echo form_hidden('todo_order',$todo_finished['item_order']); ?>
                                                                        <?php echo form_hidden('finished',1); ?>
                                                                        <div class="checkbox checkbox-default todo-checkbox">
                                                                            <input type="checkbox" value="<?php echo $todo_finished['todoid']; ?>" name="todo_id" checked>
                                                                            <label></label>
                                                                        </div>
                                                                        <span class="todo-description todo-finished"><?php echo $todo_finished['description']; ?><a href="#" onclick="delete_todo_item(this,<?php echo $todo_finished['todoid']; ?>)" class="pull-right todo-delete"><i class="fa fa-remove"></i></a></span>
                                                                        <small class="todo-date todo-date-finished"><?php echo $todo_finished['datefinished']; ?></small>
                                                                    </li>
                                                                <?php } ?>
                                                                <li class="padding no-todos ui-state-disabled <?php if($total_finished_todos > 0){echo 'hide';} ?>"><?php echo _l('home_no_finished_todos_found'); ?></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-7">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="panel_s">
                                                    <div class="panel-body">
                                                        <div class="dt-loader hide"></div>
                                                        <div id="calendar"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="panel_s">
                                                            <div class="panel-heading">
                                                                <?php echo _l('home_tickets_awaiting_reply_by_department'); ?>
                                                            </div>
                                                            <div class="panel-body">
                                                                <canvas class="chart" id="tickets-awaiting-reply-by-department"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="panel_s">
                                                            <div class="panel-heading">
                                                                <?php echo _l('home_tickets_awaiting_reply_by_status'); ?>
                                                            </div>
                                                            <div class="panel-body">
                                                                <canvas class="chart" id="tickets-awaiting-reply-by-status"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="panel_s events animated fadeIn ">
                                                    <div class="panel-heading-bg">
                                                        <?php echo _l('home_this_week_events'); ?>
                                                    </div>
                                                    <div class="panel-body">
                                                        <?php foreach($upcoming_events as $event){ ?>
                                                            <p class="event">
                                                                <span class="label label-primary pull-left"><i class="fa fa-calendar"></i> <?php echo _d($event['start']); ?></span>
                                                                <?php if($event['public'] == 1) { ?>
                                                                    <span class="label label-warning pull-right"><?php echo _l('home_public_event'); ?></span>
                                                                <?php } ?>
                                                                <span class="event-title bold"><?php echo $event['title']; ?></span>
                                                                <?php if($event['userid'] != get_staff_user_id()){ ?>
                                                                    <small class="display-block valign"><?php echo _l('home_event_added_by'); ?> <a href="<?php echo admin_url('profile/'.$event['userid']); ?>"><?php echo staff_profile_image($event['userid'],array('staff-profile-xs-image')); ?> <?php echo get_staff_full_name($event['userid']); ?></a></small>
                                                                <?php } ?>
                                                            </p>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="panel-footer">
                                                        <?php echo _l('home_upcoming_events_next_week'); ?> : <?php echo $upcoming_events_next_week; ?>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="panel_s">
                                            <div class="panel-body">
                                                <?php echo form_open_multipart('admin/newsfeed/add_post',array('class'=>'dropzone','id'=>'new-post-form')); ?>
                                                <a href="<?php echo admin_url('profile'); ?>">
                                                    <?php echo staff_profile_image($_staff->staffid,array('staff-profile-image-small')); ?>
                                                    <?php echo $_staff->firstname . ' ' . $_staff->lastname ;?></a>
                                                <textarea name="content" id="post" rows="5" class="form-control" placeholder="<?php echo _l('whats_on_your_mind'); ?>"></textarea>
                                                <hr />
                                                <button type="submit" class="btn btn-primary pull-right"><?php echo _l('new_post'); ?></button>
                                                <a href="#" class="btn btn-default add-post-attachments"><i data-toggle="tooltip" title="<?php echo _l('newsfeed_upload_tooltip'); ?>" class="fa fa-files-o"></i></a>
                                                <select id="post-visibility" class="selectpicker" multiple name="visibility[]">
                                                    <option value="all" selected><?php echo _l('newsfeed_all_departments'); ?></option>
                                                    <?php foreach($departments as $department){ ?>
                                                        <option value="<?php echo $department['departmentid']; ?>"><?php echo $department['name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div class="dz-message" data-dz-message><span></span></div>
                                                <div class="dropzone-previews"></div>
                                                <?php echo form_close(); ?>
                                            </div>
                                        </div>
                                        <?php echo form_hidden('total_pages',$total_pages); ?>
                                        <div id="newsfeed_data">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end Tab 1 -->
                            <!-- TAB 2-->
                            <div role="tabpanel" class="tab-pane ptop10" id="gateway">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel_s">
                                            <div class="panel-body">
                                                <div class="row home-summary">
                                                    <div class="col-md-5ths col-xs-6 text-center border-right">
                                                        <a href="<?php echo admin_url('reports/transactions'); ?>">
                                                            <h2 class="bold no-margin"><?php echo total_rows('tbltransactions'); ?>  </h2>
                                                            <span class="bold text-warning mtop15 inline-block"><i class="fa fa-credit-card"></i>  Total Transactions</span>
                                                        </a>
                                                    </div>
                                                    <div class="col-md-5ths col-xs-6 text-center border-right">
                                                        <a href="<?php echo admin_url('reports/transactions?status=0'); ?>">
                                                            <h2 class="bold no-margin"><?php echo total_rows('tbltransactions',array('status'=>0)); ?>  </h2>
                                                            <span class="bold text-success mtop15 inline-block"><i class="fa fa-credit-card"></i>  Approved Transactions</span>
                                                        </a>
                                                    </div>
                                                    <div class="col-md-5ths col-xs-6 text-center border-right">
                                                        <a href="<?php echo admin_url('reports/transactions?status=1'); ?>">
                                                            <h2 class="bold no-margin"><?php echo total_rows('tbltransactions',array('status'=>1)); ?>  </h2>
                                                            <span class="bold text-danger mtop15 inline-block"><i class="fa fa-credit-card"></i>  Declined Transactions</span>
                                                        </a>
                                                    </div>
                                                    <?php
                                                    $total_transactions = total_rows('tbltransactions');
                                                    $total_approved = total_rows('tbltransactions',array('status'=>0));
                                                    $total_declined = total_rows('tbltransactions',array('status'=>1));
                                                    $total_chargeback = total_rows('tbltransactions',array('retrived'=>1));

                                                    $total_refund = 0;

                                                    $total_refund_payment = total_rows('tbltransactions',array('status'=>0,'refunded'=>1,'payment'=>1));
                                                    $total_refund_capture = total_rows('tbltransactions',array('status'=>0,'refunded'=>1,'captured'=>1));

                                                    $total_refund = $total_refund_payment + $total_refund_capture;

                                                    $percent_approved = ($total_transactions > 0 ? number_format(($total_approved * 100) / $total_transactions,2) : 0);

                                                    $total_revenue = 0;

                                                    $total_payment = sum_from_table('tbltransactions',array('field'=>'settlement','where'=>array('status'=>0,'payment'=>1)));
                                                    $total_capture = sum_from_table('tbltransactions',array('field'=>'settlement','where'=>array('status'=>0,'captured'=>1)));

                                                    $total_revenue = $total_payment + $total_capture;

                                                    $total_refund_amount = 0;

                                                    $total_payment_refund = sum_from_table('tbltransactions',array('field'=>'settlement','where'=>array('status'=>0,'payment'=>1,'refunded'=>1)));
                                                    $total_capture_refund = sum_from_table('tbltransactions',array('field'=>'settlement','where'=>array('status'=>0,'captured'=>1,'refunded'=>1)));

                                                    $total_refund_amount = $total_payment_refund + $total_capture_refund;

                                                    $total_declined_amount = 0;

                                                    $total_payment_declined= sum_from_table('tbltransactions',array('field'=>'settlement','where'=>array('status'=>1,'payment'=>1)));
                                                    $total_capture_declined = sum_from_table('tbltransactions',array('field'=>'settlement','where'=>array('status'=>1,'captured'=>1)));

                                                    $total_declined_amount = $total_payment_declined + $total_capture_declined;

                                                    $total_chargeback_revenue = sum_from_table('tbltransactions',array('field'=>'settlement','where'=>array('status'=>0,'retrived'=>1)));

                                                    ?>
                                                    <div class="col-md-5ths col-xs-6 text-center border-right">
                                                        <a href="<?php echo admin_url('gateway/transactions'); ?>">
                                                            <h2 class="bold no-margin"><?php echo $percent_approved?>  %</h2>
                                                            <span class="bold text-info mtop15 inline-block"><i class="fa fa-credit-card"></i>  Rate of Acceptance</span>
                                                        </a>
                                                    </div>
                                                    <div class="col-md-5ths col-xs-6 text-center border-right">
                                                        <a href="<?php echo admin_url('gateway/transactions?status=1'); ?>">
                                                            <h2 class="bold no-margin"><?php echo get_formatted_revenue($total_revenue) ?></h2>
                                                            <span class="bold text-info mtop15 inline-block"><i class="fa fa-money"></i>  Total Revenue</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-md-6">
                                        <div class="panel panel-success">
                                            <div class="panel-heading">
                                                <div class="row">
                                                    <div class="col-xs-3">
                                                        <i class="fa fa-credit-card fa-5x"></i>
                                                    </div>
                                                    <div class="col-xs-9 text-right">
                                                        <div class="huge"><b><?php echo total_rows('tbltransactions',array('status'=>0)); ?></b></div>
                                                        <div>Approved Count (CC)</div>
                                                    </div>
                                                </div>
                                                <table class="table">
                                                    <tr>
                                                        <td><b>Today</b></td>
                                                        <td><span class="badge pull-right"><?php echo get_cc_count_today()?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This week</b></td>
                                                        <td><span class="badge pull-right"><?php echo get_cc_count_week()?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This month</b></td>
                                                        <td><span class="badge pull-right"><?php echo get_cc_count_month()?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This year</b></td>
                                                        <td><span class="badge pull-right"><?php echo get_cc_count_month()?></span></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="panel panel-success">
                                            <div class="panel-heading">
                                                <div class="row">
                                                    <div class="col-xs-3">
                                                        <i class="fa fa-credit-card fa-5x"></i>
                                                    </div>
                                                    <div class="col-xs-9 text-right">
                                                        <div class="huge"><b><?php echo format_money($total_revenue) ?></b></div>
                                                        <div>Approved Volume (CC)</div>
                                                    </div>
                                                </div>
                                                <table class="table">
                                                    <tr>
                                                        <td><b>Today</b></td>
                                                        <td><span class="badge pull-right"><?php echo format_money(get_revenue_count_today())?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This week</b></td>
                                                        <td><span class="badge pull-right"><?php echo format_money(get_revenue_count_week())?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This month</b></td>
                                                        <td><span class="badge pull-right"><?php echo format_money(get_revenue_count_month())?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This year</b></td>
                                                        <td><span class="badge pull-right"><?php echo format_money(get_revenue_count_year())?></span></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="panel panel-success">
                                            <div class="panel-heading">
                                                <div class="row">
                                                    <div class="col-xs-3">
                                                        <i class="fa fa-undo fa-5x"></i>
                                                    </div>
                                                    <div class="col-xs-9 text-right">
                                                        <div class="huge"><b><?php echo $total_refund?></b></div>
                                                        <div>Refunds Count (CC)</div>
                                                    </div>
                                                </div>
                                                <table class="table">
                                                    <tr>
                                                        <td><b>Today</b></td>
                                                        <td><span class="badge pull-right"><?php echo get_refund_count_today()?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This week</b></td>
                                                        <td><span class="badge pull-right"><?php echo get_refund_count_week()?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This month</b></td>
                                                        <td><span class="badge pull-right"><?php echo get_refund_count_month()?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This year</b></td>
                                                        <td><span class="badge pull-right"><?php echo get_refund_count_month()?></span></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="panel panel-success">
                                            <div class="panel-heading">
                                                <div class="row">
                                                    <div class="col-xs-3">
                                                        <i class="fa fa-undo fa-5x"></i>
                                                    </div>
                                                    <div class="col-xs-9 text-right">
                                                        <div class="huge"><b><?php echo format_money($total_refund_amount) ?></b></div>
                                                        <div>Refunds Volume (CC)</div>
                                                    </div>
                                                </div>
                                                <table class="table">
                                                    <tr>
                                                        <td><b>Today</b></td>
                                                        <td><span class="badge pull-right"><?php echo format_money(get_refund_revenue_today())?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This week</b></td>
                                                        <td><span class="badge pull-right"><?php echo format_money(get_refund_revenue_week())?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This month</b></td>
                                                        <td><span class="badge pull-right"><?php echo format_money(get_refund_revenue_month())?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This year</b></td>
                                                        <td><span class="badge pull-right"><?php echo format_money(get_refund_revenue_month())?></span></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-md-6">
                                        <div class="panel panel-danger">
                                            <div class="panel-heading">
                                                <div class="row">
                                                    <div class="col-xs-3">
                                                        <i class="fa fa-credit-card fa-5x"></i>
                                                    </div>
                                                    <div class="col-xs-9 text-right">
                                                        <div class="huge"><?php echo $total_declined?></div>
                                                        <div>Declined Count (CC)</div>
                                                    </div>
                                                </div>
                                                <table class="table">
                                                    <tr>
                                                        <td><b>Today</b></td>
                                                        <td><span class="badge pull-right"><?php echo get_decline_count_today()?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This week</b></td>
                                                        <td><span class="badge pull-right"><?php echo get_decline_count_week()?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This month</b></td>
                                                        <td><span class="badge pull-right"><?php echo get_decline_count_month()?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This year</b></td>
                                                        <td><span class="badge pull-right"><?php echo get_decline_count_month()?></span></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="panel panel-danger">
                                            <div class="panel-heading">
                                                <div class="row">
                                                    <div class="col-xs-3">
                                                        <i class="fa fa-credit-card fa-5x"></i>
                                                    </div>
                                                    <div class="col-xs-9 text-right">
                                                        <div class="huge"><?php echo format_money($total_declined_amount)?></div>
                                                        <div>Declined Volume (CC)</div>
                                                    </div>
                                                </div>
                                                <table class="table">
                                                    <tr>
                                                        <td><b>Today</b></td>
                                                        <td><span class="badge pull-right"><?php echo format_money(get_decline_revenue_today())?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This week</b></td>
                                                        <td><span class="badge pull-right"><?php echo format_money(get_decline_revenue_week())?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This month</b></td>
                                                        <td><span class="badge pull-right"><?php echo format_money(get_decline_revenue_month())?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This year</b></td>
                                                        <td><span class="badge pull-right"><?php echo format_money(get_decline_revenue_month())?></span></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="panel panel-danger">
                                            <div class="panel-heading">
                                                <div class="row">
                                                    <div class="col-xs-3">
                                                        <i class="fa fa-university fa-5x"></i>
                                                    </div>
                                                    <div class="col-xs-9 text-right">
                                                        <div class="huge"><?php echo $total_chargeback?></div>
                                                        <div>Chargeback Count (CC)</div>
                                                    </div>
                                                </div>
                                                <table class="table">
                                                    <tr>
                                                        <td><b>Today</b></td>
                                                        <td><span class="badge pull-right"><?php echo get_chargeback_count_today()?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This week</b></td>
                                                        <td><span class="badge pull-right"><?php echo get_chargeback_count_week()?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This month</b></td>
                                                        <td><span class="badge pull-right"><?php echo get_chargeback_count_month()?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This year</b></td>
                                                        <td><span class="badge pull-right"><?php echo get_chargeback_count_month()?></span></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="panel panel-danger">
                                            <div class="panel-heading">
                                                <div class="row">
                                                    <div class="col-xs-3">
                                                        <i class="fa fa-university fa-5x"></i>
                                                    </div>
                                                    <div class="col-xs-9 text-right">
                                                        <div class="huge"><?php echo format_money($total_chargeback_revenue)?></div>
                                                        <div>Chargeback Volume (CC)</div>
                                                    </div>
                                                </div>
                                                <table class="table">
                                                    <tr>
                                                        <td><b>Today</b></td>
                                                        <td><span class="badge pull-right"><?php echo format_money(get_chargeback_revenue_today())?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This week</b></td>
                                                        <td><span class="badge pull-right"><?php echo format_money(get_chargeback_revenue_week())?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This month</b></td>
                                                        <td><span class="badge pull-right"><?php echo format_money(get_chargeback_revenue_month())?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>This year</b></td>
                                                        <td><span class="badge pull-right"><?php echo format_money(get_chargeback_revenue_month())?></span></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end TAB 2-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
 google_api = '<?php echo $google_api_key; ?>';
 calendarIDs = '<?php echo json_encode($google_ids_calendars); ?>';
</script>
<?php init_tail(); ?>

<?php include_once(APPPATH . 'views/admin/includes/modals/post_likes.php'); ?>
<?php include_once(APPPATH . 'views/admin/includes/modals/newsfeed_images.php'); ?>
<?php include_once(APPPATH . 'views/admin/includes/modals/post_comment_likes.php'); ?>
<?php $this->load->view('admin/utilities/calendar_template'); ?>
<script src="<?php echo site_url('assets/js/newsfeed.js'); ?>"></script>
<script>
 var weekly_payments_statistics;
 $(document).ready(function() {
	    // Tickets awaiting reply by department chart
	    var ctx_tickets_departments = $('#tickets-awaiting-reply-by-department').get(0).getContext('2d');
	    var tickets_dep_chart = new Chart(ctx_tickets_departments).Doughnut(<?php echo $tickets_awaiting_reply_by_department; ?>);

	/*    $("#tickets-awaiting-reply-by-department").on('click',function(evt){
                            var activePoints = tickets_dep_chart.getSegmentsAtEvent(evt);
                            console.log(activePoints);
                        })*/

	    // Tickets awaiting reply by department chart
	    var ctx_tickets_status = $('#tickets-awaiting-reply-by-status').get(0).getContext('2d');
	    new Chart(ctx_tickets_status).Doughnut(<?php echo $tickets_reply_by_status; ?>);
	    // Payments statistics
	    init_weekly_payment_statistics();
	    $('select[name="currency"]').on('change', function() {
	    	init_weekly_payment_statistics();
	    });
	});

 function init_weekly_payment_statistics() {
    if ($('#weekly-payment-statistics').length > 0) {
       if (typeof(weekly_payments_statistics) !== 'undefined') {
          weekly_payments_statistics.destroy();
      }
      var currency = $('select[name="currency"]').val();
      $.get(admin_url + 'home/weekly_payments_statistics/' + currency, function(response) {
          var ctx = $('#weekly-payment-statistics').get(0).getContext('2d');
          weekly_payments_statistics = new Chart(ctx).Line(response, line_chart_options);
      }, 'json');
  }
}

</script>
</body>
</html>
