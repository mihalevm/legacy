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
class CreateCardForm extends Model {
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
    public function updateBonusBalance ($cid, $balance) {
        $this->db_conn->createCommand("update lgc_bcards set bsumm=:bsumm where cid=:cid")
            ->bindValue(':cid', $cid)
            ->bindValue(':bsumm', $balance)
            ->execute();
    }
}