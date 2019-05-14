<?php

use yii\helpers\Html;
use yii\bootstrap\Button;
use yii\widgets\Pjax;
use kartik\date\DatePicker;
use yii\widgets\MaskedInput;

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
                'value' => function($data){
                    return $data['ttype'] == 's' ? -$data['bsumm']:$data['bsumm'];
                }
            ],
            [
                'format' => 'ntext',
                'attribute'=>'tdesc',
                'label'=>'Описание',
            ],
            [
                'format'      => 'raw',
                'label'       => '<i class="fa fa-cog" aria-hidden="true"></i>',
                'encodeLabel' => false,
                'value'       => function($data){
                    return '<div class="lgc_tedit" onclick="transaction.edit('.$data['tid'].')"><i class="fa fa-edit" aria-hidden="true"></i></div>';
                }

            ],
        ],
    ]);
   Pjax::end();
?>
    </div>
</div>

<div class="modal fade" id="editTransaction" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Редактирование покупки</h4>
            </div>
            <div class="modal-body">
                <div class="lgc_tform">
                    <div><label>Дата покупки:</label><?=Html::textInput('pay_date', '', ['disabled' => 'true', "class" => "lgc_ro_input"]); ?></div>
                    <div><label>Сумма покупки:</label><?=MaskedInput::widget(['name' => 'summ','mask' => '999999']); ?></div>
                    <div><label>Бонусных баллов:</label><?=MaskedInput::widget(['name' => 'bsumm','mask' => '999999']); ?></div>
                    <div><label>Описание покупки:</label><?=Html::textInput('descr', '', ['placeholder' => 'Описание покупки']); ?></div>
                    <div class="lgc_ttype">
                        <label>Бонусы:</label>
                        <div class="radio">
                            <label><input type="radio" name="bonus_op" value="a" checked>Зачисление</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="bonus_op" value="s">Списание</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" onclick="transaction.save()">Сохранить</button>
            </div>
        </div>
    </div>
</div>