<?php
declare (strict_types=1);

namespace ffhome\common\util\deliver;

/**
 * 达达快递接口
 * Class DaDa
 * @package ffhome\common\util\deliver
 */
class DaDa
{
    /**
     * 成功返回码
     */
    const SUCCESS = 0;
    /**
     * 签名检测失败
     */
    const FAIL_CHECK_SIGNATURE = -2;

    /**
     * 快递状态：未使用快递
     */
    const S_UN_USE = -1;
    /**
     * 快递状态：待接单
     */
    const S_WAITING_RECEIVE_ORDER = 1;
    /**
     * 快递状态：待取货
     */
    const S_WAITING_TAKE_GOODS = 2;
    /**
     * 快递状态：配送中
     */
    const S_SENDING = 3;
    /**
     * 快递状态：已完成
     */
    const S_COMPLETED = 4;
    /**
     * 快递状态：订单取消
     */
    const S_CANCELED = 5;
    /**
     * 快递状态：投递异常，返还中
     */
    const S_BACKING = 9;
    /**
     * 快递状态：返还完成
     */
    const S_BACK = 10;
    /**
     * 快递状态：已到店
     */
    const S_ARRIVE_SHOP = 100;

    private $appKey;
    private $appSecret;
    /**
     * @var string 商户SourceID
     */
    private $sourceId;
    /**
     * @var string rest请求地址
     */
    private $baseUrl;

    /**
     * DaDa constructor.
     * @param string $sourceId 商户SourceID
     * @param string $appKey
     * @param string $appSecret
     * @param bool $isTest 是否是测试端口
     */
    public function __construct(string $sourceId, string $appKey, string $appSecret, bool $isTest = false)
    {
        $this->sourceId = $sourceId;
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->baseUrl = $isTest ? 'https://newopen.qa.imdada.cn' : 'https://newopen.imdada.cn';
    }

    /**
     * 回调方法中检查签名是否正确，正确就调用fn方法
     * @param $param
     * @param \Closure $fn
     * @return mixed fn方法的返回值
     */
    public static function checkSignature($param, \Closure $fn)
    {
        $arr = [$param['client_id'], $param['order_id'], $param['update_time']];
        sort($arr, SORT_STRING);
        if (md5(join('', $arr)) == $param['signature']) {
            return $fn();
        }
        return self::FAIL_CHECK_SIGNATURE;
    }

    /**
     * 查询城市码
     * @return mixed
     */
    public function listCityCode()
    {
        return self::post('/api/cityCode/list', []);
    }

    /**
     * 查询门店详情
     * @param $shopId
     * @return mixed 门店编码
     */
    public function getShopDetail($shopId)
    {
        return self::post('/api/shop/detail', ['origin_shop_id' => $shopId]);
    }

    /**
     * 创建门店
     * @param array $data
     * @return mixed
     */
    public function addShops(array $data)
    {
        return self::post('/api/shop/add', $data);
    }

    /**
     * 更新门店
     * @param array $data
     * @return mixed
     */
    public function updateShop(array $data)
    {
        return self::post('/api/shop/update', $data);
    }

    /**
     * 查询运费
     * @param array $data
     * @return mixed
     */
    public function queryDeliverFee(array $data)
    {
        return self::post('/api/order/queryDeliverFee', $data);
    }

    /**
     * 直接下单
     * @param array $data
     * @return mixed
     */
    public function addOrder(array $data)
    {
        return self::post('/api/order/addOrder', $data);
    }

    /**
     * 查询运费后下单
     * @param string $deliveryNo 平台订单编号
     * @return mixed
     */
    public function addOrderAfterQuery(string $deliveryNo)
    {
        return self::post('/api/order/addAfterQuery', ['deliveryNo' => $deliveryNo]);
    }

    /**
     * 查询订单明细
     * @param $orderId
     * @return mixed
     */
    public function getOrderDetail($orderId)
    {
        return self::post('/api/order/status/query', ['order_id' => $orderId]);
    }

    private function post(string $url, array $body)
    {
        $data = [
            'app_key' => $this->appKey,
            'timestamp' => strval(time()),
            'format' => 'json',
            'v' => '1.0',
            'source_id' => $this->sourceId,
            'body' => empty($body) ? '{}' : json_encode($body, JSON_UNESCAPED_SLASHES),
        ];
        ksort($data);
        $sign = $this->appSecret;
        foreach ($data as $key => $val) {
            $sign .= "{$key}{$val}";
        }
        $sign .= $this->appSecret;
        $sign = strtoupper(md5($sign));
        $data['signature'] = $sign;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$this->baseUrl}{$url}");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_SLASHES));
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            return ['code' => -2, 'msg' => '请求发生错误：' . $error];
        }
        $data = json_decode($data, true);
        return $data;
    }
}