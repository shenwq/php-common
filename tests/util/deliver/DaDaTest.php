<?php
declare (strict_types=1);

namespace ffhome\tests\common\util\deliver;

use ffhome\common\util\deliver\DaDa;
use PHPUnit\Framework\TestCase;


class DaDaTest extends TestCase
{
    protected $api;

    protected function setUp()
    {
        $this->api = new DaDa('XXXXXXXX', 'XXXXXXXXXXXXX', 'XXXXXXXXXXXXXXXXXX', true);
    }

    public function testListCityCode()
    {
        $info = $this->api->listCityCode();
        echo count($info['result']);
        $this->assertEquals(0, $info['code']);
    }

    public function testGetShopDetail()
    {
        $info = $this->api->getShopDetail('77accb60d74c4a89');
        print_r($info);
        $this->assertEquals(0, $info['code']);
    }
}