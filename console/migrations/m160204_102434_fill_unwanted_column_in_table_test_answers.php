<?php

//use yii\db\Schema;
use yii\db\Migration;
use common\models\TestAnswers;

/**
 * Заполняет значение признака, что ответ не желательный
 * Это нужно было сделать при изначальном заполнении ответов, но теперь уже дело д
 * Class m160204_102434_fill_unwanted_column_in_table_test_answers
 */
class m160204_102434_fill_unwanted_column_in_table_test_answers extends Migration
{
    const ANSWERS_TABLE = 'test_answers';
    // id ответов, коотрые помечаются как нежелательные
    private $answersToUpdate = [
        TestAnswers::TYPE_UNWANTED_BY_JOKE =>
            [11, 21, 30, 36, 37, 43, 47, 52, 68, 108, 112, 124, 132, 155, 177, 188, 189],
        TestAnswers::TYPE_UNWANTED_BY_DISEASE =>
            [
                69, 76, 82, 86, 91, 93, 97, 113
            ]
    ];

    private $columnToUpdate = ['name' => 'unwanted'];

    public function up()
    {
        echo 'update column ' . $this->columnToUpdate['name'] . ' in table ' .self::ANSWERS_TABLE. "\n";

        foreach ($this->answersToUpdate as $type => $range) {

            $this->update(self::ANSWERS_TABLE,[$this->columnToUpdate['name']=>$type],['in','id',$range]);
        }

    }

    public function down()
    {

        $this->update(self::ANSWERS_TABLE,[$this->columnToUpdate['name']=>null]);

        return true;

    }


}
