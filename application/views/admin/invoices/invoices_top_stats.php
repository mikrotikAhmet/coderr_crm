                        <div id="stats-top" class="hide">
                            <div id="invoices_total"></div>
                            <div class="col-md-12">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <?php echo form_hidden('status',$status); ?>
                                        <?php echo form_hidden('custom_view',$custom_view); ?>
                                        <?php
                                        $total_invoices = total_rows('tblinvoices');
                                        $total_open = total_rows('tblinvoices',array('status'=>1));
                                        $total_paid = total_rows('tblinvoices',array('status'=>2));
                                        $total_not_paid_completely = total_rows('tblinvoices',array('status'=>3));
                                        $total_overdue = total_rows('tblinvoices',array('status'=>4));

                                        $percent_open = ($total_invoices > 0 ? number_format(($total_open * 100) / $total_invoices,2) : 0);
                                        $percent_paid = ($total_invoices > 0 ? number_format(($total_paid * 100) / $total_invoices,2) : 0);
                                        $percent_overdue = ($total_invoices > 0 ? number_format(($total_overdue * 100) / $total_invoices,2) : 0);
                                        $percent_not_paid_completely = ($total_invoices > 0 ? number_format(($total_not_paid_completely * 100) / $total_invoices,2) : 0);
                                        ?>
                                        <div class="row text-left quick-top-stats">
                                            <div class="col-md-3">
                                                <div class="row">
                                                    <div class="col-md-9">
                                                        <a href="#" onclick="show_invoices_by_status(2); return false;"><h5 class="bold"><?php echo _l('invoice_status_paid'); ?></h5></a>
                                                    </div>
                                                    <div class="col-md-3 text-right bold">
                                                        <?php echo $total_paid; ?> / <?php echo $total_invoices; ?>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="progress no-margin">
                                                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_paid; ?>">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="row">
                                                    <div class="col-md-9">
                                                        <a href="#" onclick="show_invoices_by_status(1); return false;"><h5 class="bold"><?php echo _l('invoice_status_unpaid'); ?></h5></a>
                                                    </div>
                                                    <div class="col-md-3 text-right bold">
                                                        <?php echo $total_open; ?> / <?php echo $total_invoices; ?>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="progress no-margin">
                                                            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_open; ?>">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="row">
                                                    <div class="col-md-9">
                                                        <a href="#" onclick="show_invoices_by_status(4); return false;"><h5 class="bold"><?php echo _l('invoice_status_overdue'); ?></h5></a>
                                                    </div>
                                                    <div class="col-md-3 text-right bold">
                                                        <?php echo $total_overdue; ?> / <?php echo $total_invoices; ?>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="progress no-margin">
                                                            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_overdue; ?>">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="row">
                                                    <div class="col-md-9">
                                                        <a href="#" onclick="show_invoices_by_status(3); return false;"><h5 class="bold"><?php echo _l('invoice_status_not_paid_completely'); ?></h5></a>
                                                    </div>
                                                    <div class="col-md-3 text-right bold">
                                                        <?php echo $total_not_paid_completely; ?> / <?php echo $total_invoices; ?>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="progress no-margin">
                                                            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_not_paid_completely; ?>">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
