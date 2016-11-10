<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
      <?php
      $rel_type = '';
      $rel_id = '';
      if(isset($proposal) || ($this->input->get('rel_id') && $this->input->get('rel_type'))){
        if($this->input->get('rel_id')){
          $rel_id = $this->input->get('rel_id');
          $rel_type = $this->input->get('rel_type');
        } else {
          $rel_id = $proposal->rel_id;
          $rel_type = $proposal->rel_type;
        }
      }
      echo form_hidden('proposal_rel_id',$rel_id);
      ?>
      <?php echo form_open($this->uri->uri_string(),array('id'=>'proposal-form')); ?>
      <div class="col-md-6">
       <div class="panel_s">

        <div class="panel-body">
          <?php $value = (isset($proposal) ? $proposal->subject : ''); ?>
          <?php echo render_input('subject','proposal_subject',$value); ?>
          <div class="form-group">
            <label for="rel_type" class="control-label"><?php echo _l('proposal_related'); ?></label>
            <select name="rel_type" class="selectpicker" data-width="100%">
              <option value=""></option>
              <option value="lead" <?php if((isset($proposal) && $proposal->rel_type == 'lead') || $this->input->get('rel_type')){if($rel_type == 'lead'){echo 'selected';}} ?>><?php echo _l('proposal_for_lead'); ?></option>
              <option value="customer" <?php if((isset($proposal) &&  $proposal->rel_type == 'customer') || $this->input->get('rel_type')){if($rel_type == 'customer'){echo 'selected';}} ?>><?php echo _l('proposal_for_customer'); ?></option>
            </select>
          </div>
          <div class="form-group hide" id="rel_id_wrapper">
            <select name="rel_id" id="rel_id" class="selectpicker" data-width="100%" data-live-search="true"></select>
          </div>
          <div class="form-group">
            <label for="status" class="control-label"><?php echo _l('proposal_status'); ?></label>
            <?php
            $disabled = '';
            if(isset($proposal)){
              if($proposal->estimate_id != NULL || $proposal->invoice_id != NULL){
                $disabled = 'disabled';
              }
            }
            ?>
            <select name="status" class="selectpicker" data-width="100%" <?php echo $disabled; ?>>
              <option value="1" <?php if(isset($proposal)){if($proposal->status == 1){echo 'selected';}}else{echo 'selected';} ?>><?php echo _l('proposal_status_open'); ?></option>
              <option value="2" <?php if(isset($proposal) && $proposal->status ==2){echo 'selected';} ?>><?php echo _l('proposal_status_declined'); ?></option>
              <option value="3" <?php if(isset($proposal) && $proposal->status ==3){echo 'selected';} ?>><?php echo _l('proposal_status_accepted'); ?></option>
              <option value="4" <?php if(isset($proposal) && $proposal->status ==4){echo 'selected';} ?>><?php echo _l('proposal_status_sent'); ?></option>
            </select>
          </div>

          <?php
          $i = 0;
          $selected = '';
          foreach($staff as $member){
            if(!has_permission('manageSales',$member['staffid'])){
              unset($staff[$i]);
            }
            if(isset($proposal)){
              if($proposal->assigned == $member['staffid']) {
                $selected = $member['staffid'];
              }
            }
            $i++;
          }
          echo render_select('assigned',$staff,array('staffid',array('firstname','lastname')),'proposal_assigned',$selected);
          ?>

          <?php $value = (isset($proposal) ? $proposal->date : _d(date('Y-m-d'))) ?>
          <?php echo render_date_input('date','proposal_date',$value); ?>

          <?php $value = (isset($proposal) ? $proposal->open_till : _d(date('Y-m-d',strtotime('+7 DAY',strtotime(date('Y-m-d')))))); ?>
          <?php echo render_date_input('open_till','proposal_open_till',$value); ?>


          <?php $value = (isset($proposal) ? $proposal->total : ''); ?>
          <?php echo render_input('total','proposal_total',$value,'number'); ?>
          <?php
          $selected = '';
          foreach($currencies as $currency){
            if(isset($proposal)){
              if($currency['id'] == $proposal->currency){
                $selected = $currency['id'];
              }
            } else {
              if($currency['isdefault'] == 1){
                $selected = $currency['id'];
              }
            }
          }
          ?>
          <?php echo render_select('currency',$currencies,array('id','symbol','name'),'proposal_currency',$selected);
          ?>
          <div class="form-group">
            <div class="checkbox checkbox-primary" data-toggle="tooltip" title="<?php echo _l('proposal_allow_comments_help'); ?>">
              <input type="checkbox" name="allow_comments" <?php if(isset($proposal)){if($proposal->allow_comments == 1){echo 'checked';}}; ?>>
              <label for=""><?php echo _l('proposal_allow_comments'); ?></label>
            </div>
          </div>
          <button class="btn btn-primary pull-right" type="submit"><?php echo _l('submit'); ?></button>

        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="panel_s">
        <div class="panel-body">
         <?php $value = (isset($proposal) ? $proposal->proposal_to : ''); ?>
         <?php echo render_input('proposal_to','proposal_to',$value); ?>
         <?php $value = (isset($proposal) ? $proposal->address : ''); ?>
         <?php echo render_input('address','proposal_address',$value); ?>
         <?php $value = (isset($proposal) ? $proposal->email : ''); ?>
         <?php echo render_input('email','proposal_email',$value); ?>
         <?php $value = (isset($proposal) ? $proposal->phone : ''); ?>
         <?php echo render_input('phone','proposal_phone',$value); ?>

         <?php $rel_id = (isset($proposal) ? $proposal->id : false); ?>
         <?php echo render_custom_fields('proposal',$rel_id); ?>

       </div>
     </div>
   </div>
   <?php echo form_close(); ?>
 </div>
</div>
</div>
<?php init_tail(); ?>
<script>
  $(document).ready(function() {
   init_relation_data();
   _validate_form($('form'), {
     subject : 'required',
     proposal_to : 'required',
     rel_type: 'required',
     rel_id : 'required',
     'date' : 'required',
     email: {
      email:true,
      required:true
    },
    currency :  {
      required : {
        depends:function(element){
          if($('input[name="total"]').val() != ''){
            return true;
          } else {
            return false;
          }
        }
      }
    }
  });
   $('select[name="rel_id"]').on('change', function() {
     var rel_id = $(this).val();
     var rel_type = $('select[name="rel_type"]').val();
     $.get(admin_url + 'proposals/get_relation_data_values/' + rel_id + '/' + rel_type, function(response) {
       $('input[name="proposal_to"]').val(response.to);
       $('input[name="address"]').val(response.address);
       $('input[name="email"]').val(response.email);
       $('input[name="phone"]').val(response.phone);
             // check if customer default currency is passed
             if(response.currency){
              $('#currency').selectpicker('val',response.currency);
            }
          }, 'json');
   });
   $('select[name="rel_type"]').on('change', function() {
     init_relation_data();
   });
 });

  function init_relation_data(data) {
   var data = {};
   var rel_id = $('input[name="proposal_rel_id"]');
   var type = $('select[name="rel_type"]');
   if (rel_id.length > 0) {
     data.type = type.val();
     if (data.type == '') {
       return;
     } else {
       $('#rel_id_wrapper').removeClass('hide');
       data.rel_id = rel_id.val();
     }
   } else {
     data.type = type.val();
     if (data.type == '') {
       $('#rel_id_wrapper').addClass('hide');
       return;
     } else {
       $('#rel_id_wrapper').removeClass('hide');
     }
   }
   $.post(admin_url + 'misc/get_relation_data', data).success(function(response) {
     $('#rel_id').html(response);
     $('#rel_id').selectpicker('refresh');
   });
 }

</script>
</body>
</html>
