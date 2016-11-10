<div class="col-md-12 page-pdf-html-logo">
	<?php get_company_logo(); ?>
	<?php if(is_staff_logged_in()){ ?>
	<a href="<?php echo admin_url(); ?>invoices/list_invoices/<?php echo $invoice->id; ?>" class="btn btn-info pull-right"><?php echo _l('goto_admin_area'); ?></a>
	<?php } ?>
</div>
<div class="clearfix"></div>
<div class="panel_s mtop20 animated fadeIn">
	<div class="panel-body">
		<div class="col-md-10 col-md-offset-1">
			<div class="row">
				<div class="col-md-6">
					<div class="mtop10 display-block">
						<?php echo format_invoice_status($invoice->status,'',true,true); ?>
					</div>
				</div>
				<div class="col-md-6 text-right">
					<?php echo form_open($this->uri->uri_string()); ?>
					<input type="submit" name="invoicepdf" class="btn btn-primary" value="<?php echo _l('clients_invoice_html_btn_download'); ?>">
					<?php echo form_close(); ?>
				</div>
			</div>
			<div class="row mtop40">
				<div class="col-md-6">
					<h4 class="bold"><?php echo format_invoice_number($invoice->id); ?></h4>
					<address>
						<span class="bold"><?php echo get_option('invoice_company_name'); ?></span><br>
						<?php echo get_option('invoice_company_address'); ?><br>
						<?php echo get_option('invoice_company_city'); ?>, <?php echo get_option('invoice_company_country_code'); ?> <?php echo get_option('invoice_company_postal_code'); ?><br>
						<?php if(get_option('invoice_company_phonenumber') != ''){ ?>
						<abbr title="Phone">P:</abbr> <?php echo get_option('invoice_company_phonenumber'); ?>
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
						<span class="bold"><?php echo $invoice->client->company; ?></span><br>
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
					<!-- shipping details -->
					<?php if($invoice->include_shipping == 1 && $invoice->show_shipping_on_invoice == 1){ ?>
					<span class="bold"><?php echo _l('ship_to'); ?>:</span>
					<address>
						<?php echo $invoice->shipping_street; ?><br>
						<?php echo $invoice->shipping_city; ?>, <?php echo $invoice->shipping_state; ?><br/><?php echo get_country_short_name($invoice->shipping_country); ?>,<?php echo $invoice->shipping_zip; ?>
					</address>
					<?php } ?>
					<p>
						<span><span class="text-muted"><?php echo _l('invoice_data_date'); ?></span> <?php echo $invoice->date; ?></span><br>
						<?php if(!empty($invoice->duedate)){ ?>
						<span class="mtop20"><span class="text-muted"><?php echo _l('invoice_data_duedate'); ?></span> <?php echo $invoice->duedate; ?></span>
						<?php } ?>
						<?php if($invoice->sale_agent != 0){
							if(get_option('show_sale_agent_on_invoices') == 1){ ?>
							<br /><span class="mtop20">
							<span class="text-muted"><?php echo _l('sale_agent_string'); ?>:</span>
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

		<div class="col-md-6 col-md-offset-6">
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
							echo '<tr class="tax-area"><td>'.$_tax->name.'('._format_number($_tax->taxrate).'%)</td><td>'._format_number($total,$invoice->symbol).'</td></tr>';
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
		<?php if(!empty($invoice->clientnote)){ ?>
		<div class="col-md-12">
			<?php echo _l('invoice_note'); ?><br /><?php echo $invoice->clientnote; ?>
		</div>
		<?php } ?>
		<?php if(!empty($invoice->terms)){ ?>
		<div class="col-md-12">
			<hr />
			<?php echo _l('terms_and_conditions'); ?><br /><?php echo $invoice->terms; ?>
		</div>
		<?php } ?>
		<div class="col-md-12 mtop25">
			<?php
			$total_payments = count($invoice->payments);
			if($total_payments > 0){ ?>
			<h4 class="bold"><?php echo _l('invoice_received_payments'); ?></h4>
			<table class="table table-hover">
				<thead>
					<tr>
						<th><?php echo _l('invoice_payments_table_number_heading'); ?></th>
						<th><?php echo _l('invoice_payments_table_mode_heading'); ?></th>
						<th><?php echo _l('invoice_payments_table_date_heading'); ?></th>
						<th><?php echo _l('invoice_payments_table_amount_heading'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($invoice->payments as $payment){ ?>
					<tr>
						<td>
							<span class="pull-left"><?php echo $payment['paymentid']; ?></span>
							<?php echo form_open($this->uri->uri_string()); ?>
							<button type="submit" value="<?php echo $payment['paymentid']; ?>" class="btn btn-icon btn-default pull-right" name="paymentpdf"><i class="fa fa-file-pdf-o"></i></button>
							<?php echo form_close(); ?>
						</td>
						<td><?php echo $payment['name']; ?></td>
						<td><?php echo _d($payment['date']); ?></td>
						<td><?php echo $payment['amount']; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php } else { ?>
			<h5 class="bold mtop15 pull-left"><?php echo _l('invoice_no_payments_found'); ?></h5>
			<?php } ?>
		</div>
		<?php if ($invoice->status != 2 && $invoice->total > 0){ ?>
		<div class="col-md-12">
			<div class="row">
				<?php if(found_invoice_mode($payment_modes,$invoice->id,false)) { ?>
				<hr />
				<div class="col-md-6">
					<h4 class="bold"><?php echo _l('invoice_html_online_payment'); ?></h4>
					<?php echo form_open($this->uri->uri_string(),array('id'=>'online_payment_form')); ?>
					<?php foreach($payment_modes as $mode){
						if(!is_numeric($mode['id']) && !empty($mode['id'])) {
							if(!is_payment_mode_allowed_for_invoice($mode['id'],$invoice->id)){
								continue;
							}
							?>
							<div class="radio radio-primary">
								<input type="radio" value="<?php echo $mode['id']; ?>" name="paymentmode">
								<label><?php echo $mode['name']; ?></label>
							</div>
							<?php if(!empty($mode['description'])){ ?>
							<div class="mbot15">
								<?php echo $mode['description']; ?>
							</div>
							<?php }
						}
					} ?>
					<div class="form-group">

						<?php if(get_option('allow_payment_amount_to_be_modified') == 1){ ?>
						<label for="amount" class="control-label"><?php echo _l('invoice_html_amount'); ?></label>
						<input type="number" data-total="<?php echo get_invoice_total_left_to_pay($invoice->id,$invoice->total); ?>" name="amount" class="form-control" value="<?php echo get_invoice_total_left_to_pay($invoice->id,$invoice->total); ?>">
						<?php } else {
							echo '<hr />';
							echo '<span class="bold">' . _l('invoice_html_total_pay',format_money(get_invoice_total_left_to_pay($invoice->id,$invoice->total),$invoice->symbol)) . '</span>';
						} ?>
					</div>
					<input type="submit" name="make_payment" class="btn btn-success" value="<?php echo _l('invoice_html_online_payment_button_text'); ?>">
					<input type="hidden" name="hash" value="<?php echo $hash; ?>">
					<?php echo form_close(); ?>
				</div>
				<?php } ?>
				<?php if(found_invoice_mode($payment_modes,$invoice->id)) {?>
				<div class="col-md-6">
					<h4 class="bold"><?php echo _l('invoice_html_offline_payment'); ?></h4>
					<?php foreach($payment_modes as $mode){
						if(is_numeric($mode['id'])) {
							if(!is_payment_mode_allowed_for_invoice($mode['id'],$invoice->id)){
								continue;
							}
							?>
							<h3 class="bold"><?php echo $mode['name']; ?></h3>
							<?php if(!empty($mode['description'])){ ?>
							<div class="mbot15">
								<?php echo $mode['description']; ?>
							</div>
							<?php }
						}
					} ?>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
</div>
</div>
