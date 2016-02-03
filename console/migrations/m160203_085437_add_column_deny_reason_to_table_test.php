<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Добавляет текстовое поле deny_reason для записи ответа на допю вопрос
 * (причина отказа, после теста)
 *
 * Class m160203_085437_add_column_deny_reason_to_table_test
 */
class m160203_085437_add_column_deny_reason_to_table_test extends Migration
{
    const TEST_TABLE = 'test';
    private $columnToAdd = ['name' => 'deny_reason'];

    public function up()
    {
        echo 'add column ' . $this->columnToAdd['name'] . ' to table ' .self::TEST_TABLE. "\n";

        $this->addColumn(self::TEST_TABLE, $this->columnToAdd['name'],$this->string(500));
    }

    public function down()
    {
        echo 'drop column ' . $this->columnToAdd['name'] . ' from table ' .self::TEST_TABLE. "\n";
        $this->dropColumn(self::TEST_TABLE, $this->columnToAdd['name']);

        return true;
    }

}
