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
     * @param boolean $isjwt
     * @return AccountInterface
     */
    public function init(string $token = '', bool $isjwt = true): AccountInterface;

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
    public function unBind(): array;

    /**
     * 判断绑定主账号
     * @return boolean
     */
    public function isBind(): bool;

    /**
     * 判断是否空账号
     * @return bool
     */
    public function isNull(): bool;

    /**
     * 获取关联终端
     * @return array
     */
    public function allBind(): array;

    /**
     * 解除终端关联
     * @param integer $usid 终端编号
     * @return array
     */
    public function delBind(int $usid): array;

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
     * @param integer $usid
     * @return \plugin\account\service\contract\AccountInterface
     * @throws \think\db\exception\DbException
     */
    public function token(int $usid): AccountInterface;

    /**
     * 延期令牌时间
     * @return \plugin\account\service\contract\AccountInterface
     */
    public function expire(): AccountInterface;
}