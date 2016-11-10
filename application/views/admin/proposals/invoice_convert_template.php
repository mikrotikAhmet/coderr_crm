<div class="modal animated fadeIn proposal-convert-modal" id="convert_to_invoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <?php echo form_open('admin/proposals/convert_to_invoice/'.$proposal->id,array('id'=>'proposal_convert_to_invoice_form')); ?>
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          <span class="edit-title"><?php echo _l('proposal_convert_to_invoice'); ?></span>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <?php $this->load->view('admin/invoices/invoice_template'); ?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
      </div>
    </div>
    <?php echo form_close(); ?>
  </div>
</div>
<script>
  init_selectpicker();
  init_datepicker();
  init_items_sortable();
  init_items_search();
  _validate_form($('#proposal_convert_to_invoice_form'),{clientid:'required',date:'required',currency:'required',number:'required'});
  // Init accountacy currency symbol
  init_currency_symbol($('select[name="currency"]').val());

  <?php if($proposal->assigned != 0){ ?>
   $('#convert_to_invoice #sale_agent').selectpicker('val',<?php echo $proposal->assigned; ?>);
   <?php } ?>
   <?php if($proposal->currency != 0){ ?>
     $('#convert_to_invoice #currency').selectpicker('val',<?php echo $proposal->currency; ?>);
     $('#convert_to_invoice #currency').change();
     <?php } ?>
     <?php
     if($proposal->rel_type == 'lead'){
       if (total_rows('tblclients',array('leadid'=>$proposal->rel_id))){
        $this->db->where('leadid',$proposal->rel_id);
        $lead_converted_client_id = $this->db->get('tblclients')->row();
        ?>
        $('#convert_to_invoice #clientid').selectpicker('val',<?php echo $lead_converted_client_id->userid; ?>);
        <?php }
      } else { ?>
        $('#convert_to_invoice #clientid').selectpicker('val',<?php echo $proposal->rel_id; ?>);
        <?php } ?>
        $('#convert_to_invoice #clientid').change();
      </script>


