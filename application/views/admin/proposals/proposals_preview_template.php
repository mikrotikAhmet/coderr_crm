<div class="panel_s">
  <div class="panel-body">
   <ul class="nav nav-tabs no-margin" role="tablist">
     <li role="presentation" class="active">
      <a href="#tab_proposal" aria-controls="tab_proposal" role="tab" data-toggle="tab">
       <?php echo _l('proposal'); ?>
     </a>
   </li>
   <?php if(isset($proposal)){ ?>
   <li role="presentation">
    <a href="#tab_comments" aria-controls="tab_comments" role="tab" data-toggle="tab">
     <?php echo _l('proposal_comments'); ?>
   </a>
 </li>
 <li role="presentation">
  <a href="#tab_tasks" aria-controls="tab_tasks" role="tab" data-toggle="tab">
   <?php echo _l('tasks'); ?>
 </a>
</li>
<?php } ?>
</ul>
</div>
</div>
<div class="panel_s animated fadeIn">
  <div class="panel-body">
    <div class="row">
      <div class="col-md-3">
        <?php echo format_proposal_status($proposal->status,'pull-left mright5 mtop10'); ?>
      </div>
      <div class="col-md-9 text-right">
       <a href="<?php echo admin_url('proposals/proposal/'.$proposal->id); ?>" data-toggle="tooltip" title="<?php echo _l('proposal_edit'); ?>" class="btn btn-default mright5" data-placement="bottom"><i class="fa fa-pencil-square-o"></i></a>
       <a href="<?php echo admin_url('proposals/pdf/'.$proposal->id); ?>" class="btn btn-default mright5" data-toggle="tooltip" title="<?php echo _l('proposal_pdf'); ?>" data-placement="bottom"><i class="fa fa-file-pdf-o"></i></a>
       <a href="#" class="btn btn-default add-proposal-items mright5" data-placement="bottom" data-toggle="tooltip" data-title="<?php echo _l('proposal_add_items'); ?>"><i class="fa fa-list"></i></a>
       <a href="#" class="btn btn-default mright5" data-target="#proposal_send_to_customer" data-toggle="modal"><i class="fa fa-envelope" data-toggle="tooltip" data-title="<?php echo _l('proposal_send_to_email'); ?>" data-placement="bottom"></i></a>
       <div class="btn-group ">
        <button type="button" class="btn btn-default dropdown-toggle mright5" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php echo _l('more'); ?> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
         <li>
          <a href="<?php echo site_url('viewproposal/'.$proposal->id .'/'.$proposal->hash); ?>" target="_blank"><?php echo _l('proposal_view'); ?></a>
        </li>
        <li>
          <a href="<?php echo admin_url() . 'proposals/copy/'.$proposal->id; ?>"><?php echo _l('proposal_copy'); ?></a>
        </li>
        <?php if($proposal->estimate_id == NULL && $proposal->invoice_id == NULL){ ?>
        <?php if($proposal->status != '1'){ ?>
        <li>
          <a href="<?php echo admin_url() . 'proposals/mark_action_status/1/'.$proposal->id; ?>"><?php echo _l('proposal_mark_as_open'); ?></a>
        </li>
        <?php } ?>
        <?php if($proposal->status != '2'){ ?>
        <li>
          <a href="<?php echo admin_url() . 'proposals/mark_action_status/2/'.$proposal->id; ?>"><?php echo _l('proposal_mark_as_declined'); ?></a>
        </li>
        <?php } ?>
        <?php if($proposal->status != '3'){ ?>
        <li>
          <a href="<?php echo admin_url() . 'proposals/mark_action_status/3/'.$proposal->id; ?>"><?php echo _l('proposal_mark_as_accepted'); ?></a>
        </li>
        <?php } ?>
        <?php if($proposal->status != '4'){ ?>
        <li>
          <a href="<?php echo admin_url() . 'proposals/mark_action_status/4/'.$proposal->id; ?>"><?php echo _l('proposal_mark_as_sent'); ?></a>
        </li>
        <?php } ?>
        <?php } ?>
        <li>
          <a href="<?php echo admin_url() . 'proposals/delete/'.$proposal->id; ?>"><?php echo _l('proposal_delete'); ?></a>
        </li>
      </ul>
    </div>
    <?php if($proposal->estimate_id == NULL && $proposal->invoice_id == NULL){ ?>
    <div class="btn-group">
      <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <?php echo _l('proposal_convert'); ?> <span class="caret"></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-right">
        <?php
        $disable_convert = false;
        $not_related = false;

        if($proposal->rel_type == 'lead'){
          if(total_rows('tblclients',array('leadid'=>$proposal->rel_id)) == 0){
            $disable_convert = true;
            $help_text = 'proposal_convert_to_lead_disabled_help';
          }
        } else if(empty($proposal->rel_type)){
          $disable_convert = true;
          $help_text = 'proposal_convert_not_related_help';
        }
        ?>
        <li <?php if($disable_convert){ echo 'data-toggle="tooltip" title="'._l($help_text,_l('proposal_convert_estimate')).'"';} ?>><a href="#" <?php if($disable_convert){ echo 'style="cursor:not-allowed;" onclick="return false;"';} else {echo 'data-template="estimate" onclick="convert_template(this); return false;"';} ?>><?php echo _l('proposal_convert_estimate'); ?></a></li>

        <li <?php if($disable_convert){ echo 'data-toggle="tooltip" title="'._l($help_text,_l('proposal_convert_invoice')).'"';} ?>><a href="#" <?php if($disable_convert){ echo 'style="cursor:not-allowed;" onclick="return false;"';} else {echo 'data-template="invoice" onclick="convert_template(this); return false;"';} ?>><?php echo _l('proposal_convert_invoice'); ?></a></li>
      </ul>
    </div>
    <?php } else {
      if($proposal->estimate_id != NULL){
        echo '<a href="'.admin_url('estimates/list_estimates/'.$proposal->estimate_id).'" class="btn btn-info">'.format_estimate_number($proposal->estimate_id).'</a>';
      } else {
       echo '<a href="'.admin_url('invoices/list_invoices/'.$proposal->invoice_id).'" class="btn btn-info">'.format_invoice_number($proposal->invoice_id).'</a>';
     }
   } ?>
 </div>
</div>
<div class="clearfix"></div>
<hr />
<div class="row">
  <div class="col-md-12">
    <div class="tab-content">
     <div role="tabpanel" class="tab-pane active" id="tab_proposal">
       <div class="row mtop10">
         <div class="col-md-6">
           <h4 class="bold mbot15"><a href="<?php echo admin_url('proposals/proposal/'.$proposal->id); ?>"><?php echo $proposal->subject; ?></a></h4>
           <address>
            <span class="bold"><a href="<?php echo admin_url('settings?tab_hash=company_info'); ?>" target="_blank"><?php echo get_option('invoice_company_name'); ?></span></a><br>
            <?php echo get_option('invoice_company_address'); ?><br>
            <?php echo get_option('invoice_company_city'); ?>, <?php echo get_option('invoice_company_country_code'); ?> <?php echo get_option('invoice_company_postal_code'); ?><br>
            <?php if(get_option('invoice_company_phonenumber') != ''){ ?>
            <abbr title="Phone">P:</abbr> <a href="tel:<?php echo get_option('invoice_company_phonenumber'); ?>"><?php echo get_option('invoice_company_phonenumber'); ?></a>
            <?php } ?>
            <?php
            ?>
          </address>
        </div>
        <div class="col-md-6 text-right">
          <address>
            <span class="bold"><?php echo _l('proposal_to'); ?>:</span><br />
            <?php
            if(!empty($proposal->proposal_to)){
              if(!empty($proposal->rel_id) && $proposal->rel_id != 0){
                if($proposal->rel_type == 'lead'){
                  echo '<a href="'.admin_url('leads/lead/'.$proposal->rel_id).'" data-toggle="tooltip" data-title="'._l('lead').'">'.$proposal->proposal_to.'</a><br />';
                } else if($proposal->rel_type == 'customer'){
                  echo '<a href="'.admin_url('clients/client/'.$proposal->rel_id).'" data-toggle="tooltip" data-title="'._l('client').'">'.$proposal->proposal_to.'</a><br />';
                } else {
                  echo $proposal->proposal_to . '<br />';
                }
              } else {
                echo $proposal->proposal_to . '<br />';
              }
            }
            if(!empty($proposal->address)){
              echo $proposal->address . '<br />';
            }
            if(!empty($proposal->email)){
              echo '<a href="mailto:'.$proposal->email.'">' . $proposal->email . '</a><br />';
            }
            if(!empty($proposal->phone)){
              echo '<abbr title="Phone">P: </abbr>' . '<a href="tel:'.$proposal->phone.'">'.$proposal->phone.'</a>';
            }
            ?>
          </address>
          <?php
          $custom_fields = get_custom_fields('proposal');
          foreach($custom_fields as $field){ ?>
          <?php $value = get_custom_field_value($proposal->id,$field['id'],'proposal');
          if($value == ''){continue;} ?>
          <br /> <span class="bold"><?php echo ucfirst($field['name']); ?>: </span><?php echo $value; ?>
          <?php } ?>
        </div>
      </div>
      <hr />
      <a href="#" onclick="set_editor_edit_mode(); return false;" class="text-success bold"><?php echo _l('click_here_to_edit'); ?></a>
      <hr />
      <div data-editable data-name="main-content">
        <?php echo $proposal->content; ?>
      </div>

    </div>
    <div role="tabpanel" class="tab-pane" id="tab_comments">
      <div class="row proposal-comments mtop15">
        <div class="col-md-12">
         <div id="proposal-comments"></div>
         <div class="clearfix"></div>

         <textarea name="content" id="comment" rows="4" class="form-control mtop15 proposal-comment"></textarea>
         <button type="button" class="btn btn-primary mtop10 pull-right" onclick="add_proposal_comment();"><?php echo _l('proposal_add_comment'); ?></button>
       </div>
     </div>
   </div>
   <div role="tabpanel" class="tab-pane" id="tab_tasks">
     <?php init_relation_tasks_table(array( 'data-new-rel-id'=>$proposal->id,'data-new-rel-type'=>'proposal')); ?>
   </div>
 </div>
</div>
</div>
</div>
</div>
<div id="items_helper_area"></div>
<div class="modal animated fadeIn" id="proposal_send_to_customer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <?php echo form_open('admin/proposals/send_to_email/'.$proposal->id); ?>
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          <span class="edit-title"><?php echo _l('proposal_send_to_email_title'); ?></span>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="checkbox checkbox-primary">
              <input type="checkbox" name="attach_pdf" checked>
              <label for=""><?php echo _l('proposal_attach_pdf'); ?></label>
            </div>
            <h5 class="bold"><?php echo _l('proposal_preview_template'); ?></h5>
            <hr />
            <?php echo $template->message; ?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-primary"><?php echo _l('send'); ?></button>
      </div>
    </div>
    <?php echo form_close(); ?>
  </div>
</div>
<script>
  // defined in manage proposals
  proposal_id = '<?php echo $proposal->id; ?>';
  initDataTable('.table-rel-tasks', admin_url + 'tasks/init_relation_tasks/<?php echo $proposal->id; ?>/proposal', 'proposals');
  init_proposal_editor();
  get_proposal_comments();
</script>
<div id="convert_helper"></div>
