<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<?php include_once(APPPATH . 'views/admin/estimates/estimates_top_stats.php'); ?>
			<div class="col-md-5" id="small-table">
				<div class="panel_s">
					<div class="panel-body">
						<?php if(has_permission('manageSales')){ ?>
						<a href="<?php echo admin_url('estimates/estimate'); ?>" class="btn btn-info pull-left new"><?php echo _l('create_new_estimate'); ?></a>
						<?php } ?>
						<div class="display-block text-right option-buttons">
							<div class="btn-group pull-right mleft4" data-toggle="tooltip" title="<?php echo _l('estimates_list_tooltip'); ?>">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i class="fa fa-list"></i>
								</button>
								<ul class="dropdown-menu">
									<li>
										<a href="#" onclick="dt_custom_view('','.table-estimates'); return false;">
											<?php echo _l('estimates_list_all'); ?>
										</a>
									</li>
									<li>
										<a href="#" onclick="show_estimates_by_status(1); return false;">
											<?php echo _l('estimate_status_draft'); ?> (<?php echo total_rows('tblestimates',array('status'=>0)); ?>)
										</a>
									</li>
									<li>
										<a href="#" onclick="show_estimates_by_status(2); return false;">
											<?php echo _l('estimate_status_sent'); ?> (<?php echo total_rows('tblestimates',array('status'=>2)); ?>)
										</a>
									</li>
									<li>
										<a href="#" onclick="show_estimates_by_status(3); return false;">
											<?php echo _l('estimate_status_declined'); ?> (<?php echo total_rows('tblestimates',array('status'=>3)); ?>)
										</a>
									</li>
									<li>
										<a href="#" onclick="show_estimates_by_status(4); return false;">
											<?php echo _l('estimate_status_accepted'); ?> (<?php echo total_rows('tblestimates',array('status'=>4)); ?>)
										</a>
									</li>
									<li>
										<a href="#" onclick="show_estimates_by_status(5); return false;">
											<?php echo _l('estimate_status_expired'); ?> (<?php echo total_rows('tblestimates',array('status'=>5)); ?>)
										</a>
									</li>

								</ul>
							</div>
							<a href="#" class="btn btn-default" onclick="slideToggle('#stats-top'); return false;" data-toggle="tooltip" title="<?php echo _l('view_stats_tooltip'); ?>"><i class="fa fa-bar-chart"></i></a>
							<a href="#" class="btn btn-default" onclick="toggle_small_view('.table-estimates','#estimate'); return false;" data-toggle="tooltip" title="<?php echo _l('estimates_toggle_table_tooltip'); ?>"><i class="fa fa-bars"></i></a>
						</div>
					</div>
				</div>
				<div class="panel_s animated fadeIn">
					<div class="panel-body">
						<!-- if estimateid found in url -->
						<?php echo form_hidden('estimateid',$estimateid); ?>
						<?php
						$table_data = array(
							_l('estimate_dt_table_heading_number'),
							_l('estimate_dt_table_heading_date'),
							_l('reference_no'),
							_l('estimate_dt_table_heading_client'),
							_l('estimate_dt_table_heading_expirydate'),
							_l('estimate_dt_table_heading_amount'),
							_l('estimate_dt_table_heading_status'),
							);
						$custom_fields = get_custom_fields('estimate',array('show_on_table'=>1));
						foreach($custom_fields as $field){
							array_push($table_data,$field['name']);
						}
						render_datatable($table_data,'estimates'); ?>
					</div>
				</div>
			</div>
			<div class="col-md-7">
				<div id="estimate">

				</div>
			</div>
		</div>
	</div>
</div>
<script>var hidden_columns = [2,3,4];</script>
<?php init_tail(); ?>
<script>
	$(document).ready(function(){
		$.each(hidden_columns,function(i,val){
			var column_estimates = $('.table-estimates').DataTable().column( val );
			column_estimates.visible( false );
		});

		init_estimate();
		init_estimates_total();
	});
	function init_estimates_total(){
		var currency = $('select[name="estimate_total_currency"]').val();
		$.post(admin_url + 'estimates/get_estimates_total',{currency:currency,init_total:true}).success(function(response){
			$('#estimates_total').html(response);
		});
	}
</script>
</body>
</html>


