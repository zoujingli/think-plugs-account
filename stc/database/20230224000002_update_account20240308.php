<?php

// +----------------------------------------------------------------------
// | Account Plugin for ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2022~2024 ThinkAdmin [ thinkadmin.top ]
// +----------------------------------------------------------------------
// | 官方网站: https://thinkadmin.top
// +----------------------------------------------------------------------
// | 免责声明 ( https://thinkadmin.top/disclaimer )
// | 会员免费 ( https://thinkadmin.top/vip-introduce )
// +----------------------------------------------------------------------
// | gitee 代码仓库：https://gitee.com/zoujingli/think-plugs-account
// | github 代码仓库：https://github.com/zoujingli/think-plugs-account
// +----------------------------------------------------------------------

use think\migration\Migrator;

@set_time_limit(0);
@ini_set('memory_limit', -1);

/**
 * 数据库更新补丁
 * @class UpdateAccount20240308
 * @package think\migration\Migrator
 */
class UpdateAccount20240308 extends Migrator
{
    /**
     * 更新数据库
     */
    public function change()
    {
        // 更新数据库字段
        $table = $this->table('plugin_account_msms');
        $table->hasColumn('unid') || $table->renameColumn('uuid', 'unid')->update();
    }
}