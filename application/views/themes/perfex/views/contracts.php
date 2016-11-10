<div class="panel_s">
	<div class="panel-heading">
		<?php echo _l('clients_contracts'); ?>
	</div>
	<div class="panel-body">
		<?php
        $table_data = array(
         _l('clients_contracts_dt_subject'),
         _l('clients_contracts_type'),
         _l('clients_contracts_dt_start_date'),
         _l('clients_contracts_dt_end_date'),
         _l('contract_attachments'),
         );
        $custom_fields = get_custom_fields('contracts',array('show_on_client_portal'=>1));
        foreach($custom_fields as $field){
            array_push($table_data,$field['name']);
        }
        render_datatable($table_data,'contracts');
        ?>
    </div>
</div>
