<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use app\models\CreateCardForm;


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
        $this->db_conn->createCommand("insert into lgc_bcards (cnum, bsumm, days) values (:cnum, :bsumm, :days)", [
            ':cnum' => '',
            ':bsumm' => '',
            ':days' => ''
        ])
            ->bindValue(':cnum', $id)
            ->bindValue(':bsumm', $balance)
            ->bindValue(':days', $days)
            ->execute();

        return $this->db_conn->getLastInsertID();
    }

    public function getNextFreeCard (){
        $arr = ($this->db_conn->createCommand("SELECT MIN(t1.cnum + 1) AS cid FROM lgc_bcards t1 LEFT JOIN lgc_bcards t2 ON t1.cnum + 1 = t2.cnum WHERE t2.cnum IS NULL")
            ->queryAll())[0];

        return $arr['cid'];

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

    private function getCardId ($cnum){
        $arr = ($this->db_conn->createCommand("SELECT cid FROM lgc_bcards WHERE cnum=:cnum", [':cnum' => ''])
            ->bindValue(':cnum', $cnum)
            ->queryAll())[0];

        return $arr['cid'];
    }

    private function setCardUsed ($cid) {
        $this->db_conn->createCommand("update lgc_bcards set is_used='Y' where cid=:cid", [':cid' => ''])
            ->bindValue(':cid', $cid)
            ->execute();
    }

    public function createNewUser ($cnum, $bblnc, $nc, $fio, $phone, $birth, $sex, $ctype, $csize, $fsize, $spoint) {
        $cid = NULL;
        $birth = (strlen($birth) == 0 ? null: $birth);
        $new_card = new CreateCardForm();

        if (intval($nc) == 1){
            $cid = $new_card->addNewCard($cnum, $bblnc, 0);
        } else {
            $cid = $this->getCardId($cnum);
        }

        $this->db_conn->createCommand("insert into lgc_clients (fio, phone, birthday, sex, style, did, fid, cid, spoint) values (:fio, :phone, str_to_date(:birthday, '%d.%m.%Y'), :sex, :style, :did, :fid, :cid, :spoint)", [
            ':fio' => '',
            ':phone' => '',
            ':birthday' => '',
            ':sex' => '',
            ':style' => '',
            ':did' => '',
            ':fid' => '',
            ':cid' => '',
            ':spoint' => ''
        ])
            ->bindValue(':fio', $fio)
            ->bindValue(':phone', $phone)
            ->bindValue(':birthday', $birth)
            ->bindValue(':sex', $sex)
            ->bindValue(':style', $ctype)
            ->bindValue(':did', $csize)
            ->bindValue(':fid', $fsize)
            ->bindValue(':cid', $cid)
            ->bindValue(':spoint', $spoint)
            ->execute();

        $uid = $this->db_conn->getLastInsertID();

        $this->setCardUsed($cid);
        $new_card->updateBonusBalance($cid, $bblnc);

        return $uid;
    }

    public function updateNewUser ($uid, $cnum, $fio, $phone, $birth, $sex, $ctype, $csize, $fsize, $spoint) {
        $birth = (strlen($birth) == 0 ? null: $birth);

        $this->db_conn->createCommand("update lgc_clients set fio=:fio, phone=:phone, birthday=str_to_date(:birthday, '%d.%m.%Y'), sex=:sex, style=:style, did=:did, fid=:fid, spoint=:spoint where uid=:uid", [
            ':fio' => '',
            ':phone' => '',
            ':birthday' => '',
            ':sex' => '',
            ':style' => '',
            ':did' => '',
            ':fid' => '',
            ':spoint' => '',
            ':uid' => ''
        ])
            ->bindValue(':fio', $fio)
            ->bindValue(':phone', $phone)
            ->bindValue(':birthday', $birth)
            ->bindValue(':sex', $sex)
            ->bindValue(':style', $ctype)
            ->bindValue(':did', $csize)
            ->bindValue(':fid', $fsize)
            ->bindValue(':uid', $uid)
            ->bindValue(':spoint', $spoint)
            ->execute();
    }

    public function checkNewCard($cnum) {
        $arr = NULL;

        $arr = $this->db_conn->createCommand("SELECT bsumm, is_used, disabled, days FROM lgc_bcards WHERE cnum=:cnum", [':cnum' => ''])
            ->bindValue(':cnum', $cnum)
            ->queryAll();

        if (sizeof($arr)>0) {
            $arr = $arr[0];
        }else{
            $arr = NULL;
        }

        return $arr;
    }

    public function getUnqStyles(){
        $arr = NULL;

        $arr = $this->db_conn->createCommand("SELECT DISTINCT LOWER(style) as style FROM lgc_clients WHERE length(style) > 0 ORDER BY style")
            ->queryAll();

        return $arr;
    }
}
