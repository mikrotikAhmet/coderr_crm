<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <p class="text-muted bold">
                            <?php echo _l('amount_display_in_base_currency'); ?>
                        </p>
                        <?php if(is_using_multiple_currencies()){ ?>
                            <p class="text-danger">
                                <?php echo _l('multiple_currencies_is_used_expenses_vs_income_report'); ?>
                            </p>
                        <?php } ?>
                        <canvas class="chart" id="report-expense-vs-income"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(document).ready(function(){
        var ctx_expense_vs_income = $('#report-expense-vs-income').get(0).getContext('2d');
        chartExpenseVsIncome = new Chart(ctx_expense_vs_income).Bar(<?php echo $chart_expenses_vs_income_values; ?>,{responsive:true});
    });
</script>
</body>
</html>
