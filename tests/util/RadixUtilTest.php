<?php
declare (strict_types=1);

namespace ffhome\tests\common\util;

use ffhome\common\util\RadixUtil;
use PHPUnit\Framework\TestCase;

class RadixUtilTest extends TestCase
{
    public function testConvert10to62()
    {
        $this->assertEquals('q0U', RadixUtil::convert10to62(100000));
    }

    public function testConvert62to10()
    {
        $this->assertEquals(100000, RadixUtil::convert62to10('q0U'));
    }

    public function testConvert10to36()
    {
        $this->assertEquals('255s', RadixUtil::convert10to36(100000));
    }

    public function testConvert36to10()
    {
        $this->assertEquals(100000, RadixUtil::convert36to10('255s'));
    }
}