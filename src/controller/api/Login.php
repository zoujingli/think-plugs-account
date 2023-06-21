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
use think\admin\service\CaptchaService;

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
            $this->error('该接口不支持手机号登录！');
        }
        if (Message::checkVerifyCode($data['verify'], $data['phone'])) {
            Message::clearVerifyCode($data['phone']);
            $account = Account::mk($data['type']);
            $account->set($inset = ['phone' => $data['phone']]);
            $account->isBind() || $account->bind($inset, $inset);
            $this->success('绑定主账号成功！', $account->get(true));
        } else {
            $this->error('短信验证失败！');
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
            'verify.require' => '图形验证码不能为空！',
            'uniqid.require' => '图形唯一码不能为空！',
        ]);
        // 检查图形验证码
        if (!CaptchaService::instance()->check($data['verify'], $data['uniqid'])) {
            $this->error('图形码验证失败！');
        }
        // 发送手机短信验证码
        [$state, $info, $time] = Message::sendVerifyCode($data['phone']);
        $state ? $this->success($info, ['time' => $time]) : $this->error($info);
    }

    /**
     * 生成图形验证码
     * @return void
     */
    public function image()
    {
        $image = CaptchaService::instance()->initialize(['charset' => '0123456789']);
        $captcha = ['image' => $image->getData(), 'uniqid' => $image->getUniqid()];
        $this->success('生成验证码成功', $captcha);
    }
}