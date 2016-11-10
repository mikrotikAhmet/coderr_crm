<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<a href="#" onclick="new_status(); return false;" class="btn btn-info pull-left display-block">
							<?php echo _l('new_ticket_status'); ?>
						</a>
					</div>
				</div>
				<div class="panel_s">
					<div class="panel-body">
						<?php if(count($statuses) > 0){ ?>
						<div class="table-responsive">
							<table class="table animated fadeIn">
								<thead>
									<th><?php echo _l('ticket_statuses_dt_name'); ?></th>
									<th><?php echo _l('options'); ?></th>
								</thead>
								<tbody>
									<?php foreach($statuses as $status){ ?>
									<tr>
										<td><?php echo $status['name']; ?><br />
											<?php echo _l('ticket_statuses_table_total',total_rows('tbltickets',array('status'=>$status['ticketstatusid']))); ?>
										</td>
										<td>
											<a href="#" onclick="edit_status(this,<?php echo $status['ticketstatusid']; ?>); return false" data-name="<?php echo $status['name']; ?>" data-color="<?php echo $status['statuscolor']; ?>" data-order="<?php echo $status['statusorder']; ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
											<?php if ($status['isdefault'] == 0) { ?>
											<a href="<?php echo admin_url('tickets/delete_ticket_status/'.$status['ticketstatusid']); ?>" class="btn btn-danger btn-icon"><i class="fa fa-remove"></i></a>
											<?php } ?>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<?php } else { ?>
						<p class="no-margin"><?php echo _l('no_ticket_statuses_found'); ?></p>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="status" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<?php echo form_open(admin_url('tickets/status')); ?>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">
					<span class="edit-title"><?php echo _l('ticket_status_edit'); ?></span>
					<span class="add-title"><?php echo _l('new_ticket_status'); ?></span>
				</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div id="additional"></div>
						<?php echo render_input('name','ticket_status_add_edit_name'); ?>
						<?php echo render_input('statuscolor','ticket_status_add_edit_color','','color'); ?>
						<?php echo render_input('statusorder','ticket_status_add_edit_order','','number'); ?>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
			</div>
		</div><!-- /.modal-content -->
		<?php echo form_close(); ?>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>
<script>
	$(document).ready(function(){
		_validate_form($('form'),{name:'required'},manage_ticket_statuses);
		$('#status').on('hidden.bs.modal', function(event) {
			$('#additional').html('');
			$('#status input').val('');
			$('.add-title').removeClass('hide');
			$('.edit-title').removeClass('hide');
		});
	});
	function manage_ticket_statuses(form) {
		var data = $(form).serialize();
		var url = form.action;
		$.post(url, data).success(function(response) {
			window.location.reload();
		});
		return false;
	}
	function new_status(){
		$('#status').modal('show');
		$('.edit-title').addClass('hide');
	}
	function edit_status(invoker,id){
		var name = $(invoker).data('name');
		var color = $(invoker).data('color');
		var statusorder = $(invoker).data('order');
		$('#additional').append(hidden_input('id',id));
		$('#status input[name="name"]').val(name);
		$('#status input[name="statuscolor"]').val(color);
		$('#status input[name="statusorder"]').val(statusorder);
		$('#status').modal('show');
		$('.add-title').addClass('hide');
	}
</script>
</body>
</html>
