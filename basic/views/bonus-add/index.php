<?php

use yii\helpers\Html;
use yii\widgets\MaskedInput;
use yii\bootstrap\Button;

$this->title = 'Зачисление бонусов';
$this->params['breadcrumbs'][] = [
        'template' => "<li>{link}</li><li>".$this->title."</li>\n",
        'label'    => "Клиент ".$client_params['fio'],
        'url'      => ['/client-card?u='.$client_params['uid']]
        ];

$field_type = \Yii::$app->devicedetect->isMobile() ? 'number':'';
?>
<div>
    <br/>
    <?= Html::textInput('uid', $client_params['uid'], ['hidden' => 'true']); ?><br/>
    <div class="lgc_mainform">
        <label>Номер бонусной карты: </label> <?= Html::textInput('cnum', $client_params['cnum'], ['disabled' => 'true', "class" => "lgc_ro_input"]); ?><br/>
        <label>Доступно бонусов: </label> <?= Html::textInput('cur_bcumm', $client_params['bsumm'], ['disabled' => 'true', "class" => "lgc_ro_input"]); ?><br/>
        <label>Сумма покупки: </label> <?=MaskedInput::widget(['name' => 'summ','mask' => '999999', 'options'=>['type'=>$field_type, 'onkeyup' => 'bonus.addcalc()']]); ?><br/>
        <label>Бонусный процент: </label> <?= Html::dropDownList('bprcnt', '5', ['5' => '5%', '10' => '10%', '15' => '15%', '20' => '20%'] , ['onchange' => 'bonus.addcalc()']) ?><br/>
        <label>Сумма бонусных баллов: </label> <?=MaskedInput::widget(['name' => 'bsumm','mask' => '999999', 'options'=>['type'=>$field_type]]); ?><br/>
        <label>Описание покупки: </label><?= Html::textInput('descr', '', ['placeholder' => 'Описание покупки']); ?><br/>
        <div class="lgc_form_control">
            <span>
                <div style="width: 150px; display: table-cell"></div>
            </span>
            <span>
        <?= Button::widget(['label' => 'Зачислить','options' => ['name' => 'addbonus', 'class' => 'btn-sm btn-warning pull-right', 'onclick' => 'bonus.add()',],]);?>
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
                <th scope="col">Тип</th>
                <th scope="col">Сумма</th>
                <th scope="col">Бонусы</th>
                <th scope="col">Описание</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($client_last_transaction as $item){
                $item['bsumm'] = $item['ttype'] == 'a' ? $item['bsumm'] : -$item['bsumm'];
                $order_ptype = '';
                if ($item['ttype'] == 'a' || $item['ttype'] == 's'){
                    $order_ptype = 'Бонусы';
                }
                if ($item['ttype'] == 'C'){
                    $order_ptype = 'Рассрочка';
                }
                if ($item['ttype'] == 'P'){
                    $order_ptype = 'Скидка';
                }

                echo '<tr><th scope="row">'.$item['tdate'].'</th><td>'.$order_ptype.'</td><td>'.$item['summ'].'</td><td>'.$item['bsumm'].'</td><td>'.$item['tdesc'].'</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
    <div id="list_empty" class="lgc_searchresult" style="text-align: center; display: <?=!$list_isnot_empty?'block':'none'?>">
        <label>Список покупок пуст.</label><br>
    </div>
</div>
