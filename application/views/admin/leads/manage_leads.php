<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <?php echo form_hidden('custom_view'); ?>
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="<?php echo admin_url('leads/lead'); ?>" class="btn new-lead mright5 btn-info pull-left display-block">
                            <?php echo _l('new_lead'); ?>
                        </a>
                        <?php if(is_admin()){ ?>
                        <a href="<?php echo admin_url('leads/import'); ?>" class="btn btn-info pull-left display-block">
                            <?php echo _l('import_leads'); ?>
                        </a>
                        <?php } ?>
                        <div class="row">
                          <div class="col-md-5">
                           <a href="#" class="btn btn-default" onclick="slideToggle('.leads-overview'); return false;"><i class="fa fa-bar-chart"></i></a>
                           <a href="#" class="btn btn-default mleft5 toggle-custom-kan-ban-tab"><i class="fa fa-th-list"></i></a>
                       </div>
                       <div class="col-md-4 col-xs-12 pull-right leads-search">
                         <?php echo render_input('search[]','','','search',array('data-name'=>'search','onkeyup'=>'leads_canban(this.value);','placeholder'=>_l('leads_search')),array(),'no-margin') ?>
                     </div>
                 </div>
                 <div class="clearfix"></div>
                 <div class="row hide leads-overview">
                     <hr />
                     <div class="col-md-12">
                        <h3 class="text-success no-margin"><?php echo _l('leads_summary'); ?></h3>
                    </div>
                    <?php
                    $numStatuses = count($statuses);
                    foreach($statuses as $status){ ?>
                    <div class="col-md-2 border-right">
                        <h3 class="bold"><?php echo total_rows('tblleads',array('status'=>$status['id'])); ?></h3>
                        <span class="<?php if($status['isdefault'] == 1){echo 'text-success';} else {echo 'text-info';} ?> bold"><?php echo $status['name']; ?></span>
                    </div>
                    <?php } ?>
                    <?php $total_leads  = total_rows('tblleads'); ?>
                    <div class="col-md-2">
                        <?php
                        $total_lost   = total_rows('tblleads',array('lost'=>1));
                        $percent_lost = ($total_leads > 0 ? number_format(($total_lost * 100) / $total_leads,2) : 0);
                        ?>
                        <h3 class="bold"><?php echo $percent_lost; ?>%</h3>
                        <span class="text-danger bold"><?php echo _l('lost_leads'); ?></span>
                    </div>
                    <div class="col-md-2">
                        <?php
                        $total_junk   = total_rows('tblleads',array('junk'=>1));
                        $percent_junk = ($total_leads > 0 ? number_format(($total_junk * 100) / $total_leads,2) : 0);
                        ?>
                        <h3 class="bold"><?php echo $percent_junk; ?>%</h3>
                        <span class="text-danger bold"><?php echo _l('junk_leads'); ?></span>
                    </div>

                </div>
            </div>
        </div>
        <div class="panel_s mtop5">
            <div class="panel-body animated fadeIn">
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                        <div class="row">
                            <div class="container-fluid leads-kan-ban">
                                <div id="kan-ban"></div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="list_tab">
                        <div class="row" id="leads-table">
                         <div class="col-md-12">
                            <div class="btn-group pull-left" data-toggle="tooltip" title="View Leads">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-list"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left">
                                    <li>
                                        <a href="#" onclick="dt_custom_view('','.table-leads'); return false;">
                                            <?php echo _l('leads_all'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="dt_custom_view('lost','.table-leads'); return false;">
                                            <?php echo _l('lead_lost'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="dt_custom_view('junk','.table-leads'); return false;">
                                            <?php echo _l('lead_junk'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="dt_custom_view('not_assigned','.table-leads'); return false;">
                                            <?php echo _l('leads_not_assigned'); ?>
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <?php foreach($statuses as $status){ ?>
                                    <li>
                                        <a href="#" onclick="dt_custom_view('<?php echo $status['id']; ?>','.table-leads'); return false;"><?php echo $status['name']; ?>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div class="row">
                             <div class="col-md-3 col-xs-9">
                                <?php if(has_permission('manageStaff')){
                                   echo render_select('view_assigned',$staff,array('staffid',array('firstname','lastname')),'','',array('data-width'=>'100%'));
                               } ?>
                           </div>
                       </div>
                   </div>
                   <div class="col-md-12 mtop20">


                    <?php
                    $table_data = array(
                        _l('leads_dt_name'),
                        _l('leads_dt_email'),
                        _l('leads_dt_phonenumber'),
                        _l('leads_dt_assigned'),
                        _l('leads_dt_status'),
                        _l('leads_dt_last_contact'));
                    $custom_fields = get_custom_fields('leads',array('show_on_table'=>1));
                    foreach($custom_fields as $field){
                        array_push($table_data,$field['name']);
                    }
                    array_push($table_data,_l('options'));
                    render_datatable($table_data,'leads'); ?>
                </div>
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
<div class="modal fade kan-ban-lead-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

        <div class="modal-body">
        </div>
    </div>
</div>
</div>

<div class="modal fade modal-chart" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

        <div class="modal-body">
           <canvas class="chart" id="leads-report-by-statuses"></canvas>
       </div>
   </div>
</div>
</div>

<script src="<?php echo site_url('assets/js/leads.js'); ?>"></script>
</body>
</html>
