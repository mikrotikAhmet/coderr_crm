<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <ul class="nav nav-tabs no-margin" role="tablist">

            </ul>
                <div class="panel_s">
                    <div class="panel-heading">
                        <?php echo $title; ?>
                    </div>
                    <div class="panel-body">
                        <?php if(isset($affiliate)){ ?>
                            <?php echo form_hidden( 'isedit'); ?>
                            <?php echo form_hidden( 'affiliateid',$affiliate->affiliateid); ?>
                            <div class="clearfix"></div>
                        <?php } ?>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="profile">
                                <div class="row">
                                    <?php echo form_open($this->uri->uri_string(),array('class'=>'affiliate-form')); ?>
                                    <div class="col-md-12">
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li role="presentation" class="active">
                                                <a href="#contact_info" aria-controls="contact_info" role="tab" data-toggle="tab">
                                                    <?php echo _l( 'customer_profile_details'); ?>
                                                </a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#leads" aria-controler="leads" role="tab" data-toggle="tab">
                                                    Leads
                                                </a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#affiliate_attachments" aria-controler="affiliate_attachments" role="tab" data-toggle="tab">
                                                    Files
                                                </a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#affiliate_advanced" aria-controler="affiliate_advanced" role="tab" data-toggle="tab">
                                                    <?php echo _l('advanced_options'); ?>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                            <div role="tabpanel" class="tab-pane ptop10 active" id="contact_info">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <?php $value=( isset($affiliate) ? $affiliate->firstname : ''); ?>
                                                        <?php echo render_input( 'firstname', 'affiliate_firstname',$value); ?>
                                                        <?php $value=( isset($affiliate) ? $affiliate->lastname : ''); ?>
                                                        <?php echo render_input( 'lastname', 'affiliate_lastname',$value); ?>
                                                        <?php $value=( isset($affiliate) ? $affiliate->email : ''); ?>
                                                        <?php echo render_input( 'email', 'affiliate_email',$value, 'email'); ?>
                                                        <?php $value=( isset($affiliate) ? $affiliate->company : ''); ?>
                                                        <?php echo render_input( 'company', 'affiliate_company',$value); ?>
                                                        <?php $value=( isset($affiliate) ? $affiliate->vat : ''); ?>
                                                        <?php echo render_input( 'vat', 'affiliate_vat_number',$value); ?>
                                                        <div class="affiliate_password_set_wrapper">
                                                            <label for="password" class="control-label">
                                                                <?php echo _l( 'affiliate_password'); ?>
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control password" id="password" name="password">
                                                                <span class="input-group-addon">
                                                                    <a href="#" class="generate_password" onclick="generatePassword(this);return false;"><i class="fa fa-refresh"></i></a>
                                                                </span>
                                                            </div>
                                                            <?php if(isset($affiliate) && $affiliate->last_password_change != NULL){ ?>
                                                                <p class="text-muted">
                                                                    <?php echo _l( 'affiliate_password_change_populate_note'); ?>
                                                                </p>
                                                                <?php echo _l( 'affiliate_password_last_changed'); ?>
                                                                <?php echo time_ago($affiliate->last_password_change); ?>
                                                            <?php } ?>
                                                        </div>
                                                        <?php if(!isset($affiliate)){ ?>
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" name="donotsendwelcomeemail">
                                                                <label>
                                                                    <?php echo _l( 'affiliate_do_not_send_welcome_email'); ?>
                                                                </label>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="checkbox" name="send_set_password_email">
                                                            <label>
                                                                <?php echo _l( 'affiliate_send_set_password_email'); ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <?php $value=( isset($affiliate) ? $affiliate->address : ''); ?>
                                                        <?php echo render_input( 'address', 'affiliate_address',$value); ?>
                                                        <?php $value=( isset($affiliate) ? $affiliate->city : ''); ?>
                                                        <?php echo render_input( 'city', 'affiliate_city',$value); ?>
                                                        <?php $value=( isset($affiliate) ? $affiliate->state : ''); ?>
                                                        <?php echo render_input( 'state', 'affiliate_state',$value); ?>
                                                        <?php $value=( isset($affiliate) ? $affiliate->zip : ''); ?>
                                                        <?php echo render_input( 'zip', 'affiliate_postal_code',$value); ?>
                                                        <?php $value=( isset($affiliate) ? $affiliate->phonenumber : ''); ?>
                                                        <?php echo render_input( 'phonenumber', 'affiliate_phonenumber',$value); ?>
                                                        <div class="form-group">
                                                            <label for="country" class="control-label">
                                                                <?php if(isset($affiliate)){ if(file_exists(FCPATH . 'assets/images/country-flags/'.$affiliate->iso2 . '.png')){ ?>
                                                                    <img src="<?php echo site_url('assets/images/country-flags/'.$affiliate->iso2 . '.png'); ?>" alt="<?php echo $affiliate->short_name; ?>">
                                                                <?php } } ?> Country
                                                            </label>
                                                            <select name="country" class="form-control selectpicker" id="country" data-live-search="true">
                                                                <option value=""></option>
                                                                <?php
                                                                $countries= get_all_countries();
                                                                $customer_default_country = get_option('customer_default_country');
                                                                foreach($countries as $country){ $selected='' ;
                                                                    if(isset($affiliate)){
                                                                        if($affiliate->country == $country['country_id']){ $selected = 'selected'; }
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
                                                        <?php $rel_id=( isset($affiliate) ? $affiliate->affiliateid : false); ?>
                                                        <?php echo render_custom_fields( 'affiliates',$rel_id); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div role="tabpanel" class="tab-pane ptop10" id="leads">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <?php
                                                        $table_data = array(
                                                            'Name',
                                                            'Options'
                                                        );
                                                        render_datatable($table_data,'affiliate-clients');
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div role="tabpanel" class="tab-pane ptop10" id="affiliate_attachments">
                                                <?php echo form_open_multipart(admin_url('uploads/upload_attachment/'.$affiliate->affiliateid),array('class'=>'dropzone','id'=>'affiliate-attachments-upload')); ?>
                                                <input type="file" name="file" multiple />
                                                <?php echo form_close(); ?>
                                                <div class="attachments">

                                                </div>
                                            </div>
                                            <div role="tabpanel" class="tab-pane ptop10" id="affiliate_advanced">
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
                                                                    if(isset($affiliate)){
                                                                        if($affiliate->default_language == $language){
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
                                                            if(isset($affiliate)){
                                                                if($currency['id'] == $affiliate->default_currency){
                                                                    $selected = $currency['id'];
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        <?php echo render_select('default_currency',$currencies,array('id','symbol','name'),'invoice_add_edit_currency',$selected,array('data-none-selected-text'=>_l('system_default_string'))); ?>
                                                        <?php
                                                        $selected = array();
                                                        if(isset($affiliate_groups)){
                                                            foreach($affiliate_groups as $group){
                                                                array_push($selected,$group['groupid']);
                                                            }
                                                        }
                                                        echo render_select('groups_in[]',$groups,array('id','name'),'affiliate_groups',$selected,array('multiple'=>true));
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary mtop20">
                                                <?php echo _l( 'submit'); ?>
                                            </button>
                                        </div>
                                    </div>
                                    <?php echo form_close(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>

    <?php if(isset($client)){ ?>
        include_once(APPPATH . 'views/admin/affiliates/modals/send_file_modal.php');
    <?php } ?>

    var affiliate_id = $('input[name="affiliateid"]').val();
    Dropzone.options.affiliateAttachmentsUpload = {
        paramName: "file",
        addRemoveLinks: false,
        accept: function(file, done) {
            done();
        },
        success: function(file, response) {
            get_affiliate_attachments()
        }
    };

    function get_affiliate_attachments() {
        var affiliateid = $('input[name="affiliateid"]').val();
        if (typeof(affiliateid) != 'undefined') {
            $.get(admin_url + 'affiliates/get_attachments/' + affiliateid, function(response) {
                $('.attachments').html(response);
            });
        }
    }

    function delete_affiliate_main_file(attachment_id, href) {
        $.get(admin_url + 'affiliates/delete_attachment/' + attachment_id, function(response) {
            if (response.success == true) {
                $(href).parents('tr').remove();
            }
        }, 'json');
    }

    initDataTable('.table-affiliate-clients', admin_url + 'affiliates/get_clients/2');
</script>
</body>
</html>
