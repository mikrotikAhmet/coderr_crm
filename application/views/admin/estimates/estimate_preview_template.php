<div class="col-md-12 no-padding animated fadeIn">
    <div class="panel_s">
        <div class="panel-body padding-16">
         <ul class="nav nav-tabs no-margin" role="tablist">
            <li role="presentation" class="active">
                <a href="#tab_estimate" aria-controls="tab_estimate" role="tab" data-toggle="tab">
                    <?php echo _l('estimate'); ?>
                </a>
            </li>
            <li role="presentation">
                <a href="#tab_tasks" aria-controls="tab_tasks" role="tab" data-toggle="tab">
                    <?php echo _l('tasks'); ?>
                </a>
            </li>
            <li role="presentation">
                <a href="#tab_activity" aria-controls="tab_activity" role="tab" data-toggle="tab">
                    <?php echo _l('estimate_view_activity_tooltip'); ?>
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="panel_s">
    <div class="panel-body">
        <div class="row">

            <div class="col-md-3">
                <?php echo format_estimate_status($estimate->status,'mtop10');  ?>
            </div>
            <div class="col-md-9">
                <div class="pull-right">
                    <a href="<?php echo admin_url('estimates/estimate/'.$estimate->id); ?>" class="btn btn-default mright5 pull-left" data-toggle="tooltip" title="<?php echo _l('edit_estimate_tooltip'); ?>" data-placement="bottom"><i class="fa fa-pencil-square-o"></i></a>

                    <a href="<?php echo admin_url('estimates/pdf/'.$estimate->id); ?>" class="btn btn-default pull-left mright5" data-toggle="tooltip" title="View PDF" data-placement="bottom"><i class="fa fa-file-pdf-o"></i></a>
                    <?php
                    $_tooltip = _l('estimate_sent_to_email_tooltip');
                    if($estimate->sent == 1){
                        $_tooltip = _l('estimate_already_send_to_client_tooltip',time_ago($estimate->datesend));
                    }
                    ?>
                    <a href="#" class="estimate-send-to-client btn btn-default mright5 pull-left" data-toggle="tooltip" title="<?php echo $_tooltip; ?>" data-placement="bottom"><i class="fa fa-envelope"></i></a>

                    <div class="btn-group">
                        <button type="button" class="btn btn-default pull-left dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo _l('more'); ?> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="<?php echo site_url('viewestimate/' . $estimate->id . '/' .  $estimate->hash) ?>" target="_blank"><?php echo _l('view_estimate_as_client'); ?></a></li>

                           <?php if($estimate->invoiceid == NULL){ ?>
                            <?php if($estimate->status != '1'){ ?>
                            <li>
                              <a href="<?php echo admin_url() . 'estimates/mark_action_status/1/'.$estimate->id; ?>">
                              <?php echo _l('estimate_mark_as',_l('estimate_status_draft')); ?></a>
                            </li>
                            <?php } ?>
                            <?php if($estimate->status != '2'){ ?>
                            <li>
                              <a href="<?php echo admin_url() . 'estimates/mark_action_status/2/'.$estimate->id; ?>">
                               <?php echo _l('estimate_mark_as',_l('estimate_status_sent')); ?></a>
                            </li>
                            <?php } ?>
                            <?php if($estimate->status != '3'){ ?>
                            <li>
                              <a href="<?php echo admin_url() . 'estimates/mark_action_status/3/'.$estimate->id; ?>">
                               <?php echo _l('estimate_mark_as',_l('estimate_status_declined')); ?></a>
                            </li>
                            <?php } ?>
                            <?php if($estimate->status != '4'){ ?>
                            <li>
                              <a href="<?php echo admin_url() . 'estimates/mark_action_status/4/'.$estimate->id; ?>">
                               <?php echo _l('estimate_mark_as',_l('estimate_status_accepted')); ?></a>
                            </li>
                            <?php } ?>
                                <?php if($estimate->status != '5'){ ?>
                            <li>
                              <a href="<?php echo admin_url() . 'estimates/mark_action_status/5/'.$estimate->id; ?>">
                               <?php echo _l('estimate_mark_as',_l('estimate_status_expired')); ?></a>
                            </li>
                            <?php } ?>
                            <?php } ?>


                            <?php
                            if((get_option('delete_only_on_last_estimate') == 1 && is_last_estimate($estimate->id)) || (get_option('delete_only_on_last_estimate') == 0)){ ?>
                            <li>
                            <a href="<?php echo admin_url('estimates/delete/'.$estimate->id); ?>" data-placement="bottom"><?php echo _l('delete_estimate_tooltip'); ?></a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
                <?php if($estimate->invoiceid == NULL){ ?>
                <a href="<?php echo admin_url('estimates/convert_to_invoice/'.$estimate->id); ?>" class="mleft10 btn btn-success"><?php echo _l('estimate_convert_to_invoice'); ?></a>
                <?php } else { ?>
                <a href="<?php echo admin_url('invoices/list_invoices/'.$estimate->invoice->id); ?>" data-placement="bottom" data-toggle="tooltip" title="<?php echo _l('estimate_invoiced_date',_dt($estimate->invoiced_date)); ?>"class="btn mleft10 btn-info"><?php echo format_invoice_number($estimate->invoice->id); ?></a>
                <?php } ?>

            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <hr />
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">
            <div id="estimate-preview" class="mtop30">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="bold"><a href="<?php echo admin_url('estimates/estimate/'.$estimate->id); ?>"><?php echo format_estimate_number($estimate->id); ?></a></h4>
                            <address>
                                <span class="bold"><a href="<?php echo admin_url('settings?tab_hash=company_info'); ?>" target="_blank"><?php echo get_option('invoice_company_name'); ?></span></a><br>
                                <?php echo get_option('invoice_company_address'); ?><br>
                                <?php echo get_option('invoice_company_city'); ?>, <?php echo get_option('invoice_company_country_code'); ?> <?php echo get_option('invoice_company_postal_code'); ?><br>
                                <?php if(get_option('invoice_company_phonenumber') != ''){ ?>
                                <abbr title="Phone">P:</abbr> <?php echo get_option('invoice_company_phonenumber'); ?><br />
                                <?php } ?>
                                <?php
                                // check for company custom fields
                                $custom_company_fields = get_company_custom_fields();
                                foreach($custom_company_fields as $field){
                                    echo $field['label'] . ':' . $field['value'] . '<br />';
                                }
                                ?>
                            </address>
                        </div>
                        <div class="col-sm-6 text-right">
                            <span class"bold"><?php echo _l('estimate_to'); ?>:</span>
                            <address>
                                <span class="bold"><a href="<?php echo admin_url('clients/client/'.$estimate->client->userid); ?>" target="_blank"><?php echo $estimate->client->company; ?></span></a><br>
                                <?php echo $estimate->billing_street; ?><br>
                                <?php echo $estimate->billing_city; ?>, <?php echo $estimate->billing_state; ?><br/><?php echo get_country_short_name($estimate->billing_country); ?>,<?php echo $estimate->billing_zip; ?><br>
                                <?php if(!empty($estimate->client->vat)){ ?>
                                <?php echo _l('estimate_vat'); ?>: <?php echo $estimate->client->vat; ?><br />
                                <?php } ?>
                                <?php
                                // check for customer custom fields which is checked show on pdf
                                $pdf_custom_fields = get_custom_fields('customers',array('show_on_pdf'=>1));
                                foreach($pdf_custom_fields as $field){
                                    $value = get_custom_field_value($estimate->clientid,$field['id'],'customers');
                                    if($value == ''){continue;}
                                    echo $field['name'] . ': ' . $value . '<br />';
                                }
                                ?>
                            </address>
                            <?php if($estimate->include_shipping == 1 && $estimate->show_shipping_on_estimate == 1){ ?>
                            <span class="bold"><?php echo _l('ship_to'); ?>:</span>
                            <address>
                                <?php echo $estimate->shipping_street; ?><br>
                                <?php echo $estimate->shipping_city; ?>, <?php echo $estimate->shipping_state; ?><br/>
                                <?php echo get_country_short_name($estimate->shipping_country); ?>,<?php echo $estimate->shipping_zip; ?>
                            </address>
                            <?php } ?>
                            <p>
                                <span><span class="text-muted"><?php echo _l('estimate_data_date'); ?>:</span> <?php echo $estimate->date; ?></span>
                                <?php if(!empty($estimate->expirydate)){ ?>

                                <br /><span class="mtop20"><span class="text-muted"><?php echo _l('estimate_data_expiry_date'); ?>:</span>
                                <?php echo $estimate->expirydate; ?></span>
                                <?php } ?>
                                <?php if(!empty($estimate->reference_no)){ ?>

                                <br /><span class="mtop20"><span class="text-muted"><?php echo _l('reference_no'); ?>:</span> <?php echo $estimate->reference_no; ?></span>
                                <?php } ?>
                                <?php if($estimate->sale_agent != 0){
                                    if(get_option('show_sale_agent_on_estimates') == 1){ ?>
                                    <br /><span class="mtop20">
                                    <span class="text-muted"><?php echo _l('sale_agent_string'); ?>:</span>
                                    <?php echo get_staff_full_name($estimate->sale_agent); ?>
                                </span>
                                <?php }
                            }
                            ?>
                            <?php
                        // check for estimate custom fields which is checked show on pdf
                            $pdf_custom_fields = get_custom_fields('estimate',array('show_on_pdf'=>1));
                            foreach($pdf_custom_fields as $field){
                                $value = get_custom_field_value($estimate->id,$field['id'],'estimate');
                                if($value == ''){continue;} ?>
                                <br /><span class="mtop20">
                                <span class="text-muted"><?php echo $field['name']; ?>: </span>
                                <?php echo $value; ?>
                            </span>
                            <?php
                        }
                        ?>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table items">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="description"><?php echo _l('estimate_table_item_heading'); ?></th>
                                    <th><?php echo _l('estimate_table_quantity_heading'); ?></th>
                                    <th><?php echo _l('estimate_table_rate_heading'); ?></th>
                                    <th><?php echo _l('estimate_table_tax_heading'); ?></th>
                                    <th><?php echo _l('estimate_table_amount_heading'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($estimate)){
                                    $_tax_tr = '';
                                    $taxes = array();
                                    $i = 1;
                                    foreach($estimate->items as $item){
                                        $_item = '';
                                        $_item .= '<tr>';
                                        $_item .= '<td>' .$i. '</td>';
                                        $_item .= '<td class="bold description">'.$item['description'].'<br /><span class="text-muted">'.$item['long_description'].'</span></td>';
                                        $_item .= '<td>'.floatVal($item['qty']).'</td>';
                                        $_item .= '<td>'._format_number($item['rate']).'</td>';
                                        $taxrate = ($item['taxrate'] !== null ? $item['taxrate'] : 0);
                                        $_item .= '<td>'.$taxrate.'%</td>';
                                        $_item .= '<td class="amount">'._format_number(($item['qty'] * $item['rate'])).'</td>';
                                        $_item .= '</tr>';
                                        echo $_item;
                                        if($item['taxid']){
                                            if(!array_key_exists($item['taxid'],$taxes)) {
                                                if($item['taxrate'] != null){
                                                    $calculated_tax = (($item['qty'] * $item['rate']) / 100 * $item['taxrate']);
                                                    $taxes[$item['taxid']] = $calculated_tax;
                                                }
                                            } else {
                                                $taxes[$item['taxid']] += $calculated_tax = (($item['qty'] * $item['rate']) / 100 * $item['taxrate']);
                                            }
                                        }
                                        $i++;
                                    }
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4 col-md-offset-8">
                    <table class="table text-right">
                        <tbody>
                            <tr id="subtotal">
                                <td><span class="bold"><?php echo _l('estimate_subtotal'); ?></span>
                                </td>
                                <td class="subtotal">
                                    <?php
                                    if(isset($estimate)){
                                        echo _format_number($estimate->subtotal,$estimate->symbol);
                                        echo form_hidden('subtotal',$estimate->subtotal);
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php if($estimate->discount_percent != 0){ ?>
                            <tr>
                                <td>
                                    <span class="bold"><?php echo _l('estimate_discount'); ?> (<?php echo $estimate->discount_percent; ?>%)</span>
                                </td>
                                <td class="discount">
                                    <?php echo '-' . _format_number($estimate->discount_total); ?>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php
                            if(isset($estimate)){
                                foreach($taxes as $taxid => $total){
                                    $_tax = get_tax_by_id($taxid);
                                    if($estimate->discount_percent != 0 && $estimate->discount_type == 'before_tax'){
                                        $total_tax_calculated = ($total * $estimate->discount_percent) / 100;
                                        $total = ($total - $total_tax_calculated);
                                    }
                                    echo '<tr class="tax-area"><td>'.$_tax->name.'('.$_tax->taxrate.'%)</td><td>'._format_number($total,$estimate->symbol).'</td></tr>';
                                }
                            }
                            ?>
                            <?php if($estimate->adjustment != '0.00'){ ?>
                            <tr>
                                <td>
                                    <span class="bold"><?php echo _l('estimate_adjustment'); ?></span>
                                </td>
                                <td class="adjustment">
                                    <?php echo _format_number($estimate->adjustment); ?>
                                </td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td><span class="bold"><?php echo _l('estimate_total'); ?></span>
                                </td>
                                <td class="total">
                                    <?php
                                    if(isset($estimate)){
                                        echo format_money($estimate->total,$estimate->symbol);
                                        echo form_hidden('subtotal',$estimate->total);
                                    }
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php if($estimate->clientnote != ''){ ?>
                <div class="col-md-12 mtop15">
                    <p class="bold text-muted"><?php echo _l('estimate_note'); ?></p>
                    <p><?php echo $estimate->clientnote; ?></p>
                </div>
                <?php } ?>

                <?php if($estimate->terms != ''){ ?>
                <div class="col-md-12 mtop15">
                    <p class="bold text-muted"><?php echo _l('terms_and_conditions'); ?></p>
                    <p><?php echo $estimate->terms; ?></p>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div role="tabpanel" class="tab-pane ptop10" id="tab_tasks">
    <?php init_relation_tasks_table(array('data-new-rel-id'=>$estimate->id,'data-new-rel-type'=>'estimate')); ?>
</div>
<div role="tabpanel" class="tab-pane ptop10" id="tab_activity">
    <div class="row">
        <div class="col-md-12">
            <?php foreach($activity as $activity){ ?>
            <div class="display-block">
                <?php if(is_numeric($activity['staffid'])){ ?>
                <a href="<?php echo admin_url('profile/'.$activity['staffid']); ?>"><?php echo staff_profile_image($activity['staffid'],array('staff-profile-image-small','pull-left mright10')); ?></a>
                <?php } ?>
                <div class="media-body">
                    <div class="display-block">
                        <?php
                            // Version 1.0.5 fix
                        if(is_numeric($activity['staffid'])
                            && strpos($activity['description'],get_staff_full_name($activity['staffid'])) !== false ){
                            echo $activity['description'];
                    } else {
                        if(is_numeric($activity['staffid'])){
                            echo get_staff_full_name($activity['staffid']) . ' ' . $activity['description'];
                        } else {
                            echo $activity['description'];
                        }
                    }
                    ?>
                </div>
                <small class="text-muted"><?php echo _dt($activity['date']); ?></small>
            </div>
            <hr />
        </div>
        <?php } ?>
    </div>
</div>
</div>
</div>

</div>
</div>
</div>
<script>
    initDataTable('.table-rel-tasks', admin_url +'tasks/init_relation_tasks/<?php echo $estimate->id; ?>/estimate', 'tasks');
</script>
<?php $this->load->view('admin/estimates/estimate_send_to_client'); ?>

