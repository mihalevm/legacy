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
use app\models\CreateCardForm;

class CreateCardController extends Controller {

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

        $model = new CreateCardForm();

        return $this->render('index',[
            'model' => $model,
            'LastCard' => $model->getLastCardNumber(),
        ]);
    }

    public function actionGenerate(){
        if ( null === Yii::$app->user->id) {
            return $this->redirect(['/login']);
        }

        $r = Yii::$app->request;
        $res = 0;
        $model = new CreateCardForm();

        if (null !== $r->post('bn') && null !== $r->post('en')){
            $balance = (null !== $r->post('b')?intval($r->post('b')):0);
            $days    = (null !== $r->post('d')?intval($r->post('d')):0);

            for ($i = intval($r->post('bn')); $i <= intval($r->post('en')); $i++) {
                $model->addNewCard($i, $balance, $days);
            }

            $res = $model->getLastCardNumber();
        }

        return $this->_sendJSONAnswer($res);
    }

}