<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#tax_modal"><?php echo _l('new_tax'); ?></a>
					</div>
				</div>
				<div class="panel_s">

					<div class="panel-body">

						<div class="clearfix"></div>
						<?php render_datatable(array(
							_l('tax_dt_name'),
							_l('tax_dt_rate'),
							_l('options')
							),'taxes'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="tax_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">
						<span class="edit-title"><?php echo _l('tax_edit_title'); ?></span>
						<span class="add-title"><?php echo _l('tax_add_title'); ?></span>
					</h4>
				</div>
				<?php echo form_open('admin/taxes/manage',array('id'=>'tax_form')); ?>
				<?php echo form_hidden('taxid'); ?>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<?php echo render_input('name','tax_add_edit_name'); ?>
							<?php echo render_input('taxrate','tax_add_edit_rate','','number'); ?>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
					<button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
	</div>
	<?php init_tail(); ?>
	<script>
		_validate_form($('form'),{name:'required',rate:{number:true,required:true}},manage_tax);
		/* TAX MANAGE FUNCTIONS */
		function manage_tax(form) {
			var data = $(form).serialize();
			var url = form.action;
			$.post(url, data).success(function(response) {
				response = $.parseJSON(response);
				if (response.success == true) {
					$('.table-taxes').DataTable().ajax.reload();
					alert_float('success', response.message);
				}
				$('#tax_modal').modal('hide');
			});
			return false;
		}

		$('#tax_modal').on('show.bs.modal', function(event) {
			var button = $(event.relatedTarget)
			var id = button.data('id');
			$('#tax_modal input').val('');
			$('#tax_modal .add-title').removeClass('hide');
			$('#tax_modal .edit-title').addClass('hide');

			if (typeof(id) !== 'undefined') {
				$('input[name="taxid"]').val(id);
				var name = $(button).parents('tr').find('td').eq(0).text();
				var rate = $(button).parents('tr').find('td').eq(1).text();
				$('#tax_modal .add-title').addClass('hide');
				$('#tax_modal .edit-title').removeClass('hide');
				$('#tax_modal input[name="name"]').val(name);
				$('#tax_modal input[name="taxrate"]').val(rate);
			}
		});
		/* END TAX MANAGE FUNCTIONS */
	</script>
</body>
</html>
