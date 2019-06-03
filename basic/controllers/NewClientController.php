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
use app\models\NewClientForm;

class NewClientController extends Controller {

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

        $model = new NewClientForm();
        return $this->render('index',[
            'cSize'  => $model->getAllCSize(),
            'fSize'  => $model->getAllFSize(),
            'dStyle' => $model->getUnqStyles(),
        ]);
    }

    public function actionCreate(){
        if ( null === Yii::$app->user->id) {
            return $this->redirect(['/login']);
        }

        $r = Yii::$app->request;
        $res = 0;
        $model = new NewClientForm();

        if (null !== $r->post('cnum')){
            $res = $model->createNewUser(
                $r->post('cnum'),
                $r->post('bb'),
                $r->post('nc'),
                $r->post('fio'),
                $r->post('phone'),
                $r->post('birth'),
                $r->post('sex'),
                $r->post('ctype'),
                $r->post('csize'),
                $r->post('fsize')
            );
        }

        return $this->_sendJSONAnswer($res);
    }

    public function actionUpdate(){
        if ( null === Yii::$app->user->id) {
            return $this->redirect(['/login']);
        }

        $r = Yii::$app->request;
        $res = 0;
        $model = new NewClientForm();

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
                $r->post('fsize')
            );
        }

        return $this->_sendJSONAnswer($res);
    }

    public function actionNewcard(){
        if ( null === Yii::$app->user->id) {
            return $this->redirect(['/login']);
        }

        $r = Yii::$app->request;
        $res = 0;
        $model = new NewClientForm();

        if (null !== $r->post('c') && intval($r->post('c'))>0 ) {
            $res = $model->checkNewCard($r->post('c'));
        }

        return $this->_sendJSONAnswer($res);
    }


}