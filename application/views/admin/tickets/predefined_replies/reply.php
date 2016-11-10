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
						<?php if(isset($predefined_reply)){ ?>
						<a href="<?php echo admin_url('tickets/predefined_reply'); ?>" class="btn btn-success pull-right mbot20 display-block"><?php echo _l('new_predefined_reply'); ?></a>
						<div class="clearix"></div>
						<?php } ?>
						<?php echo form_open($this->uri->uri_string()); ?>

						<?php $value = (isset($predefined_reply) ? $predefined_reply->name : ''); ?>
						<?php echo render_input('name','predifined_reply_add_edit_name',$value); ?>

						<?php $contents = ''; if(isset($predefined_reply)){$contents = $predefined_reply->message;} ?>
						<?php $this->load->view('admin/editor/template',array('name'=>'message','contents'=>$contents)); ?>
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
