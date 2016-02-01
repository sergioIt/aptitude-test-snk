<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 27.01.16
 * Time: 17:00
 */

namespace common\models;

use Yii;
use yii\helpers\Html;
/**
 * This is the model class for table "test_result".
 *
 * @property integer $id
 * @property integer $test_id
 * @property integer $question_id
 * @property integer $answer_id
 * @property integer $custom
 * @property integer $scale
 * @property string $custom_text
 *
 * @property TestQuestions $question
 * @property Test $test
 */
class TestResult extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test_result';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['test_id', 'question_id'], 'required'],
            [['test_id', 'question_id', 'answer_id', 'custom', 'scale'], 'integer'],
            [['custom_text'], 'string', 'max' => 255]
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
            'question_id' => 'Question ID',
            'answer_id' => 'Answer ID',
            'custom' => 'Custom',
            'scale' => 'Scale',
            'custom_text' => 'Custom Text',
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
     * @return \yii\db\ActiveQuery
     */
    public function getTest()
    {
        return $this->hasOne(Test::className(), ['id' => 'test_id']);
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                // чтобы в базе сохранялось именно null, а не пустая строка
                if (strlen($this->custom_text) == 0) {
                    $this->custom_text = null;
                }

            } else {

            }

            return true;
        }

        return false;
    }

    /**
     * Сохраняет один или несколько вараинтов ответа
     * @param $data - массив данных, пришёдших от ajax через post
     * @return bool всё сохранено или нет
     */
    public static function saveBatch($data){

        $saved = true;

        // если есть одновременно несклько вариантов ответа, то сохраянем несклько записей резульатов
        if(! empty($data['checkbox_answers'])){

            foreach($data['checkbox_answers'] as $answerId){

                $result = new TestResult();
                $result->test_id = $data['test_id'];
                $result->question_id = $data['question_id'];
                $result->answer_id = $answerId;

                if(! $result->save()){
                    $saved = false;
                }

            }

        }
        //иначе - сохраняем одну запись с одним ответом
        else{
            $result = new TestResult();
            $result->test_id = $data['test_id'];
            $result->question_id = $data['question_id'];
            $result->answer_id = $data['answer_id'];
            $result->scale = $data['scale'];
            $result->custom = $data['custom'];
            $result->custom_text = Html::encode($data['custom_text']);
            if(! $result->save()){
                $saved = false;
            }
        }

        return $saved;
    }
}