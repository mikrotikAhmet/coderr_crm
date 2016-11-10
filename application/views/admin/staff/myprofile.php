<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo _l('staff_profile_string'); ?>
					</div>
					<div class="panel-body">
						<div class="button-group mtop10 pull-right">
							<a href="<?php echo $staff_p->facebook; ?>" target="_blank" class="btn btn-default btn-xs"><i class="fa fa-facebook"></i></a>
							<a href="<?php echo $staff_p->linkedin; ?>" class="btn btn-default btn-xs"><i class="fa fa-linkedin"></i></a>
							<a href="<?php echo $staff_p->skype; ?>" data-toggle="tooltip" title="<?php echo $staff_p->skype; ?>" target="_blank" class="btn btn-default btn-xs"><i class="fa fa-skype"></i></a>
							<?php if(has_permission('manageStaff')){ ?>
							<a href="<?php echo admin_url('staff/member/'.$staff_p->staffid); ?>" class="btn btn-default btn-xs"><i class="fa fa-pencil-square"></i></a>
							<?php } ?>
						</div>
						<div class="clearfix"></div>
						<?php if(is_admin($staff_p->staffid)){ ?>
						<p class="pull-right text-info"><?php echo _l('staff_admin_profile'); ?></p>
						<?php } ?>
						<?php echo staff_profile_image($staff_p->staffid,array('staff-profile-image-thumb'),'thumb'); ?>
						<div class="profile mtop20 display-inline-block">
							<h4><?php echo $staff_p->firstname . ' ' . $staff_p->lastname; ?></h4>
							<small class="display-block"><i class="fa fa-envelope"></i> <?php echo $staff_p->email; ?></small>
							<?php if($staff_p->phonenumber != ''){ ?>
							<small><i class="fa fa-phone-square"></i> <?php echo $staff_p->phonenumber; ?></small>
							<?php } ?>
							<?php if(count($staff_departments) > 0) { ?>
							<div class="form-group mtop10">
								<label for="departments" class="control-label"><?php echo _l('staff_profile_departments'); ?></label>
								<div class="clearfix"></div>
								<?php
								foreach($departments as $department){ ?>
								<?php
								foreach ($staff_departments as $staff_department) {
									if($staff_department['departmentid'] == $department['departmentid']){ ?>
									<div class="chip-circle"><?php echo $staff_department['name']; ?></div>
									<?php }
								}
								?>
								<?php } ?>
							</div>
								<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<?php if ($staff_p->staffid == get_staff_user_id()){ ?>
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo _l('staff_profile_notifications'); ?>
					</div>
					<div class="panel-body">
						<div id="notifications">
						</div>
						<a href="#" class="btn btn-primary loader"><?php echo _l('load_more'); ?></a>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>

<?php init_tail(); ?>
<script>
	$(document).ready(function(){
		var notifications = $('#notifications');
		if(notifications.length > 0){
			var page = 0;
			var total_pages = '<?php echo $total_pages; ?>';
			$('.loader').on('click',function(e){
				e.preventDefault();
				if(page <= total_pages){
					$.post(admin_url + 'staff/notifications',{page:page}).success(function(response){
						response = $.parseJSON(response);
						var notifications = '';
						$.each(response,function(i,obj){
							notifications += '<div class="notification-box-all">';
							var link_notification = '';
							var link_class_indicator = '';
							if(obj.link){
								link_notification= ' data-link="'+admin_url+obj.link+'"';
								link_class_indicator = ' notification_link';
							}
							notifications += obj.profile_image;
							notifications +='<div class="media-body'+link_class_indicator+'"'+link_notification+'>';
							notifications += '<div class="description">' + obj.description + '</div>';
							notifications += '<small class="text-muted text-right">' + obj.date + '</small>';
							notifications += '</div>';
							notifications += '</div>';

						});
						$('#notifications').append(notifications);
						page++;
					});

					if(page >= total_pages - 1)
					{
						$(".loader").addClass("disabled");
					}
				}
			});

			$('.loader').click();
		}
	});
</script>
</body>
</html>
