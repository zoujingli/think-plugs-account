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
 * 用户账号调度器
 * Class Account
 * @package plugin\account\service
 */
abstract class Account
{
    const _WAP = 'wap';
    const _WEB = 'web';
    const _WXAPP = 'wxapp';
    const _WECHAT = 'wechat';
    const _IOSAPP = 'iosapp';
    const _ANDROID = 'android';

    private static $initd = false;

    private static $types = [
        // 接口支付配置（不需要的直接注释）
        self::_WAP     => ['name' => '手机浏览器', 'field' => 'phone', 'status' => 1],
        self::_WEB     => ['name' => '电脑浏览器', 'field' => 'phone', 'status' => 1],
        self::_WXAPP   => ['name' => '微信小程序', 'field' => 'openid', 'status' => 1],
        self::_WECHAT  => ['name' => '微信服务号', 'field' => 'openid', 'status' => 1],
        self::_IOSAPP  => ['name' => '苹果APP应用', 'field' => 'phone', 'status' => 1],
        self::_ANDROID => ['name' => '安卓APP应用', 'field' => 'phone', 'status' => 1],
    ];

    /**
     * 创建账号实例
     * @param string $code 通道编号
     * @param string $token 认证令牌
     * @return mixed|AccountInterface
     * @throws \think\admin\Exception
     */
    public static function mk(string $code, string $token = ''): AccountInterface
    {
        if (self::getField($code)) {
            $vars = ['type' => $code, 'field' => self::$types[$code]['field']];
            return app(AccountAccess::class, $vars)->init($token);
        } else {
            throw new Exception("用户通道 [{$code}] 未定义或参数错误");
        }
    }

    /**
     * 动态增加通道
     * @param string $type
     * @param string $name
     * @param string $field
     * @return array[]
     */
    public static function addType(string $type, string $name, string $field = 'phone'): array
    {
        self::$types[$type] = ['name' => $name, 'field' => $field, 'status' => 1];
        return self::getTypes();
    }

    /**
     * 获取全部通道
     * @return array[]
     */
    public static function getTypes(): array
    {
        try {
            if (self::$initd) return self::$types;
            $denys = sysdata('plugin.account.denys');
            foreach (self::$types as $k => &$v) {
                $v['status'] = intval(!in_array($k, $denys));
            }
            self::$initd = true;
            return self::$types;
        } catch (\Exception $exception) {
            return [];
        }
    }

    /**
     * 设置通道状态
     * @param string $code 通道编号
     * @param integer $status 通道状态
     * @return bool
     */
    public static function setStatus(string $code, int $status): bool
    {
        if (isset(self::$types[$code])) {
            self::$types[$code]['status'] = intval(!!$status);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取通道认证字段
     * @param string $code
     * @return string
     */
    public static function getField(string $code): string
    {
        self::$initd || self::getTypes();
        if (!empty(self::$types[$code]['status'])) {
            return self::$types[$code]['field'] ?? '';
        } else {
            return '';
        }
    }

    /**
     * 保存用户通道状态
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function saveStatus()
    {
        $denys = [];
        foreach (self::getTypes() as $k => $v) {
            if (empty($v['status'])) $denys[] = $k;
        }
        return sysdata('plugin.account.denys', $denys);
    }
}