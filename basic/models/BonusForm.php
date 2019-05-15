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
class BonusForm extends Model {
    protected $db_conn;

    function __construct () {
        $this->db_conn = Yii::$app->db;
    }

// Bonus transaction part

    private function getCardIdbyUid ($uid){
        $arr = ($this->db_conn->createCommand("SELECT cid FROM lgc_clients WHERE uid=:uid")
            ->bindValue(':uid', $uid)
            ->queryAll())[0];

        return $arr['cid'];
    }

    public function getClientParams ($uid) {
        $arr = ($this->db_conn->createCommand("select c.uid, c.fio, b.cnum, b.bsumm from lgc_clients c, lgc_bcards b where c.cid=b.cid and c.uid=:uid")
            ->bindValue(':uid', $uid)
            ->queryAll())[0];

        return $arr;
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

    public function getLastStat ($uid) {
        $arr = $this->db_conn->createCommand("select date_format(tdate,'%d.%m.%Y') as tdate, summ, bsumm, tdesc, ttype from lgc_btransactions where uid=:uid order by tid desc limit 5")
            ->bindValue(':uid', $uid)
            ->queryAll();

        return $arr;
    }

}
