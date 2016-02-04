<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Добавляет поле unwanted (нежелательный ответ)
 * в таблицу ответов
 *
 * Class m160204_101326_add_column_unwanted_to_table_answers
 */
class m160204_101326_add_column_unwanted_to_table_answers extends Migration
{
    const TABLE = 'test_answers';
    private $columnToAdd = ['name' => 'unwanted', 'type' => 'tinyint unsigned'];

    public function up()
    {
        echo 'add column ' . $this->columnToAdd['name'] . ' to table ' .self::TABLE. "\n";

        $this->addColumn(self::TABLE, $this->columnToAdd['name'],$this->columnToAdd['type']);

    }

    public function down()
    {
        echo 'drop column ' . $this->columnToAdd['name'] . ' from table ' .self::TABLE. "\n";
        $this->dropColumn(self::TABLE, $this->columnToAdd['name']);

        return true;
    }


}
