<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<a href="#" onclick="new_department(); return false;" class="btn btn-info pull-left display-block">
							<?php echo _l('new_department'); ?>
						</a>
					</div>
				</div>
				<div class="panel_s">
					<div class="panel-body">
						<div class="clearfix"></div>
						<?php render_datatable(array(
							_l('department_list_name'),
							_l('department_email'),
							_l('department_calendar_id'),
							_l('options')
							),'departments'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="department" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<?php echo form_open(admin_url('departments/department')); ?>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<span class="edit-title"><?php echo _l('edit_department'); ?></span>
						<span class="add-title"><?php echo _l('new_department'); ?></span>
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div id="additional"></div>
							<?php echo render_input('name','department_name'); ?>
							<?php echo render_input('email','department_email','','email'); ?>
							<?php echo render_input('calendar_id','department_calendar_id'); ?>
							<div class="checkbox checkbox-primary">
								<input type="checkbox" name="hidefromclient">
								<label><?php echo _l('department_hide_from_client'); ?></label>
							</div>
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
			_validate_form($('form'),{name:'required',email:{
				email: true,
				remote: {
					url: admin_url + "departments/email_exists",
					type: 'post',
					data: {
						email: function() {
							return $('input[name="email"]').val();
						},
						departmentid:function(){
							return $('input[name="id"]').val();
						}
					}
				}}},manage_departments);
			$('#department').on('hidden.bs.modal', function(event) {
				$('#additional').html('');
				$('#department input').val('');
				$('.add-title').removeClass('hide');
				$('.edit-title').removeClass('hide');
			});
		});
		function manage_departments(form) {
			var data = $(form).serialize();
			var url = form.action;
			$.post(url, data).success(function(response) {
				response = $.parseJSON(response);
				if(response.success == true){
					alert_float('success',response.message);
				}
				$('.table-departments').DataTable().ajax.reload();
				$('#department').modal('hide');
			});
			return false;
		}
		function new_department(){
			$('#department').modal('show');
			$('.edit-title').addClass('hide');
		}
		function edit_department(invoker,id){
			var hide_from_client = $(invoker).data('hide-from-client');
			if(hide_from_client == 1){
				$('input[name="hidefromclient"]').prop('checked',true);
			} else {
				$('input[name="hidefromclient"]').prop('checked',false);
			}
			$('#additional').append(hidden_input('id',id));
			$('#department input[name="name"]').val($(invoker).data('name'));
			$('#department input[name="email"]').val($(invoker).data('email'));
			$('#department input[name="calendar_id"]').val($(invoker).data('calendar-id'));
			$('#department').modal('show');
			$('.add-title').addClass('hide');
		}
	</script>
</body>
</html>
