<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#payment_mode_modal"><?php echo _l('new_payment_mode'); ?></a>
					</div>
				</div>
				<div class="panel_s">
					<div class="panel-body">
						<p class="text-warning"><?php echo _l('payment_modes_add_edit_announcement'); ?></p>
						<div class="clearfix"></div>
						<?php render_datatable(array(
							_l('payment_modes_dt_name'),
							_l('payment_modes_dt_description'),
							_l('payment_modes_dt_active'),
							_l('options')
							),'payment-modes'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="payment_mode_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">
						<span class="edit-title"><?php echo _l('payment_mode_edit_heading'); ?></span>
						<span class="add-title"><?php echo _l('payment_mode_add_heading'); ?></span>
					</h4>
				</div>
				<?php echo form_open('admin/paymentmodes/manage',array('id'=>'payment_modes_form')); ?>
				<?php echo form_hidden('paymentmodeid'); ?>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<?php echo render_input('name','payment_mode_add_edit_name'); ?>
							<?php echo render_textarea('description','payment_mode_add_edit_description','',array('data-toggle'=>'tooltip','title'=>'payment_mode_add_edit_description_tooltip','rows'=>5)); ?>
							<div class="checkbox checkbox-primary">
								<input type="checkbox" name="active" id="active">
								<label for="active"><?php echo _l('payment_mode_add_edit_active'); ?></label>
							</div>
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
		_validate_form($('form'), {
			name: 'required'
		}, manage_payment_modes);
		/* PAYMENT MODES MANAGE FUNCTIONS */
		function manage_payment_modes(form) {
			var data = $(form).serialize();
			var url = form.action;
			$.post(url, data).success(function(response) {
				response = $.parseJSON(response);
				if (response.success == true) {
					$('.table-payment-modes').DataTable().ajax.reload();
					alert_float('success', response.message);
				}
				$('#payment_mode_modal').modal('hide');
			});
			return false;
		}
		$('#payment_mode_modal').on('show.bs.modal', function(event) {
			var button = $(event.relatedTarget)
			var id = button.data('id');
			$('#payment_mode_modal input').val('');
			$('#payment_mode_modal input[name="active"]').prop('checked', true);
			$('#payment_mode_modal textarea[name="description"]').val('');
			$('#payment_mode_modal .add-title').removeClass('hide');
			$('#payment_mode_modal .edit-title').addClass('hide');

			if (typeof(id) !== 'undefined') {
				$('input[name="paymentmodeid"]').val(id);
				var name = $(button).parents('tr').find('td').eq(0).text();
				var description = $(button).parents('tr').find('td').eq(1).text();
				var active = $(button).parents('tr').find('td').eq(2).find('input').prop('checked');
				$('#payment_mode_modal input[name="active"]').prop('checked', active);
				$('#payment_mode_modal .add-title').addClass('hide');
				$('#payment_mode_modal .edit-title').removeClass('hide');
				$('#payment_mode_modal input[name="name"]').val(name);
				$('#payment_mode_modal textarea[name="description"]').val(description);
			}
		});
		/* END PAYMENT MODES MANAGE FUNCTIONS */
	</script>
</body>
</html>
