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

namespace plugin\account\controller;

use plugin\account\service\Account;
use think\admin\Controller;

/**
 * 普通用户管理
 * Class User
 * @package plugin\account\controller\user
 */
class Admin extends Controller
{
    /**
     * 普通用户管理
     * @auth true
     * @menu true
     */
    public function index(): string
    {
        $token = '';
        $result = Account::mk(Account::CHANNEL_WAP, $token)->set(
            ['phone' => '13617343800'],
            ['username' => 'Anyon']
        );
        dump($result);
        return __METHOD__;
    }

    /**
     * 修改用户状态
     * @auth true
     */
    public function state()
    {
    }
}