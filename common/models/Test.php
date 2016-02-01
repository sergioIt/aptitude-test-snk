<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 27.01.16
 * Time: 16:52
 */

namespace common\models;

use Yii;
use frontend\models;

/**
 * This is the model class for table "test".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $created
 * @property string $updated
 * @property integer $status
 *
 * @property TestUser $user
 * @property TestResult[] $testResults
 */
class Test extends \yii\db\ActiveRecord
{
    /**
     * статус теста по умолчанмию (когда только создан и ни одного ответа не получено)
     */
    const STATUS_DEFAULT = 0;
    /**
     * статус теста: не закончен
     */
    const STATUS_NOT_FINISHED = 1;
    /**
     * статус тета: закончен
     */
    const STATUS_FINISHED = 2;

    /**
     * статус теста: закончен с отказом (при ответе на последний вопрос)
     */
    const STATUS_FAULT = 3;
    /**
     * фомат даты для валидатора yii
     */
    const DATE_DB_FORMAT_FOR_VALIDATOR = 'yyyy-MM-dd HH:mm:ss';

    /**
     * массив соответствий значения шкалы и оценки овтета
     * @var array
     */
    private static $scaleValues = [
        10 =>
            [0 => -1,
            1 => -1,
            2 => -1,
            3 => -1,
            4 => -1,
            5 => -1,
            6 => 1,
            7 => 1,
            8 => 2,
            9 => 2,
            10 => 2
            ],
        100 =>
            [0 => -1,
                10 => -1,
                20 => -1,
                30 => -1,
                40 => -1,
                50 => -1,
                60 => 1,
                70 => 1,
                80 => 2,
                90 => 2,
                100 => 2,
            ]
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'created',], 'required'],
            // user_id и статус не могут быть null
            [['user_id', 'status'], 'integer', 'integerOnly' => true],
            // общий бал, резульататы проверочных групп могут быть целым число либо null
            [['score', 'check_group_1', 'check_group_2', 'check_group_3'], 'integer',],
            ['created',
                'date',
                'format' => self::DATE_DB_FORMAT_FOR_VALIDATOR
            ],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'created' => 'Создан',
            'updated' => 'Изменён',
            'status' => 'Статус',
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                $this->created = (new \DateTime())->format('Y-m-d H:i:s');
            } else {

                $this->updated = (new \DateTime())->format('Y-m-d H:i:s');
            }

            return true;
        }

        return false;

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(TestUser::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestResults()
    {
        return $this->hasMany(TestResult::className(), ['test_id' => 'id']);
    }

    /**
     * @param $testId
     */
    public static function updateTest($testId)
    {

        $test = self::findOne(['id' => $testId]);

        // находим последний результат для этого теста
        $result = TestResult::find()->where(['test_id' => $testId])->orderBy('id desc')->one();
        // находим последний вопрос
        $lastQuestion = TestQuestions::find()->orderBy('id desc')->one();
        // находим текущий вопрос
        $currentQuestion = TestQuestions::findOne($result->question_id);

        // если текущий вопрос оценивается, то считаем оценку
        if ($currentQuestion->evaluative == 1) {

            // если вопрос по шкале, то особый обход
            if ($currentQuestion->scale > 0) {
                var_dump($currentQuestion->scale);

                var_dump(self::$scaleValues);

                $value = self::$scaleValues[$currentQuestion->scale][$result->scale];

            } // иначе вытаскиваем оценку из ответа
            else {
                $value = TestAnswers::findOne($result->answer_id)->value;
            }

            $test->score += $value;
        }
        //сравниваеим id вопроса из последнего результата и id последнего вопроса
        if ($result->question_id < $lastQuestion->id) {
            $test->status = self::STATUS_NOT_FINISHED;

        } else {

            $test->status = self::STATUS_FINISHED;

            // если тест закончен, и на последний вопрос был получен ответ, требующий подтверждения (отказ кандидата)
            // то ставим статус отказа
            if(TestAnswers::findOne($result->answer_id)->need_confirm > 0){
                $test->status = self::STATUS_FAULT;
            }
            // @todo обработка проверочных вопросов

        }


        $test->save();
    }

    /**
     * Обрабатывает группы проверочных вопросов
     * если тест полсностью завершён
     * @param object $test
     * @re
     */
    private function checkGroup($test){



        return $test;
    }

}