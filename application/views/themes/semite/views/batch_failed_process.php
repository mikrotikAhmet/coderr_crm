<div style="text-align: center">
    <img src="<?php echo template_assets_url(); ?>images/cancelled.png" style="width: 200px">
    <h1 class="text-danger">Batch Payment Has been failed</h1>
</div>
<p>Error Msg  : <b><?php echo $reason?></b></p>
<div style="text-align: center">
    <a href="<?php echo site_url()?>batch/processor" class="btn btn-primary">Back to Batch Processor</a>
</div>