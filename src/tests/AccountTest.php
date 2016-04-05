<?php


class AccountTest extends TestCase
{
    /**
     * login.
     */
    public function testLogin()
    {
        $this->seeInDatabase('user', ['phone' => '18700000000']);

        $this->json('POST', '/api/v1/account/login', ['phone' => '18700000000', 'password' => '111111'])
             ->seeJson([
                 'code' => 1,
                 'message' => '登录成功',
             ]);
    }

    public function testDefaultUser()
    {
        $user = factory('Wind\Models\UserModel')->create();

        $this->json('POST', '/api/v1/account/login', ['phone' => $user->phone, 'password' => '111111'])
             ->seeJson([
                 'code' => 1,
                 'message' => '登录成功',
             ]);
    }
}
