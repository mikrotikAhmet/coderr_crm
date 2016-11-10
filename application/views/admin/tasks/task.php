 <?php echo form_open(admin_url('tasks/task/'.$id),array('id'=>'task-form')); ?>
 <div class="modal fade" id="_task_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          <?php echo $title; ?>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <?php
            $rel_type = '';
            $rel_id = '';
            if(isset($task) || ($this->input->get('rel_id') && $this->input->get('rel_type'))){
              if($this->input->get('rel_id')){
                $rel_id = $this->input->get('rel_id');
                $rel_type = $this->input->get('rel_type');
              } else {
                $rel_id = $task->rel_id;
                $rel_type = $task->rel_type;
              }
              ?>
               <?php echo form_hidden('task_rel_id',$rel_id); ?>
              <div class="clearfix"></div>
              <?php } ?>
              <div class="form-group">
                <div class="checkbox checkbox-primary" data-toggle="tooltip" title="<?php echo _l('task_public_help'); ?>">
                  <input type="checkbox" name="is_public" <?php if(isset($task)){if($task->is_public == 1){echo 'checked';}}; ?>>
                  <label for=""><?php echo _l('task_public'); ?></label>
                </div>
              </div>
              <?php $value = (isset($task) ? $task->name : ''); ?>
              <?php echo render_input('name','task_add_edit_subject',$value); ?>
              <div class="form-group">
                <label for="priority" class="control-label"><?php echo _l('task_add_edit_priority'); ?></label>
                <select name="priority" class="selectpicker" id="priority" data-width="100%">
                  <option value="Low"><?php echo _l('task_priority_low'); ?></option>
                  <option value="Medium"><?php echo _l('task_priority_medium'); ?></option>
                  <option value="High"><?php echo _l('task_priority_high'); ?></option>
                  <option value="Urgent"><?php echo _l('task_priority_urgent'); ?></option>
                </select>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <?php $value = (isset($task) ? $task->startdate : _d(date('Y-m-d'))); ?>
                  <?php echo render_date_input('startdate','task_add_edit_start_date',$value); ?>
                </div>
                <div class="col-md-6">
                  <?php $value = (isset($task) ? $task->duedate : ''); ?>
                  <?php echo render_date_input('duedate','task_add_edit_due_date',$value); ?>
                </div>
              </div>

              <div class="form-group">
                <label for="rel_type" class="control-label"><?php echo _l('task_related_to'); ?></label>
                <select name="rel_type" class="selectpicker" id="rel_type" data-width="100%">
                  <option value=""></option>
                  <option value="customer"
                  <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'customer'){echo 'selected';}} ?>>Customer</option>
                  <option value="invoice" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'invoice'){echo 'selected';}} ?>>Invoice</option>
                  <option value="estimate" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'estimate'){echo 'selected';}} ?>>Estimate</option>
                  <option value="contract" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'contract'){echo 'selected';}} ?>>Contract</option>
                  <option value="ticket" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'ticket'){echo 'selected';}} ?>>Ticket</option>
                  <option value="expense" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'expense'){echo 'selected';}} ?>>Expense</option>
                  <option value="lead" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'lead'){echo 'selected';}} ?>>Lead</option>
                  <option value="proposal" <?php if(isset($task) || $this->input->get('rel_type')){if($rel_type == 'proposal'){echo 'selected';}} ?>>Proposal</option>
                </select>
              </div>
              <div class="form-group hide" id="rel_id_wrapper">
                <select name="rel_id" id="rel_id" class="selectpicker" data-width="100%" data-live-search="true"></select>
              </div>
              <?php $rel_id_custom_field = (isset($task) ? $task->id : false); ?>
              <?php echo render_custom_fields('tasks',$rel_id_custom_field); ?>
              <p class="bold"><?php echo _l('task_add_edit_description'); ?></p>
              <?php $contents = ''; if(isset($task)){$contents = $task->description;} ?>
              <?php $this->load->view('admin/editor/template',array('name'=>'description','contents'=>$contents,'editor_name'=>'task')); ?>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
          <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
        </div>
      </div>
    </div>
    <?php echo form_close(); ?>
    <script>
     _validate_form($('#task-form'), {
       name: 'required',
       startdate: 'required'
     },task_form_handler);
     init_relation_data();
     $('select[name="rel_type"]').on('change', function() {
       init_relation_data();
     });

     init_datepicker();
     init_selectpicker();
     var Editor_tasks;
    // var container = editor.addContainer(container);
     var Editor_tasks = new Quill('.editor-container-task', {
      modules: {
        'toolbar': {
          container: '.toolbar-container-task'
        },
        'link-tooltip': true,
        'image-tooltip': true,
        'multi-cursor': true
      },
      theme: 'snow'
    });
     Editor_tasks.on('text-change', function(delta, source) {
      data = Editor_tasks.getHTML();
      $('.editor_change_contents-task').val(data);
    });
   Editor_tasks.setHTML($('.editor_contents-task').html());



     function init_relation_data(data) {
       var data = {};
       var task_rel_id = $('input[name="task_rel_id"]');
       var type = $('select[name="rel_type"]');
       if (task_rel_id.length > 0) {
         data.type = type.val();
         if (data.type == '') {
           return;
         } else {
           $('#rel_id_wrapper').removeClass('hide');
           data.rel_id = task_rel_id.val();
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
