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
use app\models\FsspForm;
use app\models\PcheckForm;


class CheckController extends Controller {

    private function _sendJSONAnswer($res){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $res;

        return $response;
    }

    public function beforeAction($action) {
        if ( null === Yii::$app->user->id) {
            return $this->redirect(['/login']);
        }

        return parent::beforeAction($action);
    }

//    public function actionIndex() {
//    }

    public function actionFsspcaptcha(){
        $model = new FsspForm();

        return $this->_sendJSONAnswer($model->GetCaptcha());
    }

    public function actionFsspresult(){
        $r = Yii::$app->request;
        $model = new FsspForm();
        $res = null;

        if (
               null != $r->post('sid')
            && null != $r->post('captcha')
            && null != $r->post('fn')
            && null != $r->post('sn')
//            && null != $r->post('mn')
            && null != $r->post('bd')
        ) {
            $res = $model->Send_Grab(
                $r->post('sid'),
                $r->post('captcha'),
                $r->post('fn'),
                $r->post('sn'),
                $r->post('mn'),
                $r->post('bd')
            );
        }

        return $this->_sendJSONAnswer($res);
    }

    public function actionPassportcheck(){
        $r     = Yii::$app->request;
        $res   = null;
        $model = new PcheckForm();

        if (   null != $r->post('s')
            && null != $r->post('n')
            && null != $r->post('c')
            && null != $r->post('uid')
            && null != $r->post('jid')
        ){
            $res = $model->PassportValidate(
                $r->post('s'),
                $r->post('n'),
                $r->post('c'),
                $r->post('uid'),
                $r->post('jid')
            );
        }

        return $this->_sendJSONAnswer($res);
    }

    public function actionPassportcaptcha (){
        $res   = null;
        $model = new PcheckForm();
        $res   = $model->getCaptcha();

        return $this->_sendJSONAnswer($res);
    }
}
