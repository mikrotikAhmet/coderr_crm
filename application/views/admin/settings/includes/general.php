<div class="row">
	<div class="col-md-6">
		<?php $company_logo = get_option('company_logo'); ?>
		<div class="form-group company_logo_upload <?php if ($company_logo != ''){echo 'hide';} ?>">
			<label for="company_logo" class="control-label"><?php echo _l('settings_general_company_logo'); ?></label>
			<input type="file" name="company_logo" class="form-control" value="" data-toggle="tooltip" title="<?php echo _l('settings_general_company_logo_tooltip'); ?>">
		</div>
		<div class="form-group company_logo <?php if ($company_logo == ''){echo 'hide';} ?>">
			<?php
			if($company_logo != ''){ ?>
			<div class="row">
				<div class="col-md-9">
					<img src="<?php echo site_url('uploads/company/'.$company_logo); ?>" class="img img-responsive" alt="<?php get_option('companyname'); ?>">
				</div>
				<div class="col-md-3 text-right">
					<a href="#" onclick="remove_company_logo(); return false;" data-toggle="tooltip" title="<?php echo _l('settings_general_company_remove_logo_tooltip'); ?>"><i class="fa fa-remove"></i></a>
				</div>
			</div>
			<div class="clearfix"></div>
			<?php } ?>
		</div>
		<hr />
		<?php $favicon = get_option('favicon'); ?>
		<div class="form-group favicon_upload <?php if ($favicon != ''){echo 'hide';} ?>">
			<label for="favicon" class="control-label"><?php echo _l('settings_general_favicon'); ?></label>
			<input type="file" name="favicon" class="form-control">
		</div>
		<div class="form-group favicon <?php if ($favicon == ''){echo 'hide';} ?>">
			<?php
			if($favicon != ''){ ?>
			<div class="row">
				<div class="col-md-9">
					<img src="<?php echo site_url('uploads/company/'.$favicon); ?>" class="img img-responsive">
				</div>
				<div class="col-md-3 text-right">
					<a href="#" onclick="remove_favicon(); return false;"><i class="fa fa-remove"></i></a>
				</div>
			</div>
			<div class="clearfix"></div>
			<?php } ?>
		</div>
		<hr />
		<?php echo render_input('settings[companyname]','settings_general_company_name',get_option('companyname')); ?>
		<hr />
		<?php echo render_input('settings[main_domain]','settings_general_company_main_domain',get_option('main_domain')); ?>
		<hr />
		<?php render_yes_no_option('rtl_support_admin','settings_rtl_support_admin'); ?>
		<hr />
		<?php render_yes_no_option('rtl_support_client','settings_rtl_support_client'); ?>
		<hr />
		<?php echo render_input('settings[tables_pagination_limit]','settings_general_tables_limit',get_option('tables_pagination_limit'),'number'); ?>
		<hr />
		<?php echo render_input('settings[limit_top_search_bar_results_to]','settings_limit_top_search_bar_results',get_option('limit_top_search_bar_results_to'),'number'); ?>
		<hr />
		<?php echo render_select('settings[default_staff_role]',$roles,array('roleid','name'),'settings_general_default_staff_role',get_option('default_staff_role'),array(),array('data-toggle'=>'tooltip','title'=>'settings_general_default_staff_role_tooltip')); ?>
		<hr />

		<?php echo render_input('settings[custom_pdf_logo_image_url]','settings_custom_pdf_logo_image_url',get_option('custom_pdf_logo_image_url'),'text',array('data-toggle'=>'tooltip','title'=>'settings_custom_pdf_logo_image_url_tooltip')); ?>
		<hr />
		<?php echo render_input('settings[google_api_key]','settings_google_api',get_option('google_api_key')); ?>
		<hr />
	</div>
</div>
