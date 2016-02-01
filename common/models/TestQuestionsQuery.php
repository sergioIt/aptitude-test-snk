<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 27.01.16
 * Time: 15:43
 */

namespace common\models;

/**
 * This is the ActiveQuery class for [[TestAnswers]].
 *
 * @see TestAnswers
 */
class TestQuestionsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return TestAnswers[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TestAnswers|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}