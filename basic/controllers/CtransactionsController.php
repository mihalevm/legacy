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

class CtransactionsController extends Controller {

    private function _sendJSONAnswer($res){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $res;

        return $response;
    }

    public function actionIndex() {
        if ( null === Yii::$app->user->id) {
            return $this->redirect(['/login']);
        }

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

    public function actionAddbonus(){
        if ( null === Yii::$app->user->id) {
            return $this->redirect(['/login']);
        }

        $r = Yii::$app->request;
        $res = 0;
        $model = new CtransactionsForm();

        if (null !== $r->post('u') && null != $r->post('s') && null != $r->post('bs')){
            $res = $model->AddTransaction(
                'a',
                $r->post('u'),
                $r->post('s'),
                $r->post('bs'),
                $r->post('d')
            );
        }

        return $this->_sendJSONAnswer($res);
    }


}