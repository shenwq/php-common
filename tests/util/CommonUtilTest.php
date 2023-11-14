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

    public function testGetOsName()
    {
        $this->assertEquals('ios', CommonUtil::getOsName('Mozilla/5.0 (iPad; CPU OS 9_3_5 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13G36 Safari/601.1'));
        $this->assertEquals('ios', CommonUtil::getOsName('Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1'));
        $this->assertEquals('android', CommonUtil::getOsName('Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.141 Mobile Safari/537.36'));
        $this->assertEquals('other', CommonUtil::getOsName('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36'));
    }

    public function testGetBrowserName()
    {
        $this->assertEquals('weixin', CommonUtil::getBrowserName('Mozilla/5.0 (Linux; Android 13; 22041216C Build/TP1A.220624.014; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/107.0.5304.141 Mobile Safari/537.36 XWEB/5023 MMWEBSDK/20230202 MMWEBID/3361 MicroMessenger/8.0.33.2320(0x28002151) WeChat/arm64 Weixin NetType/WIFI Language/zh_CN ABI/arm64'));
        $this->assertEquals('safari', CommonUtil::getBrowserName('Mozilla/5.0 (iPad; CPU OS 9_3_5 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13G36 Safari/601.1'));
        $this->assertEquals('safari', CommonUtil::getBrowserName('Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1'));
        $this->assertEquals('chrome', CommonUtil::getBrowserName('Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.141 Mobile Safari/537.36'));
        $this->assertEquals('chrome', CommonUtil::getBrowserName('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36'));
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

    public function testConvertToMap()
    {
        $param = [
            ['id' => 1, 'pid' => 0, 'name' => '市场部'],
            ['id' => 2, 'pid' => 0, 'name' => '运营部'],
            ['id' => 3, 'pid' => 1, 'name' => '市场一部'],
            ['id' => 4, 'pid' => 1, 'name' => '市场二部'],
        ];
        $result = [0 => [['id' => 1, 'pid' => 0, 'name' => '市场部'],
            ['id' => 2, 'pid' => 0, 'name' => '运营部'],],
            1 => [['id' => 3, 'pid' => 1, 'name' => '市场一部'],
                ['id' => 4, 'pid' => 1, 'name' => '市场二部'],]];
        $ret = CommonUtil::convertToMap($param, 'pid');
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
            echo CommonUtil::sync(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', function () use ($i) {
                echo "{$i}\n";
                sleep(1);
                return "return {$i}\n";
            });
        }
        $this->assertTrue(true);
    }
}