/**
 * Created by sergio on 27.01.16.
 */

Aptitude = {};
Aptitude.Process = {};

Aptitude.Process.nexButton = $(document).find('.getNext');
Aptitude.Process.questionBlock = $(document).find('#questionBlock');

$(document).ready(function () {

    Aptitude.Process.init();

    Aptitude.Process.processAnswer();
    Aptitude.Process.processAdditionalQuestion();

});


/**
 * инициализация процесса проверки ответа
 */
Aptitude.Process.init = function () {

    $('#alertErrorChooseOne').hide();
    $('#alertErrorChooseMultiple').hide();

    Aptitude.Process.enableNexButton();

};
/**
 * Обрабатывает нажатие на следующий вопрос
 *
 * @constructor
 */
Aptitude.Process.processAnswer = function () {

    $(document).on("click", '.getNext', function () {

        if (Aptitude.Process.validateAnswer()) {


            var saveAnswer = false;

            var checkedRadio = $('input.answer:checked');

            if(checkedRadio.data('need_confirm') > 0){
                if(confirm('Вы уверены, что хотите вбырать имеено этот вариант?'))
                {
                    saveAnswer = true;
                }
                else saveAnswer = false;

            }
            else saveAnswer = true;


            if(saveAnswer){
                Aptitude.Process.saveAnswer();
            }


        }
        else {
            var errType;

            var params = Aptitude.Process.getQuestionParams();
            if(params['radio_list'] > 0){
                errType = 'chooseOne';
            }
            if(params['checkbox_list'] > 0){
                errType = 'chooseMultiple';
            }
            console.log('not validated');
            Aptitude.Process.showFlashError(errType);
            Aptitude.Process.disableNexButton();

        }
    });

    var answerArea = $('form.answers');

    // обработка нажатия на ответ после ошибки
    answerArea.on('click', function () {

        Aptitude.Process.init();
    });

};
/**
 * Обрабатывает сохранение ответа на доп. вопрос (в конце)
 */
Aptitude.Process.processAdditionalQuestion = function(){

    $(document).on("click", '#btn_send_reason', function () {

        Aptitude.Process.saveAdditionalAnswer();
        var data = {
            test_id: $(document).find('#questionBlock').data('test_id'),
                    reason: $('#reason_deny').val()
        };



    });

};
/**
 * показывает сообщение об ошибке валидации ответа
 */
Aptitude.Process.showFlashError = function (type) {
    var alertError;

    if (type == 'chooseOne') {

          alertError = $('#alertErrorChooseOne');

    }
    if (type == 'chooseMultiple') {

          alertError = $('#alertErrorChooseMultiple');

    }

    alertError.show();
};
/**
 * блокирует кнопку перехода на след вопрос
 */
Aptitude.Process.disableNexButton = function () {

    Aptitude.Process.nexButton.prop('disabled', true);

};
/**
 * снимает блокировку с кнопки перехода на след. вопрос
 */
Aptitude.Process.enableNexButton = function () {

    $('.getNext').prop('disabled', false);
};

/**
 * Валидириует ответ на вопрос
 * @returns {boolean}
 */
Aptitude.Process.validateAnswer = function () {

    //собираем параметры вопроса из DOM
    var params = Aptitude.Process.getQuestionParams();
    //console.log('params in validation: '+params);
    var selectedCount = 0;
    // если ответ на вопрос обязателен, делаем проверку, выбран ли хотя бы один вариант
    if (params['required'] == 1) {
        //собираем все ответы, проверям выбран ли хотя бы один

        // если вопрос подразумевает ответ по шкале
        // то проерять нечего, считаем что любой ответ корректный
        if (params['scale'] > 0) {

            //console.log('scale param' + params['scale']);
            //var select = $('#select');

                return true;

        }

        // если вопрос подразумевает выброр одного или нескольких вараинтов ответа
        if(params['radio_list'] > 0 || params['checkbox_list'] > 0){

            //console.log('param radio list: ' + params['radio_list']);

            var options = $('.answer');
            //console.log(options);
            options.each(function () {
                if ($(this).is(':checked')) {
                    selectedCount++;
                }

            });

            if(params['radio_list'] > 0 && selectedCount > 0){
                return true;
            }
           // else return false;


            if(params['checkbox_list'] > 0 && selectedCount <= params['max_options'] && selectedCount >= params['min_options']){
                return true;
            }
        }



    }
    else  return true;

    return false;
};

/**
 * Проводит процесс подтверждения варианта ответа на вопрос, если вариант этого требует
 */
/*Aptitude.Process.processConfirmation = function(){
    var checkedRadio = $('input.answer:checked');
    console.log('checked radio: ' + checkedRadio);

    if(checkedRadio.data('need_confirm') > 0){
        if(confirm('Вы уверены, что хотите вбырать имеено этот вариант?'))
        {
            return true;
        }
        return false;
    }

    return false;
};*/
/**
 * Извлекает параметры вопроса из DOM
 *
 * @returns {Array}
 */
Aptitude.Process.getQuestionParams = function () {

    //  var questionBlock = $('#questionBlock');

    var params = [];
    params['test'] = 'tretert';
    params['scale'] = $(document).find('#questionBlock').data('scale');
    params['multiple_answers'] = $(document).find('#questionBlock').data('multiple_answers');
    params['required'] = $(document).find('#questionBlock').data('required');
    params['custom_answer'] = $(document).find('#questionBlock').data('custom_answer');
    params['radio_list'] = $(document).find('#questionBlock').data('radio_list');
    params['checkbox_list'] = $(document).find('#questionBlock').data('checkbox_list');
    params['min_options'] = $(document).find('#questionBlock').data('min_options');
    params['max_options'] = $(document).find('#questionBlock').data('max_options');

    return params;
};

/**
 * собирает все данные, необходимые для сохранения ответа
 */
Aptitude.Process.getAnswerData = function () {

    //console.log('question_id: ' + Aptitude.Process.questionBlock.data('id'));

    var params = Aptitude.Process.getQuestionParams();
    //console.log('params: ' + params['test']);
    var scale = null;
    // признак, что ответ свой
    var custom = null;
    var custom_text = null;
    var answer_id = null;
    // массив ответов из чекбоксов
    var checkbox_answers = [];

    // если ответ полность кастомный
    if (params['custom_answer'] > 0) {
        custom = 1;
        custom_text = $(document).find('#customAnswerText').val();
    }

    if (params['scale'] > 0) {

        scale = $(document).find('#select option:selected').text();
        //console.log('debug scale answer:'+scale);

    }
    if (params['radio_list'] > 0) {

        answer_id = $('input.answer:checked').val();
        custom = $(document).find('input.answer:checked').data('custom');
        if (custom == 1) {
            custom_text = $(document).find('#customAnswerText').val();
        }
    }

    if (params['checkbox_list'] > 0) {
        //console.log('process checkbox list');
        var checkboxes = $('input.answer[type=checkbox]:checked');
        //console.log(checkboxes);
        checkboxes.each( function(){
                //console.log($(this));
                checkbox_answers.push($(this).val());
        }

        );

        //console.log('checkbox ansewrs: ' + checkbox_answers);
    }


    return {
        test_id: $(document).find('#questionBlock').data('test_id'),
        question_id: $(document).find('#questionBlock').data('id'),
        answer_id: answer_id,
        scale: scale,
        custom: custom,
        custom_text: custom_text,
        checkbox_answers: checkbox_answers

    };

};

Aptitude.Process.getAdditionalAnswerData = function(){

return  {
        test_id: $(document).find('#questionBlock').data('test_id'),
        deny_reason: $('#deny_reason').val()
    };
};
/**
 * сохраняет результат ответа на вопрос на сервере (ajax)
 */
Aptitude.Process.saveAnswer = function () {

    var data = Aptitude.Process.getAnswerData();
    //console.log('answer data: ' + data);

    $.ajax({
        type: "POST",
        url: 'saveanswer',
        data: data,
        success: function (response) {
            // рендерим следующий вопрос
            // console.log(response);
            $('#content').html(response);

        }
    });

};

Aptitude.Process.saveAdditionalAnswer = function(){

    var data = Aptitude.Process.getAdditionalAnswerData();

    $.ajax({
        type: "POST",
        url: 'saveadditional',
        data: data,
        success: function (response) {
            // рендерим следующий вопрос
             console.log(response);
            $('#questionBlock').hide();
            $('#additionalForm').hide();

        }
    });
};