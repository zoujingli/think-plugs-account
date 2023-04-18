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

namespace plugin\account\controller\api;

use plugin\account\service\Account;
use think\admin\Controller;
use think\exception\HttpResponseException;

/**
 * 接口授权抽象类
 * @class Auth
 * @package plugin\account\controller\api
 */
abstract class Auth extends Controller
{

    /**
     * 接口类型
     * @var string
     */
    protected $type;

    /**
     * 主账号编号
     * @var integer
     */
    protected $unid;

    /**
     * 子账号编号
     * @var integer
     */
    protected $usid;

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
            // 获取请求令牌内容
            $token = $this->request->header('api-token', '');
            if (empty($token)) $this->error("请求TOKEN不能为空！");
            // 读取用户账号数据
            $this->account = Account::mk('', $token);
            $bind = $this->account->check();
            $this->usid = intval($bind['id'] ?? 0);
            $this->unid = intval($bind['unid'] ?? 0);
            $this->type = strval($bind['type'] ?? '');
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage(), [], $exception->getCode());
        }
    }

    /**
     * 检查用户状态
     * @param boolean $isBind
     * @return void
     */
    protected function checkUserStatus(bool $isBind = true)
    {
        $bind = $this->account->get();
        if (empty($bind['status'])) {
            $this->error('终端用户已被冻结！');
        } elseif ($isBind) {
            if (empty($bind['user'])) {
                $this->error('请完善账号资料！');
            }
            if (empty($bind['user']['status'])) {
                $this->error('账号已被冻结！');
            }
        }
    }
}