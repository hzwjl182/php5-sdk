# 推送宝 PHP5 SDK

[![Build Status](https://travis-ci.org/tuisongbao/php5-sdk.png?branch=master)](https://travis-ci.org/tuisongbao/php5-sdk)

### 安装
PHP版本要求：>=5.2

    pear channel-discover tuisongbao.pearfarm.org
    pear install tuisongbao/tuisongbao

### Example 1:  初始化客户端

```php
require "tuisongbao/lib/client.php"

client = new Client('tuisongbao-api-key', 'tuisongbao-api-secret');
```

### Example 2: 推送消息给所有用户

```php
try {
    $nid = client.sendNotificationToAll('app-key', 'hello world');
    echo sprintf('notification id: %s' , $nid);
} catch (TuisongbaoException $e) {
    echo sprintf('error message: %s, ack code: %s', $e.message, $e.code);
}
```

### Example 3: 推送消息给订阅了特定频道的用户

```php
try {
    $nid = client.sendNotificationByChannels('app-key', array('channel1'), 'hello world');
    echo sprintf('notification id: %s' , $nid);
} catch (TuisongbaoException $e) {
    echo sprintf('error message: %s, ack code: %s', $e.message, $e.code);
}
```

### Example 4: 推送消息给特定版本的用户

```php
try {
    $nid = client.sendNotificationByAppVersion('app-key', array('version1'), 'hello world');
    echo sprintf('notification id: %s' , $nid);
} catch (TuisongbaoException $e) {
    echo sprintf('error message: %s, ack code: %s', $e.message, $e.code);
}
```

### Example 5: 推送消息给特定版本中订阅了指定频道的用户

```php
try {
    $nid = client.sendNotificationByChannelsAndAppVersion('app-key', array('channel1'), array('version1'), 'hello world');
    echo sprintf('notification id: %s' , $nid);
} catch (TuisongbaoException $e) {
    echo sprintf('error message: %s, ack code: %s', $e.message, $e.code);
}
```

### Example 6: 推送消息给特定用户

```php
try {
    $nid = client.sendNotificationByTokens('app-key', array('token1'), 'hello world');
    echo sprintf('notification id: %d' , $nid);
} catch (TuisongbaoException $e) {
    echo sprintf('error message: %s, ack code: %s', $e.message, $e.code);
}
```

### 说明：

* 所有 *sendNotification* 方法均有一个可选参数 *options(array)* ，可用于设置 *extra* 、 *est* 等可选项
* 当推送给苹果设备时，如果 *extra* 中含有 *aps* 字段， *message* 会被忽略
* *est* 必须为 **timestamp**  类型（不需要考虑时区），例如：`mktime(2013, 7, 7, 0, 0, 0)`

### Example 7: 推送消息给全部苹果用户， 且定时在2013年7月7日 00:00:00发送

```php
from datetime import datetime

options = array();
options['extra'] = array(
    "aps" => array(
        "alert" => array(
            "body" => "hello world",
            "action-loc-key" => "",
            "loc-key" => "",
            "loc-args" => array(),
            "launch-image" => ""
        ),
        "badge" => 1,
        "sound" => "alert.aov"
    ),
    "key1" => "value1",
    "key2" => "value2"
);
options['est'] = mktime(2013, 7, 7, 0, 0, 0);

try {
    $nid = client.sendNotificationToAll('app-key', 'this wil be ignored', options);
    echo sprintf('notification id: %s' , $nid);
} catch (TuisongbaoException $e) {
    echo sprintf('error message: %s, ack code: %s', $e.message, $e.code);
}
```

### Example 8: 查询推送状态

```php
// 推送消息
// ......
try {
    status = client.queryNotificationStatus('app-key', $nid);
    echo sprintf('success count: %d failed count: %d', status['success'], status['failed']);
} catch (TuisongbaoException $e) {
    echo sprintf('error message: %s, ack code: %s', $e.message, $e.code);
}
```