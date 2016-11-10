<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#processor_modal">Add New Processor</a>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php render_datatable(array(
                            'Processor',
                            'Status',
                            _l('options'),
                        ),'processors'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal animated fadeIn" id="processor_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title">Modify Processor</span>
                    <span class="add-title">Add New Processor</span>
                </h4>
            </div>
            <?php echo form_open('admin/processors/processor'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('name', 'Processor Name'); ?>
                        <div class="form-group" >
                            <label for="processor_object" class="control-label">Default Object</label>
                            <select id="object" name="object" class="form-control selectpicker" data-width="100%" data-live-search="true">
                                <option value=""></option>
                                <?php foreach(get_processor_object_list() as $processor){ ?>
                                    <option value="<?php echo strtolower($processor['name']); ?>"><?php echo ucfirst($processor['name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" name="status" value="1"/>
                                <label>
                                    Enabled / Disbale Processor
                                </label>
                            </div>
                        </div>
                        <?php echo form_hidden('id'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    _validate_form($('form'), {
        name: 'required',
        object : 'required'
    }, manage_processors);
    initDataTable('.table-processors', window.location.href, 'processors', [1], [2]);

    function manage_processors(form) {

        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).success(function(response) {
            response = $.parseJSON(response);
            if (response.success == true) {
                $('.table-processors').DataTable().ajax.reload();
                alert_float('success', response.message);
            }
            $('#processor_modal').modal('hide');
        });

        return false;
    }

    $('#processor_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var processor_id = $(invoker).data('id');
        var object = $(invoker).data('object');
        var status = $(invoker).data('status');

        console.log(status+'----'+object);

        $('#processor_modal .add-title').removeClass('hide');
        $('#processor_modal .edit-title').addClass('hide');
        $('#processor_modal input[name="name"]').val('');
        $('#processor_modal select[name="object"]').val('');
        $('#processor_modal input[name="status"]').val('');
        $('select[name="object"]').trigger('change');


        // is from the edit button
        if (typeof(processor_id) !== 'undefined') {
            $('#processor_modal input[name="id"]').val(processor_id);
            $('#processor_modal .add-title').addClass('hide');
            $('#processor_modal .edit-title').removeClass('hide');
            $('#processor_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
            $('#processor_modal select[name="object"]').val(object);
            $('#processor_modal input[name="status"]').attr("checked",true);
            $('select[name="object"]').trigger('change');

        }
    });
</script>
</body>
</html>
