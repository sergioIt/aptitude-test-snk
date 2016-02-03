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
     * Разниа в баллах между проверочными вопросами, при которой считаем, что что-то не так
     */
    const CHECK_CRITERIA_DIFFERENCE = 2;

    /**
     * статус итогоа проверки 1-ой группы вопросов: пройдена
     */
    const STATUS_CHECK_GROUP_TRUE = 1;
    /**
     * статус итогоа проверки 1-ой группы вопросов: подозрение на ложе
     */
    const STATUS_CHECK_GROUP_FALSE = 2;


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
            ['deny_reason', 'string', 'max' => 500],
            ['deny_reason', 'trim']

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
            'deny_reason' => 'Причина отказа',
            'score' => 'Общий балл',
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
     * Получает ответы на вопросы, которые входят в проверочную группу
     * @param int $groupId номер проверочной группы
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getTestResultsByCheckGroup($groupId)
    {

        return TestResult::find()
            ->select(['test_questions.scale as fullScale', //величина шкалы
                'ans.value',    // вес ответа в баллах
                'test_result.id',   // id резульатата
                'test_result.question_id',  // id вопроса
                'test_result.answer_id',    // id ответа
                'test_result.scale'])// выбранное значние шкалы
            ->joinWith('question', false)
            ->leftJoin('test_answers as ans', 'test_result.answer_id = ans.id')
            ->where(['test_id' => $this->id, 'check_group' => $groupId])
            ->asArray()
            ->all();
    }

    /**
     * Обновляет данные теста:
     * сколько баллов набрано
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
            if (TestAnswers::findOne($result->answer_id)->need_confirm > 0) {
                $test->status = self::STATUS_FAULT;
            }
            // обработка проверочных вопросов
            $test->processCheckGroups(1);
            $test->processCheckGroups(2);
            $test->processCheckGroups(3);

        }

        $test->save();
    }

    /**
     * Проверяет, был ли отказ в последнем вопросе теста
     * от этого зависит, показывать ли дополнительный вопрос
     *
     * @param $testId
     * @return bool
     */
    public static function ifTestIsFault($testId){
        $test = self::findOne(['id' => $testId]);

        if($test->status == self::STATUS_FAULT){

            return true;
        }
        return false;
    }
    /**
     * Проверяет ответы на вопросы в проверочной группе
     */
    private function processCheckGroups($groupId)
    {

        $results = $this->getTestResultsByCheckGroup($groupId);

        switch ($groupId) {
            // обработка 1-ой группы проверочных вопросов
            case 1:
                // по умлочнанию считаем, что проверка пройдена
                // и только если нарушится критерий оценки, то меняем статус
                $this->check_group_1 = self::STATUS_CHECK_GROUP_TRUE;
                $values = [];
                $scaleValue = 0;
                foreach ($results as $result) {

                    if (isset($result['value'])) {

                        $values[] = $result['value'];
                    }
                    // если у вопроса есть шкала, то определяем значение по шкале
                    if (isset($result['fullScale'])) {

                        $scaleValue = self::$scaleValues[$result['fullScale']][$result['scale']];
                    }
                }
                // далее сравниваем значение по шкале (вопрос 7) с двумя другими вопросами (6,18)
                foreach ($values as $value) {
                    // если разница в баллах слишком велика по критерию, то считаем, что проверка пройдена
                    // с результатот "подозорение на ложь"
                    if (abs($scaleValue - $value) > self::CHECK_CRITERIA_DIFFERENCE) {
                        $this->check_group_1 = self::STATUS_CHECK_GROUP_FALSE;
                    }
                }

                break;
            // обработка 2-ой группы проверочных вопросов
            case 2:
               // echo 'group 2';
                $values = [];

                foreach ($results as $result) {

                    $values[] = $result['value'];
                }
                // в этой группе вопросов ответы должны совпадать
                // то есть все ответы имеют равный вес, а значит их экстремальные значения тоже должны совпадать
                if (min($values) == max($values)) {
                    $this->check_group_2 = self::STATUS_CHECK_GROUP_TRUE;
                } else {
                    $this->check_group_2 = self::STATUS_CHECK_GROUP_FALSE;
                }

                break;
            // обработка 3-ей группы проверочных вопросов
            case 3:
                //echo 'group 3';
                $this->check_group_3 = self::STATUS_CHECK_GROUP_TRUE;
                $values = [];
                foreach ($results as $result) {

                    $values[] = $result['value'];
                }

                $min = min($values);
                $max = max($values);

                if (abs($max - $min) > self::CHECK_CRITERIA_DIFFERENCE) {
                    $this->check_group_3 = self::STATUS_CHECK_GROUP_FALSE;
                }
                break;
        }

    }

    /**
     * Сохраняет причину отказа после прохождения теста
     *
     * @param $data
     * @return bool
     */
    public static function saveDenyReason($data){

        $test = self::findOne(['id' => $data['test_id']]);

        if($test){
           $test->deny_reason =   $data['deny_reason'];
            return $test->save();
        }

        return false;
    }


}