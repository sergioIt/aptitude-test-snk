<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 12.02.16
 * Time: 17:46
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\models\User;
?>

<h2>Комментарии к тесту</h2>
<div id="comments" class="row">



<?
if (! empty($test->comments)) {

    foreach($test->comments as $comment){
        ?>
        <div class="comment col-md-8">
            <?=
            Html::tag('p',User::findOne(['id' => $comment->user_id])->username, ['class' => 'label label-primary']);
             ?>
            <?=
            Html::tag('p',$comment->created, ['class' => 'label label-primary']);
             ?>

        <?= $comment->text; ?>

        </div>
        <?
    }
}
?>
</div>
<div class="row">

<br><br>

<div class="form-group col-md-4 center-block">
<?=Html::textarea('comment','',['id'=>'comment_field', 'class'=>'form-control','rows'=>4]); ?>
</div>

<div class="form-group">
            <?=  Html::button('Добавить комментарий',
                ['id'=>'btn_add_comment',
                    'data-test_id' => $test->id,
                    'data-user_id' => Yii::$app->getUser()->id,
                    'class' => 'btn btn-success']); ?>
</div>


</div>

    <div id="alert_comment_added" class="alert alert-success">Комментрий добавлен</div>
<?

//var_dump($test);