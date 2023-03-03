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

namespace plugin\account\service\contract;

/**
 * 用户账号接口类
 * @class AccountInterface
 * @package plugin\account\service\contract
 */
interface AccountInterface
{
    /**
     * 读取子账号资料
     * @param boolean $rejwt
     * @return array
     */
    public function get(bool $rejwt = false): array;

    /**
     * 设置子账号资料
     * @param array $data 用户资料
     * @param boolean $rejwt 返回令牌
     * @return array
     */
    public function set(array $data = [], bool $rejwt = false): array;

    /**
     * 初始化通道
     * @param string $token
     * @return \plugin\account\service\contract\AccountInterface
     */
    public function init(string $token = ''): AccountInterface;

    /**
     * 绑定主账号
     * @param array $map 主账号条件
     * @param array $data 主账号资料
     * @return array
     */
    public function bind(array $map, array $data = []): array;

    /**
     * 解绑主账号
     * @return array
     */
    public function unbind(): array;

    /**
     * 刷新账号序号
     * @return array
     */
    public function recode(): array;

    /**
     * 检查是否有效
     * @return array
     * @throws \think\admin\Exception
     */
    public function check(): array;

    /**
     * 生成授权令牌
     * @param integer $unid
     * @return \plugin\account\service\contract\AccountInterface
     * @throws \think\db\exception\DbException
     */
    public function token(int $unid): AccountInterface;

    /**
     * 延期令牌时间
     * @return \plugin\account\service\contract\AccountInterface
     */
    public function expire(): AccountInterface;
}