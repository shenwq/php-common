<?php
declare (strict_types=1);

namespace ffhome\tests\common\util\deliver;

use ffhome\common\util\deliver\DaDa;
use PHPUnit\Framework\TestCase;
use function foo\func;


class DaDaTest extends TestCase
{
    protected $api;

    protected function setUp()
    {
        $this->api = new DaDa('XXXXXXXX', 'XXXXXXXXXXXXX', 'XXXXXXXXXXXXXXXXXX', true);
    }

    public function testCheckSignatureOK()
    {
        $ret = DaDa::checkSignature([
            'client_id' => '1464707142796312576',
            'order_id' => '100015',
            'update_time' => 1679552742,
            'signature' => 'bce7fc5b30227433f87001e6a82c81f8'
        ], function () {
            return DaDa::SUCCESS;
        });
        $this->assertEquals(DaDa::SUCCESS, $ret);
    }

    public function testCheckSignatureFail()
    {
        $ret = DaDa::checkSignature([
            'client_id' => '1464707142796312576',
            'order_id' => '100015',
            'update_time' => 1679552742,
            'signature' => 'bce7fc5b30227433f87001e6a82c8f18'
        ], function () {
            return DaDa::SUCCESS;
        });
        $this->assertEquals(DaDa::FAIL_CHECK_SIGNATURE, $ret);
    }

    public function testListCityCode()
    {
        $info = $this->api->listCityCode();
        echo count($info['result']);
        $this->assertEquals(DaDa::SUCCESS, $info['code']);
    }

    public function testGetShopDetail()
    {
        $info = $this->api->getShopDetail('shop1');
        print_r($info);
        $this->assertEquals(DaDa::SUCCESS, $info['code']);
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
        $this->assertEquals(DaDa::SUCCESS, $info['code']);
    }

    public function testUpdateShop()
    {
        $data = [
            'origin_shop_id' => 'shop1',
            'station_name' => '义起火烧一起行',
        ];
        $info = $this->api->updateShop($data);
        print_r($info);
        $this->assertEquals(DaDa::SUCCESS, $info['code']);
    }

    public function testQueryDeliverFee()
    {
        $data = [
            'shop_no' => 'shop1',
            'origin_id' => '100001',
            'cargo_price' => 65.5,
            'is_prepay' => 0,
            'receiver_name' => '沈先生',
            'receiver_phone' => '18951234816',
            'receiver_address' => '江苏省常州市天宁区翠竹新村119幢甲单元102',
            'callback' => 'https://mall-api.XXXXX.cn/leaguer/callback/dada',
            'cargo_weight' => 1,
            'receiver_lat' => 31.79,
            'receiver_lng' => 119.98,
        ];
        $info = $this->api->queryDeliverFee($data);
        print_r($info);
        $this->assertEquals(DaDa::SUCCESS, $info['code']);
    }

    public function testAddOrderAfterQuery()
    {
        $info = $this->api->addOrderAfterQuery('Dada55ec3a322f4b4dd3b7e74359dc400e83');
        print_r($info);
        $this->assertEquals(DaDa::SUCCESS, $info['code']);
    }

    public function testAddOrder()
    {
        $data = [
            'shop_no' => 'shop1',
            'origin_id' => '100001',
            'cargo_price' => 65.5,
            'is_prepay' => 0,
            'receiver_name' => '沈先生',
            'receiver_phone' => '18951234816',
            'receiver_address' => '江苏省常州市天宁区翠竹新村119幢甲单元102',
            'callback' => 'https://mall-api.XXXXX.cn/leaguer/callback/dada',
            'cargo_weight' => 1,
            'receiver_lat' => 31.79,
            'receiver_lng' => 119.98,
        ];
        $info = $this->api->addOrder($data);
        print_r($info);
        $this->assertEquals(DaDa::SUCCESS, $info['code']);
    }

    public function testGetOrderDetail()
    {
        $info = $this->api->getOrderDetail(100012);
        print_r($info);
        $this->assertEquals(DaDa::SUCCESS, $info['code']);
    }
}