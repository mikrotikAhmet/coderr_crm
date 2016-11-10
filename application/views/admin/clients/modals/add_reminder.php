                    <!-- Client add reminder modal -->
                    <div class="modal fade reminder-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <?php echo form_open('admin/misc/add_reminder/'.$client->userid . '/customer',array('id'=>'form-reminder')); ?>
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-question-circle" data-toggle="tooltip" title="<?php echo _l('set_reminder_tooltip'); ?>" data-placement="bottom"></i> <?php echo _l('client_edit_set_reminder_title'); ?></h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php echo form_hidden('rel_id',$client->userid); ?>
                                            <?php echo form_hidden('rel_type','customer'); ?>
                                            <?php echo render_date_input('date','set_reminder_date'); ?>
                                            <?php
                                            $i = 0;
                                            foreach($members as $staff){
                                                if(!has_permission('manageClients',$staff['staffid'])){
                                                    unset($members[$i]);
                                                }
                                                $i++;
                                            }
                                            ?>
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

