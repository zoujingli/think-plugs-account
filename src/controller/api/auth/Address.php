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

namespace plugin\account\controller\api\auth;

use plugin\account\controller\api\Auth;
use plugin\account\model\PluginAccountUserAddress;
use think\admin\helper\QueryHelper;

/**
 * 用户收货地址管理
 * Class Address
 * @package plugin\account\controller\api\auth
 */
class Address extends Auth
{
    /**
     * 添加或修改收货地址
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function set()
    {
        $data = $this->_vali([
            'id.default'          => 0,
            'umid.value'          => $this->umid,
            'unid.value'          => $this->unid,
            'type.default'        => 0,
            'idcode.default'      => '', // 身份证号码
            'idimg1.default'      => '', // 身份证正面
            'idimg2.default'      => '', // 身份证反面
            'type.in:0,1'         => '地址状态不在范围！',
            'name.require'        => '收货姓名不能为空！',
            'phone.mobile'        => '收货手机格式错误！',
            'phone.require'       => '收货手机不能为空！',
            'region_prov.require' => '地址省份不能为空！',
            'region_city.require' => '地址城市不能为空！',
            'region_area.require' => '地址区域不能为空！',
            'address.require'     => '详情地址不能为空！',
        ]);

        // 读取历史数据
        $map = ['id' => $data['id'], 'umid' => $data['umid']];
        $addr = PluginAccountUserAddress::mk()->where($map)->findOrEmpty();

        // 去除其它默认选项
        if (isset($data['type']) && $data['type'] > 0) {
            $map = [['umid', '=', $this->umid], ['id', '<>', $data['id']]];
            PluginAccountUserAddress::mk()->where($map)->update(['type' => 0]);
        }

        // 更新保存收货地址
        if ($addr->save($data) && $addr->isExists()) {
            $this->success('地址保存成功！', $addr->refresh()->toArray());
        } else {
            $this->error('地址保存失败！');
        }
    }

    /**
     * 获取收货地址
     */
    public function get()
    {
        PluginAccountUserAddress::mQuery(null, function (QueryHelper $query) {
            $query->equal('id')->where(['umid' => $this->umid, 'deleted' => 0]);
            $query->withoutField('deleted')->order('type desc,id desc');
            $this->success('获取地址数据！', $query->page(false, false, false, 15));
        });
    }

    /**
     * 修改地址状态
     * @return void
     * @throws \think\db\exception\DbException
     */
    public function state()
    {
        $data = $this->_vali([
            'umid.value'   => $this->umid,
            'id.require'   => '地址编号不能为空！',
            'type.in:0,1'  => '地址状态不在范围！',
            'type.require' => '地址状态不能为空！',
        ]);

        // 检查地址是否存在
        $map = ['id' => $data['id'], 'umid' => $data['umid']];
        $addr = PluginAccountUserAddress::mk()->where($map)->findOrEmpty();
        $addr->isEmpty() && $this->error('修改的地址不存在！');

        // 更新默认地址状态
        $addr->save(['type' => $data['type']]);

        // 去除其它默认选项
        if ($data['type'] > 0) {
            $map = [['umid', '=', $this->umid], ['id', '<>', $data['id']]];
            PluginAccountUserAddress::mk()->where($map)->update(['type' => 0]);
        }
        $this->success('默认设置成功！', $addr->refresh()->toArray());
    }

    /**
     * 删除收货地址
     */
    public function remove()
    {
        $map = $this->_vali([
            'id.require' => '地址不能为空！', 'umid.value' => $this->umid,
        ]);
        $item = PluginAccountUserAddress::mk()->where($map)->findOrEmpty();
        if ($item->isEmpty()) $this->error('需要删除的地址不存在！');
        if ($item->save(['deleted' => 1]) !== false) {
            $this->success('删除地址成功！');
        } else {
            $this->error('删除地址失败！');
        }
    }
}