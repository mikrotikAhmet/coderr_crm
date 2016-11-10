<nav class="navbar navbar-default">
	<div class="container">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<?php get_company_logo('','navbar-brand'); ?>

		</div>
		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav navbar-right">
				<?php if((get_option('use_knowledge_base') == 1 && !is_client_logged_in() && get_option('knowledge_base_without_registration') == 1) || (get_option('use_knowledge_base') == 1 && is_client_logged_in())){ ?>
				<li><a href="<?php echo site_url('knowledge_base'); ?>"><?php echo _l('clients_nav_kb'); ?></a></li>
				<?php } ?>
				<?php if(!is_client_logged_in()){ ?>
				<li><a href="<?php echo site_url('clients/login'); ?>"><?php echo _l('clients_nav_login'); ?></a></li>
				<?php if(get_option('allow_registration') == 1){ ?>
				<li><a href="<?php echo site_url('clients/register'); ?>"><?php echo _l('clients_nav_register'); ?></a></li>
				<?php } ?>
				<?php } else { ?>
				<?php if(has_customer_permission('proposals')){ ?>
				<li><a href="<?php echo site_url('clients/proposals'); ?>"><?php echo _l('clients_nav_proposals'); ?></a></li>
				<?php } ?>
				<?php if(has_customer_permission('invoices')){ ?>
				<li><a href="<?php echo site_url('clients/invoices'); ?>"><?php echo _l('clients_nav_invoices'); ?></a></li>
				<?php } ?>
				<?php if(has_customer_permission('estimates')){ ?>
				<li><a href="<?php echo site_url('clients/estimates'); ?>"><?php echo _l('clients_nav_estimates'); ?></a></li>
				<?php } ?>
				<?php if(has_customer_permission('contracts')){ ?>
				<li><a href="<?php echo site_url('clients/contracts'); ?>"><?php echo _l('clients_nav_contracts'); ?></a></li>
				<?php } ?>
				<?php if(has_customer_permission('support')){ ?>
				<li><a href="<?php echo site_url('clients/tickets'); ?>"><?php echo _l('clients_nav_support'); ?></a></li>
				<?php } ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
						<?php echo $client->firstname . ' ' . $client->lastname; ?>
						<span class="caret"></span></a>
						<ul class="dropdown-menu animated fadeIn">
							<li><a href="<?php echo site_url('clients/profile'); ?>"><?php echo _l('clients_nav_profile'); ?></a></li>

							<li><a href="<?php echo site_url('clients/logout'); ?>"><?php echo _l('clients_nav_logout'); ?></a></li>
						</ul>
					</li>
					<?php } ?>
				</ul>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>

	<?php if(is_staff_logged_in()){ ?>
	<div id="staff_logged_in" class="alert alert-danger logged-in-as">
		You are logged in as <a href="<?php echo admin_url(); ?>">administrator</a>
	</div>
	<?php } ?>
