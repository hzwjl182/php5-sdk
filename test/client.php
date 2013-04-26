<?php
  $base = realpath(dirname(__FILE__) . '/..');
  require "$base/lib/client.php";

  class ClientTest extends PHPUnit_Framework_TestCase
  {
    const API_KEY = 'thmwozah66';
    const API_SECRET = '953c5f9b-cb80-4fef-9950-02062eefedb7';
    const APP_KEY = 'wa9ilthanjizwd19w0001030';
    const MESSAGE = 'hello world';

    private static $TOKENS = array('token1');
    private static $CHANNELS = array('channels');
    private static $APP_VERSIONS = array('version1');

    protected function setUp()
    {
        $this->client = new Client(self::API_KEY, self::API_SECRET);
    }

    public function testPushNotificationToAll()
    {
      $nid = $this->client->pushNotificationToAll(self::APP_KEY, self::MESSAGE);
      $this->assertTrue(is_int($nid));
    }

    public function testPushNotificationByTokens()
    {
      $nid = $this->client->pushNotificationByTokens(self::APP_KEY, self::$TOKENS, self::MESSAGE);
      $this->assertTrue(is_int($nid));
    }

    /**
     * @expectedException Exception
     */
    public function testPushNotificationByChannels()
    {
      $this->client->pushNOtificationByChannels(self::APP_KEY, self::$CHANNELS, self::MESSAGE);
    }

    /**
     * @expectedException Exception
     */
    public function testPushNotificationByAppVersions()
    {
      $this->client->pushNOtificationByAppVersion(self::APP_KEY, self::$APP_VERSIONS, self::MESSAGE);
    }

    /**
     * @expectedException Exception
     */
    public function testPushNotificationByChannelsAndAppVersion()
    {
      $this->client->pushNOtificationByChannelsAndAppVersion(self::APP_KEY, self::$CHANNELS, self::$APP_VERSIONS, self::MESSAGE);
    }

    public function testQueryNotificationStatus()
    {
      $nid = $this->client->pushNotificationToAll(self::APP_KEY, self::MESSAGE);
      $this->assertTrue(is_int($nid));

      $status = $this->client->queryNotificationStatus(self::APP_KEY, $nid);
      $this->assertArrayHasKey('success', $status);
      $this->assertTrue(is_int($status['success']));
      $this->assertArrayHasKey('failed', $status);
      $this->assertTrue(is_int($status['failed']));
    }
  }
?>