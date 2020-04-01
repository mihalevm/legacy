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
use app\models\ReportForm;

class ReportController extends Controller {

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
        return $this->render('index',[]);
    }

    public function actionRpCompany() {
        $model = new ReportForm();

        $companySum = new ArrayDataProvider([
            'allModels' => $model->getCompanySumm(),
            'sort' => [
                'attributes' => ['name', 'totalSum', 'debitSum'],
            ],
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        return $this->render('rpcompany',[
            'model' => $model,
            'companySum' => $companySum
        ]);
    }

    public function actionRpClientsDebit() {
        $model = new ReportForm();

        $clientsSum = new ArrayDataProvider([
            'allModels' => $model->getClientsSumm(),
            'sort' => [
                'attributes' => ['fio', 'cbalance', 'name', 'debitSum'],
            ],
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        return $this->render('rpclientsdebit',[
            'model' => $model,
            'clientsSum' => $clientsSum
        ]);
    }
}
