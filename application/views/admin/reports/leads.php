<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-6 animated fadeIn">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo _l('report_this_week_leads_conversions'); ?>
					</div>
					<div class="panel-body">
						<canvas class="leads-this-week chart" id="leads-this-week"></canvas>
					</div>
				</div>
			</div>
			<div class="col-md-6 animated fadeIn">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo _l('report_leads_sources_conversions'); ?>
					</div>
					<div class="panel-body">
						<canvas class="leads-sources-report chart" id="leads-sources-report"></canvas>
					</div>
				</div>
			</div>
			<div class="col-md-12 animated fadeIn">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo _l('report_leads_monthly_conversions'); ?>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<?php
								echo '<select name="month" class="form-control">' . PHP_EOL;
								for ($m=1; $m<=12; $m++) {
									$_selected = '';
									if($m == date('m')){
										$_selected  = ' selected';
									}
									echo '  <option value="' . $m . '"'.$_selected.'>' . _l(date('F', mktime(0,0,0,$m,1))) . '</option>' . PHP_EOL;
								}
								echo '</select>' . PHP_EOL;
								?>
							</div>
						</div>
						<canvas class="leads-monthly chart mtop20" id="leads-monthly"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script>
	var MonthlyLeadsChart;
	$(document).ready(function() {
	    $.get(admin_url + 'reports/leads_monthly_report/' + $('select[name="month"]').val(), function(response) {
	        var ctx = $('#leads-monthly').get(0).getContext('2d');
	        MonthlyLeadsChart = new Chart(ctx).Line(response, line_chart_options);
	    }, 'json');
	    $('select[name="month"]').on('change', function() {
	        MonthlyLeadsChart.destroy();
	        $.get(admin_url + 'reports/leads_monthly_report/' + $('select[name="month"]').val(), function(response) {
	            var ctx = $('#leads-monthly').get(0).getContext('2d');
	            MonthlyLeadsChart = new Chart(ctx).Line(response, line_chart_options);
	        }, 'json');
	    });
	    $.get(admin_url + 'reports/leads_this_week_report/', function(response) {
	        var ctx = $('#leads-this-week').get(0).getContext('2d');
	        new Chart(ctx).Pie(response, {
	            responsive: true
	        });
	    }, 'json');

	    $.get(admin_url + 'reports/leads_sources_report/', function(response) {
	        var ctx = $('#leads-sources-report').get(0).getContext('2d');
	        new Chart(ctx).Line(response, {
	            responsive: true
	        });
	    }, 'json');
	});
</script>
</body>
</html>
