<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body animated fadeIn">
						<div class="dt-loader hide"></div>
						<div id="calendar"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('admin/utilities/calendar_template'); ?>
<script>
google_api = '<?php echo $google_api_key; ?>';
calendarIDs = '<?php echo json_encode($google_ids_calendars); ?>';
</script>
<?php init_tail(); ?>
</body>
</html>
