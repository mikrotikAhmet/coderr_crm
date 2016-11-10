<?php
if(count($attachments_array) > 0){
  $attachments_modal .= '<a href="#" class="label-href" data-toggle="modal" data-target="#myModal">
  <small class="label label-info">'._l('clients_contract_attachments').'</small>
</a>';
$attachments_modal .= '<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">'._l('clients_contract_attachments').'</h4>
    </div>
    <div class="modal-body">';
      $attachments_modal .= '<div class="row">';
      foreach($attachments_array as $attachment) {
        $attachments_modal .= '<div class="display-block" style="padding:0px;">';
        $attachments_modal .= '<div class="col-md-12">';
        $attachments_modal .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
        $attachments_modal .= '<a href="'.site_url('download/file/contract/'.$attachment['id']).'">'.$attachment['file_name'].'</a>';
        $attachments_modal .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
        $attachments_modal .= '</div>';
        $attachments_modal .= '<div class="clearfix"></div><hr class="no-margin"/>';
        $attachments_modal .= '</div>';
      }
      $attachments_modal .= '</div>';
      $attachments_modal .= '</div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->';
}
?>
