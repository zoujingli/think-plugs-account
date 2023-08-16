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
use think\Response;
use WeMini\Crypt;
use WeMini\Live;
use WeMini\Qrcode;

/**
 * 微信小程序入口
 * @class Wxapp
 * @package plugin\account\controller\api
 */
class Wxapp extends Controller
{
    /**
     * 接口通道类型
     * @var string
     */
    private const type = Account::WXAPP;

    /**
     * 小程序配置参数
     * @var array
     */
    private $params;

    /**
     * 接口初始化
     * @throws \think\admin\Exception
     */
    protected function initialize()
    {
        if (Account::field(static::type)) {
            $wxapp = sysdata('plugin.wechat.wxapp');
            $this->params = [
                'appid'      => $wxapp['appid'] ?? '',
                'appsecret'  => $wxapp['appkey'] ?? '',
                'cache_path' => syspath('runtime/wechat'),
            ];
        } else {
            $this->error('接口未开通');
        }
    }

    /**
     * 换取会话
     */
    public function session()
    {
        try {
            $input = $this->_vali(['code.require' => '凭证不能为空']);
            [$openid, $unionid, $sesskey] = $this->applySesskey($input['code']);
            $data = [
                'appid'       => $this->params['appid'],
                'openid'      => $openid,
                'unionid'     => $unionid,
                'session_key' => $sesskey,
            ];
            $this->success('授权换取成功', Account::mk(static::type)->set($data, true));
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            trace_file($exception);
            $this->error("处理失败，{$exception->getMessage()}");
        }
    }

    /**
     * 数据解密
     */
    public function decode()
    {
        try {
            $input = $this->_vali([
                'iv.require'        => '向量不能为空',
                'code.require'      => '授权不能为空',
                'encrypted.require' => '密文不能为空',
            ]);
            [$openid, $unionid, $input['session_key']] = $this->applySesskey($input['code']);
            $result = Crypt::instance($this->params)->decode($input['iv'], $input['session_key'], $input['encrypted']);
            if (is_array($result) && isset($result['avatarUrl']) && isset($result['nickName'])) {
                $data = [
                    'extra'    => $result,
                    'appid'    => $this->params['appid'],
                    'openid'   => $openid,
                    'unionid'  => $unionid,
                    'headimg'  => $result['avatarUrl'],
                    'nickname' => $result['nickName'],
                ];
                if ($data['nickname'] === '微信用户') unset($data['headimg'], $data['nickname']);
                $this->success('数据解密成功', Account::mk(static::type)->set($data, true));
            } elseif (is_array($result)) {
                $this->success('数据解密成功', $result);
            } else {
                $this->error('数据解析失败');
            }
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            trace_file($exception);
            $this->error("数据处理失败，{$exception->getMessage()}");
        }
    }

    /**
     * 换取会话授权
     * @param string $code 授权编号
     * @return array [openid, unionid, sessionkey]
     */
    private function applySesskey(string $code): array
    {
        try {
            $cache = $this->app->cache->get($code, []);
            if (isset($cache['openid']) && isset($cache['session_key'])) {
                return [$cache['openid'], $cache['unionid'] ?? '', $cache['session_key']];
            }
            $result = Crypt::instance($this->params)->session($code);
            if (isset($result['openid']) && isset($result['session_key'])) {
                $this->app->cache->set($code, $result, 7200);
                return [$result['openid'], $result['unionid'] ?? '', $result['session_key']];
            } else {
                $this->error($result['errmsg'] ?? '授权换取失败');
            }
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            trace_file($exception);
            $this->error("换取授权失败，{$exception->getMessage()}");
        }
    }

    /**
     * 获取小程序码
     */
    public function qrcode(): Response
    {
        try {
            $data = $this->_vali([
                'size.default' => 430,
                'type.default' => 'base64',
                'path.require' => '跳转不能为空',
            ]);
            $result = Qrcode::instance($this->params)->createMiniPath($data['path'], $data['size']);
            if ($data['type'] === 'base64') {
                $this->success('生成小程序码成功！', [
                    'base64' => 'data:image/png;base64,' . base64_encode($result),
                ]);
            } else {
                return response($result)->contentType('image/png');
            }
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            trace_file($exception);
            $this->error($exception->getMessage());
        }
    }

    /**
     * 获取直播列表
     */
    public function getLiveList()
    {
        try {
            $data = $this->_vali(['start.default' => 0, 'limit.default' => 10]);
            $list = Live::instance($this->params)->getLiveList($data['start'], $data['limit']);
            $this->success('获取直播列表成功！', $list);
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            trace_file($exception);
            $this->error($exception->getMessage());
        }
    }

    /**
     * 获取回放源视频
     */
    public function getLiveInfo()
    {
        try {
            $data = $this->_vali([
                'start.default'   => 0,
                'limit.default'   => 10,
                'action.default'  => 'get_replay',
                'room_id.require' => '直播间不能为空',
            ]);
            $result = Live::instance($this->params)->getLiveInfo($data);
            $this->success('获取回放视频成功！', $result);
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            trace_file($exception);
            $this->error($exception->getMessage());
        }
    }
}