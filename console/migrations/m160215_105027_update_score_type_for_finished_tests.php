<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\Test;
//use yii;
/**
 * Заполяет поле score_type исходя из score (общега набранного балла)
 * только для законченных тестов
 *
 * Class m160215_105027_update_score_type_for_finished_tests
 */
class m160215_105027_update_score_type_for_finished_tests extends Migration
{
    const TEST_TABLE = 'test';

    public function up()
    {
        $testData = Test::find()
            ->select(['id', 'score'])
            ->where(['in','status',[Test::STATUS_FINISHED,Test::STATUS_FAULT]])
            ->asArray()
            ->all();

        foreach($testData as $test){

            $this->update(self::TEST_TABLE, ['score_type' => Test::getScoreTypeByScore($test['score'])],['id' => $test['id']]);
        }

    }

    public function down()
    {
        echo 'clearing column score_type (set null for all rows)' . "\n";
        $this->update(self::TEST_TABLE,['score_type' => null]);

        return true;
    }

}