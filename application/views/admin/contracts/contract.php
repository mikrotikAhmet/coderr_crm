<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-5">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo $title; ?>
					</div>
					<div class="panel-body">
						<?php if(isset($contract)) { ?>
						<a href="#" class="btn btn-default" data-toggle="modal" data-target="#renew_contract_modal">
							<i class="fa fa-refresh"></i>
						</a>
						<hr />
						<div class="clearfix"></div>
						<?php } ?>
						<?php echo form_open($this->uri->uri_string(),array('id'=>'contract-form')); ?>
						<div class="form-group">
							<div class="checkbox checkbox-primary" data-toggle="tooltip" title="<?php echo _l('contract_trash_tooltip'); ?>">
								<input type="checkbox" name="trash" <?php if(isset($contract)){if($contract->trash == 1){echo 'checked';}}; ?>>
								<label for=""><?php echo _l('contract_trash'); ?></label>
							</div>
						</div>

						<div class="form-group">
							<div class="checkbox checkbox-primary">
								<input type="checkbox" name="not_visible_to_client" <?php if(isset($contract)){if($contract->not_visible_to_client == 1){echo 'checked';}}; ?>>
								<label for=""><?php echo _l('contract_not_visible_to_client'); ?></label>
							</div>
						</div>

						<?php $value = (isset($contract) ? $contract->subject : ''); ?>
						<?php echo render_input('subject','contract_subject',$value,'text',array('data-toggle'=>'tooltip','title'=>'contract_subject_tooltip')); ?>
						<div class="row">
							<div class="col-md-6">
								<?php $value = (isset($contract) ? $contract->datestart : _d(date('Y-m-d'))); ?>
								<?php echo render_date_input('datestart','contract_start_date',$value); ?>
							</div>
							<div class="col-md-6">
								<?php $value = (isset($contract) ? $contract->dateend : ''); ?>
								<?php echo render_date_input('dateend','contract_end_date',$value); ?>
							</div>
						</div>
						<?php $selected = (isset($contract) ? $contract->contract_type : ''); ?>
						<?php echo render_select('contract_type',$types,array('id','name'),'contract_type',$selected); ?>

						<?php $selected = (isset($contract) ? $contract->client : ''); ?>
						<?php echo render_select('client',$clients,array('userid',array('firstname','lastname'),'company'),'contract_client_string',$selected); ?>

						<div class="form-group">
							<label for="contract_value"><?php echo _l('contract_value'); ?></label>
							<div class="input-group" data-toggle="tooltip" title="<?php echo _l('contract_value_tooltip'); ?>">
								<input type="number" class="form-control" name="contract_value" value="<?php if(isset($contract)){echo $contract->contract_value; }?>">
								<div class="input-group-addon">
									<?php echo $base_currency->symbol; ?>
								</div>
							</div>
						</div>
						<?php $value = (isset($contract) ? $contract->description : ''); ?>
						<?php echo render_textarea('description','contract_description',$value,array('rows'=>10)); ?>
						<?php $rel_id = (isset($contract) ? $contract->id : false); ?>
						<?php echo render_custom_fields('contracts',$rel_id); ?>
						<button type="submit" class="btn btn-primary pull-right"><?php echo _l('submit'); ?></button>
						<?php echo form_close(); ?>
					</div>
				</div>
			</div>
			<?php if(isset($contract)) { ?>
			<div class="col-md-7">
				<div class="panel_s">
					<div class="panel-heading"><?php echo _l('contract_edit_overview'); ?></div>
					<div class="panel-body">
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active">
								<a href="#tab_attachments" aria-controls="tab_attachments" role="tab" data-toggle="tab">
									<?php echo _l('contract_attachments'); ?>
								</a>
							</li>
							<li role="presentation">
								<a href="#tab_renewals" aria-controls="tab_renewals" role="tab" data-toggle="tab">
									<?php echo _l('no_contract_renewals_history_heading'); ?>
								</a>
							</li>
							<li role="presentation">
								<a href="#tab_tasks" aria-controls="tab_tasks" role="tab" data-toggle="tab">
									<?php echo _l('tasks'); ?>
								</a>
							</li>
						</ul>
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane ptop10 active" id="tab_attachments">
								<?php echo form_open(admin_url('contracts/add_contract_attachment/'.$contract->id),array('id'=>'contract-attachments-form','class'=>'dropzone')); ?>
								<?php echo form_close(); ?>
								<div id="contract_attachments" class="mtop30"></div>
							</div>
							<div role="tabpanel" class="tab-pane ptop10" id="tab_renewals">
								<?php
								if(count($contract_renewal_history) == 0){
									echo _l('no_contract_renewals_found');
								}
								foreach($contract_renewal_history as $renewal){ ?>
								<div class="display-block">
									<a href="<?php echo admin_url('profile/'.$renewal['renewed_by']); ?>">
										<?php echo staff_profile_image($renewal['renewed_by'],array('staff-profile-image-small','pull-left mright10')); ?>
									</a>
									<div class="media-body">
										<div class="display-block">
											<?php
											echo
											_l('contract_renewed_by',"<a href=".admin_url('profile/'.$renewal['renewed_by']).">".$renewal['firstname'] . ' ' . $renewal['lastname']."</a>");
											?>
											<a href="<?php echo admin_url('contracts/delete_renewal/'.$renewal['id'] . '/'.$renewal['contractid']); ?>" class="pull-right"><i class="fa fa-remove"></i></a>
											<br />
											<small class="text-muted"><?php echo _dt($renewal['date_renewed']); ?></small>
											<hr />
											<span class="text-success bold" data-toggle="tooltip" title="<?php echo _l('contract_renewal_old_start_date',_d($renewal['old_start_date'])); ?>">
												<?php echo _l('contract_renewal_new_start_date',_d($renewal['new_start_date'])); ?>
											</span>
											<br />
											<?php if(is_date($renewal['new_end_date'])){
												$tooltip = '';
												if(is_date($renewal['old_end_date'])){
													$tooltip = _l('contract_renewal_old_end_date',_d($renewal['old_end_date']));
												}
												?>
												<span class="text-success bold" data-toggle="tooltip" title="<?php echo $tooltip; ?>">
													<?php echo _l('contract_renewal_new_end_date',_d($renewal['new_end_date'])); ?>
												</span>
												<br/>
												<?php } ?>
												<?php if($renewal['new_value'] > 0){
													$contract_renewal_value_tooltip = '';
													if($renewal['old_value'] > 0){
														$contract_renewal_value_tooltip = ' data-toggle="tooltip" data-title="'._l('contract_renewal_old_value',_format_number($renewal['old_value'])).'"';
													} ?>
													<span class="text-success bold"<?php echo $contract_renewal_value_tooltip; ?>>
														<?php echo _l('contract_renewal_new_value',_format_number($renewal['new_value'])); ?>
													</span>
													<br />
													<?php } ?>
												</div>
											</div>
											<hr />
										</div>
										<?php } ?>
									</div>
									<div role="tabpanel" class="tab-pane ptop10" id="tab_attachments">
										<?php echo form_open(admin_url('contracts/add_contract_attachment/'.$contract->id),array('id'=>'contract-attachments-form','class'=>'dropzone')); ?>
										<?php echo form_close(); ?>
										<div id="contract_attachments" class="mtop30"></div>
									</div>
									<div role="tabpanel" class="tab-pane ptop10" id="tab_tasks">
										<?php init_relation_tasks_table(array('data-new-rel-id'=>$contract->id,'data-new-rel-type'=>'contract')); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php init_tail(); ?>
		<?php if(isset($contract)){ ?>
		<!-- init table tasks -->
		<script>
			initDataTable('.table-rel-tasks', admin_url +'tasks/init_relation_tasks/<?php echo $contract->id; ?>/contract', 'tasks');
		</script>
		<div class="modal animated fadeIn" id="renew_contract_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<?php echo form_open(admin_url('contracts/renew'),array('id'=>'renew-contract-form')); ?>
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">
							<?php echo _l('contract_renew_heading'); ?>
						</h4>
					</div>
					<div class="modal-body">
						<?php
						$new_end_date_assume = '';
						if(!empty($contract->dateend)){
							$dStart                      = new DateTime($contract->datestart);
							$dEnd                        = new DateTime($contract->dateend);
							$dDiff                       = $dStart->diff($dEnd);
							$new_end_date_assume = _d(date('Y-m-d', strtotime(date('Y-m-d', strtotime('+' . $dDiff->days . 'DAY')))));
						}
						?>
						<?php echo render_date_input('new_start_date','contract_start_date',_d(date('Y-m-d'))); ?>
						<?php echo render_date_input('new_end_date','contract_end_date',$new_end_date_assume); ?>
						<?php echo render_input('new_value','contract_value',$contract->contract_value,'number'); ?>
						<?php echo form_hidden('contractid',$contract->id); ?>
						<?php echo form_hidden('old_start_date',$contract->datestart); ?>
						<?php echo form_hidden('old_end_date',$contract->dateend); ?>
						<?php echo form_hidden('old_value',$contract->contract_value); ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
						<button type="submit" class="btn btn-primary" onclick="update_lead_status_canban(); return false;"><?php echo _l('submit'); ?></button>
					</div>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
		<?php } ?>
		<script>
			Dropzone.options.contractAttachmentsForm = {
				addRemoveLinks: false,
				complete:function(file){
					if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
						get_contract_attachments();
					}
				}
			}
			$(document).ready(function(){
				get_contract_attachments();
				_validate_form($('#contract-form'),{client:'required',datestart:'required',subject:'required'});
				_validate_form($('#renew-contract-form'),{new_start_date:'required'});
			});

			function get_contract_attachments(){
				var contractid = $('input[name="contractid"]').val();
				if(typeof(contractid) == 'undefined' || contractid == ''){
					return;
				}
				$.get(admin_url + 'contracts/get_contract_attachments/'+contractid,function(response){
					$('#contract_attachments').html(response);
				});
			}
			function delete_contract_attachment(wrapper,id){
				$.get(admin_url + 'contracts/delete_contract_attachment/'+id,function(response){
					if(response.success == true){
						$(wrapper).parents('.contract-attachment-wrapper').remove();
					}
				},'json');
			}
		</script>
	</body>
	</html>
