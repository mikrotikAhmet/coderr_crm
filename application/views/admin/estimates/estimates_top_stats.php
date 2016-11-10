            <div id="stats-top" class="hide">
            <div id="estimates_total"></div>
                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <?php echo form_hidden('status',$status); ?>
                            <?php echo form_hidden('custom_view'); ?>
                            <?php
                            $total_estimates = total_rows('tblestimates');

                            $total_draft = total_rows('tblestimates',array('status'=>1));
                            $total_sent = total_rows('tblestimates',array('status'=>2));
                            $total_declined = total_rows('tblestimates',array('status'=>3));
                            $total_accepted = total_rows('tblestimates',array('status'=>4));
                            $total_expired = total_rows('tblestimates',array('status'=>5));

                            $percent_draft = ($total_estimates > 0 ? number_format(($total_draft * 100) / $total_estimates,2) : 0);
                            $percent_sent = ($total_estimates > 0 ? number_format(($total_sent * 100) / $total_estimates,2) : 0);
                            $percent_declined = ($total_estimates > 0 ? number_format(($total_declined * 100) / $total_estimates,2) : 0);
                            $percent_accepted = ($total_estimates > 0 ? number_format(($total_accepted * 100) / $total_estimates,2) : 0);
                            $percent_expired = ($total_estimates > 0 ? number_format(($total_expired * 100) / $total_estimates,2) : 0);
                            ?>
                            <div class="row text-left quick-top-stats">
                                <div class="col-md-5ths col-xs-6">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <a href="#" onclick="show_estimates_by_status(1); return false;"><h5 class="bold"><?php echo _l('estimate_status_draft'); ?></h5></a>
                                        </div>
                                        <div class="col-md-3 text-right bold">
                                            <?php echo $total_sent; ?> / <?php echo $total_estimates; ?>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="progress no-margin">
                                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_sent; ?>">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5ths col-xs-6">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <a href="#" onclick="show_estimates_by_status(2); return false;"><h5 class="bold"><?php echo _l('estimate_status_sent'); ?></h5></a>
                                        </div>
                                        <div class="col-md-3 text-right bold">
                                            <?php echo $total_draft; ?> / <?php echo $total_estimates; ?>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="progress no-margin">
                                                <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_draft; ?>">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5ths col-xs-6">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <a href="#" onclick="show_estimates_by_status(3); return false;"><h5 class="bold"><?php echo _l('estimate_status_declined'); ?></h5></a>
                                        </div>
                                        <div class="col-md-3 text-right bold">
                                            <?php echo $total_declined; ?> / <?php echo $total_estimates; ?>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="progress no-margin">
                                                <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_declined; ?>">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5ths col-xs-6">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <a href="#" onclick="show_estimates_by_status(4); return false;"><h5 class="bold"><?php echo _l('estimate_status_accepted'); ?></h5></a>
                                        </div>
                                        <div class="col-md-3 text-right bold">
                                            <?php echo $total_accepted; ?> / <?php echo $total_estimates; ?>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="progress no-margin">
                                                <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_accepted; ?>">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5ths col-xs-6">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <a href="#" onclick="show_estimates_by_status(5); return false;"><h5 class="bold"><?php echo _l('estimate_status_expired'); ?></h5></a>
                                        </div>
                                        <div class="col-md-3 text-right bold">
                                            <?php echo $total_expired; ?> / <?php echo $total_estimates; ?>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="progress no-margin">
                                                <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_expired; ?>">

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
