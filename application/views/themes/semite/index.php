<?php
echo $head;
if($use_navigation == true){
	get_template_part('navigation');
}
?>
<div id="wrapper">
	<div id="content">
		<div class="container">
			<div class="row">
				<?php get_template_part('alerts'); ?>
				<div class="clearfix"></div>
				<?php echo $view; ?>
			</div>
		</div>
	</div>
	<?php
	echo $footer;
	echo $scripts;
	?>
</div>
</body>
</html>
