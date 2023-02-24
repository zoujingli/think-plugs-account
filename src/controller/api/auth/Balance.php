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
use plugin\account\model\PluginAccountUserBalance;
use think\admin\helper\QueryHelper;

/**
 * 用户余额转账
 * Class Balance
 * @package plugin\account\controller\api\auth
 */
class Balance extends Auth
{
    /**
     * 获取余额记录
     */
    public function get()
    {
        PluginAccountUserBalance::mQuery(null, function (QueryHelper $query) {
            $query->withoutField('deleted,create_by');
            $query->where(['unid' => $this->unid, 'deleted' => 0])->like('create_time#date');
            $this->success('获取数据成功！', $query->order('id desc')->page(true, false, false, 10));
        });
    }
}