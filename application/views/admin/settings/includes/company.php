    <div role="tabpanel" class="tab-pane" id="company_info">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="bold">
                            <?php echo _l('settings_sales_company_info_heading'); ?>
                        </h4>
                    </div>
                    <div class="col-md-4 text-right">
                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal">
                            <?php echo _l('new_company_field'); ?>
                        </button>
                    </div>
                </div>
                <p class="text-muted">
                    <?php echo _l('settings_sales_company_info_note'); ?>
                </p>
                <?php echo render_input('settings[invoice_company_name]','settings_sales_company_name',get_option('invoice_company_name')); ?>
                <?php echo render_input('settings[invoice_company_address]','settings_sales_address',get_option('invoice_company_address')); ?>
                <?php echo render_input('settings[invoice_company_city]','settings_sales_city',get_option('invoice_company_city')); ?>
                <?php echo render_input('settings[invoice_company_country_code]','settings_sales_country_code',get_option('invoice_company_country_code')); ?>
                <?php echo render_input('settings[invoice_company_postal_code]','settings_sales_postal_code',get_option('invoice_company_postal_code')); ?>
                <?php echo render_input('settings[invoice_company_phonenumber]','settings_sales_phonenumber',get_option('invoice_company_phonenumber')); ?>
                <?php $custom_company_fields = get_company_custom_fields();
                foreach($custom_company_fields as $field){ ?>
                <div class="option">
                    <label for=""><?php echo $field['label']; ?></label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="settings[<?php echo $field["name"]; ?>]" value="<?php echo $field['value']; ?>">
                        <div class="input-group-addon">
                            <a href="#" onclick="delete_option(this,<?php echo $field['id']; ?>); return false;"><i class="fa fa-remove"></i></a>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-question-circle" data-toggle="tooltip" title="<?php echo _l('new_company_field_info'); ?>" data-placement="bottom"></i> <?php echo _l('new_company_field'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo render_input('new_company_field_name','new_company_field_name'); ?>
                            <?php echo render_input('new_company_field_value','new_company_field_value'); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="button" class="btn btn-primary new-company-pdf-field"><?php echo _l('submit'); ?></button>
                </div>
            </div>
        </div>
    </div>
