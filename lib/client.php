<?php
  require_once 'utils.php';

  if (!function_exists('curl_init')) {
    throw new Exception('tuisongbao needs the CURL PHP extension.');
  }
  if (!function_exists('json_decode')) {
    throw new Exception('tuisongbao needs the JSON PHP extension.');
  }

  class Client
  {
    const FORMAT = 'json';
    const API_VERSION = '1.0';
    const BASE_URL = 'http://rest.tuisongbao.com';
    const NOTIFICATION_URL = '/notification';

    public function __construct($apikey, $apisecret)
    {
      $this->sysParams = array();
      $this->sysParams['apikey'] = $apikey;
      $this->sysParams['apisecret'] = $apisecret;
      $this->sysParams['format'] = self::FORMAT;
      $this->sysParams['v'] = self::API_VERSION;
    }

    public function sendNotificationToAll($appkey, $message, $extra=NULL, $est=NULL)
    {
      $options = array();
      $options['appkey'] = $appkey;
      $options['message'] = $message;
      $options['extra'] = $extra;
      $options['est'] = $est;

      return $this->_sendNotification($options);
    }

    public function sendNotificationByTokens($appkey, $tokens, $message, $extra=NULL, $est=NULL)
    {
      $options = array();
      $options['appkey'] = $appkey;
      $options['tokens'] = $tokens;
      $options['message'] = $message;
      $options['extra'] = $extra;
      $options['est'] = $est;

      return $this->_sendNotification($options);
    }

    public function sendNotificationByChannels($appkey, $channels, $message, $extra=NULL, $est=NULL)
    {
      $options = array();
      $options['appkey'] = $appkey;
      $options['channels'] = $channels;
      $options['message'] = $message;
      $options['extra'] = $extra;
      $options['est'] = $est;

      return $this->_sendNotification($options);
    }

    public function sendNotificationByAppVersion($appkey, $appv, $message, $extra=NULL, $est=NULL)
    {
      $options = array();
      $options['appkey'] = $appkey;
      $options['appv'] = $appv;
      $options['message'] = $message;
      $options['extra'] = $extra;
      $options['est'] = $est;

      return $this->_sendNotification($options);
    }

    public function sendNotificationByChannelsAndAppVersion($appkey, $channels, $appv, $message, $extra=NULL, $est=NULL)
    {
      $options = array();
      $options['appkey'] = $appkey;
      $options['channels'] = $channels;
      $options['appv'] = $appv;
      $options['message'] = $message;
      $options['extra'] = $extra;
      $options['est'] = $est;

      return $this->_sendNotification($options);
    }

    private function _sendNotification($options)
    {
      if (array_key_exists('extra', $options)) {
        if (is_null($options['extra'])) {
          unset($options['extra']);
        }
      }

      if (array_key_exists('est', $options)) {
        if (is_null($options['est'])) {
          unset($options['est']);
        } else {
          $options['est'] = Utils::formatDatetime($options['est']);
        }
      }

      $params = $options + $this->sysParams;
      unset($params['apisecret']);

      $paramsToSign = array() + $this->sysParams;
      unset($paramsToSign['apisecret']);
      $paramsToSign['appkey'] = $options['appkey'];
      $paramsToSign['message'] = $options['message'];
      if (array_key_exists('est', $options)) {
        if ($options['est']) {
          $paramsToSign['est'] = $options['est'];
        }
      }

      $result = $this->_request(self::BASE_URL . self::NOTIFICATION_URL, $params, $paramsToSign, false);

      return $result['nid'];
    }

    public function queryNotificationStatus($appkey, $nid)
    {
      $params = array('appkey' => $appkey);
      $params = $params + $this->sysParams;
      unset($params['apisecret']);

      $result = $this->_request(self::BASE_URL . self::NOTIFICATION_URL . '/' . $nid, $params, $params);

      return array('success' => $result['success'], 'failed' => $result['failed']);
    }

    private function _request($url, $params, $paramsToSign, $get=TRUE)
    {
      $params['timestamp'] = $paramsToSign['timestamp'] = Utils::formatDatetime();
      $params['sign'] = Utils::md5Sign($paramsToSign, $this->sysParams['apisecret']);

      $ch = curl_init($url);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
      if ($get) {
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
      } else {
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type' => 'application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
      }

      $result = curl_exec($ch);

      if (curl_errno($ch)) {
        throw new TuisongbaoException('curl error: ' . curl_error($ch));
      }

      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if($httpCode != 200) {
        throw new TuisongbaoException('http request failed, httpCode: '.$httpCode);
      }

      $result = json_decode($result, TRUE);
      if (is_null($result)) {
        throw new TuisongbaoException('got invalid response');
      }

      if ($result['ack'] != '0') {
        throw new TuisongbaoException($result['error'], $result['ack']);
      }

      curl_close($ch);

      return $result;
    }
  }

  class TuisongbaoException extends Exception
  {
    public function __construct($message, $code = NUll)
    {
      parent::__construct($message, $code);
    }

    public function __toString()
    {
      $msg = __CLASS__.'{$this->message}';
      if (!is_null($this->code)) {
        $msg.', ack: {$this->code}';
      }

      return $msg.'\n';
    }
  }
?>