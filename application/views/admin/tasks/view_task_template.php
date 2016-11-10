<div class="col-md-12 task-single-col no-padding">
  <div class="panel_s">
    <div class="panel-body">
      <?php if($task->finished == 1){
       echo '<div class="ribbon success"><span>'._l('task_finished').'</span></div>';
     } ?>
     <div class="row padding-5 task-info-wrapper">
      <div class="col-md-10">
       <div class="label label-info task-info pull-left">
         <h5 class="no-margin"><i class="fa pull-left fa-bolt"></i> <?php echo _l('task_single_priority'); ?>: <?php echo $task->priority; ?></h5>
       </div>
       <div class="label label-info mleft10 task-info  pull-left">
         <h5 class="no-margin"><i class="fa pull-left fa-margin"></i> <?php echo _l('task_single_start_date'); ?>: <?php echo _d($task->startdate); ?></h5>
       </div>
       <div class="label mleft10 task-info pull-left <?php if(!$task->finished){echo ' label-danger'; }else{echo 'label-info';} ?><?php if(!$task->duedate){ echo ' hide';} ?>">
         <h5 class="no-margin"><i class="fa pull-left fa-calendar"></i> <?php echo _l('task_single_due_date'); ?>: <?php echo _d($task->duedate); ?></h5>
       </div>
       <?php if($task->finished == 1){ ?>
       <div class="label mleft10 pull-left task-info label-success">
         <h5 class="no-margin"><i class="fa pull-left fa-check"></i> <?php echo _l('task_single_finished'); ?>: <?php echo _d($task->datefinished); ?></h5>
       </div>
       <?php } ?>
     </div>
   </div>
 </div>
</div>
<div class="panel_s">
  <div class="panel-body">
   <div class="row mbot15">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-9">
          <div class="title-wrapper pull-left">
           <?php if($task->finished == 0){ ?>
           <a href="#" class="pull-left" onclick="mark_complete(<?php echo $task->id; ?>); return false;" data-toggle="tooltip" title="<?php echo _l('task_single_mark_as_complete'); ?>"><i class="fa fa-check task-icon task-unfinished-icon"></i></a>
           <?php } else if($task->finished == 1){ ?>
           <a href="#" class="pull-left" onclick="unmark_complete(<?php echo $task->id; ?>); return false;" data-toggle="tooltip" title="<?php echo _l('task_unmark_as_complete'); ?>"><i class="fa fa-check task-icon task-finished-icon"></i></a>
           <?php } ?>
           <h3 class="no-margin pull-left"> <?php echo $task->name; ?></h3>
           <?php if($task->is_public == 0){ ?>
           <div class="clearfix"></div>
           <p class="text-muted no-margin"><small><span data-toggle="tooltip" data-title="<?php echo _l('task_is_private_help'); ?>"><?php echo _l('task_is_private'); ?></span> <?php if(has_permission('manageTasks')) { ?> - <a href="#" onclick="make_task_public(<?php echo $task->id; ?>); return false;"><?php echo _l('task_view_make_public'); ?></a> <?php } ?></small></p>
           <?php } ?>
         </div>
         <div class="pull-right">
          <?php echo form_open_multipart('admin/tasks/upload_file',array('id'=>'task-attachment','class'=>'inline-block')); ?>

          <?php echo form_close(); ?>
        </div>
        <div class="clearfix"></div>
        <?php
        if(!empty($task->rel_id)){
          echo '<div class="task-single-related-wrapper">';
          echo '<hr />';
          $task_rel_data = get_relation_data($task->rel_type,$task->rel_id);
          $task_rel_value = get_relation_values($task_rel_data,$task->rel_type);
          echo '<h4 class="bold text-info">'._l('task_single_related').': <a href="'.$task_rel_value['link'].'">'.$task_rel_value['name'].'</a></h4>';
          echo '</div>';
        }
        ?>
        <hr />

        <div class="row mbot30">
          <div class="col-md-3 mtop5">
            <i class="fa fa-users"></i> <span class="bold"><?php echo _l('task_single_assignees'); ?></span>
          </div>
          <div class="col-md-9" id="assignees">
            <?php
            $_assignees = '';
            foreach ($assignees as $assignee) {
              $_remove_assigne = '';
              if ($task->addedfrom == get_staff_user_id() || is_admin()) {
                $_remove_assigne = ' <a href="#" class="remove-task-user" onclick="remove_assignee(' . $assignee['id'] . ',' . $task->id . '); return false;"><i class="fa fa-remove"></i></a>';
              }
              $_assignees .= '
              <span class="task-user" data-toggle="tooltip" data-title="'.get_staff_full_name($assignee['assigneeid']).'">
                <a href="' . admin_url('profile/' . $assignee['assigneeid']) . '">' . staff_profile_image($assignee['assigneeid'], array(
                  'staff-profile-image-small'
                  )) .'</a> ' . $_remove_assigne . '</span>
              </span>';
            }

            if ($_assignees == '') {
              $_assignees = '<div class="bold mtop5 task-connectors-no-indicator display-block">'._l('task_no_assignees').'</div>';
            }
            echo $_assignees;
            ?>
          </div>
        </div>
        <div class="row mbot30">
          <div class="col-md-3 mtop5">
            <i class="fa fa-transgender"></i> <span class="bold"><?php echo _l('task_single_followers'); ?></span>
          </div>
          <div class="col-md-9" id="followers">
            <?php
            $_followers        = '';
            foreach ($followers as $follower) {
              $_remove_follower = '';
              if ($task->addedfrom == get_staff_user_id() || is_admin()) {
                $_remove_follower = ' <a href="#" class="remove-task-user" onclick="remove_follower(' . $follower['id'] . ',' . $task->id . '); return false;"><i class="fa fa-remove"></i></a>';
              }
              $_followers .= '
              <span class="task-user" data-toggle="tooltip" data-title="'.get_staff_full_name($follower['followerid']).'">
                <a href="' . admin_url('profile/' . $follower['followerid']) . '">' . staff_profile_image($follower['followerid'], array(
                  'staff-profile-image-small'
                  )) . '</a> ' . $_remove_follower . '</span>
              </span>';
            }
            if ($_followers == '') {
              $_followers = '<div class="bold mtop5 task-connectors-no-indicator display-block">'._l('task_no_followers').'</div>';
            }
            echo $_followers;
            ?>
          </div>
        </div>

      </div>
      <div class="col-md-3">
       <?php if(has_permission('manageTasks')){ ?>
       <div class="text-left">
        <select data-width="100%" class="text-muted task-action-select selectpicker" name="select-assignees" data-live-search="true" title='<?php echo _l('task_single_assignees_select_title'); ?>'></select>
      </div>
      <?php } ?>
      <?php if(has_permission('manageTasks')){ ?>
      <div class="text-left">
        <select data-width="100%" class="text-muted selectpicker task-action-select mtop10" name="select-followers" data-live-search="true" title='<?php echo _l('task_single_followers_select_title'); ?>'></select>
      </div>
      <?php } ?>
      <a href="#" onclick="add_task_checklist_item(); return false"><span class="label mtop10 label-default label-task-action new-checklist-item"><i class="fa fa-plus-circle"></i>
        <?php echo _l('add_checklist_item'); ?></span></a>
        <a href="#" class="add-task-attachments"><span class="label mtop10 label-default label-task-action"><i class="fa fa-paperclip"></i> <?php echo _l('add_task_attachments'); ?></span></a>
        <?php if(has_permission('manageTasks')) { ?>
        <a href="#" class="edit_task" onclick="edit_task(<?php echo $id; ?>); return false;">
          <span class="label label-default label-task-action mtop10"><i class="fa fa-pencil-square"></i> <?php echo _l('task_single_edit'); ?></span></a>
          <a href="#" onclick="delete_task(); return false;">
            <span class="label label-default mtop10 label-task-action task-delete"><i class="fa fa-remove"></i> <?php echo _l('task_single_delete'); ?></span></a>
            <?php } ?>
          </div>
        </div>
      </div>

    </div>
    <div class="clearfix"></div>
    <?php
    $custom_fields = get_custom_fields('tasks');
    foreach($custom_fields as $field){ ?>
    <?php $value = get_custom_field_value($task->id,$field['id'],'tasks');
    if($value == ''){continue;} ?>
    <div class="row mbot10">
      <div class="col-md-9 mtop5">
        <span class="bold"><?php echo ucfirst($field['name']); ?></span>
        <br />
        <div class="text-left">
          <?php echo $value; ?>
        </div>
      </div>
    </div>
    <?php } ?>
    <div class="row">
      <div class="col-md-12 mtop20" id="attachments">

      </div>
    </div>
    <div class="row">
      <div class="col-md-12 mbot20">
        <div id="checklist-items" class="mtop15">

        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 mbot20" id="description">
        --<br />
        <h4 class="bold"><?php echo _l('task_view_description'); ?></h4>
        <?php echo check_for_links($task->description); ?>
      </div>
    </div>
    <div class="row tasks-comments">
      <div class="col-md-12">
       <textarea name="comment" id="task_comment" rows="5" class="form-control mtop15"></textarea>
       <button type="button" class="btn btn-primary mtop10 pull-right" onclick="add_task_comment();"><?php echo _l('task_single_add_new_comment'); ?></button>
       <div class="clearfix"></div>
     </div>
     <div id="task-comments">
      <?php echo $comments; ?>
    </div>
  </div>
</div>
</div>
</div>
<script>
  taskid = '<?php echo $task->id; ?>'
  init_selectpicker();
  Dropzone.autoDiscover = false;
  var tasksAttachmentsDropzone = new Dropzone("#task-attachment", {
    clickable: '.add-task-attachments',
    autoProcessQueue: true,
    createImageThumbnails: false,
    addRemoveLinks: false,
    previewTemplate: '<div style="display:none"></div>',
    maxFiles: 10,
  });

  tasksAttachmentsDropzone.on("sending", function(file, xhr, formData) {
    formData.append("taskid", taskid);
  });
  // On post added success
  tasksAttachmentsDropzone.on('complete', function(files, response) {
    if (tasksAttachmentsDropzone.getUploadingFiles().length === 0 && tasksAttachmentsDropzone.getQueuedFiles().length === 0) {
      reload_task_attachments();
    }
  });
</script>
