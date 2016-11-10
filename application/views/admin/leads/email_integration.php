<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <?php echo $title; ?>
                    </div>
                    <div class="panel-body">
                        <?php echo form_open($this->uri->uri_string(),array('id'=>'leads-email-integration')); ?>
                        <div class="row">
                            <div class="col-md-6">
                             <div class="checkbox checkbox-primary">
                                <input type="checkbox" name="active" <?php if($mail->active == 1){echo 'checked';} ?>>
                                <label for=""><?php echo _l('leads_email_active'); ?></label>
                            </div>
                            <?php echo render_input('imap_server','leads_email_integration_imap',$mail->imap_server); ?>

                            <?php echo render_input('email','leads_email_integration_email',$mail->email,'email'); ?>

                            <?php echo render_input('password','leads_email_integration_password',$mail->password,'password'); ?>

                            <?php echo render_input('port','leads_email_integration_port',$mail->port); ?>

                            <div class="form-group">
                                <label for="encryption"><?php echo _l('leads_email_encryption'); ?></label><br />
                                <div class="radio radio-primary radio-inline">
                                    <input type="radio" name="encryption" value="tls" <?php if($mail->encryption == 'tls'){echo 'checked';} ?>>
                                    <label for="">TLS</label>
                                </div>
                                <div class="radio radio-primary radio-inline">
                                    <input type="radio" name="encryption" value="ssl" <?php if($mail->encryption == 'ssl'){echo 'checked';} ?>>
                                    <label for="">SSL</label>
                                </div>
                                <div class="radio radio-primary radio-inline">
                                    <input type="radio" name="encryption" value="" <?php if($mail->encryption == ''){echo 'checked';} ?>>
                                    <label for=""><?php echo _l('leads_email_integration_folder_no_encryption'); ?></label>
                                </div>
                            </div>

                            <?php echo render_input('folder','leads_email_integration_folder',$mail->folder); ?>

                            <?php echo render_input('check_every','leads_email_integration_check_every',$mail->check_every,'number',array('min'=>10)); ?>

                            <div class="checkbox checkbox-primary" data-toggle="tooltip" data-title="<?php echo _l('leads_email_integration_only_check_unseen_emails_help'); ?>">
                                <input type="checkbox" name="only_loop_on_unseen_emails" <?php if($mail->only_loop_on_unseen_emails == 1){echo 'checked';} ?>>
                                <label for=""><?php echo _l('leads_email_integration_only_check_unseen_emails'); ?></label>
                            </div>

                        </div>

                        <div class="col-md-6">
                           <?php echo render_select('lead_source',$sources,array('id','name'),'leads_email_integration_default_source',$mail->lead_source); ?>

                           <?php
                           echo render_select('lead_status',$statuses,array('id','name'),'leads_email_integration_default_status',$mail->lead_status);
                           ?>
                           <?php
                           $selected = '';
                           foreach($members as $staff){
                            if($mail->responsible == $staff['staffid']){
                                $selected = $staff['staffid'];
                            }
                        }
                        ?>
                        <?php echo render_select('responsible',$members,array('staffid',array('firstname','lastname')),'leads_email_integration_default_assigned',$selected); ?>

                        <hr />
                        <label for="" class="control-label">Notification settings</label>
                        <div class="clearfix"></div>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="notify_lead_imported" <?php if($mail->notify_lead_imported == 1){echo 'checked';} ?>>
                            <label for=""><?php echo _l('leads_email_integration_notify_when_lead_imported'); ?></label>
                        </div>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="notify_lead_contact_more_times" <?php if($mail->notify_lead_contact_more_times == 1){echo 'checked';} ?>>
                            <label for=""><?php echo _l('leads_email_integration_notify_when_lead_contact_more_times'); ?></label>
                        </div>
                        <hr />
                        <div class="radio radio-primary radio-inline">
                            <input type="radio" name="notify_type" value="roles" <?php if($mail->notify_type == 'roles'){echo 'checked';} ?>>
                            <label for="">Staff Members with role</label>
                        </div>
                        <div class="radio radio-primary radio-inline">
                            <input type="radio" name="notify_type" value="specific_staff" <?php if($mail->notify_type == 'specific_staff'){echo 'checked';} ?>>
                            <label for="">Specific Staff Members</label>
                        </div>
                        <div class="clearfix mtop15"></div>
                        <div id="role_notify" class="<?php if($mail->notify_type != 'roles'){echo 'hide';} ?>">
                            <?php
                            $selected = array();
                            if($mail->notify_type == 'roles'){
                                $selected = unserialize($mail->notify_ids);
                            }
                            ?>
                            <?php echo render_select('notify_ids_roles[]',$roles,array('roleid',array('name')),'leads_email_integration_notify_roles',$selected,array('multiple'=>true)); ?>
                        </div>
                        <div id="specific_staff_notify" class="<?php if($mail->notify_type != 'specific_staff'){echo 'hide';} ?>">
                            <?php
                            $selected = array();
                            if($mail->notify_type == 'specific_staff'){
                                $selected = unserialize($mail->notify_ids);
                            }
                            ?>
                            <?php echo render_select('notify_ids_staff[]',$members,array('staffid',array('firstname','lastname')),'leads_email_integration_notify_staff',$selected,array('multiple'=>true)); ?>
                        </div>
                        <hr />
                        <a href="<?php echo admin_url('leads/test_email_integration'); ?>" class="btn btn-default pull-right"><?php echo _l('leads_email_integration_test_connection'); ?></a>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12 text-right">
                        <hr />
                        <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<?php init_tail(); ?>
<script>
    $(document).ready(function() {
        $('input[name="notify_type"]').on('change', function() {
            var val = $(this).val();
            if (val == 'specific_staff') {
                $('#specific_staff_notify').removeClass('hide');
                $('#role_notify').addClass('hide');
            } else {
                $('#specific_staff_notify').addClass('hide');
                $('#role_notify').removeClass('hide');
            }
        });
    })
    _validate_form($('#leads-email-integration'), {
        lead_source: 'required',
        lead_status: 'required',
        imap_server: 'required',
        password: 'required',
        port: 'required',
        email: {
            email: true,
            required: true
        },
        check_every: {
            required: true,
            number: true
        },
        folder: 'required'
    });
</script>
</body>
</html>
