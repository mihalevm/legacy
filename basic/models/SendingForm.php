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

    private function generateRandomString($length = 30) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    private function getFinished ($slid) {
        $cnt_all = ($this->db_conn->createCommand("SELECT COUNT(*) as cnt_all FROM lgc_smssendstat WHERE slid=:s")
            ->bindValue(':s', $slid)
            ->queryAll())[0]['cnt_all'];

        $cnt_sended = ($this->db_conn->createCommand("SELECT COUNT(*) as cnt_sended FROM lgc_smssendstat WHERE slid=:s AND sended='Y'")
            ->bindValue(':s', $slid)
            ->queryAll())[0]['cnt_sended'];

        return intval(100*$cnt_sended/$cnt_all);
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
        $this->db_conn->createCommand("update lgc_smssendlist set sname=:sname, sdate=str_to_date(:sdate, '%d.%m.%Y'), message=:msg where slid=:slid", [
            ':sname' => '',
            ':sdate' => '',
            ':msg' => '',
            ':slid' => 0
        ])
            ->bindValue(':sname', $sname)
            ->bindValue(':sdate', $sdate)
            ->bindValue(':msg',     $msg)
            ->bindValue(':slid',   $slid)
            ->execute();

        return 1;
    }

    public function getSMSSendInsert ($sdate, $sname, $msg) {
        $slid = null;

        $this->db_conn->createCommand("insert into lgc_smssendlist (sdate, sname, message) values (str_to_date(:sdate, '%d.%m.%Y'), :sname, :msg)", [
            ':sdate' => '',
            ':sname' => '',
            ':msg' => ''
        ])
            ->bindValue(':sname', $sname)
            ->bindValue(':sdate', $sdate)
            ->bindValue(':msg',     $msg)
            ->execute();

        $slid = $this->db_conn->getLastInsertID();

        if ($slid) {
            $this->db_conn->createCommand("insert into lgc_smssendstat (slid, uid) select :slid, uid from lgc_clients where disabled='N'", [':slid' => 0])
                ->bindValue(':slid', $slid)
                ->execute();
        }

        return $slid;
    }

    public function getSMSSendDelete ($slid) {
        $this->db_conn->createCommand("delete from lgc_smssendlist where slid=:slid", [':slid' => 0])
            ->bindValue(':slid',   $slid)
            ->execute();

        $this->db_conn->createCommand("delete from lgc_smssendstat where slid=:slid", [':slid' => 0])
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
        $pattern = $this->generateRandomString();

        $this->db_conn->createCommand("UPDATE lgc_smssendstat SET sended=:pattern WHERE ssid IN (select ssid  FROM (SELECT ssid FROM lgc_smssendstat WHERE sended='N' AND slid=:slid LIMIT 10) tmp )", [
            ':pattern' => '',
            ':slid' => 0
        ])
            ->bindValue(':pattern', $pattern)
            ->bindValue(':slid',   $slid)
            ->execute();

        $arr = $this->db_conn->createCommand("SELECT s.ssid as id, c.phone AS ph from lgc_smssendstat s, lgc_clients c WHERE s.slid=:slid and s.uid=c.uid AND c.disabled='N' AND s.sended=:pattern ", [
            ':slid' => 0,
            ':pattern' => ''
        ])
            ->bindValue(':slid', $slid)
            ->bindValue(':pattern', $pattern)
            ->queryAll();

        return $arr;
    }

    public function rest_setSMSSendedItem($ssid, $is_send) {
        $is_send = $is_send == 's' ? 'Y' : 'N';

        $this->db_conn->createCommand("update lgc_smssendstat set sended=:is_send where ssid=:ssid", [
            ':is_send' => 'N',
            ':ssid' => 0
        ])
            ->bindValue(':is_send', $is_send)
            ->bindValue(':ssid', $ssid)
            ->execute();

        $slid = ($this->db_conn->createCommand("select slid from lgc_smssendstat where ssid=:s")
            ->bindValue(':s', $ssid)
            ->queryAll())[0]['slid'];

        $prc = $this->getFinished($slid);

        $this->db_conn->createCommand("update lgc_smssendlist set prc=:prc where slid=:slid", [
            ':prc' => 0,
            ':slid' => 0
        ])
            ->bindValue(':slid', $slid)
            ->bindValue(':prc',   $prc)
            ->execute();

        return $prc;
    }
}
