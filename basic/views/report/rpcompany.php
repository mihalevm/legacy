<?php

use yii\widgets\Pjax;

$this->title = 'Отчет по компаниям';
$this->params['breadcrumbs'][] = [
    'template' => "<li>{link}</li><li>".$this->title."</li>\n",
    'label'    => "Отчеты",
    'url'      => ['/report']
];
?>
<div>
<?php
    Pjax::begin(['id' => 'company_list', 'timeout' => false, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST']]);
    echo \yii\grid\GridView::widget([
        'dataProvider' => $companySum,
        'layout' => "{items}<div align='right'>{pager}</div>",
        'rowOptions' => function ($model, $key, $index, $grid) {
            return [
            ];
        },
        'columns' => [
            [
                'format' => 'ntext',
                'attribute'=>'name',
                'label'=>'Название компании',
            ],
            [
                'format' => 'ntext',
                'attribute'=>'totalSum',
                'label'=>'Кредит',
            ],
            [
                'format' => 'ntext',
                'attribute'=>'debitSum',
                'label'=>'Сумма просроченных платежей',
            ],
        ],
    ]);
   Pjax::end();
?>
</div>