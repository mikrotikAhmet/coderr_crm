<div class="col-md-8">
	<?php echo form_open('clients/profile'); ?>
	<?php echo form_hidden('profile',true); ?>
	<div class="panel_s">
		<div class="panel-heading">
			<?php echo _l('clients_profile_heading'); ?>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="firstname"><?php echo _l('clients_firstname'); ?></label>
						<input type="text" class="form-control" name="firstname" id="firstname" value="<?php echo set_value('firstname',$client->firstname); ?>">
						<?php echo form_error('firstname'); ?>
					</div>
					<div class="form-group">
						<label for="lastname"><?php echo _l('clients_lastname'); ?></label>
						<input type="text" class="form-control" name="lastname" id="lastname" value="<?php echo set_value('lastname',$client->lastname); ?>">
						<?php echo form_error('lastname'); ?>
					</div>
					<div class="form-group">
						<label for="email"><?php echo _l('clients_email'); ?></label>
						<input type="text" class="form-control" disabled="true" id="email" value="<?php echo $client->email; ?>">
					</div>
					<div class="form-group">
						<label for="company" class="control-label"><?php echo _l('clients_company'); ?></label>
						<input type="text" class="form-control" name="company" value="<?php if(isset($client)){echo $client->company;} ?>">
					</div>
					<div class="form-group">
						<label for="vat" class="control-label"><?php echo _l('clients_vat'); ?></label>
						<input type="text" class="form-control" name="vat" value="<?php if(isset($client)){echo $client->vat;} ?>">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="phonenumber"><?php echo _l('clients_phone'); ?></label>
						<input type="text" class="form-control" name="phonenumber" id="phonenumber" value="<?php echo $client->phonenumber; ?>">
					</div>
					<div class="form-group">
						<label for="lastname"><?php echo _l('clients_country'); ?></label>
						<select name="country" class="form-control" id="country">
							<option value=""></option>
							<?php foreach(get_all_countries() as $country){ ?>
							<?php
							$selected = '';
								if($client->country == $country['country_id']){echo $selected = true;}
							?>
							<option value="<?php echo $country['country_id']; ?>" <?php echo set_select('country', $country['country_id'],$selected); ?>><?php echo $country['short_name']; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group">
						<label for="city"><?php echo _l('clients_city'); ?></label>
						<input type="text" class="form-control" name="city" id="city" value="<?php echo $client->city; ?>">
					</div>
					<div class="form-group">
						<label for="address"><?php echo _l('clients_address'); ?></label>
						<input type="text" class="form-control" name="address" id="address" value="<?php echo $client->address; ?>">
					</div>
					<div class="form-group">
						<label for="zip"><?php echo _l('clients_zip'); ?></label>
						<input type="text" class="form-control" name="zip" id="zip" value="<?php echo $client->zip; ?>">
					</div>
					<div class="form-group">
						<label for="state"><?php echo _l('clients_state'); ?></label>
						<input type="text" class="form-control" name="state" id="state" value="<?php echo $client->state; ?>">
					</div>
					<?php echo render_custom_fields( 'customers',$client->userid,array('show_on_client_portal'=>1)); ?>
				</div>
				<div class="row p15">
					<div class="col-md-12 text-right mtop20">
						<div class="form-group">
							<button type="submit" class="btn btn-primary"><?php echo _l('clients_edit_profile_update_btn'); ?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php echo form_close(); ?>
</div>
<div class="col-md-4">
	<div class="panel_s">
		<div class="panel-heading">
			<?php echo _l('clients_edit_profile_change_password_heading'); ?>
		</div>
		<div class="panel-body">
			<?php echo form_open('clients/profile'); ?>
			<?php echo form_hidden('change_password',true); ?>
			<div class="form-group">
				<label for="oldpassword"><?php echo _l('clients_edit_profile_old_password'); ?></label>
				<input type="password" class="form-control" name="oldpassword" id="oldpassword">
				<?php echo form_error('oldpassword'); ?>
			</div>
			<div class="form-group">
				<label for="newpassword"><?php echo _l('clients_edit_profile_new_password'); ?></label>
				<input type="password" class="form-control" name="newpassword" id="newpassword">
				<?php echo form_error('newpassword'); ?>
			</div>
			<div class="form-group">
				<label for="newpasswordr"><?php echo _l('clients_edit_profile_new_password_repeat'); ?></label>
				<input type="password" class="form-control" name="newpasswordr" id="newpasswordr">
				<?php echo form_error('newpasswordr'); ?>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-block"><?php echo _l('clients_edit_profile_change_password_btn'); ?></button>
			</div>
			<?php echo form_close(); ?>
		</div>
		<?php if($client->last_password_change !== NULL){ ?>
		<div class="panel-footer">
			<?php echo _l('clients_profile_last_changed_password',time_ago($client->last_password_change)); ?>
		</div>
		<?php } ?>
	</div>
</div>
