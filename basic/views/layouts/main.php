<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
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
            '<li>'
            . Html::beginForm(['/login/logout'], 'post')
            . Html::submitButton(
                FAS::icon('sign-out-alt').'Выход (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>'
        ];
    } else {
        $items = [
            ['label' => FAS::icon('sign-in-alt').'Вход', 'url' => ['/login']]
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

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
