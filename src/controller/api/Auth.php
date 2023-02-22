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

namespace plugin\account\controller\api;

use plugin\account\service\Account;
use think\admin\Controller;
use think\admin\extend\JwtExtend;
use think\exception\HttpResponseException;

/**
 * 接口授权认证基类
 * Class Auth
 * @package plugin\account\controller\api
 */
abstract class Auth extends Controller
{
    /**
     * 终端用户编号
     * @var integer
     */
    protected $unid;

    /**
     * 终端用户数据
     * @var array
     */
    protected $device;

    /**
     * 终端账号接口
     * @var \plugin\account\service\contract\AccountInterface
     */
    protected $account;

    /**
     * 控制器初始化
     */
    protected function initialize()
    {
        try {
            // 读取用户账号数据
            $auther = JwtExtend::getInData();
            $this->account = Account::mk($auther['type'] ?? '-', $auther['token'] ?? '-');
            $this->device = $this->account->check();
            $this->unid = $this->device['id'];
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage(), '{-null-}', $exception->getCode());
        }
    }

    /**
     * 显示用户禁用提示
     */
    protected function checkUserStatus()
    {
        if (empty($this->device['status'])) {
            $this->error('终端用户已被冻结！');
        }
    }
}