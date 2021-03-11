<?php

namespace ffhome\tests\common\util;

use ffhome\common\util\CommonUtil;
use PHPUnit\Framework\TestCase;

class CommonUtilTest extends TestCase
{
    public function testPassword()
    {
        $this->assertEquals('2c6c5a6b1a2429da237285b1e0b33eef', CommonUtil::password('password'));
        $this->assertEquals('88e349eede9223bd25ab30c2f0f3cf80', CommonUtil::password('123456'));
    }

    public function testGetTree()
    {
        $param = [
            ['id' => 1, 'pid' => 0, 'name' => '市场部'],
            ['id' => 2, 'pid' => 0, 'name' => '运营部'],
            ['id' => 3, 'pid' => 1, 'name' => '市场一部'],
            ['id' => 4, 'pid' => 1, 'name' => '市场二部'],
        ];
        $result = [
            ['id' => 1, 'pid' => 0, 'name' => '市场部', 'child' => [
                ['id' => 3, 'pid' => 1, 'name' => '市场一部', 'child' => []],
                ['id' => 4, 'pid' => 1, 'name' => '市场二部', 'child' => []]
            ]],
            ['id' => 2, 'pid' => 0, 'name' => '运营部', 'child' => []],
        ];
        $ret = CommonUtil::getTree($param);
        $this->assertCount(2, $ret);
        $this->assertEquals($result, $ret);
    }

    public function testConvertToArray()
    {
        $param = [
            ['id' => 1, 'pid' => 0, 'name' => '市场部'],
            ['id' => 2, 'pid' => 0, 'name' => '运营部'],
            ['id' => 3, 'pid' => 1, 'name' => '市场一部'],
            ['id' => 4, 'pid' => 1, 'name' => '市场二部'],
        ];
        $result = ['市场部', '运营部', '市场一部', '市场二部'];
        $ret = CommonUtil::convertToArray($param, 'name');
        $this->assertCount(4, $ret);
        $this->assertEquals($result, $ret);
    }

    public function testConvertToArray2()
    {
        $param = [
            ['id' => 1, 'pid' => 0, 'name' => '市场部'],
            ['id' => 2, 'pid' => 0, 'name' => '运营部'],
            ['id' => 3, 'pid' => 1, 'name' => '市场一部'],
            ['id' => 4, 'pid' => 1, 'name' => '市场二部'],
        ];
        $result = [1 => '市场部', 2 => '运营部', 3 => '市场一部', 4 => '市场二部'];
        $ret = CommonUtil::convertToArray($param, 'name', 'id');
        $this->assertCount(4, $ret);
        $this->assertEquals($result, $ret);
    }

    public function testGetPrefixArray()
    {
        $param = [
            'id' => 1,
            'name' => '测试',
            'user_id' => 1,
            'user_name' => '管理员',
            'user_mobile' => '1234567890'
        ];
        $result = [
            'id' => 1,
            'name' => '管理员',
            'mobile' => '1234567890'
        ];
        $ret = CommonUtil::getPrefixArray($param, 'user_');
        $this->assertEquals($result, $ret);
    }
}