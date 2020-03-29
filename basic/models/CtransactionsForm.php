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

// Bonus transaction part
/*
    private function getCardIdbyUid ($uid){
        $arr = ($this->db_conn->createCommand("SELECT cid FROM lgc_clients WHERE uid=:uid")
            ->bindValue(':uid', $uid)
            ->queryAll())[0];

        return $arr['cid'];
    }


    private function UpdateBonusBalace($cid, $bsumm) {
        $arr = ($this->db_conn->createCommand("select bsumm from lgc_bcards where cid=:cid")
            ->bindValue(':cid', $cid)
            ->queryAll())[0];

        $cur_bsumm = intval($arr['bsumm']);

        $cur_bsumm = $cur_bsumm + $bsumm > 0 ? $cur_bsumm + $bsumm : 0;

        $this->db_conn->createCommand("update lgc_bcards set bsumm=:bsumm where cid=:cid")
            ->bindValue(':bsumm', $cur_bsumm)
            ->bindValue(':cid', $cid)
            ->execute();
    }

    public function AddTransaction($ttype, $uid, $summ, $bsumm, $descr){
        $cid = $this->getCardIdbyUid($uid);

        $this->db_conn->createCommand("insert into lgc_btransactions (cid, uid, ttype, summ, bsumm, tdesc) values (:cid, :uid, :ttype, :summ, :bsumm, :tdesc)")
            ->bindValue(':cid', $cid)
            ->bindValue(':uid', $uid)
            ->bindValue(':ttype', $ttype)
            ->bindValue(':summ', $summ)
            ->bindValue(':bsumm', $bsumm)
            ->bindValue(':tdesc', $descr)
            ->execute();

        $tid = $this->db_conn->getLastInsertID();

        if ( $tid > 0 ) {
            $bsumm = $ttype == 'a' ? $bsumm : -$bsumm;
            $this->UpdateBonusBalace($cid, $bsumm);
        }

        return $tid;
    }
*/

}
