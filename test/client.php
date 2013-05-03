<?php
  $base = realpath(dirname(__FILE__) . '/..');
  require "$base/lib/client.php";

  class ClientTest extends PHPUnit_Framework_TestCase
  {
    const API_KEY = 'thmwozah66';
    const API_SECRET = '953c5f9b-cb80-4fef-9950-02062eefedb7';
    const APP_KEY = '24jslzwf6yq699d9t0001066';
    const MESSAGE = 'hello world';

    private static $TOKENS = array('24jslzwf6yq699d9t0001066_0');
    private static $CHANNELS = array('channel1');
    private static $APP_VERSIONS = array('version1');

    protected function setUp()
    {
        $this->client = new Client(self::API_KEY, self::API_SECRET);
    }

    public function testSendNotificationToAll()
    {
      $nid = $this->client->sendNotificationToAll(self::APP_KEY, self::MESSAGE);
      $this->assertTrue(is_int($nid));
    }

    public function testSendNotificationByTokens()
    {
      $nid = $this->client->sendNotificationByTokens(self::APP_KEY, self::$TOKENS, self::MESSAGE);
      $this->assertTrue(is_int($nid));
    }

    public function testSendNotificationByChannels()
    {
      $nid = $this->client->sendNotificationByChannels(self::APP_KEY, self::$CHANNELS, self::MESSAGE);
      $this->assertTrue(is_int($nid));
    }

    public function testSendNotificationByAppVersions()
    {
      $nid = $this->client->sendNotificationByAppVersion(self::APP_KEY, self::$APP_VERSIONS, self::MESSAGE);
      $this->assertTrue(is_int($nid));
    }

    public function testSendNotificationByChannelsAndAppVersion()
    {
      $nid = $this->client->sendNotificationByChannelsAndAppVersion(self::APP_KEY, self::$CHANNELS, self::$APP_VERSIONS, self::MESSAGE);
      $this->assertTrue(is_int($nid));
    }

    public function testQueryNotificationStatus()
    {
      $nid = $this->client->sendNotificationToAll(self::APP_KEY, self::MESSAGE);
      $this->assertTrue(is_int($nid));

      $status = $this->client->queryNotificationStatus(self::APP_KEY, $nid);
      $this->assertArrayHasKey('success', $status);
      $this->assertTrue(is_int($status['success']));
      $this->assertArrayHasKey('failed', $status);
      $this->assertTrue(is_int($status['failed']));
    }
  }
?>