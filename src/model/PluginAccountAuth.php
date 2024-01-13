<?php

// +----------------------------------------------------------------------
// | Account Plugin for ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2022~2024 ThinkAdmin [ thinkadmin.top ]
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

namespace plugin\account\model;

use think\model\relation\HasOne;

/**
 * 子账号授权模型
 * @class PluginAccountAuth
 * @package plugin\account\model
 */
class PluginAccountAuth extends Abs
{
    /**
     * 关联子账号
     * @return \think\model\relation\HasOne
     */
    public function client(): HasOne
    {
        return $this->hasOne(PluginAccountBind::class, 'id', 'usid')->with(['user']);
    }
}