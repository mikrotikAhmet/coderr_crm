<?php init_head(); ?>
<style>
	#ribbon_<?php echo $ticket->ticketid; ?> span::before {
		border-top: 3px solid <?php echo $ticket->statuscolor; ?>;
		border-left: 3px solid <?php echo $ticket->statuscolor; ?>;
	}
</style>
<?php set_ticket_open($ticket->adminread,$ticket->ticketid); ?>
<div id="wrapper">
	<div class="content">
		<?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'single-ticket-form')); ?>
		<?php echo form_hidden('ticketid',$ticket->ticketid); ?>
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<?php echo '<div class="ribbon" id="ribbon_'.$ticket->ticketid.'"><span style="background:'.$ticket->statuscolor.'">'.$ticket->status_name.'</span></div>'; ?>
						<ul class="nav nav-tabs no-margin" role="tablist">
							<li role="presentation" class="<?php if(!$this->session->flashdata('active_tab')){echo 'active';} ?>">
								<a href="#addreply" aria-controls="addreply" role="tab" data-toggle="tab">
									<?php echo _l('ticket_single_add_reply'); ?>
								</a>
							</li>
							<li role="presentation">
								<a href="#note" aria-controls="note" role="tab" data-toggle="tab">
									<?php echo _l('ticket_single_add_note'); ?>
								</a>
							</li>
							<li role="presentation">
								<a href="#othertickets" aria-controls="othertickets" role="tab" data-toggle="tab">
									<?php echo _l('ticket_single_other_user_tickets'); ?>
								</a>
							</li>
							<li role="presentation">
								<a href="#tasks" aria-controls="tasks" role="tab" data-toggle="tab">
									<?php echo _l('tasks'); ?>
								</a>
							</li>
							<li role="presentation" class="<?php if($this->session->flashdata('active_tab_settings')){echo 'active';} ?>">
								<a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">
									<?php echo _l('ticket_single_settings'); ?>
								</a>
							</li>
						</ul>
					</div>
				</div>
				<div class="panel_s">
					<div class="panel-body">

						<div class="row">
							<div class="col-md-8">
								<?php if(!empty($ticket->priority_name)){ ?>
								<span class="label label-default inline-block">
									<?php echo _l('ticket_single_priority',$ticket->priority_name); ?>
								</span>
								<?php } ?>
								<?php if($ticket->lastreply !== NULL){ ?>
								<span class="label label-primary inline-block" data-toggle="tooltip" title="<?php echo time_ago_specific($ticket->lastreply); ?>">
									<?php echo _l('ticket_single_last_reply',time_ago($ticket->lastreply)); ?>
								</span>
								<?php } ?>
								<h3>#<?php echo $ticket->ticketid; ?> - <?php echo $ticket->subject; ?></h3>
								<hr />
							</div>
							<div class="col-md-4 text-right">
								<div class="row">
									<div class="col-md-6 col-md-offset-6">
										<?php echo render_select('status_top',$_ticket_statuses,array('ticketstatusid','name'),'ticket_single_change_status_top',$ticket->status); ?>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane <?php if(!$this->session->flashdata('active_tab')){echo 'active';} ?>" id="addreply">
								<?php if(sizeof($ticket->ticket_notes) > 0){ ?>
								<div class="row">
									<div class="col-md-12">
										<h4 class="bold"><?php echo _l('ticket_single_private_staff_notes'); ?></h4>
										<div class="ticketstaffnotes">
											<table>
												<tbody>
													<?php foreach($ticket->ticket_notes as $note){ ?>
													<tr>
														<td><?php echo _l('ticket_single_ticket_note_by',$note['note_creator']); ?></td>
														<td align="right"><?php echo _l('ticket_single_note_added',_dt($note['date'])); ?></td>
														<td align="right" width="25">
															<a href="<?php echo admin_url('tickets/delete_ticket_note/'.$note["ticketid"] .'/'.$note["ticketnoteid"].''); ?>" class="delete_ticket_note">
																<i class="fa fa-remove"></i>
															</a></td>
														</tr>
														<?php } ?>
													</tbody>
												</table>
												<div class="padding-10">
													<?php echo $note['note']; ?>
												</div>
											</div>
										</div>
									</div>
									<?php } ?>
									<div class="form-group">
										<?php $this->load->view('admin/editor/template',array('name'=>'message','contents'=>'')); ?>
									</div>
									<div class="panel_s ticket-reply-tools">
										<div class="panel-body">
											<div class="row">
												<div class="col-md-5">
													<?php echo render_select('status',$_ticket_statuses,array('ticketstatusid','name'),'ticket_single_change_status'); ?>
													<?php if($ticket->assigned !== get_staff_user_id()){ ?>
													<div class="checkbox checkbox-primary">
														<input type="checkbox" name="assign_to_current_user">
														<label><?php echo _l('ticket_single_assign_to_me_on_update'); ?></label>
													</div>
													<?php } ?>
												</div>
												<?php
												$use_knowledge_base = get_option('use_knowledge_base');
												?>
												<div class="col-md-7 mtop20">
													<a class="btn btn-default pull-right mleft10" data-toggle="modal" data-target="#insert_predefined_reply">
														<?php echo _l('ticket_single_insert_predefined_reply'); ?></a>
														<?php if($use_knowledge_base == 1){ ?>
														<a class="btn btn-default pull-right" data-toggle="modal" data-target="#insert_knowledge_base_link">
															<?php echo _l('ticket_single_insert_knowledge_base_link'); ?></a>
															<?php } ?>
														</div>
														<?php
														include_once(APPPATH . 'views/admin/includes/modals/insert_predefined_reply.php');
														if($use_knowledge_base == 1){
															include_once(APPPATH . 'views/admin/includes/modals/insert_knowledge_base_link.php');
														}
														?>
													</div>
													<hr />
													<div class="row attachments">
														<div class="attachment">
															<div class="col-md-5">
																<div class="form-group">
																	<label for="attachment" class="control-label">
																		<?php echo _l('ticket_single_attachments'); ?>
																	</label>
																	<input type="file" class="form-control" name="attachments[]">
																</div>
															</div>
															<div class="col-md-1">
																<button class="btn btn-success original-button mtop25 add_more_attachments" type="button"><i class="fa fa-plus"></i></button>
															</div>
															<div class="clearfix"></div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-12">
															<button type="submit" class="btn btn-primary">
																<?php echo _l('ticket_single_add_response'); ?>
															</button>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div role="tabpanel" class="tab-pane" id="note">
											<div class="form-group">
												<label for="note"><?php echo _l('ticket_single_note_heading'); ?></label>
												<textarea class="form-control" name="note" rows="5"></textarea>
											</div>
											<a class="btn btn-primary pull-right add_note_ticket"><?php echo _l('ticket_single_add_note'); ?></a>
										</div>
										<div role="tabpanel" class="tab-pane" id="othertickets">
											<?php echo AdminTicketsTableStructure(); ?>
										</div>
										<div role="tabpanel" class="tab-pane" id="tasks">
											<?php init_relation_tasks_table(array('data-new-rel-id'=>$ticket->ticketid,'data-new-rel-type'=>'ticket')); ?>
										</div>
										<div role="tabpanel" class="tab-pane <?php if($this->session->flashdata('active_tab_settings')){echo 'active';} ?>" id="settings">
											<div class="row">
												<div class="col-md-6">
													<div class="col-md-12 row">
														<?php echo render_input('subject','ticket_settings_subject',$ticket->subject); ?>
													</div>
													<div class="col-md-12 row">
														<?php echo render_select('userid',$clients,array('userid',array('firstname','lastname'),'company'),'ticket_settings_client',$ticket->userid,array('data-toggle'=>'tooltip','title'=>'ticket_settings_select_client')); ?>
													</div>
													<div class="col-md-12 row">
														<?php echo render_input('to','ticket_settings_to',$ticket->submitter,'text',array('disabled'=>true)); ?>
													</div>
													<div class="col-md-12 row">
													<?php
													if($ticket->userid != 0){
														echo render_input('email','ticket_settings_email',$ticket->email,'email',array('disabled'=>true));
													} else {
														echo render_input('email','ticket_settings_email',$ticket->ticket_email,'email',array('disabled'=>true));
													}
													?>
													</div>
													<div class="col-md-12 row">
														<?php echo render_select('department',$departments,array('departmentid','name'),'ticket_settings_departments',$ticket->department); ?>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="assigned" class="control-label">
															<?php echo _l('ticket_settings_assign_to'); ?>
														</label>
														<select name="assigned" id="assigned" class="form-control selectpicker">
															<option value="none"><?php echo _l('ticket_settings_none_assigned'); ?></option>
															<?php foreach($staff as $member){ ?>
															<option value="<?php echo $member['staffid']; ?>" <?php if($member['staffid'] == $ticket->assigned){echo 'selected';} ?>>
																<?php echo $member['firstname'] . ' ' . $member['lastname'] ; ?>
																<?php if(($member['staffid'] == get_staff_user_id())){ echo ' - You';} ?>
															</option>
															<?php } ?>
														</select>
													</div>
													<?php if(get_option('services') == 1){ ?>
													<div class="col-md-12 row">
														<?php echo render_select('service',$services,array('serviceid','name'),'ticket_settings_service',$ticket->service); ?>
													</div>
													<?php } ?>
													<div class="col-md-12 row">
														<?php echo render_select('priority',$priorities,array('priorityid','name'),'ticket_settings_priority',$ticket->priority); ?>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12 text-center mtop25">
													<a href="#" class="btn btn-primary save_changes_settings_single_ticket">
														<?php echo _l('submit'); ?>
													</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="panel_s mtop20">
								<div class="panel-body <?php if($ticket->admin == NULL){echo 'client-reply';} ?>">
									<div class="row">
										<div class="col-md-3 border-right">
											<p>
												<?php if($ticket->admin == NULL || $ticket->admin == 0){ ?>
												<?php if($ticket->userid != 0){ ?>
												<a href="<?php echo admin_url('clients/client/'.$ticket->userid); ?>"
													><?php echo $ticket->submitter; ?>
												</a>
												<?php } else {
													echo $ticket->submitter;
													?>
													<br />
													<a href="mailto:<?php echo $ticket->ticket_email; ?>"><?php echo $ticket->ticket_email; ?></a>
													<hr />
													<?php
													if(total_rows('tblticketsspamcontrol',array('type'=>'sender','value'=>$ticket->ticket_email)) == 0){ ?>
													<button type="button" data-sender="<?php echo $ticket->ticket_email; ?>" class="btn btn-danger block-sender"><?php echo _l('block_sender'); ?></button>
													<?php
												} else {
													echo '<span class="label label-danger">'._l('sender_blocked').'</span>';
												}
											}
										}else {  ?>
										<a href="<?php echo admin_url('profile/'.$ticket->admin); ?>"><?php echo $ticket->submitter; ?></a>
										<?php } ?>
									</p>
									<p class="text-muted">
										<?php if($ticket->admin !== NULL || $ticket->admin != 0){
											echo _l('ticket_staff_string');
										} else {
											if($ticket->userid != 0){
												echo _l('ticket_client_string');
											}
										}
										?>
									</p>
								</div>
								<div class="col-md-9">
									<?php echo check_for_links($ticket->message); ?><br />
									<p>-----------------------------</p>
									<?php if(filter_var($ticket->ip, FILTER_VALIDATE_IP)){ ?>
									<p>IP: <?php echo $ticket->ip; ?></p>
									<?php } ?>

									<?php if(count($ticket->attachments) > 0){
										echo '<hr />';
										foreach($ticket->attachments as $attachment){ ?>
										<a href="<?php echo site_url('download/file/ticket/'. $attachment['id']); ?>" class="display-block"><i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i> <?php echo $attachment['file_name']; ?></a>
										<?php }
									} ?>
								</div>
							</div>
						</div>
						<div class="panel-footer">
							<?php echo _l('ticket_posted',_dt($ticket->date)); ?>
						</div>
					</div>

					<?php foreach($ticket_replies as $reply){ ?>
					<div class="panel_s">
						<div class="panel-body <?php if($reply['admin'] == NULL){echo 'client-reply';} ?>">
							<div class="row">
								<div class="col-md-3 border-right">
									<p>
										<?php if($reply['admin'] == NULL || $reply['admin'] == 0){ ?>
										<?php if($reply['userid'] != 0){ ?>
										<a href="<?php echo admin_url('clients/client/'.$reply['userid']); ?>"><?php echo $reply['submitter']; ?></a>
										<?php } else { ?>
										<?php echo $reply['submitter']; ?>
										<br />
										<a href="mailto:<?php echo $reply['reply_email']; ?>"><?php echo $reply['reply_email']; ?></a>
										<?php } ?>
										<?php }  else { ?>
										<a href="<?php echo admin_url('profile/'.$reply['admin']); ?>"><?php echo $reply['submitter']; ?></a>
										<?php } ?>
									</p>
									<p class="text-muted">
										<?php if($reply['admin'] !== NULL || $reply['admin'] != 0){
											echo _l('ticket_staff_string');
										} else {
											if($reply['userid'] != 0){
												echo _l('ticket_client_string');
											}
										}
										?>
									</p>
									<hr />
									<a href="<?php echo admin_url('tickets/delete_ticket_reply/'.$ticket->ticketid .'/'.$reply['id']); ?>" class="text-danger pull-left"><span class="label label-danger"><?php echo _l('delete_ticket_reply'); ?></span></a>
								</div>
								<div class="col-md-9">
									<div class="clearfix"></div>
									<?php echo check_for_links($reply['message']); ?><br />
									<p>-----------------------------</p>
									<?php if(filter_var($reply['ip'], FILTER_VALIDATE_IP)){ ?>
									<p>IP: <?php echo $reply['ip']; ?></p>
									<?php } ?>
									<?php if(count($reply['attachments']) > 0){
										echo '<hr />';
										foreach($reply['attachments'] as $attachment){ ?>
										<a href="<?php echo site_url('download/file/ticket/'. $attachment['id']); ?>" class="display-block"><i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i> <?php echo $attachment['file_name']; ?></a>
										<?php }
									} ?>
								</div>
							</div>
						</div>
						<div class="panel-footer">
							<span><?php echo _l('ticket_posted',_dt($reply['date'])); ?></span>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
	<?php init_tail(); ?>
	<script src="<?php echo site_url(); ?>assets/js/tickets.js"></script>
	<script>
		initDataTable('.table-rel-tasks', admin_url +'tasks/init_relation_tasks/<?php echo $ticket->ticketid; ?>/ticket', 'tasks');
		_validate_form($('form'),{
			"attachments[]" : {
				extension: ticket_attachments_file_extension
			}
		});
	</script>
</body>
</html>
