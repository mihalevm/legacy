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
class NewClientForm extends Model {
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

    private function getCardId ($cnum){
        $arr = ($this->db_conn->createCommand("SELECT cid FROM lgc_bcards WHERE cnum=:cnum")
            ->bindValue(':cnum', $cnum)
            ->queryAll())[0];

        return $arr['cid'];
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

}
