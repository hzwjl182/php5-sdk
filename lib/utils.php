<?php
  class Utils
  {
    public static function md5Sign($params, $secret) {
      ksort($params);
      $strToSign = $secret;
      foreach ($params as $key => $value) {
        $strToSign .= $key.$value;
      }
      $strToSign .= $secret;

      return strtoupper(md5($strToSign));
    }
  }
?>