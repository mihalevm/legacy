<?php

use yii\helpers\Html;
use yii\bootstrap\Button;
use yii\widgets\Pjax;
use kartik\date\DatePicker;

$this->title = 'СМС рассылки';
$this->params['breadcrumbs'][] = $this->title;

?>
<div>
    <div class="lgc_mainform">
        <?php
        Pjax::begin(['id' => 'sending_list', 'timeout' => false, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST']]);
        echo \yii\grid\GridView::widget([
            'dataProvider' => $sending_list,
            'layout' => "{items}<div align='right'>{pager}</div>",
            'rowOptions' => function ($model, $key, $index, $grid) {
                return [
                ];
            },
            'columns' => [
                [
                    'format'=>'ntext',
                    'attribute'=>'sdate',
                    'label'=>'Дата начала',
                ],
                [
                    'format' => 'ntext',
                    'attribute'=>'sname',
                    'label'=>'Название',
                ],
                [
                    'format' => 'ntext',
                    'attribute'=>'prc',
                    'label'=>'Статус(%)',
                ],
                [
                    'format'      => 'raw',
                    'headerOptions' => ['style'=>'text-align:center;',],
                    'label'       => '<i class="fa fa-cog" aria-hidden="true"></i>',
                    'encodeLabel' => false,
                    'value'       => function($data){
                        return '<div class="lgc_tedit" title="Редактирование рассылки" onclick="sending.edit('.$data['slid'].')"><i class="fa fa-edit" aria-hidden="true"></i></div><div class="lgc_tedit" title="Удаление рассылки" onclick="sending.show_confirm_dialog('.$data['slid'].')"><i class="fa fa-times" aria-hidden="true"></i></div>';
                    }
                ],
            ],
        ]);
        Pjax::end();
        ?>
        <div class="lgc_form_control" style="text-align: right">
            <span>
                <?=Button::widget(['label' => 'Создать','options' => ['class' => 'btn-sm btn-success', 'onclick' => 'sending.create()',],]);?>
            </span>
            <span>
                <?=Button::widget(['label' => 'Обновить','options' => ['class' => 'btn-sm btn-primary', 'onclick' => 'sending.refresh()',],]);?>
            </span>
        </div>
    </div>

    <div class="modal fade" id="editSendItem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Редактирование рассылки</h4>
                </div>
                <div class="modal-body">
                    <div class="lgc_tform">
                        <div><label>Дата начала:</label>
                            <?php
                            echo DatePicker::widget([
                                'model' => $model,
                                'attribute' => 'sdate',
                                'options' => ['placeholder' => 'Начало периода','value'=>'',],
                                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                'removeButton' => false,
                                'pluginOptions' => [
                                    'format' => 'dd.mm.yyyy',
                                    'orientation' => 'bottom left',
                                    'autoclose'=>true,
                                    'todayHighlight' => true,
                                ]
                            ]);
                            ?>
                        </div>
                        <div><label>Название:</label><?=Html::textInput('sname', '', ['placeholder' => 'Описание рассылки']); ?></div>
                        <div><label>Текст:</label><?=Html::textarea('message', '', ['placeholder' => 'Текст рассылки', 'onkeydown'=>'sending.smslengthcounter(this)']); ?></div>
                        <div><label>Кол-во СМС(смс/симв):</label><span id="text_count">0/0</span></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="sending.save()">Сохранить</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Отмена</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirm_delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body" style="text-align: center">
                    Удалить рассылку ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger"  data-dismiss="modal" onclick="sending.delete()">Удалить</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Отмена</button>
                </div>
            </div>
        </div>
    </div>
</div>
