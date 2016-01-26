<?php

use yii\db\Schema;
use yii\db\Migration;

class m160126_141557_create_tests_answers_table extends Migration
{

    const TABLE_ANSWERS = 'test_answers';
    const TABLE_QUESTIONS = 'test_questions';

    private $indexUniqText = ['name'=> 'unique_text', 'table' => self::TABLE_ANSWERS, 'columns' => ['question_id','text'], 'uniq' => true];

    public $foreignKey = ['name'=>'fk_answer_question_id_to_questions',
        'table'=>self::TABLE_ANSWERS,
        'column' => 'question_id',
        'ref_table' => self::TABLE_QUESTIONS,
        'ref_column' => 'id'
    ];


    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable(self::TABLE_ANSWERS, [
            'id' => $this->primaryKey()->notNull(),
            //id воропса
            'question_id' => $this->integer()->notNull(),
            // текст варианта ответа
            'text' => $this->string()->notNull(),
            'value' => 'tinyint(1)',
            // вариант ответа подразумевает свой вариант
            'custom' => 'tinyint(1) UNSIGNED',
            // требует ли ответ подтверждения
            'need_confirm' => 'tinyint(1) UNSIGNED'

        ], $tableOptions);

        // добавляем внешний ключ на id вопроса
        $this->addForeignKey(
            $this->foreignKey['name'],
            $this->foreignKey['table'],
            $this->foreignKey['column'],
            $this->foreignKey['ref_table'],
            $this->foreignKey['ref_column']
            );

        $this->createIndex($this->indexUniqText['name'],
            $this->indexUniqText['table'],
            $this->indexUniqText['columns'],
            $this->indexUniqText['uniq']);
    }

    public function down()
    {
        echo 'drop foreign key '.  $this->foreignKey['name'] ."\n";

        $this->dropForeignKey( $this->foreignKey['name'], $this->foreignKey['table']);

        $this->dropIndex($this->indexUniqText['name'], $this->indexUniqText['table']);

        echo 'drop table'. self::TABLE_ANSWERS ."\n";

        $this->dropTable(self::TABLE_ANSWERS);
    }


}
