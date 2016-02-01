<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * создаёт таблицу с резульатами тестов (соответствия вопросов и ответов, выбранное пользователем)
 *
 * Class m160201_141917_create_table_test_result
 */
class m160201_141917_create_table_test_result extends Migration
{
    const TABLE_TEST = 'test';
    const TABLE_TEST_RESULT = 'test_result';
    const TABLE_TEST_USER = 'test_user';
    const TABLE_TEST_QUESTION = 'test_questions';


    private $foreignKeyTestResultToTest = [
        'name' => 'fk_test_result_test_id_to_test',
        'table'=>self::TABLE_TEST_RESULT,
        'column' => 'test_id',
        'ref_table' => self::TABLE_TEST,
        'ref_column' => 'id'
    ];

    private $foreignKeyTestResultToQuestion = [
        'name' => 'fk_test_result_question_id_to_question',
        'table'=>self::TABLE_TEST_RESULT,
        'column' => 'question_id',
        'ref_table' => self::TABLE_TEST_QUESTION,
        'ref_column' => 'id'
    ];

    public function up()
    {

    // return true;
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';


        echo 'create table'. self::TABLE_TEST_RESULT ."\n";

        $this->createTable(self::TABLE_TEST_RESULT, [
            'id' => $this->primaryKey()->notNull(),
            // какой тест
            'test_id' => 'integer not null',
            //какой вопрос
            'question_id' => 'integer not null',
            // какой ответ (может и не быть номера, если нет вариантов)
            'answer_id' => 'integer unsigned',
            // свой ответ или нет
            'custom' => 'tinyint(1) UNSIGNED',
            //вопрос со шкалой, то какой ответ (число)
            'scale' => 'smallint UNSIGNED',
            // текст своего ответа
            'custom_text' => $this->string(),

        ], $tableOptions);

        // добавляем внешний ключ на id теста в результатах теста
        $this->addForeignKey(
            $this->foreignKeyTestResultToTest['name'],
            $this->foreignKeyTestResultToTest['table'],
            $this->foreignKeyTestResultToTest['column'],
            $this->foreignKeyTestResultToTest['ref_table'],
            $this->foreignKeyTestResultToTest['ref_column']
        );
        // добавляем внешний ключ на id вороса в результатах теста
        $this->addForeignKey(
            $this->foreignKeyTestResultToQuestion['name'],
            $this->foreignKeyTestResultToQuestion['table'],
            $this->foreignKeyTestResultToQuestion['column'],
            $this->foreignKeyTestResultToQuestion['ref_table'],
            $this->foreignKeyTestResultToQuestion['ref_column']
        );

    }

    public function down()
    {

        echo 'drop foreign key '.  $this->foreignKeyTestResultToTest['name'] ."\n";

      //  $this->dropForeignKey( $this->foreignKeyTestResultToTest['name'], $this->foreignKeyTestResultToTest['table']);

        echo 'drop foreign key '.  $this->foreignKeyTestResultToQuestion['name'] ."\n";

        //$this->dropForeignKey( $this->foreignKeyTestResultToQuestion['name'], $this->foreignKeyTestResultToQuestion['table']);

        echo 'drop table'. self::TABLE_TEST_RESULT ."\n";

        $this->dropTable(self::TABLE_TEST_RESULT);

        return true;

    }

}
