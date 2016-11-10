		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th><?php echo _l('payments_table_number_heading'); ?></th>
						<th><?php echo _l('payments_table_mode_heading'); ?></th>
						<th><?php echo _l('payments_table_date_heading'); ?></th>
						<th><?php echo _l('payments_table_amount_heading'); ?></th>
						<th><?php echo _l('options'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($invoice->payments as $payment){ ?>
					<tr class="payment">
						<td><?php echo $payment['paymentid']; ?>
							<?php echo icon_btn('admin/payments/pdf/' . $payment['paymentid'], 'file-pdf-o','btn-default pull-right'); ?>
						</td>
						<td><?php echo $payment['name']; ?>
							<?php if($payment['transactionid']){
								echo '<br />'._l('payments_table_transaction_id',$payment['transactionid']);
							}
							?>
						</td>
						<td><?php echo _d($payment['date']); ?></td>
						<td><?php echo $payment['amount']; ?></td>
						<td>
							<a href="<?php echo admin_url('payments/payment/'.$payment['paymentid']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
							<a href="<?php echo admin_url('invoices/delete_payment/'.$payment['paymentid'] . '/' . $payment['invoiceid']); ?>" class="btn btn-danger btn-icon"><i class="fa fa-remove"></i></a>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>


