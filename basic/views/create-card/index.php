<?php

use yii\helpers\Html;
use yii\widgets\MaskedInput;
use yii\bootstrap\Button;

$this->title = 'Создание карт';
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="lgc_mainform">
<?php
    echo Html::label('Номер последний карты: '.$LastCard, null,['name'=>'last_num'] );
    echo '<br/>';
    echo Html::label('Начало диапазона:');
    echo MaskedInput::widget(['name' => 'sid','mask' => '9999',]);
    echo Html::label('Конец диапазона:');
    echo MaskedInput::widget(['name' => 'eid','mask' => '9999',]);
    echo Html::label('Стартовый баланс:');
    echo MaskedInput::widget(['name' => 'blnc','mask' => '9999999','value' => '0']);
    echo Html::label('Срок действия (0 безлимитна):');
    echo MaskedInput::widget(['name' => 'days','mask' => '9999', 'value' => '0']);
?>
        <div class="lgc_form_control">
            <span>
                <div style="width: 150px; display: table-cell"></div>
            </span>
            <span>
            <?= Button::widget(['label' => 'Создать','options' => ['class' => 'btn-sm btn-primary pull-right', 'onclick' => 'createcard.start()',],]);?>
            </span>
        </div>
    </div>
</div>
