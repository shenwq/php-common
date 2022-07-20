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
     * @return array  [result:true表示对比成功，false表示对比失败,msg:错误信息]
     */
    public function mobileThree(string $name, string $idcard, string $mobile): array
    {
        $param = $this->getSign();
        $param = array_merge($param, [
            'idcard' => $idcard,
            'name' => $name,
            'mobile' => $mobile
        ]);
        $result = CommonUtil::http(self::BASE_URL . '/mobile_three/check', $param);
        if ($result['code'] == 200) {
            $ret = intval($result['data']['result']);
            switch ($ret) {
                case 0:
                    return ['result' => true, 'msg' => '一致，收费'];
                case 1:
                    return ['result' => false, 'msg' => '不一致，收费'];
                case 2:
                    return ['result' => false, 'msg' => '无记录，不收费'];
            }
        } else if ($result['code'] == 603) {
            return ['result' => false, 'msg' => '接口余额不足请联系客服'];
        } else {
            return ['result' => false, 'msg' => $result['msg']];
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
     *                       0.45及以上 系统判断为同一人
     *                msg:错误消息]
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
            return ['result' => ($result['data']['score'] > 0.45 && $result['data']['incorrect'] == 100),
                'score' => $result['data']['score'],
                'msg' => ''];
        } else if ($result['code'] == 603) {
            return ['result' => false, 'score' => 0, 'msg' => '接口余额不足请联系客服'];
        } else {
            return ['result' => false, 'score' => 0, 'msg' => $result['msg']];
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