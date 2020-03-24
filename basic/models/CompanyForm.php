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
class CompanyForm extends Model {
    public $sdate;
    public $edate;
    protected $db_conn;

    function __construct () {
        $this->db_conn = Yii::$app->db;
    }

    public function getAllCompany () {
        $arr = $this->db_conn->createCommand("select coid, name, manager, contacts, disabled from lgc_company")
            ->queryAll();

        return $arr;
    }

    public function createCompany ($coname, $manager, $contacts, $disabled) {
        $this->db_conn->createCommand("insert into lgc_company (name, manager, contacts, disabled) values (:coname, :manager, :contacts, :disabled)", [
            ':coname' => '',
            ':manager' => '',
            ':contacts' => '',
            ':disabled' => 'N'
        ])
            ->bindValue(':coname', $coname)
            ->bindValue(':manager', $manager)
            ->bindValue(':contacts', $contacts)
            ->bindValue(':disabled', $disabled == 1 ? 'Y':'N')
            ->execute();

        return $this->db_conn->getLastInsertID();
    }

    public function updateCompany ($coid, $coname, $manager, $contacts, $disabled) {
        $this->db_conn->createCommand("update lgc_company set name=:name, manager=:manager, contacts=:contacts, disabled=:disabled where coid=:coid", [
            ':name' => '',
            ':manager' => '',
            ':contacts' => '',
            ':disabled' => 'N',
            ':coid' => ''
        ])
            ->bindValue(':name', $coname)
            ->bindValue(':manager', $manager)
            ->bindValue(':contacts', $contacts)
            ->bindValue(':disabled', $disabled === 1 ? 'Y':'N')
            ->bindValue(':coid', $coid)
            ->execute();

        return 1;
    }

/* Not mine  */
    public function getClientParams ($uid) {
        $arr = ($this->db_conn->createCommand("select uid, fio from lgc_clients where uid=:uid", [':uid' => '0'])
            ->bindValue(':uid', $uid)
            ->queryAll())[0];

        return $arr;
    }

    public function getAllTransactions ($uid, $sdate, $edate) {
        $arr = $this->db_conn->createCommand("select tid, date_format(tdate,'%d.%m.%Y') as tdate, summ, bsumm, tdesc, ttype from lgc_btransactions where uid=:uid and tdate >= timestamp(str_to_date(:sdate, '%d.%m.%Y')) and tdate <= timestamp(str_to_date(:edate, '%d.%m.%Y')) order by tid")
            ->bindValue(':sdate', $sdate)
            ->bindValue(':edate', $edate)
            ->bindValue(':uid', $uid)
            ->queryAll();

        return $arr;
    }

    public  function getTransaction ($tid) {
        $arr = ($this->db_conn->createCommand("select cid, date_format(tdate,'%d.%m.%Y') as tdate, summ, bsumm, tdesc, ttype from lgc_btransactions where tid=:tid")
            ->bindValue(':tid', $tid)
            ->queryAll())[0];

        return $arr;
    }

    public function setTransaction ($tid, $boper, $summ, $bsumm, $descr) {
        $transaction = $this->getTransaction($tid);
        $cid         = $transaction['cid'];
        $operation   = $transaction['ttype'];
        $bonus       = $transaction['bsumm'];

        $current_bsumm = ($this->db_conn->createCommand("select bsumm from lgc_bcards where cid=:cid")
            ->bindValue(':cid', $cid)
            ->queryAll())[0];

        $current_bsumm = $current_bsumm['bsumm'];

        if ($operation == 'a'){
            $current_bsumm = $current_bsumm-$bonus;
        }
        if ($operation == 's'){
            $current_bsumm = $current_bsumm+$bonus;
        }
        if($boper == 'a'){
            $current_bsumm = $current_bsumm+$bsumm;
        }
        if($boper == 's'){
            $current_bsumm = $current_bsumm-$bsumm;
        }

        $current_bsumm = $current_bsumm > 0 ? $current_bsumm : 0;

        $this->db_conn->createCommand("update lgc_btransactions set bsumm=:bsumm, summ=:summ, ttype=:ttype, tdesc=:tdesc where tid=:tid")
            ->bindValue(':bsumm', $bsumm)
            ->bindValue(':tid', $tid)
            ->bindValue(':summ', $summ)
            ->bindValue(':ttype', $boper)
            ->bindValue(':tdesc', $descr)
            ->execute();

        $this->db_conn->createCommand("update lgc_bcards set bsumm=:bsumm where cid=:cid")
            ->bindValue(':bsumm', $current_bsumm)
            ->bindValue(':cid', $cid)
            ->execute();

        return 1;
    }

    public function DelTransaction ($tid) {
        $transaction = $this->getTransaction($tid);
        $cid         = $transaction['cid'];
        $operation   = $transaction['ttype'];
        $bonus       = $transaction['bsumm'];

        $current_bsumm = ($this->db_conn->createCommand("select bsumm from lgc_bcards where cid=:cid")
            ->bindValue(':cid', $cid)
            ->queryAll())[0];

        $current_bsumm = $current_bsumm['bsumm'];

        if ($operation == 'a'){
            $current_bsumm = $current_bsumm-$bonus;
        }
        if ($operation == 's'){
            $current_bsumm = $current_bsumm+$bonus;
        }

        $this->db_conn->createCommand("delete from lgc_btransactions where tid=:tid")
            ->bindValue(':tid', $tid)
            ->execute();

        $this->db_conn->createCommand("update lgc_bcards set bsumm=:bsumm where cid=:cid")
            ->bindValue(':bsumm', $current_bsumm)
            ->bindValue(':cid', $cid)
            ->execute();

        return 1;
    }
}
