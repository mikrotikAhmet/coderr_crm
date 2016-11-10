<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label for="clients_default_theme" class="control-label"><?php echo _l('settings_clients_default_theme'); ?></label>
			<select name="settings[clients_default_theme]" id="clients_default_theme" class="form-control selectpicker">
				<?php foreach(get_all_client_themes() as $theme){ ?>
				<option value="<?php echo $theme; ?>" <?php if(active_clients_theme() == $theme){echo 'selected';} ?>><?php echo ucfirst($theme); ?></option>
				<?php } ?>
			</select>
		</div>
		<hr />
		<?php echo render_select( 'settings[customer_default_country]',get_all_countries(),array( 'country_id',array( 'short_name')), 'customer_default_country',get_option('customer_default_country')); ?>
		<hr />
		<?php render_yes_no_option('allow_registration','settings_clients_allow_registration'); ?>
		<hr />
		<?php render_yes_no_option('use_knowledge_base','settings_general_use_knowledgebase','settings_general_use_knowledgebase_tooltip'); ?>
		<hr />
		<?php render_yes_no_option('knowledge_base_without_registration','settings_clients_allow_kb_view_without_registration'); ?>
	</div>
</div>
