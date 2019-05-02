<?php

use yii\helpers\Html;
use yii\widgets\MaskedInput;
use yii\bootstrap\Button;

$this->title = 'Редактирование клиента';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= Html::textInput('uid', $client_params['uid'], ['hidden' => 'true']); ?><br/>
    <div class="lgc_mainform">
        <label>Номер бонусной карты: </label> <?= Html::textInput('cnum', $client_params['cnum'], ['disabled' => 'true']); ?><br/>
        <label>Доступно бонусов: </label> <?= Html::textInput('cnum', $client_params['bsumm'], ['disabled' => 'true']); ?><br/>
        <label>ФИО: </label> <?= Html::textInput('fio', $client_params['fio'], ['placeholder' => 'ФИО Клиента']); ?><br/>
        <label>Номер телефона: </label> <?=MaskedInput::widget(['name' => 'phone','mask' => '9-999-999-99-99', 'value'=>$client_params['phone']]); ?><br/>
        <label>Дата рождения: </label><?= MaskedInput::widget(['name' => 'birth','mask' => '99.99.99','value'=>$client_params['birthday']]);?><br/>
        <label>Пол: </label> <?= Html::dropDownList('sex', $client_params['sex'], ['1' => 'Мужской', '0' => 'Женский']) ?><br/>
        <label>Стиль одежды: </label><?= Html::textInput('ctype', $client_params['style'], ['placeholder' => 'Тип одежды']); ?><br/>
        <label>Размер одежды: </label><?= Html::dropDownList('csize', $client_params['did'], $cSize) ?><br/>
        <label>Размер обуви: </label><?= Html::dropDownList('fsize', $client_params['fid'], $fSize) ?><br/>

        <?= Button::widget(['label' => 'Сохранить','options' => ['name' => 'newusersave', 'class' => 'btn-sm btn-primary pull-right', 'onclick' => 'newclient.create()',],]);?>
        <?= Button::widget(['label' => 'Списать','options' => ['name' => 'subbonus', 'class' => 'btn-sm btn-danger pull-right', 'onclick' => 'newclient.bonussub()',],]);?>
        <?= Button::widget(['label' => 'Покупки','options' => ['name' => 'newusersave', 'class' => 'btn-sm btn-primary pull-right', 'onclick' => '',],]);?>
        <?= Button::widget(['label' => 'Зачислить','options' => ['name' => 'addbonus', 'class' => 'btn-sm btn-warning pull-right', 'onclick' => 'newclient.bonusadd()',],]);?>
    </div>
</div>
