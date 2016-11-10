<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="#" onclick="make_expense_pdf_export(); return false;" class="btn btn-default pull-left mright10"><i class="fa fa-file-pdf-o"></i></a>
                        <a download="expenses-report-<?php echo $current_year; ?>.xls" class="btn btn-default pull-left mright10" href="#" onclick="return ExcellentExport.excel(this, 'expenses-report-table', 'Expenses Report <?php echo $current_year; ?>');"><i class="fa fa-file-excel-o"></i></a>
                        <?php if(count($expense_years) > 0 ){ ?>
                        <select class="selectpicker" name="expense_year" onchange="change_expense_report_year(this.value);">
                            <?php foreach($expense_years as $year) { ?>
                            <option value="<?php echo $year['year']; ?>"<?php if($year['year'] == $current_year){echo 'selected';} ?>>
                                <?php echo $year['year']; ?>
                            </option>
                            <?php } ?>
                        </select>
                        <?php } ?>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin pull-right mright10 text-info inline-block" data-toggle="tooltip" title="<?php echo _l('expense_report_info'); ?>"><i class="fa fa-exclamation-triangle"></i></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover expenses-report" id="expenses-report-table">
                                <thead>
                                    <tr>
                                        <th class="bold">Category</th>
                                        <?php
                                        for ($m=1; $m<=12; $m++) {
                                            echo '  <th class="bold">' . _l(date('F', mktime(0,0,0,$m,1))) . '</th>';
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $categories_total = array();
                                    foreach($categories as $category) { ?>
                                    <tr>
                                        <td class="bold"><?php echo $category['name']; ?></td>
                                        <?php
                                        $_total_categories_yearly = array();
                                        for ($m=1; $m<=12; $m++) {
                                            // Set the monthly total expenses array
                                            if(!isset($monthly_total[$m])){
                                                $monthly_total[$m] = array();
                                            }
                                            // Set the total expenses by category array
                                            if(!isset($_total_categories_yearly[$category['id'] .'_' .$category['name']])) {
                                                // Add the id prefix in case of duplicate categories
                                                $_total_categories_yearly[$category['id'] .'_' .$category['name']] = array();
                                            }
                                            // Ge the expenses
                                            $this->db->select('amount,taxrate,tblexpenses.tax')->from('tblexpenses')->join('tbltaxes','tbltaxes.id = tblexpenses.tax','left')->where('MONTH(date)',$m)->where('YEAR(date)',$current_year)->where('category',$category['id'])->where('billable',0);

                                            $expenses = $this->db->get()->result_array();
                                            $total_expenses = array();
                                            echo '<td>';
                                            foreach($expenses as $expense){
                                                $total = $expense['amount'];
                                                // Check if tax is applied
                                                if($expense['tax'] != 0){
                                                    $total += ($total / 100 * $expense['taxrate']);
                                                }
                                                $total_expenses[] = $total;
                                            }
                                            $total_expenses = array_sum($total_expenses);
                                            // Add to total monthy expenses
                                            array_push($monthly_total[$m],$total_expenses);
                                            // ADd to total yearly expenses
                                            array_push($_total_categories_yearly[$category['id'] .'_' .$category['name']],$total_expenses);

                                            // Output the total for this category
                                            if(count($categories) <= 8){
                                                echo format_money($total_expenses,$base_currency->symbol);
                                            } else {
                                            // show tooltip for the month if more the 8 categories found. becuase when listing down you wont be able to see the month
                                                echo '<span data-toggle="tooltip" title="'._l(date('F', mktime(0,0,0,$m,1))).'">'.format_money($total_expenses,$base_currency->symbol) .'</span>';
                                            }
                                            echo '</td>';
                                            ?>
                                            <?php } ?>
                                        </tr>
                                        <?php
                                        // Sum and add the total for current category for all months
                                        $categories_total[$category['id'] . '_' . $category['name']] = array_sum($_total_categories_yearly[$category['id'] . '_' .$category['name']]);
                                    } ?>
                                    <?php $current_year_total = array(); ?>
                                    <tr class="text-danger">
                                        <td class="bold">Total</td>
                                        <?php if(isset($monthly_total)) { ?>
                                        <?php foreach($monthly_total as $total){
                                            $total = array_sum($total);
                                            $current_year_total[] = $total;
                                            ?>
                                            <td class="bold <?php if($total == 0){echo 'text-success';}; ?>">
                                                <?php echo format_money($total,$base_currency->symbol); ?>
                                            </td>
                                            <?php } ?>
                                            <?php } ?>
                                        </tr>
                                        <tr class="categories bold">
                                            <td colspan="13" class="text-danger font-medium bold">
                                                <span class="text-muted"><?php echo _l('total_expenses_for'); ?> <span class="bold"><?php echo $current_year; ?></span>:</span> <?php echo format_money(array_sum($current_year_total),$base_currency->symbol); ?>
                                            </td>
                                        </tr>
                                        <?php if(count($categories_total)){ ?>
                                        <tr class="categories">
                                           <td colspan="13" class="font-medium text-muted bold">
                                            <?php echo _l('expenses_yearly_by_categories'); ?>
                                        </td>
                                    </tr>
                                    <?php
                                    foreach($categories_total as $category_name => $total){
                                        $_class_indicator = 'text-danger';
                                        if($total == 0){
                                            $_class_indicator = 'text-success';
                                        }
                                        echo '<tr class="categories">';
                                        ?>
                                        <td class="bold" colspan="13">
                                            <?php
                                            $_temp_cat = explode('_',$category_name);
                                            echo $_temp_cat[1] .': '.format_money($total,$base_currency->symbol);
                                            ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script src="<?php echo site_url('bower_components/excellentexport/excellentexport.min.js'); ?>"></script>
<script>
    function change_expense_report_year(year) {
        window.location.href = admin_url + 'reports/expenses/' + year;
    }
    function make_expense_pdf_export() {
        var body = [];
        var export_headings = [];
        var export_widths = [];
        var export_data = [];
        var export_categories_data = '';
        var headings = $('table th');
        var data_tbody = $('table tbody tr').not('.categories');
        var data_categories = $('table tr.categories');
            // Get the categories yearly total
            $.each(data_categories, function() {
                export_categories_data += stripTags($(this).find('td').text());
            });
            // Prepare the pdf headings
            $.each(headings, function() {
                var heading = {};
                heading.text = $(this).text();
                heading.fillColor = '#444A52';
                heading.color = '#fff';
                export_headings.push(heading);
                export_widths.push(54);
            });
            body.push(export_headings);
            // Categories total
            $.each(data_tbody, function() {
                var row = [];
                $.each($(this).find('td'), function() {
                    var data = $(this);
                    row.push($(data).text());
                });
                body.push(row);
            });
            // Pdf definition
            var docDefinition = {
                pageOrientation: 'landscape',
                pageMargins: [12, 12, 12, 12],
                "alignment":"center",
                content: [
                {
                    text: '<?php echo _l("expenses_report_for"); ?> <?php echo $current_year; ?>:',
                    bold: true,
                    fontSize: 25,
                    margin: [0, 5]
                },
                {
                    text:'<?php echo get_option("companyname"); ?>',
                    margin: [2,5]
                },
                {
                    table: {
                        headerRows: 1,
                        widths: export_widths,
                        body: body
                    },
                },
                {
                    text: export_categories_data,
                    alignment: 'left'
                }
                ],
                defaultStyle: {
                    alignment: 'justify',
                    fontSize: 10,
                }
            };
            // Open the pdf.
            pdfMake.createPdf(docDefinition).open();
        }
    </script>
</body>
</html>
