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
    $totalSum   = 0;
    $totalDebit = 0;

    Pjax::begin(['id' => 'company_list', 'timeout' => false, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST']]);
    echo \yii\grid\GridView::widget([
        'dataProvider' => $companySum,
        'showFooter' => true,
        'layout' => "{items}<div align='right'>{pager}</div>",
        'footerRowOptions' => ['class' => 'lgc_report_footer'],
        'columns' => [
            [
                'format' => 'raw',
                'encodeLabel' => false,
                'label'=>'№',
                'value' => function ($data, $key, $index, $widget) {
                    return $index+1;
                },
            ],
            [
                'format' => 'ntext',
                'attribute'=>'name',
                'label'=>'Название компании',
                'footer' => 'Итого:',
            ],
            [
                'format' => 'raw',
                'encodeLabel' => false,
                'label'=>'Кредитный баланс',
                'value' => function ($data, $key, $index, $widget) use (&$totalSum) {
                    $totalSum += floatval($data['totalSum']);
                    $widget->footer = $totalSum;

                    return $data['totalSum'];
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
        ],
    ]);
   Pjax::end();
?>
</div>