<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-5">
				<div class="row">
					<div class="col-md-12">
						<div class="panel_s">
							<div class="panel-heading">
								<?php echo $title; ?>
							</div>
							<?php echo form_open($this->uri->uri_string(),array('id'=>'survey_form')); ?>
							<div class="panel-body">
								<?php $value = (isset($survey) ? $survey->subject : ''); ?>
								<?php echo render_input('subject','survey_add_edit_subject',$value); ?>

								<p class="bold"><?php echo _l('survey_add_edit_email_description'); ?></p>
								<?php $contents = ''; if(isset($survey)){$contents = $survey->description;} ?>
								<?php $this->load->view('admin/editor/template',array('name'=>'description','contents'=>$contents)); ?>
								<p class="bold">
									<?php echo _l('survey_include_survey_link'); ?> : <span class="text-info">{survey_link}</span>
								</p>

								<?php if($found_custom_fields){ ?>
								<hr />
								<p class="bold tooltip-pointer" data-toggle="tooltip" title="<?php echo _l('survey_mail_lists_custom_fields_tooltip'); ?>"><?php echo _l('survey_available_mail_lists_custom_fields'); ?></p>
								<?php } ?>
								<?php
								foreach($mail_lists as $list){
									if(count($list['customfields']) == 0){
										continue;
									}
									?>
									<div class="btn-group">
										<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<?php echo $list['name']; ?> <span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											<?php foreach($list['customfields'] as $custom_field){ ?>
											<li><a href="#" class="add_email_list_custom_field_to_survey" data-toggle="tooltip" title="{<?php echo $custom_field['fieldslug']; ?>}" data-slug="{<?php echo $custom_field['fieldslug']; ?>}"><?php echo $custom_field['fieldname']; ?></a></li>
											<?php } ?>
										</ul>
									</div>
									<?php } ?>
									<hr />
									<div class="clearfix"></div>

									<?php $value = (isset($survey) ? $survey->viewdescription : ''); ?>
									<?php echo render_textarea('viewdescription','survey_add_edit_short_description_view',$value); ?>

									<?php $value = (isset($survey) ? $survey->fromname : ''); ?>
									<?php echo render_input('fromname','survey_add_edit_from',$value); ?>

									<?php $value = (isset($survey) ? $survey->redirect_url : ''); ?>
									<?php echo render_input('redirect_url','survey_add_edit_redirect_url',$value,'text',array('data-toggle'=>'tooltip','title'=>'survey_add_edit_red_url_note')); ?>
									<div class="checkbox checkbox-primary">
										<input type="checkbox" name="disabled" <?php if(isset($survey) && $survey->active == 0){echo 'checked';} ?>>
										<label><?php echo _l('survey_add_edit_disabled'); ?></label>
									</div>
									<div class="checkbox checkbox-primary">
										<input type="checkbox" name="onlyforloggedin" <?php if(isset($survey) && $survey->onlyforloggedin == 1){echo 'checked';} ?>>
										<label><?php echo _l('survey_add_edit_only_for_logged_in'); ?></label>
									</div>
									<button type="submit" class="btn btn-primary pull-right"><?php echo _l('submit'); ?></button>
								</div>
								<?php echo form_close(); ?>
							</div>
						</div>
						<?php if(isset($survey)){ ?>
						<div class="col-md-12">
							<div class="panel_s">
								<div class="panel-heading">
									<?php echo _l('send_survey_string'); ?>
								</div>
								<div class="panel-body">
									<?php echo form_open('admin/surveys/send/'.$survey->surveyid); ?>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<div class="checkbox checkbox-primary">
													<input type="checkbox" name="send_survey_to[clients]">
													<label><?php echo _l('survey_send_mail_list_clients'); ?></label>
												</div>
												<div class="checkbox checkbox-primary">
													<input type="checkbox" name="send_survey_to[staff]">
													<label><?php echo _l('survey_send_mail_list_staff'); ?></label>
												</div>
												<p class="bold"><?php echo _l('survey_send_mail_lists_string'); ?></p>
												<p class="no-margin text-muted"><?php echo _l('survey_send_mail_lists_note_logged_in'); ?></p>
												<?php foreach($mail_lists as $list){ ?>
												<div class="checkbox checkbox-primary">
													<input type="checkbox" name="send_survey_to[<?php echo $list['listid']; ?>]">
													<label><?php echo $list['name']; ?></label>
												</div>
												<?php } ?>
											</div>
										</div>
										<div class="col-md-4 text-right">
											<button type="submit" class="btn btn-primary"><?php echo _l('survey_send_string'); ?></button>
										</div>
										<div class="col-md-12">
											<?php foreach($send_log as $log){ ?>
											<p class="text-success">
												<?php echo _l('survey_added_to_queue',_dt($log['date'])); ?>
												( <?php echo ($log['iscronfinished'] == 0 ? _l('survey_send_till_now'). ' ' : '') ?>
												<?php echo _l('survey_send_to_total',$log['total']); ?> )
												<br />
												<b class="bold">
													<?php echo _l('survey_send_finished',($log['iscronfinished'] == 1 ? 'Yes' : 'No')); ?>
												</b>
											</p>
											<?php if(!empty($log['send_to_mail_lists'])){ ?>
											<p>
												Survey send lists: <?php
												$send_lists = unserialize($log['send_to_mail_lists']);
												foreach($send_lists as $send_list){
													$last = end($send_lists);
													echo $send_list . ($last == $send_list ? '':',');
												}
												?>
											</p>
											<?php } ?>
											<hr />

											<?php } ?>
										</div>
									</div>
									<?php echo form_close(); ?>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
				<div class="col-md-7">
					<div class="panel_s">
						<div class="panel-heading">
							<?php echo _l('survey_questions_string'); ?>
						</div>
						<div class="panel-body">
							<?php if(isset($survey)){ ?>
							<!-- Single button -->
							<a href="<?php echo site_url('survey/'.$survey->surveyid . '/' . $survey->hash); ?>" target="_blank" class="btn btn-success pull-right mleft10"><i class="fa fa-eye"></i></a>
							<div class="btn-group pull-right">
								<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<?php echo _l('survey_insert_field'); ?> <span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									<li>
										<a href="#" onclick="add_survey_question('checkbox',<?php echo $survey->surveyid; ?>);return false;">
											<?php echo _l('survey_field_checkbox'); ?></a>
										</li>
										<li>
											<a href="#" onclick="add_survey_question('radio',<?php echo $survey->surveyid; ?>);return false;">
												<?php echo _l('survey_field_radio'); ?></a>
											</li>
											<li>
												<a href="#" onclick="add_survey_question('input',<?php echo $survey->surveyid; ?>);return false;">
													<?php echo _l('survey_field_input'); ?></a>
												</li>
												<li>
													<a href="#" onclick="add_survey_question('textarea',<?php echo $survey->surveyid; ?>);return false;">
														<?php echo _l('survey_field_textarea'); ?></a>
													</li>
												</ul>
											</div>
											<div class="clearfix"></div>
											<hr />
											<?php
											$question_area = '<ul class="list-unstyled survey_question_callback" id="survey_questions">';
											if(count($survey->questions) > 0){
												foreach($survey->questions as $question){
													$question_area .= '<li>';
													$question_area .= '<div class="form-group question">';
													$question_area .= '<div class="checkbox checkbox-primary required">';
													if($question['required'] == 1){
														$_required = ' checked';
													} else {
														$_required = '';
													}
													$question_area .= '<input type="checkbox" onchange="update_question(this,\''.$question['boxtype'].'\','.$question['questionid'].');" data-question_required="'.$question['questionid'].'" name="required[]" '.$_required.'>';
													$question_area .= '<label>'._l('survey_question_required').'</label>';
													$question_area .= '</div>';
													$question_area .= '<input type="hidden" value="" name="order[]">';
										// used only to identify input key no saved in database
													$question_area .='<label for="'.$question['questionid'].'" class="control-label display-block">Question
													<a href="#" onclick="update_question(this,\''.$question['boxtype'].'\','.$question['questionid'].'); return false;" class="pull-right"><i class="fa fa-refresh text-success question_update"></i></a>
													<a href="#" onclick="remove_question_from_database(this,'.$question['questionid'].'); return false;" class="pull-right"><i class="fa fa-minus text-danger"></i></a>
												</label>';
												$question_area .= '<input type="text" onblur="update_question(this,\''.$question['boxtype'].'\','.$question['questionid'].');" data-questionid="'.$question['questionid'].'" class="form-control questionid" value="'.$question['question'].'">';
												if($question['boxtype'] == 'textarea'){
													$question_area .= '<textarea class="form-control mtop20" disabled="disabled" rows="6">'._l('survey_question_only_for_preview').'</textarea>';
												} else if($question['boxtype'] == 'checkbox' || $question['boxtype'] == 'radio'){
													$question_area .= '<div class="row">';
													$x = 0;
													foreach($question['box_descriptions'] as $box_description){
														$box_description_icon_class = 'fa-minus text-danger';
														$box_description_function = 'remove_box_description_from_database(this,'.$box_description['questionboxdescriptionid'].'); return false;';
														if($x == 0){
															$box_description_icon_class = 'fa-plus';
															$box_description_function = 'add_box_description_to_database(this,'.$question['questionid'].','.$question['boxid'].'); return false;';
														}
														$question_area .= '<div class="box_area">';
														$question_area .= '<div class="col-md-1 survey_add_more_box">';
														$question_area .= '<a href="#" class="add_remove_action" onclick="'.$box_description_function.'"><i class="fa '.$box_description_icon_class.'"></i></a>';
														$question_area .= '</div>';
														$question_area .= '<div class="col-md-11">';
														$question_area .= '<div class="'.$question['boxtype'].' '.$question['boxtype'].'-primary">';
														$question_area .= '<input type="'.$question['boxtype'].'" disabled="disabled"/>';
														$question_area .= '
														<label>
															<input type="text" onblur="update_question(this,\''.$question['boxtype'].'\','.$question['questionid'].');" data-box-descriptionid="'.$box_description['questionboxdescriptionid'].'" value="'.$box_description['description'].'" class="survey_input_box_description">
														</label>';
														$question_area .= '</div>';
														$question_area .= '</div>';
														$question_area .= '</div>';
														$x++;
													}
											// end box row
													$question_area .= '</div>';
												} else {
													$question_area .= '<input type="text" class="form-control mtop20" disabled="disabled" value="'._l('survey_question_only_for_preview').'">';
												}
												$question_area .= '</div>';
												$question_area .= '</li>';
											}
										}
										$question_area .= '</ul>';
										echo $question_area;
										?>
										<?php } else { ?>
										<p class="no-margin"><?php echo _l('survey_create_first'); ?></p>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php init_tail(); ?>
				<script src="<?php echo site_url(); ?>assets/js/surveys.js"></script>
			</body>
			</html>
