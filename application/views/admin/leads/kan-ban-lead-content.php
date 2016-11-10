<?php $this->load->view('admin/leads/lead_tabs',array('profile'=>$this->load->view('admin/leads/profile','',true))); ?>
<script>
    initDataTable('.table-rel-tasks', admin_url + 'tasks/init_relation_tasks/<?php echo $lead->id; ?>/lead', 'tasks');
    initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?php echo $lead->id ;?> + '/' + 'lead', 'reminders', [4], [4]);
    initDataTable('.table-proposals-lead', admin_url + 'proposals/proposal_relations/' + <?php echo $lead->id ;?> + '/lead', 'proposals', 'undefined', 'undefined');
    init_selectpicker();
    validate_lead_form(lead_kanban_profile_form_handler);
    validate_lead_convert_to_client_form();
    init_datepicker();
    _validate_form($('#form-reminder'), {
        date: 'required',
        staff: 'required'
    }, reminderFormHandler);
    $('body').find('.nav-tabs a[href="' + window.location.hash + '"]').tab('show');
    new Dropzone("#lead-attachment-upload", {
        addRemoveLinks: false,
        sending: function(file, xhr, formData) {
            formData.append("leadid", <?php echo $lead->id; ?>);
        },
        complete: function(file) {
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                get_lead_attachments(<?php echo $lead->id; ?>);
            }
        }
    });

</script>
