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

namespace plugin\account\service\contract;

use plugin\account\model\PluginAccountAuth;
use plugin\account\model\PluginAccountDevice;
use think\admin\Exception;
use think\App;

/**
 * 用户账号通用类
 * @class AccountAccess
 * @package plugin\account\service\contract
 */
class AccountAccess implements AccountInterface
{
    /**
     * 当前应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 当前通道类型
     * @var string
     */
    protected $type;

    /**
     * 授权检查字段
     * @var string
     */
    protected $field;

    /**
     * 令牌有效时间
     * @var integer
     */
    protected $expire = 3600;

    /**
     * 当前认证令牌
     * @var PluginAccountAuth
     */
    protected $auther;

    /**
     * 当前用户终端
     * @var PluginAccountDevice
     */
    protected $device;

    /**
     * 测试专用TOKEN
     * @var string
     */
    protected $testToken = 'tester';

    /**
     * 账号通道构造方法
     * @param \think\App $app
     * @param string $type 通道类型
     * @param string $field 授权字段
     */
    public function __construct(App $app, string $type, string $field)
    {
        $this->app = $app;
        $this->type = $type;
        $this->field = $field;
    }

    /**
     * 初始化账号通道
     * @param string $token
     * @return \plugin\account\service\contract\AccountInterface
     */
    public function init(string $token = ''): AccountInterface
    {
        if (!empty($token)) {
            $map = ['type' => $this->type, 'token' => $token];
            $this->auther = PluginAccountAuth::mk()->with('device')->where($map)->findOrEmpty();
            $this->device = $this->auther->device ?? PluginAccountDevice::mk();
        } else {
            $this->auther = PluginAccountAuth::mk();
            $this->device = PluginAccountDevice::mk();
        }
        return $this;
    }

    /**
     * 检查令牌是否有效
     * @param string $token
     * @return array
     * @throws \think\admin\Exception
     */
    public function check(string $token): array
    {
        if ($this->device->isEmpty()) {
            throw new Exception('登录认证无效，请重新登录！', 401);
        }
        if ($token !== $this->testToken && $this->auther['token'] !== $token) {
            throw new Exception('登录认证无效，请重新登录！', 401);
        }
        if ($this->expire > 0 && $this->auther['expire'] < time()) {
            throw new Exception('登录认证超时，请重新登录！', 502);
        }
        return static::expire()->get();
    }

    /**
     * 更新用户用户参数
     * @param array $data 更新数据
     * @return array
     * @throws \think\admin\Exception
     * @throws \think\db\exception\DbException
     */
    public function set(array $data = []): array
    {
        $data['type'] = $this->type;
        // 如果传入授权验证字段
        if (isset($data[$this->field]) && $data[$this->field]) {
            if ($this->device->isExists()) {
                if ($data[$this->field] !== $this->device[$this->field]) {
                    throw new Exception('禁用强行替换用户');
                }
            } else {
                $map = [$this->field => $data[$this->field]];
                $this->device = PluginAccountDevice::mk()->where($map)->findOrEmpty();
            }
        } elseif ($this->device->isEmpty()) {
            throw new Exception("必要字段 {$this->field} 不能为空！");
        }
        $this->device = $this->save($data);
        if ($this->device->isEmpty()) {
            throw new Exception("更新用户资料失败！");
        } else {
            return $this->token(intval($this->device['id']))->get();
        }
    }

    /**
     * 获取用户数据
     * @return array
     */
    public function get(): array
    {
        $data = $this->device->toArray();
        $data['auther'] = [
            'type'  => $this->auther->getAttr('type'),
            'token' => $this->auther->getAttr('token'),
        ];
        return $data;
    }

    /**
     * 生成新的用户令牌
     * @param integer $unid
     * @return \plugin\account\service\contract\AccountInterface
     * @throws \think\db\exception\DbException
     */
    public function token(int $unid): AccountInterface
    {
        // 清理无效令牌
        PluginAccountAuth::mk()
            ->where('token', '<>', $this->testToken)
            ->whereBetween('expire', [1, time()])->delete();

        if ($this->auther->isEmpty()) {
            $this->auther = PluginAccountAuth::mk()->where(['unid' => $unid])->findOrEmpty();
        }

        // 生成新令牌数据
        if ($this->auther->isEmpty()) {
            do $data = ['type' => $this->type, 'token' => md5(uniqid(strval(rand(0, 999))))];
            while (PluginAccountAuth::mk()->where($data)->findOrEmpty()->isExists());
            $this->auther->save($data + ['unid' => $unid]);
        } else {
            $this->expire();
        }
        return $this;
    }

    /**
     * 延期令牌有效时间
     * @return \plugin\account\service\contract\AccountInterface
     */
    public function expire(): AccountInterface
    {
        $expire = $this->expire > 0 ? $this->expire + time() : 0;
        $this->auther->isExists() && $this->auther->save([
            'type' => $this->type, 'expire' => $expire
        ]);
        return $this;
    }

    /**
     * 保存更新用户资料
     * @param array $data
     * @return \plugin\account\model\PluginAccountDevice
     * @throws \think\admin\Exception
     */
    protected function save(array $data): PluginAccountDevice
    {
        if (empty($data)) {
            throw new Exception('更新数据不能为空！');
        }
        if ($this->device->isEmpty()) {
            $this->device->setAttr('extra', $data);
        } else {
            $extra = $this->device->getAttr('extra');
            $this->device->setAttr('extra', $extra + $data);
        }
        $this->device->save($data);
        return $this->device;
    }
}