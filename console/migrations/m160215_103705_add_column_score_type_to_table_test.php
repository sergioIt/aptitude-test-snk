<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Добавляет вычисляемое поле score_type в таблицу test
 * делается это для того, чтобы заработала сортировка в gridView по этому полю
 *
 * Class m160215_103705_add_column_score_type_to_table_test
 */
class m160215_103705_add_column_score_type_to_table_test extends Migration
{
    const TEST_TABLE = 'test';
    // столбцы для добавления: тип оценки теста (по которой выстраивается рекомендация)
    private $columnsToAdd =
        [
            ['name' => 'score_type', 'type' => "tinyint unsigned not null default '0' "],

        ];

    public function up()
    {
        foreach($this->columnsToAdd as $column)
        {
            echo 'add column ' . $column['name'] . ' to table ' .self::TEST_TABLE. "\n";

            $this->addColumn(self::TEST_TABLE, $column['name'],$column['type']);
        }


    }

    public function down()
    {
        foreach($this->columnsToAdd as $column)
        {
            echo 'drop column ' . $column['name'] . ' from table ' .self::TEST_TABLE. "\n";

            $this->dropColumn(self::TEST_TABLE, $column['name']);
        }

    }


}
