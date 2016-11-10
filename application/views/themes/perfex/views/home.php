			<div class="col-md-12">
				<div class="panel_s">
					<?php
					if(has_customer_permission('invoices')){ ?>

					<div class="panel-heading">
						<?php echo _l('clients_quick_invoice_info'); ?>
					</div>
					<div class="panel-body">
						<?php echo form_hidden('status'); ?>
						<?php
						$total_invoices = total_rows('tblinvoices',array('clientid'=>get_client_user_id()));
						$total_open = total_rows('tblinvoices',array('status'=>1,'clientid'=>get_client_user_id()));
						$total_paid = total_rows('tblinvoices',array('status'=>2,'clientid'=>get_client_user_id()));
						$total_not_paid_completely = total_rows('tblinvoices',array('status'=>3,'clientid'=>get_client_user_id()));
						$total_overdue = total_rows('tblinvoices',array('status'=>4,'clientid'=>get_client_user_id()));

						$percent_open = ($total_invoices > 0 ? number_format(($total_open * 100) / $total_invoices,2) : 0);
						$percent_paid = ($total_invoices > 0 ? number_format(($total_paid * 100) / $total_invoices,2) : 0);
						$percent_overdue = ($total_invoices > 0 ? number_format(($total_overdue * 100) / $total_invoices,2) : 0);
						$percent_not_paid_completely = ($total_invoices > 0 ? number_format(($total_not_paid_completely * 100) / $total_invoices,2) : 0);
						?>
						<div class="row text-left invoice-quick-info">
							<div class="col-md-3">
								<div class="row">
									<div class="col-md-8">
										<a href="<?php echo site_url('clients/invoices/2'); ?>"><h5 class="bold"><?php echo _l('invoice_status_paid'); ?></h5></a>
									</div>
									<div class="col-md-4 text-right bold">
										<?php echo $total_paid; ?> / <?php echo $total_invoices; ?>
									</div>
									<div class="col-md-12">
										<div class="progress no-margin">
											<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_paid; ?>">
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="row">
									<div class="col-md-8">
										<a href="<?php echo site_url('clients/invoices/1'); ?>"><h5 class="bold">
											<?php echo _l('invoice_status_unpaid'); ?></h5></a>
										</div>
										<div class="col-md-4 text-right bold">
											<?php echo $total_open; ?> / <?php echo $total_invoices; ?>
										</div>
										<div class="col-md-12">
											<div class="progress no-margin">
												<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_open; ?>">
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-3">
									<div class="row">
										<div class="col-md-8">
											<a href="<?php echo site_url('clients/invoices/4'); ?>"><h5 class="bold"><?php echo _l('invoice_status_overdue'); ?></h5></a>
										</div>
										<div class="col-md-4 text-right bold">
											<?php echo $total_overdue; ?> / <?php echo $total_invoices; ?>
										</div>
										<div class="col-md-12">
											<div class="progress no-margin">
												<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_overdue; ?>">
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-3">
									<div class="row">
										<div class="col-md-8">
											<a href="<?php echo site_url('clients/invoices/3'); ?>"><h5 class="bold"><?php echo _l('invoice_status_not_paid_completely'); ?></h5></a>
										</div>
										<div class="col-md-4 text-right bold">
											<?php echo $total_not_paid_completely; ?> / <?php echo $total_invoices; ?>
										</div>
										<div class="col-md-12">
											<div class="progress no-margin">
												<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_not_paid_completely; ?>">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<hr />
							<div class="row">
								<div class="col-md-3">
									<div class="form-group" id="report-time">
										<select class="form-control" name="months-report" data-width="100%">
											<option value=""><?php echo _l('clients_report_sales_months_all_time'); ?></option>
											<option value="6"><?php echo _l('clients_report_sales_months_six_months'); ?></option>
											<option value="12"><?php echo _l('clients_report_sales_months_twelve_months'); ?></option>
											<option value="custom"><?php echo _l('clients_report_sales_months_custom'); ?></option>
										</select>
									</div>
									<?php if(is_client_using_multiple_currencies()){ ?>
									<div id="currency" class="form-group mtop15" data-toggle="tooltip" title="<?php echo _l('clients_home_currency_select_tooltip'); ?>">
										<select class="form-control" name="currency">
											<?php foreach($currencies as $currency){
												$selected = '';
												if($currency['isdefault'] == 1){
													$selected = 'selected';
												}
												?>
												<option value="<?php echo $currency['id']; ?>" <?php echo $selected; ?>><?php echo $currency['symbol']; ?> - <?php echo $currency['name']; ?></option>
												<?php } ?>
											</select>
										</div>
										<?php } ?>
										<div id="date-range" class="animated mbot15 hide">
											<label for="report-from" class="control-label"><?php echo _l('clients_report_select_from_date'); ?></label>
											<div class="input-group date">
												<input type="text" class="form-control datepicker" id="report-from" name="report-from">
												<div class="input-group-addon">
													<i class="fa fa-calendar calendar-icon"></i>
												</div>
											</div>
											<div class="clearfix mtop15"></div>
											<label for="report-to" class="control-label"><?php echo _l('clients_report_select_to_date'); ?></label>
											<div class="input-group date">
												<input type="text" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
												<div class="input-group-addon">
													<i class="fa fa-calendar calendar-icon"></i>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<canvas id="client-home-chart" class="animated fadeIn"></canvas>
									</div>
								</div>
							</div>
							<?php } ?>
						</div>
					</div>
