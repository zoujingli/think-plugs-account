<?php

namespace think\admin\tests;

use PHPUnit\Framework\TestCase;
use plugin\account\service\Account;

class AccountTest extends TestCase
{

    public function testAddType()
    {
        Account::add('test', '测试接口');
        $this->assertIsString(Account::field('test'));
    }

    public function testGetTypes()
    {
        $info = Account::types();
        $this->assertIsArray($info);
    }

    public function testChangeType()
    {
        $field = Account::field('web');
        $this->assertNotEmpty($field);

        Account::set('web', 0);

        $field = Account::field('web');
        $this->assertEmpty($field);

        try {
            Account::mk('web');
        } catch (\think\admin\Exception $exception) {
            $this->assertStringContainsString('未定义', $exception->getMessage());
        }
    }

    public function testAddAccount()
    {
        $account = Account::mk(Account::WAP);
        $info = $account->set(['phone' => '138888888888', 'nickname' => '账号创建测试'], true);
        $this->assertEquals($info['id'], $account->get()['id'], '创建用户测试成功！');
    }

    public function testBindAccount()
    {
        $username = 'UserName' . uniqid();
        $account = Account::mk(Account::WAP);
        $phone = '13888888' . mt_rand(1000, 9999);
        $account->set(['phone' => $phone]);

        // 关联绑定主账号
        $info = $account->bind(['phone' => $phone], ['username' => $username]);
        $this->assertEquals($info['user']['username'], $username, '账号绑定关联成功！');

        // 刷新主账号序号
        $news = $account->recode();
        $this->assertNotEquals($info['user']['code'], $news['user']['code'], '刷新用户序号成功');
    }

    public function testUnbindAccount()
    {
        $account = Account::mk(Account::WAP);
        $account->set(['phone' => '138888888888']);

        // 关联绑定主账号
        $info = $account->bind(['phone' => '138888888888'], ['username' => 'UserName' . uniqid()]);
        $this->assertNotEmpty($info['user'], '账号绑定成功！');

        $info = $account->unBind();
        $this->assertEmpty($info['user'], '账号解绑成功！');
    }
}