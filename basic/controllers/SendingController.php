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
use app\models\SendingForm;

class SendingController extends Controller {

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

        $model = new SendingForm();
        $sending_list = new ArrayDataProvider([
            'allModels' => $model->getSMSSending(),
            'sort' => [
                'attributes' => ['sdate'],
            ],
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        return $this->render('index',[
            'model' => $model,
            'sending_list' => $sending_list,
        ]);
    }

    public function actionGetsend(){
        if ( null === Yii::$app->user->id) {
            return $this->redirect(['/login']);
        }

        $model = new SendingForm();
        $r = Yii::$app->request;
        $res = null;

        if (null !== $r->post('s')){
            $res = $model->getSMSSend($r->post('s'));
        }

        return $this->_sendJSONAnswer($res);
    }

    public function actionSavesend(){
        if ( null === Yii::$app->user->id) {
            return $this->redirect(['/login']);
        }

        $model = new SendingForm();
        $r = Yii::$app->request;
        $res = null;

        if (null !== $r->post('s')){
            $res = $model->getSMSSendUpdate(
                $r->post('s'),
                $r->post('d'),
                $r->post('n'),
                $r->post('m')
            );
        }

        return $this->_sendJSONAnswer($res);
    }

    public function actionNewsend(){
        if ( null === Yii::$app->user->id) {
            return $this->redirect(['/login']);
        }

        $model = new SendingForm();
        $r = Yii::$app->request;
        $res = null;

        if (null !== $r->post('d')){
            $res = $model->getSMSSendInsert(
                $r->post('d'),
                $r->post('n'),
                $r->post('m')
            );
        }

        return $this->_sendJSONAnswer($res);
    }

    public function actionDelsend(){
        if ( null === Yii::$app->user->id) {
            return $this->redirect(['/login']);
        }

        $model = new SendingForm();
        $r = Yii::$app->request;
        $res = null;

        if (null !== $r->post('s')){
            $res = $model->getSMSSendDelete(
                $r->post('s'),
                $r->post('d'),
                $r->post('n'),
                $r->post('m')
            );
        }

        return $this->_sendJSONAnswer($res);
    }
}