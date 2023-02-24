<?php

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
        $this->_create_plugin_account_user_address();
        $this->_create_plugin_account_user_balance();
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
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '插件-账号-资料',
        ])
            ->addColumn('path', 'string', ['limit' => 999, 'default' => ',,', 'null' => true, 'comment' => '关系路径'])
            ->addColumn('phone', 'string', ['limit' => 20, 'default' => '', 'null' => true, 'comment' => '用户手机'])
            ->addColumn('headimg', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '用户头像'])
            ->addColumn('nickname', 'string', ['limit' => 99, 'default' => '', 'null' => true, 'comment' => '用户昵称'])
            ->addColumn('username', 'string', ['limit' => 50, 'default' => '', 'null' => true, 'comment' => '用户姓名'])
            ->addColumn('remark', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '用户备注'])
            ->addColumn('extra', 'text', ['default' => NULL, 'null' => true, 'comment' => '扩展数据'])
            ->addColumn('sort', 'integer', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '排序权重'])
            ->addColumn('status', 'integer', ['limit' => 1, 'default' => 1, 'null' => true, 'comment' => '用户状态(0拉黑,1正常)'])
            ->addColumn('deleted', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '删除状态(0未删,1已删)'])
            ->addColumn('create_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '注册时间'])
            ->addColumn('update_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间'])
            ->addIndex('sort', ['name' => 'idx_plugin_account_user_sort'])
            ->addIndex('phone', ['name' => 'idx_plugin_account_user_phone'])
            ->addIndex('status', ['name' => 'idx_plugin_account_user_status'])
            ->addIndex('deleted', ['name' => 'idx_plugin_account_user_deleted'])
            ->addIndex('create_time', ['name' => 'idx_plugin_account_user_create_time'])
            ->save();

        // 修改主键长度
        $this->table($table)->changeColumn('id', 'integer', ['limit' => 20, 'identity' => true]);
    }

    /**
     * 创建数据对象
     * @class PluginAccountUserAddress
     * @table plugin_account_user_address
     * @return void
     */
    private function _create_plugin_account_user_address()
    {

        // 当前数据表
        $table = 'plugin_account_user_address';

        // 存在则跳过
        if ($this->hasTable($table)) return;

        // 创建数据表
        $this->table($table, [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '插件-账号-地址',
        ])
            ->addColumn('unid', 'integer', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '主账号ID'])
            ->addColumn('usid', 'integer', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '子账号ID'])
            ->addColumn('type', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '默认状态(0普通,1默认)'])
            ->addColumn('name', 'string', ['limit' => 100, 'default' => '', 'null' => true, 'comment' => '收货人姓名'])
            ->addColumn('phone', 'string', ['limit' => 20, 'default' => '', 'null' => true, 'comment' => '收货人手机'])
            ->addColumn('idcode', 'string', ['limit' => 255, 'default' => '', 'null' => true, 'comment' => '身体证证号'])
            ->addColumn('idimg1', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '身份证正面'])
            ->addColumn('idimg2', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '身份证反面'])
            ->addColumn('region_prov', 'string', ['limit' => 100, 'default' => '', 'null' => true, 'comment' => '地址-省份'])
            ->addColumn('region_city', 'string', ['limit' => 100, 'default' => '', 'null' => true, 'comment' => '地址-城市'])
            ->addColumn('region_area', 'string', ['limit' => 100, 'default' => '', 'null' => true, 'comment' => '地址-区域'])
            ->addColumn('region_addr', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '地址-详情'])
            ->addColumn('deleted', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '删除状态(0未删除,1已删除)'])
            ->addColumn('create_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间'])
            ->addIndex('type', ['name' => 'idx_plugin_account_user_address_type'])
            ->addIndex('usid', ['name' => 'idx_plugin_account_user_address_usid'])
            ->addIndex('unid', ['name' => 'idx_plugin_account_user_address_unid'])
            ->addIndex('phone', ['name' => 'idx_plugin_account_user_address_phone'])
            ->addIndex('deleted', ['name' => 'idx_plugin_account_user_address_deleted'])
            ->save();

        // 修改主键长度
        $this->table($table)->changeColumn('id', 'integer', ['limit' => 20, 'identity' => true]);
    }

    /**
     * 创建数据对象
     * @class PluginAccountUserBalance
     * @table plugin_account_user_balance
     * @return void
     */
    private function _create_plugin_account_user_balance()
    {

        // 当前数据表
        $table = 'plugin_account_user_balance';

        // 存在则跳过
        if ($this->hasTable($table)) return;

        // 创建数据表
        $this->table($table, [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '插件-账号-余额',
        ])
            ->addColumn('unid', 'integer', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '账号编号'])
            ->addColumn('code', 'string', ['limit' => 20, 'default' => '', 'null' => true, 'comment' => '操作编号'])
            ->addColumn('name', 'string', ['limit' => 200, 'default' => '', 'null' => true, 'comment' => '操作名称'])
            ->addColumn('remark', 'string', ['limit' => 999, 'default' => '', 'null' => true, 'comment' => '操作备注'])
            ->addColumn('amount', 'decimal', ['precision' => 20, 'scale' => 2, 'default' => '0.00', 'null' => true, 'comment' => '操作金额'])
            ->addColumn('cancel', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '作废状态(0未作废,1已作废)'])
            ->addColumn('unlock', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '解锁状态(0锁定中,1已生效)'])
            ->addColumn('deleted', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '删除状态(0未删除,1已删除)'])
            ->addColumn('create_by', 'integer', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '系统用户'])
            ->addColumn('cancel_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '作废时间'])
            ->addColumn('unlock_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '解锁时间'])
            ->addColumn('create_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间'])
            ->addIndex('unid', ['name' => 'idx_plugin_account_user_balance_unid'])
            ->addIndex('code', ['name' => 'idx_plugin_account_user_balance_code'])
            ->addIndex('cancel', ['name' => 'idx_plugin_account_user_balance_cancel'])
            ->addIndex('unlock', ['name' => 'idx_plugin_account_user_balance_unlock'])
            ->addIndex('deleted', ['name' => 'idx_plugin_account_user_balance_deleted'])
            ->addIndex('create_time', ['name' => 'idx_plugin_account_user_balance_create_time'])
            ->save();

        // 修改主键长度
        $this->table($table)->changeColumn('id', 'integer', ['limit' => 20, 'identity' => true]);
    }
}
