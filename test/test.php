<?php
  $base = realpath(dirname(__FILE__) . '/..');
  require "$base/lib/client.php";

  const API_KEY = 'thmwozah66';
  const API_SECRET = '953c5f9b-cb80-4fef-9950-02062eefedb7';
  const APP_KEY = 'wa9ilthanjizwd19w0001030';
  const MESSAGE = 'hello world';

  $client = new Client(API_KEY, API_SECRET);
  $nid = $client->pushNotificationToAll(APP_KEY, MESSAGE);
  print($nid);
?>