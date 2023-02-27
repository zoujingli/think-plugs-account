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

namespace plugin\account\model;

use think\admin\Model;
use think\model\relation\HasMany;

/**
 * 用户主账号模型
 * @class PluginAccountUser
 * @package plugin\account\model
 */
class PluginAccountUser extends Model
{

    /**
     * 关联子账号
     * @return \think\model\relation\HasMany
     */
    public function binds(): HasMany
    {
        return $this->hasMany(PluginAccountBind::class, 'unid', 'id');
    }

    /**
     * 字段属性处理
     * @param mixed $value
     * @return string
     */
    public function setExtraAttr($value): string
    {
        return is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 字段属性处理
     * @param mixed $value
     * @return array
     */
    public function getExtraAttr($value): array
    {
        return empty($value) ? [] : (is_string($value) ? json_decode($value, true) : $value);
    }

    /**
     * 格式化输出时间
     * @param mixed $value
     * @return string
     */
    public function getCreateTimeAttr($value): string
    {
        return format_datetime($value);
    }

    /**
     * 格式化输出时间
     * @param mixed $value
     * @return string
     */
    public function getUpdateTimeAttr($value): string
    {
        return format_datetime($value);
    }
}