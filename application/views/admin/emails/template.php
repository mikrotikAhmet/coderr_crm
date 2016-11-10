<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<?php echo form_open($this->uri->uri_string()); ?>
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-4">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo $title; ?>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<?php echo render_input('name','template_name',$template->name); ?>
								<?php echo render_input('subject','template_subject',$template->subject); ?>
								<?php echo render_input('fromname','template_fromname',$template->fromname); ?>
								<?php echo render_input('fromemail','template_fromemail',$template->fromemail); ?>
								<div class="checkbox checkbox-primary">
									<input type="checkbox" name="plaintext" <?php if($template->plaintext == 1){echo 'checked';} ?>>
									<label><?php echo _l('send_as_plain_text'); ?></label>
								</div>
								<div class="checkbox checkbox-primary">
									<input type="checkbox" name="disabled" <?php if($template->active == 0){echo 'checked';} ?>>
									<label data-toggle="tooltip" title="Disable this email from being sent"><?php echo _l('email_template_disabed'); ?></label>
								</div>
								<button type="submit" class="btn btn-primary pull-right"><?php echo _l('submit'); ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-8">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo _l('email_template_email_message'); ?>
					</div>
					<div class="panel-body">
						<p class="bold"><?php echo _l('task_add_edit_description'); ?></p>
						<?php $this->load->view('admin/editor/template',array('name'=>'message','contents'=>$template->message)); ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="panel_s">
							<div class="panel-heading">
								<?php echo _l('email_template_merge_fields'); ?>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12">
										<a class="btn btn-default bold" href="#" onclick="slideToggle('.available_merge_fields_container'); return false;"><?php echo _l('available_merge_fields'); ?></a>
										<div class="clearfix"></div>
										<div class="row available_merge_fields_container hide">
											<?php
											$available_merge_fields;
											foreach($available_merge_fields as $field){
												foreach($field as $key => $val){
													echo '<div class="col-md-6 merge_fields_col">';
													echo '<h5 class="bold">'.ucfirst($key).'</h5>';
													foreach($val as $_field){
														foreach($_field['available'] as $_available){
															if($_available == $template->type){
																echo '<p>'.$_field['name'].'<span class="pull-right"><a href="#" class="add_merge_field">'.$_field['key'].'</a></span></p>';
															}
														}
													}
													echo '</div>';
												}
											}
											?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>
<?php init_tail(); ?>
<script>
	$(document).ready(function() {
	    var merge_fields_col = $('.merge_fields_col');
	    // If not fields available
	    $.each(merge_fields_col, function() {
	        var total_available_fields = $(this).find('p');
	        if (total_available_fields.length == 0) {
	            $(this).remove();
	        }
	    });
	});

	_validate_form($('form'), {
	    name: 'required',
	    subject: 'required',
	    fromname: 'required'
	});

</script>
</body>
</html>
