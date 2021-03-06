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
class ReportForm extends Model {
    protected $db_conn;

    function __construct () {
        $this->db_conn = Yii::$app->db;
    }

    public function getCompanySumm () {
        $arr = $this->db_conn->createCommand("SELECT co.name, (SELECT SUM(c.cbalance) FROM lgc_clients c WHERE c.coid=co.coid) AS totalSum, (SELECT SUM(p.pay_sum) FROM lgc_clients c, lgc_cperiods p WHERE c.coid=co.coid AND c.uid = p.uid AND p.payed='N' AND p.pay_data<NOW()) AS debitSum FROM lgc_company co WHERE co.disabled = 'N'")
            ->queryAll();

        return $arr;
    }

    public function getClientsSumm () {
        $arr = $this->db_conn->createCommand("SELECT c.uid, c.fio, c.cbalance, co.name, (SELECT SUM(p.pay_sum) FROM lgc_cperiods p WHERE c.uid=p.uid AND p.pay_data<NOW()) AS debitSum from lgc_clients c, lgc_company co WHERE c.disabled = 'N' AND c.coid=co.coid AND co.disabled = 'N'")
            ->queryAll();

        return $arr;
    }

    public function hasDebit () {
        $arr = ($this->db_conn->createCommand("SELECT count(*) as cnt FROM lgc_clients c, lgc_cperiods p WHERE c.uid = p.uid AND p.payed='N' AND p.pay_data<NOW() and p.notify='N'")
            ->queryAll())[0];

        $arr['cnt'] = $arr['cnt'] ? $arr['cnt'] : '';

        return $arr['cnt'];
    }

    public function setNotifyChecked(){
        $this->db_conn->createCommand("update lgc_cperiods set notify='Y'")
            ->execute();

        return 1;
    }
}
