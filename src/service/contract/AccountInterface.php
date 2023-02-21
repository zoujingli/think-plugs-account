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
     * @return array
     */
    public function get(): array;

    /**
     * 设置账号资料
     * @param array $data 用户资料
     * @return array
     */
    public function set(array $data = []): array;

    /**
     * 初始化账号通道
     * @param string $token
     * @return \plugin\account\service\contract\AccountInterface
     */
    public function init(string $token = ''): AccountInterface;

    /**
     * 检查令牌是否有效
     * @param string $token
     * @return array
     * @throws \think\admin\Exception
     */
    public function check(string $token): array;

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