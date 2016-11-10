<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<?php echo form_open($this->uri->uri_string()); ?>
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-5">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo $title; ?>
					</div>
					<div class="panel-body">
						<?php if(isset($article)){ ?>
						<a href="<?php echo admin_url('knowledge_base/article'); ?>" class="btn btn-success pull-left mbot20 display-block"><?php echo _l('kb_article_new_article'); ?></a>
						<?php } ?>
						<div class="row">
							<div class="col-md-12">
								<?php $value = (isset($article) ? $article->subject : ''); ?>
								<?php echo render_input('subject','kb_article_add_edit_subject',$value); ?>

								<?php $value = (isset($article) ? $article->articlegroup : ''); ?>
								<?php echo render_select('articlegroup',get_kb_groups(),array('groupid','name'),'kb_article_add_edit_group',$value); ?>

								<div class="checkbox checkbox-primary">
									<input type="checkbox" name="disabled" <?php if(isset($article) && $article->active_article == 0){echo 'checked';} ?>>
									<label><?php echo _l('kb_article_disabled'); ?></label>
								</div>
								<button type="submit" class="btn btn-primary pull-right"><?php echo _l('submit'); ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-7">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo _l('kb_article_description'); ?>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<?php $contents = ''; if(isset($article)){$contents = $article->description;} ?>
								<?php $this->load->view('admin/editor/template',array('name'=>'description','contents'=>$contents)); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
	<?php init_tail(); ?>
	<script>
		$(document).ready(function(){
			_validate_form($('form'),{subject:'required',articlegroup:'required'});
		});
	</script>
</body>
</html>
