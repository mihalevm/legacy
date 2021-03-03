<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Вход';
$this->params['breadcrumbs'][] = $this->title;
?>
<br/>
<div class="site-login">
    <div class="row">

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'action' => '/login/login',
        'fieldConfig' => [
            'template' => "<div class='col-lg-6'>{label}</div><div class=\"col-lg-6\">{input}</div><div style='text-align: right; margin-right: 20px;'>{error}</div>"
        ],
    ]); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Пользователь:', ['class'=>"lgc_login_label"]) ?>

        <?= $form->field($model, 'password')->passwordInput()->label('Пароль:', ['class'=>"lgc_login_label"]) ?>

        <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "<div class=\"col-lg-offset-6 col-lg-6\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
        ])->label('Запомнить сессию') ?>
    </div>

    <div class="lgc_form_control">
            <span>
                <div style="width: 150px; display: table-cell"></div>
            </span>
        <span>
                <?= Html::submitButton('Вход', ['class' => 'btn btn-primary ', 'name' => 'login-button']) ?>
            </span>
    </div>
</div>
    <?php ActiveForm::end(); ?>
</div>
