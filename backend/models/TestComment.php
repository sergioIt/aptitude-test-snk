<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 15.02.16
 * Time: 16:05
 */

namespace backend\models;

use common\models\Test;

use Yii;

/**
 * This is the model class for table "test_comment".
 *
 * @property integer $id
 * @property integer $test_id
 * @property integer $user_id
 * @property string $text
 * @property string $created
 *
 * @property Test $test
 * @property User $user
 */
class TestComment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['test_id', 'user_id', 'text',], 'required'],
            [['test_id', 'user_id'], 'integer', 'integerOnly' => true],
            [['text'], 'string'],
            ['created',
                'date',
                'format' => Test::DATE_DB_FORMAT_FOR_VALIDATOR
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'test_id' => 'Test ID',
            'user_id' => 'User ID',
            'text' => 'Text',
            'created' => 'Created',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTest()
    {
        return $this->hasOne(Test::className(), ['id' => 'test_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function beforeSave($insert){

        if (parent::beforeSave($insert)) {

            $date = new \DateTime();
            $date->setTimezone( new \DateTimeZone(Test::TIMEZONE));

            if ($this->isNewRecord) {
                $this->created =$date->format('Y-m-d H:i:s');
            }

            return true;
        }

        return false;

    }
}