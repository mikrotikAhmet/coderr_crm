<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="btn-group mleft10" data-toggle="tooltip">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fa fa-list"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right width220">
								<li>
									<a href="#" onclick="dt_custom_view('','.table-contracts'); return false;">
										<?php echo _l('contracts_view_exclude_trashed'); ?>
									</a>
								</li>
								<li>
									<a href="#" onclick="dt_custom_view('all','.table-contracts'); return false;">
										<?php echo _l('contracts_view_all'); ?>
									</a>
								</li>
								<li>
									<a href="#" onclick="dt_custom_view('expired','.table-contracts'); return false;">
										<?php echo _l('contracts_view_expired'); ?>
									</a>
								</li>
								<li>
									<a href="#" onclick="dt_custom_view('without_dateend','.table-contracts'); return false;">
										<?php echo _l('contracts_view_without_dateend'); ?>
									</a>
								</li>
								<li>
									<a href="#" onclick="dt_custom_view('trash','.table-contracts'); return false;">
										<?php echo _l('contracts_view_trash'); ?>
									</a>
									<li class="divider"></li>
								</li>
								<?php foreach($contract_types as $type){ ?>
								<li>
									<a href="#" onclick="dt_custom_view('<?php echo $type['id']; ?>','.table-contracts'); return false;">
										<?php echo $type['name']; ?>
									</a>
								</li>
								<?php } ?>

							</ul>
						</div>
						<a href="<?php echo admin_url('contracts/contract'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_contract'); ?></a>

						<div class="row" id="contract_summary">
							<hr />
							<?php $minus_7_days = date('Y-m-d', strtotime("-7 days")); ?>
							<?php $plus_7_days = date('Y-m-d', strtotime("+7 days")); ?>

							<div class="col-md-12">
								<h3 class="no-margin text-success"><?php echo _l('contract_summary_heading'); ?></h3>
							</div>
							<div class="col-md-2 border-right">
								<h3 class="bold"><?php echo total_rows('tblcontracts',array('DATE(dateend) >'=>date('Y-m-d'),'trash'=>0)); ?></h3>
								<span class="text-info bold"><?php echo _l('contract_summary_active'); ?></span>
							</div>
							<div class="col-md-2 border-right">
								<h3 class="bold"><?php echo total_rows('tblcontracts',array('DATE(dateend) <'=>date('Y-m-d'),'trash'=>0)); ?></h3>
								<span class="text-danger bold"><?php echo _l('contract_summary_expired'); ?></span>
							</div>
							<div class="col-md-2 border-right">
								<h3 class="bold"><?php echo total_rows('tblcontracts','dateend BETWEEN "'.$minus_7_days.'" AND "'.$plus_7_days.'" AND trash=0'); ?></h3>
								<span class="text-warning bold"><?php echo _l('contract_summary_about_to_expire'); ?></span>
							</div>
							<div class="col-md-2 border-right">
								<h3 class="bold"><?php echo total_rows('tblcontracts','dateadded BETWEEN "'.$minus_7_days.'" AND "'.$plus_7_days.'" AND trash=0'); ?></h3>
								<span class="text-info bold"><?php echo _l('contract_summary_recently_added'); ?></span>
							</div>
							<div class="col-md-2">
								<h3 class="bold"><?php echo total_rows('tblcontracts',array('trash'=>1)); ?></h3>
								<span class="text-info bold"><?php echo _l('contract_summary_trash'); ?></span>
							</div>
							<div class="clearfix"></div>
							<hr />
							<div class="col-md-6 border-right">
								<h4 class="text-muted bold"><?php echo _l('contract_summary_by_type'); ?></h4>
								<canvas class="chart" id="contracts-by-type-chart"></canvas>
							</div>
							<div class="col-md-6">
								<h4 class="text-muted bold"><?php echo _l('contract_summary_by_type_value'); ?></h4>
								<canvas class="chart" id="contracts-value-by-type-chart"></canvas>
							</div>
						</div>
					</div>
				</div>
				<div class="panel_s">
					<?php echo form_hidden('custom_view'); ?>
					<div class="panel-body">
						<?php
						$table_data = array(
							_l('contract_list_subject'),
							_l('contract_list_client'),
							_l('contract_types_list_name'),
							_l('contract_list_start_date'),
							_l('contract_list_end_date'),
							);
						$custom_fields = get_custom_fields('contracts',array('show_on_table'=>1));
						foreach($custom_fields as $field){
							array_push($table_data,$field['name']);
						}
						array_push($table_data,_l('options'));
						render_datatable($table_data,'contracts'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script>
	var chartContractTypes,chartContractTypesValues;
	$(document).ready(function(){
		var ctx_types = $('#contracts-by-type-chart').get(0).getContext('2d');
		var ctx_types_values = $('#contracts-value-by-type-chart').get(0).getContext('2d');
		chartContractTypes = new Chart(ctx_types).Bar(<?php echo $chart_types; ?>,{responsive:true});
		chartContractTypesValues = new Chart(ctx_types_values).Line(<?php echo $chart_types_values; ?>,{responsive:true});
	});
</script>
</body>
</html>
