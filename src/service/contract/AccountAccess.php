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

namespace plugin\account\service\contract;

use plugin\account\model\PluginAccountAuth;
use plugin\account\model\PluginAccountBind;
use plugin\account\model\PluginAccountUser;
use think\admin\Exception;
use think\admin\extend\CodeExtend;
use think\admin\extend\JwtExtend;
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
     * 当前认证令牌
     * @var PluginAccountAuth
     */
    protected $auth;

    /**
     * 当前用户终端
     * @var PluginAccountBind
     */
    protected $bind;

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
     * 是否JWT模式
     * @var boolean
     */
    protected $isjwt;

    /**
     * 令牌有效时间
     * @var integer
     */
    protected $expire = 3600;

    /**
     * 测试专用TOKEN
     * @var string
     */
    protected $tester = 'tester';

    /**
     * 通道构造方法
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
     * 初始化通道
     * @param string $token
     * @param boolean $isjwt
     * @return AccountInterface
     */
    public function init(string $token = '', bool $isjwt = true): AccountInterface
    {
        if (!empty($token)) {
            $map = ['type' => $this->type, 'token' => $token];
            $this->auth = PluginAccountAuth::mk()->where($map)->findOrEmpty();
            $this->bind = $this->auth->device()->findOrEmpty();
        } else {
            $this->auth = PluginAccountAuth::mk();
            $this->bind = PluginAccountBind::mk();
        }
        $this->isjwt = $isjwt;
        return $this;
    }

    /**
     * 设置子账号资料
     * @param array $data 用户资料
     * @param boolean $rejwt 返回令牌
     * @return array
     * @throws \think\admin\Exception
     */
    public function set(array $data = [], bool $rejwt = false): array
    {
        $data['type'] = $this->type;
        // 如果传入授权验证字段
        if (isset($data[$this->field])) {
            if ($this->bind->isExists()) {
                if ($data[$this->field] !== $this->bind->getAttr($this->field)) {
                    throw new Exception('不能直接更换绑定');
                }
            } else {
                $map = [$this->field => $data[$this->field]];
                $this->bind = PluginAccountBind::mk()->where($map)->findOrEmpty();
            }
        } elseif ($this->bind->isEmpty()) {
            throw new Exception("必要字段 {$this->field} 不能为空！");
        }
        $this->bind = $this->save($data);
        if ($this->bind->isEmpty()) {
            throw new Exception("更新用户资料失败！");
        } else {
            return $this->token(intval($this->bind->getAttr('id')))->get($rejwt);
        }
    }

    /**
     * 获取用户数据
     * @param boolean $rejwt 返回令牌
     * @return array
     */
    public function get(bool $rejwt = false): array
    {
        $data = $this->bind->hidden(['password'])->toArray();
        if ($this->bind->isExists()) {
            $data['user'] = $this->bind->user()->findOrEmpty()->toArray();
            if ($rejwt) $data['token'] = $this->isjwt ? JwtExtend::token([
                'type'  => $this->auth->getAttr('type'),
                'token' => $this->auth->getAttr('token')
            ], null, null, false) : $this->auth->getAttr('token');
        }
        return $data;
    }

    /**
     * 绑定主账号
     * @param array $map 主账号条件
     * @param array $data 主账号资料
     * @return array
     * @throws \think\admin\Exception
     */
    public function bind(array $map, array $data = []): array
    {
        if ($this->bind->isEmpty()) throw new Exception('终端用户不存在！');
        $user = PluginAccountUser::mk()->where(['deleted' => 0])->where($map)->findOrEmpty();
        if ($this->bind->getAttr('unid') > 0 && ($user->isEmpty() || $this->bind->getAttr('unid') !== $user['id'])) {
            throw new Exception("已绑定其他用户！");
        }
        if (!empty($data['extra'])) {
            $user->setAttr('extra', array_merge($user->getAttr('extra'), $data['extra']));
            unset($data['extra']);
        }
        if ($user->isEmpty()) {
            do $data['code'] = $this->generateUserCode();
            while (PluginAccountUser::mk()->where(['code' => $data['code']])->findOrEmpty()->isExists());
        }
        if ($user->save($data + $map) && $user->isExists()) {
            $this->bind->save(['unid' => $user['id']]);
            $this->app->event->trigger('ThinkPlugsAccountBind', [
                'unid' => intval($user['id']), 'usid' => intval($this->bind->getAttr('id')),
            ]);
            return $this->get();
        } else {
            throw new Exception('绑定用户失败！');
        }
    }

    /**
     * 解绑主账号
     * @return array
     * @throws \think\admin\Exception
     */
    public function unBind(): array
    {
        if ($this->bind->isEmpty()) {
            throw new Exception('终端账号不存在！');
        }
        if (($unid = $this->bind->getAttr('unid')) > 0) {
            $this->bind->save(['unid' => 0]);
            $this->app->event->trigger('ThinkPlugsAccountUnbind', [
                'unid' => $unid, 'usid' => $this->bind->getAttr('id'),
            ]);
        }
        return $this->get();
    }

    /**
     * 判断绑定主账号
     * @return boolean
     */
    public function isBind(): bool
    {
        return $this->bind->isExists() && $this->bind->user()->findOrEmpty()->isExists();
    }

    /**
     * 判断是否空账号
     * @return boolean
     */
    public function isNull(): bool
    {
        return $this->bind->isEmpty();
    }

    /**
     * 获取关联终端
     * @return array
     */
    public function allBind(): array
    {
        try {
            if ($this->isNull()) return [];
            if ($this->isBind() && ($unid = $this->bind->getAttr('unid'))) {
                $map = ['unid' => $unid, 'deleted' => 0];
                return PluginAccountBind::mk()->where($map)->select()->toArray();
            } else {
                return [$this->bind->refresh()->toArray()];
            }
        } catch (\Exception $exception) {
            return [];
        }
    }

    /**
     * 解除终端关联
     * @param integer $usid 终端编号
     * @return array
     */
    public function delBind(int $usid): array
    {
        if ($this->isBind() && ($unid = $this->bind->getAttr('unid'))) {
            $map = ['id' => $usid, 'unid' => $unid];
            PluginAccountBind::mk()->where($map)->update(['unid' => 0]);
        }
        return $this->allBind();
    }

    /**
     * 刷新账号序号
     * @return array
     */
    public function recode(): array
    {
        if ($this->bind->isExists() && ($user = $this->bind->user()->findOrEmpty())->isExists()) {
            do $data = ['code' => $this->generateUserCode()];
            while (PluginAccountUser::mk()->where($data)->findOrEmpty()->isExists());
            $user->save($data);
        }
        return $this->get();
    }

    /**
     * 检查是否有效
     * @return array
     * @throws \think\admin\Exception
     */
    public function check(): array
    {
        if ($this->bind->isEmpty()) {
            throw new Exception('登录令牌无效，请重新登录！', 401);
        }
        if ($this->auth->getAttr('token') !== $this->tester) {
            if ($this->expire > 0 && $this->auth->getAttr('time') < time()) {
                throw new Exception('登录认证超时，请重新登录！', 502);
            }
        }
        return static::expire()->get();
    }

    /**
     * 生成授权令牌
     * @param integer $unid
     * @return \plugin\account\service\contract\AccountInterface
     */
    public function token(int $unid): AccountInterface
    {
        // 清理无效令牌
        PluginAccountAuth::mk()->where('token', '<>', $this->tester)->whereBetween('time', [1, time()])->delete();

        // 刷新登录令牌
        if ($this->auth->isEmpty()) {
            $this->auth = PluginAccountAuth::mk()->where(['usid' => $unid])->findOrEmpty();
        }

        // 生成新令牌数据
        if ($this->auth->isEmpty()) {
            do $data = ['type' => $this->type, 'token' => md5(uniqid(strval(rand(0, 999))))];
            while (PluginAccountAuth::mk()->where($data)->findOrEmpty()->isExists());
            $this->auth->save($data + ['usid' => $unid]);
        }
        return $this->expire();
    }

    /**
     * 延期令牌时间
     * @return \plugin\account\service\contract\AccountInterface
     */
    public function expire(): AccountInterface
    {
        $time = $this->expire > 0 ? $this->expire + time() : 0;
        $this->auth->isExists() && $this->auth->save([
            'type' => $this->type, 'time' => $time
        ]);
        return $this;
    }

    /**
     * 更新用户资料
     * @param array $data
     * @return \plugin\account\model\PluginAccountBind
     * @throws \think\admin\Exception
     */
    protected function save(array $data): PluginAccountBind
    {
        if (empty($data)) {
            throw new Exception('用户数据不能为空！');
        }
        if (!empty($data['extra'])) {
            $extra = $this->bind->getAttr('extra');
            $this->bind->setAttr('extra', array_merge($extra, $data['extra']));
            unset($data['extra']);
        }
        if ($this->bind->save($data) && $this->bind->isExists()) {
            return $this->bind->refresh();
        } else {
            throw new Exception('用户数据保存失败！');
        }
    }

    /**
     * 生成用户随机编号
     * @return string
     */
    private function generateUserCode(): string
    {
        return 'U' . CodeExtend::uniqidNumber(15);
    }
}