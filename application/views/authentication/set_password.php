<?php $this->load->view('authentication/includes/head.php'); ?>
<body class="authentication">
 <div class="container">
  <div class="row">
   <div class="col-md-4 col-md-offset-4 authentication-form-wrapper animated fadeIn">
    <div class="company-logo">
     <?php echo get_company_logo(); ?>
   </div>
   <div class="mtop40 authentication-form">
    <h1><?php echo _l('admin_auth_set_password_heading'); ?></h1>
    <?php echo form_open($this->uri->uri_string()); ?>
    <?php echo validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>
    <?php if($this->session->flashdata('message-danger')){ ?>
    <div class="alert alert-danger">
      <?php echo $this->session->flashdata('message-danger'); ?>
    </div>
    <?php } ?>
    <?php echo render_input('password','admin_auth_set_password','','password'); ?>
    <?php echo render_input('passwordr','admin_auth_set_password_repeat','','password'); ?>
    <div class="form-group">
      <button type="submit" class="btn btn-primary btn-block">Submit</button>
    </div>
    <?php echo form_close(); ?>
  </div>
</div>
</div>
</div>
</body>
</html>
