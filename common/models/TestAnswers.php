<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 27.01.16
 * Time: 15:42
 */

namespace common\models;

use Yii;

/**
 * This is the model class for table "test_answers".
 *
 * @property integer $id
 * @property integer $question_id
 * @property string $text
 * @property integer $value
 * @property integer $custom
 *
 * @property TestQuestions $question
 */
class TestAnswers extends \yii\db\ActiveRecord
{
    /**
     * Тип нежелательного ответа: ответ в шутку
     */
    const TYPE_UNWANTED_BY_JOKE = 1;
    /**
     * Тип нежелательного ответа: склонность болеть
     */
    const TYPE_UNWANTED_BY_DISEASE = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test_answers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'text'], 'required'],
            [['question_id', 'value', 'custom', 'need_confirm','unwanted'], 'integer'],
            [['text'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_id' => 'ID вопроса',
            'text' => 'Текст ответа',
            'value' => 'Оценка',
            'custom' => 'Свой ответ',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(TestQuestions::className(), ['id' => 'question_id']);
    }

    /**
     * @inheritdoc
     * @return TestQuestionsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TestQuestionsQuery(get_called_class());
    }
}