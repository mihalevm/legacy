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
        <label>Расчет: </label><?= Html::dropDownList('paytype', 0, $paytype) ?><br/>
        <label>Контакты: </label> <?= Html::textarea('contacts', null, ['placeholder' => 'Описание']); ?><br/>
        <label>Заблокирована: </label> <?= Html::checkbox('disabled', false); ?><br/>
        <div class="lgc_form_control">
            <span style="float: right">
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
                'data-ptype'    => $model['ptype'],
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
                'attribute'=>'ptypename',
                'label'=>'Расчет',
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