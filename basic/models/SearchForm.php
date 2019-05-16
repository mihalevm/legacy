<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class SearchForm extends Model {
    protected $db_conn;

    function __construct () {
        $this->db_conn = Yii::$app->db;
    }

    public function Search ($sp) {
        $sp = strtolower($sp);
        $arr = $this->db_conn->createCommand("SELECT c.uid, c.fio, c.phone, b.cnum, b.bsumm FROM lgc_clients c, lgc_bcards b WHERE c.cid=b.cid and c.disabled='N' AND ( lower(c.fio) LIKE '%".$sp."%' OR c.phone LIKE '%".$sp."%' OR b.cnum LIKE '%".$sp."%')")
            ->queryAll();

        return $arr;
    }
}
