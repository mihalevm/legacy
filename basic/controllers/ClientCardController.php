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
use app\models\ClientCardForm;
use app\models\NewClientForm;

class ClientCardController extends Controller {

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
        $model = new ClientCardForm();
        $comodel = new NewClientForm();
        $client_params = null;

        if (null !== $r->get('u')){
            $client_params = $model->getClientParams($r->get('u'));
        }

        return $this->render('index',[
            'model' => $model,
            'cSize' => $model->getAllCSize(),
            'fSize' => $model->getAllFSize(),
            'client_params' => $client_params,
            'dStyle' => $model->getUnqStyles(),
            'company' => $comodel->getAllCompany(),
        ]);
    }

    public function actionUpdate(){
        $r = Yii::$app->request;
        $res = 0;
        $model = new ClientCardForm();

        if (null !== $r->post('cnum') && null != $r->post('uid')){
            $res = $model->updateNewUser(
                $r->post('uid'),
                $r->post('cnum'),
                $r->post('fio'),
                $r->post('phone'),
                $r->post('birth'),
                $r->post('sex'),
                $r->post('ctype'),
                $r->post('csize'),
                $r->post('fsize'),
                $r->post('spoint'),
                $r->post('coid')
            );
        }

        return $this->_sendJSONAnswer($res);
    }

    public function actionDelete(){
        $r = Yii::$app->request;
        $res = 0;
        $model = new ClientCardForm();

        if (null !== $r->post('u')){
            $res = $model->DeleteUser(
                $r->post('u')
            );
        }

        return $this->_sendJSONAnswer($res);
    }
}