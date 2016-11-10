<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<?php include_once(APPPATH . 'views/admin/invoices/invoices_top_stats.php'); ?>
			<div class="col-md-5" id="small-table">
				<div class="panel_s">
					<div class="panel-body">
						<?php if(has_permission('manageSales')){ ?>
						<a href="<?php echo admin_url('invoices/invoice'); ?>" class="btn btn-info pull-left new"><?php echo _l('create_new_invoice'); ?></a>
						<?php } ?>
						<div class="display-block text-right option-buttons">
							<div class="btn-group pull-right mleft4" data-toggle="tooltip" title="<?php echo _l('invoices_list_tooltip'); ?>">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i class="fa fa-list"></i>
								</button>
								<ul class="dropdown-menu width220">
									<li>
										<a href="#" onclick="dt_custom_view('','.table-invoices'); return false;">
											<?php echo _l('invoices_list_all'); ?>
										</a>
									</li>
									<li>
										<a href="#" onclick="dt_custom_view('not_sent','.table-invoices'); return false;">
											<?php echo _l('invoices_list_not_sent'); ?> (<?php echo total_rows('tblinvoices',array('sent'=>0)); ?>)
										</a>
									</li>
									<li>
										<a href="#" onclick="dt_custom_view('not_have_payment','.table-invoices'); return false;">
											<?php echo _l('invoices_list_not_have_payment'); ?>
										</a>
									</li>
									<li>
										<a href="#" onclick="dt_custom_view('recurring','.table-invoices'); return false;">
											<?php echo _l('invoices_list_recuring'); ?> (<?php echo total_rows('tblinvoices',array('recurring >'=>0)); ?>)
										</a>
									</li>
									<li class="divider"></li>

									<li>
										<a href="#" onclick="show_invoices_by_status(1); return false;"><?php echo _l('invoice_status_unpaid'); ?></a>
									</li>
									<li>
										<a href="#" onclick="show_invoices_by_status(2); return false;"><?php echo _l('invoice_status_paid'); ?></a>
									</li>
									<li>
										<a href="#" onclick="show_invoices_by_status(3); return false;"><?php echo _l('invoice_status_not_paid_completely'); ?>
										</a>
									</li>
									<li>
										<a href="#" onclick="show_invoices_by_status(4); return false;"><?php echo _l('invoice_status_overdue'); ?>
										</a>
									</li>
									<li class="divider"></li>
									<?php foreach($payment_modes as $mode){ ?>
									<li>
										<a href="#" onclick="dt_custom_view('<?php echo $mode['id']; ?>','.table-invoices'); return false;">
											<?php echo _l('invoices_list_made_payment_by',$mode['name']); ?>
										</a>
									</li>
									<?php } ?>
								</ul>
							</div>
							<a href="#" class="btn btn-default" onclick="slideToggle('#stats-top'); return false;" data-toggle="tooltip" title="<?php echo _l('view_stats_tooltip'); ?>"><i class="fa fa-bar-chart"></i></a>
							<a href="#" class="btn btn-default" onclick="toggle_small_view('.table-invoices','#invoice'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-bars"></i></a>
						</div>
					</div>
				</div>
				<div class="panel_s animated fadeIn">
					<div class="panel-body">

						<!-- if invoiceid found in url -->
						<?php echo form_hidden('invoiceid',$invoiceid); ?>
						<?php
						$table_data = array(
							_l('invoice_dt_table_heading_number'),
							_l('invoice_dt_table_heading_date'),
							_l('invoice_dt_table_heading_client'),
							_l('invoice_dt_table_heading_duedate'),
							_l('invoice_dt_table_heading_amount'),
							_l('invoice_dt_table_heading_status'));

						$custom_fields = get_custom_fields('invoice',array('show_on_table'=>1));
						foreach($custom_fields as $field){
							array_push($table_data,$field['name']);
						}

						render_datatable($table_data,'invoices');
						?>
					</div>
				</div>
			</div>
			<div class="col-md-7">
				<div id="invoice">

				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="invoice_attach" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?php echo _l('invoice_attach_file'); ?></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<?php echo form_open_multipart('admin/invoices/upload_file',array('id'=>'invoice-upload','class'=>'dropzone')); ?>
						<input type="file" name="file" multiple />
						<?php echo form_close(); ?>
						<div class="row mtop15" id="invoice_uploaded_files_preview">
						</div>
					</div>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script> var hidden_columns = [1,3];</script>
<?php init_tail(); ?>
<script>
	$(document).ready(function(){
		$.each(hidden_columns,function(i,val){
			var column_invoices = $('.table-invoices').DataTable().column( val );
			column_invoices.visible( false );
		});
		init_invoice();
	});
</script>
</body>
</html>


