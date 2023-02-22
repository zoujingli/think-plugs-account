<?php

// +----------------------------------------------------------------------
// | Account Plugin for ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2022~2023 Anyon <zoujingli@qq.com>
// +----------------------------------------------------------------------
// | 官方网站: https://thinkadmin.top
// +----------------------------------------------------------------------
// | 免责声明 ( https://thinkadmin.top/disclaimer )
// | 会员免费 ( https://thinkadmin.top/vip-introduce )
// +----------------------------------------------------------------------
// | gitee 代码仓库：https://gitee.com/zoujingli/think-plugs-account
// | github 代码仓库：https://github.com/zoujingli/think-plugs-account
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace plugin\account;

use think\admin\Plugin;

class Service extends Plugin
{
    protected $package = 'zoujingli/think-plugs-account';

    public static function menu(): array
    {
        $name = app(static::class)->appName;
        return [
            [
                'name' => '用户管理',
                'subs' => [
                    ['name' => '用户账号管理', 'icon' => 'layui-icon layui-icon-user', 'node' => "{$name}/admin/index"],
                    ['name' => '用户终端管理', 'icon' => 'layui-icon layui-icon-cellphone', 'node' => "{$name}/device/index"],
                    ['name' => '用户余额管理', 'icon' => 'layui-icon layui-icon-rmb', 'node' => "{$name}/balance/index"],
                ],
            ],
        ];
    }
}