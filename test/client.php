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

    public function testSendNotificationByTokens()
    {
      $target = array(
        'tokens' => array('4fcdd9afa01faea7a404f810e79d326caca88f376a01ce934df3e948e4b3289c')
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'by tokens', $target);
      $this->assertTrue(is_int($nid));
    }

    public function testSendNotificationByTargetFilters()
    {
      $target = array(
        'appversion' => array('8.0.7'),
        'tagInclude' => array('App')
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'by other target filters', $target);
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
      for ($i = 0; $i < 300; $i++) {
        $tokens + array('token');
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
     * @expectedExceptionCode 24
     * @expectedExceptionMessage appversion全部无效
     */
    public function testSendNotificationByTargetWithInvalidAppVersion()
    {
      $target = array(
        'appversion' => ['invalid appversion']
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
        'locationcode' => ['invalid locationcode']
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
        'taginclude' => ['invalid taginclude']
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'by target with invalid taginclude', $target);
      $this->assertTrue(is_int($nid));
    }

    /**
     * @expectedException TuisongbaoException
     * @expectedExceptionCode 27
     * @expectedExceptionMessage 消息内容和extra总长度应大于0且小于规定长度
     */
    public function testSendNotificationToAllWithEmptyMessage()
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
      $tooLongMessage = 'to all with too long message';
      for ($i = 0; $i < 300; $i++) {
        $tooLongMessage .= 'm';
      }

      $nid = $this->client->sendNotification(self::APP_KEY, $tooLongMessage, array());
      $this->assertTrue(is_int($nid));
    }

    /**
     * @expectedException TuisongbaoException
     * @expectedExceptionCode 27
     * @expectedExceptionMessage 信息内容和extra内容总长度应大于0且小于规定长度
     */
    public function testSendNotificationToAllWithTooLongExtra()
    {
      $tooLongExtraValue = '';
      for ($i = 0; $i < 300; $i++) {
        $tooLongExtraValue .= 'm';
      }

      $options = array(
        'extra' => array(
          'key' => $tooLongExtraValue
        )
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'to all with too long extra', array(), $options);
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
          'key1.period' => 'value1'
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
    public function testSendNotificationToAllByExpiredEST()
    {
      $options = array(
        'est' => mktime(2013, 1, 1, 0, 0, 0)
      );
      $nid = $this->client->sendNotification(self::APP_KEY, 'to all by expired est', array(), $options);
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
        'lastlaunchtime' => '2010 01-01 00:00:00'
      );

      $nid = $this->client->sendNotification(self::APP_KEY, 'by invalid target', $target);
      $this->assertTrue(is_int($nid));
    }
  }
?>