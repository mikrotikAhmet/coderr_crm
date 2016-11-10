<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo $title; ?>
					</div>
					<div class="panel-body">
						<?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'staff_profile_table')); ?>
						<?php if($_staff->profile_image == NULL){ ?>
						<div class="form-group">
							<label for="profile_image" class="profile-image"><?php echo _l('staff_edit_profile_image'); ?></label>
							<input type="file" name="profile_image" class="form-control" id="profile_image">
						</div>
						<?php } ?>
						<?php if($_staff->profile_image != NULL){ ?>
						<div class="form-group">
							<div class="row">
								<div class="col-md-9">
									<?php echo staff_profile_image($_staff->staffid,array('img','img-responsive','staff-profile-image-thumb'),'thumb'); ?>
								</div>
								<div class="col-md-3 text-right">
									<a href="<?php echo admin_url('staff/remove_staff_profile_image'); ?>"><i class="fa fa-remove"></i></a>
								</div>
							</div>
						</div>
						<?php } ?>
						<div class="form-group">
							<label for="firstname" class="control-label"><?php echo _l('staff_add_edit_firstname'); ?></label>
							<input type="text" class="form-control" name="firstname" value="<?php if(isset($member)){echo $member->firstname;} ?>">
						</div>
						<div class="form-group">
							<label for="lastname" class="control-label"><?php echo _l('staff_add_edit_lastname'); ?></label>
							<input type="text" class="form-control" name="lastname" value="<?php if(isset($member)){echo $member->lastname;} ?>">
						</div>
						<div class="form-group">
							<label for="email" class="control-label"><?php echo _l('staff_add_edit_email'); ?></label>
							<input type="email" class="form-control" disabled="true" value="<?php echo $member->email; ?>">
						</div>
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
								<label for="departments"><?php echo _l('staff_edit_profile_your_departments'); ?></label>
								<div class="clearfix"></div>
								<?php
								foreach($departments as $department){ ?>
								<?php
								foreach ($staff_departments as $staff_department) {
									if($staff_department['departmentid'] == $department['departmentid']){ ?>
									<div class="chip-circle mtop20"><?php echo $staff_department['name']; ?></div>
									<?php }
								}

								?>
								<?php } ?>
							</div>

							<button type="submit" class="btn btn-primary pull-right"><?php echo _l('submit'); ?></button>
							<?php echo form_close(); ?>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="panel_s">
						<div class="panel-heading">
							<?php echo _l('staff_edit_profile_change_your_password'); ?>
						</div>
						<div class="panel-body">
							<?php echo form_open('admin/staff/change_password_profile',array('id'=>'staff_password_change_form')); ?>
							<div class="form-group">
								<label for="oldpassword" class="control-label"><?php echo _l('staff_edit_profile_change_old_password'); ?></label>
								<input type="password" class="form-control" name="oldpassword" id="oldpassword">
							</div>
							<div class="form-group">
								<label for="newpassword" class="control-label"><?php echo _l('staff_edit_profile_change_new_password'); ?></label>
								<input type="password" class="form-control" id="newpassword" name="newpassword">
							</div>
							<div class="form-group">
								<label for="newpasswordr" class="control-label"><?php echo _l('staff_edit_profile_change_repet_new_password'); ?></label>
								<input type="password" class="form-control" id="newpasswordr" name="newpasswordr">
							</div>
							<button type="submit" class="btn btn-primary pull-right"><?php echo _l('submit'); ?></button>
							<?php echo form_close(); ?>
						</div>
						<?php if($member->last_password_change != NULL){ ?>
						<div class="panel-footer">
							<?php echo _l('staff_add_edit_password_last_changed'); ?>: <?php echo time_ago($member->last_password_change); ?>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php init_tail(); ?>
	<script>
		_validate_form($('#staff_profile_table'),{firstname:'required',lastname:'required'});
		_validate_form($('#staff_password_change_form'),{oldpassword:'required',newpassword:'required',newpasswordr: { equalTo: "#newpassword"}});

	</script>
</body>
</html>
