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

use think\migration\Migrator;

@set_time_limit(0);
@ini_set('memory_limit', -1);

class InstallAccount20230516 extends Migrator
{

    /**
     * 创建数据库
     */
    public function change()
    {
        $this->_create_plugin_account_msms();
    }

    /**
     * 创建数据对象
     * @class PluginAccountMsms
     * @table plugin_account_msms
     * @return void
     */
    private function _create_plugin_account_msms()
    {

        // 当前数据表
        $table = 'plugin_account_msms';

        // 存在则跳过
        if ($this->hasTable($table)) return;

        // 创建数据表
        $this->table($table, [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '插件-账号-短信',
        ])
            ->addColumn('uuid', 'biginteger', ['limit' => 20, 'default' => 0, 'null' => false, 'comment' => '账号编号'])
            ->addColumn('usid', 'biginteger', ['limit' => 20, 'default' => 0, 'null' => false, 'comment' => '终端编号'])
            ->addColumn('type', 'string', ['limit' => 100, 'default' => '', 'null' => true, 'comment' => '短信类型'])
            ->addColumn('scene', 'string', ['limit' => 100, 'default' => '', 'null' => true, 'comment' => '业务场景'])
            ->addColumn('smsid', 'string', ['limit' => 100, 'default' => '', 'null' => true, 'comment' => '消息编号'])
            ->addColumn('phone', 'string', ['limit' => 100, 'default' => '', 'null' => true, 'comment' => '目标手机'])
            ->addColumn('result', 'string', ['limit' => 512, 'default' => '', 'null' => true, 'comment' => '返回结果'])
            ->addColumn('params', 'string', ['limit' => 512, 'default' => '', 'null' => true, 'comment' => '短信内容'])
            ->addColumn('status', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '短信状态(0失败,1成功)'])
            ->addColumn('create_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间'])
            ->addIndex('type', ['name' => 'idx_plugin_account_msms_type'])
            ->addIndex('uuid', ['name' => 'idx_plugin_account_msms_uuid'])
            ->addIndex('usid', ['name' => 'idx_plugin_account_msms_usid'])
            ->addIndex('phone', ['name' => 'idx_plugin_account_msms_phone'])
            ->addIndex('scene', ['name' => 'idx_plugin_account_msms_scene'])
            ->addIndex('smsid', ['name' => 'idx_plugin_account_msms_smsid'])
            ->addIndex('status', ['name' => 'idx_plugin_account_msms_status'])
            ->addIndex('create_time', ['name' => 'idx_plugin_account_msms_create_time'])
            ->create();

        // 修改主键长度
        $this->table($table)->changeColumn('id', 'integer', ['limit' => 11, 'identity' => true]);
    }
}
