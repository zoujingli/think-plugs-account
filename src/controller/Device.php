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

namespace plugin\account\controller;

use plugin\account\model\PluginAccountBind;
use plugin\account\service\Account;
use think\admin\Controller;
use think\admin\helper\QueryHelper;

/**
 * 终端用户管理
 * @class Device
 * @package plugin\account\controller\user
 */
class Device extends Controller
{
    /**
     * 终端用户管理
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->type = $this->get['type'] ?? 'index';
        PluginAccountBind::mQuery()->layTable(function () {
            $this->title = '终端用户管理';
            $this->types = Account::types(1);
        }, function (QueryHelper $query) {
            $query->with('user')->equal('type#utype')->like('phone,nickname,username,create_time');
            $query->where(['deleted' => 0, 'status' => intval($this->type === 'index')]);
        });
    }

    /**
     * 接口终端配置
     * @auth true
     * @return void
     * @throws \think\admin\Exception
     */
    public function config()
    {
        $this->types = Account::types();
        $this->expire = Account::expire();
        if ($this->request->isGet()) {
            $this->fetch();
        } else {
            // 设置接口有效时间
            $expire = $this->request->post('expire');
            if (is_numeric($expire)) {
                Account::expire($expire);
            }
            // 设置开放接口通道
            $types = $this->request->post('types', []);
            foreach ($this->types as $k => $v) {
                Account::set($k, intval(in_array($k, $types)));
            }
            if (Account::save()) {
                $this->success('配置保存成功！');
            } else {
                $this->error('配置保存失败！');
            }
        }
    }

    /**
     * 修改用户状态
     * @auth true
     */
    public function state()
    {
        PluginAccountBind::mSave($this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }
}