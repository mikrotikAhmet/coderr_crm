<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#affiliate_group_modal"><?php echo _l('new_affiliate_group'); ?></a>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php render_datatable(array(
                            _l('affiliate_group_name'),
                            _l('options'),
                            ),'affiliate-groups'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal animated fadeIn" id="affiliate_group_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <span class="edit-title"><?php echo _l('affiliate_group_edit_heading'); ?></span>
                        <span class="add-title"><?php echo _l('affiliate_group_add_heading'); ?></span>
                    </h4>
                </div>
                <?php echo form_open('admin/affiliates/group'); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo render_input('name','affiliate_group_name'); ?>
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
            name: 'required'
        }, manage_affiliate_groups);
        initDataTable('.table-affiliate-groups', window.location.href, 'groups', [1], [1]);

        function manage_affiliate_groups(form) {

            var data = $(form).serialize();
            var url = form.action;
            $.post(url, data).success(function(response) {
                response = $.parseJSON(response);
                if (response.success == true) {
                    $('.table-affiliate-groups').DataTable().ajax.reload();
                    alert_float('success', response.message);
                }
                $('#affiliate_group_modal').modal('hide');
            });

            return false;
        }

        $('#affiliate_group_modal').on('show.bs.modal', function(e) {
            var invoker = $(e.relatedTarget);
            var group_id = $(invoker).data('id');
            $('#affiliate_group_modal .add-title').removeClass('hide');
            $('#affiliate_group_modal .edit-title').addClass('hide');
            $('#affiliate_group_modal input').val('');
            // is from the edit button
            if (typeof(group_id) !== 'undefined') {
                $('#affiliate_group_modal input[name="id"]').val(group_id);
                $('#affiliate_group_modal .add-title').addClass('hide');
                $('#affiliate_group_modal .edit-title').removeClass('hide');
                $('#affiliate_group_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
            }
        });
    </script>
</body>
</html>
