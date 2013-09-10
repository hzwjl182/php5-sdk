# 推送宝 PHP5 SDK

[![Build Status](https://travis-ci.org/tuisongbao/php5-sdk.png?branch=master)](https://travis-ci.org/tuisongbao/php5-sdk)

**对应推送宝 RESTful API 协议1.1**，请参考：http://docs.tuisongbao.com/developer/restful.html

## 安装

PHP版本要求：>=5.2

- pear

    ```bash
    pear channel-discover tuisongbao.pearfarm.org
    pear install tuisongbao/tuisongbao
    ```

- composer

    ```bash
    composer install tuisongbao/tuisongbao
    ```

## 使用

该SDK仅是对推送宝 RESTful API的一层简单封装。

### 推送消息

```php
// 引入SDK文件
require "tuisongbao/lib/client.php";

// 初始化客户端
$client = new Client('your-api-key', 'your-api-secret');

// 准备extra（可选），参考：http://docs.tuisongbao.com/developer/rest_apis.html#extra
$extra = array(
    'key' => 'value'
);

// 准备target，参考：http://docs.tuisongbao.com/developer/rest_apis.html#target

// 发送给指定token
$target = array(
    'tokens' => array('token1', 'token2')
);

// 或者，按其它条件筛选
$target = array(
    'appversion' => array('1.0.0'),
    'locationcode' => array('310115'),
    'lastlaunchtime' => '2013-09-01 00:00:00',
    'taginclude' => array('tag1', 'tag2'),
    'tagexclude' => array('tag3', 'tag4')
)

// 或者，发送给全部
$target = array();

// 推送
$appKey = 'your-app-key';
$message = '天气通新版本发布了，欢迎下载';
$options = array(
    'extra' => $extra,
    'est' => mktime(0, 0, 0, 10, 1, 2013)   // 定时发送
);

try {
    $nid = $client.sendNotification($appKey, $message, $target, $options);
    echo sprintf('notification id: %s' , $nid);
} catch (TuisongbaoException $e) {
    echo sprintf('error message: %s, ack code: %s', $e.message, $e.code);
}
```

**说明：**

* 参数 `options(array)` 是可选的，可用于设置 `extra` 、 `est` 等可选项。
* 当推送给iOS设备时，如果 `extra` 中含有 `aps` 字段， `message` 会被忽略。
* `est` 必须为 **timestamp**  类型（不需要考虑时区），例如：`mktime(0, 0, 0, 10, 1, 2013)`

### 查询推送状态

```php
try {
    $status = $client.queryNotificationStatus('your-app-key', $nid);
    echo sprintf('success count: %d failed count: %d', status['success'], status['failed']);
} catch (TuisongbaoException $e) {
    echo sprintf('error message: %s, ack code: %s', $e.message, $e.code);
}
```

## 示例: 推送消息给全部iOS用户， 且定时在2013年7月7日 00:00:00发送

```php
$target = array();

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
options['est'] = mktime(0, 0, 0, 10, 1, 2013);

try {
    $nid = $client.sendNotification('app-key', 'this wil be ignored', $target, options);
    echo sprintf('notification id: %s' , $nid);
} catch (TuisongbaoException $e) {
    echo sprintf('error message: %s, ack code: %s', $e.message, $e.code);
}
```