<?php
/**
 * Created by PhpStorm.
 * User: mmv
 * Date: 05.08.2019
 * Time: 11:15
 */

namespace app\models;

use Yii;
use yii\httpclient\Client;
use yii\base\Model;

class FsspForm extends Model {
    protected $db_conn;

    function __constructor () {
        $this->db_conn = Yii::$app->db;
    }

    private function getHttpClient ( $sid ) {
        $host = 'is.fssprus.ru';
        $http = 'https://'.$host;
        $url  = $http.'/ajax_search';

        $client = new Client();

        $response = $client->createRequest()
            ->setOptions([
                'timeout' => 2
            ])
            ->setMethod('get')
            ->setUrl($url)
            ->setHeaders([
                'Accept'           => 'application/json, text/javascript, */*; q=0.01',
                'Accept-Encoding'  => 'gzip, deflate, br',
                'Accept-Language'  => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
                'Cache-Control'    => 'no-cache',
                'Connection'       => 'keep-alive',
                'Content-Type'     => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Host'             => $host,
                'Pragma'           => 'no-cache',
                'Referer'          => $http,
                'User-Agent'       => 'runscope/0.1,Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:39.0) Gecko/20100101 Firefox/39.0',
                'X-Requested-With' => 'XMLHttpRequest',
                'Cookie'           => 'connect.sid='.$sid.';',
            ]);

        return $response;
    }

    private function parseSumm($str){
        $summ = 0;
        $matches = null;
        preg_match_all('/.+:\s(\d+\.\d\d)/U', $str, $matches);

        if (count($matches) > 0) {
            foreach ($matches[1] as $isumm) {
                $summ += floatval($isumm);
            }
        }

        return $summ;
    }

    private function parseContent ($html) {
        $dom = new \DOMDocument();
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOERROR);
        $sum = 0;

        $all_tr = $dom->getElementsByTagName('tr');

        foreach ($all_tr as $tr) {
            $node = $tr->childNodes;

            if ($node->length > 1){
                $sum += $this->parseSumm($node->item(5)->nodeValue);
            }
        }

        return $sum;
    }

    public function Send_Grab($sid, $code, $fn, $sn, $mn, $bd) {
        $ts       = time();
        $response = $this->getHttpClient($sid);
        $answer   = null;

        $response->setData([
            'is' =>[
                'ip_preg'    => '',
                'variant'    => '1',
                'last_name'  => $sn,
                'first_name' => $fn,
                'patronymic' => $mn,
                'date'       => $bd,
                'drtr_name'  => '',
                'address'    => '',
                'ip_number'  => '',
                'id_number'  => '',
                'id_type'    => [],
                'id_issuer'  => '',
                'region_id'  => [-1],
                'extended'   => 1,
            ],
            'code'     => $code,
            'nocache'  => 1,
            'system'   => 'ip',
            'callback' => 'jQuery340016456929004994936_'.$ts,
            '_'        => $ts,
        ]);

        try {
            $answer = $response->send();
        } catch (\Exception $e) {
            $answer = null;
        }

        if (null != $answer  && $answer->getIsOk() ){
            if (!strstr($answer->content, "Неверно введен код") ) {
                $matches = null;
                preg_match('/\(\{\"data\":\"(.+)\",\"/', $answer->content, $matches);
                $answer = null;
                $content = $matches[1];
                $content = str_replace('\r\n', '', $content );
                $content = str_replace('  ', '', $content   );
                $content = str_replace('\"', '"', $content  );


                $answer['data']  = $this->parseContent($content);
                $answer['error'] = 200;
            } else {
                $answer = null;
                $answer['data'] = 'Код с картинки введен не верно.';
                $answer['error'] = 400;
            }
        } else {
            $answer['data'] = 'Сервис временно не доступен.<br/>Нажмите кнопку "Повторить".';
            $answer['error'] = 500;
        }

        return $answer;
    }

    public function GetCaptcha() {
        $answer = null;
        $captcha = [];

        $response = $this->getHttpClient('');

        $response->setData([
            'is' =>[
                'ip_preg'    => '',
                'variant'    => '1',
                'drtr_name'  => '',
                'address'    => '',
                'ip_number'  => '',
                'id_number'  => '',
                'id_type'    => [],
                'id_issuer'  => '',
                'region_id'  => [-1],
                'extended'   => 1,
            ],
            'nocache' => 1,
            'system'  => 'ip',
        ]);

        try {
            $answer = $response->send();
        } catch (\Exception $e) {
            $answer = null;
        }

        if (null != $answer  && $answer->getIsOk() ){
            $matches = null;
            preg_match('/\"(data:image.+)\\\"\sid=\\\"capchaVisual/', $answer->content, $matches);
            $captcha['captcha'] = $matches[1];
            $captcha['cookies'] = $answer->getCookies();
            $captcha['error']   = 200;

            if ($captcha['cookies']->get('connect.sid')) {
                $captcha['cookies'] = $captcha['cookies']->get('connect.sid')->value;
            }
        } else {
            $captcha['captcha'] = '';
            $captcha['cookies'] = '';
            $captcha['error']   = 500;
        }

        return $captcha;
    }
}