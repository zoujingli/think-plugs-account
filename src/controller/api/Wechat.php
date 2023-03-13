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

use app\wechat\service\WechatService;
use plugin\account\service\Account;
use think\admin\Controller;
use think\Response;

/**
 * 微信服务号入口
 * Class Wechat
 * @package plugin\account\controller\api
 * @example 域名请修改为自己的地址，放到网页代码合适位置
 * <meta name="referrer" content="always">
 * <script referrerpolicy="unsafe-url" src="https://your.domain.com/data/api.wechat/oauth?mode=1"></script>
 *
 * 授权模式支持两种模块，参数 mode=0 时为静默授权，mode=1 时为完整授权
 * 注意：回跳地址默认从 Header 中的 http_referer 获取，也可以传 source 参数
 */
class Wechat extends Controller
{

    /**
     * 通道认证类型
     * @var string
     */
    const type = Account::WECHAT;

    /**
     * 唯一绑定字段
     * @var string
     */
    private $field;

    /**
     * 微信调度器
     * @var WechatService
     */
    private $wechat;

    /**
     * 接口原地址
     * @var string
     */
    private $target;

    /**
     * 控制器初始化
     * @return $this
     */
    protected function initialize(): Wechat
    {
        if ($this->field = Account::field(static::type)) {
            $this->wechat = WechatService::instance();
            $this->target = input('source') ?: $this->request->server('http_referer', $this->request->url(true));
        } else {
            $this->error(sprintf('接口通道 [%s] 未开通！', static::type));
        }
        return $this;
    }

    /**
     * 生成微信网页签名
     * @throws \WeChat\Exceptions\InvalidResponseException
     * @throws \WeChat\Exceptions\LocalCacheException
     * @throws \think\admin\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function jssdk()
    {
        $this->success('获取签名参数', $this->wechat->getWebJssdkSign($this->target));
    }

    /**
     * 微信网页授权脚本
     * @return \think\Response
     * @throws \WeChat\Exceptions\InvalidResponseException
     * @throws \WeChat\Exceptions\LocalCacheException
     * @throws \think\admin\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function oauth(): Response
    {
        $script = [];
        $result = $this->wechat->getWebOauthInfo($this->target, input('mode', 1), false);
        if (empty($result['openid'])) {
            $script[] = 'alert("WeChat Oauth failed.")';
        } else {
            $fans = $result['fansinfo'] ?? [];
            // 筛选保存数据
            $data = [$this->field => $result['openid'], 'extra' => $fans];
            if (isset($fans['unionid'])) $data['unionid'] = $fans['unionid'];
            if (isset($fans['nickname'])) $data['nickname'] = $fans['nickname'];
            if (isset($fans['headimgurl'])) $data['headimg'] = $fans['headimgurl'];
            $result['userinfo'] = Account::mk(static::type)->set($data, true);
            // 返回数据给前端
            $script[] = "window.WeChatOpenid='{$result['openid']}'";
            $script[] = 'window.WeChatFansInfo=' . json_encode($result['fansinfo'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $script[] = 'window.WeChatUserInfo=' . json_encode($result['userinfo'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        $script[] = '';
        return Response::create(join(";\n", $script))->contentType('application/x-javascript');
    }

    /**
     * 网页授权测试
     * 使用网页直接访问此链接
     * @return string
     */
    public function otest(): string
    {
        // 生成网页授权脚本
        $authurl = url('api.wechat/oauth', ['mode' => 1], false, true)->build();

        // 返回授权测试模板
        return <<<EOL
<html lang="zh">
    <head>
        <meta charset="utf-8">
        <title>微信网页授权测试</title>
        <meta name="referrer" content="always">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
        <style>pre{padding:20px;overflow:auto;margin-top:10px;background:#ccc;border-radius:6px;}</style>
    </head>
    <body>
        <div>当前链接</div>
        <pre>{$authurl}</pre>
        
        <div style="margin-top:30px">粉丝数据</div>
        <pre id="fansdata">待网页授权，加载粉丝数据...</pre>
        
        <div style="margin-top:30px">用户数据</div>
        <pre id="userdata">待网页授权，加载用户数据...</pre>
        
         <script referrerpolicy="unsafe-url" src="{$authurl}"></script> 
        <script>
            if(typeof window.WeChatFansInfo === 'object'){   
                document.getElementById('fansdata').innerText = JSON.stringify(window.WeChatFansInfo, null, 2);
            }
            if(typeof window.WeChatUserInfo === 'object'){
                document.getElementById('userdata').innerText = JSON.stringify(window.WeChatUserInfo, null, 2);
            }
        </script>
    </body>
</html>
EOL;
    }
}