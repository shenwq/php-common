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
        $info = $this->api->getShopDetail('shop1');
        print_r($info);
        $this->assertEquals(0, $info['code']);
    }

    public function testAddShops()
    {
        $data = [[
            'station_name' => '义起火烧 一起行',
            'business' => 29,
            'station_address' => '江苏省常州市天宁区大润发（永宁店）二楼',
            'lng' => 119.974174,
            'lat' => 31.792744,
            'contact_name' => 'XXX',
            'phone' => 'XXXXXXXXXXX',
            'origin_shop_id' => 'shop1',
        ]];
        $info = $this->api->addShops($data);
        print_r($info);
        $this->assertEquals(0, $info['code']);
    }

    public function testUpdateShop()
    {
        $data = [
            'origin_shop_id' => 'shop1',
            'station_name' => '义起火烧一起行',
        ];
        $info = $this->api->updateShop($data);
        print_r($info);
        $this->assertEquals(0, $info['code']);
    }
}