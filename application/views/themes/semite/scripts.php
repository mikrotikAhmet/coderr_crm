<script src="<?php echo site_url(); ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo site_url(); ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo site_url(); ?>bower_components/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="<?php echo site_url(); ?>bower_components/jquery-validation/dist/additional-methods.min.js"></script>
<script src="<?php echo site_url(); ?>bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="<?php echo site_url(); ?>bower_components/datatables/media/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo site_url(); ?>bower_components/datatables-buttons/js/dataTables.buttons.js"></script>
<script src="<?php echo site_url(); ?>bower_components/datatables-buttons/js/buttons.colVis.js"></script>
<script src="<?php echo site_url(); ?>bower_components/datatables-buttons/js/buttons.html5.js"></script>
<script src="<?php echo site_url(); ?>bower_components/datatables-buttons/js/buttons.print.js"></script>
<script src="<?php echo site_url(); ?>bower_components/jszip/dist/jszip.min.js"></script>
<script src="<?php echo site_url(); ?>bower_components/pdfmake/build/pdfmake.min.js"></script>
<script src="<?php echo site_url(); ?>bower_components/pdfmake/build/vfs_fonts.js"></script>
<script src="<?php echo site_url(); ?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo site_url(); ?>bower_components/Chart.js/Chart.min.js" type="text/javascript"></script>
<link type="text/css" href="<?php echo site_url(); ?>assets/plugins/sweetalert/sweetalert.css" rel="stylesheet" media="screen" />
<script type="text/javascript" src="<?php echo site_url(); ?>assets/plugins/sweetalert/sweetalert.js"></script>
<script src="<?php echo template_assets_url(); ?>js/global.js"></script>
<script src="<?php echo template_assets_url(); ?>js/batch.js"></script>
<?php if(is_client_logged_in()){ ?>
<script src="<?php echo template_assets_url(); ?>js/clients.js"></script>
<script src="<?php echo template_assets_url(); ?>js/client-report.js"></script>
<?php } ?>

