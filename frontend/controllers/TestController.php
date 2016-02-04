<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 25.01.16
 * Time: 17:52
 */
namespace frontend\controllers;


use common\models\Test;
use common\models\TestQuestions;
use common\models\TestResult;
use common\models\TestUser;
use Yii;
use yii\base\Exception;
use Yii\web\session;
use yii\helpers\Html;
use yii\helpers\Url;

class TestController extends \yii\web\Controller
{
    public function actionBegin()
    {
        $testUser = new TestUser();

        if ($testUser->load(\Yii::$app->request->post()) && $testUser->save()) {

            $session = Yii::$app->session;
            $session->open();
            //заносим user_id в сессию
            $session['user_id'] = $testUser->id;

            $this->layout = 'custom';

            $test = $this->createNewTest();
            if ($test) {
                $session['test_id'] = $test->id;
                // назначаем первый вопрос
                $session['question_id'] = 1;
                $session['question_count'] = TestQuestions::find()->count();

                $this->redirect(Url::to(['test/process']));
            } else {
                throw new Exception('error creating new test');
            }

        }

        $this->layout = 'custom';
        return $this->render('begin', ['model' => $testUser]);
    }

    /**
     * создаёт новый тест для созданного пользователя
     */
    private function createNewTest()
    {

        $test = new Test();
        $test->user_id = Yii::$app->session->get('user_id');
        $test->created = (new \DateTime())->format('Y-m-d H:i:s');

        if ($test->save() && $test->validate()) {

            return $test;
        } else {
            throw new Exception('can\'t create new test');
        }

    }

    /**
     * Создаёт новый тест и проводит сам тест
     * Рендерит первый вопрос, остальне - через ajax
     *
     * @return string
     */
    public function actionProcess()
    {

        $testId = Yii::$app->session->get('test_id');
        $userId = Yii::$app->session->get('user_id');
        $questionId = Yii::$app->session->get('question_id');


     // это для быстрого перехода к нужному вопросу
     // используется только при локальном дебаге, в продакшене быть не долждно, т.к. нарушается логика

/*          $q = Yii::$app->request->get('q_id');
        if(isset($q))
        {
            $questionId = $q;
        }*/

        if (!isset($testId) || !isset($userId)) {

            $beginActionUrl = Yii::$app->urlManager->createUrl('test/begin');
            $this->redirect($beginActionUrl);
        }

        $question = TestQuestions::findOne(['id' => $questionId]);

        if($question){
            $question->prepare();
            $this->layout = 'custom';

            return $this->render('question',
                ['question' => $question,
                    'questionsCount' => Yii::$app->session->get('question_count'),
                    'testId' => $testId,
                    'userId' => $userId,
                    'percent' => self::getPercent($question->id),

                ]);
        }
        else{
            $this->redirect(Url::to(['test/begin']));

        }
        return true;
    }

    /**
     * Вычисляет процент прохождения теста
     * @param $questionId
     * @return float
     */
    private function getPercent($questionId)
    {

        return ($questionId - 1) / Yii::$app->session->get('question_count') * 100;
    }

    /**
     * сохраняет ответ в базе
     */
    public function actionSaveanswer()
    {

        if (Yii::$app->request->isAjax) {

            $data = Yii::$app->request->post();

           if (TestResult::saveBatch($data)) {

                $session = Yii::$app->session;

                $session->open();

                $nextQuestionId = $data['question_id'] + 1;

                $session['question_id'] = $nextQuestionId;

                $testId = Yii::$app->session->get('test_id');
                // обновляем тест: дату редакатирования и статус
                Test::updateTest($testId);

                // находим следующий вопрос
               //@todo тут лучше не повторять код, а перебросить на process, где обрабатывать ajax-запрос
                $question = TestQuestions::findOne(['id' => $nextQuestionId]);

                // если вопрос нашёлся, то рендерим его
                if ($question) {

                    $question->prepare();
                    return $this->renderAjax('question',
                        ['question' => $question,
                            'questionsCount' => Yii::$app->session->get('question_count'),
                            'testId' => $testId,
                            'percent' => self::getPercent($question->id),

                        ]);
                }
                //иначе рендерим концовку
                else {
                    return $this->renderAjax('finish',
                        [
                            'additionalQuestion' =>  Test::ifTestIsFault($testId),
                            'testId' => $testId,
                            'originUrl' => Url::to(['/'])
                        ]);
                }

            } else {
                echo 'error: no save';
            }

        } else {
            echo 'not ajax';
        }

        return true;
    }

    /**
     * Принимает ajax-запрос на сохранение ответа на доп. вопрос
     * и сохраняет ответ в модели теста
     */
    public function actionSaveadditional()
    {
        if (Yii::$app->request->isAjax) {

            $data = Yii::$app->request->post();

            Test::saveDenyReason($data);
        }
        else{
            echo 'not ajax';
        }
    }
}