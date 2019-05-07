<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;


/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class TransactionsForm extends Model {
    public $sdate;
    public $edate;
    protected $db_conn;

    function __construct () {
        $this->db_conn = Yii::$app->db;
    }

    public function getClientParams ($uid) {
        $arr = ($this->db_conn->createCommand("select uid, fio from lgc_clients where uid=:uid")
            ->bindValue(':uid', $uid)
            ->queryAll())[0];

        return $arr;
    }

    public function getAllTransactions ($uid, $sdate, $edate) {
        $arr = $this->db_conn->createCommand("select date_format(tdate,'%d.%m.%Y') as tdate, summ, bsumm, tdesc, ttype from lgc_btransactions where uid=:uid and tdate >= timestamp(str_to_date(:sdate, '%d.%m.%Y')) and tdate <= timestamp(str_to_date(:edate, '%d.%m.%Y')) order by tid")
            ->bindValue(':sdate', $sdate)
            ->bindValue(':edate', $edate)
            ->bindValue(':uid', $uid)
            ->queryAll();

        return $arr;
    }

}
