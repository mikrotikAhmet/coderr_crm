<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
           <?php
           $client_db_fields = $this->db->list_fields('tblclients');
           if(!isset($simulate) > 0) { ?>
           <p>
            Your CSV data should be in the format below. The first line of your CSV file should be the column headers as in the table example. Also make sure that your file is UTF-8 to avoid unnecessary encoding problems.
          </p>
          <p class="text-danger">Duplicate email rows wont be imported</p>
          <div class="table-responsive no-dt">
           <table class="table table-hover table-bordered">
             <thead>
               <tr>
                <?php

                $total_fields = 0; ?>
                <?php foreach($client_db_fields as $field){
                  if(in_array($field,$not_importable)){continue;}
                  ?>
                  <?php $total_fields++; ?>
                  <th class="bold"><?php if($field == 'firstname' || $field == 'lastname' || $field == 'email'){echo '<span class="text-danger">*</span>';} ?> <?php echo str_replace('_',' ',ucfirst($field)); ?></th>
                  <?php } ?>
                  <?php $custom_fields = get_custom_fields('customers');
                  foreach($custom_fields as $field){ ?>
                  <?php $total_fields++; ?>
                  <th class="bold"><?php echo $field['name']; ?></th>
                  <?php } ?>
                </tr>
              </thead>
              <tbody>
                <?php for($i = 0; $i<1;$i++){
                  echo '<tr>';
                  for($x = 0; $x<$total_fields;$x++){
                    echo '<td>Sample Data</td>';
                  }
                  echo '</tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
          <?php } ?>
          <?php if(isset($simulate) > 0) { ?>
          <h4 class="bold">Simulation Data <small class="text-info">Max 100 rows are shown</small></h4>
          <p class="text-info">If you are satisfied with the results upload the file again and click import</p>
          <div class="table-responsive no-dt">
           <table class="table table-hover table-bordered">
            <thead>
             <tr>
              <?php foreach($client_db_fields as $field){
                if(in_array($field,$not_importable)){continue;}
                ?>
                <th class="bold"><?php echo str_replace('_',' ',ucfirst($field)); ?></th>
                <?php } ?>
                <?php $custom_fields = get_custom_fields('customers');
                foreach($custom_fields as $field){ ?>
                <th class="bold"><?php echo $field['name']; ?></th>
                <?php } ?>
              </tr>
            </thead>
            <tbody>
              <?php
              $simulate_fields = array();
              foreach($client_db_fields as $field){
                if(in_array($field,$not_importable)){continue;}
                array_push($simulate_fields,$field);
              }
              $custom_fields = get_custom_fields('customers');
              foreach($custom_fields as $field){
                array_push($simulate_fields,$field['name']);
              }
              $total_simulate = count($simulate);
              for($i = 0; $i < $total_simulate;$i++){
                echo '<tr>';
                for($x = 0;$x < count($simulate_fields);$x++){
                  if(!isset($simulate[$i][$simulate_fields[$x]])){echo '<td>/</td>';}else{
                    echo '<td>'.$simulate[$i][$simulate_fields[$x]].'</td>';
                  }
                }
                echo '</tr>';
              }
              ?>
            </tbody>
          </table>
        </div>
        <?php } ?>
        <div class="row">
         <div class="col-md-4 mtop15">
          <?php echo form_open_multipart($this->uri->uri_string()) ;?>
          <?php echo form_hidden('clients_import','true'); ?>
          <?php echo render_input('file_csv','choose_csv_file','','file'); ?>
          <?php echo render_select('groups_in[]',$groups,array('id','name'),'customer_groups',array(),array('multiple'=>true)); ?>
          <?php echo render_input('default_pass_all','default_pass_clients_import'); ?>
          <div class="form-group">
            <button type="button" class="btn btn-primary import btn-import-submit"><?php echo _l('import'); ?></button>
            <button type="button" class="btn btn-primary simulate btn-import-submit"><?php echo _l('simulate_import'); ?></button>
          </div>
          <?php echo form_close(); ?>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>
<?php init_tail(); ?>
<script>
 _validate_form($('form'),{file_csv:{required:true,extension: "csv"},source:'required',status:'required'});
 $(document).ready(function(){
  $('.btn-import-submit').on('click',function(){
    if($(this).hasClass('simulate')){
      $('form').append(hidden_input('simulate',true));
    }
    $('form').submit();
  });
})
</script>
</body>
</html>
