<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo $title; ?>
					</div>
					<div class="panel-body animated fadeIn">
						<a href="<?php echo admin_url('utilities/make_backup_db'); ?>" class="btn btn-default pull-right">
							<?php echo _l('utility_create_new_backup_db'); ?>
						</a>
						<a href="#" data-toggle="modal" data-target="#auto_backup_config" class="btn btn-default mright5 pull-right">
						<?php echo _l('auto_backup'); ?>
					</a>
					<div class="clearfix"></div>
					<p class="mtop20 text-info bold">
						<?php echo _l('utility_db_backup_note'); ?>
					</p>
					<table class="table">
						<thead>
							<th><?php echo _l('utility_backup_table_backupname'); ?></th>
							<th><?php echo _l('utility_backup_table_backupsize'); ?></th>
							<th><?php echo _l('utility_backup_table_backupdate'); ?></th>
							<th><?php echo _l('options'); ?></th>
						</thead>
						<tbody>
							<?php $backups = list_files(BACKUPS_FOLDER); ?>
							<?php foreach($backups as $backup) {
								$_fullpath = BACKUPS_FOLDER . $backup; ?>
								<tr>
									<td>
										<a href="<?php echo site_url('download/file/db_backup/' . $backup); ?>"><?php echo $backup; ?></a>
									</td>
									<td>
										<?php echo bytesToSize($_fullpath); ?>
									</td>
									<td>
										<?php echo date('M dS, Y, g:i a',filectime($_fullpath)); ?>
									</td>
									<td>
										<a href="<?php echo admin_url('utilities/delete_backup/'.$backup); ?>" class="btn btn-danger btn-icon"><i class="fa fa-remove"></i></a>
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="auto_backup_config" tabindex="-1" role="dialog">
	<div class="modal-dialog">
	<?php echo form_open(admin_url('utilities/update_auto_backup_options')); ?>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?php echo _l('auto_backup'); ?></h4>
			</div>
			<div class="modal-body">
				<?php echo render_yes_no_option('auto_backup_enabled','auto_backup_enabled'); ?>
				<?php echo render_input('auto_backup_every','auto_backup_every',get_option('auto_backup_every'),'number'); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
			</div>
		</div><!-- /.modal-content -->
		<?php echo form_close(); ?>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>
</body>
</html>
