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

use Monolog\Logger;
use Monolog\Handler\SwiftMailerHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ChromePHPHandler;

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
     * Связь с вопросом
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(TestQuestions::className(), ['id' => 'question_id']);
    }

    /**
     * Связь с ответом
     * @return \yii\db\ActiveQuery
     */
    public function getAnswer()
    {
        return $this->hasOne(TestAnswers::className(), ['id' => 'answer_id']);
    }

    /**
     * Связь с тестом
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

                    $logger = new Logger('aptitude-frontend');

                    $logger->pushHandler(new StreamHandler(__DIR__.'/../../frontend/runtime/logs/critical.log', Logger::CRITICAL));
                    $logger->pushHandler(new ChromePHPHandler());

                    $logger->critical('test result save fails',$result->errors);
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

    /**
     * Компонует массив резульатов: группирует ответы, на вопросы, подразумеваеющие несколько вариантов ответа одноверменно
     * и удаляет дубли из массива резульататов, получающиеся из-за того,
     * что каждый ответ хранится отдельно, даже если в случае когда несколько ответов на один вопрос
     *
     * @param array $results исходный массив результатов
     * @return array $results обработанный массив результатов
     */
    public static function composeAjaxOutput($results){

        // собираем ответы на вопрос, предлагающий несколько вариантов овтета
        $answersCombined = [];
        //массив соответствий номера вопроса и id резульатов, которые нужно исключить во view
        // т.к. они дублируются из-за структуры хранения ответов

        $keysToUnset = [];
        foreach($results as $key=>$result){

            if(isset($result['question']['multiple_answers'])){

                $answersCombined[$result['question_id']][] = $result['answer']['text'];
                $keysToUnset[$result['question_id']][] = $key;
            }

        }
        // удаляем 1-ый элемент массива ключей для элеметнтов, которые нужно убрать из массива результатов
        // чтобы убрать только дубли
        foreach ($keysToUnset as &$array) {
            array_shift($array);
        }
        // удаляем дубли в массиве вопросов и ответов
        foreach ($keysToUnset as $questionKeysToUnSet) {

            foreach($questionKeysToUnSet as $questionKeyToUnSet){

                unset($results[$questionKeyToUnSet]);
            }
        }
        //стыкуем варианты ответов, к массиву результатов
        foreach($results as &$result){

            if(isset($answersCombined[$result['question_id']])){

                $result['answers_combined'] = implode(',',$answersCombined[$result['question_id']]);
            }
        }

        return $results;
    }


}