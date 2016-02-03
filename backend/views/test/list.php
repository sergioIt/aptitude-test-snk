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

/*var_dump($testStatusTypes);*/
?>
<h2>Список тестов </h2>

<?= GridView::widget([
'dataProvider' => $dataProvider,
    'columns' =>[
        'id',
        'user' => [

            'header' => 'Пользователь',
            'value' =>

                function ($model, $key, $index, $column) {
                    return $model->user->name.' '.$model->user->surname;
                }
        ],
        'created',
        'updated',
        'status' =>
        [
            'header' => 'Статус',
            'format' => 'raw',
            'value' =>

        function ($model, $key, $index, $column) use ($testStatusTypes){

            return Html::tag('p', Html::encode($testStatusTypes[$model->status]['text']),
            ['class' => 'label label-'.$testStatusTypes[$model->status]['class']
            ]);
        }
        ],
        'score',
        'check_group_1' =>
        [
            'header' => 'Проверка 1 <br> (на ложь)',
            'format' => 'raw',
            'value' => function($model) use ($testCheckTypes){

                if(isset($model->check_group_1))
                {
                    $html = Html::tag('p', Html::encode($testCheckTypes[$model->check_group_1]['text']),
                        ['class' => 'label label-'.$testCheckTypes[$model->check_group_1]['class'],
                         'title' => $testCheckTypes[$model->check_group_1]['title']

                        ]);
                }
                else{
                    $html = Html::tag('p', '--',
                        ['class' => 'label label-default',
                          'title'   => 'проверка не проводилась'

                        ]);
                }

                return $html;
            }
        ],
        'check_group_2' =>
        [
            'header' => 'Группа 2 <br> (на ложь)',
            'format' => 'raw',
            'value' => function($model) use ($testCheckTypes){

                if(isset($model->check_group_2))
                {
                    $html = Html::tag('p', Html::encode($testCheckTypes[$model->check_group_2]['text']),
                        ['class' => 'label label-'.$testCheckTypes[$model->check_group_2]['class'],
                            'title' => $testCheckTypes[$model->check_group_2]['title']
                        ]);
                }
                else{
                    $html = Html::tag('p', '--',
                        ['class' => 'label label-default',
                            'title'   => 'проверка не проводилась'

                        ]);
                }

                return $html;
            }
        ],
        'check_group_3' =>
        [
            'header' => 'Группа 3 <br> (неопределённость)',
            'format' => 'raw',
            'value' => function($model) use ($testCheckTypesForGroup3){

                if(isset($model->check_group_3))
                {
                    $html = Html::tag('p', Html::encode($testCheckTypesForGroup3[$model->check_group_3]['text']),
                        ['class' => 'label label-'.$testCheckTypesForGroup3[$model->check_group_3]['class'],
                            'title' => $testCheckTypesForGroup3[$model->check_group_3]['title']
                        ]);
                }
                else{
                    $html = Html::tag('p', '--',
                        ['class' => 'label label-default',
                            'title'   => 'проверка не проводилась'

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
                'view' => function($key){
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>','#',
                        [
                        'id' => 'activity-view-link',
                        'title' => Yii::t('yii', 'Просмотр таста'),
                        'data-toggle' => 'modal',
                        'data-target' => '#activity-modal',
                        'data-id' => $key,
                        'data-pjax' => '0',

                    ]);
                }

            ]
            //'format' => 'raw',
           // 'value' => '',
        ]

    ]
]);
?>
<?
Modal::begin([
'header' => '<h4 class="modal-title">Create New</b></h4>',
'toggleButton' => ['label' => 'Create New'],
'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
]);

echo 'Say hello...';

Modal::end();


?>

