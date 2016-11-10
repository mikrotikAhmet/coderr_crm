<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s no-margin">
                    <div class="panel-body">
                        <?php if(isset($client) && $client->leadid != NULL){ ?>
                        <div class="alert alert-info">
                            Customer From <a href="<?php echo admin_url('leads/lead/'.$client->leadid); ?>">Lead</a>
                        </div>
                        <?php } ?>
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs no-margin" role="tablist">
                         <?php if(isset($client)){ ?>
                         <li>
                           <div class="btn-group pull-left btn-customer-options">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                               <i class="fa fa-cog"></i>
                           </button>
                           <ul class="dropdown-menu dropdown-menu-left">
                            <li>
                              <a href="<?php echo admin_url('clients/login_as_client/'.$client->userid); ?>" target="_blank">
                                <i class="fa fa-share-square-o"></i> <?php echo _l('login_as_client'); ?>
                            </a>
                        </li>
                        <li>
                         <a href="<?php echo admin_url('clients/delete/'.$client->userid); ?>" data-toggle="tooltip" data-title="<?php echo _l('client_delete_tooltip'); ?>" data-placement="bottom"><i class="fa fa-remove"></i> <?php echo _l('delete'); ?>
                         </a>
                     </li>
                 </ul>
             </div>
         </li>
         <?php } ?>
         <li role="presentation" class="active">
            <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">
                <?php echo _l( 'client_add_edit_profile'); ?>
            </a>
        </li>
        <?php if(isset($client)) { ?>
        <li role="presentation">
            <a href="#proposals" aria-controls="proposals" role="tab" data-toggle="tab">
                <?php echo _l( 'proposals'); ?>
            </a>
        </li>
        <li role="presentation">
            <a href="#invoices" aria-controls="invoices" role="tab" data-toggle="tab">
                <?php echo _l( 'client_invoices_tab'); ?>
            </a>
        </li>
        <li role="presentation">
            <a href="#estimates" aria-controls="estimates" role="tab" data-toggle="tab">
                <?php echo _l( 'client_estimates_tab'); ?>
            </a>
        </li>
        <li role="presentation">
            <a href="#payments" aria-controls="estimates" role="tab" data-toggle="tab">
                <?php echo _l( 'client_payments_tab'); ?>
            </a>
        </li>
        <li role="presentation">
            <a href="#expenses" aria-controls="estimates" role="tab" data-toggle="tab">
                <?php echo _l( 'client_expenses_tab'); ?>
            </a>
        </li>
        <li role="presentation">
            <a href="#contracts" aria-controls="contracts" role="tab" data-toggle="tab">
                <?php echo _l( 'contracts_invoices_tab'); ?>
            </a>
        </li>
        <li role="presentation">
            <a href="#tickets" aria-controls="tickets" role="tab" data-toggle="tab">
                <?php echo _l( 'contracts_tickets_tab'); ?>
            </a>
        </li>
        <li role="presentation">
            <a href="#reminders" aria-controls="reminders" role="tab" data-toggle="tab">
                <?php echo _l( 'client_reminders_tab'); ?>
            </a>
        </li>
        <li role="presentation">
            <a href="#tab_tasks" aria-controls="tab_tasks" role="tab" data-toggle="tab">
                <?php echo _l( 'tasks'); ?>
            </a>
        </li>
        <li role="presentation">
            <a href="#tab_attachments" aria-controls="tab_attachments" role="tab" data-toggle="tab">
                <?php echo _l( 'customer_attachments'); ?>
            </a>
        </li>
        <li role="presentation">
            <a href="#tab_map" aria-controls="tab_map" role="tab" data-toggle="tab">
                <?php echo _l( 'customer_map'); ?>
            </a>
        </li>
        <li role="presentation">
            <a href="#notes" aria-controls="notes" role="tab" data-toggle="tab">
                <?php echo _l( 'contracts_notes_tab'); ?>
            </a>
        </li>
        <?php } ?>
    </ul>
    <!-- Tab panes -->
</div>
</div>
<div class="panel_s">
    <div class="panel-heading">
        <?php echo $title; ?>
    </div>
    <div class="panel-body">

       <?php if(isset($client)){ ?>
       <?php echo form_hidden( 'isedit'); ?>
       <?php echo form_hidden( 'userid',$client->userid); ?>

       <div class="clearfix"></div>
       <?php } ?>
       <div>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="profile">
                <div class="row">
                    <?php echo form_open($this->uri->uri_string(),array('class'=>'client-form')); ?>
                    <div class="col-md-12">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#contact_info" aria-controls="contact_info" role="tab" data-toggle="tab">
                                    <?php echo _l( 'customer_profile_details'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#billing_and_shipping" aria-controls="billing_and_shipping" role="tab" data-toggle="tab">
                                    <?php echo _l( 'billing_shipping'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#client_permissions" aria-controler="client_permissions" role="tab" data-toggle="tab">
                                   <?php echo _l('customer_permissions'); ?>
                               </a>
                           </li>
                           <li role="presentation">
                            <a href="#client_advanced" aria-controler="client_advanced" role="tab" data-toggle="tab">
                                <?php echo _l('advanced_options'); ?>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane ptop10 active" id="contact_info">
                            <div class="row">
                                <div class="col-md-4">
                                    <?php $value=( isset($client) ? $client->firstname : ''); ?>
                                    <?php echo render_input( 'firstname', 'client_firstname',$value); ?>

                                    <?php $value=( isset($client) ? $client->lastname : ''); ?>
                                    <?php echo render_input( 'lastname', 'client_lastname',$value); ?>

                                    <?php $value=( isset($client) ? $client->email : ''); ?>
                                    <?php echo render_input( 'email', 'client_email',$value, 'email'); ?>

                                    <?php $value=( isset($client) ? $client->company : ''); ?>
                                    <?php echo render_input( 'company', 'client_company',$value); ?>

                                    <?php $value=( isset($client) ? $client->vat : ''); ?>
                                    <?php echo render_input( 'vat', 'client_vat_number',$value); ?>

                                    <?php $value=( isset($client) ? $client->latitude : ''); ?>
                                    <?php echo render_input( 'latitude', 'customer_latitude',$value); ?>

                                    <?php $value=( isset($client) ? $client->longitude : ''); ?>
                                    <?php echo render_input( 'longitude', 'customer_longitude',$value); ?>
                                    <div class="client_password_set_wrapper">
                                        <label for="password" class="control-label">
                                            <?php echo _l( 'client_password'); ?>
                                        </label>
                                        <div class="input-group">
                                            <input type="text" class="form-control password" id="password" name="password">
                                            <span class="input-group-addon">
                                                <a href="#" class="generate_password" onclick="generatePassword(this);return false;"><i class="fa fa-refresh"></i></a>
                                            </span>
                                        </div>
                                        <?php if(isset($client) && $client->last_password_change != NULL){ ?>
                                        <p class="text-muted">
                                            <?php echo _l( 'client_password_change_populate_note'); ?>
                                        </p>
                                        <?php echo _l( 'client_password_last_changed'); ?>
                                        <?php echo time_ago($client->last_password_change); ?>
                                        <?php } ?>
                                    </div>
                                    <?php if(!isset($client)){ ?>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="donotsendwelcomeemail">
                                        <label>
                                            <?php echo _l( 'client_do_not_send_welcome_email'); ?>
                                        </label>
                                    </div>
                                    <?php } ?>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="send_set_password_email">
                                        <label>
                                            <?php echo _l( 'client_send_set_password_email'); ?>
                                        </label>
                                    </div>

                                </div>
                                <div class="col-md-4">
                                    <?php $value=( isset($client) ? $client->address : ''); ?>
                                    <?php echo render_input( 'address', 'client_address',$value); ?>

                                    <?php $value=( isset($client) ? $client->city : ''); ?>
                                    <?php echo render_input( 'city', 'client_city',$value); ?>

                                    <?php $value=( isset($client) ? $client->state : ''); ?>
                                    <?php echo render_input( 'state', 'client_state',$value); ?>

                                    <?php $value=( isset($client) ? $client->zip : ''); ?>
                                    <?php echo render_input( 'zip', 'client_postal_code',$value); ?>

                                    <?php $value=( isset($client) ? $client->phonenumber : ''); ?>
                                    <?php echo render_input( 'phonenumber', 'client_phonenumber',$value); ?>

                                    <div class="form-group">
                                        <label for="country" class="control-label">
                                            <?php if(isset($client)){ if(file_exists(FCPATH . 'assets/images/country-flags/'.$client->iso2 . '.png')){ ?>
                                            <img src="<?php echo site_url('assets/images/country-flags/'.$client->iso2 . '.png'); ?>" alt="<?php echo $client->short_name; ?>">
                                            <?php } } ?> Country
                                        </label>
                                        <select name="country" class="form-control selectpicker" id="country" data-live-search="true">
                                           <option value=""></option>
                                           <?php
                                           $countries= get_all_countries();
                                           $customer_default_country = get_option('customer_default_country');
                                           foreach($countries as $country){ $selected='' ;
                                           if(isset($client)){
                                            if($client->country == $country['country_id']){ $selected = 'selected'; }
                                        } else {
                                            if($country['country_id'] == $customer_default_country){
                                                $selected = 'selected';
                                            }
                                        } ?>
                                        <option value="<?php echo $country['country_id']; ?>" <?php echo $selected; ?>>
                                            <?php echo $country[ 'short_name']; ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>

                            </div>
                            <div class="col-md-4">
                               <?php $rel_id=( isset($client) ? $client->userid : false); ?>
                               <?php echo render_custom_fields( 'customers',$rel_id); ?>
                           </div>
                       </div>
                   </div>
                   <div role="tabpanel" class="tab-pane ptop10" id="billing_and_shipping">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4><?php echo _l('billing_address'); ?> <a href="#" class="pull-right billing-same-as-customer"><small class="label label-primary"><?php echo _l('customer_billing_same_as_profile'); ?></small></a></h4>
                                    <?php $value=( isset($client) ? $client->billing_street : ''); ?>
                                    <?php echo render_input( 'billing_street', 'billing_street',$value); ?>

                                    <?php $value=( isset($client) ? $client->billing_city : ''); ?>
                                    <?php echo render_input( 'billing_city', 'billing_city',$value); ?>

                                    <?php $value=( isset($client) ? $client->billing_state : ''); ?>
                                    <?php echo render_input( 'billing_state', 'billing_state',$value); ?>

                                    <?php $value=( isset($client) ? $client->billing_zip : ''); ?>
                                    <?php echo render_input( 'billing_zip', 'billing_zip',$value); ?>
                                    <?php $selected=( isset($client) ? $client->billing_country : $customer_default_country ); ?>
                                    <?php echo render_select( 'billing_country',$countries,array( 'country_id',array( 'short_name')), 'billing_country',$selected); ?>
                                </div>
                                <div class="col-md-6">
                                    <h4><?php echo _l('shipping_address'); ?> <a href="#" class="pull-right customer-copy-billing-address"><small class="label label-primary"><?php echo _l('customer_billing_copy'); ?></small></a></h4>
                                    <?php $value=( isset($client) ? $client->shipping_street : ''); ?>
                                    <?php echo render_input( 'shipping_street', 'shipping_street',$value); ?>

                                    <?php $value=( isset($client) ? $client->shipping_city : ''); ?>
                                    <?php echo render_input( 'shipping_city', 'shipping_city',$value); ?>

                                    <?php $value=( isset($client) ? $client->shipping_state : ''); ?>
                                    <?php echo render_input( 'shipping_state', 'shipping_state',$value); ?>

                                    <?php $value=( isset($client) ? $client->shipping_zip : ''); ?>
                                    <?php echo render_input( 'shipping_zip', 'shipping_zip',$value); ?>
                                    <?php $selected=( isset($client) ? $client->shipping_country : $customer_default_country ); ?>
                                    <?php echo render_select( 'shipping_country',$countries,array( 'country_id',array( 'short_name')), 'shipping_country',$selected); ?>
                                </div>
                                <?php if(isset($client) &&
                                (total_rows('tblinvoices',array('clientid'=>$client->userid)) > 0 || total_rows('tblestimates',array('clientid'=>$client->userid)) > 0)){ ?>
                                <div class="col-md-12">
                                    <div class="alert alert-warning">
                                      <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="update_all_other_transactions">
                                        <label>
                                          <?php echo _l('customer_update_address_info_on_invoices'); ?><br />
                                      </label>
                                  </div>
                                  <b><?php echo _l('customer_update_address_info_on_invoices_help'); ?></b>
                              </div>
                          </div>
                          <?php } ?>
                      </div>
                  </div>
              </div>
          </div>
          <div role="tabpanel" class="tab-pane ptop10" id="client_advanced">
            <?php
            $date_formats = get_available_date_formats();
            ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?>
                        </label>
                        <select name="default_language" id="default_language" class="form-control selectpicker">
                            <option value=""><?php echo _l('system_default_string'); ?></option>
                            <?php foreach(list_folders(APPPATH .'language') as $language){
                                $selected = '';
                                if(isset($client)){
                                    if($client->default_language == $language){
                                        $selected = 'selected';
                                    }
                                }
                                ?>
                                <option value="<?php echo $language; ?>" <?php echo $selected; ?>><?php echo ucfirst($language); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php
                        $selected = '';
                        foreach($currencies as $currency){
                            if(isset($client)){
                                if($currency['id'] == $client->default_currency){
                                    $selected = $currency['id'];
                                }
                            }
                        }
                        ?>
                        <?php echo render_select('default_currency',$currencies,array('id','symbol','name'),'invoice_add_edit_currency',$selected,array('data-none-selected-text'=>_l('system_default_string'))); ?>
                        <?php
                        $selected = array();
                        if(isset($customer_groups)){
                            foreach($customer_groups as $group){
                                array_push($selected,$group['groupid']);
                            }
                        }
                        echo render_select('groups_in[]',$groups,array('id','name'),'customer_groups',$selected,array('multiple'=>true));
                        ?>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane ptop10" id="client_permissions">
                <?php foreach($customer_permissions as $permission){ ?>
                <div class="col-md-6 row">
                    <div class="row">
                        <div class="col-md-6 mtop10 border-right">
                            <span class="bold"><?php echo $permission['name']; ?></span>
                        </div>
                        <div class="col-md-6 mtop10">
                        <input type="checkbox" class="switch-box" <?php if(isset($client) && has_customer_permission($permission['short_name'],$client->userid)){echo 'checked';} ?>  value="<?php echo $permission['id']; ?>" data-size="mini" name="permissions[]">
                        </div>
                    </div>
                </div>
                <div class="clearfix">  </div>
                <?php } ?>
            </div>
            <button type="submit" class="btn btn-primary mtop20">
                <?php echo _l( 'submit'); ?>
            </button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
</div>
<?php if(isset($client)){ ?>

<div role="tabpanel" class="tab-pane ptop10" id="proposals">
  <?php
  $table_data = array(
    _l('proposal_subject'),
    _l('proposal_total'),
    _l('proposal_open_till'),
    _l('proposal_date_created'),
    _l('proposal_status'));
  $custom_fields = get_custom_fields('proposal',array('show_on_table'=>1));
  foreach($custom_fields as $field){
    array_push($table_data,$field['name']);
}
render_datatable($table_data,'proposals-client-profile');
?>
</div>
<div role="tabpanel" class="tab-pane ptop10" id="invoices">
    <?php
    $table_data = array( _l( 'client_invoice_number_table_heading'), _l( 'client_invoice_date_table_heading'), _l( 'client_string_table_heading'), _l( 'client_invoice_due_date_table_heading'), _l( 'client_amount_table_heading'), _l( 'client_status_table_heading'));

    $custom_fields = get_custom_fields('invoice',array('show_on_table'=>1));
    foreach($custom_fields as $field){
        array_push($table_data,$field['name']);
    }
    render_datatable($table_data, 'invoices-single-client');
    ?>
</div>
<div class="tab-pane ptop10" id="estimates">
    <?php
    $table_data = array( _l( 'estimate_dt_table_heading_number'), _l( 'estimate_dt_table_heading_date'), _l( 'reference_no'), _l( 'estimate_dt_table_heading_client'), _l( 'estimate_dt_table_heading_expirydate'), _l( 'estimate_dt_table_heading_amount'), _l( 'estimate_dt_table_heading_status'));

    $custom_fields = get_custom_fields('estimate',array('show_on_table'=>1));
    foreach($custom_fields as $field){
        array_push($table_data,$field['name']);
    }
    render_datatable($table_data, 'estimates-single-client');
    ?>
</div>
<div class="tab-pane ptop10" id="payments">
    <?php render_datatable(array( _l( 'payments_table_number_heading'), _l( 'payments_table_invoicenumber_heading'), _l( 'payments_table_mode_heading'), _l( 'payments_table_client_heading'), _l( 'payments_table_amount_heading'), _l( 'payments_table_date_heading'), _l( 'options') ), 'payments-single-client'); ?>
</div>
<div class="tab-pane ptop10" id="expenses">
    <?php
    $table_data = array( _l( 'expense_dt_table_heading_category'), _l( 'expense_dt_table_heading_amount'), _l( 'expense_dt_table_heading_date'), _l( 'expense_dt_table_heading_customer'), _l( 'expense_dt_table_heading_reference_no'), _l( 'expense_dt_table_heading_payment_mode'));

    $custom_fields = get_custom_fields('expenses',array('show_on_table'=>1));
    foreach($custom_fields as $field){
        array_push($table_data,$field['name']);
    }
    render_datatable($table_data, 'expenses-single-client');
    ?>
</div>
<div role="tabpanel" class="tab-pane ptop10" id="tab_attachments">
    <?php echo form_open_multipart(admin_url('clients/upload_attachment/'.$client->userid),array('class'=>'dropzone','id'=>'client-attachments-upload')); ?>
    <input type="file" name="file" multiple />
    <?php echo form_close(); ?>
    <div class="attachments">

    </div>
</div>
<div role="tabpanel" class="tab-pane ptop10" id="notes">
    <a href="#" class="btn btn-success btn-icon pull-right" onclick="slideToggle('.usernote'); return false;"><i class="fa fa-plus"></i>
    </a>
    <div class="clearfix"></div>
    <div class="row usernote hide">
        <div class="col-md-12">
            <?php echo form_open(admin_url( 'misc/add_user_note/'.$client->userid . '/0')); ?>
            <?php echo render_textarea( 'description', 'note_description', '',array( 'rows'=>5)); ?>
            <button class="btn btn-primary pull-right">
                <?php echo _l( 'submit'); ?>
            </button>
            <?php echo form_close(); ?>
        </div>
    </div>
    <?php if(count($user_notes)> 0){ ?>
    <table class="table mtop10 animated fadeIn">
        <thead>
            <tr>
                <th>
                    <?php echo _l( 'clients_notes_table_description_heading'); ?>
                </th>
                <th>
                    <?php echo _l( 'clients_notes_table_addedfrom_heading'); ?>
                </th>
                <th>
                    <?php echo _l( 'clients_notes_table_dateadded_heading'); ?>
                </th>
                <th>
                    <?php echo _l( 'options'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($user_notes as $note){ ?>
            <tr>
                <td>
                    <?php echo $note[ 'description']; ?>
                </td>
                <td>
                    <?php echo '<a href="'.admin_url( 'profile/'.$note[ 'addedfrom']). '">'.$note[ 'firstname'] . ' ' . $note[ 'lastname'] . '</a>' ?>
                </td>
                <td>
                    <?php echo _dt($note[ 'dateadded']); ?>
                </td>
                <td><a href="<?php echo admin_url('misc/remove_user_note/'. $note['usernoteid'] . '/' . $client->userid .'/0'); ?>" class="btn btn-danger btn-icon"><i class="fa fa-remove"></i></a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php } else { ?>
    <p class="no-margin">
        <?php echo _l( 'no_user_notes'); ?>
    </p>
    <?php } ?>
</div>
<div role="tabpanel" class="tab-pane ptop10" id="tickets">
    <?php echo AdminTicketsTableStructure(); ?>
</div>
<div role="tabpanel" class="tab-pane ptop10" id="reminders">
 <a href="#" data-toggle="modal" data-target=".reminder-modal"><i class="fa fa-bell-o"> <?php echo _l('set_reminder'); ?></i></a>
 <?php render_datatable(array( _l( 'reminder_description'), _l( 'reminder_date'), _l( 'reminder_staff'), _l( 'reminder_is_notified'), _l( 'options'), ), 'reminders'); ?>
</div>
<div role="tabpanel" class="tab-pane ptop10" id="contracts">
    <?php render_datatable(array( _l( 'contract_list_client'), _l( 'contract_list_subject'), _l('contract_types_list_name'),_l( 'contract_list_start_date'), _l( 'contract_list_end_date'), _l( 'options'), ), 'contracts-single-client'); ?>
</div>
<div role="tabpanel" class="tab-pane ptop10" id="tab_tasks">
    <?php init_relation_tasks_table(array( 'data-new-rel-id'=>$client->userid,'data-new-rel-type'=>'customer')); ?>
</div>
<div role="tabpanel" class="tab-pane ptop10" id="tab_map">
    <?php if($google_api_key !== ''){ ?>
    <div id="map" class="customer_map"></div>
    <?php } else {
        echo _l('setup_google_api_key_customer_map');
    }?>
</div>
<?php } ?>
</div>
</div>
</div>
</div>
</div>

</div>
</div>
</div>

<?php init_tail(); ?>
<!-- Modals for zip -->
<?php if(isset($client)){
    include_once(APPPATH . 'views/admin/clients/modals/send_file_modal.php');
    include_once(APPPATH . 'views/admin/clients/modals/zip_payments.php');
    include_once(APPPATH . 'views/admin/clients/modals/add_reminder.php');
    include_once(APPPATH . 'views/admin/clients/modals/zip_invoices.php');
    include_once(APPPATH . 'views/admin/clients/modals/zip_estimates.php'); ?>
    <script>
        initDataTable('.table-rel-tasks', admin_url + 'tasks/init_relation_tasks/<?php echo $client->userid; ?>/customer', 'tasks');
    </script>
    <?php if(!empty($google_api_key)){ ?>
    <script>
        var latitude = '<?php echo $client->latitude; ?>';
        var longitude = '<?php echo $client->longitude; ?>';
        var marker = '<?php echo $client->company; ?>';
    </script>
    <script src="<?php echo site_url('assets/js/map.js'); ?>"></script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_api_key; ?>&callback=initMap">
</script>
<?php } ?>
<?php } ?>
<script>
  var customer_id = $('input[name="userid"]').val();
  Dropzone.options.clientAttachmentsUpload = {
      paramName: "file",
      addRemoveLinks: false,
      accept: function(file, done) {
          done();
      },
      success: function(file, response) {
          get_client_attachments()
      }
  };

  $(document).ready(function() {
      get_client_attachments();
      initDataTable('.table-contracts-single-client', admin_url + 'contracts/index/' + customer_id, 'contracts', [5], [5]);
      initDataTable('.table-invoices-single-client', admin_url + 'invoices/list_invoices/false/' + customer_id, 'invoices');
      initDataTable('.table-estimates-single-client', admin_url + 'estimates/list_estimates/false/' + customer_id, 'estimates');
      initDataTable('.table-payments-single-client', admin_url + 'payments/list_payments/' + customer_id, 'payments', [6], [6]);
      initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + customer_id + '/' + 'customer', 'reminders', [4], [4]);
      initDataTable('.table-expenses-single-client', admin_url + 'expenses/list_expenses/false/' + customer_id, 'expenses', 'undefined', 'undefined');
      initDataTable('.table-proposals-client-profile', admin_url + 'proposals/proposal_relations/' + customer_id + '/customer', 'proposals', 'undefined', 'undefined');
      _validate_form($('.client-form'), {
          firstname: 'required',
          lastname: 'required',
          password: {
              required: {
                  depends: function(element) {
                      var is_edit = $('input[name="isedit"]');
                      var sent_set_password = $('input[name="send_set_password_email"]');
                      if (is_edit.length == 0 && sent_set_password.prop('checked') == false) {
                          return true;
                      }
                  }
              }
          },
          email: {
              required: true,
              email: true,
              remote: {
                  url: site_url + "admin/misc/clients_email_exists",
                  type: 'post',
                  data: {
                      email: function() {
                          return $('input[name="email"]').val();
                      },
                      userid: function() {
                          return customer_id;
                      }
                  }
              }
          }
      });


      $('.billing-same-as-customer').on('click', function(e) {
          e.preventDefault();
          $('input[name="billing_street"]').val($('input[name="address"]').val());
          $('input[name="billing_city"]').val($('input[name="city"]').val());
          $('input[name="billing_state"]').val($('input[name="state"]').val());
          $('input[name="billing_zip"]').val($('input[name="zip"]').val());
          $('select[name="billing_country"]').selectpicker('val', $('select[name="country"]').selectpicker('val'));
      });

      $('.customer-copy-billing-address').on('click', function(e) {
          e.preventDefault();
          $('input[name="shipping_street"]').val($('input[name="billing_street"]').val());
          $('input[name="shipping_city"]').val($('input[name="billing_city"]').val());
          $('input[name="shipping_state"]').val($('input[name="billing_state"]').val());
          $('input[name="shipping_zip"]').val($('input[name="billing_zip"]').val());
          $('select[name="shipping_country"]').selectpicker('val', $('select[name="billing_country"]').selectpicker('val'));
      });


  });

  function get_client_attachments() {
      var clientid = $('input[name="userid"]').val();
      if (typeof(clientid) != 'undefined') {
          $.get(admin_url + 'clients/get_attachments/' + clientid, function(response) {
              $('.attachments').html(response);
          });
      }
  }

  function delete_customer_main_file(attachment_id, href) {
      $.get(admin_url + 'clients/delete_attachment/' + attachment_id, function(response) {
          if (response.success == true) {
              $(href).parents('tr').remove();
          }
      }, 'json');
  }
</script>
</body>

</html>
