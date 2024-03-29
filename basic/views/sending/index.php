<?php

use yii\helpers\Html;
use yii\bootstrap\Button;
use yii\widgets\Pjax;
use kartik\date\DatePicker;
use dosamigos\multiselect\MultiSelect;


$this->title = 'СМС рассылки';
$this->params['breadcrumbs'][] = $this->title;

?>
<div>
    <div class="lgc_mainform">
        <div class="lgc_download_bar">
            <label>Скачать программу рассылки</label>
            <a href="/download/sender.apk" class="btn-sm btn-primary btn-lg active" role="button" aria-pressed="true" style="float: right;" target="_blank">Скачать</a>
        </div>
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
                    'attribute'=>'ucnt',
                    'label'=>'Клиентов',
                    'contentOptions' => ['style'=>'text-align:center'],
                ],
                [
                    'format' => 'ntext',
                    'attribute'=>'prc',
                    'label'=>'Статус(%)',
                    'contentOptions' => ['style'=>'text-align:center'],
                ],
                [
                    'format'      => 'raw',
                    'headerOptions' => ['style'=>'text-align:center;',],
                    'label'       => '<i class="fa fa-cog" aria-hidden="true"></i>',
                    'encodeLabel' => false,
                    'value'       => function($data){
                        return '<div class="lgc_tedit" title="Редактирование рассылки" onclick="sending.edit('.$data['slid'].')"><i class="fa fa-edit" aria-hidden="true"></i></div><div class="lgc_tedit" title="Перезапуск" onclick="sending.show_confirm_dialog_restart('.$data['slid'].')"><i class="fa fa-sync" aria-hidden="true"></i></div><div class="lgc_tedit" title="Удаление рассылки" onclick="sending.show_confirm_dialog('.$data['slid'].')"><i class="fa fa-times" aria-hidden="true"></i></div>';
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
                        <div><label>Точки продажи:</label>
                            <?php
                            echo MultiSelect::widget([
                                    "id"=>"sell_point",
                                    "options" => ['multiple'=>"multiple", 'class'=>'input-group'],
                                    'data' => ['1' => 'ТРЦ Ракета', '0' => 'ТЦ ЦУМ', '2' => 'ТЦ Азия'],
                                    'value' => [],
                                    'name' => 'sell_points',
                                    "clientOptions" =>
                                        [
                                            "includeSelectAllOption" => true,
                                            'numberDisplayed' => 2
                                        ],
                            ]);
                            ?>
                        </div>
                        <div><label>Название:</label><?=Html::textInput('sname', '', ['placeholder' => 'Описание рассылки']); ?></div>
                        <div><label>Текст:</label><?=Html::textarea('message', '', ['placeholder' => 'Текст рассылки', 'onkeyup'=>'sending.smslengthcounter(this)']); ?></div>
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
    <div class="modal fade" id="confirm_restart" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body" style="text-align: center">
                    Сбросить статистику и перезапустить рассылку?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger"  data-dismiss="modal" onclick="sending.restart()">Перезапустить</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Отмена</button>
                </div>
            </div>
        </div>
    </div>
</div>
