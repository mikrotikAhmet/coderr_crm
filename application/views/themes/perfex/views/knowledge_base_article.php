<div class="col-md-12">
	<div class="panel_s">
		<div class="panel-body">
			<h1><?php echo $article->subject; ?></h1>
			<p>
				<?php echo $article->description; ?>
			</p>
			<h4 class="mtop20"><?php echo _l('clients_knowledge_base_find_useful'); ?></h4>
			<div class="answer_response"></div>
			<div class="btn-group mtop15 article_useful_buttons" role="group">
				<input type="hidden" name="articleid" value="<?php echo $article->articleid; ?>">
				<button type="button" data-answer="1" class="btn btn-success"><?php echo _l('clients_knowledge_base_find_useful_yes'); ?></button>
				<button type="button" data-answer="0" class="btn btn-danger"><?php echo _l('clients_knowledge_base_find_useful_no'); ?></button>
			</div>
		</div>
	</div>
</div>
