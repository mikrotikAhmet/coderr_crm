<div class="panel_s">
	<div class="panel-heading">
		<?php echo _l('clients_my_invoices'); ?>
	</div>
	<div class="panel-body">
		<?php
        $table_data = array(
            _l('clients_invoice_dt_number'),
            _l('clients_invoice_dt_date'),
            _l('clients_invoice_dt_duedate'),
            _l('clients_invoice_dt_amount'),
            _l('clients_invoice_dt_status'),
            );
        $custom_fields = get_custom_fields('invoice',array('show_on_client_portal'=>1));
        foreach($custom_fields as $field){
            array_push($table_data,$field['name']);
        }
        render_datatable($table_data,'invoices');

        ?>
    </div>
</div>
