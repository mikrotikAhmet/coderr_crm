                    <!-- Modal -->
                    <div class="modal fade" id="client_zip_invoices" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <?php echo form_open('admin/clients/zip_invoices/'.$client->userid); ?>
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel"><?php echo _l('client_zip_invoices'); ?></h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="invoice_zip_status"><?php echo _l('client_zip_status'); ?></label>
                                                <div class="radio radio-primary">
                                                    <input type="radio" value="all" checked name="invoice_zip_status">
                                                    <label for="all"><?php echo _l('client_zip_status_all'); ?></label>
                                                </div>
                                                <div class="radio radio-primary">
                                                    <input type="radio" value="1" name="invoice_zip_status">
                                                    <label for="<<?php echo _l('invoice_status_unpaid'); ?>"><?php echo _l('invoice_status_unpaid'); ?></label>
                                                </div>
                                                <div class="radio radio-primary">
                                                    <input type="radio" value="2" name="invoice_zip_status">
                                                    <label for="<<?php echo _l('invoice_status_paid'); ?>"><?php echo _l('invoice_status_paid'); ?></label>
                                                </div>
                                                <div class="radio radio-primary">
                                                    <input type="radio" value="3" name="invoice_zip_status">
                                                    <label for="<<?php echo _l('invoice_status_not_paid_completely'); ?>"><?php echo _l('invoice_status_not_paid_completely'); ?></label>
                                                </div>
                                                <div class="radio radio-primary">
                                                    <input type="radio" value="4" name="invoice_zip_status">
                                                    <label for="<<?php echo _l('invoice_status_overdue'); ?>"><?php echo _l('invoice_status_overdue'); ?></label>
                                                </div>
                                            </div>
                                            <?php
                                            if($client->company != ''){
                                                $file_name = slug_it($client->company);
                                            } else {
                                                $file_name = slug_it($client->firstname . ' ' .$client->lastname);
                                            }
                                            ?>
                                            <?php include(APPPATH .'views/admin/clients/modals/modal_zip_date_picker.php'); ?>
                                            <?php echo form_hidden('file_name',$file_name); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                                    <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
