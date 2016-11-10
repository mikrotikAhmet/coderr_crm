<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<?php echo form_open($this->uri->uri_string(),array('id'=>'invoice-form')); ?>
			<?php
			if(isset($invoice)){
				echo form_hidden('isedit');
			}
			?>
			<div class="col-md-12">
				<?php $this->load->view('admin/invoices/invoice_template'); ?>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
	<?php init_tail(); ?>
	<script>
		$(document).ready(function(){
			var form = _validate_form($('#invoice-form'),{clientid:'required',date:'required',currency:'required',number:'required'});
	    // Init accountacy currency symbol
	    init_currency_symbol($('select[name="currency"]').val());
	});
</script>
</body>
</html>

