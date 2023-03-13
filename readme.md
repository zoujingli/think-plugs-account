# ThinkPlugsAccount for ThinkAdmin

[![Latest Stable Version](https://poser.pugx.org/zoujingli/think-plugs-account/v/stable)](https://packagist.org/packages/zoujingli/think-plugs-account)
[![Latest Unstable Version](https://poser.pugx.org/zoujingli/think-plugs-account/v/unstable)](https://packagist.org/packages/zoujingli/think-plugs-account)
[![Total Downloads](https://poser.pugx.org/zoujingli/think-plugs-account/downloads)](https://packagist.org/packages/zoujingli/think-plugs-account)
[![Monthly Downloads](https://poser.pugx.org/zoujingli/think-plugs-account/d/monthly)](https://packagist.org/packages/zoujingli/think-plugs-account)
[![Daily Downloads](https://poser.pugx.org/zoujingli/think-plugs-account/d/daily)](https://packagist.org/packages/zoujingli/think-plugs-account)
[![PHP Version Require](http://poser.pugx.org/zoujingli/think-plugs-account/require/php)](https://packagist.org/packages/zoujingli/think-plugs-account)
[![ThinkAdmin VIP 授权](https://img.shields.io/badge/license-VIP%20授权-blueviolet.svg)](https://thinkadmin.top/vip-introduce)

**插件正在开发测试中，不建议使用 ！！！**

用户账号管理插件，此插件为[会员尊享插件](https://thinkadmin.top/vip-introduce)，未授权不可商用。

此插件目前数据接口已支持 **微信服务号** 和 **微信小程序** 两种登录授权，其他登录方式需要等短信验证插件上线再开放。
账号服务层数据已支持 **微信服务号**、**微信小程序**、**安卓APP程序**、**苹果IOS程序**、**手机网页端**、**电脑网页端** 以及 **自定义方式**。

目前账号体系尚处在测试阶段，在未清楚其工作原理时建议不要直接使用！

### 话术解析

1. 账号调度器 **Account**，用于创建账号管理的实例对象，以及处理部分基础数据；
2. 账号接口类型 **Account::IOSAPP**，终端用户请求的通道标识，通常以字段 **type** 传参数；
3. 账号实例接口 **AccountInterface**，包含用户账号编号、终端用户编号的数据及对应的操作，包含接口授权等操作；
4. 用户账号编号 **unid**，对应数据表 **PlugsUser** 的 **id** 字段，用户的唯一账号，通过绑定与解绑来关联终端用户；
5. 终端用户编号 **usid**，对应数据表 **PlugsBind** 的 **id** 字段，用户的其中一种登录账号，同时只能绑定一个用户账号；

**注意：** 用户账号编号 `unid` 是由终端用户登录后，调用 `$account->bind()` 创建或绑定用户账号，进而获取用户账号编号 `unid` 值。
终端用户编号取消关联用户账号，调用 `$account->unbind()` 即可，随后终端用户又可以绑定其他用户账号。

### 开放接口

通过用户登录接口，换取 **JWT-TOKEN** 内容，之后接口需要在每次请求的头部 **header** 加上 **Api-Token** 字段并带上之后获取到的值。

### 安装插件

```shell
### 注意，仅支持在 ThinkAdmin v6.1 中使用
composer require zoujingli/think-plugs-account dev-master
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
$account = Account::mk(Account::WXAPP, TOKEN='');
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
Account::add('diy', '自定义通道名称', '终端用户编号验证字段');

// 通道状态 - 禁用接口，将禁止该方式访问数据
Account::set('diy', 0);

// 通道状态 - 启用接口，将启用该方式访问数据
Account::set('diy', 1);

// 保存通道状态，下次访问也同样生效
Account::save();

// 获取接口认证字段以及检查接口是否有效
$field = Account::field('diy');
if($field)// 接口有效
else //接口无效

// 获取全部接口
$types = Account::types();
var_dump($types);
```

### 功能节点

可根据下面的功能节点配置菜单及访问权限，按钮操作级别的节点未展示！

* 用户账号管理：`plugin-account/master/index`
* 终端用户管理：`plugin-account/device/index`

### 插件数据

本插件涉及数据表有：

* 插件-账号-授权 `plugin_account_auth`
* 插件-账号-终端 `plugin_account_bind`
* 插件-账号-资料 `plugin_account_user`

### 版权说明

**ThinkPlugsAccount** 为 **ThinkAdmin** 会员插件，未授权不可商用，了解商用授权请阅读 [《会员尊享介绍》](https://thinkadmin.top/vip-introduce)。