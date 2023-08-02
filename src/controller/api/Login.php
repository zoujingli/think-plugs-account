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
use plugin\account\service\Message;
use think\admin\Controller;
use think\admin\extend\ImageVerify;

/**
 * 手机号登录入口
 * @class Login
 * @package plugin\account\controller\api
 */
class Login extends Controller
{
    /**
     * 通过手机号登录
     * @return void
     * @throws \think\admin\Exception
     */
    public function in()
    {
        $data = $this->_vali([
            'type.require'   => '类型不能为空！',
            'phone.mobile'   => '手机号格式错误！',
            'phone.require'  => '手机号不能为空！',
            'verify.require' => '验证码不能为空！'
        ]);
        if (Account::field($data['type']) !== 'phone') {
            $this->error('不支持手机登录');
        }
        if (Message::checkVerifyCode($data['verify'], $data['phone'])) {
            Message::clearVerifyCode($data['phone']);
            $account = Account::mk($data['type']);
            $account->set($inset = ['phone' => $data['phone']]);
            $account->isBind() || $account->bind($inset, $inset);
            $this->success('绑定主账号成功', $account->get(true));
        } else {
            $this->error('短信验证失败');
        }
    }

    /**
     * 发送短信验证码
     * @return void
     */
    public function send()
    {
        $data = $this->_vali([
            'phone.mobile'   => '手机号格式错误！',
            'phone.require'  => '手机号不能为空！',
            'uniqid.require' => '拼图编号不能为空！',
            'verify.require' => '拼图位置不能为空！',
        ]);
        // 检查拼图验证码
        $state = ImageVerify::verify($data['uniqid'], $data['verify'], true);
        // 发送手机短信验证码
        if ($state === 1) {
            [$state, $info, $result] = Message::sendVerifyCode($data['phone']);
            $state ? $this->success($info, $result) : $this->error($info);
        } else {
            $this->error('拼图验证失败');
        }
    }

    /**
     * 生成拼图验证码
     * @return void
     */
    public function image()
    {
        $images = [
            syspath('public/static/theme/img/login/bg1.jpg'),
            syspath('public/static/theme/img/login/bg2.jpg'),
        ];
        $image = ImageVerify::render($images[array_rand($images)]);
        $this->success('生成拼图成功', [
            'bgimg'  => $image['bgimg'],
            'water'  => $image['water'],
            'uniqid' => $image['code'],
        ]);
    }

    /**
     * 实时验证结果
     * @return void
     */
    public function verify()
    {
        $data = $this->_vali([
            'uniqid.require' => '拼图验证不能为空！',
            'verify.require' => '拼图数值不能为空！'
        ]);
        $result = ImageVerify::verify($data['uniqid'], $data['verify']);
        $this->success('获取验证结果', ['state' => $result]);
    }
}