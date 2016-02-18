<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 27.01.16
 * Time: 16:52
 */

namespace common\models;

use Monolog\Handler\PHPConsoleHandler;
use Yii;
use frontend\models;
use backend\models\TestComment;

use yii\data\ActiveDataProvider;

use Monolog\Logger;
//use Monolog\Handler\SwiftMailerHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ChromePHPHandler;


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
     * критическое колчесво нежелательных ответов по признаку "адекватность"
     */
    const LIMIT_UNWANTED_ANSWERS_CRITERIA_ADEQUACY = 2;
    /**
     * критическое колчесво нежелательных ответов по признаку "здоровье"
     */
    const LIMIT_UNWANTED_ANSWERS_CRITERIA_HEALTH = 2;

    /**
     * статус итогоа проверки группы вопросов: пройдена успешно
     */
    const STATUS_CHECK_GROUP_TRUE = 1;
    /**
     * статус итогоа проверки группы вопросов: подозрение на ложь
     */
    const STATUS_CHECK_GROUP_FALSE = 2;
    /**
     *  неопределённый статус проверки группы вопросов
     */
    const STATUS_CHECK_GROUP_UNDEFINED = 0;

    const MIN_POSSIBLE_SCORE = -76;

    const MAX_POSSIBLE_SCORE = 84;

    // типы людей исходя из общего балла
    // "не подоходит"
    const SCORE_TYPE_BAD = 1;
    // "сомневающийся"
    const SCORE_TYPE_DOUBTER = 2;
    // склонен к сомневающимуся
    const SCORE_TYPE_INCLINED_TO_DOUBT = 3;
    // "подходит"
    const SCORE_TYPE_GOOD = 4;
    // "хитрый" (слишком правильно ответил)
    const SCORE_TYPE_CRAFTY = 5;
    // специальный тип на случай, если проверка ещё не проводилась
    const SCORE_TYPE_UNDEFINED = 0;
    // временная зона для сохранения дат
    const TIMEZONE = 'Europe/Moscow';
    // номер первой проверочной группы
    const CHECK_GROUP_1 = 1;
    const CHECK_GROUP_1_NAME = 'группа 1';
    // номер второй проверочной группы
    const CHECK_GROUP_2 = 2;
    const CHECK_GROUP_2_NAME = 'группа 2';
    //номер третьей проверочной группы
    const CHECK_GROUP_3 = 3;
    const CHECK_GROUP_3_NAME = 'группа 3';
    // номер четвёртой проверочной группы (на нежелательные ответы)
    const CHECK_GROUP_ADEQUACY = 4;
    const CHECK_GROUP_ADEQUACY_NAME = 'адекватность';
    // номер пятой проверочной группы (на нежелательные ответы)
    const CHECK_GROUP_HEALTH = 5;
    const CHECK_GROUP_HEALTH_NAME = 'здоровье';

    /**
     * значение поля unwanted для ответа, который участвует в анализе на адекватность
     */
    const UNWANTED_ANSWER_TYPE_FOR_ADEQUACY = 1;
    /**
     * значение поля unwanted для ответа, который участвует в анализе на здоровье
     */
    const UNWANTED_ANSWER_TYPE_FOR_HEALTH = 2;

    /**
     * минимально допустимая продолжительность прохождениея теста
     * если меньше, то появится предпреждение
     */
    const TEST_DURATION_MINIMUM_MINUTES = 8;

    /**
     * Статус проверки теста на продолжительность: нормальный
     */
    const TEST_DURATION_STATUS_CHECK_OK = 1;
    /**
     * Статус проверки теста на продолжительность: не проводилась
     */
    const TEST_DURATION_STATUS_EMPTY = 0;
    /**
     * Статус проверки теста на продолжительность: слишком быстро
     */
    const TEST_DURATION_STATUS_CHECK_WARNING = 2;

    /**
     * массив соответствий границ, в которые попал общий балл,
     * и типов людей
     *
     * @var array
     */
    private static $scoreTypes = [
        self::SCORE_TYPE_BAD => ['min' => self::MIN_POSSIBLE_SCORE, 'max' => -22],
        self::SCORE_TYPE_DOUBTER => ['min' => -23, 'max' => 32],
        self::SCORE_TYPE_INCLINED_TO_DOUBT => ['min' => 33, 'max' => 40],
        self::SCORE_TYPE_GOOD => ['min' => 41, 'max' => 80],
        self::SCORE_TYPE_CRAFTY => ['min' => 81, 'max' => self::MAX_POSSIBLE_SCORE],
    ];

    /**
     * Все проверочные группы
     * @var array
     */
    private static $checkGroups = [
        self::CHECK_GROUP_1,
        self::CHECK_GROUP_2,
        self::CHECK_GROUP_3,
    ];

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
     * Рекомендации в завимисмости от типа кандидата (тип вычисляется по общему кол-ву баллов)
     *
     * @var array
     */
    private static $recommendationsByScoreType = [

        self::SCORE_TYPE_UNDEFINED => 'тип кандидата не определён, рекомендация по типа не определена (возможно, тест не закончен)',
        self::SCORE_TYPE_BAD => 'отказать в рассмотрении данной кандидатуры на место',
        self::SCORE_TYPE_DOUBTER => 'Данный кандидат, похоже, еще не совсем определился, нужна ему эта работа или нет.
        Рекомендация к более пристальному и внимательному разговору с данным кандидатом по телефону или во время очного интервью.
        Более внимательное отношение к деталям предстоящей работы, описание всех сложностей, с которыми придется
столкнуться в процессе работы сварщиком. Несколько раз “в упор” спросить о готовности,
поговорить об ответственности сторон и дать время подумать,
взвесить решение”. При прочих равных условиях отдать предпочтение кандидату, набравшему больше баллов.',
        self::SCORE_TYPE_INCLINED_TO_DOUBT => 'Кандидат скорее
тяготеет к “сомневающемуся типу”, - соответственно, от него можно ожидатьизменения решения в ту или иную сторону в любой момент. Если есть
возможность, отложите рассмотрение данной кандидатуры на время, не
принимайте окончательное решение по нему. Рассмотрите более пристально
кандидатов, набравших более 40 баллов',
        self::SCORE_TYPE_GOOD => 'данные результаты прохождения теста говорят о
желании и готовности данного кандидата работать в ЗАО “СНК” на должности
сварщика термитной сварки',
        self::SCORE_TYPE_CRAFTY => 'Внимание! Кандидат набрал максимальное количество баллов по тесту.
        Стоит приглядеться к нему повнимательнее, возможно, что его хитрость проявится позднее и в других
ситуациях в работе!',

    ];
    /**
     * @var array
     */
    private static $recommendationsByGroupCheck = [

        self::CHECK_GROUP_1 => [
            self::STATUS_CHECK_GROUP_UNDEFINED => 'проверка не проводилась',
            self::STATUS_CHECK_GROUP_FALSE => 'подозрение на ложь',
            self::STATUS_CHECK_GROUP_TRUE => 'нет подозрений на ложь',
        ],
        self::CHECK_GROUP_2 => [
            self::STATUS_CHECK_GROUP_UNDEFINED => 'проверка не проводилась',
            self::STATUS_CHECK_GROUP_FALSE => 'подозрение на ложь',
            self::STATUS_CHECK_GROUP_TRUE => 'нет подозрений на ложь',
        ],
        self::CHECK_GROUP_3 => [
            self::STATUS_CHECK_GROUP_UNDEFINED => 'проверка не проводилась',
            self::STATUS_CHECK_GROUP_FALSE => 'подозрение на проблемы с неопределённостью',
            self::STATUS_CHECK_GROUP_TRUE => 'нет подозрений на проблемы с неопределённостью',
        ],
    ];

    /**
     * Рекомендауции по результатам проверки на адекватность по нежелательным вопросам
     *
     * @var array
     */
    private static $recommendationsByAdequacyCheck = [

        self::STATUS_CHECK_GROUP_UNDEFINED => 'проверка не проводилась',
        self::STATUS_CHECK_GROUP_TRUE => 'проверка на адекватность пройдена (не более 3 нежелательных ответов)',
        self::STATUS_CHECK_GROUP_FALSE => 'Возможно, не следует всерьез рассматривать данного кандидата на
должность сварщика термитной сварки. В противном случае у него стоит
уточнить, сам ли он заполнял тест и насколько честно (не в шутку ли) выбирал
тот или иной вариант ответа. А также снова рассказать ему о тех сложностях,
что его ожидают и снова спросить о его готовности во всем этом участвовать',

    ];
    /**
     * Рекомендауции по результатам проверки на здоровье по нежелательным вопросам
     *
     * @var array
     */
    private static $recommendationsByHealthCheck = [

        self::STATUS_CHECK_GROUP_UNDEFINED => 'проверка не проводилась',
        self::STATUS_CHECK_GROUP_TRUE => 'проверка на адекватность пройдена (не более 3 нежелательных ответов)',
        self::STATUS_CHECK_GROUP_FALSE => 'Внимание!
Следует задать вопрос кандидату по его здоровью, уточнение информации по
самочувствию во время переездов на любом виде транспорта. Рекомендуется
индивидуальный разговор по предстоящим переездам при очном собеседовании
или по телефону',

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
            [['score', 'check_group_1', 'check_group_2', 'check_group_3', 'check_adequacy', 'check_health'], 'integer',],

            ['created',
                'date',
                'format' => self::DATE_DB_FORMAT_FOR_VALIDATOR
            ],
            ['deny_reason', 'string', 'max' => 500],
            ['deny_reason', 'trim'],
            [['score'], 'integer', 'min' => self::MIN_POSSIBLE_SCORE, 'max' => self::MAX_POSSIBLE_SCORE ],
            // отметки о резульатах анализа по группам вопросов могут быть либо 1, либо 2
            [['check_group_1', 'check_group_2', 'check_group_3', 'check_adequacy', 'check_health'], 'in',
                'range' => [self::STATUS_CHECK_GROUP_TRUE, self::STATUS_CHECK_GROUP_FALSE]],
            ['score_type', 'integer']

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
            'userName' => 'Имя',
            'fullUserName' => 'Имя',
            'userPhone' => 'Телефон',
            'userAge' => 'Возраст',
            'durationCheck' => 'Скорость',
            'score_type' => 'Рекомендация',
            'userStatus' => 'Статус кандидата'
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            $date = new \DateTime();
            $date->setTimezone( new \DateTimeZone(self::TIMEZONE));

            if ($this->isNewRecord) {
                $this->created =$date->format('Y-m-d H:i:s');
            } else {

                $this->updated =$date->format('Y-m-d H:i:s');
            }

            return true;
        }

        return false;

    }

    /**
     * Получает запись из таблицы кандидатов, соответствубщую тесту
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(TestUser::className(), ['id' => 'user_id']);
    }

    /**
     * Получает все комменты для теста
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(TestComment::className(), ['test_id' => 'id']);
    }

    /**
     * Получает полное имя кандидата
     * @return string
     */
    public function getUserName(){

        return $this->user->name;
    }

    /**
     * Получает статус пользователя теста (кандидата)
     * @return int
     */
    public function getUserStatus(){

        return $this->user->status;
    }

    public function getFullUserName(){

        return $this->user->name . ' '.$this->user->surname;
    }

    /**
     * Получает номер телефона кандидата
     * @return string
     */
    public function getUserPhone(){

        return $this->user->phone;
    }

    public function isWorkedOnRailWay(){

        $result = TestResult::find()
          //->select('text')
         // ->joinWith('question', false)
            ->joinWith('answer')
          ->where(['test_id' => $this->id, 'test_result.question_id' => 1])
            ->asArray()
          ->one();

        return $result['answer']['text'];

    }

    /**
     * Получает возраст кандидата по его дате рождения
     * @return int
     */
    public function getUserAge(){

        return (new \DateTime())->diff(new \DateTime($this->user->date_of_birth))->y;
    }

    /**
     * Проверяет, не был ли пройден тест слишком быстро
     */
    public function isPassedTooFast(){

        if($this->getTestDuration() < self::TEST_DURATION_MINIMUM_MINUTES){

            return true;
        }

        return false;
    }

    /**
     * Проверяет, закончен ли тест (по статусу)
     * @return bool
     */
    public function isTestFinished(){

        return $this->status >= self::STATUS_FINISHED;
    }

    /**
     * получает продолжительность теста в минутах
     */
    public function getTestDuration(){

        if($this->status >= self::STATUS_FINISHED){

            return (new \DateTime($this->created))->diff(new \DateTime($this->updated))->i;
        }

        return false;
    }

    /**
     * Получает результат проверки на продолжительность теста
     */
    public function getDurationCheck(){

        $checkType = null;

        if(! $this->isTestFinished()){

            $checkType = self::TEST_DURATION_STATUS_EMPTY;
        }
        else{

            if($this->isPassedTooFast()){

                $checkType = self::TEST_DURATION_STATUS_CHECK_WARNING;
            }
            else{
                $checkType = self::TEST_DURATION_STATUS_CHECK_OK;
            }

        }

        return $checkType;

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
     * Получает все ответы, которые помечены как не желаьтельные
     * @param int $unwantedAnswerType тип нежелательного вопроса
     * @see UNWANTED_ANSWER_TYPE_FOR_ADEQUACY
     * @return array|\yii\db\ActiveRecord[]
     */
    private function getUnwantedAnswers($unwantedAnswerType)
    {

        return TestResult::find()
            ->select(['ans.unwanted'])
            ->leftJoin('test_answers as ans', 'test_result.answer_id = ans.id')
            ->where(['test_id' => $this->id, 'ans.unwanted' => $unwantedAnswerType])
            ->asArray()
            ->all();
    }

    /**
     * Обновляет данные теста:
     * сколько баллов набрано, и если вопрос последний, то пост-обработка групп вопросов
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

            // обработка на нежелательные ответы
            // проверка на адкеватность
            $test->processCheckAdequacy();
            //проверка на здоровье
            $test->processCheckHealth();

            $test->setScoreType();

        }
        // если что-то пошло не так - добавляем лог в файл и консоль chrome
        // @todo добавить отправку на почту, т.к. это критическая ошибка

        if (! $test->update()){

            $logger = new Logger('aptitude-frontend');

            $logger->pushHandler(new StreamHandler(__DIR__.'/../../frontend/runtime/logs/critical.log', Logger::CRITICAL));
            $logger->pushHandler(new ChromePHPHandler());

/*            $transport = \Swift_SmtpTransport::newInstance('smtp.mandrillapp.com', 587);

            $mailer = \Swift_Mailer::newInstance($transport);

            $message = \Swift_Message::newInstance()
                ->setSubject('critical_error')
                ->setFrom(['serge.kite@gmail.com' => 'sergio'])
                ->setTo(['serge.kite@gmail.com' => 'sergio'])
                ->setBody('errors:'.var_export($test->errors,true),'text/html');

            $logger->pushHandler(new SwiftMailerHandler($mailer,$message,Logger::CRITICAL));
*/

            $logger->critical('test update fails',$test->errors);
        }
    }

    /**
     * Проверяет, был ли отказ в последнем вопросе теста
     * от этого зависит, показывать ли дополнительный вопрос
     *
     * @param $testId
     * @return bool
     */
    public static function ifTestIsFault($testId)
    {
        $test = self::findOne(['id' => $testId]);

        if ($test->status == self::STATUS_FAULT) {

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
     * Проверяет колчество нежелательных ответов по типу "адекватность"
     */
    private function processCheckAdequacy()
    {

        $unwanted = $this->getUnwantedAnswers(self::UNWANTED_ANSWER_TYPE_FOR_ADEQUACY);

        if (count($unwanted) > self::LIMIT_UNWANTED_ANSWERS_CRITERIA_ADEQUACY) {

            $this->check_adequacy = self::STATUS_CHECK_GROUP_FALSE;
        } else $this->check_adequacy = self::STATUS_CHECK_GROUP_TRUE;
    }

    /**
     * Получает число нежелательнх ответов по адекватности
     *
     * @return int
     */
    public function getAdequacyFailedCount(){

       return count($this->getUnwantedAnswers(self::UNWANTED_ANSWER_TYPE_FOR_ADEQUACY));
    }

    /**
     *  Получает число нежелательнх ответов по здоровью
     * @return int
     */
    public function getHealthFailedCount(){

       return count($this->getUnwantedAnswers(self::UNWANTED_ANSWER_TYPE_FOR_HEALTH));
    }
    /**
     *  Проверяет колчество нежелательных ответов по типу "здоровье"
     */
    private function processCheckHealth()
    {
        $unwanted = $this->getUnwantedAnswers(self::UNWANTED_ANSWER_TYPE_FOR_HEALTH);

        if (count($unwanted) > self::LIMIT_UNWANTED_ANSWERS_CRITERIA_HEALTH) {

            $this->check_health = self::STATUS_CHECK_GROUP_FALSE;
        } else $this->check_health = self::STATUS_CHECK_GROUP_TRUE;
    }

    /**
     * Сохраняет причину отказа после прохождения теста
     *
     * @param $data
     * @return bool
     */
    public static function saveDenyReason($data)
    {

        $test = self::findOne(['id' => $data['test_id']]);

        if ($test) {
            $test->deny_reason = $data['deny_reason'];
            return $test->save();
        }

        return false;
    }

    /**
     *  Задает занчение score_type,
     * по которому строится рекомендация для действий с кандидатом
     *
     * @return string|bool
     */
    public function setScoreType()
    {

        $this->score_type = self::getScoreTypeByScore($this->score);
    }

    /**
     * Определяет тип балла по числа баллов
     * используется в миграции при запаолнеии поля score_type
     *
     * @param $score
     * @return int
     */
    public static function getScoreTypeByScore($score)
    {

        foreach (self::$scoreTypes as $type => $range) {

            if ($range['min'] <= $score && $score <= $range['max']) {

                return $type;

            }

        }
        // тип должен обязательно определиться, но если почему-то этого не произошло
        // то оставляем тип по умолчанию (не определённый)
        return self::SCORE_TYPE_UNDEFINED;
    }

    /**
     * Возвращает массив соответствий статусов теста
     * и данных для их рендеринга в списке тестов
     * @return array
     */
    public static function getTestStatusLabels()
    {

        return [self::STATUS_DEFAULT => ['class' => 'default', 'text' => 'начат','title' => 'тест начат, но на 1-ый вопрос ещё нет овтета'],
            self::STATUS_NOT_FINISHED => ['class' => 'warning', 'text' => 'не закончен'],
            self::STATUS_FINISHED => ['class' => 'success', 'text' => 'закончен'],
            self::STATUS_FAULT => ['class' => 'danger', 'text' => 'отказ','title' => 'тест закончен с отказом']
        ];
    }

    /**
     * Возвращает массив соответствий статусов результата анализа проверочных групп вопросов
     * и данных для их рендеринга в списке тестов
     * @pram $checkGroupId номер проверочной группы вопросов
     * @return array|bool
     */
    public static function getTestCheckResultsLabels($checkGroupId)
    {

        // в зависимости от номера проверочной группы возвращаем массив соответствий
        switch ($checkGroupId) {

            case self::CHECK_GROUP_1:
                return [
                    self::STATUS_CHECK_GROUP_TRUE => ['class' => 'primary', 'text' => 'ок',
                        'title' => self::$recommendationsByGroupCheck[self::CHECK_GROUP_1][self::STATUS_CHECK_GROUP_TRUE]
                    ],
                    self::STATUS_CHECK_GROUP_FALSE => ['class' => 'danger', 'text' => 'ложь',
                        'title' => self::$recommendationsByGroupCheck[self::CHECK_GROUP_1][self::STATUS_CHECK_GROUP_FALSE]
                    ],
                    ];
            case self::CHECK_GROUP_2:
                return [
                    self::STATUS_CHECK_GROUP_TRUE => ['class' => 'primary', 'text' => 'ок',
                        'title' => self::$recommendationsByGroupCheck[self::CHECK_GROUP_2][self::STATUS_CHECK_GROUP_TRUE]
                    ],
                    self::STATUS_CHECK_GROUP_FALSE => ['class' => 'danger', 'text' => 'ложь',
                        'title' => self::$recommendationsByGroupCheck[self::CHECK_GROUP_2][self::STATUS_CHECK_GROUP_FALSE]
                    ],
                ];
            case self::CHECK_GROUP_3:
                return [
                    self::STATUS_CHECK_GROUP_TRUE => ['class' => 'primary', 'text' => 'ок',
                        'title' => self::$recommendationsByGroupCheck[self::CHECK_GROUP_3][self::STATUS_CHECK_GROUP_TRUE]
                    ],
                    self::STATUS_CHECK_GROUP_FALSE => ['class' => 'warning', 'text' => 'неопределённость',
                        'title' => self::$recommendationsByGroupCheck[self::CHECK_GROUP_3][self::STATUS_CHECK_GROUP_FALSE]
                    ],
                ];


        }
        // если номер группы другой либо не пришёл - возвращаем ложь
        return false;
    }

    /**
     * Возвращает массив соответствий типа человека и рекомендаций по результатам теста
     * и данных для рендеринга этип типов и рекомендаций в списке
     *
     *
     */
    public static function getRecommendationsLabels()
    {

        return [

            self::SCORE_TYPE_BAD => ['class' => 'danger', 'text' => 'не подходит',
                'title' => self::$recommendationsByScoreType[self::SCORE_TYPE_BAD]],
            self::SCORE_TYPE_DOUBTER => ['class' => 'warning', 'text' => 'сомневающийся',
                'title' => self::$recommendationsByScoreType[self::SCORE_TYPE_DOUBTER]],
            self::SCORE_TYPE_INCLINED_TO_DOUBT => ['class' => 'warning', 'text' => 'скорее сомневающийся',
                'title' => self::$recommendationsByScoreType[self::SCORE_TYPE_INCLINED_TO_DOUBT]],
            self::SCORE_TYPE_GOOD => ['class' => 'success', 'text' => 'подходит',
                'title' =>  self::$recommendationsByScoreType[self::SCORE_TYPE_GOOD]],
            self::SCORE_TYPE_CRAFTY=> ['class' => 'warning', 'text' => 'хитрый?',
                'title' => self::$recommendationsByScoreType[self::SCORE_TYPE_CRAFTY]],
            self::SCORE_TYPE_UNDEFINED => ['class' => 'default', 'text' => '--',
                'title' => self::$recommendationsByScoreType[self::SCORE_TYPE_UNDEFINED]
        ]
            ];
    }

    /**
     * Получает текст рекомендации исходя из общего балла
     */
    public function getRecommendation(){

        $score_type = $this->getScoreTypeByScore($this->score);

        return self::$recommendationsByScoreType[$score_type];
    }

    /**
     * Возвращает массив соответствий по итогам проверки вопросов на адекватность
     *
     * @return array
     */
    public static function getCheckAdequacyLabels()
    {
        return [
            self::STATUS_CHECK_GROUP_TRUE => ['class' => 'primary', 'text' => 'ок',
                'title' => self::$recommendationsByAdequacyCheck[self::STATUS_CHECK_GROUP_TRUE]],
            self::STATUS_CHECK_GROUP_FALSE => ['class' => 'warning', 'text' => 'адекватность',
                'title' => self::$recommendationsByAdequacyCheck[self::STATUS_CHECK_GROUP_FALSE]],
        ];

    }

    /**
     * Возвращает массив соответствий по итогам проверки вопросов на адекватность
     *
     * @return array
     */
    public static function getCheckHealthLabels()
    {
        return [
            self::STATUS_CHECK_GROUP_TRUE => ['class' => 'primary', 'text' => 'ок',
                'title' => self::$recommendationsByHealthCheck[self::STATUS_CHECK_GROUP_TRUE]],
            self::STATUS_CHECK_GROUP_FALSE => ['class' => 'warning', 'text' => 'здороввье',
                'title' => self::$recommendationsByHealthCheck[self::STATUS_CHECK_GROUP_FALSE]],
        ];
    }

    /**
     *  Возвращает массив соответствий ярлыков для проверки времени прохолждения теста
     * @return array
     */
    public static function getDurationLabels(){
        return [
            self::TEST_DURATION_STATUS_EMPTY => ['class' => 'default', 'text' => '--', 'title' => 'провека не проводилась'],
            self::TEST_DURATION_STATUS_CHECK_OK => ['class' => 'primary', 'text' => 'ок', 'title' => 'продолжительность теста нормальная'],
            self::TEST_DURATION_STATUS_CHECK_WARNING => ['class' => 'warning', 'text' => 'быстро', 'title' => 'тест пройден слишком быстро'],
        ];

    }

    /**
     *  Возвращает массив соответствий ярлыков статуса кандидата
     * @return array
     */
    public static function getUserStatusLabels(){

        return [

            TestUser::STATUS_DEFAULT =>
                ['class' => 'default', 'text' => TestUser::STATUS_DEFAULT_NAME,
                    'title' => TestUser::$userStatusText[TestUser::STATUS_DEFAULT]],
            TestUser::STATUS_UNDER_CONSIDERATION =>
                ['class' => 'warning', 'text' => TestUser::STATUS_UNDER_CONSIDERATION_NAME,
                    'title' => TestUser::$userStatusText[TestUser::STATUS_UNDER_CONSIDERATION]],
            TestUser::STATUS_ACCEPTED =>
                ['class' => 'success', 'text' => TestUser::STATUS_ACCEPTED_NAME,
                    'title' => TestUser::$userStatusText[TestUser::STATUS_ACCEPTED]],
            TestUser::STATUS_DECLINED =>
                ['class' => 'danger', 'text' => TestUser::STATUS_DECLINED_NAME,
                     'title' => TestUser::$userStatusText[TestUser::STATUS_DECLINED]],

        ];
    }

    /**
     * Получает текст, соответстсующий статусу кандадата
     *
     * @return mixed
     */
    public function getUserStatusText(){

        return TestUser::$userStatusText[$this->userStatus];
    }

    /**
     * Возвращает массив соответствий кнопок пеерключения видимости групп вопросов (на бэкенде)
     */
    public static function getBackendControlButtonLabels(){

        return [

            self::CHECK_GROUP_1 => ['text' => self::CHECK_GROUP_1_NAME,
                'btn_class' => [

                    self::STATUS_CHECK_GROUP_UNDEFINED => 'default',
                    self::STATUS_CHECK_GROUP_FALSE => 'danger',
                    self::STATUS_CHECK_GROUP_TRUE => 'success',
                ],
                'title' => [

                    self::STATUS_CHECK_GROUP_UNDEFINED => self::$recommendationsByGroupCheck[self::CHECK_GROUP_1][self::STATUS_CHECK_GROUP_UNDEFINED],
                    self::STATUS_CHECK_GROUP_FALSE =>  self::$recommendationsByGroupCheck[self::CHECK_GROUP_1][self::STATUS_CHECK_GROUP_FALSE],
                    self::STATUS_CHECK_GROUP_TRUE =>  self::$recommendationsByGroupCheck[self::CHECK_GROUP_1][self::STATUS_CHECK_GROUP_TRUE],
                ],
                'group' =>  self::CHECK_GROUP_1

            ],

            self::CHECK_GROUP_2 => ['text' => self::CHECK_GROUP_2_NAME,
                'btn_class' => [

                    self::STATUS_CHECK_GROUP_UNDEFINED => 'default',
                    self::STATUS_CHECK_GROUP_FALSE => 'danger',
                    self::STATUS_CHECK_GROUP_TRUE => 'success',
                ],
                'title' => [

                    self::STATUS_CHECK_GROUP_UNDEFINED => self::$recommendationsByGroupCheck[self::CHECK_GROUP_2][self::STATUS_CHECK_GROUP_UNDEFINED],
                    self::STATUS_CHECK_GROUP_FALSE => self::$recommendationsByGroupCheck[self::CHECK_GROUP_2][self::STATUS_CHECK_GROUP_FALSE],
                    self::STATUS_CHECK_GROUP_TRUE => self::$recommendationsByGroupCheck[self::CHECK_GROUP_2][self::STATUS_CHECK_GROUP_TRUE],
                ],
                'group' =>  self::CHECK_GROUP_2

            ],

            self::CHECK_GROUP_3 => ['text' => self::CHECK_GROUP_3_NAME,
                'btn_class' => [

                    self::STATUS_CHECK_GROUP_UNDEFINED => 'default',
                    self::STATUS_CHECK_GROUP_FALSE => 'warning',
                    self::STATUS_CHECK_GROUP_TRUE => 'success',
                ],
                'title' => [

                    self::STATUS_CHECK_GROUP_UNDEFINED =>  self::$recommendationsByGroupCheck[self::CHECK_GROUP_3][self::STATUS_CHECK_GROUP_UNDEFINED],
                    self::STATUS_CHECK_GROUP_FALSE => self::$recommendationsByGroupCheck[self::CHECK_GROUP_3][self::STATUS_CHECK_GROUP_FALSE],
                    self::STATUS_CHECK_GROUP_TRUE =>  self::$recommendationsByGroupCheck[self::CHECK_GROUP_3][self::STATUS_CHECK_GROUP_TRUE],
                ],
                'group' =>  self::CHECK_GROUP_3

            ],

            self::CHECK_GROUP_ADEQUACY => ['text' => self::CHECK_GROUP_ADEQUACY_NAME,
                'btn_class' => [

                    self::STATUS_CHECK_GROUP_UNDEFINED => 'default',
                    self::STATUS_CHECK_GROUP_FALSE => 'warning',
                    self::STATUS_CHECK_GROUP_TRUE => 'success',
                ],
                'title' => [

                    self::STATUS_CHECK_GROUP_UNDEFINED => self::$recommendationsByAdequacyCheck[self::STATUS_CHECK_GROUP_UNDEFINED],
                    self::STATUS_CHECK_GROUP_FALSE => self::$recommendationsByAdequacyCheck[self::STATUS_CHECK_GROUP_FALSE],
                    self::STATUS_CHECK_GROUP_TRUE => self::$recommendationsByAdequacyCheck[self::STATUS_CHECK_GROUP_TRUE],
                ],
                'group' =>  self::CHECK_GROUP_ADEQUACY

            ],
        self::CHECK_GROUP_HEALTH => ['text' => self::CHECK_GROUP_HEALTH_NAME,
                'btn_class' => [

                    self::STATUS_CHECK_GROUP_UNDEFINED => 'default',
                    self::STATUS_CHECK_GROUP_FALSE => 'warning',
                    self::STATUS_CHECK_GROUP_TRUE => 'success',
                ],
                'title' => [

                    self::STATUS_CHECK_GROUP_UNDEFINED => self::$recommendationsByHealthCheck[self::STATUS_CHECK_GROUP_UNDEFINED],
                    self::STATUS_CHECK_GROUP_FALSE => self::$recommendationsByHealthCheck[self::STATUS_CHECK_GROUP_FALSE],
                    self::STATUS_CHECK_GROUP_TRUE => self::$recommendationsByHealthCheck[self::STATUS_CHECK_GROUP_TRUE],
                ],
                'group' =>  self::CHECK_GROUP_HEALTH

            ],

        ];

    }

    /**
     * получает результаты проверок по группам
     * @return array
     */
    public function getCheckGroupsResults(){

        $check = [];

        foreach(self::$checkGroups as $group){

            $check[$group] = $this->getCheckByCheckGroup($group);

        }
        return $check;
    }

    private function getCheckByCheckGroup($group){

        switch ($group) {
            case self::CHECK_GROUP_1:
                $check = $this->check_group_1;
                break;
            case self::CHECK_GROUP_2:
                $check = $this->check_group_2;
                break;
            case self::CHECK_GROUP_3:
                $check = $this->check_group_3;
                break;
            default:
                $check = 0;
        }
        // так приходится делать,потому что check_group_1 и т.р. по умолчанию null
        // а должен быть 0
        if($check == null)
        {
            return 0;
        }
        return $check;
    }

    /**
     * Получает оценку адекватности по нежелательным ответам
     * @return int|mixed
     */
    public function getCheckAdequacy(){

        if($this->check_adequacy == null){
            return 0;
        }
        return $this->check_adequacy;

    }

    public function getCheckHealth(){
        if($this->check_health == null){
            return 0;
        }
        return $this->check_health;

    }

}