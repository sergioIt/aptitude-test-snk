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

use yii\grid\GridView;


class TestController extends Controller{

    public function actionList()
    {
        $this->layout = 'custom';

        if (\Yii::$app->user->isGuest) {

            return  $this->redirect(Url::to(['site/login']));
            //return $this->goHome();
        }

        $this->layout = 'custom';


        $dataProvider = new ActiveDataProvider([
            'query' => Test::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $testStatusTypes = [
            Test::STATUS_DEFAULT => ['class' => 'default', 'text' => 'только начат'],
            Test::STATUS_NOT_FINISHED => ['class' => 'warning', 'text' => 'не закончен'],
            Test::STATUS_FINISHED => ['class' => 'success', 'text' => 'закончен'],
            Test::STATUS_FAULT => ['class' => 'danger', 'text' => 'закончен с отказом']

        ];

        $testCheckTypes = [
            Test::STATUS_CHECK_GROUP_TRUE => ['class' => 'primary', 'text' => 'ок','title' => 'нет подозрений на ложь'],
            Test::STATUS_CHECK_GROUP_FALSE => ['class' => 'danger', 'text' => 'ложь', 'title' => 'подозрение на ложь'],
        ];

        $testCheckTypesForGroup3 = [
            Test::STATUS_CHECK_GROUP_TRUE => ['class' => 'primary', 'text' => 'ок','title' => 'нет подозрений на проблемы с неопредёлнностью'],
            Test::STATUS_CHECK_GROUP_FALSE => ['class' => 'warning', 'text' => 'неопределённость', 'title' => 'подозрение на проблемы с неопредёлнностью'],
        ];

       /* echo GridView::widget([
            'dataProvider' => $dataProvider,
        ]);*/
        return $this->render('list',
            ['dataProvider' => $dataProvider,
            'testStatusTypes' => $testStatusTypes,
            'testCheckTypes' => $testCheckTypes,
            'testCheckTypesForGroup3' => $testCheckTypesForGroup3,

            ]
        );
    }


}