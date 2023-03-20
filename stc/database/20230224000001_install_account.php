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

class InstallAccount extends Migrator
{

    /**
     * 创建数据库
     */
    public function change()
    {
        $this->_create_plugin_account_auth();
        $this->_create_plugin_account_bind();
        $this->_create_plugin_account_user();
    }

    /**
     * 创建数据对象
     * @class PluginAccountAuth
     * @table plugin_account_auth
     * @return void
     */
    private function _create_plugin_account_auth()
    {

        // 当前数据表
        $table = 'plugin_account_auth';

        // 存在则跳过
        if ($this->hasTable($table)) return;

        // 创建数据表
        $this->table($table, [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '插件-账号-授权',
        ])
            ->addColumn('usid', 'integer', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '终端账号'])
            ->addColumn('type', 'string', ['limit' => 20, 'default' => '', 'null' => true, 'comment' => '授权类型'])
            ->addColumn('time', 'integer', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '有效时间'])
            ->addColumn('token', 'string', ['limit' => 32, 'default' => '', 'null' => true, 'comment' => '授权令牌'])
            ->addColumn('tokenv', 'string', ['limit' => 32, 'default' => '', 'null' => true, 'comment' => '授权验证'])
            ->addColumn('create_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间'])
            ->addIndex('usid', ['name' => 'idx_plugin_account_auth_usid'])
            ->addIndex('type', ['name' => 'idx_plugin_account_auth_type'])
            ->addIndex('time', ['name' => 'idx_plugin_account_auth_time'])
            ->addIndex('token', ['name' => 'idx_plugin_account_auth_token'])
            ->save();

        // 修改主键长度
        $this->table($table)->changeColumn('id', 'integer', ['limit' => 20, 'identity' => true]);
    }

    /**
     * 创建数据对象
     * @class PluginAccountBind
     * @table plugin_account_bind
     * @return void
     */
    private function _create_plugin_account_bind()
    {

        // 当前数据表
        $table = 'plugin_account_bind';

        // 存在则跳过
        if ($this->hasTable($table)) return;

        // 创建数据表
        $this->table($table, [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '插件-账号-终端',
        ])
            ->addColumn('unid', 'integer', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '会员编号'])
            ->addColumn('type', 'string', ['limit' => 20, 'default' => '', 'null' => true, 'comment' => '终端类型'])
            ->addColumn('phone', 'string', ['limit' => 30, 'default' => '', 'null' => true, 'comment' => '绑定手机'])
            ->addColumn('openid', 'string', ['limit' => 50, 'default' => '', 'null' => true, 'comment' => 'OPENID'])
            ->addColumn('unionid', 'string', ['limit' => 50, 'default' => '', 'null' => true, 'comment' => 'UnionID'])
            ->addColumn('headimg', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '用户头像'])
            ->addColumn('nickname', 'string', ['limit' => 99, 'default' => '', 'null' => true, 'comment' => '用户昵称'])
            ->addColumn('password', 'string', ['limit' => 32, 'default' => '', 'null' => true, 'comment' => '登录密码'])
            ->addColumn('extra', 'text', ['default' => NULL, 'null' => true, 'comment' => '扩展数据'])
            ->addColumn('sort', 'integer', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '排序权重'])
            ->addColumn('status', 'integer', ['limit' => 1, 'default' => 1, 'null' => true, 'comment' => '账号状态'])
            ->addColumn('deleted', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '删除状态(0未删,1已删)'])
            ->addColumn('create_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '注册时间'])
            ->addColumn('update_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间'])
            ->addIndex('type', ['name' => 'idx_plugin_account_bind_type'])
            ->addIndex('unid', ['name' => 'idx_plugin_account_bind_unid'])
            ->addIndex('sort', ['name' => 'idx_plugin_account_bind_sort'])
            ->addIndex('status', ['name' => 'idx_plugin_account_bind_status'])
            ->addIndex('openid', ['name' => 'idx_plugin_account_bind_openid'])
            ->addIndex('unionid', ['name' => 'idx_plugin_account_bind_unionid'])
            ->addIndex('deleted', ['name' => 'idx_plugin_account_bind_deleted'])
            ->addIndex('create_time', ['name' => 'idx_plugin_account_bind_create_time'])
            ->save();

        // 修改主键长度
        $this->table($table)->changeColumn('id', 'integer', ['limit' => 20, 'identity' => true]);
    }

    /**
     * 创建数据对象
     * @class PluginAccountUser
     * @table plugin_account_user
     * @return void
     */
    private function _create_plugin_account_user()
    {

        // 当前数据表
        $table = 'plugin_account_user';

        // 存在则跳过
        if ($this->hasTable($table)) return;

        // 创建数据表
        $this->table($table, [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '插件-账号-用户',
        ])
            ->addColumn('code', 'string', ['limit' => 16, 'default' => '', 'null' => true, 'comment' => '用户编号'])
            ->addColumn('phone', 'string', ['limit' => 20, 'default' => '', 'null' => true, 'comment' => '用户手机'])
            ->addColumn('email', 'string', ['limit' => 20, 'default' => '', 'null' => true, 'comment' => '用户账号'])
            ->addColumn('unionid', 'string', ['limit' => 50, 'default' => '', 'null' => true, 'comment' => 'UnionID'])
            ->addColumn('username', 'string', ['limit' => 50, 'default' => '', 'null' => true, 'comment' => '用户姓名'])
            ->addColumn('nickname', 'string', ['limit' => 99, 'default' => '', 'null' => true, 'comment' => '用户昵称'])
            ->addColumn('headimg', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '用户头像'])
            ->addColumn('region_prov', 'string', ['limit' => 99, 'default' => '', 'null' => true, 'comment' => '所在省份'])
            ->addColumn('region_city', 'string', ['limit' => 99, 'default' => '', 'null' => true, 'comment' => '所在城市'])
            ->addColumn('region_area', 'string', ['limit' => 99, 'default' => '', 'null' => true, 'comment' => '所在区域'])
            ->addColumn('remark', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '备注(内部使用)'])
            ->addColumn('extra', 'text', ['default' => NULL, 'null' => true, 'comment' => '扩展数据'])
            ->addColumn('sort', 'integer', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '排序权重'])
            ->addColumn('status', 'integer', ['limit' => 1, 'default' => 1, 'null' => true, 'comment' => '用户状态(0拉黑,1正常)'])
            ->addColumn('deleted', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '删除状态(0未删,1已删)'])
            ->addColumn('create_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '注册时间'])
            ->addColumn('update_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间'])
            ->addIndex('code', ['name' => 'idx_plugin_account_user_code'])
            ->addIndex('phone', ['name' => 'idx_plugin_account_user_phone'])
            ->addIndex('email', ['name' => 'idx_plugin_account_user_email'])
            ->addIndex('unionid', ['name' => 'idx_plugin_account_user_unionid'])
            ->addIndex('username', ['name' => 'idx_plugin_account_user_username'])
            ->addIndex('region_prov', ['name' => 'idx_plugin_account_user_region_prov'])
            ->addIndex('region_city', ['name' => 'idx_plugin_account_user_region_city'])
            ->addIndex('region_area', ['name' => 'idx_plugin_account_user_region_area'])
            ->addIndex('sort', ['name' => 'idx_plugin_account_user_sort'])
            ->addIndex('status', ['name' => 'idx_plugin_account_user_status'])
            ->addIndex('deleted', ['name' => 'idx_plugin_account_user_deleted'])
            ->addIndex('create_time', ['name' => 'idx_plugin_account_user_create_time'])
            ->save();

        // 修改主键长度
        $this->table($table)->changeColumn('id', 'integer', ['limit' => 20, 'identity' => true]);
    }
}
