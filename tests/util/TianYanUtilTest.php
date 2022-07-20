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
}