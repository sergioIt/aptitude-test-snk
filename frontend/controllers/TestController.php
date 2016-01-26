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

           $this->layout = 'custom';
            // @todo создание нового теста перед редиректом н
            $this->redirect('test/process');
        }
        else{


        }
        $this->layout = 'custom';
        return $this->render('begin',['model' => $model]);
    }

    public function actionProcess()
    {
        return $this->render('process');
    }

}