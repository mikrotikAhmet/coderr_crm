                              <div class="modal animated fadeIn" id="newEventModal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title"><?php echo _l('utility_calendar_new_event_title'); ?></h4>
                                        </div>
                                        <?php echo form_open('admin/utilities/calendar',array('id'=>'calendar-event-form')); ?>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <?php echo render_input('title','utility_calendar_new_event_placeholder'); ?>
                                                    <?php echo render_date_input('start','utility_calendar_new_event_start_date'); ?>
                                                    <div class="clearfix mtop15"></div>
                                                    <?php echo render_date_input('end','utility_calendar_new_event_end_date'); ?>
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="public">
                                                        <label><?php echo _l('utility_calendar_new_event_make_public'); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                                            <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                                        </div>
                                        <?php echo form_close(); ?>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
