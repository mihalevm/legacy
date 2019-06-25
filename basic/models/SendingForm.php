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

    public function rest_getSMSSending () {
        $arr = $this->db_conn->createCommand("select slid as id, sname as title, message as msg from lgc_smssendlist where prc<>100 and sdate <= CURRENT_DATE()")
            ->queryAll();

        return $arr;
    }

    public function rest_getSMSSendingItems($slid) {
        $arr = $this->db_conn->createCommand("SELECT s.ssid as id, c.phone AS ph from lgc_smssendstat s, lgc_clients c WHERE s.slid=:slid and s.uid=c.uid AND s.sended='N' LIMIT 10")
            ->bindValue(':slid', $slid)
            ->queryAll();

        return $arr;
    }

    public function rest_setSMSSendedItem($ssid) {
        $arr = $this->db_conn->createCommand("update lgc_smssendstat set sended='Y' where ssid=:ssid")
            ->bindValue(':ssid', $ssid)
            ->queryAll();

        return $arr;
    }
}
