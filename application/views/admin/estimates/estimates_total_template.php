    <?php if(isset($currencies)){
     $col = 'col-md-2 ';
     ?>
     <div class="<?php echo $col; ?> stats-total-currency">
        <div class="panel_s">
            <div class="panel-body">
                <select class="selectpicker" name="estimate_total_currency" onchange="init_estimates_total();" data-width="100%">
                    <?php foreach($currencies as $currency){
                        $selected = '';
                        if(!$this->input->post('currency')){
                            if($currency['isdefault'] == 1){
                                $selected = 'selected';
                            }
                        } else {
                            if($this->input->post('currency') == $currency['id']){
                               $selected = 'selected';
                           }
                       }
                       ?>
                       <option value="<?php echo $currency['id']; ?>" <?php echo $selected; ?> data-subtext="<?php echo $currency['name']; ?>"><?php echo $currency['symbol']; ?></option>
                       <?php } ?>
                   </select>
               </div>
           </div>
       </div>
       <?php
   } else {
    $col = 'col-md-5ths col-xs-6 ';
}

?>
<?php foreach($totals as $status => $data){
    if($status == 0){
        $_status_lang = 'estimate_status_draft';
        $desc_class = 'text-muted';
    } else if ($status == 1){
        $_status_lang = 'estimate_status_sent';
        $desc_class = 'text-info';
    } else if ($status == 2){
        $_status_lang = 'estimate_status_declined';
        $desc_class = 'text-danger';
    } else if ($status == 3){
        $_status_lang = 'estimate_status_accepted';
        $desc_class = 'text-success';
    } else if ($status == 4){
        $_status_lang = 'estimate_status_expired';
        $desc_class = 'text-warning';
    }
    ?>
    <div class="<?php echo $col; ?>total-column">
        <div class="panel_s">
            <div class="panel-body">
                <h3 class="_total">
                    <?php echo format_money($data['total'],$data['symbol']); ?>
                </h3>
                <span class="<?php echo $desc_class; ?>"><?php echo _l($_status_lang); ?></span>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="clearfix"></div>
    <script>
        init_selectpicker();
    </script>
