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
						<?php if(isset($role)){ ?>
						<a href="<?php echo admin_url('roles/role'); ?>" class="btn btn-success pull-right mbot20 display-block"><?php echo _l('new_role'); ?></a>
						<div class="clearfix"></div>
						<?php } ?>
						<?php echo form_open($this->uri->uri_string()); ?>

						<?php $value = (isset($role) ? $role->name : ''); ?>
						<?php echo render_input('name','role_add_edit_name',$value); ?>

						<?php foreach($permissions as $permission){
							$checked = '';
							if(isset($role)){
								foreach($role_permissions as $role_permission){
									if($role_permission['permissionid'] == $permission['permissionid']){
										$checked = 'checked';
									}
								}
							}
							?>
							<div class="row">
								<div class="col-md-4 mtop10">
									<span class="bold"><?php echo $permission['name']; ?></span>
								</div>
								<div class="col-md-8 mtop10">
									<input type="checkbox" class="switch-box" value="<?php echo $permission['permissionid']; ?>" data-size="mini" name="permissions[]" <?php echo $checked; ?>>
								</div>
							</div>
							<?php } ?>
							<button type="submit" class="btn btn-primary pull-right"><?php echo _l('submit'); ?></button>
							<?php echo form_close(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php init_tail(); ?>
	<script>
		_validate_form($('form'),{name:'required'});
	</script>
</body>
</html>
