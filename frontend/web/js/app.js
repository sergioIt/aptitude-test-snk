/**
 * Created by sergio on 26.01.16.
 */
BeginTest = {};


$(document).ready(function(){

    $('.input_phone').inputmask({"mask": "+9(999)999-9999"});

    $('#btn_begin_test').on('click',function(){

        $('#intro').hide();
        $('#begin-test-form').show();
    });


});