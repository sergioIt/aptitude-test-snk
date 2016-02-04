<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Добавляет поля для результата анализа на адеквастность и здоровье
 *
 * Class m160204_141611_add_colunms_check_adequacy_check_hekath_to_test
 */
class m160204_141611_add_colunms_check_adequacy_check_hekath_to_test extends Migration
{
    const TEST_TABLE = 'test';
    // столбцы для добавления
    private $columnsToAdd =
        [
            ['name' => 'check_adequacy', 'type' => 'tinyint unsigned'],
            ['name' => 'check_health', 'type' => 'tinyint unsigned'],

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
