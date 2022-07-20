<?php
declare (strict_types=1);

namespace ffhome\tests\common\util;

use ffhome\common\util\TianYanUtil;
use PHPUnit\Framework\TestCase;

class TianYanUtilTest extends TestCase
{
    private $util;

    protected function setUp()
    {
        $this->util = new TianYanUtil('appid', 'appsecurity');
    }

    public function testMobileThree()
    {
        $result = $this->util->mobileThree('姓名', '身份证号', '手机号码');
        $this->assertEquals(0, $result);
    }

    public function testFaceIdCardByImage()
    {
        $result = $this->util->faceIdCardByImage('姓名', '身份证号', '图片路径');
        $this->assertTrue($result['result']);
    }

    public function testFaceIdCardByUrl()
    {
        $result = $this->util->faceIdCardByUrl('姓名', '身份证号', 'https://ffhome.top/xxx.png');
        $this->assertTrue($result['result']);
    }

    public function testBase64EncodeImage()
    {
        $result = $this->util->base64EncodeImage('d:/11.png');
        $this->assertEquals(0, $result);
    }
}