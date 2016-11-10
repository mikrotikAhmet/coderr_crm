<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'expense-form','class'=>'dropzone dropzone-manual')) ;?>
            <div class="col-md-5">
                <div class="panel_s">
                    <div class="panel-heading">
                        <?php echo $title; ?>
                    </div>
                    <div class="panel-body">
                        <?php if(isset($expense) && $expense->attachment !== ''){ ?>
                        <div class="row">
                            <div class="col-md-10">
                             <i class="<?php echo get_mime_class($expense->filetype); ?>"></i> <a href="<?php echo site_url('download/file/expense/'.$expense->expenseid); ?>"><?php echo $expense->attachment; ?></a>
                         </div>
                         <div class="col-md-2 text-right">
                            <a href="<?php echo admin_url('expenses/delete_expense_attachment/'.$expense->expenseid); ?>" class="text-danger"><i class="fa fa-trash-o"></i></a>
                        </div>
                    </div>
                    <hr />
                    <?php } ?>

                    <?php if(!isset($expense) || (isset($expense) && $expense->attachment == '')){ ?>
                    <div id="dropzoneDragArea" class="dz-default dz-message">
                      <span><?php echo _l('expense_add_edit_attach_receipt'); ?></span>
                  </div>
                  <div class="dropzone-previews"></div>
                  <?php } ?>

                  <?php $value = (isset($estimate) ? $expense->note : ''); ?>
                  <?php echo render_textarea('note','expense_add_edit_note',$value,array('rows'=>1),array()); ?>
                  <?php $selected = (isset($expense) ? $expense->category : ''); ?>
                  <?php echo render_select('category',$categories,array('id','name'),'expense_category',$selected); ?>

                  <?php $value = (isset($expense) ? $expense->date : date('Y-m-d')); ?>
                  <?php echo render_date_input('date','expense_add_edit_date',$value); ?>
                  <label for="amount"><?php echo _l('expense_add_edit_amount'); ?></label>
                  <div class="input-group" data-toggle="tooltip" title="<?php echo _l('expense_add_edit_amount_tooltip'); ?>">
                    <input type="number" class="form-control" name="amount" value="<?php if(isset($expense)){echo $expense->amount; }?>">
                    <div class="input-group-addon">
                        <?php echo $base_currency->symbol; ?>
                    </div>
                </div>
                <?php $value = (isset($expense) ? $expense->reference_no : ''); ?>
                <?php echo render_input('reference_no','expense_add_edit_reference_no',$value); ?>

                <?php $selected = (isset($expense) ? $expense->clientid : ''); ?>
                <?php echo render_select('clientid',$customers,array('userid',array('firstname','lastname'),'company'),'expense_add_edit_customer',$selected); ?>
                <?php
                $_hide = 'hide';
                if(!isset($expense) ){
                    $_hide = 'hide';
                } else {
                    if($expense->billable == 1 || $expense->clientid != 0){
                        $_hide = '';
                    }
                }
                ?>
                <div class="checkbox checkbox-primary billable <?php echo $_hide; ?>">
                    <input type="checkbox" <?php if(isset($expense) && $expense->invoiceid !== NULL){echo 'disabled'; } ?> name="billable" <?php if(isset($expense)){if($expense->billable == 1){echo 'checked';}}; ?>>
                    <label for="" <?php if(isset($expense) && $expense->invoiceid !== NULL){echo 'data-toggle="tooltip" title="'._l('expense_already_invoiced').'"'; } ?>><?php echo _l('expense_add_edit_billable'); ?></label>
                </div>
                <?php $selected = (isset($expense) ? $expense->paymentmode : ''); ?>
                <?php echo render_select('paymentmode',$payment_modes,array('id','name'),'payment_mode',$selected); ?>
                <div class="form-group">
                    <label class="control-label" for="tax"><?php echo _l('expense_add_edit_tax'); ?></label>
                    <select class="selectpicker display-block" data-width="100%" name="tax">
                        <option value=""><?php echo _l('no_tax'); ?></option>
                        <?php $default_tax = get_option('default_tax'); ?>
                        <?php foreach($taxes as $tax){
                            $selected = '';
                            if(isset($expense)){
                                if($tax['id'] == $expense->tax){
                                    $selected = 'selected';
                                }
                            } else {
                                if($default_tax == $tax['id']){
                                    $selected = 'selected';
                                }
                            }
                            ?>
                            <option value="<?php echo $tax['id']; ?>" <?php echo $selected; ?> data-subtext="<?php echo $tax['name']; ?>"><?php echo $tax['taxrate']; ?>%</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="clearfix mtop15"></div>

                    <?php $rel_id = (isset($expense) ? $expense->expenseid : false); ?>
                    <?php echo render_custom_fields('expenses',$rel_id); ?>



                    <button type="submit" class="btn btn-primary pull-right"><?php echo _l('submit'); ?></button>

                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="panel_s">
                <div class="panel-heading">Advanced Options</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="repeat_every" class="control-label">Repeat Every</label>
                        <select name="repeat_every" id="repeat_every" class="selectpicker" data-width="100%">
                            <option value=""></option>
                            <option value="1-week" <?php if(isset($expense) && $expense->repeat_every == 1 && $expense->recurring_type == 'week'){echo 'selected';} ?>>Week</option>
                            <option value="2-week" <?php if(isset($expense) && $expense->repeat_every == 2 && $expense->recurring_type == 'week'){echo 'selected';} ?>>2 Weeks</option>
                            <option value="1-month" <?php if(isset($expense) && $expense->repeat_every == 1 && $expense->recurring_type == 'month'){echo 'selected';} ?>>1 Month</option>
                            <option value="2-month" <?php if(isset($expense) && $expense->repeat_every == 2 && $expense->recurring_type == 'month'){echo 'selected';} ?>>2 Months</option>
                            <option value="3-month" <?php if(isset($expense) && $expense->repeat_every == 3 && $expense->recurring_type == 'month'){echo 'selected';} ?>>3 Months</option>
                            <option value="6-month" <?php if(isset($expense) && $expense->repeat_every == 6 && $expense->recurring_type == 'month'){echo 'selected';} ?>>6 Months</option>
                            <option value="1-year" <?php if(isset($expense) && $expense->repeat_every == 1 && $expense->recurring_type == 'year'){echo 'selected';} ?>>1 Year</option>
                            <option value="custom" <?php if(isset($expense) && $expense->custom_recurring == 1){echo 'selected';} ?>>Custom</option>
                        </select>
                    </div>
                    <div class="recurring_custom <?php if((isset($expense) && $expense->custom_recurring != 1) || (!isset($expense))){echo 'hide';} ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <?php $value = (isset($expense) && $expense->custom_recurring == 1 ? $expense->repeat_every : ''); ?>
                                <?php echo render_input('repeat_every_custom','',$value,'number'); ?>
                            </div>
                            <div class="col-md-6">
                             <select name="repeat_type_custom" id="repeat_type_custom" class="selectpicker" data-width="100%">
                                <option value="day" <?php if(isset($expense) && $expense->custom_recurring == 1 && $expense->recurring_type == 'day'){echo 'selected';} ?>>Day(s)</option>
                                <option value="week" <?php if(isset($expense) && $expense->custom_recurring == 1 && $expense->recurring_type == 'week'){echo 'selected';} ?>>Week(s)</option>
                                <option value="month" <?php if(isset($expense) && $expense->custom_recurring == 1 && $expense->recurring_type == 'month'){echo 'selected';} ?>>Month(s)</option>
                                <option value="year" <?php if(isset($expense) && $expense->custom_recurring == 1 && $expense->recurring_type == 'year'){echo 'selected';} ?>>Year(s)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div data-toggle="tooltip" title="<?php echo _l('expense_recurring_autocreate_invoice_tooltip'); ?>">
                    <div class="checkbox checkbox-primary billable_recurring_options <?php echo $_hide; ?>">
                        <input type="checkbox" name="create_invoice_billable" <?php if(isset($expense)){if($expense->create_invoice_billable == 1){echo 'checked';}}; ?>>
                        <label for=""><?php echo _l('expense_recurring_auto_create_invoice'); ?></label>
                    </div>
                </div>
                <div class="checkbox checkbox-primary billable_recurring_options <?php echo $_hide; ?>">
                    <input type="checkbox" name="send_invoice_to_customer" <?php if(isset($expense)){if($expense->send_invoice_to_customer == 1){echo 'checked';}}; ?>>
                    <label for=""><?php echo _l('expense_recurring_send_custom_on_renew'); ?></label>
                </div>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
</div>
</div>
<?php init_tail(); ?>
<script>
  Dropzone.autoDiscover = false;
  if($('#dropzoneDragArea').length > 0){
    var expenseDropzone = new Dropzone("#expense-form", {
        autoProcessQueue: false,
        clickable: '#dropzoneDragArea',
        previewsContainer: '.dropzone-previews',
        addRemoveLinks: true,
        maxFiles: 1,
    });
}
$(document).ready(function(){
    _validate_form($('form'),{category:'required',date:'required',amount:'required'},expenseSubmitHandler);
    $('select[name="clientid"]').on('change',function(){
        var val = $(this).val();
        if(val != ''){
            $('.billable').removeClass('hide');
            if ($('input[name="billable"]').prop('checked') == true) {
                $('.billable_recurring_options').removeClass('hide');
            }
        } else {
            $('.billable').addClass('hide');
            $('.billable_recurring_options').addClass('hide');
        }
    });
    $('input[name="billable"]').on('change',function(){
        if ($(this).prop('checked') == true) {
            $('.billable_recurring_options').removeClass('hide');
        } else {
            $('.billable_recurring_options').addClass('hide');
        }
    });
    $('select[name="repeat_every"]').on('change',function(){
        var val = $(this).val();
        if(val == 'custom'){
            $('.recurring_custom').removeClass('hide');
        } else {
            $('.recurring_custom').addClass('hide');
        }
    });
});
function expenseSubmitHandler(form){

  $('input[name="billable"]').prop('disabled',false);
  $.post(form.action, $(form).serialize()).success(function(response) {
    response = $.parseJSON(response);
    if (response.expenseid) {
     if(typeof(expenseDropzone) !== 'undefined'){
        if (expenseDropzone.getQueuedFiles().length > 0) {
            expenseDropzone.options.url = admin_url + 'expenses/add_expense_attachment/' + response.expenseid;
            $.when(expenseDropzone.processQueue()).then(window.location.assign(response.url));
        } else {
            window.location.assign(response.url)
        }
    } else {
        window.location.assign(response.url)
    }
}
});
  return false;
}
</script>
</body>
</html>
