<ul class="nav nav-tabs" role="tablist">
	<li role="presentation" class="active">
		<a href="#general" aria-controls="general" role="tab" data-toggle="tab"><?php echo _l('settings_sales_heading_general'); ?></a>
	</li>
	<li role="presentation">
		<a href="#invoice" aria-controls="invoice" role="tab" data-toggle="tab"><?php echo _l('settings_sales_heading_invoice'); ?></a>
	</li>
	<li role="presentation">
		<a href="#estimates" aria-controls="estimates" role="tab" data-toggle="tab"><?php echo _l('settings_sales_heading_estimates'); ?></a>
	</li>
</ul>
<div class="tab-content mtop30">
	<!-- Paypal -->
	<div role="tabpanel" class="tab-pane active" id="general">
		<div class="row">
			<div class="col-md-6">
				<h4 class="bold">
					<?php echo _l('settings_sales_general'); ?>
				</h4>
				<p class="text-muted">
					<p><?php echo _l('settings_sales_general_note'); ?></p>
				</p>
				<hr />
				<?php echo render_input('settings[decimal_separator]','settings_sales_decimal_separator',get_option('decimal_separator')); ?>
				<hr />
				<?php echo render_input('settings[thousand_separator]','settings_sales_thousand_separator',get_option('thousand_separator')); ?>
				<hr />
				<?php echo render_input('settings[number_padding_invoice_and_estimate]','settings_number_padding_invoice_and_estimate',get_option('number_padding_invoice_and_estimate')); ?>
				<hr />

				<?php render_yes_no_option('show_status_on_pdf_ei','show_invoice_estimate_status_on_pdf'); ?>
				<hr />
				<div class="form-group">
					<label for="currency_placement" class="control-label clearfix"><?php echo _l('settings_sales_currency_placement'); ?></label>
					<div class="radio radio-primary radio-inline">
						<input type="radio" name="settings[currency_placement]" value="before" <?php if(get_option('currency_placement') == 'before'){echo 'checked';} ?>>
						<label><?php echo _l('settings_sales_currency_placement_before'); ?> ( $25.00 ) </label>
					</div>
					<div class="radio radio-primary radio-inline">
						<input type="radio" name="settings[currency_placement]" value="after" <?php if(get_option('currency_placement') == 'after'){echo 'checked';} ?>>
						<label><?php echo _l('settings_sales_currency_placement_after'); ?> ( 25.00$ )</label>
					</div>
				</div>
				<?php echo render_select('settings[default_tax]',$taxes,array('id','name'),'settings_default_tax',get_option('default_tax')); ?>
			</div>
		</div>
	</div>
	<div role="tabpanel" class="tab-pane" id="invoice">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="control-label" for="invoice_prefix"><?php echo _l('settings_sales_invoice_prefix'); ?></label>
					<input type="text" name="settings[invoice_prefix]" class="form-control" value="<?php echo get_option('invoice_prefix'); ?>">
				</div>
				<hr />
				<?php echo render_input('settings[invoice_due_after]','settings_sales_invoice_due_after',get_option('invoice_due_after'),'text',array('data-toggle'=>'tooltip','title'=>'invoice_due_after_help')); ?>
				<hr />
				<?php render_yes_no_option('view_invoice_only_logged_in','settings_sales_require_client_logged_in_to_view_invoice'); ?>
				<hr />
				<?php render_yes_no_option('allow_payment_amount_to_be_modified','settings_allow_payment_amount_to_be_modified'); ?>
				<hr />
				<?php render_yes_no_option('delete_only_on_last_invoice','settings_delete_only_on_last_invoice'); ?>
				<hr />

				<?php echo render_input('settings[next_invoice_number]','settings_sales_next_invoice_number',get_option('next_invoice_number'),'text',array('data-toggle'=>'tooltip','title'=>'settings_sales_next_invoice_number_tooltip')); ?>

				<hr />
				<?php echo render_input('settings[invoice_year]','settings_sales_invoice_year',get_option('invoice_year'),'text',array('data-toggle'=>'tooltip','title'=>'settings_sales_invoice_year_tooltip')); ?>
				<hr ?>
				<?php render_yes_no_option('invoice_number_decrement_on_delete','settings_sales_decrement_invoice_number_on_delete','settings_sales_decrement_invoice_number_on_delete_tooltip'); ?>
				<hr />
				<?php render_yes_no_option('show_sale_agent_on_invoices','settings_show_sale_agent_on_invoices'); ?>
				<hr />
				<div class="form-group">
					<label for="invoice_number_format" class="control-label clearfix"><?php echo _l('settings_sales_invoice_number_format'); ?></label>
					<div class="radio radio-primary radio-inline">
						<input type="radio" name="settings[invoice_number_format]" value="1" <?php if(get_option('invoice_number_format') == '1'){echo 'checked';} ?>>
						<label><?php echo _l('settings_sales_invoice_number_format_number_based'); ?></label>
					</div>
					<div class="radio radio-primary radio-inline">
						<input type="radio" name="settings[invoice_number_format]" value="2" <?php if(get_option('invoice_number_format') == '2'){echo 'checked';} ?>>
						<label><?php echo _l('settings_sales_invoice_number_format_year_based'); ?> (YYYY/000001)</label>
					</div>
					<hr />
				</div>
				<?php echo render_textarea('settings[predefined_terms_invoice]','settings_predefined_predefined_term',get_option('predefined_terms_invoice')); ?>
				<?php echo render_textarea('settings[predefined_clientnote_invoice]','settings_predefined_clientnote',get_option('predefined_clientnote_invoice')); ?>
			</div>
		</div>
	</div>
	<div role="tabpanel" class="tab-pane" id="estimates">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="control-label" for="estimate_prefix"><?php echo _l('settings_sales_estimate_prefix'); ?></label>
					<input type="text" name="settings[estimate_prefix]" class="form-control" value="<?php echo get_option('estimate_prefix'); ?>">
				</div>
				<hr />
				<?php render_yes_no_option('delete_only_on_last_estimate','settings_delete_only_on_last_estimate'); ?>
				<hr />
				<?php echo render_input('settings[next_estimate_number]','settings_sales_next_estimate_number',get_option('next_estimate_number')); ?>
				<hr />
				<?php render_yes_no_option('view_estimate_only_logged_in','settings_sales_require_client_logged_in_to_view_estimate'); ?>
				<hr />
				<?php echo render_input('settings[estimate_year]','settings_sales_estimate_year',get_option('estimate_year'),'text',array('data-toggle'=>'tooltip','title'=>'settings_sales_estimate_year_tooltip')); ?>
				<hr />
				<?php render_yes_no_option('estimate_number_decrement_on_delete','settings_sales_decrement_estimate_number_on_delete','settings_sales_decrement_estimate_number_on_delete_tooltip'); ?>
				<hr />
				<?php render_yes_no_option('show_sale_agent_on_estimates','settings_show_sale_agent_on_estimates'); ?>
				<hr />
				<?php render_yes_no_option('estimate_auto_convert_to_invoice_on_client_accept','settings_estimate_auto_convert_to_invoice_on_client_accept'); ?>
				<hr />
				<?php render_yes_no_option('exclude_estimate_from_client_area_with_draft_status','settings_exclude_estimate_from_client_area_with_draft_status'); ?>
				<hr />
				<div class="form-group">
					<label for="estimate_number_format" class="control-label clearfix"><?php echo _l('settings_sales_estimate_number_format'); ?></label>
					<div class="radio radio-primary radio-inline">
						<input type="radio" name="settings[estimate_number_format]" value="1" <?php if(get_option('estimate_number_format') == '1'){echo 'checked';} ?>>
						<label><?php echo _l('settings_sales_estimate_number_format_number_based'); ?></label>
					</div>
					<div class="radio radio-primary radio-inline">
						<input type="radio" name="settings[estimate_number_format]" value="2" <?php if(get_option('estimate_number_format') == '2'){echo 'checked';} ?>>
						<label><?php echo _l('settings_sales_estimate_number_format_year_based'); ?> (YYYY/000001)</label>
					</div>
					<hr />
				</div>
				<?php echo render_textarea('settings[predefined_terms_estimate]','settings_predefined_predefined_term',get_option('predefined_terms_estimate')); ?>
				<?php echo render_textarea('settings[predefined_clientnote_estimate]','settings_predefined_clientnote',get_option('predefined_clientnote_estimate')); ?>
			</div>
		</div>
	</div>
</div>
