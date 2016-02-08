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

?>
<? if (isset($err)) {
    echo $err;
} ?>
<? if (isset($results)) {
    ?>

    <h4> Результаты теста #<?=$test->id?></h4>
    <? foreach ($results as $result){ ?>

  <h2> Вопрос <?=$result['question_id'] ?> </h2>
       <h3> <?= $result['question']['text']; ?></h3>


    <h2> Ответ: </h2>
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
    <?}?>

<!--    --><?// var_dump($results); ?>
<?
}
?>



