<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-heading">
                        <?php echo $title; ?>
                    </div>
                    <div class="panel-body">
                        <?php if(isset($custom_field)){ ?>
                        <a href="<?php echo admin_url('custom_fields/field'); ?>" class="btn btn-success pull-left mbot20 display-block"><?php echo _l('new_custom_field'); ?></a>
                        <div class="clearfix"></div>
                        <?php } ?>
                        <?php echo form_open($this->uri->uri_string()); ?>
                        <label for="fieldto"><?php echo _l('custom_field_add_edit_belongs_top'); ?></label>
                        <select name="fieldto" class="selectpicker" data-width="100%">
                            <option value=""></option>
                            <option value="leads" <?php if(isset($custom_field) && $custom_field->fieldto == 'leads'){echo 'selected';} ?>><?php echo _l('custom_field_leads'); ?></option>
                            <option value="customers" <?php if(isset($custom_field) && $custom_field->fieldto == 'customers'){echo 'selected';} ?>><?php echo _l('custom_field_customers'); ?></option>
                            <option value="staff" <?php if(isset($custom_field) && $custom_field->fieldto == 'staff'){echo 'selected';} ?>><?php echo _l('custom_field_staff'); ?></option>
                            <option value="contracts" <?php if(isset($custom_field) && $custom_field->fieldto == 'contracts'){echo 'selected';} ?>><?php echo _l('custom_field_contracts'); ?></option>
                            <option value="tasks" <?php if(isset($custom_field) && $custom_field->fieldto == 'tasks'){echo 'selected';} ?>><?php echo _l('custom_field_tasks'); ?></option>
                            <option value="expenses" <?php if(isset($custom_field) && $custom_field->fieldto == 'expenses'){echo 'selected';} ?>><?php echo _l('custom_field_expenses'); ?></option>
                            <option value="invoice" <?php if(isset($custom_field) && $custom_field->fieldto == 'invoice'){echo 'selected';} ?>><?php echo _l('custom_field_invoice'); ?></option>
                            <option value="estimate" <?php if(isset($custom_field) && $custom_field->fieldto == 'estimate'){echo 'selected';} ?>><?php echo _l('custom_field_estimate'); ?></option>
                            <option value="proposal" <?php if(isset($custom_field) && $custom_field->fieldto == 'proposal'){echo 'selected';} ?>><?php echo _l('proposal'); ?></option>

                        </select>
                        <div class="clearfix mbot15"></div>
                        <?php $value = (isset($custom_field) ? $custom_field->name : ''); ?>
                        <?php echo render_input('name','custom_field_name',$value); ?>
                        <label for="custom_type"><?php echo _l('custom_field_add_edit_type'); ?></label>
                        <select name="type" class="selectpicker" data-width="100%">
                            <option value=""></option>
                            <option value="input" <?php if(isset($custom_field) && $custom_field->type == 'input'){echo 'selected';} ?>>Input</option>
                            <option value="textarea" <?php if(isset($custom_field) && $custom_field->type == 'textarea'){echo 'selected';} ?>>Textarea</option>
                            <option value="date_picker" <?php if(isset($custom_field) && $custom_field->type == 'date_picker'){echo 'selected';} ?>>Date Picker</option>
                            <option value="select" <?php if(isset($custom_field) && $custom_field->type == 'select'){echo 'selected';} ?>>Select</option>
                        </select>
                        <div class="clearfix mbot15"></div>
                        <?php $value = (isset($custom_field) ? $custom_field->options : ''); ?>
                        <?php echo render_textarea('options','custom_field_add_edit_options',$value,array('rows'=>3,'data-toggle'=>'tooltip','title'=>'custom_field_add_edit_options_tooltip')); ?>
                        <?php $value = (isset($custom_field) ? $custom_field->field_order : ''); ?>
                        <?php echo render_input('field_order','custom_field_add_edit_order',$value,'number'); ?>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="disabled" <?php if(isset($custom_field) && $custom_field->active == 0){echo 'checked';} ?>>
                            <label><?php echo _l('custom_field_add_edit_disabled'); ?></label>
                        </div>
                        <div class="checkbox checkbox-primary" id="required_wrap">
                            <input type="checkbox" name="required" <?php if(isset($custom_field) && $custom_field->required == 1){echo 'checked';} ?>>
                            <label><?php echo _l('custom_field_required'); ?></label>
                        </div>
                        <p class="bold text-info"><?php echo _l('custom_field_visibility'); ?></p>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="show_on_table" <?php if(isset($custom_field) && $custom_field->show_on_table == 1){echo 'checked';} ?>>
                            <label><?php echo _l('custom_field_show_on_table'); ?></label>
                        </div>
                        <div class="checkbox checkbox-primary show-on-pdf <?php if(!isset($custom_field) || (isset($custom_field) && !in_array($custom_field->fieldto,$pdf_fields))){echo 'hide';} ?>">
                            <input type="checkbox" name="show_on_pdf" <?php if(isset($custom_field) && $custom_field->show_on_pdf == 1){echo 'checked';} ?>>
                            <label><?php echo _l('custom_field_show_on_pdf'); ?></label>
                        </div>
                        <div class="checkbox checkbox-primary show-on-client-portal <?php if(!isset($custom_field) || (isset($custom_field) && !in_array($custom_field->fieldto,$client_portal_fields))){echo 'hide';} ?>">
                            <input type="checkbox" name="show_on_client_portal" <?php if(isset($custom_field) && $custom_field->show_on_client_portal == 1){echo 'checked';} ?>>
                            <label><i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('custom_field_show_on_client_portal_help'); ?>"></i> <?php echo _l('custom_field_show_on_client_portal'); ?></label>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right"><?php echo _l('submit'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
   var pdf_fields = <?php echo json_encode($pdf_fields); ?>;
   var client_portal_fields = <?php echo json_encode($client_portal_fields); ?>;
   $(document).ready(function() {
       _validate_form($('form'), {
           fieldto: 'required',
           name: 'required',
           type: 'required'
       });
       $('select[name="fieldto"]').on('change', function() {
           var field = $(this).val();
           if ($.inArray(field, pdf_fields) !== -1) {
               $('.show-on-pdf').removeClass('hide');
           } else {
               $('.show-on-pdf').addClass('hide');
           }

           if ($.inArray(field, client_portal_fields) !== -1) {
               $('.show-on-client-portal').removeClass('hide');
           } else {
               $('.show-on-client-portal').addClass('hide');
           }

       });
     // Editor cant be required
     $('select[name="type"]').on('change', function() {
       var type = $(this).val();
       if (type == 'editor') {
           $('#required_wrap').addClass('hide');
       } else {
           $('#required_wrap').removeClass('hide');
       }
   });
 });
</script>
</body>
</html>
