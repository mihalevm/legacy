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
class SendingForm extends Model {
    protected $db_conn;

    function __construct () {
        $this->db_conn = Yii::$app->db;
    }

    public function getSMSSending () {
        $arr = $this->db_conn->createCommand("select slid, sname, message, date_format(cdate,'%d.%m.%Y') as cdate, date_format(sdate,'%d.%m.%Y') as sdate, prc from lgc_smssendlist")
            ->queryAll();

        return $arr;
    }

    public function getSMSSend ($slid) {
        $arr = $this->db_conn->createCommand("select slid, sname, message, date_format(cdate,'%d.%m.%Y') as cdate, date_format(sdate,'%d.%m.%Y') as sdate, prc from lgc_smssendlist where slid=:s")
            ->bindValue(':s', $slid)
            ->queryAll();

        return $arr[0];
    }

    public function getSMSSendUpdate ($slid, $sdate, $sname, $msg) {
        $this->db_conn->createCommand("update lgc_smssendlist set sname=:sname, sdate=str_to_date(:sdate, '%d.%m.%Y'), message=:msg where slid=:slid")
            ->bindValue(':sname', $sname)
            ->bindValue(':sdate', $sdate)
            ->bindValue(':msg',     $msg)
            ->bindValue(':slid',   $slid)
            ->execute();

        return 1;
    }

    public function getSMSSendInsert ($sdate, $sname, $msg) {
        $slid = null;

        $this->db_conn->createCommand("insert into lgc_smssendlist (sdate, sname, message) values (str_to_date(:sdate, '%d.%m.%Y'), :sname, :msg)")
            ->bindValue(':sname', $sname)
            ->bindValue(':sdate', $sdate)
            ->bindValue(':msg',     $msg)
            ->execute();

        $slid = $this->db_conn->getLastInsertID();

        if ($slid) {
            $this->db_conn->createCommand("insert into lgc_smssendstat (slid, uid) select :slid, uid from lgc_clients where disabled='N'")
                ->bindValue(':slid', $slid)
                ->execute();
        }

        return $slid;
    }

    public function getSMSSendDelete ($slid) {
        $this->db_conn->createCommand("delete from lgc_smssendlist where slid=:slid")
            ->bindValue(':slid',   $slid)
            ->execute();

        $this->db_conn->createCommand("delete from lgc_smssendstat where slid=:slid")
            ->bindValue(':slid',   $slid)
            ->execute();

        return 1;
    }
}
