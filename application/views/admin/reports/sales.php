<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo _l('sales_report_heading'); ?>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<select class="selectpicker" id="report-group-change" name="report-group-change" data-width="100%" data-title="<?php echo _l('reports_sales_select_report_type'); ?>">
										<option value=""></option>
										<option value="total-income"><?php echo _l('report_sales_type_income'); ?></option>
										<option value="customers-report"><?php echo _l('report_sales_type_customer'); ?></option>
										<option value="customers-group"><?php echo _l('report_by_customer_groups'); ?></option>
									</select>
								</div>
								<?php if(isset($currencies)){ ?>
								<div id="currency" class="form-group mtop15 hide" data-toggle="tooltip" title="<?php echo _l('report_sales_base_currency_select_explanation'); ?>">
									<select class="selectpicker" name="currency" data-width="100%">
										<?php foreach($currencies as $currency){
											$selected = '';
											if($currency['isdefault'] == 1){
												$selected = 'selected';
											}
											?>
											<option value="<?php echo $currency['id']; ?>" <?php echo $selected; ?> data-subtext="<?php echo $currency['name']; ?>"><?php echo $currency['symbol']; ?></option>
											<?php } ?>
										</select>
									</div>
									<?php } ?>
									<div id="date-range" class="hide animated mbot15 mtop15">
										<label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
										<div class="input-group date">
											<input type="text" class="form-control datepicker" id="report-from" name="report-from">
											<div class="input-group-addon">
												<i class="fa fa-calendar calendar-icon"></i>
											</div>
										</div>
										<label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
										<div class="input-group date">
											<input type="text" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
											<div class="input-group-addon">
												<i class="fa fa-calendar calendar-icon"></i>
											</div>
										</div>
									</div>
									<div class="form-group hide" id="report-time">
										<select class="selectpicker" name="months-report" data-width="100%">
											<option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
											<option value="6"><?php echo _l('report_sales_months_six_months'); ?></option>
											<option value="12"><?php echo _l('report_sales_months_twelve_months'); ?></option>
											<option value="custom"><?php echo _l('report_sales_months_custom'); ?></option>
										</select>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<div class="panel_s hide" id="report">
						<div class="panel-heading">
							<?php echo _l('reports_sales_generated_report'); ?>
						</div>
						<div class="panel-body">
							<canvas id="chart" class="animated fadeIn"></canvas>
							<div id="customers-report" class="hide">
								<?php render_datatable(array(
									_l('reports_sales_dt_customers_client'),
									_l('reports_sales_dt_customers_total_invoices'),
									_l('reports_sales_dt_items_customers_amount'),
									_l('reports_sales_dt_items_customers_amount_with_tax'),
									),'customers-report'); ?>
								</div>
								<canvas id="customers-group-gen" class="animated fadeIn"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php init_tail(); ?>
		<script src="<?php echo site_url('assets/js/sales-reports.js'); ?>"></script>
	</body>
	</html>
