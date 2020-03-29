<?php

use yii\helpers\Html;
use yii\widgets\MaskedInput;
use kartik\date\DatePicker;
use yii\bootstrap\Button;

$this->title = 'Покупка в рассрочку';
$this->params['breadcrumbs'][] = [
        'template' => "<li>{link}</li><li>".$this->title."</li>\n",
        'label'    => "Клиент ".$client_params['fio'],
        'url'      => ['/client-card?u='.$client_params['uid']]
        ];

$field_type = \Yii::getAlias('@device') != 'desktop' ? 'number':'';
?>
<div>
    <br/>
    <?= Html::textInput('uid', $client_params['uid'], ['hidden' => 'true']); ?><br/>
    <div class="lgc_mainform">
        <label>Рекомендованый тип платежа: </label><?= Html::textInput('paytype', $client_params['paytype'], ['disabled' => 'true', "class" => "lgc_ro_input"]); ?><br/>
        <label>Кредитный баланс:<?php if (floatval($client_params['cbalance']) > 0) {echo('<i class="fa fa-exclamation-triangle lgc_hint_warning" aria-hidden="true"></i>');};?></label> <?= Html::textInput('cblnc', $client_params['cbalance'], ['disabled' => 'true', "class" => "lgc_ro_input"]); ?><br/>
        <label>Сумма покупки: </label> <?=MaskedInput::widget(['name' => 'summ','mask' => '999999', 'options'=>['type'=>$field_type, 'onkeyup' => 'bonus.addcalc()']]); ?><br/>
        <label>Первоначальный взнос: </label> <?=MaskedInput::widget(['name' => 'firstPay','mask' => '999999','value' => '0', 'options'=>['type'=>$field_type,]]); ?><br/>
        <label>Описание покупки: </label><?= Html::textInput('descr', '', ['placeholder' => 'Описание покупки']); ?><br/>
        <div class="lgc_form_control">
            <span>
        <?php if ($client_params['pay_period'] > 0) {echo (Button::widget(['label' => 'Погашение','options' => ['name' => 'addpay', 'class' => 'btn-sm btn-success', 'onclick' => '', 'data-toggle' =>'modal', 'data-target' => '#addpayModal',],]));};?>
            </span>
            <span>
        <?= Button::widget(['label' => 'Скидка','options' => ['name' => 'sale', 'class' => 'btn-sm btn-primary', 'onclick' => 'bonus.add()',],]);?>
            </span>
            <span>
        <?= Button::widget(['label' => 'Кредит','options' => ['name' => 'credit', 'class' => 'btn-sm btn-danger', 'onclick' => 'ctransaction.creditCalculatorShow()',],]);?>
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

<div class="modal fade" id="creditCalculateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Расчет графика платежей</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="lgc_table">
                    <div class="lgc_column_element">
                        <label>Сумма:</label><br/>
                        <?= Html::textInput('creditSumm', 0, ['disabled' => 'true']); ?>
                    </div>
                    <div class="lgc_column_element">
                        <label>Дата оплаты:</label><br/>
                        <?=DatePicker::widget(
                            ['model' => $model,
                             'attribute' => 'sdate',
                             'options' => ['placeholder' => 'Дата оплаты',
                                            'value' => date('01.m.Y', strtotime(date('Y-m-d'))),
                                            'onchange' => "ctransaction.creditListItems()",
                                 ],
                             'type' => DatePicker::TYPE_COMPONENT_APPEND,
                             'removeButton' => false,
                             'pluginOptions' => [
                                'format' => 'dd.mm.yyyy',
                                'orientation' => 'bottom left',
                                'autoclose'=>true,
                                'todayHighlight' => true,
                            ],
                            ]);?>
                    </div>
                    <div class="lgc_column_element">
                        <label>Длительность(мес.): </label><br/> <?= Html::dropDownList('months', 1, ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'], ['onchange'=>'ctransaction.creditListItems()']) ?><br/>
                    </div>
                </div>
                <table id="paymentPeriod" class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">№</th>
                        <th scope="col">Дата</th>
                        <th scope="col">Сумма</th>
                        <th scope="col">Остаток</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <?php if ($client_params['pay_period'] > 0) {echo('<div class="lgc_credit_exist_warn"><label>Есть не погашенная задолженность</label></div>');}?>
                <button type="button" class="btn btn-primary" onclick="ctransaction.saveCreditPeriods()">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addpayModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body" style="text-align: center">
                Оплата кредита
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Сохранить</button>
            </div>
        </div>
    </div>
</div>
