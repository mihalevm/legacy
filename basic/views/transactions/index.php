<?php

use yii\helpers\Html;
use yii\bootstrap\Button;
use yii\widgets\Pjax;
use kartik\date\DatePicker;

$this->title = 'История покупок';
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
    <h1><?= Html::encode($this->title) ?></h1>
    <?= Html::textInput('uid', $client_params['uid'], ['hidden' => 'true']); ?><br/>
    <div class="lgc_mainform">
        <label>Клиент:</label>
        <?= Html::textInput('cnum', $client_params['fio'], ['disabled' => 'true', "class" => "lgc_ro_input"]); ?>
        <div class="filter">
<?php
    echo '<label class="control-label">Начало периода</label>';
    echo DatePicker::widget([
        'model' => $model,
        'attribute' => 'sdate',
        'options' => ['placeholder' => 'Начало периода','value' => date('01.m.Y', strtotime(date('Y-m-d'))),],
        'type' => DatePicker::TYPE_COMPONENT_APPEND,
        'removeButton' => false,
        'pluginOptions' => [
            'format' => 'dd.mm.yyyy',
            'orientation' => 'bottom right',
            'autoclose'=>true,
            'todayHighlight' => true,
        ]
    ]);

    echo '<label class="control-label">Конец периода</label>';
    echo DatePicker::widget([
        'model' => $model,
        'attribute' => 'edate',
        'options' => ['placeholder' => 'Конец периода','value' => date('t.m.Y', strtotime(date('Y-m-d'))),],
        'type' => DatePicker::TYPE_COMPONENT_APPEND,
        'removeButton' => false,
        'pluginOptions' => [
            'format' => 'dd.mm.yyyy',
            'orientation' => 'bottom right',
            'autoclose'=>true,
            'todayHighlight' => true,
        ]
    ]);
?>
        <?=Button::widget(['label' => 'Обновить','options' => ['class' => 'btn-sm btn-primary pull-right', 'onclick' => 'transaction.refresh()',],]);?>
        </div>
<?php
    Pjax::begin(['id' => 'transactions_list', 'timeout' => false, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST']]);
    echo \yii\grid\GridView::widget([
        'dataProvider' => $client_transactions,
        'layout' => "{items}<div align='right'>{pager}</div>",
        'rowOptions' => function ($model, $key, $index, $grid) {
            return [
/*
                'class'      => $index&1 ? 'tg-attr-item-one':'tg-attr-item-two',
                'data-aid'   => $model['aid'],
                'data-atype'   => $model['atype'],
                'data-aname' => $model['aname'],
                'data-adesc' => $model['adesc'],
                'data-title' => $model['title'],
                'data-test'  => $model['test'],
                'onclick'    => 'attreditor.setActiveItem(this);'
tdate, summ, bsumm, tdesc, ttype
*/
            ];
        },
        'columns' => [
            [
                'format' => 'ntext',
                'attribute'=>'tdate',
                'label'=>'Дата',
            ],
            [
                'format' => 'ntext',
                'attribute'=>'summ',
                'label'=>'Сумма покупки',
            ],
            [
                'format' => 'ntext',
                'attribute'=>'bsumm',
                'label'=>'Бонусы',
            ],
            [
                'format' => 'ntext',
                'attribute'=>'tdesc',
                'label'=>'Описание',
            ]
        ],
    ]);
   Pjax::end();
?>
    </div>
</div>
