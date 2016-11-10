<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<?php echo form_open($this->uri->uri_string(),array('id'=>'estimate-form')); ?>

			<?php
			if(isset($estimate)){
				echo form_hidden('isedit');
			}
			?>
			<div class="col-md-12">
				<?php $this->load->view('admin/estimates/estimate_template'); ?>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>
</div>
<?php init_tail(); ?>
<script>
	$(document).ready(function(){
	_validate_form($('#estimate-form'),{clientid:'required',date:'required',currency:'required',number:'required'});
	// Init accountacy currency symbol
	init_currency_symbol($('select[name="currency"]').val());
});
</script>
</body>
</html>
