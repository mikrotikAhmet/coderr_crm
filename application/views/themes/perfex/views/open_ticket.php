<?php echo form_open_multipart('clients/open_ticket',array('class'=>'open-new-ticket-form')); ?>
<div class="col-md-7">
	<div class="panel_s">
		<div class="panel-heading">
			<?php echo _l('clients_ticket_open_subject'); ?>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label for="subject"><?php echo _l('customer_ticket_subject'); ?></label>
						<input type="text" class="form-control" name="subject" id="subject">
					</div>
					<div class="form-group">
						<label for="department"><?php echo _l('clients_ticket_open_departments'); ?></label>
						<select name="department" id="department" class="form-control selectpicker">
							<option value=""></option>
							<?php foreach($departments as $department){ ?>
							<option value="<?php echo $department['departmentid']; ?>"><?php echo $department['name']; ?></option>
							<?php } ?>
						</select>
					</div>
					<?php
					if(get_option('services') == 1){ ?>
					<div class="form-group">
						<label for="service"><?php echo _l('clients_ticket_open_service'); ?></label>
						<select name="service" id="service" class="form-control selectpicker">
							<option value=""></option>
							<?php foreach($services as $service){ ?>
							<option value="<?php echo $service['serviceid']; ?>"><?php echo $service['name']; ?></option>
							<?php } ?>
						</select>
					</div>
					<?php } ?>
					<div class="form-group">
						<label for="priority"><?php echo _l('clients_ticket_open_priority'); ?></label>
						<select name="priority" id="priority" class="form-control selectpicker">
							<option value=""></option>
							<?php foreach($priorities as $priority){ ?>
							<option value="<?php echo $priority['priorityid']; ?>"><?php echo $priority['name']; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="col-md-5">
	<div class="panel_s">
		<div class="panel-heading">
			<?php echo _l('clients_latest_tickets'); ?>
		</div>
		<div class="panel-body">
			<ul class="list-group">
				<?php foreach($latest_tickets as $ticket) {?>
				<li class="list-group-item no-margin">
					<a href="<?php echo site_url('clients/ticket/'.$ticket['ticketid']); ?>" target="_blank"><?php echo $ticket['subject']; ?></a><br />
					<span class="small"><?php echo _l('clients_ticket_posted',_dt($ticket['date'])); ?></span>
					<span class="label pull-right" style="background:<?php echo $ticket['statuscolor']; ?>"><?php echo $ticket['status_name']; ?></span>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>
<div class="col-md-12">
	<div class="panel_s">
		<div class="panel-heading">
			<?php echo _l('clients_ticket_open_body'); ?>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<textarea name="message" id="message" class="form-control" rows="15"></textarea>
			</div>
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
						<button class="btn btn-success original-button mtop20 add_more_attachments" type="button"><i class="fa fa-plus"></i></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="col-md-12 text-center mtop20">
	<button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
</div>
</div>
<?php echo form_close(); ?>
