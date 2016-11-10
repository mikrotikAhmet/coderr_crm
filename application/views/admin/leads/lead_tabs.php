                 <?php if($lead->lost == 1){
                    echo '<div class="ribbon danger"><span>'._l('lead_lost').'</span></div>';
                } else if($lead->junk == 1){
                    echo '<div class="ribbon warning"><span>'._l('lead_junk').'</span></div>';
                } else {
                    if (total_rows('tblclients', array(
                        'leadid' => $lead->id))) {
                        echo '<div class="ribbon success"><span>'._l('lead_is_client').'</span></div>';
                }
            }
            ?>
            <button type="button" class="close leads-kanban-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <ul class="nav nav-tabs" role="tablist">
                <?php if(count($mail_activity) > 0){ ?>
                <li role="presentation" class="active">
                    <a href="#tab_email_activity" aria-controls="tab_email_activity" role="tab" data-toggle="tab">
                        <?php echo _l('lead_email_activity'); ?>
                    </a>
                </li>
                <?php } ?>
                <?php if(isset($profile)){ ?>
                <!-- from leads modal -->
                <li role="presentation">
                    <a href="#tab_lead_profile" aria-controls="tab_lead_profile" role="tab" data-toggle="tab">
                        <?php echo _l('lead_profile'); ?>
                    </a>
                </li>
                <?php } ?>
                <li role="presentation" <?php if(count($mail_activity) == 0){echo 'class="active"'; } ?>>
                    <a href="#activity" aria-controls="activity" role="tab" data-toggle="tab">
                        <?php echo _l('lead_add_edit_activity'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#tab_proposals" aria-controls="tab_proposals" role="tab" data-toggle="tab">
                        <?php echo _l('proposals'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#tab_tasks" aria-controls="tab_tasks" role="tab" data-toggle="tab">
                        <?php echo _l('tasks'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#attachments" aria-controls="attachments" role="tab" data-toggle="tab">
                        <?php echo _l('lead_attachments'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#reminders" aria-controls="notes" role="tab" data-toggle="tab">
                        <?php echo _l('leads_reminders_tab'); ?>
                    </a>
                </li>

                <li role="presentation">
                    <a href="#notes" aria-controls="notes" role="tab" data-toggle="tab">
                        <?php echo _l('lead_add_edit_notes'); ?>
                    </a>
                </li>

            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <?php if(count($mail_activity) > 0){ ?>
                <div role="tabpanel" class="tab-pane active" id="tab_email_activity">
                    <?php foreach($mail_activity as $_mail_activity){ ?>
                    <div class="media-left">
                        <i class="fa fa-envelope"></i>

                    </div>
                    <div class="media-body">
                        <h4 class="bold no-margin"><?php echo $_mail_activity['subject']; ?></h4>
                        <?php echo $_mail_activity['body']; ?><small class="text-muted display-block"><?php echo _dt($_mail_activity['dateadded']); ?></small>
                        <hr />
                    </div>
                    <div class="clearfix"></div>
                    <?php } ?>
                </div>
                <?php } ?>

                <?php if(isset($profile)){ ?>
                <!-- from leads modal -->
                <div role="tabpanel" class="tab-pane" id="tab_lead_profile">
                    <?php echo $profile; ?>
                </div>
                <?php } ?>

                <div role="tabpanel" class="tab-pane <?php if(count($mail_activity) == 0){echo 'active'; } ?>" id="activity">
                    <div class="panel_s mtop20">
                        <?php foreach($activity_log as $log){   ?>
                        <div class="media">
                            <?php if($log['staffid'] != 0){ ?>
                            <div class="media-left">
                                <a href="<?php echo admin_url('profile/'.$log["staffid"]); ?>">
                                    <?php echo staff_profile_image($log['staffid'],array('staff-profile-image-small','media-object')); ?>&nbsp;
                                </a>
                            </div>
                            <?php } ?>
                            <div class="media-body">
                                <?php echo $log['description']; ?><small class="text-muted display-block"><?php echo _dt($log['date']); ?></small>
                                <hr />
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_proposals">
                 <?php
                 $table_data = array(
                    _l('proposal_subject'),
                    _l('proposal_total'),
                    _l('proposal_open_till'),
                    _l('proposal_date_created'),
                    _l('proposal_status'));
                 $custom_fields = get_custom_fields('proposal',array('show_on_table'=>1));
                 foreach($custom_fields as $field){
                    array_push($table_data,$field['name']);
                }
                render_datatable($table_data,'proposals-lead'); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_tasks">
                <?php init_relation_tasks_table(array('data-new-rel-id'=>$lead->id,'data-new-rel-type'=>'lead')); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="reminders">
              <a href="#" data-toggle="modal" data-target=".reminder-modal"><i class="fa fa-bell-o"> <?php echo _l('client_edit_set_reminder'); ?></i></a>
              <?php render_datatable(array( _l( 'reminder_description'), _l( 'reminder_date'), _l( 'reminder_staff'), _l( 'reminder_is_notified'), _l( 'options'), ), 'reminders'); ?>
              <!-- Lead add reminder modal -->
              <div class="modal fade reminder-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <?php echo form_open('admin/misc/add_reminder/'.$lead->id . '/lead',array('id'=>'form-reminder')); ?>
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel"><i class="fa fa-question-circle" data-toggle="tooltip" title="<?php echo _l('set_reminder_tooltip'); ?>" data-placement="bottom"></i> <?php echo _l('lead_set_reminder_title'); ?></h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php echo form_hidden('rel_id',$lead->id); ?>
                                    <?php echo form_hidden('rel_type','lead'); ?>
                                    <?php echo render_date_input('date','set_reminder_date'); ?>
                                    <?php echo render_select('staff',$members,array('staffid',array('firstname','lastname')),'reminder_set_to',get_staff_user_id()); ?>
                                    <?php echo render_textarea('description','reminder_description'); ?>
                                    <div class="form-group">
                                        <label for="notify_by_email"><?php echo _l('reminder_notify_me_by_email'); ?></label>
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" name="notify_by_email">
                                            <label for=""><?php echo _l('reminder_notify_me_by_email'); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                            <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="attachments">
            <?php echo form_open('admin/leads/add_lead_attachment',array('class'=>'dropzone mtop30','id'=>'lead-attachment-upload')); ?>
            <?php echo form_close(); ?>
            <div class="mtop30" id="lead_attachments"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="notes">
            <?php echo form_open(admin_url('leads/add_note'),array('id'=>'lead-notes')); ?>
            <button type="submit" class="btn btn-info pull-right mtop15 mbot15">
                <?php echo _l('lead_add_edit_add_note'); ?></button>
                <?php echo form_hidden('leadid',$lead->id); ?>
                <?php echo render_textarea('description'); ?>
                <div class="lead-select-date-contacted hide">
                    <?php echo render_date_input('custom_contact_date','lead_add_edit_datecontacted'); ?>
                </div>
                <div class="radio radio-primary">
                    <input type="radio" name="contacted_indicator" value="yes">
                    <label><?php echo _l('lead_add_edit_contected_this_lead'); ?></label>
                </div>
                <div class="radio radio-primary">
                    <input type="radio" name="contacted_indicator" value="no" checked>
                    <label><?php echo _l('lead_not_contacted'); ?></label>
                </div>
                <?php echo form_close(); ?>
                <hr />
                <div class="panel_s mtop20">
                    <?php foreach($notes as $note){ ?>
                    <div class="media lead-note">
                        <div class="media-left">
                            <a href="<?php echo admin_url('profile/'.$note["staffid"]); ?>">
                                <?php echo staff_profile_image($note['staffid'],array('staff-profile-image-small','media-object')); ?>
                            </a>
                        </div>
                        <div class="media-body">
                            <?php if($note['staffid'] == get_staff_user_id() || is_admin()){ ?>
                            <a href="#" class="pull-right text-danger" onclick="delete_lead_note(this,<?php echo $note['id']; ?>);return false;"><i class="fa fa-trash-o"></i></a>
                            <?php } ?>
                            <h5 class="media-heading mtop10"><?php echo get_staff_full_name($note['staffid']); ?></h5>
                            <?php echo $note['description']; ?><small class="text-muted display-block"><?php echo _dt($note['dateadded']); ?></small>
                        </div>
                        <hr />
                    </div>

                    <?php } ?>
                </div>
            </div>

        </div>
