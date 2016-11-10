<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<a href="<?php echo admin_url('knowledge_base/article'); ?>" class="btn btn-primary"><?php echo _l('kb_article_new_article'); ?></a>
						<a href="#" class="btn btn-default mleft5 toggle-custom-kan-ban-tab"><i class="fa fa-th-list"></i></a>
						<?php echo form_hidden('custom_view'); ?>
					</div>
					<div class="panel_s mtop5">
						<div class="panel-body">
							<div class="row">
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane active kb-kan-ban kan-ban-tab" id="kan-ban">
										<div class="container-fluid">
											<?php
											if(count($groups) == 0){
												echo _l('kb_no_articles_found');
											}
											foreach($groups as $group){
												$kanban_colors = '';
												foreach(get_system_favourite_colors() as $color){
													$color_selected_class = 'cpicker-small';
													$kanban_colors .= "<div class='cpicker ".$color_selected_class."' data-color='".$color."' style='background:".$color.";border:1px solid ".$color."'></div>";
												}
												?>
												<ul class="kan-ban-col" data-col-group-id="<?php echo $group['groupid']; ?>">
													<li class="kan-ban-col-wrapper">
														<div class="border-right panel_s">
															<?php
															$group_color = 'style="background:'.$group["color"].';border:1px solid '.$group['color'].'"';
															?>
															<div class="panel-heading-bg primary-bg" <?php echo $group_color; ?> data-group-id="<?php echo $group['groupid']; ?>">
																<?php echo $group['name']; ?>
																<small>(<?php echo total_rows('tblknowledgebase','articlegroup='.$group['groupid']); ?>)</small>
																<a href="#" onclick="return false;" class="pull-right color-white" data-placement="bottom" data-toggle="popover" data-content="<div class='kan-ban-settings'><?php echo $kanban_colors; ?></div>" data-html="true" data-trigger="focus"><i class="fa fa-angle-down"></i>
																</a>
															</div>
															<?php
															$this->db->select()->from('tblknowledgebase')->where('articlegroup',$group['groupid'])->order_by('article_order','asc');
															$articles = $this->db->get()->result_array();
															?>
															<div class="kan-ban-content-wrapper">
																<div class="kan-ban-content">
																	<ul class="sortable article-group groups" data-group-id="<?php echo $group['groupid']; ?>">
																		<?php foreach($articles as $article) { ?>
																		<li class="<?php if($article['active'] == 0){echo 'line-throught';} ?>" data-article-id="<?php echo $article['articleid']; ?>">
																			<div class="panel-body">
																				<i class="fa fa-file-text-o"></i>
																				<a href="<?php echo admin_url('knowledge_base/article/'.$article['articleid']); ?>"><?php echo $article['subject']; ?></a>

																				<?php if($article['description'] != ''){ ?>
																				<p class="text-muted"><?php echo strip_tags(mb_substr($article['description'],0,180)) . '...'; ?></p>
																				<?php }	?>
																			</div>
																		</li>
																		<?php } ?>
																	</ul>
																</div>
															</div>
														</li>
													</ul>
													<?php } ?>
												</div>
											</div>
											<div role="tabpanel" class="tab-pane" id="list_tab">
												<div class="col-md-12">
													<div class="btn-group mleft5" data-toggle="tooltip" title="<?php echo _l('view_articles_list'); ?>">
														<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
															<i class="fa fa-list"></i>
														</button>
														<ul class="dropdown-menu dropdown-menu-left" style="width:300px;">
															<li>
																<a href="#" onclick="dt_custom_view('','.table-articles'); return false;"><?php echo _l('view_articles_list_all'); ?></a>
															</li>
															<?php foreach($groups as $group){ ?>
															<li><a href="#" onclick="dt_custom_view(<?php echo $group['groupid']; ?>,'.table-articles'); return false;"><?php echo $group['name']; ?></a></li>
															<?php } ?>

														</ul>
													</div>
													<?php render_datatable(
														array(
															_l('kb_dt_article_name'),
															_l('kb_dt_group_name'),
															_l('options'),
															),'articles'); ?>
														</div>
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
							$(document).ready(function() {
								var kanbanCol = $('.kan-ban-content-wrapper');
								kanbanCol.css('max-height', (window.innerHeight - 275) + 'px');
								$('.kan-ban-content').css('min-height', (window.innerHeight - 275) + 'px');
								var kanbanColCount = parseInt(kanbanCol.length);
								$('.container-fluid').css('min-width', (kanbanColCount * 360) + 'px');
								$(".groups").sortable({
									connectWith: ".article-group",
									helper: 'clone',
									appendTo: '#kan-ban',
									placeholder: "ui-state-highlight-kan-ban-kb",
									revert: true,
									scroll: true,
									scrollSensitivity: 50,
									scrollSpeed: 70,
									update: function(event, ui) {
										if (this === ui.item.parent()[0]) {
											var articles = $(ui.item).parents('.article-group').find('li');
											i = 1;
											var order = [];
											$.each(articles, function() {
												i++;
												order.push([$(this).data('article-id'), i]);
											});

											setTimeout(function() {
												$.post(admin_url + 'knowledge_base/update_kan_ban', {
													order: order,
													groupid: $(ui.item.parent()[0]).data('group-id')
												}).success(function(response) {
													response = $.parseJSON(response);
													if (response.success == true) {
														alert_float('success', response.message);
													}
												})
											}, 100);
										}
									}
								}).disableSelection();

								$(".container-fluid").sortable({
									helper: 'clone',
									item: '.kan-ban-col',
									update: function(event, ui) {
										var order = [];
										var status = $('.kan-ban-col');
										var i = 0;
										$.each(status, function() {
											order.push([$(this).data('col-group-id'), i]);
											i++;
										});
										var data = {}
										data.order = order;
										$.post(admin_url + 'knowledge_base/update_groups_order', data);
									}
								});

							    // Status color change
							    $('body').on('click', '.kb-kan-ban .cpicker', function() {
							    	var color = $(this).data('color');
							    	var group_id = $(this).parents('.panel-heading-bg').data('group-id');
							    	$.post(admin_url + 'knowledge_base/change_group_color', {
							    		color: color,
							    		group_id: group_id
							    	});
							    });

							});
						</script>
					</body>
					</html>
