<div class="col-md-12">
    <div class="panel_s">
        <?php
        if(has_customer_permission('gateway')){ ?>

            <div class="panel-heading">
                <h4>Merchant Dashboard</h4>
            </div>
            <!-- Top Statistics -->
            <?php get_template_part('top_stats');?>
            <!-- Home Transaction Chart -->
            <?php get_template_part('transaction_home_chart');?>
            <!-- Last 10 Transactions -->
            <?php get_template_part('home_transaction_list');?>
        <?php } ?>
    </div>
</div>
