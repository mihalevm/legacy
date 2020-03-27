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
class ClientCardForm extends Model {
    protected $db_conn;

    function __construct () {
        $this->db_conn = Yii::$app->db;
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

    public function updateNewUser ($uid, $cnum, $fio, $phone, $birth, $sex, $ctype, $csize, $fsize, $spoint, $coid) {
        $birth = (strlen($birth) == 0 ? null: $birth);

        $this->db_conn->createCommand("update lgc_clients set fio=:fio, phone=:phone, birthday=str_to_date(:birthday, '%d.%m.%Y'), sex=:sex, style=:style, did=:did, fid=:fid, spoint=:spoint, coid=:coid where uid=:uid")
            ->bindValue(':fio', $fio)
            ->bindValue(':phone', $phone)
            ->bindValue(':birthday', $birth)
            ->bindValue(':sex', $sex)
            ->bindValue(':style', $ctype)
            ->bindValue(':did', $csize)
            ->bindValue(':fid', $fsize)
            ->bindValue(':uid', $uid)
            ->bindValue(':coid', $coid)
            ->bindValue(':spoint', $spoint)
            ->execute();
    }

    public function getClientParams ($uid) {
        $arr = ($this->db_conn->createCommand("select c.uid, c.fio, c.phone, date_format(c.birthday,'%d%m%Y') as birthday, c.sex, c.style, c.did, c.fid, b.cnum, b.bsumm, c.spoint, c.coid, c.cbalance from lgc_clients c, lgc_bcards b where c.cid=b.cid and c.uid=:uid")
            ->bindValue(':uid', $uid)
            ->queryAll())[0];

        return $arr;

    }

    public function DeleteUser ($uid) {
        $this->db_conn->createCommand("update lgc_clients set disabled='Y' where uid=:uid")
            ->bindValue(':uid', $uid)
            ->execute();
    }

    public function getUnqStyles(){
        $arr = NULL;

        $arr = $this->db_conn->createCommand("SELECT DISTINCT LOWER(style) as style FROM lgc_clients WHERE length(style) > 0 ORDER BY style")
            ->queryAll();

        return $arr;
    }
}
