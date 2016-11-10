<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<a href="#addNewTodo" data-toggle="modal" class="btn btn-primary">
									<?php echo _l('new_todo'); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6">
								<div class="panel_s events animated fadeIn">
									<div class="panel-body todo-body">
										<h4 class="todo-title warning-bg"><i class="fa fa-warning"></i>
											<?php echo _l('unfinished_todos_title'); ?></h4>
											<ul class="list-unstyled todo unfinished-todos todos-sortable">
												<li class="padding no-todos hide ui-state-disabled">
													<?php echo _l('no_unfinished_todos_found'); ?>
												</li>
											</ul>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12 text-center padding">
											<a href="#" class="btn btn-default text-center unfinished-loader">Load more</a>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="panel_s animated fadeIn">
										<div class="panel-body todo-body">
											<h4 class="todo-title info-bg"><i class="fa fa-check"></i>
												<?php echo _l('finished_todos_title'); ?></h4>
												<ul class="list-unstyled todo finished-todos todos-sortable">
													<li class="padding no-todos hide ui-state-disabled">
														<?php echo _l('no_finished_todos_found'); ?>
													</li>
												</ul>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12 text-center padding">
												<a href="#" class="btn btn-default text-center finished-loader">
													<?php echo _l('load_more'); ?>
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal animated fadeIn" id="addNewTodo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><?php echo _l('todo_add_title'); ?></h4>
			</div>
			<?php echo form_open('admin/todo/add',array('id'=>'add_new_todo_item')); ?>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<?php echo render_textarea('description','add_new_todo_description',''); ?>
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
	$(document).ready(function() {
	    var total_pages_unfinished = '<?php echo $total_pages_unfinished; ?>';
	    var total_pages_finished = '<?php echo $total_pages_finished; ?>';
	    var page_unfinished = 0;
	    var page_finished = 0;
	    $('.unfinished-loader').on('click', function(e) {
	        e.preventDefault();
	        if (page_unfinished <= total_pages_unfinished) {
	            $.post(window.location.href, {
	                finished: 0,
	                todo_page: page_unfinished
	            }).success(function(response) {
	                response = $.parseJSON(response);
	                if (response.length == 0) {
	                    $('.unfinished-todos .no-todos').removeClass('hide');
	                }

	                $.each(response, function(i, obj) {
	                    $('.unfinished-todos').append(render_li_items(0, obj));
	                });

	                page_unfinished++;
	            });

	            if (page_unfinished >= total_pages_unfinished - 1) {
	                $(".unfinished-loader").addClass("disabled");
	            }
	        }
	    });

	    $('.finished-loader').on('click', function(e) {
	        e.preventDefault();
	        if (page_finished <= total_pages_finished) {
	            $.post(window.location.href, {
	                finished: 1,
	                todo_page: page_finished
	            }).success(function(response) {
	                response = $.parseJSON(response);

	                if (response.length == 0) {
	                    $('.finished-todos .no-todos').removeClass('hide');
	                }
	                $.each(response, function(i, obj) {
	                    $('.finished-todos').append(render_li_items(1, obj));
	                });

	                page_finished++;
	            });

	            if (page_finished >= total_pages_finished - 1) {
	                $(".finished-loader").addClass("disabled");
	            }
	        }
	    });

	    $('.unfinished-loader').click();
	    $('.finished-loader').click();
	});

	function render_li_items(finished, obj) {
	    var todo_finished_class = '';
	    if (finished == 1) {
	        todo_finished_class = ' todo-finished';
	    }
	    return '<li><input type="hidden" value="' + finished + '" name="finished"><input type="hidden" value="' + obj.item_order + '" name="todo_order"><div class="checkbox checkbox-default todo-checkbox"><input type="checkbox" name="todo_id" value="' + obj.todoid + '"><label></label></div><span class="todo-description' + todo_finished_class + '">' + obj.description + '<a href="#" onclick="delete_todo_item(this,' + obj.todoid + ')" class="pull-right todo-delete"><i class="fa fa-remove"></i></a></span><small class="todo-date">' + obj.dateadded + '</small></li>';
	}
</script>
</body>
</html>
