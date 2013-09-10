<?php
  $base = realpath(dirname(__FILE__) . '/..');
  require "$base/lib/client.php";

  class ClientTest extends PHPUnit_Framework_TestCase
  {
    const API_KEY = 'jdyrvdzd79';
    const API_SECRET = 'f4c82714-f0e8-447b-b306-333bcf186ba7';
    const APP_KEY = 'rgutjrl7te2lrmn2r0001318';

    protected function setUp()
    {
      $this->client = new Client(self::API_KEY, self::API_SECRET);
    }

    /**
     * Test happy-pass scenarios.
     */

    public function testSendNotificationToAll()
    {
      $nid = $this->client->sendNotification(self::APP_KEY, 'to all', array());
      $this->assertTrue(is_int($nid));
    }

    public function testSendNotificationToAllWithExtra()
    {
      $options = array(
        'extra' => array(
          'key' => 'value'
        )
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'to all with extra', array(), $options);
      $this->assertTrue(is_int($nid));
    }

    public function testSendNotificationToAllWithEmptyMessageAndExtra()
    {
      $options = array(
        'extra' => array(
          'key' => 'value'
        )
      );

      $nid = $this->client->sendNotification(self::APP_KEY, NULL, array(), $options);
      $this->assertTrue(is_int($nid));
    }

    public function testSendNotificationToAllWithUserData()
    {
      $nid = $this->client->sendNotification(self::APP_KEY, 'to all with userData {{{name}}}', array());
      $this->assertTrue(is_int($nid));
    }

    public function testSendNotificationToAllWithESTIn3MinutesLater()
    {
      $options = array(
        'est' => mktime() + 3 * 60
      );
      $nid = $this->client->sendNotification(self::APP_KEY, 'to all with est in 3 minutes', array(), $options);
      $this->assertTrue(is_int($nid));
    }

    public function testSendNotificationByTokens()
    {
      $target = array(
        'tokens' => array('4fcdd9afa01faea7a404f810e79d326caca88f376a01ce934df3e948e4b3289c')
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'by tokens', $target);
      $this->assertTrue(is_int($nid));
    }

    public function testSendNotificationByInvalidTokensWithUserData()
    {
      $target = array(
        'tokens' => array('invalid tokens')
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'by invalid tokens with userData {{{name}}}', $target);
      $this->assertTrue(is_int($nid));
    }

    public function testSendNotificationByTargetFilters()
    {
      $target = array(
        'appversion' => array('8.0.7', 'invalid appversion'),
        'locationcode' => array('310115', 'invalid locationcode'),
        'taginclude' => array('App', 'invalid taginclude'),
        'tagexclude' => array('invalid tagexclude')
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'by target filters', $target);
      $this->assertTrue(is_int($nid));
    }

    public function testSendNotificationByTargetWithLastLaunchTime()
    {
      $target = array(
        'lastlaunchtime' => date('Y-m-d H:i:s', mktime())
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'by target with last launch time', $target);
      $this->assertTrue(is_int($nid));
    }

    public function testQueryNotificationStatus()
    {
      $nid = $this->client->sendNotification(self::APP_KEY, 'to all for querying status later', array());
      $this->assertTrue(is_int($nid));

      $status = $this->client->queryNotificationStatus(self::APP_KEY, $nid);
      $this->assertArrayHasKey('success', $status);
      $this->assertTrue(is_int($status['success']));
      $this->assertArrayHasKey('failed', $status);
      $this->assertTrue(is_int($status['failed']));
    }

    /**
     * Test server input check.
     */

    /**
     * @expectedException TuisongbaoException
     * @expectedExceptionCode 1
     * @expectedExceptionMessage 认证失败
     */
    public function testSendNotificationToAllWithWrongSecret()
    {
      $client2 = new Client(self::API_KEY, 'wrong scret');
      $nid = $client2->sendNotification('nonexistsapp', 'to all with wrong secret', array());
      $this->assertTrue(is_int($nid));
    }

    /**
     * @expectedException TuisongbaoException
     * @expectedExceptionCode 21
     * @expectedExceptionMessage application不存在
     */
    public function testSendNotificationToNonExistsApp()
    {
      $nid = $this->client->sendNotification('nonexistsapp', 'to non exists app', array());
      $this->assertTrue(is_int($nid));
    }

    /**
     * @expectedException TuisongbaoException
     * @expectedExceptionCode 22
     * @expectedExceptionMessage tokens数量应小于1000
     */
    public function testSendNotificationByTooMuchTokens()
    {
      $tokens = array();
      for ($i = 0; $i < 1001; $i++) {
        $tokens[] = 'token';
      }

      $target = array(
        'tokens' => $tokens
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'by too much tokens', $target);
      $this->assertTrue(is_int($nid));
    }

    /**
     * @expectedException TuisongbaoException
     * @expectedExceptionCode 23
     * @expectedExceptionMessage target筛选条件字段的值不能为空或类型必须是数组
     */
    public function testSendNotificationByTargetWithEmptyFilterValue()
    {
      $target = array(
        'appversion' => array()
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'by target with empty filter value', $target);
      $this->assertTrue(is_int($nid));
    }

    /**
     * @expectedException TuisongbaoException
     * @expectedExceptionCode 23
     * @expectedExceptionMessage target筛选条件字段的值不能为空或类型必须是数组
     */
    public function testSendNotificationByTargetWithWrongFilterValueType()
    {
      $target = array(
        'appversion' => 'should be array'
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'by target with wrong filter value type', $target);
      $this->assertTrue(is_int($nid));
    }

    /**
     * @expectedException TuisongbaoException
     * @expectedExceptionCode 20
     * @expectedExceptionMessage 请求参数不符合要求 或 请求无法处理
     */
    public function testSendNotificationByTargetWithWrongLastLaunchTimeFormat()
    {
      $target = array(
        'lastlaunchtime' => 'wrong format'
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'by target with last launch time', $target);
      $this->assertTrue(is_int($nid));
    }

    /**
     * @expectedException TuisongbaoException
     * @expectedExceptionCode 24
     * @expectedExceptionMessage appversion全部无效
     */
    public function testSendNotificationByTargetWithInvalidAppVersion()
    {
      $target = array(
        'appversion' => array('invalid appversion'),
        'locationcode' => array('310115', 'invalid locationcode'),
        'taginclude' => array('App', 'invalid taginclude'),
        'tageclude' => array('invalid tagexclude')
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'by target with invalid appversion', $target);
      $this->assertTrue(is_int($nid));
    }

    /**
     * @expectedException TuisongbaoException
     * @expectedExceptionCode 25
     * @expectedExceptionMessage locationcode全部无效
     */
    public function testSendNotificationByTargetWithInvalidLocationCode()
    {
      $target = array(
        'locationcode' => array('invalid locationcode'),
        'appversion' => array('8.0.7', 'invalid appversion'),
        'taginclude' => array('App', 'invalid taginclude'),
        'tagexclude' => array('invalid tagexclude'),
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'by target with invalid locationcode', $target);
      $this->assertTrue(is_int($nid));
    }

    /**
     * @expectedException TuisongbaoException
     * @expectedExceptionCode 26
     * @expectedExceptionMessage taginclude全部无效
     */
    public function testSendNotificationByTargetWithInvalidTagInclude()
    {
      $target = array(
        'taginclude' => array('invalid taginclude'),
        'appversion' => array('8.0.7', 'invalid appversion'),
        'locationcode' => array('310115', 'invalid locationcode'),
        'tagexclude' => array('invalid tagexclude')
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'by target with invalid taginclude', $target);
      $this->assertTrue(is_int($nid));
    }

    /**
     * @expectedException TuisongbaoException
     * @expectedExceptionCode 27
     * @expectedExceptionMessage 消息内容和extra总长度应大于0且小于规定长度
     */
    public function testSendNotificationToAllWithEmptyMessageAndEmptyExtra()
    {
      $nid = $this->client->sendNotification(self::APP_KEY, '', array());
      $this->assertTrue(is_int($nid));
    }

    /**
     * @expectedException TuisongbaoException
     * @expectedExceptionCode 27
     * @expectedExceptionMessage 消息内容和extra总长度应大于0且小于规定长度
     */
    public function testSendNotificationToAllWithTooLongMessage()
    {
      $tooLongMessage = 'to all with too long message ';
      while (strlen($tooLongMessage) < 208) {
        $tooLongMessage .= 'm';
      }

      $nid = $this->client->sendNotification(self::APP_KEY, $tooLongMessage, array());
      $this->assertTrue(is_int($nid));
    }

    /**
     * @expectedException TuisongbaoException
     * @expectedExceptionCode 27
     * @expectedExceptionMessage 消息内容和extra总长度应大于0且小于规定长度
     */
    public function testSendNotificationToAllWithTooLongExtra()
    {
      $message = 'to all with too long extra ';
      $tooLongExtraValue = '';
      while ((strlen($tooLongExtraValue) + strlen($message)) < 198) {
        $tooLongExtraValue .= 'm';
      }

      $options = array(
        'extra' => array(
          'key' => $tooLongExtraValue
        )
      );

      $nid = $this->client->sendNotification(self::APP_KEY, $message, array(), $options);
      $this->assertTrue(is_int($nid));
    }

    /**
     * @expectedException TuisongbaoException
     * @expectedExceptionCode 28
     * @expectedExceptionMessage extra的key只能含有英文字母
     */
    public function testSendNotificationToAllWithExtraKeyHasSpecialCharacter()
    {
      $options = array(
        'extra' => array(
          'key.period' => 'value'
        )
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'to all with extra key has special character', array(), $options);
      $this->assertTrue(is_int($nid));
    }

    /**
     * @expectedException TuisongbaoException
     * @expectedExceptionCode 29
     * @expectedExceptionMessage 定时推送时间小于当前时间
     */
    public function testSendNotificationToAllWithExpiredEST()
    {
      $options = array(
        'est' => mktime(19, 20, 0, 9, 9, 2010)
      );
      $nid = $this->client->sendNotification(self::APP_KEY, 'to all with expired est', array(), $options);
      $this->assertTrue(is_int($nid));
    }

    /**
     * @expectedException TuisongbaoException
     * @expectedExceptionCode 30
     * @expectedExceptionMessage 目标设备无效
     */
    public function testSendNotificationByInvalidTarget()
    {
      $target = array(
        'lastlaunchtime' => '2010-01-01 00:00:00'
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'by invalid target', $target);
      $this->assertTrue(is_int($nid));
    }
  }
?>
