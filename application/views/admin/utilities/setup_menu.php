<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <a href="#" onclick="save_menu();return false;" class="btn btn-primary"><?php echo _l('utilities_menu_save'); ?></a>
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
           <?php
                          //add_setup_menu_item(array('url'=>admin_url() . 'utilities/setup_menu','permission'=>'is_admin','name'=>'Setup Menu'),'menu-builder');
           $menu_active = get_option('setup_menu_active');
           $menu_active = json_decode($menu_active);
           $menu_inactive = get_option('setup_menu_inactive');
           $menu_inactive = json_decode($menu_inactive);
           ?>
           <div class="clearfix"></div>
           <div class="row">
            <div class="col-md-6 border-right">
              <h4 class="bold"><?php echo _l('active_menu_items'); ?></h4>
              <div class="dd active">
                <?php
                $i = 1;
                echo '<ol class="dd-list">';
                if(count($menu_active->setup_menu_active) == 0){ ?>
                <li class="dd-item dd3-empty"></li>
                <?php }
                foreach($menu_active->setup_menu_active as $item){
                  ?>
                  <li class="dd-item dd3-item main" data-id="<?php echo $item->id; ?>">
                   <div class="dd-handle dd3-handle"></div>
                   <?php $name = _l($item->name);
                   if(strpos($name,'translate_not_found') !== false){
                    $name = $item->name;
                  }
                  ?>
                  <div class="dd3-content"><?php echo $name ?>
                    <a href="#" class="text-muted toggle-menu-options main-item-options pull-right"><i class="fa fa-cog"></i></a>
                  </div>
                  <div class="menu-options main-item-options" style="display:none;" data-menu-options="<?php echo $item->id; ?>">
                   <label class="control-label"><?php echo _l('utilities_menu_name'); ?></label>
                   <div class="input-group">

                     <input type="text" value="<?php echo $item->name; ?>" class="form-control input-sm main-item-name" name="name-menu-item-<?php echo $item->id; ?>">
                     <span class="input-group-addon"><i class="fa fa-question" data-toggle="tooltip" data-placement="left" data-title="<?php echo _l('utilities_menu_translate_name_help'); ?>"></i></span>
                   </div>
                   <label class="control-label"><?php echo _l('utilities_menu_url'); ?></label>
                   <div class="input-group">

                     <?php
                     $url = '#';

                     if(isset($item->url) && !empty($item->url)){
                      $url = $item->url;
                    }

                    ?>
                    <input type="text" value="<?php echo $url; ?>" class="form-control input-sm main-item-url" name="url-menu-item-<?php echo $item->id; ?>">
                    <span class="input-group-addon"><i class="fa fa-question" data-toggle="tooltip" data-placement="left" data-title="<?php echo _l('utilities_menu_url_help',admin_url()); ?>"></i></span>
                  </div>
                  <div class="form-group">
                   <label class="control-label"><?php echo _l('utilities_menu_icon'); ?> <i class="<?php echo $item->icon; ?>"></i></label>
                   <input type="text" value="<?php echo $item->icon; ?>" class="form-control input-sm main-item-icon">
                 </div>
                 <?php
                 $selected = '';
                 if(isset($item->permission)){
                  $selected = $item->permission;
                }
                echo render_select('permission',$permissions,array('shortname','name'),_l('utilities_menu_permission'),$selected); ?>
              </div>
              <?php if(isset($item->children)){ ?>
              <ol class="dd-list">
                <?php $x = 1; foreach($item->children as $submenu){ ?>
                <li class="dd-item dd3-item sub-items" data-id="<?php echo $submenu->id; ?>">
                  <div class="dd-handle dd3-handle"></div>
                  <?php $name = _l($submenu->name);
                  if(strpos($name,'translate_not_found') !== false){
                    $name = $submenu->name;
                  }
                  ?>
                  <div class="dd3-content"><?php echo $name; ?>
                   <a href="#" class="text-muted toggle-menu-options sub-item-options pull-right"><i class="fa fa-cog"></i></a>
                 </div>
                 <div class="menu-options sub-item-options" style="display:none;" data-menu-options="<?php echo $submenu->id; ?>">
                   <label class="control-label"><?php echo _l('utilities_menu_name'); ?></label>
                   <div class="input-group">
                     <input type="text" value="<?php echo $submenu->name; ?>" class="form-control input-sm sub-item-name" name="name-menu-item-<?php echo $submenu->id; ?>">
                     <span class="input-group-addon"><i class="fa fa-question" data-toggle="tooltip" data-placement="left" data-title="<?php echo _l('utilities_menu_translate_name_help'); ?>"></i></span>
                   </div>
                   <label class="control-label"><?php echo _l('utilities_menu_url'); ?></label>

                   <div class="input-group">
                     <?php
                     $url = '#';
                     if(isset($submenu->url) && !empty($submenu->url)){
                      $url = $submenu->url;
                    }
                    ?>
                    <input type="text" value="<?php echo $url; ?>" class="form-control input-sm sub-item-url" name="url-menu-item-<?php echo $submenu->id; ?>">
                    <span class="input-group-addon"><i class="fa fa-question" data-toggle="tooltip" data-placement="left" data-title="<?php echo _l('utilities_menu_url_help',admin_url()); ?>"></i></span>
                  </div>
                  <div class="form-group">
                   <label class="control-label"><?php echo _l('utilities_menu_icon'); ?> <i class="<?php echo $submenu->icon; ?>"></i></label>
                   <input type="text" value="<?php echo $submenu->icon; ?>" class="form-control input-sm main-item-icon">
                 </div>

                 <?php
                 $selected = '';
                 if(isset($submenu->permission)){
                  $selected = $submenu->permission;
                }
                echo render_select('permission',$permissions,array('shortname','name'),_l('utilities_menu_permission'), $selected);
                ?>
              </div>
            </li>
            <?php $x++; } ?>
          </ol>
          <?php } ?>
        </li>
        <?php $i++; } ?>
      </ol>
    </div>
  </div>
  <div class="col-md-6">
   <h4 class="bold"><?php echo _l('inactive_menu_items'); ?></h4>
   <div class="dd inactive">
    <?php
    $i = 1;
    echo '<ol class="dd-list">'; ?>
    <?php if(count($menu_inactive->setup_menu_inactive) == 0){ ?>
    <li class="dd-item dd3-empty"></li>
    <?php } ?>
    <?php
    foreach($menu_inactive->setup_menu_inactive as $item){
      ?>
      <li class="dd-item dd3-item main" data-id="<?php echo $item->id; ?>">
       <div class="dd-handle dd3-handle"></div>
       <?php $name = _l($item->name);
       if(strpos($name,'translate_not_found') !== false){
        $name = $item->name;
      }
      ?>
      <div class="dd3-content"><?php echo $name; ?>
        <a href="#" class="text-muted toggle-menu-options main-item-options pull-right"><i class="fa fa-cog"></i></a>
      </div>
      <div class="menu-options main-item-options" style="display:none;" data-menu-options="<?php echo $item->id; ?>">
       <label class="control-label"><?php echo _l('utilities_menu_name'); ?></label>
       <div class="input-group">
         <input type="text" value="<?php echo $item->name; ?>" class="form-control input-sm main-item-name" name="name-menu-item-<?php echo $item->id; ?>">
         <span class="input-group-addon"><i class="fa fa-question" data-toggle="tooltip" data-placement="left" data-title="<?php echo _l('utilities_menu_translate_name_help'); ?>"></i></span>
       </div>
       <label class="control-label"><?php echo _l('utilities_menu_url'); ?></label>

       <div class="input-group">
         <?php
         $url = '#';
         if(isset($item->url) && !empty($item->url)){
          $url = $item->url;
        }

        ?>
        <input type="text" value="<?php echo $url; ?>" class="form-control input-sm main-item-url" name="url-menu-item-<?php echo $item->id; ?>">
        <span class="input-group-addon"><i class="fa fa-question" data-toggle="tooltip" data-placement="left" data-title="<?php echo _l('utilities_menu_url_help',admin_url()); ?>"></i></span>
      </div>
      <div class="form-group">
       <label class="control-label"><?php echo _l('utilities_menu_icon'); ?> <i class="<?php echo $item->icon; ?>"></i></label>
       <input type="text" value="<?php echo $item->icon; ?>" class="form-control input-sm main-item-icon">
     </div>
     <?php
     $selected = '';
     if(isset($item->permission)){
      $selected = $item->permission;
    }
    echo render_select('permission',$permissions,array('shortname','name'),_l('utilities_menu_permission'),$selected); ?>
  </div>
  <?php if(isset($item->children)){ ?>
  <ol class="dd-list">
    <?php $x = 1; foreach($item->children as $submenu){ ?>
    <li class="dd-item dd3-item sub-items" data-id="<?php echo $submenu->id; ?>">
      <div class="dd-handle dd3-handle"></div>
      <?php $name = _l($submenu->name);
      if(strpos($name,'translate_not_found') !== false){
        $name = $submenu->name;
      }
      ?>
      <div class="dd3-content"><?php echo $name; ?>
       <a href="#" class="text-muted toggle-menu-options sub-item-options pull-right"><i class="fa fa-cog"></i></a>
     </div>
     <div class="menu-options sub-item-options" style="display:none;" data-menu-options="<?php echo $submenu->id; ?>">
      <label class="control-label"><?php echo _l('utilities_menu_name'); ?></label>
      <div class="input-group">
       <input type="text" value="<?php echo $submenu->name; ?>" class="form-control input-sm sub-item-name" name="name-menu-item-<?php echo $submenu->id; ?>">
       <span class="input-group-addon"><i class="fa fa-question" data-toggle="tooltip" data-placement="left" data-title="<?php echo _l('utilities_menu_translate_name_help'); ?>"></i></span>
     </div>
     <label class="control-label"><?php echo _l('utilities_menu_url'); ?></label>
     <div class="input-group">

       <?php
       $url = '#';
       if(isset($submenu->url) && !empty($submenu->url)){
        $url = $submenu->url;
      }
      ?>
      <input type="text" value="<?php echo $url; ?>" class="form-control input-sm sub-item-url" name="url-menu-item-<?php echo $submenu->id; ?>">
      <span class="input-group-addon"><i class="fa fa-question" data-toggle="tooltip" data-placement="left" data-title="<?php echo _l('utilities_menu_url_help',admin_url()); ?>"></i></span>

    </div>
    <div class="form-group">
     <label class="control-label"><?php echo _l('utilities_menu_icon'); ?> <i class="<?php echo $submenu->icon; ?>"></i></label>
     <input type="text" value="<?php echo $submenu->icon; ?>" class="form-control input-sm main-item-icon">
   </div>
   <?php
   $selected = '';
   if(isset($submenu->permission)){
    $selected = $submenu->permission;
  }
  echo render_select('permission',$permissions,array('shortname','name'),_l('utilities_menu_permission'), $selected);
  ?>
</div>
</li>
<?php $x++; } ?>
</ol>
<?php } ?>
</li>
<?php $i++; } ?>
</ol>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<?php init_tail(); ?>
<script src="<?php echo site_url(); ?>assets/plugins/jQueryNestable/jquery.nestable.js"></script>
<script>
  $(document).ready(function() {
      $('.dd').nestable({
          maxDepth: 2
      });
      $('.toggle-menu-options').on('click', function(e) {
          e.preventDefault();
          menu_id = $(this).parents('li').data('id');
          if ($(this).hasClass('main-item-options')) {
              $(this).parents('li').find('.main-item-options[data-menu-options="' + menu_id + '"]').slideToggle();
          } else {
              $(this).parents('li').find('.sub-item-options[data-menu-options="' + menu_id + '"]').slideToggle();
          }
      })
  });

  function save_menu() {
      var items = $('.dd.active').find('li.main');
      $.each(items, function() {
          var main_menu = $(this);
          var name = $(this).find('.main-item-options input.main-item-name').val();
          var url = $(this).find('.main-item-options input.main-item-url').val();
          var permission = $(this).find('.main-item-options select[name="permission"]').selectpicker('val');
          var icon = $(this).find('.main-item-icon').val();
          main_menu.data('name', name);
          main_menu.data('url', url);
          main_menu.data('permission', permission);
          main_menu.data('icon', icon);

      });

      var sub_items = $('.dd.active li.sub-items');
      $.each(sub_items, function() {
          var sub_item = $(this);
          var name = $(this).find('.sub-item-options input.sub-item-name').val();
          var url = $(this).find('.sub-item-options input.sub-item-url').val();
          var permission = $(this).find('.sub-item-options select[name="permission"]').selectpicker('val');
          var icon = $(this).find('.main-item-icon').val();
          sub_item.data('name', name);
          sub_item.data('url', url);
          sub_item.data('permission', permission);
          sub_item.data('icon', icon);
      });

      var setup_menu_active = $('.dd.active').nestable('serialize');
      /* Inactive */
      var items_inactive = $('.dd.inactive').find('li.main');
      $.each(items_inactive, function() {
          var main_menu = $(this);
          var name = $(this).find('.main-item-options input.main-item-name').val();
          var url = $(this).find('.main-item-options input.main-item-url').val();
          var permission = $(this).find('.main-item-options select[name="permission"]').selectpicker('val');
          var icon = $(this).find('.main-item-icon').val();
          main_menu.data('name', name);
          main_menu.data('url', url);
          main_menu.data('permission', permission);
          main_menu.data('icon', icon);

      });

      var sub_items = $('.dd.inactive li.sub-items');
      $.each(sub_items, function() {
          var sub_item = $(this);
          var name = $(this).find('.sub-item-options input.sub-item-name').val();
          var url = $(this).find('.sub-item-options input.sub-item-url').val();
          var permission = $(this).find('.sub-item-options select[name="permission"]').selectpicker('val');
          var icon = $(this).find('.main-item-icon').val();
          sub_item.data('name', name);
          sub_item.data('url', url);
          sub_item.data('permission', permission);
          sub_item.data('icon', icon);
      });

      var setup_menu_inactive = $('.dd.inactive').nestable('serialize');
      var data = {};
      data.active = setup_menu_active;
      data.inactive = setup_menu_inactive;
      $.post(admin_url + 'utilities/update_setup_menu', data).success(function() {
          window.location.reload();
      })

  }
</script>
</body>
</html>
