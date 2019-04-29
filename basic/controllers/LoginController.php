<?php
/**
 * Created by PhpStorm.
 * User: mmv
 * Date: 26.04.2019
 * Time: 14:09
 */

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\LoginForm;

class LoginController extends Controller
{
    public function actionIndex() {
        $model = new LoginForm();

        return $this->render('index',[
            'model' => $model,
        ]);
    }

    public function actionLogin() {
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goHome();
        }

        $model->password = '';
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}