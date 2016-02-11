<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 25.01.16
 * Time: 18:41
 *
 * начала тестирования: форма для ввода контактных данных
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->registerCssFile('/css/test.css');
$this->registerJsFile('plugins/inputmask/jquery.inputmask.bundle.js',
    [ 'depends' => ['\yii\web\JqueryAsset'],
     'position' => \yii\web\View::POS_END, ]
    );
$this->registerJsFile('js/app.js', [ 'depends' => ['\yii\web\JqueryAsset'],
     'position' => \yii\web\View::POS_END, ]
    );
$this->title = 'Начало теста';
?>
<div class="container">


<div class="jumbotron" id="intro">
    <h1>Вас приветствует команда компании ЗАО “СНК”! </h1>

    <h2> Сейчас Вам предстоит пройти входное тестирование на должность сварщика термитной сварки</h2>
    <h2> Просим Вас отвечать на вопросы теста вдумчиво и честно.<br>
        Прежде всего, для самих себя.
        <br>Нам в команду нужны люди, которые хотят
        работать и пришли к нам надолго. Верим, что и Вы пришли с той же целью.

    </h2>
        <h2> Удачи! </h2>
    <p>
        <br>
        <a class="btn btn-primary btn-lg" href="#" role="button" id="btn_begin_test">Начать тест</a>
    </p>

</div>

    <div class="begin-test-contact" id="begin-test-form">
    <h1><?= Html::encode($this->title) ?></h1>


    <!--  <p>
          If you have business inquiries or other questions, please fill out the following form to contact us. Thank you.
      </p>-->

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'name', [

            ]) ?>
            <?= $form->field($model, 'surname') ?>
            <?= $form->field($model, 'patronymic') ?>
            <?= $form->field($model, 'phone')->textInput(['class' => 'input_phone']) ?>
            <?= $form->field($model, 'date_of_birth')->widget(\yii\jui\DatePicker::classname(),[
                'language' => 'ru',
                'dateFormat' => 'yyyy-MM-dd',
                'inline' => false,
                'clientOptions' => [
                'changeMonth' => true,
                'yearRange' => '1925:2005',
                'changeYear' => true,
                'showOn' => 'button',
                //'buttonImage' => 'images/calendar.gif',
                'buttonImageOnly' => false,
                'buttonText' => 'Выберите дату',

            ],
            ])/*->textInput(['value' => '1998-12-02'])*/;
            ?>
            <div class="form-group">
                <?= Html::submitButton('Начать тест', ['class' => 'btn btn-lg btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

    </div>