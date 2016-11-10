<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-heading">
            <?php echo $title; ?>
          </div>
          <div class="panel-body">
            <div>
              <ul class="nav nav-tabs" role="tablist">
                <?php
                $count = count($rss_sites);
                for($i = 0; $i < $count; $i++){ ?>
                <li role="presentation" class="<?php if($i == 0){echo 'active';}; ?>">
                  <a href="#<?php echo slug_it($rss_sites[$i]['name']); ?>" aria-controls="<?php echo slug_it($rss_sites[$i]['name']); ?>" role="tab" data-toggle="tab">
                    <?php echo $rss_sites[$i]['name']; ?>
                  </a>
                </li>
                <?php } ?>
              </ul>
              <div class="tab-content">
                <?php for($x = 0; $x < $count; $x++){ ?>
                <div role="tabpanel" class="tab-pane ptop10 <?php if($x == 0){echo 'active';}; ?>" id="<?php echo slug_it($rss_sites[$x]['name']); ?>">
                  <?php
                  $content = utf8_encode(file_get_contents($rss_sites[$x]['feed_url']));
                  try {
                   $xml = new SimpleXmlElement($content);
                   if(isset($xml->channel->item)){
                    foreach($xml->channel->item as $entry) { ?>
                    <div class="row">
                      <div class="col-md-12">
                        <h4 class="bold">
                          <a href="<?php echo $entry->link; ?>" title="<?php echo $entry->title; ?>" target="_blank"><?php echo $entry->title; ?></a>
                          <small><?php echo _dt(date('Y-m-d H:i:s',strtotime($entry->pubDate))); ?></small>
                        </h4>
                      </div>
                      <div class="clearfix"></div>
                      <div class="col-md-12">
                        <div class="feed_description">
                         <?php
                         echo $entry->description;
                         ?>
                       </div>
                     </div>
                   </div>
                   <hr />
                   <?php } ?>
                   <?php } ?>
                   <?php }
                   catch (Exception  $e) { }
                   ?>
                 </div>
                 <?php } ?>
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
 </script>
</body>
</html>
