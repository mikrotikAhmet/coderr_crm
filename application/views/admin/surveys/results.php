<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-6 animated fadeIn">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo $title; ?>
					</div>
					<div class="panel-body">
						<?php $text_questions = array(); ?>
						<?php if(count($survey->questions) > 0){
							foreach($survey->questions as $question){  ?>
							<div class="mbot20">
								<?php if($question['boxtype'] == 'checkbox' || $question['boxtype'] == 'radio'){ ?>
								<h4 class="bold"><?php echo $question['question']; ?></h4>
								<?php $x = 0;
								foreach($question['box_descriptions'] as $box_description){ ?>
								<div class="row">
									<div class="col-md-12">
										<div class="row">
											<div class="col-md-8">
												<?php echo $box_description['description']; ?>
											</div>
											<div class="col-md-4 text-right">
												<?php
												$total_box_description_answers = total_rows('tblsurveyresults',array('surveyid'=>$survey->surveyid,'boxdescriptionid'=>$box_description['questionboxdescriptionid']));
												$total_box_answers = total_rows('tblsurveyresults',array('surveyid'=>$survey->surveyid,'boxid'=>$box_description['boxid']));
												?>
												<?php echo $total_box_description_answers; ?> / <?php echo $total_box_answers; ?>
											</div>
										</div>
									</div>
									<div class="col-md-12">
										<?php
										$percent = ($total_box_description_answers > 0 ? number_format(($total_box_description_answers * 100) / $total_box_answers,2) : 0);
										?>
										<div class="progress">
											<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent; ?>">
												<?php echo $percent; ?>%
											</div>
										</div>
									</div>
								</div>
								<?php } ?> <!-- End question boxes -->
								<?php } else if($question['boxtype'] == 'input' || $question['boxtype'] == 'textarea'){
									$text_questions[] = $question;
								}?> <!-- end if is boxtype || radio -->
							</div>
							<?php } ?>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="panel_s">
						<div class="panel-heading">
							<?php echo _l('survey_text_questions_results'); ?>
						</div>
						<div class="panel-body">
							<?php
							$original_questions = $text_questions;
							foreach($text_questions as $question){ ?>
							<h4 class="bold"><?php echo $question['question']; ?></h4>
							<a href="#questionid_<?php echo $question['questionid']; ?>" class="btn btn-primary btn-xs" data-toggle="modal"><?php echo _l('survey_view_all_answers'); ?></a>
							<?php
							$answers = get_text_question_answers($question['questionid'],$question['surveyid']);
							?>
							<div class="modal fade" id="questionid_<?php echo $question['questionid']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											<h4 class="modal-title" id="myModalLabel"><?php echo $question['question']; ?></h4>
										</div>
										<div class="modal-body">
											<ul class="list-unstyled">
												<?php
												$i = 1;
												foreach($answers as $answer) { ?>
												<li class="text-success mbot10"><?php echo $i, ': ' .$answer['answer']; ?></li>
												<?php
												$i++;
											} ?>
										</ul>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
									</div>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>
