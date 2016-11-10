<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<?php if(isset($member)){ ?>
			<div class="member">
				<?php echo form_hidden('isedit'); ?>
				<?php echo form_hidden('memberid',$member->staffid); ?>
			</div>
			<?php } ?>
			<?php echo form_open($this->uri->uri_string(),array('class'=>'staff-form')); ?>
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo $title; ?>
					</div>
					<div class="panel-body">
						<?php if(isset($member)){ ?>
						<a href="<?php echo admin_url('staff/member'); ?>" class="btn btn-success pull-right mbot20 display-block"><?php echo _l('new_staff'); ?></a>
						<?php } ?>
						<div class="clearfix"></div>
						<?php $value = (isset($member) ? $member->firstname : ''); ?>
						<?php echo render_input('firstname','staff_add_edit_firstname',$value); ?>

						<?php $value = (isset($member) ? $member->lastname : ''); ?>
						<?php echo render_input('lastname','staff_add_edit_lastname',$value); ?>

						<?php $value = (isset($member) ? $member->email : ''); ?>
						<?php echo render_input('email','staff_add_edit_email',$value,'email'); ?>

						<?php $value = (isset($member) ? $member->phonenumber : ''); ?>
						<?php echo render_input('phonenumber','staff_add_edit_phonenumber',$value); ?>
						<div class="form-group">
							<label for="facebook" class="control-label"><i class="fa fa-facebook"></i> <?php echo _l('staff_add_edit_facebook'); ?></label>
							<input type="text" class="form-control" name="facebook" value="<?php if(isset($member)){echo $member->facebook;} ?>">
						</div>
						<div class="form-group">
							<label for="linkedin" class="control-label"><i class="fa fa-linkedin"></i> <?php echo _l('staff_add_edit_linkedin'); ?></label>
							<input type="text" class="form-control" name="linkedin" value="<?php if(isset($member)){echo $member->linkedin;} ?>">
						</div>
						<div class="form-group">
							<label for="skype" class="control-label"><i class="fa fa-skype"></i> <?php echo _l('staff_add_edit_skype'); ?></label>
							<input type="text" class="form-control" name="skype" value="<?php if(isset($member)){echo $member->skype;} ?>">
						</div>
						<div class="form-group">
							<label for="departments"><?php echo _l('staff_add_edit_departments'); ?></label>
							<?php foreach($departments as $department){ ?>
							<div class="checkbox checkbox-primary">
								<?php
								$checked = '';
								if(isset($member)){
									foreach ($staff_departments as $staff_department) {
										if($staff_department['departmentid'] == $department['departmentid']){
											$checked = ' checked';
										}
									}
								}
								?>
								<input type="checkbox" name="departments[]" value="<?php echo $department['departmentid']; ?>"<?php echo $checked; ?>>
								<label><?php echo $department['name']; ?></label>
							</div>
							<?php } ?>
						</div>
						<?php $rel_id = (isset($member) ? $member->staffid : false); ?>
						<?php echo render_custom_fields('staff',$rel_id); ?>

						<?php
						$selected = '';
						foreach($roles as $role){
							if(isset($member)){
								if($member->role == $role['roleid']){
									$selected = $role['roleid'];
								}
							} else {
								$default_staff_role = get_option('default_staff_role');
								if($default_staff_role == $role['roleid'] ){
									$selected = $role['roleid'];
								}
							}
						}
						?>
						<?php echo render_select('role',$roles,array('roleid','name'),'staff_add_edit_role',$selected); ?>

						<div class="form-group" id="staff_permissions">
							<label for="permissions"><?php echo _l('staff_add_edit_permissions'); ?></label>
							<div class="clearfix">	</div>
							<?php foreach($permissions as $permission){ ?>
							<?php
							$checked = '';
							$disabled = '';
							if(isset($member)){
								if(!is_admin($member->staffid)){
									foreach ($staff_permissions as $staff_permission) {
										if($staff_permission['permissionid'] == $permission['permissionid']){
											$checked = ' checked';
										}
									}
								} else {
									$checked = ' checked';
									$disabled = ' disabled="disabled"';
								}
							}
							?>
							<div class="col-md-6">
								<div class="row">
									<div class="col-md-6 mtop10">
										<span class="bold"><?php echo $permission['name']; ?></span>
									</div>
									<div class="col-md-6 mtop10">
										<input type="checkbox" class="switch-box role" <?php echo $disabled; ?> value="<?php echo $permission['permissionid']; ?>" data-size="mini" name="permissions[]" <?php echo $checked; ?>>
									</div>
								</div>
							</div>
							<?php } ?>
							<?php if (is_admin()){ ?>
							<div class="row">

								<div class="col-md-12">
									<hr />
									<div class="checkbox checkbox-primary">
										<?php
										$isadmin = '';
										if(isset($member)) {
											if($member->staffid == get_staff_user_id() || is_admin($member->staffid)){
												$isadmin = ' checked';
											}
										}
										?>
										<input type="checkbox" name="administrator" <?php echo $isadmin; ?>>
										<label><?php echo _l('staff_add_edit_administrator'); ?></label>
									</div>
								</div>
							</div>
							<?php } ?>
						</div>
	<div class="clearfix form-group"></div>
						<label for="password" class="control-label"><?php echo _l('staff_add_edit_password'); ?></label>
						<div class="input-group">
							<input type="text" class="form-control password" name="password">
							<span class="input-group-addon">
								<a href="#" class="generate_password" onclick="generatePassword(this);return false;"><i class="fa fa-refresh"></i></a>
							</span>
						</div>
						<?php if(isset($member) && $member->last_password_change != NULL){ ?>
						<p class="text-muted"><?php echo _l('staff_add_edit_password_note'); ?></p>
						<?php echo _l('staff_add_edit_password_last_changed'); ?>: <?php echo time_ago($member->last_password_change); ?>
						<?php } ?>
						<button type="submit" class="btn btn-primary pull-right mtop20"><?php echo _l('submit'); ?></button>

					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo _l('advanced_options'); ?>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?></label>
							<select name="default_language" id="default_language" class="form-control selectpicker">
								<option value=""><?php echo _l('system_default_string'); ?></option>
								<?php foreach(list_folders(APPPATH .'language') as $language){
									$selected = '';
									if(isset($member)){
										if($member->default_language == $language){
											$selected = 'selected';
										}
									}
									?>
									<option value="<?php echo $language; ?>" <?php echo $selected; ?>><?php echo ucfirst($language); ?></option>
									<?php } ?>
								</select>
							</div>

						</div>
					</div>
				</div>
				<?php echo form_close(); ?>
				<?php if(isset($member)){ ?>
				<div class="col-md-6">
					<div class="panel_s">
						<div class="panel-heading">
							<?php echo _l('staff_add_edit_notes'); ?>
							<a href="#" class="btn btn-success btn-icon pull-right" onclick="slideToggle('.usernote'); return false;"><i class="fa fa-plus"></i></a>
						</div>
						<div class="panel-body">
							<div class="row usernote hide">
								<div class="col-md-12">
									<?php echo form_open(admin_url('misc/add_user_note/'.$member->staffid . '/1')); ?>
									<?php echo render_textarea('description','staff_add_edit_note_description','',array('rows'=>5)); ?>
									<button class="btn btn-primary pull-right"><?php echo _l('submit'); ?></button>
									<?php echo form_close(); ?>
								</div>
							</div>
							<?php if(count($user_notes) > 0){ ?>
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th><?php echo _l('staff_notes_table_description_heading'); ?></th>
											<th><?php echo _l('staff_notes_table_addedfrom_heading'); ?></th>
											<th><?php echo _l('staff_notes_table_dateadded_heading'); ?></th>
											<th><?php echo _l('options'); ?></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($user_notes as $note){ ?>
										<tr>
											<td><?php echo $note['description']; ?></td>
											<td><?php echo $note['firstname'] . ' ' . $note['lastname']; ?></td>
											<td><?php echo _dt($note['dateadded']); ?></td>
											<td><a href="<?php echo admin_url('misc/remove_user_note/'.$note['usernoteid'] . '/' .$member->staffid .'/1'); ?>" class="btn btn-danger btn-icon"><i class="fa fa-remove"></i></a></td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
							<?php } else { ?>
							<p class="no-margin"><?php echo _l('staff_no_user_notes_found'); ?></p>
							<?php } ?>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php init_tail(); ?>
		<script>
			init_roles_permissions();
			_validate_form($('.staff-form'),{
				firstname:'required',
				lastname:'required',
				username:'required',
				password: {
					required: {
						depends: function(element){
							return ($('input[name="isedit"]').length == 0) ? true : false
						}
					}
				},
				email: {
					required:true,
					email:true,
					remote:{
						url: site_url + "admin/misc/staff_email_exists",
						type:'post',
						data: {
							email:function(){
								return $('input[name="email"]').val();
							},
							memberid:function(){
								return $('input[name="memberid"]').val();
							}
						}

					}
				}
			});
		</script>
	</body>
	</html>
