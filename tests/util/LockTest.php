<?php
declare (strict_types=1);

namespace ffhome\tests\common\util;

use ffhome\common\util\CommonUtil;
use ffhome\common\util\Lock;
use PHPUnit\Framework\TestCase;

class LockTest extends TestCase
{
    public function testSync()
    {
        for ($i = 1; $i <= 5; $i++) {
            $lock = new Lock(__DIR__ . DIRECTORY_SEPARATOR . 'test.txt', false);
            echo "{$i}\n";
            sleep(1);
            $lock->unlock();
        }
        $this->assertTrue(true);
    }
}