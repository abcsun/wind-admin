<?php


class HelloworldTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testExample()
    {
        $this->get('/');

        $this->assertEquals(
            $this->response->getContent(), $this->app->version()
        );
    }

    /**
     * helloworld接口测试.
     *
     * @return [type] [description]
     */
    public function testHelloworldExample()
    {
        $this->get('/helloworld')
                ->seeJson([
                    'msg' => 'helloworld',
                ]);
    }
}
