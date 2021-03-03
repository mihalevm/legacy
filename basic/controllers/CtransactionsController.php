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
use app\models\CtransactionsForm;
use app\models\FsspForm;

class CtransactionsController extends Controller {

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

    public function actionIndex() {
        $r = Yii::$app->request;
        $model = new CtransactionsForm();
        $client_params = null;
        $client_last_transaction = null;

        if (null !== $r->get('u')){
            $client_params = $model->getClientParams($r->get('u'));
            $client_last_transaction = $model->getLastStat($r->get('u'));
        }

        return $this->render('index',[
            'model' => $model,
            'client_params' => $client_params,
            'client_last_transaction'=> $client_last_transaction,
        ]);
    }

    public function actionSell() {
        $r = Yii::$app->request;
        $model = new CtransactionsForm();
        $client_params = null;
        $client_last_transaction = null;

        if (null !== $r->get('u')){
            $client_params = $model->getClientParams($r->get('u'));
            $client_last_transaction = $model->getLastStat($r->get('u'));
        }

        return $this->render('sell',[
            'model' => $model,
            'client_params' => $client_params,
            'client_last_transaction'=> $client_last_transaction,
        ]);
    }

    public function actionSaveperiods() {
        $r = Yii::$app->request;
        $res = 0;
        $model = new CtransactionsForm();

        if (null !== $r->post('u') && null != $r->post('p') ){
            $res = $model->AddCreditPeriods(
                $r->post('u'),
                $r->post('p')
            );
        }

        return $this->_sendJSONAnswer($res);
    }

    public function actionSavecorder() {
        $r = Yii::$app->request;
        $res = 0;
        $model = new CtransactionsForm();

        if (null !== $r->post('u') && null != $r->post('s') ){
            $res = $model->AddOrder(
                $r->post('u'),
                $r->post('s'),
                $r->post('d'),
                'C'
            );
        }

        return $this->_sendJSONAnswer($res);
    }

    public function actionSaveporder() {
        $r = Yii::$app->request;
        $res = 0;
        $model = new CtransactionsForm();

        if (null !== $r->post('u') && null != $r->post('s') ){
            $res = $model->AddOrder(
                $r->post('u'),
                $r->post('s'),
                $r->post('d'),
                'P'
            );
        }

        return $this->_sendJSONAnswer($res);
    }

    public function actionGetperiods(){
        $r = Yii::$app->request;
        $model = new CtransactionsForm();
        $res = null;

        if (null !== $r->post('u')){
            $res = $model->getPayPeriods($r->post('u'));
        }

        return $this->_sendJSONAnswer($res);
    }

    public function actionAddpayments(){
        $r = Yii::$app->request;
        $model = new CtransactionsForm();
        $res = null;

        if (null !== $r->post('p')){
            $res = $model->AddPaymentsFromCPeriods($r->post('p'));
        }

        return $this->_sendJSONAnswer($res);
    }

    public function actionGetpayments(){
        $r = Yii::$app->request;
        $model = new CtransactionsForm();
        $res = [1];

        if (null !== $r->post('u') && null !== $r->post('s') && null !== $r->post('e')){
            $res = $model->getPaymentsByPeriod(
                $r->post('u'),
                $r->post('s'),
                $r->post('e')
            );
        }

        return $this->_sendJSONAnswer($res);
    }

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
            && null != $r->post('mn')
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

}
