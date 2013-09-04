# 发布到pearfarm.org

更新`pearfarm.spec`，然后，

```bash
# 安装pear
apt-get install php-pear

# 安装pearfarm
pear channel-discover pearfarm.pearfarm.org
pear install pearfarm.pearfarm.org/pearfarm

# 构建pear包
pear channel-discover tuisongbao.pearfarm.org
pearfarm build

# 生成RSA公钥后，在http://pearfarm.org/user/edit页面`Add a new Key`
pearfarm keygen

# 发布到pearfarm.org
pearfarm push
```

参考：http://pearfarm.org/help/usage

# 发布到packagist.org

将仓库地址`git@github.com:tuisongbao/php5-sdk.git`添加到https://packagist.org/packages/submit。

更新`composer.json`，push到GitHub，在https://github.com/tuisongbao/php5-sdk/releases/new 创建一个新的release。

packagist.org会自动检查是否有新的release进行构建，每天一次，如果启用了GitHub hook则可以立即更新。或者可以访问https://packagist.org/packages/tuisongbao/tuisongbao ，会触发检查。

参考：https://packagist.org/about