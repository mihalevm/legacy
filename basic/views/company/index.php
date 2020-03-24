<?php

use yii\helpers\Html;
use yii\bootstrap\Button;
use yii\widgets\Pjax;
use yii\widgets\MaskedInput;

$this->title = 'Компании';
$this->params['breadcrumbs'][] = $this->title;

$field_type = \Yii::getAlias('@device') != 'desktop' ? 'number':'';
?>
<?= Html::textInput('coid', '0', ['hidden' => 'true']); ?>
    <div class="lgc_mainform">
        <label>Название: </label> <?= Html::textInput('coname', null, ['placeholder' => 'Название компании']); ?><br/>
        <label>Руководитель: </label> <?= Html::textInput('manager', null, ['placeholder' => 'ФИО руководителя']); ?><br/>
        <label>Контакты: </label> <?= Html::textarea('contacts', null, ['placeholder' => 'Описание']); ?><br/>
        <label>Активна: </label> <?= Html::checkbox('disabled', false); ?><br/>
        <div class="lgc_form_control">
            <span>
                <div style="width: 150px; display: table-cell"></div>
            </span>
            <span>
    <?= Button::widget(['label' => 'Сохранить','options' => ['name' => 'newcompanysave', 'class' => 'btn-sm btn-primary', 'onclick' => 'company.save()',],]);?>
            </span>
        </div>
        <br/>
<?php
    Pjax::begin(['id' => 'company_list', 'timeout' => false, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST']]);
    echo \yii\grid\GridView::widget([
        'dataProvider' => $allCompanyList,
        'layout' => "{items}<div align='right'>{pager}</div>",
        'rowOptions' => function ($model, $key, $index, $grid) {
            return [
                'title'         => $model['disabled'] === 'Y' ? 'Удален':'Активен',
                'class'         => $model['disabled'] === 'Y' ? 'lgc_company_disabled':'lgc_company_enabled',
                'data-coid'     => $model['coid'],
                'data-name'     => $model['name'],
                'data-manager'  => $model['manager'],
                'data-contacts' => $model['contacts'],
                'data-disabled' => $model['disabled'],
                'onclick'       => 'company.setActiveItem(this);'
            ];
        },
        'columns' => [
            [
                'format' => 'text',
                'attribute'=>'coid',
                'label'=>'№',
            ],
            [
                'format' => 'ntext',
                'attribute'=>'name',
                'label'=>'Название',
            ],
            [
                'format' => 'ntext',
                'attribute'=>'manager',
                'label'=>'Руководитель',
            ],
            [
                'format'      => 'raw',
                'headerOptions' => ['style'=>'text-align:center;',],
                'label'       => '<i class="fa fa-cog" aria-hidden="true"></i>',
                'encodeLabel' => false,
                'value'       => function($data){
                    return $data['disabled'] == 'Y' ? '<i class="fa fa-ban" aria-hidden="true"/>': '<i class="fa fa-check" aria-hidden="true"/>';
                }

            ],
        ],
    ]);
   Pjax::end();
?>
</div>

<div class="modal fade" id="editTransaction" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Редактирование покупки</h4>
            </div>
            <div class="modal-body">
                <div class="lgc_tform">
                    <div><label>Дата покупки:</label><?=Html::textInput('pay_date', '', ['disabled' => 'true', "class" => "lgc_ro_input"]); ?></div>
                    <div><label>Сумма покупки:</label><?=MaskedInput::widget(['name' => 'summ','mask' => '999999', 'options'=>['type'=>$field_type]]); ?></div>
                    <div><label>Бонусных баллов:</label><?=MaskedInput::widget(['name' => 'bsumm','mask' => '999999', 'options'=>['type'=>$field_type]]); ?></div>
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
                <button type="button" class="btn btn-success" onclick="transaction.save()">Сохранить</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirm_delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body" style="text-align: center">
                Удалить оплату ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger"  data-dismiss="modal" onclick="transaction.delete()">Удалить</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>