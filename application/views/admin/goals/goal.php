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

            <?php $value = (isset($goal) ? $goal->subject : ''); ?>
            <?php echo render_input('subject','goal_subject',$value); ?>
            <div class="form-group">
              <label for="goal_type" class="control-label"><?php echo _l('goal_type'); ?></label>
              <select name="goal_type" class="selectpicker" data-width="100%">
                <option value=""></option>
                <?php foreach(get_goal_types() as $type){ ?>
                <option value="<?php echo $type['key']; ?>" data-subtext="<?php if(isset($type['subtext'])){echo _l($type['subtext']);} ?>" <?php if(isset($goal) && $goal->goal_type == $type['key']){echo 'selected';} ?>><?php echo _l($type['lang_key']); ?></option>
                <?php } ?>
              </select>
            </div>
            <?php $value = (isset($goal) ? $goal->achievement : ''); ?>
            <?php echo render_input('achievement','goal_achievement',$value,'number'); ?>

            <?php $value = (isset($goal) ? $goal->start_date : _d(date('Y-m-d'))); ?>
            <?php echo render_date_input('start_date','goal_start_date',$value); ?>

            <?php $value = (isset($goal) ? $goal->end_date : ''); ?>
            <?php echo render_date_input('end_date','goal_end_date',$value); ?>

            <div class="hide" id="contract_types">
              <?php $selected = (isset($goal) ? $goal->contract_type : ''); ?>
              <?php echo render_select('contract_type',$contract_types,array('id','name'),'goal_contract_type',$selected); ?>
            </div>
            <?php $value = (isset($goal) ? $goal->description : ''); ?>
            <?php echo render_textarea('description','goal_description',$value); ?>
            <div class="checkbox checkbox-primary">
              <input type="checkbox" name="notify_when_achieve" <?php if(isset($goal)){if($goal->notify_when_achieve == 1){echo 'checked';} } else {echo 'checked';} ?>>
              <label><?php echo _l('goal_notify_when_achieve'); ?></label>
            </div>
            <div class="checkbox checkbox-primary">
              <input type="checkbox" name="notify_when_fail" <?php if(isset($goal)){if($goal->notify_when_fail == 1){echo 'checked';} } else {echo 'checked';} ?>>
              <label><?php echo _l('goal_notify_when_fail'); ?></label>
            </div>
            <button type="submit" class="btn btn-primary pull-right"><?php echo _l('submit'); ?></button>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>
      <?php if(isset($goal)){ ?>
      <div class="col-md-7">
        <div class="panel_s">
          <div class="panel-heading">
            <?php echo _l('goal_achievement'); ?>
          </div>
          <div class="panel-body">
            <?php
            $show_acchievement_ribbon = false;
            $help_text = '';
            if($goal->end_date < date('Y-m-d')){
              $achieve_indicator_class = 'danger';
              $lang_key = 'goal_failed';
              $finished = true;
              $notify_type = 'failed';

              if($goal->notified == 1){
                $help_text = '<p class="text-muted text-center">'._l('goal_staff_members_notified_about_failure').'</p>';
              }

              $show_acchievement_ribbon = true;
            } else if($achievement['percent'] == 100){

              $achieve_indicator_class = 'success';
              $show_acchievement_ribbon = true;
              if($goal->notified == 1){
                $help_text = '<p class="text-muted text-center">'._l('goal_staff_members_notified_about_achievement').'</p>';
              }

              $notify_type = 'success';
              $finished = true;
              $lang_key = 'goal_achieved';

            } else if($achievement['percent'] >= 80) {
              $achieve_indicator_class = 'warning';
              $show_acchievement_ribbon = true;
              $lang_key = 'goal_close';
            }
            if($show_acchievement_ribbon == true){
              echo '<div class="ribbon '.$achieve_indicator_class.'"><span>'._l($lang_key).'</span></div>';
            }

            ?>
            <h3 class="text-center"><?php echo _l('goal_result_heading'); ?></h3>
            <?php if($goal->goal_type == 1){
              echo '<p class="text-muted text-center">' . _l('goal_income_shown_in_base_currency') . '</p>';
            }
            if((isset($finished) && $goal->notified == 0) && ($goal->notify_when_achieve == 1 || $goal->notify_when_fail == 1)){
              echo '<p class="text-center text-info">'._l('goal_notify_when_end_date_arrives').'</p>';

              echo '<div class="text-center"><a href="'.admin_url('goals/notify/'.$goal->id . '/'.$notify_type).'" class="btn btn-default">'._l('goal_notify_staff_manualy').'</a></div>';
            }
            echo $help_text;
            ?>
            <div class="achievement mtop30" data-toggle="tooltip" title="<?php echo _l('goal_total',$achievement['total']); ?>">
              <div class="goal-progress" data-thickness="40" data-reverse="true">
               <strong class="goal-percent"></strong>
             </div>
           </div>
         </div>
       </div>
     </div>
     <?php } ?>
   </div>
 </div>
</div>
<?php init_tail(); ?>
<script>
  _validate_form($('form'), {
      subject: 'required',
      goal_type: 'required',
      end_date: 'required',
      start_date: 'required'
  });
  $(document).ready(function() {
      <?php if(isset($goal)){ ?>
      var circle = $('.goal-progress').circleProgress({
          value: '<?php echo $achievement['progress_bar_percent']; ?>',
          size: 250,
          fill: {
              gradient: ["#28b8da", "#059DC1"]
          }
      }).on('circle-animation-progress', function(event, progress, stepValue) {
          $(this).find('strong.goal-percent').html(parseInt(100 * stepValue) + '<i>%</i>');
      });
      <?php } ?>
      var goal_type = $('select[name="goal_type"]').val();
      if (goal_type == 5 || goal_type == 7) {
          $('#contract_types').removeClass('hide');
      }
      $('select[name="goal_type"]').on('change', function() {
          var goal_type = $(this).val();
          if (goal_type == 5 || goal_type == 7) {
              $('#contract_types').removeClass('hide');
          } else {
              $('#contract_types').addClass('hide');
              $('#contract_type').selectpicker('val', '');
          }
      });
  });
</script>
</body>
</html>
