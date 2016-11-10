<div class="col-md-12 no-padding animated fadeIn">
  <div class="panel_s">
    <div class="panel-body padding-16">
      <?php if($expense->recurring == 1){
        echo '<div class="ribbon warning"><span>'._l('expense_recurring_indicator').'</span></div>';
      } ?>
      <ul class="nav nav-tabs no-margin" role="tablist">
        <li role="presentation" class="active">
          <a href="#tab_expense" aria-controls="tab_expense" role="tab" data-toggle="tab">
            <?php echo _l('expense'); ?>
          </a>
        </li>
        <li role="presentation">
          <a href="#tab_tasks" aria-controls="tab_tasks" role="tab" data-toggle="tab">
            <?php echo _l('tasks'); ?>
          </a>
        </li>
      </ul>
    </div>
  </div>
  <div class="panel_s">
    <div class="panel-body">
      <div class="row">
        <div class="col-md-7">
          <h3 class="bold no-margin"><?php echo $expense->category_name; ?></h3>
        </div>
        <div class="col-md-5 text-right">
         <?php if($expense->billable == 1 && $expense->invoiceid == NULL){ ?>
         <a href="<?php echo admin_url('expenses/convert_to_invoice/'.$expense->expenseid); ?>" class="btn mleft10 pull-right btn-success"><?php echo _l('expense_convert_to_invoice'); ?></a>
         <?php } else if($expense->invoiceid != NULL){ ?>
         <a href="<?php echo admin_url('invoices/list_invoices/'.$expense->invoiceid); ?>" class="btn mleft10 pull-right btn-info <?php if($expense->recurring == 1){echo 'mright20';} ?>"><?php echo format_invoice_number($invoice->id); ?></a>
         <?php } ?>
         <div class="pull-right">
          <a class="btn btn-default mright5" href="<?php echo admin_url('expenses/expense/'.$expense->expenseid); ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo _l('expense_edit'); ?>"><i class="fa fa-pencil-square-o"></i></a>
          <a class="btn btn-default mright5" href="<?php echo admin_url('expenses/delete/'.$expense->expenseid); ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo _l('expense_delete'); ?>"><i class="fa fa-remove"></i></a>
          <a class="btn btn-default mright5" href="<?php echo admin_url('expenses/copy/'.$expense->expenseid); ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo _l('expense_copy'); ?>"><i class="fa fa-clone"></i></a>
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
    <hr />
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane ptop10 active" id="tab_expense">
       <div class="row">
        <div class="col-md-6">
          <p><?php echo _l('expense_amount'); ?> <span class="text-danger bold font-medium"><?php echo format_money($expense->amount,$base_currency->symbol); ?></span>
            <?php if($expense->paymentmode != 0){ ?>
            <span class="text-muted">Paid via <?php echo $expense->payment_mode_name; ?></span>
            <?php } ?>
            <?php if($expense->tax != 0){
              echo '<br />'._l('expense_tax') .' ' . $expense->taxrate . ' ('.$expense->tax_name.')';
              $total = $expense->amount;
              $_total = ($total / 100 * $expense->taxrate);
              $total += $_total;
              echo ' - ' . format_money($total,$base_currency->symbol);
            }
            ?>
            <?php if($expense->billable == 1){
              echo '<br />';
              echo '<br />';
              if($expense->invoiceid == NULL){
                echo '<span class="text-danger">'._l('expense_invoice_not_created').'</span>';
              } else {
                if($invoice->status == 2){
                  echo '<span class="text-success">'._l('expense_billed').'</span>';
                } else {
                  echo '<span class="text-danger">'._l('expense_not_billed').'</span>';
                }
              }
            } ?>
          </p>
          <p><?php echo _l('expense_date'); ?> <span class="text-muted"><?php echo _d($expense->date); ?></span></p>
          <br />
          <br />
          <p><?php echo _l('expense_ref_noe'); ?> <span class="text-muted"><?php echo $expense->reference_no; ?></span></p>
          <?php if($expense->clientid != 0){ ?>
          <p><?php echo _l('expense_customer'); ?></p>
          <p><a href="<?php echo admin_url('clients/client/'.$expense->clientid); ?>"><?php echo $expense->firstname . ' ' .$expense->lastname; ?></a></p>
          <?php } ?>
          <?php
          $custom_fields = get_custom_fields('expenses');
          foreach($custom_fields as $field){ ?>
          <?php $value = get_custom_field_value($expense->expenseid,$field['id'],'expenses');
          if($value == ''){continue;} ?>
          <div class="row mbot10">
            <div class="col-md-12 mtop5">
              <span class="bold"><?php echo ucfirst($field['name']); ?></span>
              <br />
              <div class="text-left">
                <?php echo $value; ?>
              </div>
            </div>
          </div>
          <?php } ?>
          <?php if($expense->note != ''){ ?>
          <p><?php echo _l('expense_note'); ?></p>
          <p class="text-muted"><?php echo $expense->note; ?></p>
          <?php } ?>
        </div>
        <div class="col-md-6">
         <h4 class="bold text-muted no-margin"><?php echo _l('expense_receipt'); ?></h4>
         <hr />
         <?php if(empty($expense->attachment)) { ?>
         <?php echo form_open('admin/expenses/add_expense_attachment/'.$expense->expenseid,array('class'=>'mtop10 dropzone dropzone-expense-preview dropzone-manual','id'=>'expense-receipt-upload')); ?>
         <div id="dropzoneDragArea" class="dz-default dz-message">
          <span><?php echo _l('expense_add_edit_attach_receipt'); ?></span>
        </div>
        <?php echo form_close(); ?>
        <?php }  else { ?>
        <div class="row">
          <div class="col-md-10">
           <i class="<?php echo get_mime_class($expense->filetype); ?>"></i> <a href="<?php echo site_url('download/file/expense/'.$expense->expenseid); ?>"> <?php echo $expense->attachment; ?></a>
         </div>
         <div class="col-md-2 text-right">
          <a href="<?php echo admin_url('expenses/delete_expense_attachment/'.$expense->expenseid .'/'.'preview'); ?>" class="text-danger"><i class="fa fa-trash-o"></i></a>
        </div>
      </div>
      <?php } ?>
    </div>
  </div>
</div>
<div role="tabpanel" class="tab-pane ptop10" id="tab_tasks">
  <?php init_relation_tasks_table(array('data-new-rel-id'=>$expense->expenseid,'data-new-rel-type'=>'expense')); ?>
</div>
</div>
</div>
</div>
</div>
<script>
  initDataTable('.table-rel-tasks', admin_url +'tasks/init_relation_tasks/<?php echo $expense->expenseid; ?>/expense', 'tasks');
  if($('#dropzoneDragArea').length > 0){
    var expenseDropzone = new Dropzone("#expense-receipt-upload", {
      clickable: '#dropzoneDragArea',
      maxFiles: 1,
      success:function(file,response){
        init_expense(<?php echo $expense->expenseid; ?>);
      }
    });
  }
</script>

