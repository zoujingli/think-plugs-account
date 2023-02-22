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

namespace plugin\account\service\contract;

/**
 * 用户账号接口类
 */
interface AccountInterface
{
    /**
     * 读取账号资料
     * @param boolean $rejwt
     * @return array
     */
    public function get(bool $rejwt = false): array;

    /**
     * 设置账号资料
     * @param array $data 用户资料
     * @param boolean $rejwt
     * @return array
     */
    public function set(array $data = [], bool $rejwt = false): array;

    /**
     * 初始化账号通道
     * @param string $token
     * @return \plugin\account\service\contract\AccountInterface
     */
    public function init(string $token = ''): AccountInterface;

    /**
     * 绑定用户资料
     * @param array $map
     * @param array $data
     * @return array
     */
    public function bind(array $map, array $data = []): array;

    /**
     * 解绑用户主资料
     * @return mixed
     */
    public function unbind(): array;

    /**
     * 检查令牌是否有效
     * @return array
     * @throws \think\admin\Exception
     */
    public function check(): array;

    /**
     * 生成新的用户令牌
     * @param integer $unid
     * @return \plugin\account\service\contract\AccountInterface
     * @throws \think\db\exception\DbException
     */
    public function token(int $unid): AccountInterface;

    /**
     * 延期令牌的有效时间
     * @return \plugin\account\service\contract\AccountInterface
     */
    public function expire(): AccountInterface;
}