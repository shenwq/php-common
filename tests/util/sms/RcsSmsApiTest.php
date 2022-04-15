<?php
declare (strict_types=1);

namespace ffhome\tests\common\util\sms;

use ffhome\common\util\sms\RcsSmsApi;
use PHPUnit\Framework\TestCase;

/**
 * 美圣融云平台 Http Demo for PHP
 * 参考开发帮助文档 http://www.rcscloud.cn/common/api
 * 公司：江苏美圣信息技术有限公司
 * 电话：4006000599
 * Class RcsSmsApiTest
 * @package ffhome\tests\common\util\sms
 */
class RcsSmsApiTest extends TestCase
{
    protected $api;

    protected function setUp()
    {
        $this->api = new RcsSmsApi('account', 'apiKey');
    }

    public function testQueryUser()
    {
        $info = $this->api->queryUser();
        print_r($info);
        $this->assertEquals(0, $info['code']);
    }

    public function testQueryTemplates()
    {
        $info = $this->api->queryTemplates();
        print_r($info);
        $this->assertEquals(0, $info['code']);
    }

    public function testQueryTemplate()
    {
        $info = $this->api->queryTemplate('22c6856a405044fb8c46b5383092d10d');
        print_r($info);
        $this->assertEquals(0, $info['code']);
    }

    public function testQueryRpt()
    {
        $info = $this->api->queryRpt();
        print_r($info);
        $this->assertEquals(0, $info['code']);
    }

    public function testQueryMo()
    {
        $info = $this->api->queryMo();
        print_r($info);
        $this->assertEquals(0, $info['code']);
    }

    public function testValidBlackList()
    {
        $info = $this->api->validBlackList('18951234816');
        print_r($info);
        $this->assertEquals(0, $info['code']);
    }

    public function testValidSensitiveWord()
    {
        $info = $this->api->validSensitiveWord('毛泽东');
        print_r($info);
        $this->assertEquals(0, $info['code']);
        $this->assertEquals('毛泽东', $info['words']);
        $info = $this->api->validSensitiveWord('测试');
        print_r($info);
        $this->assertEquals(0, $info['code']);
        $this->assertEquals('', $info['words']);
    }

    public function testSend()
    {
        $info = $this->api->send('665c43a0fc544d73b4336ccbc412d8b4', '18951234816', '@1@=1793');
        print_r($info);
        $this->assertEquals(0, $info['code']);
    }
}