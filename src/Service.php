<?php

// +----------------------------------------------------------------------
// | Account Plugin for ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2022~2023 ThinkAdmin [ thinkadmin.top ]
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

use plugin\account\model\PluginAccountUserAddress;
use think\admin\Plugin;

class Service extends Plugin
{
    protected $package = 'zoujingli/think-plugs-account';

    public function register(): void
    {
        // 主账号绑定处理
        $this->app->event->listen('ThinkPlugsAccountBind', function (array $data) {
            $map = [['unid', '<>', $data['unid']], ['usid', '=', $data['usid']]];
            PluginAccountUserAddress::mk()->where($map)->update(['unid' => $data['unid']]);
        });

        // 主账号解绑处理
        $this->app->event->listen('ThinkPlugsAccountUnbind', function (array $data) {
            $map = [['unid', '>', 0], ['usid', '=', $data['usid']]];
            PluginAccountUserAddress::mk()->where($map)->update(['usid' => 0]);
        });
    }

    public static function menu(): array
    {
        $name = app(static::class)->appName;
        return [
            [
                'name' => '用户管理',
                'subs' => [
                    ['name' => '用户账号管理', 'icon' => 'layui-icon layui-icon-user', 'node' => "{$name}/master/index"],
                    ['name' => '用户终端管理', 'icon' => 'layui-icon layui-icon-cellphone', 'node' => "{$name}/device/index"],
                ],
            ],
        ];
    }
}