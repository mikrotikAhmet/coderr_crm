<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<a href="<?php echo admin_url('tickets/add'); ?>" class="btn btn-info pull-left display-block mright5">
							<?php echo _l('new_ticket'); ?>
						</a>
						<a href="#" class="btn btn-default" onclick="slideToggle('.weekly-ticket-opening',init_tickets_weekly_chart); return false;"><i class="fa fa-bar-chart"></i></a>
					</div>
				</div>
				<div class="panel_s animated weekly-ticket-opening" style="display:none;">
					<div class="panel-heading-bg">
						<?php echo _l('home_weekend_ticket_opening_statistics'); ?>
					</div>
					<div class="panel-body">
						<canvas class="chart" id="weekly-ticket-openings-chart" style="height:300px;"></canvas>
					</div>
				</div>
				<div class="panel_s">
					<div class="panel-body">
						<div class="clearfix"></div>
						<?php echo AdminTicketsTableStructure(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script src="<?php echo site_url(); ?>assets/js/tickets.js"></script>
<script>
var chart;
var chart_data = <?php echo $weekly_tickets_opening_statistics; ?>;

function init_tickets_weekly_chart() {
    if (typeof(chart) !== 'undefined') {
        chart.destroy();
    }
    // Weekly ticket openings statistics
    var ctx_tickets_weekly_openings = $('#weekly-ticket-openings-chart').get(0).getContext('2d');
    chart = new Chart(ctx_tickets_weekly_openings).Line(chart_data, line_chart_options);
}
</script>
</body>
</html>
