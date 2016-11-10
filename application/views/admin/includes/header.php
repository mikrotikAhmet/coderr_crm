<div id="header">
  <div class="header-link hide-menu visible-xs visible-sm visible-md"><i class="fa fa-bars" data-toggle="tooltip" data-placement="right" title="<?php echo _l('nav_sidebar_toggle_tooltip'); ?>"></i></div>
  <div id="logo">
    <?php get_company_logo('admin') ?>
  </div>
  <nav>
    <div class="small-logo">
      <span class="text-primary">
       <?php get_company_logo('admin') ?>
     </span>
   </div>
   <div class="mobile-menu">
    <button type="button" class="navbar-toggle visible-sm visible-xs mobile-menu-toggle collapsed" data-toggle="collapse" data-target="#mobile-collapse" aria-expanded="false">
      <i class="fa fa-chevron-down"></i>
    </button>
    <a class="business-news-mobile" href="<?php echo admin_url('business_news'); ?>"><i class="fa fa-newspaper-o"></i></a>
    <div class="mobile-navbar collapse" id="mobile-collapse" aria-expanded="false" style="height: 0px;" role="navigation" >

      <ul class="nav navbar-nav">
       <?php if(count($_notifications) > 0){ ?>
       <li><a href="<?php echo admin_url('profile'); ?>"><?php echo _l('nav_notifications'); ?> (<?php echo total_rows('tblnotifications',array('touserid'=>get_staff_user_id(),'isread'=>0)); ?>)</a></li>
       <?php } ?>
       <li><a href="<?php echo admin_url('profile'); ?>"><?php echo _l('nav_my_profile'); ?></a></li>
       <li><a href="<?php echo admin_url('staff/edit_profile'); ?>"><?php echo _l('nav_edit_profile'); ?></a></li>
       <li><a href="<?php echo site_url(); ?>authentication/logout"><?php echo _l('nav_logout'); ?></a></li>
     </ul>
   </div>
 </div>
 <ul class="hidden-xs navbar header-left">
  <li id="top_date"></li>
</ul>
<div class="navbar-right">
  <ul class="nav navbar-nav">
   <li id="top_search" class="dropdown">
    <input type="search" id="search_input" class="form-control" placeholder="Search...">
    <div id="search_results">
    </div>
  </li>
  <li id="top_search_button">
    <button class="btn"><i class="fa fa-search"></i></button>
  </li>
  <li>
   <a href="#" class="dropdown-toggle profile" data-toggle="dropdown" aria-expanded="false">
    <?php echo staff_profile_image($_staff->staffid,array('img','img-responsive','staff-profile-image-small','pull-left')); ?>
    <?php echo $_staff->firstname . ' ' . $_staff->lastname; ?>
    <i class="fa fa-angle-down"></i>
  </a>
  <ul class="dropdown-menu animated fadeIn">
    <li><a href="<?php echo admin_url('profile'); ?>"><?php echo _l('nav_my_profile'); ?></a></li>
    <li><a href="<?php echo admin_url('staff/edit_profile'); ?>"><?php echo _l('nav_edit_profile'); ?></a></li>
    <li><a href="<?php echo site_url(); ?>authentication/logout"><?php echo _l('nav_logout'); ?></a></li>
  </ul>
</li>
<li>
  <a href="<?php echo admin_url('business_news'); ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo _l('business_news'); ?>"><i class="fa fa-newspaper-o"></i></a>
</li>
<li>
  <a href="<?php echo admin_url('todo'); ?>" data-toggle="tooltip" title="<?php echo _l('nav_todo_items'); ?>" data-placement="bottom"><i class="fa fa-list"></i>
    <?php $_unfinished_todos = total_rows('tbltodoitems',array('finished'=>0,'staffid'=>get_staff_user_id())); ?>
    <span class="label label-warning icon-total-indicator nav-total-todos<?php if($_unfinished_todos == 0){echo ' hide';} ?>"><?php echo $_unfinished_todos; ?></span>
  </a>
</li>

<li class="dropdown">
  <?php $unread_notifications = total_rows('tblnotifications',array('touserid'=>get_staff_user_id(),'isread'=>0)); ?>
  <a href="#" class="dropdown-toggle notifications-icon <?php if($unread_notifications > 0){echo 'animated swing';} ?>" data-toggle="dropdown" aria-expanded="false">
    <i class="fa fa-bell" data-toggle="tooltip" data-placement="bottom" title="<?php echo _l('nav_notifications_tooltip'); ?>"></i>
    <?php
    if($unread_notifications > 0){ ?>
    <span class="label label-warning icon-total-indicator icon-notifications"><?php echo $unread_notifications; ?></span>
    <?php } ?>
  </a>
  <ul class="dropdown-menu notifications animated fadeIn">
    <?php
    foreach($_notifications as $notification){ ?>
    <li>
      <?php if(!empty($notification['link'])){ ?>
      <a href="<?php echo admin_url($notification['link']); ?>">
        <?php } ?>
        <div class="notification-box<?php if($notification['isread'] == 0){echo ' unread';} ?>">
          <?php
          if($notification['fromcompany'] == NULL){
            echo staff_profile_image($notification['fromuserid'],array('staff-profile-image-small','img-circle','pull-left'));
          }
          ?>
          <div class="media-body">
            <?php
            $description = $notification['description'];
            if($notification['fromcompany'] == NULL){
              $description = get_staff_full_name($notification['fromuserid']). ' - ' . $description;
            }
            echo $description; ?><br />
            <small class="text-muted"><?php echo time_ago($notification['date']); ?></small>
          </div>
        </div>
        <?php if(!empty($notification['link'])){ ?>
      </a>
      <?php } ?>
    </li>
    <?php } ?>
    <li class="divider"></li>
    <li>
     <?php if(count($_notifications) > 0){ ?>
     <a href="<?php echo admin_url('profile'); ?>"><?php echo _l('nav_view_all_notifications'); ?></a>
     <?php } else { ?>
     <a href="" onclick="return false;"><?php echo _l('nav_no_notifications'); ?></a>
     <?php } ?>
   </li>
 </ul>
</li>
</ul>
</div>
</nav>
</div>
<div id="mobile-search" class="hide">
  <ul>

  </ul>
</div>
