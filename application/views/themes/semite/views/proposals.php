<div class="panel_s">
    <div class="panel-heading">
        <?php echo _l('proposals'); ?>
    </div>
    <div class="panel-body">
        <?php
        $table_data = array(
            _l('proposal_subject'),
            _l('proposal_total'),
            _l('proposal_open_till'),
            _l('proposal_date'),
            _l('proposal_status'),
            );
        $custom_fields = get_custom_fields('proposal',array('show_on_client_portal'=>1));
        foreach($custom_fields as $field){
            array_push($table_data,$field['name']);
        }
        render_datatable($table_data,'proposals');
        ?>
    </div>
</div>
