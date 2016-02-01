<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 29.01.16
 * Time: 14:50
 */

?>

<div class="jumbotron">
    Спасибо!
</div>

<? //@todo если человек попросил снять его кандидатуру в последнем вопросе, то дополнительно рендерим ещё формчоку для причины отказа
if($denied){
    \yii\helpers\Html::textarea('deny_reason');
}

    ?>