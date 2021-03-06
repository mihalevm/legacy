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
use yii\data\ArrayDataProvider;
use app\models\TransactionsForm;

class TransactionsController extends Controller {

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
        $model = new TransactionsForm();
        $client_params = null;
        $client_transactions = null;
        $sdate = null !== $r->get('s') ? $r->get('s') : date('01.m.Y', strtotime(date('Y-m-d')));
        $edate = null !== $r->get('e') ? $r->get('e') : date('t.m.Y', strtotime(date('Y-m-d')));

        if (null !== $r->get('u')) {
            $client_params = $model->getClientParams($r->get('u'));

            $client_transactions = new ArrayDataProvider([
                'allModels' => $model->getAllTransactions($r->get('u'), $sdate, $edate),
                'sort' => [
                    'attributes' => ['tdate', 'summ', 'bsumm'],
                ],
                'pagination' => [
                    'pageSize' => 5,
                ],
            ]);
        }


        return $this->render('index',[
            'model' => $model,
            'client_params' => $client_params,
            'client_transactions' => $client_transactions
        ]);
    }

    public function actionAddbonus(){
        $r = Yii::$app->request;
        $res = 0;
        $model = new TransactionsForm();

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

    public function actionGettransaction(){
        $r = Yii::$app->request;
        $res = 0;
        $model = new TransactionsForm();

        if (null !== $r->post('t')){
            $res = $model->GetTransaction(
                $r->post('t')
            );
        }

        return $this->_sendJSONAnswer($res);
    }

    public function actionSavetransaction(){
        $r = Yii::$app->request;
        $res = 0;
        $model = new TransactionsForm();

        if (null !== $r->post('t')){
            $res = $model->setTransaction(
                $r->post('t'),
                $r->post('bo'),
                $r->post('s'),
                $r->post('bs'),
                $r->post('d')
            );
        }

        return $this->_sendJSONAnswer($res);
    }

    public function actionDeltransaction(){
        $r = Yii::$app->request;
        $res = 0;
        $model = new TransactionsForm();

        if (null !== $r->post('t')){
            $res = $model->DelTransaction(
                $r->post('t')
            );
        }

        return $this->_sendJSONAnswer($res);
    }

}