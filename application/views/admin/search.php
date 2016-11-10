 <ul class="dropdown-menu search-results animated fadeIn display-block" id="top_search_dropdown">
   <?php
   $total = 0;
   foreach($result as $heading => $results){
    if(count($results) > 0){
        $total++;
        ?>
        <li role="separator" class="divider"></li>
        <li class="dropdown-header"><?php echo ucwords(str_replace('_',' ',$heading)); ?></li>
        <?php } ?>
        <?php foreach($results as $_result){
            $data = '';
            switch($heading){
                case 'clients':
                $data = '<a href="'.admin_url('clients/client/'.$_result['userid']).'">'.$_result['firstname']. ' ' . $_result['lastname'] .'</a>';
                break;
                case 'staff':
                $data = '<a href="'.admin_url('staff/member/'.$_result['staffid']).'">'.$_result['firstname']. ' ' . $_result['lastname'] .'</a>';
                break;
                case 'tickets':
                $data = '<a href="'.admin_url('tickets/ticket/'.$_result['ticketid']).'">#'.$_result['ticketid'].'</a>';
                break;
                case 'surveys':
                $data = '<a href="'.admin_url('surveys/survey/'.$_result['surveyid']).'">'.$_result['subject'].'</a>';
                break;
                case 'knowledge_base_articles':
                $data =  $data = '<a href="'.admin_url('knowledge_base/article/'.$_result['articleid']).'">'.$_result['subject'].'</a>';
                break;
                case 'leads':
                $data =  $data = '<a href="'.admin_url('leads/lead/'.$_result['id']).'">'.$_result['name'].'</a>';
                break;
                case 'tasks':
                $data =  $data = '<a href="'.admin_url('tasks/task/'.$_result['id']).'">'.$_result['name'].'</a>';
                break;
                case 'contracts':
                $data =  $data = '<a href="'.admin_url('contracts/contract/'.$_result['id']).'">'.$_result['subject'].'</a>';
                break;
                case 'invoice_payment_records':
                $data =  $data = '<a href="'.admin_url('payments/payment/'.$_result['paymentid']).'">#'.$_result['paymentid'].'</a>';
                break;
                case 'invoices':
                $data =  $data = '<a href="'.admin_url('invoices/list_invoices/'.$_result['invoiceid']).'">'.format_invoice_number($_result['id']).'</a>';
                break;
                case 'expenses':
                $data =  $data = '<a href="'.admin_url('expenses/list_expenses/'.$_result['expenseid']).'">'.$_result['category_name']. ' - ' ._format_number($_result['amount']).'</a>';
                break;
                case 'goals':
                $data =  $data = '<a href="'.admin_url('goals/goal/'.$_result['id']).'">'.$_result['subject'].'</a>';
                break;
                case 'custom_fields':
                $rel_data   = get_relation_data($_result['fieldto'], $_result['relid']);
                $rel_values = get_relation_values($rel_data, $_result['fieldto']);
                $data      = '<a data-toggle="tooltip" class="pull-left" title="' . ucfirst($_result['fieldto']) . '" href="' . $rel_values['link'] . '">' . $rel_values['name'] . '</a>';
                break;
                case 'invoice_items':
                $data =  $data = '<a href="'.admin_url('invoices/list_invoices/'.$_result['invoiceid']).'">'.format_invoice_number($_result['invoiceid']);
                $data .= '<br />';
                $data .= '<small>'.$_result['description'].'</small>';
                $data .= '</a>';
                break;
            }
            ?>
            <li><?php echo $data; ?></li>
            <?php } ?>
            <?php } ?>
            <?php if($total == 0){ ?>
            <li class="padding-5 text-center">No Results Found</li>
            <?php } ?>
        </ul>

