<?php
  $base = realpath(dirname(__FILE__).'/..');
  require "$base/lib/client.php";

  class ClientTest extends PHPUnit_Framework_TestCase
  {
    const API_KEY = 'thmwozah66';
    const API_SECRET = '953c5f9b-cb80-4fef-9950-02062eefedb7';
    const APP_KEY = 'wa9ilthanjizwd19w0001030';
    const MESSAGE = 'hello world';

    protected function setUp() {
        $this->client = new Client(self::API_KEY, self::API_SECRET);
    }

    public function testPushNotificationToAll() {
      $nid = $this->client->pushNotificationToAll(self::APP_KEY, self::MESSAGE);
      $this->assertTrue(is_int($nid));
    }
  }
?>