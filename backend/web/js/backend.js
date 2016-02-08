
/**
 * Created by sergio on 04.02.16.
 */

$('.btn_view_test').click(function() {
    $.get(
        'test/view',
        {
            test_id: $(this).data('id')
        },

        function (data) {
            var modal = $('#activity-modal');
            var test_id = $(this).data('id');
            //$('#test_id_container-modal').html($(this).data('id'));
            console.log(test_id);
            modal.find('.modal-body').html(data);
            modal.modal();
            //$('#activity-modal').modal();
        }
    );
});