<?php
declare (strict_types=1);

namespace ffhome\tests\common\util;

use ffhome\common\util\IdCardUtil;
use PHPUnit\Framework\TestCase;

class IdCardUtilTest extends TestCase
{
    public function testGetBirthday()
    {
        $result = IdCardUtil::getBirthday('XXXXXXXXXXXXXXXXXX');
        $this->assertEquals('1993-09-26', $result);
    }

    public function testGetSex()
    {
        $result = IdCardUtil::getSex('XXXXXXXXXXXXXXXXXX');
        $this->assertEquals(1, $result);
    }
}