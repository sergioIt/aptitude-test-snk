<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 08.02.16
 * Time: 15:13
 *
 * Результат теста - вопроса и ответы по отдельному тесту
 */
//use yii\base\view;
use yii\helpers\Html;
use backend\models\User;
?>
<? if (isset($err)) {
    echo $err;
} ?>

<div id="summary">
    <h3>   Кандидат: <?= $test->getFullUserName();?> </h3>
    <h3>   Оценка: <?= $test->score;?> </h3>
    <h3>   Рекомендация (исходя из общего балла):</h3>
    <?= $test->getRecommendation();?>
    <h3>Статус кандидата: <?= $test->getUserStatusText()?></h3>

</div>
<hr>
<div id="controls">

    <h2> Группы проверочных вопросов</h2>

    <div class="buttons">
    <?
    foreach($checkGroups as $group=>$check){

        echo ' '.Html::button($controlButtons[$group]['text'],
            ['class'=> 'btn_show_group btn btn-'.$controlButtons[$group]['btn_class'][$check],
                //'id' => 'btn_show_check_group_1',
                'title' =>$controlButtons[$group]['title'][$check],
                'data-group' => $controlButtons[$group]['group']
            ]);
    }?>

    <?= Html::button(\common\models\Test::CHECK_GROUP_ADEQUACY_NAME.' ('.$adequacyFailedCount.')',
        ['class'=> 'btn_show_adequacy btn btn_sm btn-'.$controlButtons[\common\models\Test::CHECK_GROUP_ADEQUACY]['btn_class'][$checkAdequacy],
            'title' =>$controlButtons[\common\models\Test::CHECK_GROUP_ADEQUACY]['title'][$checkAdequacy],
          ]);
    ?>

    <?= Html::button(\common\models\Test::CHECK_GROUP_HEALTH_NAME.' ('.$healthFailedCount.')',
        ['class'=> 'btn_show_health btn btn_sm btn-'.$controlButtons[\common\models\Test::CHECK_GROUP_HEALTH]['btn_class'][$checkHealth],
            'title' =>$controlButtons[\common\models\Test::CHECK_GROUP_HEALTH]['title'][$checkHealth],
          ]);
    ?>

    <?= Html::button('показать все',['class'=> 'btn btn_sm btn-primary', 'id' => 'btn_show_all_results']); ?>

    </div>
</div>

<? if (isset($results)) {
    ?>

    <div id="comments_view" class="row">
        <h2>Комментарии</h2>
    <?    if (! empty($test->comments)) {

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
        <br>
    </div>
    <hr>
    <div id="results_view">
    <? foreach ($results as $result){ ?>

       <?
        $check_group = null;
        $check_adequacy = null;
        $check_health = null;
        $unwanted =  $result['answer']['unwanted'];

         if(isset($result['question']['check_group'])){

             $check_group = $result['question']['check_group'];
         }

        if($unwanted == \common\models\Test::UNWANTED_ANSWER_TYPE_FOR_ADEQUACY){

            $check_adequacy = 1;

        }
        if($unwanted == \common\models\Test::UNWANTED_ANSWER_TYPE_FOR_HEALTH){

            $check_health = 1;
        }
        ?>

        <div id="question_<?=$result['question_id']?>"
             data-check_group = "<?=$check_group?>"
             data-check_adequacy = "<?= $check_adequacy ?>"
             data-check_health = "<?= $check_health ?>"
             class="result"
            >
            <h2> Вопрос <?=$result['question_id'] ?>
            <? if(isset($result['question']['check_group'])){

                echo Html::tag('span','проверочная группа '.$check_group, ['class' => 'label label-primary']);
            }

            ?>
            </h2>
                <h3> <?= $result['question']['text']; ?></h3>

                <h2> Ответ:  <?  if(isset($result['answer']['unwanted'])){

                        echo Html::tag('span',' ! ',
                            ['class' => 'label label-sm label-danger',
                                'title' => 'нежелательный ответ по группе вопросов на адекватность']);
                    } ?>
                    </h2>


        <h3>
        <? //если есть вариант ответа выводим его
        if(isset($result['answer']['text']) && $result['question']['multiple_answers'] == 0){

            echo $result['answer']['text'];
//если вариант ответа подразумевает только свой вариант ответа, то выводим его
            if(isset($result['answer']['custom'])){

                echo '<br>'.$result['custom_text'];
            }

        }
        //если вопрос подразумевает шкалы, выводим ответ по шкале
        if(isset($result['scale'])) {
            echo $result['scale'];
        }
        ?>

        <? //если вопрос подразумевает только свой вариант ответа, то выводим его
        if(isset($result['custom'])){

            echo $result['custom_text'];

        }
            if(isset($result['question']['multiple_answers'])){

            echo $result['answers_combined'];

            }
        ?>
        </h3><hr>
        </div>
    <?}
    ?>
    </div>

<?
}