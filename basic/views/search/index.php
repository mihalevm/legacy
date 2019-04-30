<?php

use yii\helpers\Html;
use yii\widgets\MaskedInput;
use yii\bootstrap\Button;

$this->title = 'Поиск клиента';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="lgc_searchform">
        <label>
        <i class="fa fa-search" aria-hidden="true"></i>
        <?=Html::textInput('spattern', null, ['placeholder' => 'Поиск', 'onkeyup' => 'search.newsearch()']); ?>
        </label>
    </div>
    <div class="lgc_searchresult">
        <table class="table table-hover lgc_searchresulttable">
            <thead>
            <tr>
                <th scope="col">ФИО</th>
                <th scope="col">Телефон</th>
                <th scope="col">Номер карты</th>
                <th scope="col">Баланс</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
