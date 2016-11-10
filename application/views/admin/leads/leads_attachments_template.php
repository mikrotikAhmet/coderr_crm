<?php
$data = '<div class="row">';
foreach($attachments as $attachment) {
    $data .= '<div class="display-block lead-attachment-wrapper">';
    $data .= '<div class="col-md-10">';
    $data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
$data .= '<a href="'.site_url('download/file/lead_attachment/'.$attachment['id']).'">'.$attachment['file_name'].'</a>';
    $data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
    $data .= '</div>';
    $data .= '<div class="col-md-2 text-right">';
   // $data .= '  <button type="button" data-toggle="modal" data-return-url="'.admin_url('leads/lead/'.$attachment['leadid']).'" data-original-file-name="'.$attachment['original_file_name'].'" data-filetype="'.$attachment['filetype'].'" data-path="'.LEAD_ATTACHMENTS_FOLDER.$attachment['leadid'].'/'.$attachment['file_name'].'" data-target="#send_file" class="btn btn-info btn-icon"><i class="fa fa-envelope"></i></button>';

    $data .= '<a href="#" class="text-danger" onclick="delete_lead_attachment(this,'.$attachment['id'].'); return false;"><i class="fa fa-trash-o"></i></a>';
    $data .= '</div>';
    $data .= '<div class="clearfix"></div><hr/>';
    $data .= '</div>';
}
$data .= '</div>';
//include_once(APPPATH . 'views/admin/clients/modals/send_file_modal.php');
echo $data;
