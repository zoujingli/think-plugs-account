<?php

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