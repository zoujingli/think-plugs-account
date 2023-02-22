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

namespace plugin\account\controller\api\auth;

use plugin\account\controller\api\Auth;

/**
 * 用户资料管理
 * Class Center
 * @package plugin\account\controller\api\auth
 */
class Center extends Auth
{
    /**
     * 更新用户资料
     */
    public function set()
    {
        $data = $this->_vali([
            'headimg.default'       => '',
            'username.default'      => '',
            'base_age.default'      => '',
            'base_sex.default'      => '',
            'base_height.default'   => '',
            'base_weight.default'   => '',
            'base_birthday.default' => '',
        ]);
        foreach ($data as $key => $vo) if ($vo === '') unset($data[$key]);
        if (empty($data)) $this->error('没有修改的数据！');
        if (DataUser::mk()->where(['id' => $this->uuid])->update($data) !== false) {
            $this->success('更新资料成功！', $this->getUser());
        } else {
            $this->error('更新资料失败！');
        }
    }

    /**
     * 获取用户资料
     */
    public function get()
    {
        $this->success('获取用户资料', $this->account->get());
    }
}