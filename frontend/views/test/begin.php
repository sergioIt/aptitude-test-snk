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
$this->registerCssFile('css/test');
?>

<div class="site-contact">
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
            <?= $form->field($model, 'phone') ?>
            <?= $form->field($model, 'date_of_birth')->widget(\yii\jui\DatePicker::classname(),[
                'language' => 'ru',
                'dateFormat' => 'yyyy-MM-dd',
                //'value' => '20.01.1998'
            ]) ?>
            <div class="form-group">
                <?= Html::submitButton('Начать тест', ['class' => 'btn btn-lg btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>