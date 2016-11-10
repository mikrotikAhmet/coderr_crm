<div class="col-md-12">
	<div class="panel_s">
		<div class="panel-heading">
			<?php echo _l('clients_knowledge_base'); ?>
		</div>
		<div class="panel-body">
			<?php $groups = get_all_knowledge_base_articles_grouped();
			if(count($groups) == 0){ ?>
			<h4><?php echo _l('clients_knowledge_base_articles_not_found'); ?></h4>
			<?php } ?>
			<?php if(!$this->input->get('groupid')){ ?>
			<?php foreach($groups as $group){ ?>
			<div class="col-md-12">
				<div class="article_group_wrapper">
					<h4 class="bold"><i class="fa fa-folder"></i> <a href="<?php echo $this->uri->uri_string(); ?>?groupid=<?php echo $group['groupid']; ?>"><?php echo $group['name']; ?></a>
						<small>(<?php echo count($group['articles']); ?>)</small>
					</h4>
					<p><?php echo $group['description']; ?></p>
				</div>
			</div>
			<?php } ?>
			<?php } else { ?>
			<div class="col-md-12">
				<?php foreach($groups as $group){
					if($group['groupid'] != $this->input->get('groupid')){continue;}
					?>
					<h4 class="bold"><i class="fa fa-folder"></i> <?php echo $group['name']; ?></h4>
					<ul class="list-unstyled articles_list">
						<?php foreach($group['articles'] as $article) { ?>
						<li>
							<i class="fa fa-file-text-o"></i>
							<a href="<?php echo site_url('knowledge_base/'.$article['slug']); ?>"><?php echo $article['subject']; ?></a>
							<div class="text-muted mtop10"><?php echo mb_substr($article['description'],0,180); ?>...</div>
						</li>
						<?php } ?>
					</ul>
					<?php } ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
