<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-5">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo $title; ?>
					</div>
					<div class="panel-body">
						<?php $this->load->view('admin/leads/profile'); ?>
					</div>
				</div>
			</div>
			<?php if(isset($lead)){ ?>
			<div class="col-md-7">
				<div class="panel_s">
					<div class="panel-body panel-no-heading">
					<!-- Nav tabs -->
					<?php $this->load->view('admin/leads/lead_tabs'); ?>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
</div>
<?php init_tail(); ?>
<?php
if(isset($lead)){ ?>
<script>
	initDataTable('.table-rel-tasks', admin_url +'tasks/init_relation_tasks/<?php echo $lead->id; ?>/lead', 'tasks');
	initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?php echo $lead->id ;?> + '/'+'lead', 'reminders', [4], [4]);
	initDataTable('.table-proposals-lead', admin_url + 'proposals/proposal_relations/' + <?php echo $lead->id ;?>+'/lead', 'proposals', 'undefined', 'undefined');
</script>
<?php } ?>
<script src="<?php echo site_url('assets/js/leads.js'); ?>"></script>
</body>
</html>
