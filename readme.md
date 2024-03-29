# ThinkPlugsAccount for ThinkAdmin

[![Latest Stable Version](https://poser.pugx.org/zoujingli/think-plugs-account/v/stable)](https://packagist.org/packages/zoujingli/think-plugs-account)
[![Latest Unstable Version](https://poser.pugx.org/zoujingli/think-plugs-account/v/unstable)](https://packagist.org/packages/zoujingli/think-plugs-account)
[![Total Downloads](https://poser.pugx.org/zoujingli/think-plugs-account/downloads)](https://packagist.org/packages/zoujingli/think-plugs-account)
[![Monthly Downloads](https://poser.pugx.org/zoujingli/think-plugs-account/d/monthly)](https://packagist.org/packages/zoujingli/think-plugs-account)
[![Daily Downloads](https://poser.pugx.org/zoujingli/think-plugs-account/d/daily)](https://packagist.org/packages/zoujingli/think-plugs-account)
[![PHP Version](https://thinkadmin.top/static/icon/php-7.1.svg)](https://thinkadmin.top)
[![License](https://thinkadmin.top/static/icon/license-vip.svg)](https://thinkadmin.top/vip-introduce)

多端账号中心，通用基础用户数据管理解决方案，支持多客户端登录绑定。此插件为[会员尊享插件](https://thinkadmin.top/vip-introduce)，未授权不可商用。

目前数据接口已经支持 **微信服务号**、**微信小程序**、**手机短信验证** 三种登录授权，其他登录方式可以使用短信验证登录。
账号逻辑数据已支持 **微信服务号**、**微信小程序**、**安卓APP程序**、**苹果IOS程序**、**手机网页端**、**电脑网页端** 以及 **自定义方式**。

注意：通过 **微信服务号** 和 **微信小程序** 等授权方式登录后为临时用户，需要通过手机号短信验证并绑定手机号后才会成为正式用户。

**数据关联模型：**

`临时用户(usid)` `<->` `绑定手机(bind)` `<->` `正式用户(unid)`

### 话术解析

1. 账号调度器 **Account**，用于创建账号管理的实例对象，以及处理部分基础数据；
2. 账号接口类型 **Account::IOSAPP**，终端账号请求的通道标识，通常以字段 **type** 传参数；
3. 账号实例接口 **AccountInterface**，包含用户账号编号、终端账号编号的数据及对应的操作，包含接口授权等操作；
4. 用户账号编号 **unid**，对应数据表 **PlugsUser** 的 **id** 字段，用户的唯一账号，通过绑定与解绑来关联终端账号；
5. 终端账号编号 **usid**，对应数据表 **PlugsBind** 的 **id** 字段，用户的其中一种登录账号，同时只能绑定一个用户账号；

**注意：** 用户账号编号 `unid` 是由终端账号登录后，调用 `$account->bind()` 创建或绑定用户账号，进而获取用户账号编号 `unid` 值。
终端账号编号取消关联用户账号，调用 `$account->unbind()` 即可，随后终端账号又可以绑定其他用户账号。

### 开放接口

通过用户登录接口，换取 **JWT-TOKEN** 内容，之后接口需要在每次请求的头部 **header** 加上 **Api-Token** 字段并带上之后获取到的值。

**接口文档：** https://documenter.getpostman.com/view/4518676/2s93eeRpDr

**特别注意：** 调用接口时后台接口未启动 `Session` 中间键，建议使用 `Cache & usid` 或 `Cache & unid` 作为`key`值来缓存数据。

### 接口状态

* `code`:`0` 操作失败，稍候重试
* `code`:`1` 操作成功，正常操作
* `code`:`401` 无效令牌，需要重新登录
* `code`:`402` 资料不全，需要补全资料
* `code`:`403` 认证超时，需要重新登录

### 安装插件

```shell
### 安装前建议尝试更新所有组件
composer update --optimize-autoloader

### 安装稳定版本 ( 插件仅支持在 ThinkAdmin v6.1 中使用 )
composer require zoujingli/think-plugs-account --optimize-autoloader

### 安装测试版本（ 插件仅支持在 ThinkAdmin v6.1 中使用 ）
composer require zoujingli/think-plugs-account dev-master --optimize-autoloader
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
$user = $account->set(['openid'=>'OPENID', 'phone'=>'13888888888']);
var_dump($user);

// 列如更新用户手机号，通过上面的操作已绑定账号，可以直接设置
$account->set(['phone'=>'1399999999']);

// 设置额外的扩展数据，数据库没有字段，不需要做为查询条件的字段
$account->set(['extra'=>['desc'=>'用户描述', 'sex'=>'男']]);

// 获取用户资料，无账号返回空数组
$user = $account->get();
var_dump($user);

// 以上插件仅仅是注册终端账号，也就是临时账号
// 下面我们通过 bind 操作，绑定或创建用户账号（ 主账号 ）
$user = $account->bind(['phone'=>'1399999999'],['uesrname'=>"会员用户"]);
var_dump($user); // $user['user'] 是主账号信息

// 解除该终端账号关联主账号
$user = $account->unBind();
var_dump($user); // 此处不会再有 $user['user'] 信息

// 判断终端账号是否为空，也就是还没有调用 set 访问或 init 失败
$user = $account->isNull();

// 获取接口 Token 信息
$user = $account->get(true);
var_dump($user); // $user['token'] 即为 JwtToken 值，接口 header 传 api-token 字段

// 判断终端账号是否已经关联主账号
$is = $account->isBind();
var_dump($is);

// 获取主账号关联的所有终端账号
$binds = $account->allBind();

// 通过终端USID取消其关联主账号
$binds = $account->delBind($usid);

// 动态注册接口通道，由插件服务类或模块 sys.php 执行注册
Account::add('diy', '自定义通道名称', '终端账号编号验证字段');

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
* 终端账号管理：`plugin-account/device/index`
* 手机短信管理：`plugin-account/message/index`

### 插件数据

本插件涉及数据表有：

* 插件-账号-授权 `plugin_account_auth`
* 插件-账号-终端 `plugin_account_bind`
* 插件-账号-短信 `plugin_account_msms`
* 插件-账号-资料 `plugin_account_user`

### 版权说明

**ThinkPlugsAccount** 为 **ThinkAdmin** 会员插件，未授权不可商用，了解商用授权请阅读 [《会员尊享介绍》](https://thinkadmin.top/vip-introduce)。