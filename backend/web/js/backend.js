
/**
 * Created by sergio on 04.02.16.
 */

$('.btn_view_test').click(function() {
    var url = $(this).data('url');
    $.get(
        url,
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
$('.btn_update_test').click(function() {

    var url = $(this).data('url');

    console.log('url: ' + url);
    console.log('id: ' + $(this).data('id'));

    $.get(
        url,
        {
            test_id: $(this).data('id')
        },

        function (data) {
           console.log(data);
            var modal = $('#activity-modal');
            modal.find('.modal-body').html(data);
            modal.modal();
        }
    );
});