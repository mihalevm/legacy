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
use app\models\SearchForm;

class SearchController extends Controller {

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

        $model = new SearchForm();

        return $this->render('index',[
            'model' => $model,
        ]);
    }

    public function actionNewsearch(){
        if ( null === Yii::$app->user->id) {
            return $this->redirect(['/login']);
        }

        $r = Yii::$app->request;
        $res = 0;
        $model = new SearchForm();

        if (null !== $r->post('s')){
            $res = $model->Search($r->post('s'));
        }

        return $this->_sendJSONAnswer($res);
    }

}