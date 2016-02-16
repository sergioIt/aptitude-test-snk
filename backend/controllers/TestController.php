<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 03.02.16
 * Time: 15:33
 */

namespace backend\controllers;

use backend\models\TestComment;
use common\models\TestUser;
use yii\web\Controller;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use common\models\Test;
use common\models\TestResult;
use common\models\TestSearch;
use Yii;

class TestController extends Controller
{

    public function actionList()
    {
        $this->layout = 'custom';

        if (\Yii::$app->user->isGuest) {

            return $this->redirect(Url::to(['site/login']));
        }

        $this->layout = 'custom';

        $dataProvider = new ActiveDataProvider([
            'query' => Test::find(),
                //->where(['in', 'status', [Test::STATUS_FINISHED, Test::STATUS_FAULT]]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);


        $searchModel = new TestSearch();
        $dataProvider->pagination->pageSize=50;

        return $this->render('list',
            ['dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'testStatusLabels' => Test::getTestStatusLabels(),
                'testCheckResultsLabels' => Test::getTestCheckResultsLabels(1),
                'testCheckResultsLabelsForGroup3' => Test::getTestCheckResultsLabels(3),
                'testScoreRecommendations' => Test::getRecommendationsLabels(),
                'testCheckAdequacyLabels' => Test::getCheckAdequacyLabels(),
                'testCheckHealthLabels' => Test::getCheckHealthLabels(),
                'testDurationLabels' => Test::getDurationLabels(),
            ]
        );
    }

    public function actionView()
    {

        if (\Yii::$app->request->isAjax) {
            $params = \Yii::$app->request->get();
            $results = null;
            $test = null;
            $err = null;

            if (isset($params['test_id'])) {

                $results = TestResult::find()
                    ->joinWith(['question', 'answer'])
                    ->where(['test_id' => $params['test_id']])
                    ->asArray()
                    ->all();
                $test = Test::findOne(['id' => $params['test_id']]);

                $results = TestResult::composeAjaxOutput($results);

            } else {
                $err = 'тест не найден';
            }

            return $this->renderAjax('result',
                ['results' => $results,
                    'test' => $test,
                    'error' => $err,
                ]);
        } else {
            echo 'not ajax';
            return false;
        }
    }

    /**
     * Вызывает модельное окно с данными теста, которые можно редактировать
     * (комментарий)
     */
    public function actionUpdate(){

        if (\Yii::$app->request->isAjax) {
            $test = null;
            $err = null;
            $params = \Yii::$app->request->get();

            if (isset($params['test_id'])) {

            $test = Test::find(['id' => $params['test_id']])->one();

        } else {
                $err = 'тест не найден';
            }
            return $this->renderAjax('update',
                [
                    'test' => $test,
                    'error' => $err,
                    'userStatuses' =>  TestUser::getAllStatuses(),
                ]);

        }
        else {
            echo 'not ajax';
            return false;
        }

    }

    /**
     * Сохраняет комментарий к тесту в базе
     *
     * @return bool
     */
    public function actionSavecomment(){

        if (\Yii::$app->request->isAjax) {

            $data = Yii::$app->request->post();

            $testComment = new TestComment();
            $testComment->test_id = $data['test_id'];
            $testComment->user_id = $data['user_id'];
            $testComment->text = $data['text'];

                if($testComment->save()){

                    echo 'saved';
                }
            else{

                var_dump($testComment->errors);
            }

        }
        else{

            echo 'not ajax';
        }

        return false;
    }

    /**
     * Обновляет статус кандидата в базе
     */
    public function actionUpdateuserstatus()
    {

        if (\Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();

            //var_dump($data);
            //die();
            $testUser = TestUser::findOne($data['user_id']);

            $testUser->status = $data['status'];

            $updated = $testUser->update();

            //var_dump($updated);
            if ($updated !== false){

                echo $updated;
            }
            else{
                echo 'err';
            }

        }
        else{

            echo 'not ajax';
        }
    }

}