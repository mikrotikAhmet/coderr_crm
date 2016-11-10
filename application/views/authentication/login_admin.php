<?php $this->load->view('authentication/includes/head.php'); ?>
<body class="login_admin">
 <div class="container">
  <div class="row">
   <div class="col-md-4 col-md-offset-4 authentication-form-wrapper animated fadeIn">
    <div class="company-logo">
     <?php echo get_company_logo(); ?>
   </div>
   <div class="mtop40 authentication-form">
    <h1><?php echo _l('admin_auth_login_heading'); ?></h1>
    <?php echo form_open($this->uri->uri_string()); ?>
    <?php echo validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>
    <?php if($this->session->flashdata('message-danger')){ ?>
    <div class="alert alert-danger">
      <?php echo $this->session->flashdata('message-danger'); ?>
    </div>
    <?php } ?>
    <?php if($this->session->flashdata('message-success')){ ?>
    <div class="alert alert-success">
      <?php echo $this->session->flashdata('message-success'); ?>
    </div>
    <?php } ?>
    <?php echo render_input('email','admin_auth_login_email',set_value('email'),'email'); ?>
    <?php echo render_input('password','admin_auth_login_password','','password'); ?>
    <div class="form-group">
      <input type="checkbox" name="remember"> <?php echo _l('admin_auth_login_remember_me'); ?>
    </div>
    <div class="form-group">
      <button type="submit" class="btn btn-primary btn-block"><?php echo _l('admin_auth_login_button'); ?></button>
    </div>
    <div class="form-group">
      <a href="<?php echo site_url('authentication/forgot_password'); ?>"><?php echo _l('admin_auth_login_fp'); ?></a>
    </div>
    <?php echo form_close(); ?>
  </div>
</div>
</div>
</div>
</body>
</html>
