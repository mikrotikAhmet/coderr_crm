<div class="row">
    <div class="col-md-6">
        <?php
        $date_formats = get_available_date_formats();
        ?>
        <div class="form-group">
            <label for="dateformat" class="control-label"><?php echo _l('settings_localization_date_format'); ?></label>
            <select name="settings[dateformat]" id="dateformat" class="form-control selectpicker">
                <?php foreach($date_formats as $key => $val){ ?>
                <option value="<?php echo $key; ?>" <?php if($key == get_option('dateformat')){echo 'selected';} ?>><?php echo $val; ?></option>
                <?php } ?>
            </select>
        </div>
        <hr />
        <div class="form-group">
            <label for="timezones" class="control-label"><?php echo _l('settings_localization_default_timezone'); ?></label>
            <select name="settings[default_timezone]" id="timezones" class="form-control selectpicker" data-live-search="true">
                <?php foreach(get_timezones_list() as $timezone => $val){ ?>
                <option value="<?php echo $timezone; ?>" <?php if(get_option('default_timezone') == $timezone){echo 'selected';} ?>><?php echo $val; ?></option>
                <?php } ?>
            </select>
        </div>
        <hr />
        <div class="form-group">
            <label for="active_language" class="control-label"><?php echo _l('settings_localization_default_language'); ?></label>
            <select name="settings[active_language]" id="active_language" class="form-control selectpicker">
                <?php foreach(list_folders(APPPATH .'language') as $language){ ?>
                <option value="<?php echo $language; ?>" <?php if($language == get_option('active_language')){echo ' selected'; } ?>><?php echo ucfirst($language); ?></option>
                <?php } ?>
            </select>
        </div>
        <hr />
        <?php render_yes_no_option('output_client_pdfs_from_admin_area_in_client_language','settings_output_client_pdfs_from_admin_area_in_client_language','settings_output_client_pdfs_from_admin_area_in_client_language_help'); ?>
    </div>
</div>
