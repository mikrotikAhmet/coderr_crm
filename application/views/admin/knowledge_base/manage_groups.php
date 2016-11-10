<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<a href="#" onclick="new_group(); return false;" class="btn btn-info pull-left display-block"><?php echo _l('new_group'); ?></a>
					</div>
				</div>
				<div class="panel_s">
					<div class="panel-body">
						<?php if(count($groups) > 0){ ?>
						<div class="table-responsive">
							<table class="table animated fadeIn">
								<thead>
									<th><?php echo _l('group_table_name_heading'); ?></th>
									<th><?php echo _l('group_table_isactive_heading'); ?></th>
									<th><?php echo _l('options'); ?></th>

								</thead>
								<tbody>
									<?php foreach($groups as $group){ ?>
									<tr>
										<td><?php echo $group['name']; ?> <span class="badge"><?php echo total_rows('tblknowledgebase','articlegroup='.$group['groupid']); ?></span></td>
										<td>
											<input type="checkbox" class="switch-box input-xs" data-size="mini" data-id="<?php echo $group['groupid']; ?>" data-switch-url="admin/knowledge_base/change_group_status" <?php if($group['active'] == 1){echo 'checked';} ?>></td>
											<td>
												<a href="#" onclick="edit_group(this,<?php echo $group['groupid']; ?>); return false" data-name="<?php echo $group['name']; ?>" data-color="<?php echo $group['color']; ?>" data-description="<?php echo clear_textarea_breaks($group['description']); ?>" data-order="<?php echo $group['group_order']; ?>" data-active="<?php echo $group['active']; ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
												<a href="<?php echo admin_url('knowledge_base/delete_group/'.$group['groupid']); ?>" class="btn btn-danger btn-icon"><i class="fa fa-remove"></i></a>
											</td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
							<?php } else { ?>
							<p class="no-margin"><?php echo _l('kb_no_groups_found'); ?></p>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="group" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<?php echo form_open(admin_url('knowledge_base/group')); ?>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<span class="edit-title"><?php echo _l('edit_kb_group'); ?></span>
						<span class="add-title"><?php echo _l('new_group'); ?></span>
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div id="additional"></div>
							<?php echo render_input('name','kb_group_add_edit_name'); ?>
							<?php echo render_input('color','kb_group_color','','color'); ?>
							<?php echo render_textarea('description','kb_group_add_edit_description'); ?>
							<?php echo render_input('group_order','kb_group_order'); ?>
							<div class="checkbox checkbox-primary">
								<input type="checkbox" name="disabled">
								<label><?php echo _l('kb_group_add_edit_disabled'); ?></label>
							</div>
							<p class="text-muted"><?php echo _l('kb_group_add_edit_note'); ?></p>
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
			_validate_form($('form'),{name:'required'},manage_kb_groups);
			$('#group').on('hidden.bs.modal', function(event) {
				$('#additional').html('');
				$('#group input').val('');
				$('.add-title').removeClass('hide');
				$('.edit-title').removeClass('hide');
			});
		});
		function manage_kb_groups(form) {
			var data = $(form).serialize();
			var url = form.action;
			$.post(url, data).success(function(response) {
				window.location.reload();
			});
			return false;
		}
		function new_group(){
			$('#group').modal('show');
			$('.edit-title').addClass('hide');
		}
		function edit_group(invoker,id){
			$('#additional').append(hidden_input('id',id));
			$('#group input[name="name"]').val($(invoker).data('name'));
			$('#group textarea[name="description"]').val($(invoker).data('description'));
			$('#group input[name="color"]').val($(invoker).data('color'));
			$('#group input[name="group_order"]').val($(invoker).data('order'));

			var active = $(invoker).data('active');
			if(active == 0){
				$('input[name="disabled"]').prop('checked',true);
			} else {
				$('input[name="disabled"]').prop('checked',false);
			}
			$('#group').modal('show');
			$('.add-title').addClass('hide');
		}

	</script>
</body>
</html>
