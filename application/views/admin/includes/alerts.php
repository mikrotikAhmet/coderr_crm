<?php
$alertclass = "";
if($this->session->flashdata('message-success')){
	$alertclass = "success";
} else if ($this->session->flashdata('message-warning')){
	$alertclass = "warning";
} else if ($this->session->flashdata('message-info')){
	$alertclass = "info";
} else if ($this->session->flashdata('message-danger')){
	$alertclass = "danger";
}
if($this->session->flashdata('debug')){ ?>
<div class="col-lg-12">
	<div class="alert alert-warning">
		<?php echo $this->session->flashdata('debug'); ?>
	</div>
</div>
<?php } ?>
<?php
if($this->session->flashdata('message-'.$alertclass)){ ?>
<div class="col-lg-12" id="alerts">
	<div class="alert alert-<?php echo $alertclass; ?>">
		<?php echo $this->session->flashdata('message-'.$alertclass); ?>
	</div>
</div>
<?php } ?>

<?php
$_announcements = get_announcements_for_user();

if(sizeof($_announcements) > 0){ ;?>
<div class="col-lg-12">
	<?php foreach($_announcements as $__announcement){ ?>
	<div class="alert alert-info alert-dismissible" role="alert">
		<button type="button" class="close dismiss_announcement" data-id="<?php echo $__announcement['announcementid']; ?>" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4>Announcement! <?php if($__announcement['showname'] == 1){ echo 'From: '. $__announcement['firstname'] . ' ' .$__announcement['lastname']; } ?></h5><small class="pull-right">Added: <?php echo _dt($__announcement['dateadded']); ?></small>
		<?php echo $__announcement['message']; ?>
	</div>
	<?php } ?>
</div>
<?php } ?>

