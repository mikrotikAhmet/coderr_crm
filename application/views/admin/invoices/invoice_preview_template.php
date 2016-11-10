<?php echo form_hidden('_at_invoice_id',$invoice->id); ?>
<div class="col-md-12 no-padding animated fadeIn">
	<div class="panel_s">
		<div class="panel-body padding-17">
			<?php if($invoice->recurring > 0){
				echo '<div class="ribbon warning"><span>'._l('invoice_recurring_indicator').'</span></div>';
			} ?>
			<ul class="nav nav-tabs no-margin" role="tablist">
				<li role="presentation" class="active">
					<a href="#tab_invoice" aria-controls="tab_invoice" role="tab" data-toggle="tab">
						<?php echo _l('invoice'); ?>
					</a>
				</li>
				<li role="presentation">
					<a href="#tab_tasks" aria-controls="tab_tasks" role="tab" data-toggle="tab">
						<?php echo _l('tasks'); ?>
					</a>
				</li>
				<li role="presentation">
					<a href="#tab_activity" aria-controls="tab_activity" role="tab" data-toggle="tab">
						<?php echo _l('invoice_view_activity_tooltip'); ?>
					</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="panel_s">
		<div class="panel-body">
			<div class="row">
				<div class="col-md-3">
					<?php echo format_invoice_status($invoice->status,'mtop10'); ?>
				</div>
				<div class="col-md-9">
					<div class="pull-right">

						<?php
						$_tooltip = _l('invoice_sent_to_email_tooltip');
						if($invoice->sent == 1){
							$_tooltip = _l('invoice_already_send_to_client_tooltip',time_ago($invoice->datesend));
						}
						?>
						<a href="<?php echo admin_url('invoices/invoice/'.$invoice->id); ?>" data-toggle="tooltip" title="<?php echo _l('edit_invoice_tooltip'); ?>" class="btn btn-default pull-left mright5" data-placement="bottom"><i class="fa fa-pencil-square-o"></i></a>

						<a href="<?php echo admin_url('invoices/pdf/'.$invoice->id); ?>" class="btn btn-default pull-left mright5" data-toggle="tooltip" title="View PDF" data-placement="bottom"><i class="fa fa-file-pdf-o"></i></a>

						<a href="#" class="invoice-send-to-client btn pull-left btn-default mright5" data-toggle="tooltip" title="<?php echo $_tooltip; ?>" data-placement="bottom"><i class="fa fa-envelope"></i></a>
						<!-- Single button -->
						<div class="btn-group">
							<button type="button" class="btn btn-default pull-left dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<?php echo _l('more'); ?> <span class="caret"></span>
							</button>
							<ul class="dropdown-menu dropdown-menu-right">
								<li><a href="<?php echo site_url('viewinvoice/' . $invoice->id . '/' .  $invoice->hash) ?>" target="_blank"><?php echo _l('view_invoice_as_customer_tooltip'); ?></a></li>
								<li>
									<?php if($invoice->status == 4){ ?>
									<a href="<?php echo admin_url('invoices/send_overdue_notice/'.$invoice->id); ?>"><?php echo _l('send_overdue_notice_tooltip'); ?></a>
									<?php } ?>
								</li>
								<li>
									<a href="#" data-toggle="modal" data-target="#invoice_attach"><?php echo _l('invoice_attach_file'); ?></a>
								</li>
								<li>
									<a href="<?php echo admin_url('invoices/copy/'.$invoice->id); ?>"><?php echo _l('invoice_copy'); ?></a>
								</li>
								<?php if($invoice->sent == 0){ ?>
								<li>
									<a href="<?php echo admin_url('invoices/mark_as_sent/'.$invoice->id); ?>"><?php echo _l('invoice_mark_as_sent'); ?></a>
								</li>
								<?php } ?>
								<?php
								if((get_option('delete_only_on_last_invoice') == 1 && is_last_invoice($invoice->id)) || (get_option('delete_only_on_last_invoice') == 0)){ ?>
								<li data-toggle="tooltip" data-title="<?php echo _l('delete_invoice_tooltip'); ?>">
									<a href="<?php echo admin_url('invoices/delete/'.$invoice->id); ?>"><?php echo _l('delete_invoice'); ?></a>
								</li>
								<?php
							}
							?>
						</ul>
					</div>
					<a href="#" onclick="record_payment(<?php echo $invoice->id; ?>); return false;"  class="mleft10 pull-right btn btn-info<?php if($invoice->status == 2){echo ' disabled';} ?>"><?php echo _l('invoice_record_payment'); ?></a>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<hr />
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane ptop10 active" id="tab_invoice">
				<div id="invoice-preview" class="mtop30">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-6">
								<h4 class="bold"><a href="<?php echo admin_url('invoices/invoice/'.$invoice->id); ?>"><?php echo format_invoice_number($invoice->id); ?></a></h4>
								<address>
									<span class="bold"><a href="<?php echo admin_url('settings?tab_hash=company_info'); ?>" target="_blank"><?php echo get_option('invoice_company_name'); ?></span></a><br>
									<?php echo get_option('invoice_company_address'); ?><br>
									<?php echo get_option('invoice_company_city'); ?>, <?php echo get_option('invoice_company_country_code'); ?> <?php echo get_option('invoice_company_postal_code'); ?><br>
									<?php if(get_option('invoice_company_phonenumber') != ''){ ?>
									<abbr title="Phone">P:</abbr> <?php echo get_option('invoice_company_phonenumber'); ?><br />
									<?php } ?>
									<?php
									// check for company custom fields
									$custom_company_fields = get_company_custom_fields();
									foreach($custom_company_fields as $field){
										echo $field['label'] . ':' . $field['value'] . '<br />';
									}
									?>
								</address>
							</div>
							<div class="col-sm-6 text-right">
								<span class="bold"><?php echo _l('invoice_bill_to'); ?>:</span>
								<address>
									<span class="bold"><a href="<?php echo admin_url('clients/client/'.$invoice->client->userid); ?>" target="_blank"><?php echo $invoice->client->company; ?></a></span><br>
									<?php echo $invoice->billing_street; ?><br>
									<?php echo $invoice->billing_city; ?>, <?php echo $invoice->billing_state; ?><br/><?php echo get_country_short_name($invoice->billing_country); ?>,<?php echo $invoice->billing_zip; ?><br>
									<?php if(!empty($invoice->client->vat)){ ?>
									<?php echo _l('invoice_vat'); ?>: <?php echo $invoice->client->vat; ?><br />
									<?php } ?>
									<?php
									// check for customer custom fields which is checked show on pdf
									$pdf_custom_fields = get_custom_fields('customers',array('show_on_pdf'=>1));
									foreach($pdf_custom_fields as $field){
										$value = get_custom_field_value($invoice->clientid,$field['id'],'customers');
										if($value == ''){continue;}
										echo $field['name'] . ': ' . $value . '<br />';
									}
									?>
								</address>
								<?php if($invoice->include_shipping == 1 && $invoice->show_shipping_on_invoice == 1){ ?>
								<span class="bold"><?php echo _l('ship_to'); ?>:</span>
								<address>
									<?php echo $invoice->shipping_street; ?><br>
									<?php echo $invoice->shipping_city; ?>, <?php echo $invoice->shipping_state; ?><br/><?php echo get_country_short_name($invoice->shipping_country); ?>,<?php echo $invoice->shipping_zip; ?>
								</address>
								<?php } ?>
								<p>
									<span><span class="text-muted"><?php echo _l('invoice_data_date'); ?></span> <?php echo $invoice->date; ?></span>
									<?php if(!empty($invoice->duedate)){ ?>
									<br /><span class="mtop20"><span class="text-muted"><?php echo _l('invoice_data_duedate'); ?></span> <?php echo $invoice->duedate; ?></span>
									<?php } ?>
									<?php if($invoice->sale_agent != 0){
										if(get_option('show_sale_agent_on_invoices') == 1){ ?>
										<br /><span class="mtop20">
										<span class="text-muted"><?php echo _l('sale_agent_string'); ?>: </span>
										<?php echo get_staff_full_name($invoice->sale_agent); ?>
									</span>
									<?php }
								}
								?>
								<?php
									// check for invoice custom fields which is checked show on pdf
								$pdf_custom_fields = get_custom_fields('invoice',array('show_on_pdf'=>1));
								foreach($pdf_custom_fields as $field){
									$value = get_custom_field_value($invoice->id,$field['id'],'invoice');
									if($value == ''){continue;} ?>
									<br /><span class="mtop20">
									<span class="text-muted"><?php echo $field['name']; ?>: </span>
									<?php echo $value; ?>
								</span>
								<?php
							}
							?>
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table items">
								<thead>
									<tr>
										<th>#</th>
										<th class="description"><?php echo _l('invoice_table_item_heading'); ?></th>
										<th><?php echo _l('invoice_table_quantity_heading'); ?></th>
										<th><?php echo _l('invoice_table_rate_heading'); ?></th>
										<th><?php echo _l('invoice_table_tax_heading'); ?></th>
										<th><?php echo _l('invoice_table_amount_heading'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php if(isset($invoice)){
										$_tax_tr = '';
										$taxes = array();
										$i = 1;
										foreach($invoice->items as $item){
											$_item = '';
											$_item .= '<tr>';
											$_item .= '<td>' .$i. '</td>';
											$_item .= '<td class="bold description">'.$item['description'].'<br /><span class="text-muted">'.$item['long_description'].'</span></td>';
											$_item .= '<td>'.floatVal($item['qty']).'</td>';
											$_item .= '<td>'._format_number($item['rate']).'</td>';
											$taxrate = ($item['taxrate'] !== null ? $item['taxrate'] : 0);
											$_item .= '<td>'.$taxrate.'%</td>';
											$_item .= '<td class="amount">'._format_number(($item['qty'] * $item['rate'])).'</td>';
											$_item .= '</tr>';
											echo $_item;
											if($item['taxid']){
												if(!array_key_exists($item['taxid'],$taxes)) {
													if($item['taxrate'] != null){
														$calculated_tax = (($item['qty'] * $item['rate']) / 100 * $item['taxrate']);
														$taxes[$item['taxid']] = $calculated_tax;
													}
												} else {
													$taxes[$item['taxid']] += $calculated_tax = (($item['qty'] * $item['rate']) / 100 * $item['taxrate']);
												}
											}
											$i++;
										}
									} ?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-md-4 col-md-offset-8">
						<table class="table text-right">
							<tbody>
								<tr id="subtotal">
									<td><span class="bold"><?php echo _l('invoice_subtotal'); ?></span>
									</td>
									<td class="subtotal">
										<?php
										if(isset($invoice)){
											echo _format_number($invoice->subtotal,$invoice->symbol);
											echo form_hidden('subtotal',$invoice->subtotal);
										}
										?>
									</td>
								</tr>
								<?php if($invoice->discount_percent != 0){ ?>
								<tr>
									<td>
										<span class="bold"><?php echo _l('invoice_discount'); ?> (<?php echo $invoice->discount_percent; ?>%)</span>
									</td>
									<td class="discount">
										<?php echo '-' . _format_number($invoice->discount_total); ?>
									</td>
								</tr>
								<?php } ?>
								<?php
								if(isset($invoice)){
									foreach($taxes as $taxid => $total){
										$_tax = get_tax_by_id($taxid);
										if($invoice->discount_percent != 0 && $invoice->discount_type == 'before_tax'){
											$total_tax_calculated = ($total * $invoice->discount_percent) / 100;
											$total = ($total - $total_tax_calculated);
										}
										echo '<tr class="tax-area"><td>'.$_tax->name.'('.$_tax->taxrate.'%)</td><td>'._format_number($total,$invoice->symbol).'</td></tr>';
									}
								}
								?>
								<?php if($invoice->adjustment != '0.00'){ ?>
								<tr>
									<td>
										<span class="bold"><?php echo _l('invoice_adjustment'); ?></span>
									</td>
									<td class="adjustment">
										<?php echo _format_number($invoice->adjustment); ?>
									</td>
								</tr>
								<?php } ?>
								<tr>
									<td><span class="bold"><?php echo _l('invoice_total'); ?></span>
									</td>
									<td class="total">
										<?php
										if(isset($invoice)){
											echo format_money($invoice->total,$invoice->symbol);
											echo form_hidden('subtotal',$invoice->total);
										}
										?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<?php if(count($attachments) > 0){ ?>
				<?php foreach($attachments as $attachment){ ?>
				<div class="invoice-attachment-wrapper row mbot15">
					<div class="col-md-8">
						<div class="pull-left"><i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i></div>
						<a href="<?php echo site_url('download/file/invoice_attachment/'.$attachment['id']); ?>"><?php echo $attachment['file_name']; ?></a>
						<br />
						<small class="text-muted"> <?php echo $attachment['filetype']; ?></small>
					</div>
					<div class="col-md-4 text-right">
						<a href="#" class="text-danger" onclick="delete_invoice_attachment(this,<?php echo $attachment['id']; ?>); return false;"><i class="fa fa-trash-o"></i></a>
					</div>
				</div>
				<?php } ?>
				<?php } ?>
				<hr />
				<?php if($invoice->clientnote != ''){ ?>
				<div class="col-md-12 row mtop15">
					<p class="bold text-muted"><?php echo _l('invoice_note'); ?></p>
					<p><?php echo $invoice->clientnote; ?></p>
				</div>
				<?php } ?>
				<?php if($invoice->terms != ''){ ?>
				<div class="col-md-12 row mtop15">
					<p class="bold text-muted"><?php echo _l('terms_and_conditions'); ?></p>
					<p><?php echo $invoice->terms; ?></p>
				</div>
				<?php } ?>
				<div class="col-md-12 no-padding animated fadeIn">
					<?php
					$total_payments = count($invoice->payments);
					if($total_payments > 0){ ?>
					<h4 class="bold"><?php echo _l('invoice_received_payments'); ?></h4>
					<div class="row col-md-12">
						<?php include_once(APPPATH . 'views/admin/invoices/invoice_payments_table.php'); ?>
					</div>
					<?php } else { ?>
					<h5 class="bold mtop15 pull-left text-center"><?php echo _l('invoice_no_payments_found'); ?></h5>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<div role="tabpanel" class="tab-pane ptop10" id="tab_tasks">
		<?php init_relation_tasks_table(array('data-new-rel-id'=>$invoice->id,'data-new-rel-type'=>'invoice')); ?>
	</div>
	<div role="tabpanel" class="tab-pane ptop10" id="tab_activity">
		<div class="row">
			<div class="col-md-12">
				<?php foreach($activity as $activity){
					?>
				<div class="display-block">
					<?php if(is_numeric($activity['staffid'])){ ?>
					<a href="<?php echo admin_url('profile/'.$activity['staffid']); ?>"><?php echo staff_profile_image($activity['staffid'],array('staff-profile-image-small','pull-left mright10')); ?></a>
					<?php } ?>
					<div class="media-body">
						<div class="display-block">
							<?php
									// Version 1.0.5 fix
							if(is_numeric($activity['staffid'])
								&& strpos($activity['description'],get_staff_full_name($activity['staffid'])) !== false ){
								echo $activity['description'];
						} else {
							if(is_numeric($activity['staffid'])){
								echo get_staff_full_name($activity['staffid']) . ' ' . $activity['description'];
							} else {
								echo $activity['description'];
							}
						}
						?>
					</div>
					<small class="text-muted"><?php echo _dt($activity['date']); ?></small>
				</div>
				<hr />
			</div>
			<?php } ?>
		</div>
	</div>
</div>
</div>

</div>
</div>
</div>

<script>
	initDataTable('.table-rel-tasks', admin_url +'tasks/init_relation_tasks/<?php echo $invoice->id; ?>/invoice', 'tasks');
</script>
<?php $this->load->view('admin/invoices/invoice_send_to_client'); ?>
