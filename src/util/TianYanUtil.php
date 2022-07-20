<?php
declare (strict_types=1);

namespace ffhome\common\util;

/**
 * Class TianYanUtil
 * 天眼数据相关接口功能
 * https://www.tianyandata.cn/
 * @package ffhome\common\util
 */
class TianYanUtil
{
    const SUCCESS = 0;
    const BASE_URL = 'https://api.shumaidata.com/v4';
    /**
     * appid
     * @var string
     */
    private $appid;
    /**
     * appsecurity
     * @var string
     */
    private $appsecurity;

    public function __construct(string $appid, string $appsecurity)
    {
        $this->appid = $appid;
        $this->appsecurity = $appsecurity;
    }

    /**
     * 运营商三要素验证
     * https://www.tianyandata.cn/productDetail/20
     *
     * @param string $name 姓名
     * @param string $idcard 身份证号
     * @param string $mobile 手机号码
     * @return int  0 一致，收费  1 不一致，收费  2 无记录，不收费  3 访问失败
     * @throws \HttpException
     */
    public function mobileThree(string $name, string $idcard, string $mobile): int
    {
        $param = $this->getSign();
        $param = array_merge($param, [
            'idcard' => $idcard,
            'name' => $name,
            'mobile' => $mobile
        ]);
        $result = CommonUtil::http(self::BASE_URL . '/mobile_three/check', $param);
        if ($result['code'] == 200) {
            return intval($result['data']['result']);
        } else if ($result['code'] == 603) {
            throw new \HttpException('接口余额不足请联系客服');
        } else {
            throw new \HttpException($result['msg']);
        }
    }

    private function getSign()
    {
        //date_default_timezone_set("Asia/Shanghai");
        $timestamp = time() * 1000;
        return [
            'appid' => $this->appid,
            'timestamp' => $timestamp,
            'sign' => md5("{$this->appid}&{$timestamp}&{$this->appsecurity}")
        ];
    }
}