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
						<?php echo form_open($this->uri->uri_string()); ?>

						<?php $value = (isset($announcement) ? $announcement->name : ''); ?>
						<?php echo render_input('name','announcement_name',$value); ?>

						<?php $value = (isset($announcement) ? $announcement->message : ''); ?>
						<?php echo render_textarea('message','announcement_message',$value,array('rows'=>10)); ?>

						<div class="checkbox checkbox-primary checkbox-inline">
							<input type="checkbox" name="showtostaff" <?php if(isset($announcement)){if($announcement->showtostaff == 1){echo 'checked';} } else {echo 'checked';} ?>>
							<label><?php echo _l('announcement_show_to_staff'); ?></label>
						</div>
						<div class="checkbox checkbox-primary checkbox-inline">
							<input type="checkbox" name="showtousers" <?php if(isset($announcement)){if($announcement->showtousers == 1){echo 'checked';}} ?>>
							<label><?php echo _l('announcement_show_to_clients'); ?></label>
						</div>
						<div class="checkbox checkbox-primary checkbox-inline">
							<input type="checkbox" name="showname" <?php if(isset($announcement)){if($announcement->showname == 1){echo 'checked';}} ?>>
							<label><?php echo _l('announcement_show_my_name'); ?></label>
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
_validate_form($('form'),{name:'required',message:'required'});
</script>
</body>
</html>
