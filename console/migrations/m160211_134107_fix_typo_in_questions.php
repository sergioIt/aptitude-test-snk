<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m160211_134107_fix_typo_in_questions
 *
 * исправляет опечатки в вопросах и ответах
 */
class m160211_134107_fix_typo_in_questions extends Migration
{
    const TABLE_QUESTIONS = 'test_questions';
    const TABLE_ANSWERS = 'test_answers';

    private $questionFixes = [
        ['id' => 23, 'text' => 'Как Вы себя чувствуете во время длительных переездов на автомобиле,
находясь за рулем?'],
        ['id' => 24, 'text' => 'Как Вы себя чувствуете во время длительных переездов на автомобиле,
находясь на месте пассажира?'],
        ['id' => 30, 'text' => 'Представьте ситуацию.<br>
Вы с бригадой приехали на место. Только что выдержали тяжелейший переезд,
устали, голодные. Приезжаете, начинаете распаковывать вещи, кто-то готовит
еду, кто-то в душ идет.. И вдруг бригадиру звонят и сообщают,
что вы все трое срочно переезжаете на другое место.
<br>Как Вы себя при этом чувствуете? Что будете делать?'],

    ];

    private $answerFixes =[

        ['id' => 150, 'text' => 'не конфликтный' ]
    ];

    public function up()
    {
        echo 'update text in table ' .self::TABLE_QUESTIONS. "\n";

        foreach ($this->questionFixes as $questionFix) {

            $this->update(self::TABLE_QUESTIONS,['text'=>$questionFix['text']],['id'=>$questionFix['id']]);
        }
        echo 'update text in table ' .self::TABLE_ANSWERS. "\n";

        foreach ($this->answerFixes as $answerFix) {

            $this->update(self::TABLE_ANSWERS,['text'=>$answerFix['text']],['id'=>$answerFix['id']]);
        }

    }

    public function down()
    {
        echo "m160211_134107_fix_typo_in_questions cannot be reverted.\n";

        return false;
    }

}
