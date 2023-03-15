<?php
declare (strict_types=1);

namespace ffhome\common\util\deliver;

use ffhome\common\util\CommonUtil;

/**
 * 达达快递接口
 * Class DaDa
 * @package ffhome\common\util\deliver
 */
class DaDa
{
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
     * 查询城市码
     * @return mixed
     * @throws \Exception
     */
    public function listCityCode()
    {
        return self::post('/api/cityCode/list', []);
    }

    /**
     * 查询门店详情
     * @param $shopId
     * @return mixed 门店编码
     * @throws \Exception
     */
    public function getShopDetail($shopId)
    {
        return self::post('/api/shop/detail', ['origin_shop_id' => $shopId]);
    }

    /**
     * 创建门店
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function addShops(array $data)
    {
        return self::post('/api/shop/add', $data);
    }

    /**
     * 更新门店
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function updateShop(array $data)
    {
        return self::post('/api/shop/update', $data);
    }

    /**
     * 查询运费
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function queryDeliverFee(array $data)
    {
        return self::post('/api/order/queryDeliverFee', $data);
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
        if ($error) throw new \Exception('请求发生错误：' . $error);
        $data = json_decode($data, true);
        return $data;
    }
}