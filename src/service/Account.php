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

namespace plugin\account\service;

use plugin\account\service\contract\AccountAccess;
use plugin\account\service\contract\AccountInterface;
use think\admin\Exception;

/**
 * 前端用户处理
 * Class Account
 * @package plugin\account\service
 */
abstract class Account
{
    const CHANNEL_WAP = 'wap';
    const CHANNEL_WEB = 'web';
    const CHANNEL_WXAPP = 'wxapp';
    const CHANNEL_WECHAT = 'wechat';
    const CHANNEL_IOSAPP = 'iosapp';
    const CHANNEL_ANDROID = 'android';

    const types = [
        // 接口支付配置（不需要的直接注释）
        self::CHANNEL_WAP     => [
            'name'  => '手机浏览器',
            'field' => 'phone',
        ],
        self::CHANNEL_WEB     => [
            'name'  => '电脑浏览器',
            'field' => 'phone',
        ],
        self::CHANNEL_WXAPP   => [
            'name'  => '微信小程序',
            'field' => 'openid',
        ],
        self::CHANNEL_WECHAT  => [
            'name'  => '微信服务号',
            'field' => 'openid',
        ],
        self::CHANNEL_IOSAPP  => [
            'name'  => '苹果APP应用',
            'field' => 'phone',
        ],
        self::CHANNEL_ANDROID => [
            'name'  => '安卓APP应用',
            'field' => 'phone',
        ],
    ];

    /**
     * 创建用户账号实例
     * @param string $type 通道类型
     * @param string $token 认证令牌
     * @return mixed|AccountInterface
     * @throws \think\admin\Exception
     */
    public static function mk(string $type, string $token = ''): AccountInterface
    {
        if (isset(static::types[$type]) && isset(static::types[$type]['field'])) {
            $vars = ['type' => $type, 'field' => static::types[$type]['field']];
            return app(AccountAccess::class, $vars)->init($token);
        } else {
            throw new Exception("用户通道 [{$type}] 未定义或参数错误");
        }
    }
}