<!-- Overview Chart-->
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group" id="report-time">
                    <select class="form-control" name="months-report" data-width="100%">
                        <option value=""><?php echo _l('clients_report_sales_months_all_time'); ?></option>
                        <option value="6"><?php echo _l('clients_report_sales_months_six_months'); ?></option>
                        <option value="12"><?php echo _l('clients_report_sales_months_twelve_months'); ?></option>
                        <option value="custom"><?php echo _l('clients_report_sales_months_custom'); ?></option>
                    </select>
                </div>
                <?php if(is_client_using_multiple_currencies()){ ?>
                    <div id="currency" class="form-group mtop15" data-toggle="tooltip" title="<?php echo _l('clients_home_currency_select_tooltip'); ?>">
                        <select class="form-control" name="currency">
                            <?php foreach($currencies as $currency){
                                $selected = '';
                                if($currency['isdefault'] == 1){
                                    $selected = 'selected';
                                }
                                ?>
                                <option value="<?php echo $currency['id']; ?>" <?php echo $selected; ?>><?php echo $currency['symbol']; ?> - <?php echo $currency['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                <?php } ?>
                <div id="date-range" class="animated mbot15 hide">
                    <label for="report-from" class="control-label"><?php echo _l('clients_report_select_from_date'); ?></label>
                    <div class="input-group date">
                        <input type="text" class="form-control datepicker" id="report-from" name="report-from">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar calendar-icon"></i>
                        </div>
                    </div>
                    <div class="clearfix mtop15"></div>
                    <label for="report-to" class="control-label"><?php echo _l('clients_report_select_to_date'); ?></label>
                    <div class="input-group date">
                        <input type="text" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar calendar-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <canvas id="transaction-home-chart" class="animated fadeIn"></canvas>
                    </div>
                    <div class="col-md-6">
                        <?php //get_template_part('quick_info');?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>