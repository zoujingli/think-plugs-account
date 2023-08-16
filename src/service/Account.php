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

namespace plugin\account\service;

use plugin\account\model\PluginAccountAuth;
use plugin\account\service\contract\AccountAccess;
use plugin\account\service\contract\AccountInterface;
use think\admin\Exception;
use think\admin\extend\JwtExtend;

/**
 * 用户账号调度器
 * @class Account
 * @package plugin\account\service
 */
abstract class Account
{
    const WAP = 'wap';
    const WEB = 'web';
    const WXAPP = 'wxapp';
    const WECHAT = 'wechat';
    const IOSAPP = 'iosapp';
    const ANDROID = 'android';

    // 已禁用的账号通道
    private static $denys = null;
    private static $cacheKey = 'plugin.account.denys';

    private static $types = [
        self::WAP     => ['name' => '手机浏览器', 'field' => 'phone', 'status' => 1],
        self::WEB     => ['name' => '电脑浏览器', 'field' => 'phone', 'status' => 1],
        self::WXAPP   => ['name' => '微信小程序', 'field' => 'openid', 'status' => 1],
        self::WECHAT  => ['name' => '微信服务号', 'field' => 'openid', 'status' => 1],
        self::IOSAPP  => ['name' => '苹果APP应用', 'field' => 'phone', 'status' => 1],
        self::ANDROID => ['name' => '安卓APP应用', 'field' => 'phone', 'status' => 1],
    ];

    /**
     * 创建账号实例
     * @param string $type 通道编号
     * @param string $token 认证令牌
     * @param boolean $isjwt 是否JWT模式
     * @return AccountInterface
     * @throws \think\admin\Exception
     */
    public static function mk(string $type, string $token = '', bool $isjwt = true): AccountInterface
    {
        if ($token === AccountAccess::tester) {
            if (empty($type)) {
                $type = PluginAccountAuth::mk()->where(['token' => $token])->value('type');
                if (empty($type)) throw new Exception('账号不存在！');
            }
        } elseif ($isjwt && strlen($token) > 32) {
            $data = JwtExtend::verify($token);
            $type = $type ?: ($data['type'] ?? '');
            $token = $data['token'] ?? $token;
            if (isset($data['type']) && $data['type'] !== $type) {
                throw new Exception('授权不匹配！');
            }
        }
        if (self::field($type)) {
            $vars = ['type' => $type, 'field' => self::$types[$type]['field']];
            return app(AccountAccess::class, $vars)->init($token, $isjwt);
        } else {
            throw new Exception('接口访问异常！');
        }
    }

    /**
     * 初始化数据状态
     * @return array[]
     */
    private static function init(): array
    {
        if (is_null(self::$denys)) try {
            self::$denys = sysdata(self::$cacheKey);
            foreach (self::$types as $type => &$item) {
                $item['status'] = intval(!in_array($type, self::$denys));
            }
        } catch (\Exception $exception) {
        }
        return self::$types;
    }

    /**
     * 动态增加通道
     * @param string $type
     * @param string $name
     * @param string $field
     * @return array[]
     */
    public static function add(string $type, string $name, string $field = 'phone'): array
    {
        self::$types[$type] = ['name' => $name, 'field' => $field, 'status' => 1];
        return self::types();
    }

    /**
     * 设置通道状态
     * @param string $type 通道编号
     * @param integer $status 通道状态
     * @return bool
     */
    public static function set(string $type, int $status): bool
    {
        if (isset(self::$types[$type])) {
            self::$types[$type]['status'] = $status;
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取全部通道
     * @param ?int $status 指定状态
     * @return array
     */
    public static function types(?int $status = null): array
    {
        try {
            $all = [];
            foreach (self::init() as $type => $item) {
                if (is_null($status) || $item['status'] === $status) $all[$type] = $item;
            }
            return $all;
        } catch (\Exception $exception) {
            return [];
        }
    }

    /**
     * 保存用户通道状态
     * @return mixed
     * @throws \think\admin\Exception
     */
    public static function save()
    {
        self::$denys = [];
        foreach (self::types() as $k => $v) {
            if (empty($v['status'])) self::$denys[] = $k;
        }
        return sysdata(self::$cacheKey, self::$denys);
    }

    /**
     * 获取认证字段
     * @param string $type 通道编码
     * @return string
     */
    public static function field(string $type): string
    {
        $types = self::init();
        if (!empty($types[$type]['status'])) {
            return $types[$type]['field'] ?? '';
        } else {
            return '';
        }
    }

    /**
     * 解析请求令牌
     * @param string $token
     * @param ?string $type
     * @return AccountInterface
     * @throws \think\admin\Exception
     */
    public static function token(string $token = '', ?string &$type = null): AccountInterface
    {
        if ($token === AccountAccess::tester) {
            $map = ['token' => $token];
            empty($type) or ($map['type'] = $type);
            $auth = PluginAccountAuth::mk()->where($map)->findOrEmpty();
            if ($auth->isEmpty()) throw new Exception('账号不存在！');
            return static::mk($type = $auth->getAttr('type'), $auth->getAttr('token'));
        } else {
            $data = JwtExtend::verify($token);
            return static::mk($type = $data['type'] ?? '-', $data['token'] ?? '-');
        }
    }
}