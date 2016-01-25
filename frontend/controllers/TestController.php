<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 25.01.16
 * Time: 17:52
 */
namespace frontend\controllers;


use app\models\TestUser;

class TestController extends \yii\web\Controller
{
    public function actionBegin()
    {
        $model = new TestUser();

        if($model->load(\Yii::$app->request->post()) && $model->save()){

            
            // @todo создание нового теста перед редиректом на него
            $this->redirect('test/process');
        }
        else{


        }

        return $this->render('begin',['model' => $model]);
    }

    public function actionProcess()
    {
        return $this->render('process');
    }

}