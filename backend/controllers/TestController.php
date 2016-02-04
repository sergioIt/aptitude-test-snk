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

class TestController extends Controller{

    public function actionList()
    {
        $this->layout = 'custom';

        if (\Yii::$app->user->isGuest) {

            return  $this->redirect(Url::to(['site/login']));
        }

        $this->layout = 'custom';

        $dataProvider = new ActiveDataProvider([
            'query' => Test::find()
                ->where(['in','status',[Test::STATUS_FINISHED,Test::STATUS_FAULT]]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('list',
            ['dataProvider' => $dataProvider,
            'testStatusTypes' => Test::getTestStatusLabels(),
            'testCheckResultsLabels' => Test::getTestCheckResultsLabels(1),
            'testCheckResultsLabelsForGroup3' => Test::getTestCheckResultsLabels(3),
            'testScoreRecommendations' => Test::getRecommendationsLabels(),
            'testCheckAdequacyLabels' => Test::getCheckAdequacyLabels(),
            'testCheckHealthLabels' => Test::getCheckHealthLabels(),
            ]
        );
    }


}