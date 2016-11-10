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
						<?php if(isset($list)){ ?>
						<a href="<?php echo admin_url('mail_lists/mail_list'); ?>" class="btn btn-success pull-right mbot20 display-block"><?php echo _l('new_mail_list'); ?></a>
						<div class="clearfix"></div>
						<?php } ?>
						<?php echo form_open($this->uri->uri_string()); ?>

						<?php $value = (isset($list) ? $list->name : ''); ?>
						<?php echo render_input('name','mail_list_add_edit_name',$value); ?>
						<div class="form-group">
							<a href="#" class="btn btn-default add_list_custom_field" onclick="add_list_custom_field()"><i class="fa fa-plus"></i> <?php echo _l('mail_list_add_edit_customfield'); ?></a>
						</div>
						<div class="list_custom_fields_area">
							<?php
							if(isset($list)){
								if(count($custom_fields) > 0){
									foreach($custom_fields as $field){ ?>
									<div class="input-group list_custom_field mbot15">
										<input type="text" name="list_custom_fields_update[<?php echo $field['customfieldid']; ?>]" value="<?php echo $field['fieldname']; ?>" class="form-control">
										<span class="input-group-addon">
											<a href="#" onclick="remove_list_custom_field(this,<?php echo $field['customfieldid']; ?>)"><i class="fa fa-remove"></i></a>
										</span>
									</div>
									<?php }
								}
							}
							?>
						</div>
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
	_validate_form($('form'), {
	    name: 'required'
	});
	// Will add mail list custom field
	function add_list_custom_field(listid) {

	    var name = "list_custom_fields_add[]";
	    if (typeof(listid) !== 'undefined') {
	        name = 'list_custom_fields_update[' + listid + ']'
	    }

	    $('.list_custom_fields_area').append('<div class="input-group list_custom_field mbot15"><input type="text" name="' + name + '" placeholder="Enter field name" class="form-control"><span class="input-group-addon"><a href="#" onclick="remove_list_custom_field(this)"><i class="fa fa-remove"></i></a></span></div>')
	}
	// Remove mail list custom field / if is edit removes from database
	function remove_list_custom_field(field, fieldid) {
	    if (typeof(fieldid) !== 'undefined') {
	        $.get(admin_url + 'mail_lists/remove_list_custom_field/' + fieldid, function(response) {
	            if (response.success == true) {
	                alert_float('success', response.message);
	                $(field).parents('.list_custom_field').remove();
	            } else {
	                alert_float('warning', response.message);
	            }
	        }, 'json');
	    } else {
	        $(field).parents('.list_custom_field').remove();
	    }
	}
</script>
</body>
</html>
