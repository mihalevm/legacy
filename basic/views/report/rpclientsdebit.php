<?php

use yii\widgets\Pjax;

$this->title = 'Балансы и задолженности клиентов компаний';
$this->params['breadcrumbs'][] = [
    'template' => "<li>{link}</li><li>".$this->title."</li>\n",
    'label'    => "Отчеты",
    'url'      => ['/report']
];
?>
<div>
<?php
    Pjax::begin(['id' => 'clients_list', 'timeout' => false, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST']]);
    echo \yii\grid\GridView::widget([
        'dataProvider' => $clientsSum,
        'layout' => "{items}<div align='right'>{pager}</div>",
        'rowOptions' => function ($model, $key, $index, $grid) {
            return [
            ];
        },
        'columns' => [
            [
                'format'      => 'raw',
                'label'       => 'Клиент',
                'encodeLabel' => false,
                'value'       => function($data){
                    return '<a href="/client-card?u='.$data['uid'].'">'.$data['fio'].'</a>';
                }
            ],
            [
                'format' => 'ntext',
                'attribute'=>'cbalance',
                'label'=>'Кредитный баланс',
            ],
            [
                'format' => 'ntext',
                'attribute'=>'debitSum',
                'label'=>'Сумма просроченных платежей',
            ],
            [
                'format' => 'ntext',
                'attribute'=>'name',
                'label'=>'Компания',
            ],
        ],
    ]);
   Pjax::end();
?>
</div>