<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\MaskedInput;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\assets\LegacyAsset;
use rmrevin\yii\fontawesome\FAS;
use app\models\ReportForm;


AppAsset::register($this);

if ( null !== Yii::$app->user->id) {
    LegacyAsset::register($this);
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    $model = new ReportForm();

    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    $items = [];

    if ( null !== Yii::$app->user->id) {
        $items = [
            ['label' => FAS::icon('search').'Поиск', 'url' => ['/search']],
            ['label' => FAS::icon('user-plus').'Новый клиент', 'url' => ['/new-client']],
            ['label' => FAS::icon('users').'Компании', 'url' => ['/company']],
            ['label' => FAS::icon('comment-alt').'Рассылки', 'url' => ['/sending']],
            ['label' => FAS::icon('chart-bar').'Отчеты <span name="debitWarning" class="badge badge-warning" title="Есть просроченные платежи">'.$model->hasDebit().'</span>', 'url' => ['/report']],
            ['label' => FAS::icon('check-circle').'Проверка', 'items' => [
                ['label' => FAS::icon('gavel').'ФССП',      'url' => '#', 'options' => ['onclick' => 'check.fsspCheckShow()'] ],
                ['label' => FAS::icon('id-card').'Паспорт', 'url' => '#', 'options' => ['onclick' => 'check.PassportCheckShow()'] ],
            ]],
            '<li>'
            . Html::beginForm(['/login/logout'], 'post')
            . Html::submitButton(
                FAS::icon('sign-out-alt').' Выход (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>'
        ];
    } else {
        $items = [
            ['label' => FAS::icon('sign-in-alt').' Вход', 'url' => ['/login']]
        ];
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $items,
        'encodeLabels' => false,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <div class="loader loader_right_top"></div></li>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-right">&copy; Legacy <?= date('Y') ?></p>
    </div>
</footer>

<?php if ( null !== Yii::$app->user->id) { ?>
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
                        <div class="col-lg-8 col-lg-offset-2 col-sm-8 col-sm-offset-2 pb-10"><input name="fssp_param_fn" type="text" class="form-control" placeholder="Фамилия" value=""/></div>
                        <div class="col-lg-8 col-lg-offset-2 col-sm-8 col-sm-offset-2 pb-10"><input name="fssp_param_sn" type="text" class="form-control" placeholder="Имя" value=""/></div>
                        <div class="col-lg-8 col-lg-offset-2 col-sm-8 col-sm-offset-2 pb-10"><input name="fssp_param_mn" type="text" class="form-control" placeholder="Отчество" value=""/></div>
                        <div class="col-lg-8 col-lg-offset-2 col-sm-8 col-sm-offset-2 pb-10">
                            <?= MaskedInput::widget(['name' => 'fssp_param_bd','mask' => '99.99.9999','value'=>'', 'options'=>['placeholder'=>'ДД.ММ.ГГГГ', 'type'=>'text', 'class'=>'form-control']]);?>
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
                    <button name="fssp_bnt_next" type="button" class="btn btn-primary pull-right ml-10" onclick="check.fsspNextStep()">Далее</button>
                    <button name="fssp_bnt_refresh" type="button" class="btn btn-warning pull-right ml-10" onclick="check.fsspReloadCaptcha()">Обновить</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="passportheckModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Проверка паспорта</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row" name="passport_params">
                        <div class="col-lg-4 col-lg-offset-4 col-sm-4 col-sm-offset-4 pb-10">
                            <?= MaskedInput::widget(['name' => 'passport_param_ps','mask' => '9999','value'=>'', 'options'=>['placeholder'=>'Серия', 'type'=>'text', 'class'=>'form-control']]);?>
                        </div>
                        <div class="col-lg-4 col-lg-offset-4 col-sm-4 col-sm-offset-4 pb-10">
                            <?= MaskedInput::widget(['name' => 'passport_param_pn','mask' => '999999','value'=>'', 'options'=>['placeholder'=>'Номер', 'type'=>'text', 'class'=>'form-control']]);?>
                        </div>
                        <div class="col-lg-8 col-lg-offset-2 p-15 text-center"><img name="passport_img_captcha"/></div>
                        <div class="col-lg-4 col-lg-offset-4 col-sm-4 col-sm-offset-4 pb-10">
                            <input name="passport_param_pc" type="text" class="form-control" placeholder="Значение с картинки" autofocus autocomplete="off">
                        </div>
                        <br/>
                        <div class="col-lg-8 col-lg-offset-2 m-15 text-center"><label name="passport_lbl_status"></label></div>
                    </div>
                    <div class="row" name="passport_result">
                        <div class="col-lg-12 pb-10 text-center" name="passport_result_text"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <label name="passport_loader" class="pull-left" style="visibility: visible;"></label>
                    <button name="passport_bnt_next" type="button" class="btn btn-primary pull-right ml-10" onclick="check.PassportValidation()">Проверить</button>
                    <button name="passport_bnt_refresh" type="button" class="btn btn-warning pull-right ml-10" onclick="check.PassportReloadCaptcha()">Обновить</button>
                </div>
            </div>
        </div>
    </div>

<?php }?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
