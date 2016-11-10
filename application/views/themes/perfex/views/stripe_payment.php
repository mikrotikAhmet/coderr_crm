<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<title>
		<?php if (isset($title)){
			echo $title;
		} else {
			echo get_option('companyname');
		}
		?>
	</title>
	<link href="<?php echo site_url(); ?>assets/css/reset.css" rel="stylesheet">
	<!-- Bootstrap -->
	<link href="<?php echo site_url(); ?>bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href='<?php echo site_url(); ?>bower_components/open-sans-fontface/open-sans.css' rel='stylesheet' type='text/css'>
	<link href="<?php echo template_assets_url(); ?>css/style.css" rel="stylesheet">
</head>
<body>
	<div class="container">
		<div class="col-md-8 col-md-offset-2">
			<div class="row">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo _l('payment_for_invoice'); ?> <a href="<?php echo site_url('viewinvoice/'. $invoice->id . '/' . $invoice->hash); ?>" target="_blank"><?php echo format_invoice_number($invoice->id); ?></a>
					</div>
					<div class="panel-body">
						<p><span class="bold"><?php echo _l('payment_total',format_money($total,$invoice->symbol)); ?></span></p>
						<?php echo $stripe_form; ?>
					</div>
				</div>
			</div>
		</div>
		<script src="<?php echo site_url(); ?>bower_components/jquery/dist/jquery.min.js"></script>
		<script src="<?php echo site_url(); ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	</body>
	</html>
