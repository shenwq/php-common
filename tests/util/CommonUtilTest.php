<?php
declare (strict_types=1);

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

    public function testFormatText()
    {
        $this->assertEquals('我是John,年龄:32', CommonUtil::formatText('我是{$name},年龄:{$age}', ['name' => 'John', 'age' => 32]));
    }

    public function testMobileBlur()
    {
        $this->assertEquals('189*****816', CommonUtil::mobileBlur('18951234816'));
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

    public function testHttp()
    {
        $ret = CommonUtil::http('https://ip.ffhome.top/ip/49.72.197.2');
        $this->assertEquals('江苏', $ret['region']);
        $this->assertEquals('苏州', $ret['city']);
        print_r($ret);
    }

    public function testImageAddBaseUrl()
    {
        $html = '<p>';
        $html .= '<img alt="" src="/upload/20221207/001.jpg" />';
        $html .= '<img alt="" src="/upload/20221207/002.jpg" style="height:1367px; width:900px" />';
        $html .= '</p>';

        $expected = '<p>';
        $expected .= '<img alt="" src="http://test.ffhome.top/upload/20221207/001.jpg" />';
        $expected .= '<img alt="" src="http://test.ffhome.top/upload/20221207/002.jpg" style="height:1367px; width:900px" />';
        $expected .= '</p>';

        $ret = CommonUtil::imageAddBaseUrl($html, 'http://test.ffhome.top');
        $this->assertEquals($expected, $ret);
    }

    public function testModifyImageWidth()
    {
        $html = '<p>';
        $html .= '<img alt="" src="/upload/20221207/001.jpg" style="height:1367px; width:900px" />';
        $html .= '<img alt="" src="/upload/20221207/002.jpg" style="height:1367px; width:900px" />';
        $html .= '</p>';

        $expected = '<p>';
        $expected .= '<img alt="" src="/upload/20221207/001.jpg" width="100%" />';
        $expected .= '<img alt="" src="/upload/20221207/002.jpg" width="100%" />';
        $expected .= '</p>';

        $ret = CommonUtil::modifyImageWidth($html);
        $this->assertEquals($expected, $ret);
    }

    public function testSync()
    {
        for ($i = 1; $i <= 5; $i++) {
            CommonUtil::sync(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', function () use ($i) {
                echo $i . "\n";
                sleep(1);
            });
        }
        $this->assertTrue(true);
    }
}