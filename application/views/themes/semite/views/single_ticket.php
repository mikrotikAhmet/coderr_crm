<?php set_ticket_open($ticket->clientread,$ticket->ticketid,false); ?>
<div class="col-md-4">
	<div class="panel_s">
		<div class="panel-heading">
			<?php echo _l('clients_single_ticket_informations_heading'); ?>
		</div>
		<div class="panel-body">
			<h3>
				#<?php echo $ticket->ticketid; ?> - <?php echo $ticket->subject; ?>
			</h3>
			<hr />
			<p>
				<?php echo _l('clients_ticket_single_department', $ticket->department_name); ?>
			</p>
			<hr />
			<p>
				<?php echo _l('clients_ticket_single_submited',_dt($ticket->date)); ?>
			</p>
			<hr />
			<p>
				<?php echo _l('clients_ticket_single_status'); ?> <span class="label pull-right" style="background:<?php echo $ticket->statuscolor; ?>"><?php echo $ticket->status_name; ?></span>
			</p>
			<hr />
			<p>
				<?php echo _l('clients_ticket_single_priority',$ticket->priority_name); ?>
			</p>
		</div>
		<div class="panel-footer">
			<a href="#" class="btn btn-primary single-ticket-add-reply"><?php echo _l('clients_ticket_single_add_reply_btn'); ?></a>
		</div>
	</div>
</div>
<div class="col-md-8">
	<?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'ticket-reply')); ?>
	<input type="hidden" name="userid" value="<?php echo $client->userid; ?>">
	<div class="panel_s single-ticket-reply-area" style="display:none;">
		<div class="panel-heading">
			<?php echo _l('clients_ticket_single_add_reply_heading'); ?>
		</div>
		<div class="panel-body">
			<textarea name="message" class="form-control" rows="15"></textarea>
		</div>
		<div class="panel-footer attachments_area">
			<div class="row attachments">
				<div class="attachment">
					<div class="col-md-6 col-md-offset-3">
						<div class="form-group">
							<label for="attachment" class="control-label"><?php echo _l('clients_ticket_attachments'); ?></label>
							<input type="file" class="form-control" name="attachments[]">
						</div>
					</div>
					<div class="col-md-1">
						<button class="btn btn-success original-button mtop25 add_more_attachments" type="button"><i class="fa fa-plus"></i></button>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 mtop20 text-center">
				<button class="btn btn-primary" type="submit"><?php echo _l('submit'); ?></button>
			</div>
		</div>
	</div>
	<?php echo form_close(); ?>
	<div class="panel_s">
		<div class="panel-heading">
			<?php echo _l('clients_single_ticket_string'); ?>
		</div>
		<div class="panel-body <?php if($ticket->admin == NULL){echo 'client-reply';} ?>">
			<div class="row">
				<div class="col-md-3 border-right">
					<p><?php echo $ticket->submitter; ?></p>
					<p class="text-muted">
						<?php if($ticket->admin !== NULL){
							echo 'Staff';
						} else {
							echo 'Client';
						}
						?>
					</p>
				</div>
				<div class="col-md-9">
					<?php echo check_for_links($ticket->message); ?><br />
					<p>-----------------------------</p>
					<?php if(count($ticket->attachments) > 0){
						echo '<hr />';
						foreach($ticket->attachments as $attachment){ ?>
						<a href="<?php echo site_url('download/file/ticket/'. $attachment['id']); ?>" class="display-block"><i class="fa fa-paperclip"></i> <?php echo $attachment['file_name']; ?></a>
						<?php }
					} ?>
				</div>
			</div>
		</div>
	</div>
	<?php foreach($ticket_replies as $reply){ ?>
	<div class="panel_s">
		<div class="panel-body <?php if($reply['admin'] == NULL){echo 'client-reply';} ?>">
			<div class="row">
				<div class="col-md-3 border-right">
					<p><?php echo $reply['submitter']; ?></p>
					<p class="text-muted">
						<?php if($reply['admin'] !== NULL){
							echo 'Staff';
						} else {
							echo 'Client';
						}
						?>
					</p>
				</div>
				<div class="col-md-9">
					<?php echo check_for_links($reply['message']); ?><br />
					<p>-----------------------------</p>
					<?php if(count($reply['attachments']) > 0){
						echo '<hr />';
						foreach($reply['attachments'] as $attachment){ ?>
						<a href="<?php echo site_url('download/file/ticket/'. $attachment['id']); ?>" class="display-block"><i class="fa fa-paperclip"></i> <?php echo $attachment['file_name']; ?></a>
						<?php }
					} ?>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<span><?php echo _l('clients_single_ticket_replied',_dt($reply['date'])); ?></span>
		</div>
	</div>
	<?php } ?>
</div>
