				<div class="col-md-12 no-padding animated fadeIn">
					<div class="panel_s">
						<?php echo form_open('admin/invoices/record_payment'); ?>
						<?php echo form_hidden('invoiceid',$invoice->id); ?>
						<div class="panel-body">
							<h4 class="bold no-margin"><?php echo _l('record_payment_for_invoice'); ?> <?php echo format_invoice_number($invoice->id); ?></h4>
							<hr />
							<div class="row">
								<div class="col-md-6">
									<?php echo render_input('amount','record_payment_amount_received',get_invoice_total_left_to_pay($invoice->id,$invoice->total)); ?>
									<?php echo render_date_input('date','record_payment_date',date('Y-m-d')); ?>
									<div class="form-group">
										<label for="paymentmode" class="control-label"><?php echo _l('payment_mode'); ?></label>
										<select class="selectpicker display-block" name="paymentmode" data-width="100%" data-live-search="true">
											<option value=""></option>
											<?php foreach($payment_modes as $mode){ ?>
											<?php if(is_payment_mode_allowed_for_invoice($mode['id'],$invoice->id)){ ?>
											<option value="<?php echo $mode['id']; ?>"><?php echo $mode['name']; ?></option>
											<?php } ?>
											<?php } ?>
										</select>
									</div>
									<div class="checkbox checkbox-primary mtop15 do_not_redirect hide inline-block">
										<input type="checkbox" name="do_not_redirect" checked>
										<label for="do_not_redirect">Do not redirect me to the payment processor</label>
									</div>
								</div>
								<div class="col-md-6">
									<label for="note" class="control-label"><?php echo _l('record_payment_leave_note'); ?></label>
									<textarea name="note" class="form-control" rows="7" placeholder="<?php echo _l('invoice_record_payment_note_placeholder'); ?>" id="note"></textarea>
								</div>
							</div>
							<div class="pull-right mtop15">
								<a href="#" class="btn btn-danger" onclick="init_invoice(<?php echo $invoice->id; ?>); return false;"><?php echo _l('cancel'); ?></a>
								<button type="submit" class="btn btn-success"><?php echo _l('submit'); ?></button>
							</div>
							<?php
							if($payments){ ?>
							<div class="mtop25 inline-block full-width">
								<h5 class="bold"><?php echo _l('invoice_payments_received'); ?></h5>
								<?php include_once(APPPATH . 'views/admin/invoices/invoice_payments_table.php'); ?>
							</div>
							<?php } ?>
						</div>
						<?php echo form_close(); ?>
					</div>
				</div>
				<script>
					init_datepicker();
					_validate_form($('form'),{amount:'required',date:'required',paymentmode:'required'});
					init_selectpicker();
				</script>
