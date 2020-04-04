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
    $totalSum   = 0;
    $totalDebit = 0;

    Pjax::begin(['id' => 'clients_list', 'timeout' => false, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST']]);
    echo \yii\grid\GridView::widget([
        'dataProvider' => $clientsSum,
        'showFooter' => true,
        'layout' => "{items}<div align='right'>{pager}</div>",
        'columns' => [
            [
                'format'      => 'raw',
                'label'       => 'Клиент',
                'encodeLabel' => false,
                'value'       => function($data){
                    return '<a href="/client-card?u='.$data['uid'].'">'.$data['fio'].'</a>';
                },
                'footer' => 'Итого:',
            ],
            [
                'format' => 'raw',
                'encodeLabel' => false,
                'label'=>'Кредитный баланс',
                'value' => function ($data, $key, $index, $widget) use (&$totalSum) {
                    $totalSum += floatval($data['cbalance']);
                    $widget->footer = $totalSum;

                    return $data['cbalance'];
                },
            ],
            [
                'format' => 'raw',
                'encodeLabel' => false,
                'label'=>'Сумма просроченных платежей',
                'value' => function ($data, $key, $index, $widget) use (&$totalDebit) {
                    $totalDebit += floatval($data['debitSum']);
                    $widget->footer = $totalDebit;
                    return $data['debitSum'];
                },
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