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
class BonusForm extends Model {
    protected $db_conn;

    function __construct () {
        $this->db_conn = Yii::$app->db;
    }

    public function getLastCardNumber () {
        $arr = ($this->db_conn->createCommand("select max(cnum) as last_max_num from lgc_bcards")
            ->queryAll())[0];

        return $arr['last_max_num'];
    }

    public function addNewCard ($id, $balance, $days) {
        $this->db_conn->createCommand("insert into lgc_bcards (cnum, bsumm, days) values (:cnum, :bsumm, :days)")
            ->bindValue(':cnum', $id)
            ->bindValue(':bsumm', $balance)
            ->bindValue(':days', $days)
            ->execute();

        return $this->db_conn->getLastInsertID();
    }

    public function getAllCSize (){
        $arr = $this->db_conn->createCommand("select did, value from lgc_dsize order by value")
            ->queryAll();

        $arr = ArrayHelper::map($arr,'did','value');

        return $arr;
    }

    public function getAllFSize (){
        $arr = $this->db_conn->createCommand("select fid, value from lgc_fsize order by value")
            ->queryAll();

        $arr = ArrayHelper::map($arr,'fid','value');

        return $arr;
    }

    public function getFirstFreeCard (){
        $arr = ($this->db_conn->createCommand("SELECT cnum, bsumm FROM lgc_bcards WHERE is_used = 'N' LIMIT 1")
            ->queryAll())[0];

        return $arr;
    }

    private function setCardUsed ($cid) {
        $this->db_conn->createCommand("update lgc_bcards set is_used='Y' where cid=:cid")
            ->bindValue(':cid', $cid)
            ->execute();
    }

    public function createNewUser ($cnum, $fio, $phone, $birth, $sex, $ctype, $csize, $fsize) {
        $cid = $this->getCardId($cnum);

        $this->db_conn->createCommand("insert into lgc_clients (fio, phone, birthday, sex, style, did, fid, cid) values (:fio, :phone, str_to_date(:birthday, '%m.%d.%y'), :sex, :style, :did, :fid, :cid)")
            ->bindValue(':fio', $fio)
            ->bindValue(':phone', $phone)
            ->bindValue(':birthday', $birth)
            ->bindValue(':sex', $sex)
            ->bindValue(':style', $ctype)
            ->bindValue(':did', $csize)
            ->bindValue(':fid', $fsize)
            ->bindValue(':cid', $cid)
            ->execute();

        $uid = $this->db_conn->getLastInsertID();

        $this->setCardUsed($cid);

        return $uid;
    }

    public function updateNewUser ($uid, $cnum, $fio, $phone, $birth, $sex, $ctype, $csize, $fsize) {
        $birth = (strlen($birth) == 0 ? null: $birth);

        $this->db_conn->createCommand("update lgc_clients set fio=:fio, phone=:phone, birthday=str_to_date(:birthday, '%m.%d.%y'), sex=:sex, style=:style, did=:did, fid=:fid where uid=:uid")
            ->bindValue(':fio', $fio)
            ->bindValue(':phone', $phone)
            ->bindValue(':birthday', $birth)
            ->bindValue(':sex', $sex)
            ->bindValue(':style', $ctype)
            ->bindValue(':did', $csize)
            ->bindValue(':fid', $fsize)
            ->bindValue(':uid', $uid)
            ->execute();
    }

// Bonus transaction part

    private function getCardIdbyUid ($uid){
        $arr = ($this->db_conn->createCommand("SELECT cid FROM lgc_clients WHERE uid=:uid")
            ->bindValue(':uid', $uid)
            ->queryAll())[0];

        return $arr['cid'];
    }

    public function getClientParams ($uid) {
        $arr = ($this->db_conn->createCommand("select c.uid, b.cnum, b.bsumm from lgc_clients c, lgc_bcards b where c.cid=b.cid and c.uid=:uid")
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
        $arr = $this->db_conn->createCommand("select date_format(tdate,'%d.%m.%Y') as tdate, summ, bsumm, tdesc, ttype from lgc_btransactions where uid=:uid order by tid limit 5")
            ->bindValue(':uid', $uid)
            ->queryAll();

        return $arr;
    }

}
