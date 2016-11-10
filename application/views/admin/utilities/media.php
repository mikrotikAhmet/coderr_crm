<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<?php echo form_open_multipart(admin_url('utilities/upload_media'),array('class'=>'dropzone','id'=>'media-upload')); ?>
						<input type="file" name="file" multiple />
						<?php echo form_close(); ?>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo _l('media_files'); ?>
					</div>
					<div class="panel-body">
						<div id="media-view">
							<?php render_datatable(array(
							_l('media_dt_filename'),
							_l('media_dt_last_modified'),
							_l('media_dt_filesize'),
							_l('media_dt_mime_type'),
							_l('options'),
							),'media'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>
<?php include_once(APPPATH . 'views/admin/clients/modals/send_file_modal.php'); ?>
<?php init_tail(); ?>
<script src="<?php echo site_url('assets/js/media.js'); ?>"></script>
</body>
</html>
