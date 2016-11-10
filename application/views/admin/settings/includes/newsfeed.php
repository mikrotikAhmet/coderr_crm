<div class="row">
    <div class="col-md-6">
      <?php echo render_input('settings[newsfeed_upload_file_extensions]','settings_newsfeed_allowed_file_extensions',get_option('newsfeed_upload_file_extensions')); ?>
      <hr />
      <?php echo render_input('settings[newsfeed_maximum_files_upload]','settings_newsfeed_max_file_upload_post',get_option('newsfeed_maximum_files_upload'),'number'); ?>
      <hr />
      <?php echo render_input('settings[newsfeed_maximum_file_size]','settings_newsfeed_max_file_size',get_option('newsfeed_maximum_file_size'),'number'); ?>
  </div>
</div>
