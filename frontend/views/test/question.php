<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 25.01.16
 * Time: 18:42
 *
 * view дл отдельного вопроса теста
 */

use yii\helpers\Html;
//use yii\bootstrap\Modal;

$this->title = 'Тестирование';
$this->registerCssFile('/css/question.css');
$this->registerJsFile('js/testProcess.js',
    ['depends' => ['\yii\web\JqueryAsset'],
        'position' => \yii\web\View::POS_END,]
);

?>
<h2>Вопрос <?= $question->id ?> из <?= $questionsCount ?> </h2>
<?= yii\bootstrap\Progress::widget(['percent' => $percent, 'label' => '']) ?>

<div class="question row" id="questionBlock"
     data-id="<?= $question->id ?>"
     data-scale="<?= $question->scale ?>"
     data-multiple_answers="<?= $question->multiple_answers ?>"
     data-required="<?= $question->required ?>"
     data-test_id="<?= $testId ?>"
     data-custom_answer="<?= $question->custom_answer?>"
     data-radio_list="<?= $question->radioList?>"
     data-checkbox_list="<?= $question->checkboxList?>"
     data-min_options="<?= $question->min_options?>"
     data-max_options="<?= $question->max_options?>"
    >
    <h2>        <?= $question->text ?>    </h2>
</div>

<div class="row">


<form class="answers">

<div class="form-group">
    <? if (isset($question->scale)) {
        ?>
        <div class="col-md-1">
            <?= Html::dropDownList('scale', '0', $question->scaleData,
                ['id' => 'select','class' => 'form-control']); ?>

        </div>
    <?
    } ?>
    <?
    if (isset($question->answers)) {
        /*var_dump($question->answers);*/
        foreach ($question->answers as $answer) {     ?>
            <div class="<?=$question->listItemType?>">
                <label>
                    <input class="answer" type="<?= $question->listItemType?>" name="answer" value="<?= $answer->id ?>"
                           data-custom="<? if (($answer->custom == 1)) {
                               echo '1';
                           } ?>" data-need_confirm="<? if (($answer->need_confirm == 1)) {
                        echo '1';
                    } ?>"
                        >
                    <?= $answer->text ?>

                    <? //если вариант ответа подразумевает свой вариант, то генерим ещё текстовое поле
                    if (($answer->custom == 1)) {
                        echo Html::textInput('customAnswerText', null, ['id' => 'customAnswerText']);


                    } ?>
                </label>
            </div>
          <?
        }
    }
    ?>
<?
if(isset($question->custom_answer)){
    echo '<div class="col-md-2">';
    echo Html::textInput('customAnswerText', null,
        [
            'id' => 'customAnswerText',
            'class' => 'form-control'
        ]);
    echo '</div>';
}
?>
</div>
    <br><br>
<div class="form-group">

    <?= Html::button('Далее', [
        'data-question-id' => $question->id,
        'class' => 'getNext btn btn-lg btn-success']); ?>

</div>
</form>
</div>

<div id="alertErrorChooseOne" class="alert alert-danger" style="display: none;">Выберите вариант ответа</div>
<div id="alertErrorChooseMultiple" class="alert alert-danger" style="display: none;">Выберите 3 варианта ответа</div>