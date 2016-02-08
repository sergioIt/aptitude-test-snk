<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 03.02.16
 * Time: 15:33
 */

namespace backend\controllers;

use yii\web\Controller;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use common\models\Test;
use common\models\TestResult;

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
            'query' => Test::find()
                ->where(['in', 'status', [Test::STATUS_FINISHED, Test::STATUS_FAULT]]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('list',
            ['dataProvider' => $dataProvider,
                'testStatusLabels' => Test::getTestStatusLabels(),
                'testCheckResultsLabels' => Test::getTestCheckResultsLabels(1),
                'testCheckResultsLabelsForGroup3' => Test::getTestCheckResultsLabels(3),
                'testScoreRecommendations' => Test::getRecommendationsLabels(),
                'testCheckAdequacyLabels' => Test::getCheckAdequacyLabels(),
                'testCheckHealthLabels' => Test::getCheckHealthLabels(),
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
}