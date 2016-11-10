<script src="<?php echo site_url(); ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo site_url(); ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo site_url(); ?>bower_components/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="<?php echo site_url(); ?>bower_components/jquery-validation/dist/additional-methods.min.js"></script>
<script src="<?php echo site_url(); ?>bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="<?php echo site_url(); ?>bower_components/datatables/media/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo site_url(); ?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo site_url(); ?>bower_components/Chart.js/Chart.min.js" type="text/javascript"></script>
<script src="<?php echo template_assets_url(); ?>js/global.js"></script>
<?php if(is_client_logged_in()){ ?>
<script src="<?php echo template_assets_url(); ?>js/clients.js"></script>
<script src="<?php echo template_assets_url(); ?>js/client-report.js"></script>

<?php } ?>

