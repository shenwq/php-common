<?php
declare (strict_types=1);

namespace ffhome\tests\common\util;

use ffhome\common\util\DateUtil;
use PHPUnit\Framework\TestCase;

class DateUtilTest extends TestCase
{
    public function testGetConstellation()
    {
        $this->assertEquals('白羊座', DateUtil::getConstellation(3, 21));
        $this->assertEquals('白羊座', DateUtil::getConstellation(4, 20));
        $this->assertEquals('金牛座', DateUtil::getConstellation(4, 21));
        $this->assertEquals('金牛座', DateUtil::getConstellation(5, 20));
        $this->assertEquals('双子座', DateUtil::getConstellation(5, 21));
        $this->assertEquals('双子座', DateUtil::getConstellation(6, 21));
        $this->assertEquals('宝瓶座', DateUtil::getConstellation(1, 21));
        $this->assertEquals('摩羯座', DateUtil::getConstellation(1, 20));
    }

    public function testGetBirthdayPet()
    {
        $this->assertEquals('狗', DateUtil::getBirthdayPet(1982));
        $this->assertEquals('虎', DateUtil::getBirthdayPet(2022));
    }

    public function testTimeFormat()
    {
        $this->assertEquals('', DateUtil::timeFormat(''));
        $this->assertEquals('12:00', DateUtil::timeFormat('12:00:00'));
    }

    public function testWeek()
    {
        $this->assertEquals('', DateUtil::week(''));
        $this->assertEquals('日', DateUtil::week('2023-10-1'));
        $this->assertEquals('三', DateUtil::week('2023-11-1'));
        $this->assertEquals('五', DateUtil::week('2023-12-1'));
    }
}