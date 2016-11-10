<div class="col-md-12">
    <div class="panel_s">
        <?php
        if(has_customer_permission('batch')){ ?>

            <div class="panel-heading">
                <h4>Batch Manager</h4>
            </div>
            <?php get_template_part('batch_list');?>
        <?php } ?>
    </div>
</div>