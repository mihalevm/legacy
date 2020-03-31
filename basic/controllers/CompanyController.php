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
use app\models\CompanyForm;

class CompanyController extends Controller {

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
        $model = new CompanyForm();

        $allCompanyList = new ArrayDataProvider([
            'allModels' => $model->getAllCompany(),
            'sort' => [
                'attributes' => ['name'],
            ],
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        return $this->render('index',[
            'allCompanyList' => $allCompanyList,
            'paytype' => $model->getAllPayTypes()
        ]);
    }

    public function actionSave() {
        $model = new CompanyForm();
        $r = Yii::$app->request;
        $result = null;

        if (null !== $r->post('id') && null !== $r->post('n') && strlen($r->post('n'))>0 ){
            if ($r->post('id') == 0){
                $result = $model->createCompany(
                    $r->post('n'),
                    $r->post('m'),
                    $r->post('c'),
                    $r->post('d'),
                    $r->post('p')
                );
            } else {
                $result = $model->updateCompany(
                    $r->post('id'),
                    $r->post('n'),
                    $r->post('m'),
                    $r->post('c'),
                    intval($r->post('d')),
                    $r->post('p')

                );
            }
        }

        return $this->_sendJSONAnswer($result);
    }
}