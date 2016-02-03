<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 29.01.16
 * Time: 14:50
 */
use yii\helpers\Html;
?>
<div class="jumbotron">
 <h1>   Спасибо за пройденное тестирование! </h1>

    <?= \yii\helpers\Html::a('в начало',$originUrl,['class' => 'btn btn-success','id' => 'btn_go_to_begin']); ?>

</div>

<? //@todo если человек попросил снять его кандидатуру в последнем вопросе, то дополнительно рендерим ещё формчоку для причины отказа
//var_dump($additionalQuestion);
if($additionalQuestion){
?>
<div class="question row" id="questionBlock"
     data-test_id="<?= $testId ?>"
    >
<h2>

Пожалуйста, укажите причину Вашего отказа участвовать в конкурсе на место сварщика термитной сварки
    <small> (необязательно) </small>
</h2>
</div>
    <?= Html::beginForm('','post',['class'=>'answer', 'id'=>'additionalForm']) ?>
        <div class="row">
        <div class="form-group col-md-4 center-block">
<?=Html::textarea('reasonDeny','',['id'=>'deny_reason', 'class'=>'form-control','rows'=>4]); ?>
</div>
        </div>
        <div class="row">
        <div class="form-group">
            <?=  Html::button('Отправить',['id'=>'btn_send_reason', 'class' => 'btn btn-lg btn-success']); ?>
           </div>
        </div>
    <?= Html::endForm() ?>
<?} ?>