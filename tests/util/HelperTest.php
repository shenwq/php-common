<?php
declare (strict_types=1);

namespace ffhome\tests\common\util;

use ffhome\common\util\CommonUtil;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    public function testGetConstellation()
    {
        $this->assertEquals('白羊座', getConstellation(3, 21));
        $this->assertEquals('白羊座', getConstellation(4, 20));
        $this->assertEquals('金牛座', getConstellation(4, 21));
        $this->assertEquals('金牛座', getConstellation(5, 20));
        $this->assertEquals('双子座', getConstellation(5, 21));
        $this->assertEquals('双子座', getConstellation(6, 21));
        $this->assertEquals('宝瓶座', getConstellation(1, 21));
        $this->assertEquals('摩羯座', getConstellation(1, 20));
    }

    public function testGetBirthdayPet()
    {
        $this->assertEquals('狗', getBirthdayPet(1982));
        $this->assertEquals('虎', getBirthdayPet(2022));
    }
}