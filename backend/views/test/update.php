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

<h2 id="comments_toggle">Комментарии к тесту</h2>

<div id="comments">
<div class="row">


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
                    'class' => 'btn btn-success',
                    'data-url' => \yii::$app->getUrlManager()->createUrl('test/savecomment')

                ]); ?>
</div>


</div>
    </div>




<h2>Статус кандидата</h2>

<div id="user_status_set">

    <div class="form-group">

        <?= Html::dropDownList('statuses',$test->user->status,$userStatuses,['id'=>'test_user_status_dropdown']); ?>
        <?=  Html::button('изменить статус',
            ['id'=>'btn_update_status',
              //  'data-test_id' => $test->id,
                'data-user_id' => $test->user->id,
                'class' => 'btn btn-sm btn-success',
                'data-url' => \yii::$app->getUrlManager()->createUrl('test/updateuserstatus')
            ]

        ); ?>
</div>

</div>
<div id="alert_comment_added" class="alert alert-success">Комментрий добавлен</div>
<div id="alert_status_changed" class="alert alert-success">Статус кандидата изменён</div>
