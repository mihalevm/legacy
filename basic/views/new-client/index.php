<?php

use yii\helpers\Html;
use yii\widgets\MaskedInput;
use yii\bootstrap\Button;
use yii\jui\DatePicker;

$this->title = 'Создание клиента';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="lgc_mainform">
        <?= Html::textInput('uid', null, ['hidden' => 'true']); ?><br/>
        <label>ФИО: </label> <?= Html::textInput('fio', null, ['placeholder' => 'ФИО Клиента']); ?><br/>
        <label>Номер телефона: </label> <?= Html::textInput('phone', null, ['placeholder' => '7-903-111-11-11']); ?><br/>
        <label>Дата рождения: </label><?=DatePicker::widget(['name'=>'birth','model' => $model, 'language' => 'ru', 'dateFormat' => 'yyyy-MM-dd',]); ?><br/>
        <label>Пол: </label> <?= Html::dropDownList('atype', null, ['0' => 'Мужской', '1' => 'Женский']) ?><br/>
        <label>Стиль одежды: </label><?= Html::textInput('ctype', null, ['placeholder' => 'Тип одежды']); ?><br/>
        <label>Размер одежды: </label><?= Html::dropDownList('csize', null, $cSize) ?><br/>
        <label>Размер обуви: </label><?= Html::dropDownList('fsize', null, $fSize) ?><br/>
<!--        Номер карты (поле выбора)-->
<!--        Бонусный баланс (отображаемое поле)-->
        <?php
    echo Button::widget([
        'label' => 'Создать',
        'options' => ['class' => 'btn-lg pull-right', 'onclick' => 'createcard.start()',],
    ]);

?>
    </div>
</div>
