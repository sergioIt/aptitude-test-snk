
/**
 * Created by sergio on 04.02.16.
 */

Aptitude = {};
Aptitude.Backend = {};


$(document).ready(function () {

    Aptitude.Backend.viewResults();
    Aptitude.Backend.updateTest();


    Aptitude.Backend.processAddComment();




});

/**
 * Вызывает окно просмотра результатов теста
 */
Aptitude.Backend.viewResults = function(){
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
};

/**
 * Вызывает окно редактирования теста
 */
Aptitude.Backend.updateTest = function() {
    $('.btn_update_test').click(function () {

        var url = $(this).data('url');

       // console.log('url: ' + url);
       // console.log('id: ' + $(this).data('id'));

        $.get(
            url,
            {
                test_id: $(this).data('id')
            },

            function (data) {
//                console.log(data);
                var modal = $('#activity-modal');
                modal.find('.modal-body').html(data);
                modal.modal();
                Aptitude.Backend.initModalState();
            }
        );
    });

};

Aptitude.Backend.initModalState = function(){

    Aptitude.Backend.disableAddCommentButton();
    Aptitude.Backend.hideAlerts();
    Aptitude.Backend.emptyCommentFiled();


};
Aptitude.Backend.processAddComment = function(){



    $(document).on("keyup", '#comment_field', function (){

        Aptitude.Backend.enableAddCommentButton();

    });

    $(document).on("click", '#btn_add_comment', function (){

        Aptitude.Backend.addComment();

    });

};

Aptitude.Backend.enableAddCommentButton = function(){
    $(document).find('#btn_add_comment').prop('disabled', false);

};

Aptitude.Backend.disableAddCommentButton = function(){
    $(document).find('#btn_add_comment').prop('disabled', true);
};

Aptitude.Backend.hideAlerts = function(){
    $(document).find('#alert_comment_added').hide();

};

Aptitude.Backend.emptyCommentFiled = function(){

    $(document).find('#comment_field').val('');
};

Aptitude.Backend.showSuccessAlert = function(){

    $(document).find('#alert_comment_added').show();
    setTimeout(function() {

        Aptitude.Backend.initModalState();

    }, 3000);
};

/**
 * Получает данные для сохранения нового комментария
 *
 * @returns {{test_id: (*|jQuery), user_id: (*|jQuery), text: (*|jQuery)}}
 */
Aptitude.Backend.getCommentData = function(){

    return  {
        test_id: $(document).find('#btn_add_comment').data('test_id'),
        user_id: $(document).find('#btn_add_comment').data('user_id'),
        text: $('#comment_field').val()
    };
};
/**
 * Сохраняет комметарий к тесту
 */
Aptitude.Backend.addComment = function(){

    var data = Aptitude.Backend.getCommentData();
    console.log('comment data:' + data);
    $.ajax({
        type: "POST",
        url: 'test/savecomment',
        data: data,
        success: function (response) {
            // рендерим следующий вопрос
            console.log(response);
            Aptitude.Backend.showSuccessAlert();
            //$(document).find('#comments').append('<span class="label label-primary">sdfsdfdsfds</span>');
        }
    });
};