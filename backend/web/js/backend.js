
/**
 * Created by sergio on 04.02.16.
 */

$('.btn_view_test').click(function() {
    $.get(
        'view',
        {
            test_id: $(this).data('id')
        },

        function (data) {
            var modal = $('#activity-modal');
            modal.find('.modal-body').html(data);
            modal.modal();
        }
    );
});