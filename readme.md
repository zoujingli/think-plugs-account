# ThinkPlugsAccount for ThinkAdmin [`VIP`](https://thinkadmin.top/vip-introduce)

[![Latest Stable Version](https://poser.pugx.org/zoujingli/think-plugs-account/v/stable)](https://packagist.org/packages/zoujingli/think-plugs-account)
[![Latest Unstable Version](https://poser.pugx.org/zoujingli/think-plugs-account/v/unstable)](https://packagist.org/packages/zoujingli/think-plugs-account)
[![Total Downloads](https://poser.pugx.org/zoujingli/think-plugs-account/downloads)](https://packagist.org/packages/zoujingli/think-plugs-account)
[![Monthly Downloads](https://poser.pugx.org/zoujingli/think-plugs-account/d/monthly)](https://packagist.org/packages/zoujingli/think-plugs-account)
[![Daily Downloads](https://poser.pugx.org/zoujingli/think-plugs-account/d/daily)](https://packagist.org/packages/zoujingli/think-plugs-account)

用户账号中心，此插件为[会员尊享插件](https://thinkadmin.top/vip-introduce)，未授权不可商用。

通用基础账号管理插件，目前数据接口已支持 **微信服务号** 和 **微信小程序** 两种登录授权方式，其他账号登录方式稍后等短信验证插件上线再开放。
账号数据通道已支持 **微信服务号**、**微信小程序**、**安卓App程序**、**苹果IOS程序**、**手机网页端**、**电脑网页端** 以及 **自定义通道**。

### 开放接口

请阅读《用户账号中心》文档

### 安装插件

```shell
### 注意，仅支持在 ThinkAdmin v6.1 中使用
composer require zoujingli/think-plugs-account
```

### 卸载插件

```shell
### 注意，插件卸载不会删除数据表，需要手动删除
composer remove zoujingli/think-plugs-account
```

### 调用案例

```php
// 账号管理调度器
use plugin\account\service\Account;

// @ 注册一个新用户（ 微信小程序标识字段为 openid 字段 ）
//   不传 TOKEN 的情况下并存在 openid 时会主动通过 openid 查询用户信息
//   如果传 TOKEN 的情况下且 opneid 与原 openid 不匹配会报错，用 try 捕获异常
//   注意，每次调用 Account::mk() 都会创建新的调度器，设置 set 和 get 方法的 rejwt 参数可返回接口令牌 
$account = Account::mk(Account::_WXAPP, TOKEN='');
$user = $account->set(['openid'=>"OPENID", 'phone'=>'13888888888']);
var_dump($user);

// 列如更新用户手机号，通过上面的操作已绑定账号，可以直接设置
$account->set(['phone'=>'1399999999']);

// 设置额外的扩展数据，数据库没有字段，不需要做为查询条件的字段
$account->set(['extra'=>['desc'=>'用户描述','sex'=>'男']]);

// 获取用户资料，无账号返回空数组
$user = $account->get();
var_dump($user);

// 动态注册接口通道，由插件服务类或模块 sys.php 执行注册
Account::addType('diy', '自定义通道名称', '子账号验证字段');

// 通道状态 - 禁用接口，将禁止该方式访问数据
Account::setStatus('diy', 0);

// 通道状态 - 启用接口，将启用该方式访问数据
Account::setStatus('diy', 1);

// 保存通道状态，下次访问也同样生效
Account::saveStatus();

// 获取接口认证字段以及检查接口是否有效
$field = Account::getField('diy');
if($field)// 接口有效
else //接口无效

// 获取全部接口
$types = Account::getTypes();
var_dump($types);
```

### 功能节点

可根据下面的功能节点配置菜单及访问权限，按钮操作级别的节点未展示！

* 主账号管理：`plugin-account/master/index`
* 子账号管理：`plugin-account/device/index`
* 用户余额管理：`plugin-account/balance/index`

### 插件数据

本插件涉及数据表有：

* 插件-账号-授权 `plugin_account_auth`
* 插件-账号-设备 `plugin_account_bind`
* 插件-账号-资料 `plugin_account_user`
* 插件-账号-地址 `plugin_account_user_address`
* 插件-账号-余额 `plugin_account_user_balance`

### 版权说明

**ThinkPlugsAccount** 为 **ThinkAdmin** 会员插件，未授权不可商用，了解商用授权请阅读 [《会员尊享介绍》](https://thinkadmin.top/vip-introduce)。