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
    'columns' => [
        'id',
        'user' => [

            'header' => 'Пользователь',
            'value' =>

                function ($model, $key, $index, $column) {
                    return $model->user->name . ' ' . $model->user->surname;
                }
        ],
        'created',
        'updated',
        'status' =>
            [
                'header' => 'Статус',
                'format' => 'raw',
                'value' =>

                    function ($model, $key, $index, $column) use ($testStatusLabels) {

                        return Html::tag('p', Html::encode($testStatusLabels[$model->status]['text']),
                            ['class' => 'label label-' . $testStatusLabels[$model->status]['class']
                            ]);
                    }
            ],
        'score',
        'recommendation' => [
            'header' => 'Рекоммендация',
            'format' => 'raw',
            'value' => function ($model) use ($testScoreRecommendations) {

                $type = $model::getScoreType($model->id);
                if ($type) {

                    $html = Html::tag('p', Html::encode($testScoreRecommendations[$type]['text']),
                        ['class' => 'label label-' . $testScoreRecommendations[$type]['class'],
                            'title' => $testScoreRecommendations[$type]['title']

                        ]);
                } else {
                    $html = Html::tag('p', '---',
                        ['class' => 'label label-default',
                            'title' => 'проверка не проводилась'
                        ]);
                }
                return $html;
            }
        ],
        'check_group_1' =>
            [
                'header' => 'Проверка 1 <br> (на ложь)',
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
                'header' => 'Проверка 2 <br> (на ложь)',
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
                'header' => 'Проверка 3 <br> (неопределённость)',
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
            'header' => 'Нежелательные <br> ответы',
            'format' => 'raw',
            'value' => function ($model) use ($testCheckAdequacyLabels,$testCheckHealthLabels) {

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

                return $html;
            }

        ],
        'actions' =>
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Действия',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::button(Html::tag('span','',
                            ['class'=>'glyphicon glyphicon-search', 'aria-hidden'=>'true']),
                            [
                                'class' => 'btn btn-sm btn-primary btn_view_test',
                                'title' => Yii::t('yii', 'Просмотр теста'),
                                'data-toggle' => 'modal',
                                'data-target' => '#activity-modal',
                                'data-id' => $model->id,
                                'data-url' => \yii::$app->getUrlManager()->createUrl('test/view')
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

//echo 'Say hello...';
Modal::end();

?>

