<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 03.02.16
 * Time: 15:36
 */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\Modal;

$this->registerJsFile('js/backend.js',
    ['depends' => ['\yii\web\JqueryAsset'],
        'position' => \yii\web\View::POS_END,]
);
?>
<h2>Список тестов </h2>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
   // 'filterModel' => $searchModel,
    'columns' => [
        'id',
        'userName' => [
            'attribute' => 'fullUserName',

            'value' => function($data){
                return is_null($data->userName) ? 'Not set' : ($data->fullUserName);
            },

        ],
        'userStatus' => [
            'header' => 'Статус <br> кандидата',
            'label' => 'label',
            'format' => 'raw',
            'value' =>
                function ($model, $key, $index, $column) use ($userStatusLabels) {

                    return Html::tag('p', Html::encode($userStatusLabels[$model->userStatus]['text']),
                        [
                            'class' => 'label label-' . $userStatusLabels[$model->userStatus]['class'],
                            'title' =>  $userStatusLabels[$model->userStatus]['title']
                        ]);
                }
        ],
        'userAge',
        'userPhone',

        'isWorkedOnRailWay' => [
            'header' => 'работал на жд',
            'format' => 'raw',
            'value' =>

                function ($model, $key, $index, $column) {

                   return $model->isWorkedOnRailWay();
                }

        ],

        'created' => [
            'attribute' => 'created',
            'contentOptions' => ['style' => 'width:100px;'],
        ],
      //  'updated',
        'status' =>
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' =>

                    function ($model, $key, $index, $column) use ($testStatusLabels) {

                        return Html::tag('p', Html::encode($testStatusLabels[$model->status]['text']),
                            ['class' => 'label label-' . $testStatusLabels[$model->status]['class']
                            ]);
                    }
            ],
        'score',

        'scoreType' => [
            'attribute'=>'score_type',
            'format' => 'raw',
            'value' => function ($model) use ($testScoreRecommendations) {

                $scoreType = $model->score_type;

                return  Html::tag('p', Html::encode($testScoreRecommendations[$scoreType]['text']),
                    ['class' => 'label label-' . $testScoreRecommendations[$scoreType]['class'],
                        'title' => $testScoreRecommendations[$scoreType]['title']

                    ]);
            }
        ],
        'check_group_1' =>
            [
                'attribute' => 'check_group_1',
                'label' => 'check1',

               // 'header' => 'Проверка 1 <br> (на ложь)',
                'format' => 'raw',
                'value' => function ($model) use ($testCheckResultsLabels) {

                    if (isset($model->check_group_1)) {
                        $html = Html::tag('p', Html::encode($testCheckResultsLabels[$model->check_group_1]['text']),
                            ['class' => 'label label-' . $testCheckResultsLabels[$model->check_group_1]['class'],
                                'title' => $testCheckResultsLabels[$model->check_group_1]['title']

                            ]);
                    } else {
                        $html = Html::tag('p', '--',
                            ['class' => 'label label-default',
                                'title' => 'проверка не проводилась'

                            ]);
                    }

                    return $html;
                }
            ],
        'check_group_2' =>
            [
                'attribute' => 'check_group_2',
                //'header' => 'Проверка 2 <br> (на ложь)',
                'label' => 'check2',
                'format' => 'raw',
                'value' => function ($model) use ($testCheckResultsLabels) {

                    if (isset($model->check_group_2)) {
                        $html = Html::tag('p', Html::encode($testCheckResultsLabels[$model->check_group_2]['text']),
                            ['class' => 'label label-' . $testCheckResultsLabels[$model->check_group_2]['class'],
                                'title' => $testCheckResultsLabels[$model->check_group_2]['title']
                            ]);
                    } else {
                        $html = Html::tag('p', '--',
                            ['class' => 'label label-default',
                                'title' => 'проверка не проводилась'

                            ]);
                    }

                    return $html;
                }
            ],
        'check_group_3' =>
            [
                'attribute' => 'check_group_3',
                //'header' => 'Проверка 3 <br> (неопределённость)',
                'label' => 'check3',
                'format' => 'raw',
                'value' => function ($model) use ($testCheckResultsLabelsForGroup3) {

                    if (isset($model->check_group_3)) {
                        $html = Html::tag('p', Html::encode($testCheckResultsLabelsForGroup3[$model->check_group_3]['text']),
                            ['class' => 'label label-' . $testCheckResultsLabelsForGroup3[$model->check_group_3]['class'],
                                'title' => $testCheckResultsLabelsForGroup3[$model->check_group_3]['title']
                            ]);
                    } else {
                        $html = Html::tag('p', '--',
                            ['class' => 'label label-default',
                                'title' => 'проверка не проводилась'

                            ]);
                    }

                    return $html;

                }
            ],
        'additional_notify' => [
            'header' => 'Дополнительные <br> проверки',
            'format' => 'raw',
            'value' => function ($model) use ($testCheckAdequacyLabels,$testCheckHealthLabels, $testDurationLabels) {

                $html = '';
                //$labelAdequacy = '';
               // $labelHealth = '';

                if (! isset($model->check_adequacy) && !isset($model->check_health))
                {
                    $html = Html::tag('p', '--',
                        ['class' => 'label label-default',
                            'title' => 'проверка не проводилась'
                        ]);

                }

                if (isset($model->check_adequacy) ) {
                    $html .= Html::tag('p', Html::encode($testCheckAdequacyLabels[$model->check_adequacy]['text']),
                        ['class' => 'label label-'.$testCheckAdequacyLabels[$model->check_adequacy]['class'],
                            'title' =>  $testCheckAdequacyLabels[$model->check_adequacy]['title']
                        ]);
                }

                if (isset($model->check_health) ) {
                    $html .=' '. Html::tag('p', Html::encode($testCheckHealthLabels[$model->check_health]['text']),
                        ['class' => 'label label-'.$testCheckHealthLabels[$model->check_health]['class'],
                            'title' =>  $testCheckHealthLabels[$model->check_health]['title']
                        ]);
                }

                if(isset($model->durationCheck)){

                    $html .= ' '. Html::tag('p', Html::encode($testDurationLabels[$model->durationCheck]['text']),
                        ['class' => 'label label-'.$testDurationLabels[$model->durationCheck]['class'],
                            'title' =>  $testDurationLabels[$model->durationCheck]['title']
                        ]);
                }

                return $html;
            }

        ],
/*        'durationCheck' => [
        'header' => 'Скорость',
        'format' => 'raw',
        'value' => function ($model) use ($testDurationLabels) {

            $type = $model->durationCheck;
             return Html::tag('p', Html::encode($testDurationLabels[$type]['text']),
                    ['class' => 'label label-'.$testDurationLabels[$type]['class'],
                        'title' =>  $testDurationLabels[$type]['title']
                    ]);

}

        ],*/
        'actions' =>
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Действия',
                'template' => '{view}{update}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a(Html::tag('span','',
                            ['class'=>'glyphicon glyphicon-search', 'aria-hidden'=>'true']),
                            '#',
                            [
                                'class' => 'btn_view_test',
                                'title' => Yii::t('yii', 'Просмотр теста'),
                                'data-toggle' => 'modal',
                                'data-target' => '#activity-modal',
                                'data-id' => $model->id,
                                'data-url' => \yii::$app->getUrlManager()->createUrl('test/view'),
                                'padding' => '5px'
                            ]);
                    },
                'update' => function ($url, $model, $key) {
                        return Html::a(Html::tag('span','',
                            ['class'=>'glyphicon glyphicon-pencil', 'aria-hidden'=>'true']),
                            '#',
                            [
                                'class' => 'btn_update_test',
                                'title' => Yii::t('yii', 'Обновить'),
                                'data-toggle' => 'modal',
                                'data-target' => '#activity-modal',
                                'data-id' => $model->id,
                                'data-url' => \yii::$app->getUrlManager()->createUrl('test/update')
                            ]);
                    }

            ]

    ]
]]);

Modal::begin([
    'id' => 'activity-modal',
    'header' => '<h3 class="modal-title">Результаты теста</h3>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Закрыть</a>',
    'size'=>Modal::SIZE_LARGE,
]);

/*Modal::begin([
    'id' => 'activity-modal-update',
    'header' => '<h3 class="modal-title">Редактирование теста</h3>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Закрыть</a>',
    'size'=>Modal::SIZE_LARGE,
]);*/

echo 'Say hello...';
Modal::end();

?>

