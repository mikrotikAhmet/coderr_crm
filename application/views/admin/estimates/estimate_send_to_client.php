<div class="modal animated fadeIn" id="estimate_send_to_client_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
	<?php echo form_open('admin/estimates/send_to_email/'.$estimate->id); ?>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">
					<span class="edit-title"><?php echo _l('estimate_send_to_client_modal_heading'); ?></span>
				</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="checkbox checkbox-primary">
							<input type="checkbox" name="attach_pdf" checked>
							<label for=""><?php echo _l('estimate_send_to_client_attach_pdf'); ?></label>
						</div>
						<h5 class="bold"><?php echo _l('estimate_send_to_client_preview_template'); ?></h5>
						<hr />
						<?php echo $template->message; ?>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-primary"><?php echo _l('send'); ?></button>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>
