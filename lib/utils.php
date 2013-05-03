<?php
  class Utils
  {
    const TIMEZONE_SHANGHAI = 'Asia/Shanghai';
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    public static function formatDatetime($timestamp=NULL)
    {
      $defaultTimezone = date_default_timezone_get();
      date_default_timezone_set(self::TIMEZONE_SHANGHAI);

      if (is_null($timestamp)) {
        $timestamp = time();
      }

      $result = date(self::DATETIME_FORMAT, $timestamp);

      date_default_timezone_set($defaultTimezone);

      return $result;
    }

    public static function md5Sign($params, $secret)
    {
      ksort($params);
      $strToSign = $secret;
      foreach ($params as $key => $value) {
        $strToSign .= $key . $value;
      }
      $strToSign .= $secret;

      return strtoupper(md5($strToSign));
    }
  }
?>