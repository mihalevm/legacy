<?php

use yii\helpers\Html;
use yii\widgets\MaskedInput;
use yii\bootstrap\Button;

$this->title = 'Создание клиента';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= Html::textInput('uid', null, ['hidden' => 'true']); ?><br/>
    <div class="lgc_mainform">
        <label>Номер бонусной карты: </label> <?= Html::textInput('cnum', $cnum, ['disabled' => 'true']); ?><br/>
        <label>Доступно бонусов: </label> <?= Html::textInput('cnum', $bblnc, ['disabled' => 'true']); ?><br/>
        <label>ФИО: </label> <?= Html::textInput('fio', null, ['placeholder' => 'ФИО Клиента']); ?><br/>
        <label>Номер телефона: </label> <?=MaskedInput::widget(['name' => 'phone','mask' => '9-999-999-99-99',]); ?><br/>
        <label>Дата рождения: </label><?= MaskedInput::widget(['name' => 'birth','mask' => '99.99.99',]);?><br/>
        <label>Пол: </label> <?= Html::dropDownList('sex', null, ['1' => 'Мужской', '0' => 'Женский']) ?><br/>
        <label>Стиль одежды: </label><?= Html::textInput('ctype', null, ['placeholder' => 'Тип одежды']); ?><br/>
        <label>Размер одежды: </label><?= Html::dropDownList('csize', null, $cSize) ?><br/>
        <label>Размер обуви: </label><?= Html::dropDownList('fsize', null, $fSize) ?><br/>
        <?= Button::widget(['label' => 'Списать','options' => ['disabled'=>'', 'name' => 'subbonus', 'class' => 'btn-sm btn-danger pull-right', 'onclick' => 'newclient.bonussub()',],]);?>
        <?= Button::widget(['label' => 'Сохранить','options' => ['name' => 'newusersave', 'class' => 'btn-sm btn-primary pull-right', 'onclick' => 'newclient.create()',],]);?>
        <?= Button::widget(['label' => 'Зачислить','options' => ['disabled'=>'', 'name' => 'addbonus', 'class' => 'btn-sm btn-warning pull-right', 'onclick' => 'newclient.bonusadd()',],]);?>
    </div>
</div>
