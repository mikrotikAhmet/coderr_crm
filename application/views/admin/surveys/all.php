<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<a href="<?php echo admin_url('surveys/survey'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_survey'); ?></a>
					</div>
				</div>
				<div class="panel_s">
					<div class="panel-body">

						<div class="clearfix"></div>
						<?php render_datatable(array(
							_l('id'),
							_l('survey_dt_name'),
							_l('survey_dt_total_questions'),
							_l('survey_dt_total_participants'),
							_l('survey_dt_date_created'),
							_l('survey_dt_active'),
							_l('options'),
							),'surveys'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php init_tail(); ?>
</body>
</html>
