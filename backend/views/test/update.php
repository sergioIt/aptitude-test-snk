<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 12.02.16
 * Time: 17:46
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$form = ActiveForm::begin([
    'action'=>'save',
]);

 echo $form->field($model,'id');
 echo $form->field($model,'status');
?>
<div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-lg btn-primary']) ?>
</div>
<?
ActiveForm::end();

//var_dump($model);