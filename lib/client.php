<?php
  require_once 'utils.php';

  date_default_timezone_set('Asia/Shanghai');

  class Client
  {
    const FORMAT = 'json';
    const API_VERSION = '1.0';
    const DATETIME_FORMAT = 'Y-m-d H:i:s';
    const BASE_URL = 'http://rest.tuisongbao.com';
    const NOTIFICATION_URL = '/notification';

    private $sysParams = array();

    public function __construct($apikey, $apisecret) {
      $this->sysParams['apikey'] = $apikey;
      $this->sysParams['apisecret'] = $apisecret;
      $this->sysParams['format'] = self::FORMAT;
      $this->sysParams['v'] = self::API_VERSION;
    }

    public function pushNotificationToAll($appkey, $message, $extra=NULL, $est=NULL) {
      $options = array();
      $options['appkey'] = $appkey;
      $options['message'] = $message;
      $options['extra'] = $extra;
      $options['est'] = $est;
      return $this->_pushNotification($options);
    }

    private function _pushNotification($options) {
      if (array_key_exists('extra', $options)) {
        if (is_null($options['extra'])) {
          unset($options['extra']);
        }
      }

      if (array_key_exists('est', $options)) {
        if (is_null($options['est'])) {
          unset($options['est']);
        } else {
          $options['est'] = date(self::DATETIME_FORMAT, $options['est']);
        }
      }

      $params = array_merge($options, $this->sysParams);

      unset($params['apisecret']);

      $paramsToSign = array_merge(array(), $this->sysParams);
      unset($paramsToSign['apisecret']);
      $paramsToSign['appkey'] = $options['appkey'];
      $paramsToSign['message'] = $options['message'];
      if (array_key_exists('est', $options)) {
        if ($options['est']) {
          $paramsToSign['est'] = $options['est'];
        }
      }

      $result = $this->_request(self::BASE_URL.self::NOTIFICATION_URL, $params, $paramsToSign, false);

      return $result['nid'];
    }

    private function _request($url, $params, $paramsToSign, $get=ture) {
      $params['timestamp'] = $paramsToSign['timestamp'] = date(self::DATETIME_FORMAT);
      $params['sign'] = Utils::md5Sign($params, $this->sysParams['apisecret']);

      $ch = curl_init($url);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
      if ($get) {
        curl_setopt($ch, CURLOPT_URL, $url + '?' + http_build_query($params));
      } else {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type' => 'application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
      }

      $result = curl_exec($ch);

      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if($httpCode != 200) {
        throw new Exception('http request failed, httpCode: '.$httpCode);
      }

      $result = json_decode($result, TRUE);
      if ($result['ack'] != '200') {
        throw new Exception($result['err']);
      }

      curl_close($ch);

      return $result;
    }
  }
?>