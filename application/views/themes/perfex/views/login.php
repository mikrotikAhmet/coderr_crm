<div class="mtop40">
	<div class="row">
		<div class="col-md-4 col-md-offset-4 text-center">
			<h1>
				<?php if(get_option('allow_registration') == 1){
					echo _l('clients_login_heading_register');
				} else {
					echo _l('clients_login_heading_no_register');
				}
				?>
			</h1>
		</div>
	</div>
	<div class="col-md-4 col-md-offset-4">
		<?php echo form_open('authentication/client',array('class'=>'bgwhite p15 login-form')); ?>
		<div class="form-group">
			<label for="email"><?php echo _l('clients_login_email'); ?></label>
			<input type="text" class="form-control" name="email" id="email">
		</div>
		<div class="form-group">
			<label for="password"><?php echo _l('clients_login_password'); ?></label>
			<input type="password" class="form-control" name="password" id="password">
		</div>
		<div class="form-group">
			<input type="checkbox" name="remember"> <?php echo _l('clients_login_remember'); ?>
		</div>
		<div class="form-group">
			<button type="submit" class="btn btn-primary btn-block"><?php echo _l('clients_login_login_string'); ?></button>
			<?php if(get_option('allow_registration') == 1) { ?>
			<a href="<?php echo site_url('clients/register'); ?>" class="btn btn-default btn-block"><?php echo _l('clients_register_string'); ?>
			</a>
			<?php } ?>
		</div>
		<a href="<?php echo site_url('clients/forgot_password'); ?>"><?php echo _l('customer_forgot_password'); ?></a>
		<?php echo form_close(); ?>

	</div>
</div>
