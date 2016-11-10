<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<?php echo form_open_multipart($this->uri->uri_string()); ?>
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6">
								<div class="col-md-12 row">
									<?php echo render_input('subject','ticket_settings_subject'); ?>
								</div>
								<div class="col-md-12 row">
									<?php $selected = (isset($userid) ? $userid : ''); ?>
									<?php echo render_select('userid',$clients,array('userid',array('firstname','lastname'),'company'),'ticket_settings_client',$selected,array('data-toggle'=>'tooltip','title'=>'ticket_settings_select_client')); ?>
								</div>
								<div class="col-md-12 row">
									<?php $value = (isset($userid) ? $client->firstname . ' ' . $client->lastname: ''); ?>
									<?php echo render_input('to','ticket_settings_to',$value,'text',array('disabled'=>true)); ?>
								</div>
								<div class="col-md-12 row">
									<?php $value = (isset($userid) ? $client->email: ''); ?>
									<?php echo render_input('email','ticket_settings_email',$value,'email',array('disabled'=>true)); ?>
								</div>
								<div class="col-md-12 row">
									<?php echo render_select('department',$departments,array('departmentid','name'),'ticket_settings_departments'); ?>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="assigned" class="control-label"><?php echo _l('ticket_settings_assign_to'); ?></label>
									<select name="assigned" id="assigned" class="form-control selectpicker" data-live-search="true">
										<option value="none">None</option>
										<?php foreach($staff as $member){ ?>
										<option value="<?php echo $member['staffid']; ?>" <?php if($member['staffid'] == get_staff_user_id()){echo 'selected';} ?>>
											<?php echo $member['firstname'] . ' ' . $member['lastname'] ; ?>
											<?php
											if(($member['staffid'] == get_staff_user_id())){
												echo ' - '._l('ticket_settings_assign_to_you');
											} ?>
										</option>
										<?php } ?>
									</select>
								</div>
								<?php if(get_option('services') == 1){ ?>
								<div class="col-md-12 row">
									<?php echo render_select('service',$services,array('serviceid','name'),'ticket_settings_service'); ?>
								</div>
								<?php } ?>
								<div class="col-md-12 row">
									<?php echo render_select('priority',$priorities,array('priorityid','name'),'ticket_settings_priority'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="panel_s">
							<div class="panel-heading">
								<?php echo _l('ticket_add_body'); ?>
							</div>
							<div class="panel-body">
								<?php
								$use_knowledge_base = get_option('use_knowledge_base');
								?>
								<div class="col-md-12 mbot20">
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
										<div class="clearfix"></div>
										<?php $this->load->view('admin/editor/template',array('name'=>'message','contents'=>'')); ?>
									</div>
									<div class="panel-footer attachments_area">
										<div class="row attachments">
											<div class="attachment">
												<div class="col-md-6 col-md-offset-3">
													<div class="form-group">
														<label for="attachment" class="control-label"><?php echo _l('ticket_add_attachments'); ?></label>
														<input type="file" class="form-control" name="attachments[]">
													</div>
												</div>
												<div class="col-md-1">
													<button class="btn btn-success original-button mtop25 add_more_attachments" type="button"><i class="fa fa-plus"></i></button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 text-center">
							<button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
						</div>
					</div>
					<?php echo form_close(); ?>
				</div>
			</div>
			<?php init_tail(); ?>
			<script src="<?php echo site_url(); ?>assets/js/tickets.js"></script>
			<script>
				$(document).ready(function(){
					_validate_form($('form'),{
						"attachments[]" : {
							extension: ticket_attachments_file_extension
						},
						subject:'required',
						userid:'required',
						department:'required'
					});
				});
			</script>
		</body>
		</html>
