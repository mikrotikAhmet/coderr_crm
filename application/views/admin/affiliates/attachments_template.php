<?php $no_attachments = true; ?>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th><?php echo _l('customer_attachments_file'); ?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
         <?php foreach($attachments as $type => $attachment){

            if($type == 'affiliate'){
                $url = site_url() .'download/file/affiliate/';
                $upload_path = AFFILIATE_ATTACHMENTS_FOLDER;
                $key_indicator = 'affiliateid';
            }
            ?>
            <?php foreach($attachment as $_att){
                $no_attachments = false;
                ?>
                <tr>
                   <td>
                    <i class="<?php echo get_mime_class($_att['filetype']); ?>"></i>
                    <a data-toggle="tooltip" data-title="<?php echo _l('customer_file_from',ucfirst($type)); ?>" href="<?php echo $url . $_att['id']; ?>"><?php echo $_att['file_name']; ?></a>
                    <br />
                    <small class="text-muted"> <?php echo $_att['filetype']; ?></small>
                </td>
                <td>
                  <?php $path = $upload_path . $_att[$key_indicator] . '/' . $_att['file_name'];
                  if(is_image($path)){
                    $base64 = base64_encode(file_get_contents($path));
                    $src = 'data: '.get_mime_by_extension($_att['file_name']).';base64,'.$base64;
                    ?>
                    <button type="button" class="btn btn-info btn-icon" data-placement="bottom" data-html="true" data-toggle="popover" data-content='<img src="<?php echo $src; ?>" class="img img-responsive mbot20">' data-trigger="focus"><i class="fa fa-eye"></i></button>
                    <?php } ?>
                    <button type="button" data-toggle="modal" data-return-url="<?php echo admin_url('affiliates/affiliate/'.$affiliateid); ?>" data-file-name="<?php echo $_att['file_name']; ?>" data-filetype="<?php echo $_att['filetype']; ?>" data-path="<?php echo $path; ?>" data-target="#send_file" class="btn btn-info btn-icon"><i class="fa fa-envelope"></i></button>
                    <?php if($type == 'affiliate'){ ?>
                    <a href="#" onclick="delete_customer_main_file(<?php echo $_att['id']; ?>,this); return false;" class="btn btn-danger btn-icon"><i class="fa fa-remove"></i></a>
                    <?php } ?>
                </td>
                <?php } ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php
if($no_attachments == true){
    echo _l('customer_no_attachments_found');
} ?>
