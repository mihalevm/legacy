<?php

use yii\helpers\Html;
use yii\widgets\MaskedInput;
use yii\bootstrap\Button;

$this->title = 'Создание клиента';
$this->params['breadcrumbs'][] = $this->title;

?>
<div>
    <br/>
    <?= Html::textInput('uid', null, ['hidden' => 'true']); ?><br/>
    <div class="lgc_mainform">
        <label>Номер бонусной карты: </label><?=MaskedInput::widget(['name' => 'cnum','mask' => '99999','options'=>[ 'onkeyup'=>'newclient.newcard()']]);?><br/>
        <label>Доступно бонусов: </label><?=MaskedInput::widget(['name' => 'bblnc','mask' => '999999', 'value'=>'0']);?><br/>
        <label>ФИО: </label> <?= Html::textInput('fio', null, ['placeholder' => 'ФИО Клиента']); ?><br/>
        <label>Номер телефона: </label> <?=MaskedInput::widget(['name' => 'phone','mask' => '9-999-999-99-99',]); ?><br/>
        <label>Дата рождения: </label><?= MaskedInput::widget(['name' => 'birth','mask' => '99.99.9999',]);?><br/>
        <label>Пол: </label> <?= Html::dropDownList('sex', null, ['1' => 'Мужской', '0' => 'Женский']) ?><br/>
        <label>Стиль одежды: </label><?= Html::textInput('ctype', null, ['placeholder' => 'Тип одежды']); ?><br/>
        <label>Размер одежды: </label><?= Html::dropDownList('csize', null, $cSize) ?><br/>
        <label>Размер обуви: </label><?= Html::dropDownList('fsize', null, $fSize) ?><br/>
        <div class="lgc_form_control">
            <span>
                <div style="width: 150px; display: table-cell"></div>
            </span>
            <span>
        <?= Button::widget(['label' => 'Сохранить','options' => ['name' => 'newusersave', 'class' => 'btn-sm btn-primary', 'onclick' => 'newclient.create()',],]);?>
            </span>
        </div>
        <div class="lgc_form_control">
            <span>
        <?= Button::widget(['label' => 'Списать','options' => ['disabled'=>'', 'name' => 'subbonus', 'class' => 'btn-sm btn-danger', 'onclick' => 'newclient.bonussub()',],]);?>
            </span>
            <span>
        <?= Button::widget(['label' => 'Зачислить','options' => ['disabled'=>'', 'name' => 'addbonus', 'class' => 'btn-sm btn-warning', 'onclick' => 'newclient.bonusadd()',],]);?>
            </span>
        </div>
    </div>
</div>
