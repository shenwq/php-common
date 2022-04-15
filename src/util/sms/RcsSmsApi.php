<?php
declare (strict_types=1);

namespace ffhome\common\util\sms;

class RcsSmsApi
{
    /**
     * @var string 短信账号，从会员中心里获取
     */
    private $sid;
    /**
     * @var string APIKEY，从会员中心里获取
     */
    private $apiKey;
    /**
     * @var string rest请求地址
     */
    private $baseUrl;

    /**
     * RcsSmsApi constructor.
     * @param string $id 短信账号
     * @param string $apiKey apiKey
     * @param bool $isTest 是否是测试端口
     */
    public function __construct(string $id, string $apiKey, bool $isTest = false)
    {
        $this->sid = $id;
        $this->apiKey = $apiKey;
        //测试端口80，正式端口8030
        $this->baseUrl = 'http://api.rcscloud.cn:' . ($isTest ? 80 : 8030) . '/rcsapi/rest';
    }

    /**
     * 账号接口 信息获取
     * @return array
     */
    public function queryUser()
    {
        return self::get("/user/get.json?sid={$this->sid}");
    }

    /**
     * 查询账号所有模板
     * @return array
     */
    public function queryTemplates()
    {
        return self::get("/tpl/gets.json?sid={$this->sid}");
    }

    /**
     * 查询单个模板
     * @param $tplId 模板ID
     * @return array
     */
    public function queryTemplate($tplId)
    {
        return self::get("/tpl/get.json?sid={$this->sid}&tplid={$tplId}", $tplId);
    }

    /**
     * 状态接口 信息获取
     * @return array
     */
    public function queryRpt()
    {
        return self::get("/sms/queryrpt.json?sid={$this->sid}");
    }

    /**
     * 上行接口 信息获取
     * @return array
     */
    public function queryMo()
    {
        return self::get("/sms/querymo.json?sid={$this->sid}");
    }

    /**
     * 检测黑名单
     * @param $mobile 手机号码
     * @return array
     */
    public function validBlackList($mobile)
    {
        return self::get("/assist/bl.json?sid={$this->sid}&mobile={$mobile}");
    }

    /**
     * 检测敏感词
     * @param $content 敏感词
     * @return array
     */
    public function validSensitiveWord($content)
    {
        $content = urlencode($content);
        return self::get("/assist/sw.json?sid={$this->sid}&content={$content}");
    }

    /**
     * 发送短信
     * @param $tplId 模板ID
     * @param $mobile 手机号码，只支持一个11位的手机号
     * @param $content 参数值，多个参数以“||”隔开 如:@1@=HY001||@2@=3281
     * @param string $extno 自定义扩展码，建议1-4位,需此功能请联系客服申请开通，取值范围:0-9999
     * @return array
     */
    public function send($tplId, $mobile, $content, $extno = "")
    {
        // 签名认证 Md5(sid+apikey+tplid+mobile+content)
        $sign = md5($this->sid . $this->apiKey . $tplId . $mobile . $content);
        $url = $this->baseUrl . "/sms/sendtplsms.json"; // 服务器接口路径

        // POST方式提交服务器
        $data = ['sign' => $sign, 'sid' => $this->sid, 'tplid' => $tplId, 'mobile' => $mobile, 'content' => $content, 'extno' => $extno];
        $res = $this->post($url, $data);
        return json_decode($res, true);
    }

    private function get($url, $param1 = '')
    {
        // 签名认证 Md5(sid+apikey)
        $sign = md5($this->sid . $this->apiKey . $param1);
        // 服务器接口路径
        $url = "{$this->baseUrl}{$url}&sign={$sign}";
        // 获取信息
        return json_decode(file_get_contents($url), true);
    }

    private function post(string $url, array $data)
    {
        if (empty($url) || empty($data)) {
            return ['code' => -1, 'msg' => '参数错误'];
        }

        $o = '';
        foreach ($data as $k => $v) {
            $o .= $k . '=' . urlencode($v) . '&';
        }
        $data = substr($o, 0, -1);

        $postUrl = $url;
        $curlPost = $data;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', 'Content-Encoding: utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        return $data;
    }
}