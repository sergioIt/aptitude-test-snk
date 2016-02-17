
/**
 * Created by sergio on 04.02.16.
 */

Aptitude = {};
Aptitude.Backend = {};


$(document).ready(function () {

    Aptitude.Backend.viewResults();
    Aptitude.Backend.updateTest();

    Aptitude.Backend.processAddComment();
    Aptitude.Backend.processUpdateUserStatus();

    Aptitude.Backend.processUpdateBlocksToggle();

    Aptitude.Backend.processViewBlocksToggle();
});

/**
 * обрабатывает переключение видимости блоков редактирования теста
 */
Aptitude.Backend.processUpdateBlocksToggle = function(){

    $(document).on("click", '#comments_toggle', function (){

       $('#comments').toggle();

    });

};
/**
 * обрабатывает переключение видимости грпп вопросов при просмотре отдельного теста
 */
Aptitude.Backend.processViewBlocksToggle = function(){

    $(document).on("click", '.btn_show_group', function (){

       var group = $(this).data('group');
        Aptitude.Backend.hideAllResults();
        Aptitude.Backend.showResultGroup(group);
        console.log(group);

    });
    $(document).on("click", '.btn_show_adequacy', function () {

        Aptitude.Backend.hideAllResults();
        $(document).find('.result[data-check_adequacy="1"]').show();
    });

    $(document).on("click", '.btn_show_health', function () {

        Aptitude.Backend.hideAllResults();
        $(document).find('.result[data-check_health="1"]').show();
    });

    $(document).on("click", '#btn_show_all_results', function (){

        Aptitude.Backend.showAllResults();

    });
};

/**
 * показывает группы проверочных вопросов и ответов
 *
 * @param group номер проверочной группы вопросов
 */
Aptitude.Backend.showResultGroup = function(group){

    $(document).find('.result[data-check_group="'+group+'"]').show();

};
/**
 * Скрывает все вопросы и ответы
 */
Aptitude.Backend.hideAllResults = function(){

    $(document).find('.result').hide();
};

/**
 * Показывает все вопросы и ответы
 */
Aptitude.Backend.showAllResults = function(){

    $(document).find('.result').show();

};

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

        $.get(
            url,
            {
                test_id: $(this).data('id')
            },

            function (response) {
                var modal = $('#activity-modal');
                modal.find('.modal-body').html(response);
                modal.modal();
                Aptitude.Backend.initModalState();
            }
        );
    });

};
/**
 * сбрасывает в начальное сосотяние окно для update
 */
Aptitude.Backend.initModalState = function(){

    Aptitude.Backend.disableAddCommentButton();
    Aptitude.Backend.hideAlerts();
    Aptitude.Backend.emptyCommentFiled();


};
/**
 * обработка добавления коммента к тесту
 */
Aptitude.Backend.processAddComment = function(){



    $(document).on("keyup", '#comment_field', function (){

        Aptitude.Backend.enableAddCommentButton();

    });

    $(document).on("click", '#btn_add_comment', function (){

        Aptitude.Backend.addComment($(this));

    });

};
/**
 * обработка изменения статуса кандидата
 */
Aptitude.Backend.processUpdateUserStatus = function(){

    $(document).on("click", '#btn_update_status', function (){

        Aptitude.Backend.updateUserStatus($(this));

    });
};
/**
 * обновляет стаутс кандидата на сервере
 */
Aptitude.Backend.updateUserStatus = function(button){

    var data = Aptitude.Backend.getUserStatusData();
   var url =  button.data('url');
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        success: function (response) {
            // рендерим следующий вопрос
            console.log(response);
          if(response == 1)
          {
              Aptitude.Backend.showSuccessAlert('status');
          }
        }
    });

};
/**
 * включает кнопку добавления комментария
 */
Aptitude.Backend.enableAddCommentButton = function(){
    $(document).find('#btn_add_comment').prop('disabled', false);

};
/**
 * отключает кнопку добавление комментария
 */
Aptitude.Backend.disableAddCommentButton = function(){
    $(document).find('#btn_add_comment').prop('disabled', true);
};
/**
 * скрывает все уведомления
 */
Aptitude.Backend.hideAlerts = function(){
    $(document).find('#alert_comment_added').hide();
    $(document).find('#alert_status_changed').hide();

};
/**
 * очищает поля для комментария
 */
Aptitude.Backend.emptyCommentFiled = function(){

    $(document).find('#comment_field').val('');
};
/**
 * показывает уведомление об успешном обновлении
 */
Aptitude.Backend.showSuccessAlert = function(type){
    var alert;
    if(type == 'comment'){
            alert = $(document).find('#alert_comment_added');
    }
    if(type == 'status'){
        alert = $(document).find('#alert_status_changed');
    }
    alert.show();
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
 *  Получает данные для изменения статуса кандидата
 *
 * @returns {{user_id: (*|jQuery), status: (*|jQuery)}}
 */
Aptitude.Backend.getUserStatusData = function(){
    return  {
        user_id: $(document).find('#btn_update_status').data('user_id'),
        status: $(document).find('#test_user_status_dropdown').val()
    };

};
/**
 * Сохраняет комметарий к тесту
 */
Aptitude.Backend.addComment = function(button){

    var data = Aptitude.Backend.getCommentData();
    var url =  button.data('url');
    console.log('url: '+ url);
    console.log('comment data:' + data);
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        success: function (response) {
            // рендерим следующий вопрос
            console.log(response);
            Aptitude.Backend.showSuccessAlert('comment');
            //$(document).find('#comments').append('<span class="label label-primary">sdfsdfdsfds</span>');
        }
    });
};