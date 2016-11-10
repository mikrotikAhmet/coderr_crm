<?php
ob_start();
$proposal_comments = '';
foreach ($comments as $comment) {
    $proposal_comments .= '<div class="col-md-12 mbot10" data-commentid="' . $comment['id'] . '">';
    if($comment['staffid'] != 0){
      $proposal_comments .= '<a href="' . admin_url('profile/' . $comment['staffid']) . '">' . staff_profile_image($comment['staffid'], array(
        'staff-profile-image-small',
        'media-object img-circle pull-left mright10'
        )) . '</a>';
  }
  if ($comment['staffid'] == get_staff_user_id() || is_admin()) {
    $proposal_comments .= '<span class="pull-right"><a href="#" onclick="remove_proposal_comment(' . $comment['id'] . '); return false;"><i class="fa fa-trash text-danger"></i></span></a>';
}
$proposal_comments .= '<div class="media-body">';
if($comment['staffid'] != 0){
    $proposal_comments .= '<a href="' . admin_url('profile/' . $comment['staffid']) . '">' . get_staff_full_name($comment['staffid']) . '</a> <br />';
}
$proposal_comments .= check_for_links($comment['content']) . '<br />';
$proposal_comments .= '<small class="mtop10 text-muted">' . _dt($comment['dateadded']) . '</small>';

$proposal_comments .= '</div>';
$proposal_comments .= '</div>';
}
echo $proposal_comments;
