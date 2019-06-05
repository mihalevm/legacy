<?php

use yii\helpers\Html;
use yii\widgets\MaskedInput;
use yii\bootstrap\Button;

$this->title = 'Списание бонусов';
$this->params['breadcrumbs'][] = [
    'template' => "<li>{link}</li><li>".$this->title."</li>\n",
    'label'    => 'Клиент '.$client_params['fio'],
    'url'      => ['/client-card?u='.$client_params['uid']]
];

$field_type = \Yii::getAlias('@device') != 'desktop' ? 'number':'';
?>
<div>
    <br/>
    <?= Html::textInput('uid', $client_params['uid'], ['hidden' => 'true']); ?><br/>
    <div class="lgc_mainform">
        <label>Номер бонусной карты: </label> <?= Html::textInput('cnum', $client_params['cnum'], ['disabled' => 'true', "class" => "lgc_ro_input"]); ?><br/>
        <label>Доступно бонусов: </label> <?= Html::textInput('cur_bcumm', $client_params['bsumm'], ['disabled' => 'true', "class" => "lgc_ro_input"]); ?><br/>
        <label>Сумма к оплате: </label> <?= Html::textInput('pay_summ', '0', ['disabled' => 'true', "class" => "lgc_ro_input"]); ?><br/>
        <label>Сумма покупки: </label> <?=MaskedInput::widget(['name' => 'summ','mask' => '999999', 'options'=>['type'=>$field_type, 'onkeyup' => 'bonus.subcalc()']]); ?><br/>
        <label>Сумма бонусных баллов(<20%): </label> <?=MaskedInput::widget(['name' => 'bsumm','mask' => '999999', 'options'=>['type'=>$field_type, 'onkeyup' => 'bonus.payCalcSub()']]); ?><br/>
        <label>Описание покупки: </label><?= Html::textInput('descr', '', ['placeholder' => 'Описание покупки']); ?><br/>
        <div class="lgc_form_control">
            <span>
                <div style="width: 150px; display: table-cell"></div>
            </span>
            <span>
        <?= Button::widget(['label' => 'Списать','options' => ['name' => 'addbonus', 'class' => 'btn-sm btn-danger pull-right', 'onclick' => 'bonus.sub()',],]);?>
            </span>
        </div>
    </div>
    <?php
    $list_isnot_empty = sizeof($client_last_transaction)>0;
    ?>
    <div id="list_transaction" class="lgc_searchresult" style="display: <?=$list_isnot_empty?'block':'none'?>">
        <label>Последние покупки клиента</label><br>
        <table class="table table-hover lgc_searchresulttable lgc_searchresulttable_m">
            <thead>
            <tr>
                <th scope="col">Дата</th>
                <th scope="col">Сумма</th>
                <th scope="col">Бонусы</th>
                <th scope="col">Описание</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($client_last_transaction as $item){
                $item['bsumm'] = $item['ttype'] == 'a' ? $item['bsumm'] : -$item['bsumm'];
                echo '<tr><th scope="row">'.$item['tdate'].'</th><td>'.$item['summ'].'</td><td>'.$item['bsumm'].'</td><td>'.$item['tdesc'].'</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
    <div id="list_empty" class="lgc_searchresult" style="text-align: center; display: <?=!$list_isnot_empty?'block':'none'?>">
        <label>Список покупок пуст.</label><br>
    </div>
</div>
