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

    /**
     * 人脸身份证比对
     * https://www.tianyandata.cn/productDetail/14
     *
     * @param string $name 姓名
     * @param string $idcard 身份证号
     * @param string $path 图片路径
     * @return array [ result:true表示对比成功，false表示对比失败,
     *                score:比较结果分值，0-1之间的小数，参考指标
     *                       只有 0.40以下 系统判断为不同人；
     *                       0.40-0.44 不能确定是否为同一人 ；
     *                       0.45及以上 系统判断为同一人]
     * @throws \HttpException
     */
    public function faceIdCardByImage(string $name, string $idcard, string $path): array
    {
        $base64 = $this->base64EncodeImage($path);
        $param = $this->getSign();
        $param = array_merge($param, [
            'idcard' => $idcard,
            'name' => $name,
            'image' => $base64
        ]);
        return $this->faceIdCard($param);
    }

    /**
     * 人脸身份证比对
     * https://www.tianyandata.cn/productDetail/14
     *
     * @param string $name 姓名
     * @param string $idcard 身份证号
     * @param string $url 图片url
     * @return array [ result:true表示对比成功，false表示对比失败,
     *                score:比较结果分值，0-1之间的小数，参考指标
     *                       只有 0.40以下 系统判断为不同人；
     *                       0.40-0.44 不能确定是否为同一人 ；
     *                       0.45及以上 系统判断为同一人]
     * @throws \HttpException
     */
    public function faceIdCardByUrl(string $name, string $idcard, string $url): array
    {
        $param = $this->getSign();
        $param = array_merge($param, [
            'idcard' => $idcard,
            'name' => $name,
            'url' => $url
        ]);
        return $this->faceIdCard($param);
    }

    private function faceIdCard(array $param): array
    {
        $result = CommonUtil::http(self::BASE_URL . '/face_id_card/compare', [], $param, 'POST');
        if ($result['code'] == 200) {
            return ['result' => ($result['data']['score'] > 0.45 && $result['data']['incorrect'] == 100), 'score' => $result['data']['score']];
        } else if ($result['code'] == 603) {
            throw new \HttpException('接口余额不足请联系客服');
        } else {
            throw new \HttpException($result['msg']);
        }
    }

    public function base64EncodeImage(string $image_file, bool $ifHeader = true)
    {
        $image_info = getimagesize($image_file);
        $file = fopen($image_file, 'r');
        $image_data = fread($file, filesize($image_file));
        fclose($file);
        if ($ifHeader) {
            $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        } else {
            $base64_image = chunk_split(base64_encode($image_data));
        }
        return $base64_image;
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