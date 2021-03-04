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
        <?= Button::widget(['label' => 'Платежи','options' => ['name' => 'pays', 'class' => 'btn-sm btn-primary', 'onclick' => 'ctransaction.showPaysModal()',],]);?>
            </span>
            <span>
        <?php if ($client_params['pay_period'] > 0) {echo (Button::widget(['label' => 'Погашение','options' => ['name' => 'addpay', 'class' => 'btn-sm btn-success', 'onclick' => 'ctransaction.showAddPayModal()',],]));};?>
            </span>
            <span>
        <?= Button::widget(['label' => 'Кредит','options' => ['name' => 'credit', 'class' => 'btn-sm btn-danger', 'onclick' => 'ctransaction.creditCalculatorShow()',],]);?>
            </span>
            <span>
        <?= Button::widget(['label' => 'Проверка ФССП','options' => ['name' => 'fssp_check', 'class' => 'btn-sm btn-warning', 'onclick' => 'ctransaction.fsspCheckShow()',],]);?>
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
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Погашение платежей по рассрочке</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="paymentPeriodForPay" class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">№</th>
                        <th scope="col">Дата</th>
                        <th scope="col">Сумма</th>
                        <th scope="col">Остаток</th>
                        <th scope="col">Статус</th>
                        <th scope="col"><i class="fa fa-cog" aria-hidden="true"></i></th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot class="lgc_subtotalpay"><th colspan="2">Задолженность:</th><th id="totalPostDue">0</th><th>К оплате:</th><th colspan="2"><?=MaskedInput::widget(['name' => 'totalForPay','mask' => '999999','value' => '0', 'options' => ['onkeyup'=>'ctransaction.calculateCustomPaySum()']]); ?></th></tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" name="bnt_addpay" class="btn btn-primary" onclick="ctransaction.AddPayments()">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paysModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">История платежей по рассрочке</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="lgc_column_element_right">
                    <label>По:</label>
                    <?=DatePicker::widget(
                        ['model' => $model,
                            'attribute' => 'pays_edate',
                            'options' => ['placeholder' => 'Конец периода',
                                'value' => date('d.m.Y', strtotime(date('Y-m-d'))),
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
                <div class="lgc_column_element_right">
                    <label>C:</label>
                    <?=DatePicker::widget(
                        ['model' => $model,
                            'attribute' => 'pays_sdate',
                            'options' => ['placeholder' => 'Начало периода',
                                'value' => date('01.m.Y', strtotime(date('Y-m-d'))),
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
                <table id="paymentsList" class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">№</th>
                        <th scope="col">Дата</th>
                        <th scope="col">Сумма</th>
                        <th scope="col">Описание</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="ctransaction.showPaysModal()">Обновить</button>
            </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="fsspcheckModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Проверка задолженностей по базе ФССП</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row" name="fssp_params">
                        <div class="col-lg-8 col-lg-offset-2 col-sm-8 col-sm-offset-2 pb-10"><input name="fssp_param_fn" type="text" class="form-control" placeholder="Фамилия" value="<?= (explode(' ', $client_params['fio']))[0]; ?>"/></div>
                        <div class="col-lg-8 col-lg-offset-2 col-sm-8 col-sm-offset-2 pb-10"><input name="fssp_param_sn" type="text" class="form-control" placeholder="Имя" value="<?= (explode(' ', $client_params['fio']))[1]; ?>"/></div>
                        <div class="col-lg-8 col-lg-offset-2 col-sm-8 col-sm-offset-2 pb-10"><input name="fssp_param_mn" type="text" class="form-control" placeholder="Отчество" value="<?= (explode(' ', $client_params['fio']))[2]; ?>"/></div>
                        <div class="col-lg-8 col-lg-offset-2 col-sm-8 col-sm-offset-2 pb-10">
                            <?= MaskedInput::widget(['name' => 'fssp_param_bd','mask' => '99.99.9999','value'=>$client_params['birthday'], 'options'=>['placeholder'=>'ДД.ММ.ГГГГ', 'type'=>'text', 'class'=>'form-control']]);?>
                        </div>
                    </div>
                    <div class="row" name="fssp_captcha">
                        <div class="col-lg-8 col-lg-offset-2 p-15 text-center"><img name="fssp_img_captcha"/></div>
                        <div class="col-lg-4 col-lg-offset-4 col-sm-4 col-sm-offset-4 pb-10"><input name="fssp_str_captcha" type="text" class="form-control" placeholder="Значение с картинки" autofocus autocomplete="off"></div>
                        <br/>
                        <div class="col-lg-8 col-lg-offset-2 m-15 text-center"><label name="fssp_lbl_status"></label></div>
                    </div>
                    <div class="row" name="fssp_result">
                        <div class="col-lg-12 pb-10 text-center" name="fssp_result_text"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <label name="fssp_loader" class="pull-left" style="visibility: visible;"></label>
                    <button name="fssp_bnt_next" type="button" class="btn btn-primary pull-right ml-10" onclick="ctransaction.fsspNextStep()">Далее</button>
                    <button name="fssp_bnt_refresh" type="button" class="btn btn-warning pull-right ml-10" onclick="ctransaction.fsspReloadCaptcha()">Обновить</button>
                </div>
            </div>
        </div>
    </div>

</div>
