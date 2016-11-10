<div class="row">
	<div class="col-md-4 col-md-offset-4 text-center">
		<h1><?php echo _l('clients_register_heading'); ?></h1>
	</div>
</div>
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<?php echo form_open('clients/register'); ?>
		<div class="panel_s">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="firstname"><?php echo _l('clients_firstname'); ?></label>
							<input type="text" class="form-control" name="firstname" id="firstname" value="<?php echo set_value('firstname'); ?>">
							<?php echo form_error('firstname'); ?>
						</div>
						<div class="form-group">
							<label for="lastname"><?php echo _l('clients_lastname'); ?></label>
							<input type="text" class="form-control" name="lastname" id="lastname" value="<?php echo set_value('lastname'); ?>">
							<?php echo form_error('lastname'); ?>
						</div>
						<div class="form-group">
							<label for="email"><?php echo _l('clients_email'); ?></label>
							<input type="text" class="form-control" name="email" id="email" value="<?php echo set_value('email'); ?>">
							<?php echo form_error('email'); ?>
						</div>
						<div class="form-group">
							<label for="password"><?php echo _l('clients_register_password'); ?></label>
							<input type="password" class="form-control" name="password" id="password">
							<?php echo form_error('password'); ?>
						</div>
						<div class="form-group">
							<label for="passwordr"><?php echo _l('clients_register_password_repeat'); ?></label>
							<input type="password" class="form-control" name="passwordr" id="passwordr">
							<?php echo form_error('passwordr'); ?>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="company"><?php echo _l('clients_company'); ?></label>
							<input type="text" class="form-control" name="company" id="company" value="<?php echo set_value('company'); ?>">
						</div>
						<div class="form-group">
							<label for="vat"><?php echo _l('clients_vat'); ?></label>
							<input type="text" class="form-control" name="vat" id="vat" value="<?php echo set_value('vat'); ?>">
						</div>
						<div class="form-group">
							<label for="phonenumber"><?php echo _l('clients_phone'); ?></label>
							<input type="text" class="form-control" name="phonenumber" id="phonenumber" value="<?php echo set_value('phonenumber'); ?>">
						</div>
						<div class="form-group">
							<label for="lastname"><?php echo _l('clients_country'); ?></label>
							<select name="country" class="form-control" id="country">
								<option value=""></option>
								<?php foreach(get_all_countries() as $country){ ?>
								<option value="<?php echo $country['country_id']; ?>" <?php echo set_select('country', $country['country_id']); ?>><?php echo $country['short_name']; ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="form-group">
							<label for="city"><?php echo _l('clients_city'); ?></label>
							<input type="text" class="form-control" name="city" id="city" value="<?php echo set_value('city'); ?>">
						</div>
						<div class="form-group">
							<label for="address"><?php echo _l('clients_address'); ?></label>
							<input type="text" class="form-control" name="address" id="address" value="<?php echo set_value('address'); ?>">
						</div>
						<div class="form-group">
							<label for="zip"><?php echo _l('clients_zip'); ?></label>
							<input type="text" class="form-control" name="zip" id="zip" value="<?php echo set_value('zip'); ?>">
						</div>
						<div class="form-group">
							<label for="state"><?php echo _l('clients_state'); ?></label>
							<input type="text" class="form-control" name="state" id="state" value="<?php echo set_value('state'); ?>">
						</div>
						<?php echo render_custom_fields( 'customers','',array('show_on_client_portal'=>1)); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 text-center">
				<div class="form-group">
					<button type="submit" class="btn btn-primary"><?php echo _l('clients_register_string'); ?></button>
				</div>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>
