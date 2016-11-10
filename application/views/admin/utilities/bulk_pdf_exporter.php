<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-5">
                <div class="panel_s">
                    <div class="panel-heading">
                        <?php echo $title; ?>
                    </div>
                    <div class="panel-body">
                        <?php echo form_open($this->uri->uri_string()); ?>
                        <div class="form-group">
                            <label for="export_type"><?php echo _l('bulk_pdf_export_select_type'); ?></label>
                            <select name="export_type" id="export_type" class="selectpicker" data-width="100%">
                                <option value=""></option>
                                <option value="invoices"><?php echo _l('bulk_export_pdf_invoices'); ?></option>
                                <option value="estimates"><?php echo _l('bulk_export_pdf_estimates'); ?></option>
                                <option value="payments"><?php echo _l('bulk_export_pdf_payments'); ?></option>
                                <option value="proposals"><?php echo _l('bulk_export_pdf_proposals'); ?></option>
                            </select>
                        </div>
                        <?php echo render_date_input('date-from','zip_from_date'); ?>
                        <?php echo render_date_input('date-to','zip_to_date'); ?>
                        <?php echo render_input('tag','bulk_export_include_tag','','text',array('data-toggle'=>'tooltip','title'=>'bulk_export_include_tag_help')); ?>
                        <div class="form-group hide shifter" id="estimates_status">
                            <label for="estimate_zip_status"><?php echo _l('bulk_export_status'); ?></label>
                            <div class="radio radio-primary">
                                <input type="radio" value="all" checked name="estimate_export_status">
                                <label for="all"><?php echo _l('bulk_export_status_all'); ?></label>
                            </div>
                            <div class="radio radio-primary">
                                <input type="radio" value="1" name="estimate_export_status">
                                <label for="<<?php echo _l('estimate_status_draft'); ?>"><?php echo _l('estimate_status_draft'); ?></label>
                            </div>
                            <div class="radio radio-primary">
                                <input type="radio" value="2" name="estimate_export_status">
                                <label for="<<?php echo _l('estimate_status_sent'); ?>"><?php echo _l('estimate_status_sent'); ?></label>
                            </div>
                            <div class="radio radio-primary">
                                <input type="radio" value="3" name="estimate_export_status">
                                <label for="<<?php echo _l('estimate_status_declined'); ?>"><?php echo _l('estimate_status_declined'); ?></label>
                            </div>
                            <div class="radio radio-primary">
                                <input type="radio" value="4" name="estimate_export_status">
                                <label for="<<?php echo _l('estimate_status_accepted'); ?>"><?php echo _l('estimate_status_accepted'); ?></label>
                            </div>
                            <div class="radio radio-primary">
                                <input type="radio" value="4" name="estimate_export_status">
                                <label for="<<?php echo _l('estimate_status_expired'); ?>"><?php echo _l('estimate_status_expired'); ?></label>
                            </div>
                        </div>
                        <div class="form-group hide shifter" id="invoices_status">
                            <label for="invoice_export_status"><?php echo _l('bulk_export_status'); ?></label>
                            <div class="radio radio-primary">
                                <input type="radio" value="all" checked name="invoice_export_status">
                                <label for="all"><?php echo _l('bulk_export_status_all'); ?></label>
                            </div>
                            <div class="radio radio-primary">
                                <input type="radio" value="1" name="invoice_export_status">
                                <label for="<<?php echo _l('invoice_status_unpaid'); ?>"><?php echo _l('invoice_status_unpaid'); ?></label>
                            </div>
                            <div class="radio radio-primary">
                                <input type="radio" value="2" name="invoice_export_status">
                                <label for="<<?php echo _l('invoice_status_paid'); ?>"><?php echo _l('invoice_status_paid'); ?></label>
                            </div>
                            <div class="radio radio-primary">
                                <input type="radio" value="3" name="invoice_export_status">
                                <label for="<<?php echo _l('invoice_status_not_paid_completely'); ?>"><?php echo _l('invoice_status_not_paid_completely'); ?></label>
                            </div>
                            <div class="radio radio-primary">
                                <input type="radio" value="4" name="invoice_export_status">
                                <label for="<<?php echo _l('invoice_status_overdue'); ?>"><?php echo _l('invoice_status_overdue'); ?></label>
                            </div>
                        </div>
                        <div class="form-group hide shifter" id="proposal_status">
                          <label for="proposal_export_status"><?php echo _l('bulk_export_status'); ?></label>
                          <div class="radio radio-primary">
                            <input type="radio" value="all" checked name="proposal_export_status">
                            <label for="all"><?php echo _l('bulk_export_status_all'); ?></label>
                        </div>
                        <div class="radio radio-primary">
                            <input type="radio" value="1" name="proposal_export_status">
                            <label for="<?php echo _l('proposal_status_open'); ?>"><?php echo _l('proposal_status_open'); ?></label>
                        </div>
                        <div class="radio radio-primary">
                            <input type="radio" value="2" name="proposal_export_status">
                            <label for="<?php echo _l('proposal_status_declined'); ?>"><?php echo _l('proposal_status_declined'); ?></label>
                        </div>
                        <div class="radio radio-primary">
                            <input type="radio" value="3" name="proposal_export_status">
                            <label for="<?php echo _l('proposal_status_accepted'); ?>"><?php echo _l('proposal_status_accepted'); ?></label>
                        </div>
                        <div class="radio radio-primary">
                            <input type="radio" value="4" name="proposal_export_status">
                            <label for="<?php echo _l('proposal_status_sent'); ?>"><?php echo _l('proposal_status_sent'); ?></label>
                        </div>
                    </div>
                    <div class="form-group hide shifter" id="payment_modes">
                        <?php
                        array_unshift($payment_modes,array('id'=>'','name'=>_l('bulk_export_status_all')));
                        echo render_select('paymentmode',$payment_modes,array('id','name'),'bulk_export_zip_payment_modes');
                        ?>
                    </div>
                    <button class="btn btn-primary" type="submit"><?php echo _l('bulk_pdf_export_button'); ?></button>


                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php init_tail(); ?>
<script>
    $(document).ready(function(){
        _validate_form($('form'),{export_type:'required'});
        $('#export_type').on('change',function(){
            var val = $(this).val();
            $('.shifter').addClass('hide');
            if(val == 'invoices'){
               $('#invoices_status').removeClass('hide');
           } else if(val == 'estimates'){
               $('#estimates_status').removeClass('hide');
           } else if(val == 'payments'){
            $('#payment_modes').removeClass('hide');
        } else if(val == 'proposals'){
            $('#proposal_status').removeClass('hide');
        }
    });
    });
</script>
</body>
</html>
