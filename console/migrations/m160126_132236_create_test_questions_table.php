<?php

//use yii\db\Schema;
use yii\db\Migration;

class m160126_132236_create_test_questions_table extends Migration
{
    const TABLE_QUESTIONS = 'test_questions';

    public function up()
    {
        echo 'create table'. self::TABLE_QUESTIONS ."\n";

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable(self::TABLE_QUESTIONS, [
        'id' => $this->primaryKey()->notNull(),
        //текст вопроса
        'text' => $this->text()->notNull(),
        // оценивается ли вопрос по баллам согласно ключам (1) или просто для информации (0)
        'evaluative' => 'tinyint(1) UNSIGNED NOT NULL DEFAULT 1',
        // подразумевает ли вопрос ответ по предложенной шкале (вопросы 6,9,13)
        // 10 - по шкале от 0 до 10
        //100 - по шкале от 0 до 100
        'scale' => 'smallint UNSIGNED',
        // к какой группе контрольных вопросов принадлежит,
        // null - не принадлежит к группе
        'check_group' => 'tinyint UNSIGNED',
        // вопрос подразумевает несколько вариантов ответа, если
        // null - один вариант ответа
        'multiple_answers' => 'tinyint(1) UNSIGNED',
        // если multiple - не null, то эти поля тоже должны быть заполнены
        // минимальное число варинтов ответа
        'min_options' => 'tinyint UNSIGNED',
        //максимальное число вариантов ответа
        'max_options' => 'tinyint UNSIGNED',
        // вопрос подразумеват толькой свой ответ в текстовом поле (1)
        // по умолчанию этого нет (null)
        'custom_answer' => 'tinyint UNSIGNED',
         // обязателен ответ или нет, по умолчанию ответ обязательный (1)
         'required' => 'tinyint UNSIGNED NOT NULL DEFAULT 1'

    ], $tableOptions);

    }

    public function down()
    {
        echo 'drop table'. self::TABLE_QUESTIONS ."\n";

        $this->dropTable(self::TABLE_QUESTIONS);
    }

}
