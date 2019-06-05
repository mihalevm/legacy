<?php

use yii\helpers\Html;
use yii\widgets\MaskedInput;
use yii\bootstrap\Button;

$this->title = 'Редактирование клиента';
$this->params['breadcrumbs'][] = $this->title;

$field_type_phone = '';
$field_type_birth = '';

if (\Yii::getAlias('@device') != 'desktop') {
    $field_type_phone = 'number';
    $field_type_birth = 'text';

    $dates = str_split($client_params['birthday'], 2);
    $client_params['birthday'] = $dates[0].'.'.$dates[1].'.'.$dates[2].$dates[3];
}
?>
<div>
    <br/>
    <?= Html::textInput('uid', $client_params['uid'], ['hidden' => 'true']); ?><br/>
    <div class="lgc_mainform">
        <div class="lgc_deleteclient"><label>Удалить клиента</label><i class="fa fa-times-circle" aria-hidden="true" data-toggle="modal" data-target="#confirm_delete"></i></div>
        <label>Номер бонусной карты: </label> <?= Html::textInput('cnum', $client_params['cnum'], ['disabled' => 'true', "class" => "lgc_ro_input"]); ?><br/>
        <label>Доступно бонусов: </label> <?= Html::textInput('cnum', $client_params['bsumm'], ['disabled' => 'true', "class" => "lgc_ro_input"]); ?><br/>
        <label>ФИО: </label> <?= Html::textInput('fio', $client_params['fio'], ['placeholder' => 'ФИО Клиента']); ?><br/>
        <label>Номер телефона: </label> <?=MaskedInput::widget(['name' => 'phone','mask' => '9-999-999-99-99', 'value'=>$client_params['phone'], 'options'=>['placeholder'=>'7-9XX-XXX-XX-XX', 'type'=>$field_type_phone]]); ?><br/>
        <label>Дата рождения: </label><?= MaskedInput::widget(['name' => 'birth','mask' => '99.99.9999','value'=>$client_params['birthday'], 'options'=>['placeholder'=>'XX.XX.XXXX', 'type'=>$field_type_birth]]);?><br/>
        <label>Пол: </label> <?= Html::dropDownList('sex', $client_params['sex'], ['1' => 'Мужской', '0' => 'Женский']) ?><br/>
        <label>Стиль одежды: </label><?= Html::textInput('ctype', $client_params['style'], ['placeholder' => 'Тип одежды','list' => 'dress_style']); ?><br/>
        <label>Размер одежды: </label><?= Html::dropDownList('csize', $client_params['did'], $cSize) ?><br/>
        <label>Размер обуви: </label><?= Html::dropDownList('fsize', $client_params['fid'], $fSize) ?><br/>

        <div class="lgc_form_control">
            <span>
        <?= Button::widget(['label' => 'Покупки','options' => ['name' => 'newusersave', 'class' => 'btn-sm btn-primary', 'onclick' => 'newclient.transactions()',],]);?>
            </span>
            <span>
        <?= Button::widget(['label' => 'Сохранить','options' => ['name' => 'newusersave', 'class' => 'btn-sm btn-primary', 'onclick' => 'newclient.create()',],]);?>
            </span>
        </div>
        <div class="lgc_form_control">
            <span>
        <?= Button::widget(['label' => 'Списать','options' => ['name' => 'subbonus', 'class' => 'btn-sm btn-danger', 'onclick' => 'newclient.bonussub()',],]);?>
            </span>
            <span>
        <?= Button::widget(['label' => 'Зачислить','options' => ['name' => 'addbonus', 'class' => 'btn-sm btn-warning', 'onclick' => 'newclient.bonusadd()',],]);?>
            </span>
        </div>
    </div>
</div>

<div class="modal fade" id="confirm_delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body" style="text-align: center">
                Удалить клиента ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger"  data-dismiss="modal" onclick="newclient.delete_client()">Удалить</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>

<datalist id="dress_style">
    <?php
    foreach ($dStyle as $item){
        echo '<option value="'.$item['style'].'">';
    }
    ?>
</datalist>
