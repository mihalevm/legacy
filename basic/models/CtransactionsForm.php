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
class CtransactionsForm extends Model {
    protected $db_conn;

    function __construct () {
        $this->db_conn = Yii::$app->db;
    }

    public function getClientParams ($uid) {
        $arr = ($this->db_conn->createCommand("select c.uid, c.fio, c.cbalance, (select count(*) from lgc_cperiods where uid=:uid and payed='N') as pay_period, (select p.ptype from lgc_paytype p, lgc_company co where c.coid=co.coid and co.ptype=p.pid) as paytype from lgc_clients c where c.uid=:uid")
            ->bindValue(':uid', $uid)
            ->bindValue(':uid', $uid)
            ->queryAll())[0];

        return $arr;
    }

    public function getLastStat ($uid) {
        $arr = $this->db_conn->createCommand("SELECT tdate, summ, bsumm, tdesc, ttype FROM (select DATE_FORMAT(tdate,'%d.%m.%Y') as tdate, summ, bsumm, tdesc, ttype from lgc_btransactions where uid=:uid UNION SELECT DATE_FORMAT(tdate,'%d.%m.%Y') as tdate, summ, 0, tdesc, type FROM lgc_ctransactions WHERE uid = :uid ) as t order BY t.tdate desc limit 5")
            ->bindValue(':uid', $uid)
            ->bindValue(':uid', $uid)
            ->queryAll();

        return $arr;
    }

    public function addCPayment ($uid, $sum, $desc) {
        $res = null;

        $this->db_conn->createCommand("insert into lgc_cpayments (uid, psum, pdesc) values (:uid, :psum, :pdesc)")
            ->bindValue(':uid', $uid)
            ->bindValue(':psum', $sum)
            ->bindValue(':pdesc', $desc)
            ->execute();

        $res = $this->db_conn->getLastInsertID();

        if ($res) {
            $this->updateClientCBalance($uid, -1*$sum);
        }

        return $res;

    }

    private function addCreditPeriodItem ($uid, $item) {
        $this->db_conn->createCommand("insert into lgc_cperiods (uid, pitem, pay_data, pay_sum, pay_residue, payed) values (:uid, :pitem, str_to_date(:pay_data, '%d.%m.%Y'), :pay_sum, :pay_residue, :payed)")
            ->bindValue(':uid', $uid)
            ->bindValue(':pitem', $item->i)
            ->bindValue(':pay_data', $item->date)
            ->bindValue(':pay_sum', $item->sum)
            ->bindValue(':pay_residue', $item->residue)
            ->bindValue(':payed', ($item->i === 0 ? 'Y' : 'N'))
            ->execute();

        return $this->db_conn->getLastInsertID();
    }

    private function updateClientCBalance($uid, $sum){
        $this->db_conn->createCommand("update lgc_clients set cbalance=cbalance+:summ where uid=:uid")
            ->bindValue(':summ', $sum)
            ->bindValue(':uid', $uid)
            ->execute();

        return true;
    }

    private function deleteCreditPeriods($uid) {
        $this->db_conn->createCommand("delete from lgc_cperiods where uid=:uid")
            ->bindValue(':uid', $uid)
            ->execute();

        return true;

    }

    public function AddCreditPeriods ($uid, $periods) {
        $aPeriods = json_decode($periods);
        $firstPay = null;
        $paySum   = 0;

        if( $this->deleteCreditPeriods($uid) ) {
            foreach ($aPeriods as $item) {
                $this->addCreditPeriodItem($uid, $item);
                $paySum += $item->sum;
                if ($item->i === 0 ) {
                    $firstPay = $item;
                }
            }
            if (null !== $firstPay) {
                $this->addCPayment($uid, $firstPay->sum, 'Начальный платеж от суммы:'.$paySum);
            }
        }

        return 1;
    }

    public function AddOrder( $uid, $sum, $desc, $type){
        $res = null;

        $this->db_conn->createCommand("insert into lgc_ctransactions (uid, summ, type, tdesc) values (:uid, :summ, :type, :desc)")
            ->bindValue(':uid',  $uid)
            ->bindValue(':summ', $sum)
            ->bindValue(':type', $type)
            ->bindValue(':desc', $desc)
            ->execute();

        $res = $this->db_conn->getLastInsertID();

        if ($res > 0 ) {
            $this->updateClientCBalance($uid, $sum);
        }

        return $res;
    }

    public function getPayPeriods ($uid) {
        return $this->db_conn->createCommand("select pid, date_format(pay_data, '%d.%m.%Y') as pay_data, pitem, pay_sum, pay_residue, payed from lgc_cperiods where uid=:uid")
            ->bindValue(':uid', $uid)
            ->queryAll();
    }

    public function AddPaymentsFromCPeriods($pids) {
        $arr_pids = json_decode($pids);
        $uid = 0;

        foreach ($arr_pids as $pid ){
            $cPeriods = ($this->db_conn->createCommand("select pid, uid, date_format(pay_data, '%d.%m.%Y') as pay_data, pay_sum, payed from lgc_cperiods where pid=:pid")
                ->bindValue(':pid', $pid)
                ->queryAll())[0];

            if ($cPeriods['payed'] === 'N' && $this->addCPayment($cPeriods['uid'], $cPeriods['pay_sum'], 'Платеж по рассрочке от '.$cPeriods['pay_data'])){
                $this->db_conn->createCommand("update lgc_cperiods set payed='Y' where pid=:pid")
                    ->bindValue(':pid', $pid)
                    ->execute();
            }

            $uid = $cPeriods['uid'];
        }

        if ($uid > 0) {
            $this->checkCloseAllPeriods($uid);
        }

        return 1;
    }

    private function checkCloseAllPeriods($uid) {
        $hasOpenPeriods = ($this->db_conn->createCommand("select count(*) as cnt from lgc_cperiods where uid=:uid and payed='N'")
            ->bindValue(':uid', $uid)
            ->queryAll())[0];

        if (intval($hasOpenPeriods['cnt']) === 0) {
            $this->deleteCreditPeriods($uid);
        }

        return 1;
    }
}
