              <?php
              $is_admin = is_admin();
              foreach ($statuses as $status) {
                $kanban_colors = '';
                foreach(get_system_favourite_colors() as $color){
                  $color_selected_class = 'cpicker-small';
                  if($color == $status['color']){
                    $color_selected_class = 'cpicker-big';
                  }
                  $kanban_colors .= "<div class='cpicker ".$color_selected_class."' data-color='".$color."' style='background:".$color.";border:1px solid ".$color."'></div>";
                }
                ?>
                <ul class="kan-ban-col" data-col-status-id="<?php echo $status['id']; ?>">
                  <li class="kan-ban-col-wrapper">
                    <div class="border-right panel_s">
                      <?php
                      $status_color = '';
                      if(!empty($status["color"])){
                        $status_color = 'style="background:'.$status["color"].';border:1px solid '.$status['color'].'"';
                      }
                      ?>
                      <div class="panel-heading-bg primary-bg" <?php echo $status_color; ?> data-status-id="<?php echo $status['id']; ?>">
                        <?php echo $status['name']; ?>
                        <a href="#" onclick="return false;" class="pull-right color-white" data-placement="bottom" data-toggle="popover" data-content="<div class='kan-ban-settings'><?php echo $kanban_colors; ?></div>" data-html="true" data-trigger="focus"><i class="fa fa-angle-down"></i>
                        </a>
                      </div>
                      <div class="kan-ban-content-wrapper">
                       <div class="kan-ban-content">

                         <ul class="status leads-status sortable" data-lead-status-id="<?php echo $status['id']; ?>">
                           <?php
                           $this->db->select();
                           $this->db->from('tblleads');
                           $this->db->where('status', $status['id']);
                           if (!$is_admin) {
                            $this->db->where('assigned', get_staff_user_id());
                            $this->db->or_where('addedfrom', get_staff_user_id());
                            $this->db->or_where('assigned', 0);
                            $this->db->or_where('is_public', 1);
                          }

                          if ($this->input->get('search')) {
                            $q = $this->input->get('search');
                            $this->db->like('name', $q);
                            $this->db->or_like('email', $q);
                            $this->db->or_like('phonenumber', $q);
                            $this->db->or_like('dateadded', $q);
                          }

                          $this->db->order_by('leadorder', 'asc');
                          $this->db->order_by('dateadded', 'desc');
                          $this->db->limit(500);
                          $leads = $this->db->get()->result_array();

                          foreach ($leads as $lead) {
                            $lead_already_client_tooltip = '';
                            if (total_rows('tblclients', array(
                              'leadid' => $lead['id']
                              ))) {
                              $lead_already_client_tooltip = ' data-toggle="tooltip" title="' . _l('lead_have_client_profile') . '"';
                          }

                          if ($lead['status'] == $status['id']) {
                            ?>
                            <li data-lead-id="<?php echo $lead['id']; ?>"<?php echo $lead_already_client_tooltip; ?> class="<?php if(total_rows('tblclients',array('leadid'=>$lead['id'])) > 0){echo 'not-sortable';} ?>">
                              <div class="panel-body lead-body<?php if(total_rows('tblclients',array('leadid'=>$lead['id'])) > 0){echo ' not-sortable';} ?>">
                                <div class="row">
                                 <div class="col-md-12 lead-name">
                                   <a href="#" data-toggle="modal" data-lead-id="<?php echo $lead['id']; ?>" data-target=".kan-ban-lead-modal"><h5 class="bold"><?php echo $lead['name']; ?></h5></a>
                                 </div>
                                 <div class="col-md-7 mtop10 text-right text-muted">
                                 </div>
                                 <div class="col-md-6 text-muted">
                                     <?php if ($this->leads_model->get_source($lead['source'])) { ?>
                                        <small><?php echo _l('leads_canban_source', $this->leads_model->get_source($lead['source'])->name); ?></small>
                                     <?php }?>
                                 </div>
                                 <div class="col-md-6 text-right text-muted">
                                   <?php if(is_date($lead['lastcontact'])){ ?>
                                   <small>Last Contact: <span class="bold"><?php echo time_ago($lead['lastcontact']); ?></span></small><br />
                                   <?php } ?>
                                   <small>Added: <span class="bold"><?php echo time_ago($lead['dateadded']); ?></span></small><br />
                                   <small><i class="fa fa-paperclip"></i> <?php echo _l('leads_canban_notes', total_rows('tblleadnotes', array(
                                    'leadid' => $lead['id']
                                    ))); ?></small>
                                  </div>
                                  <?php
                                  if ($lead['assigned'] != 0) { ?>
                                  <div class="col-md-12 mtop10">
                                   <a href="<?php echo admin_url('profile/' . $lead['assigned']); ?>" data-placement="right" data-toggle="tooltip" title="<?php echo get_staff_full_name($lead['assigned']); ?>">
                                     <?php echo staff_profile_image($lead['assigned'], array(
                                      'staff-profile-image-small'
                                      )); ?></a>
                                    </div>
                                    <?php  } ?>
                                  </div>
                                </div>
                              </li>
                              <?php
                            }
                          }
                          ?>
                        </ul>
                      </div>
                    </div>
                  </li>
                </ul>
                <?php } ?>
