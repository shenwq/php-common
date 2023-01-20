<?php
declare (strict_types=1);

namespace ffhome\common\util;

class CommonUtil
{
    /**
     * 密码加密算法
     * @param string $value 需要加密的值
     * @return string
     */
    public static function password(string $value): string
    {
        $value = 'ffhome_' . md5($value) . '_encrypt' . sha1($value);
        return md5($value);
    }

    /**
     * 将str的参数使用data的数据进行替换
     * @param $str string
     * @param $data array
     * @return string
     */
    public static function formatText(string $str, array $data): string
    {
        foreach ($data as $key => $v) {
            $$key = $v;
        }
        return eval('return "' . $str . '";');
    }

    /**
     * 手机号码模糊处理
     * @param string $mobile 手机号码
     * @return string 模糊的手机号码
     */
    public static function mobileBlur(string $mobile): string
    {
        return substr($mobile, 0, 3) . '*****' . substr($mobile, -3, 3);
    }

    /**
     * 将list转换成树形结构
     * @param array $list
     * @param int $rootId 根id
     * @param string $keyName 主键名称
     * @param string $parentKeyName 父键名称
     * @param string $childName 子键名称
     * @return array
     */
    public static function getTree(array $list, int $rootId = 0, string $keyName = 'id', string $parentKeyName = 'pid'
        , string $childName = 'child'): array
    {
        $root = [$keyName => $rootId];
        self::buildTree($list, $root, $keyName, $parentKeyName, $childName);
        return $root[$childName];
    }

    private static function buildTree(array $list, array &$node, string $keyName, string $parentKeyName
        , string $childName): void
    {
        $child = [];
        foreach ($list as $v) {
            if ($v[$parentKeyName] == $node[$keyName]) {
                $child[] = $v;
            }
        }
        foreach ($child as &$v) {
            self::buildTree($list, $v, $keyName, $parentKeyName, $childName);
        }
        $node[$childName] = $child;
    }

    /**
     * 将list的数组进行转换
     * @param array $list 数组
     * @param string $val 值的键名
     * @param string $key key的键名
     * @return array
     */
    public static function convertToArray(array $list, string $val, string $key = ''): array
    {
        if (empty($list) || empty($val)) {
            return [];
        }
        $ret = [];
        foreach ($list as $vo) {
            if (empty($key)) {
                $ret[] = $vo[$val];
            } else {
                $ret[$vo[$key]] = $vo[$val];
            }
        }
        return $ret;
    }

    /**
     * 将list指定的前缀转换成单独的数组
     * @param array $list 数组
     * @param string $prefix 前缀名称
     * @return array
     */
    public static function getPrefixArray(array $list, string $prefix): array
    {
        if (empty($list)) {
            return [];
        }
        $ret = [];
        $len = strlen($prefix);
        foreach ($list as $key => $vo) {
            $idx = stripos($key, $prefix);
            if ($idx === 0) {
                $ret[substr($key, $len)] = $vo;
            }
        }
        return $ret;
    }

    /**
     * 获取真实IP
     * @return string
     */
    public static function getRealIp(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] as $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        }
        return $ip;
    }

    /**
     * 获取当前URL的主机地址(协议+主机+端口)
     * @return string
     */
    public static function getHost(): string
    {
        $sys_protocol = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        return $sys_protocol . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
    }

    /**
     * 获取当前页面完整URL地址
     * @return string
     */
    public static function getUrl(): string
    {
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
        return self::getHost() . $relate_url;
    }

    /**
     * HTML的文本中图片链接增加域名
     * @param string $html 原始html
     * @param string $baseUrl 域名
     * @return string 替换后的html
     */
    public static function imageAddBaseUrl(string $html, string $baseUrl): string
    {
        $preg = '#(src|href|url)(=[\'|\"]|\()((?!http:\/\/)[\/]?(.*?)\.(jpg|png|gif|jpeg))([\'|\"]|\))#i';
        return preg_replace($preg, "$1$2" . $baseUrl . "$3$6", $html);
    }

    /**
     * HTML的文本中图片修改宽度
     * @param string $html 原始html
     * @param string $replaced 替换后的结果，可参照默认参数
     * @return string 替换后的html
     */
    public static function modifyImageWidth(string $html, string $replaced = '$1width="100%"'): string
    {
        return preg_replace(
            [
                '/(<img [^<>]*?)width=.+?[\'|\"]/',
                '/(<img.*?)((height)=[\'"]+[0-9|%]+[\'"]+)/',
                '/(<img.*?)((style)=[\'"]+(.*?)+[\'"]+)/',
            ], [$replaced, $replaced, $replaced], $html);
    }

    /**
     * 是否是Weixin浏览器
     * @return bool
     */
    public static function isWeixin(): bool
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 是否是H5App
     * @return bool
     */
    public static function isH5App(): bool
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], "Html5Plus") === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 返回浏览器类型,app,wx,h5
     * @return string
     */
    public static function browser(): string
    {
        if (self::isH5App()) {
            return 'app';
        }
        return self::isWeixin() ? 'wx' : 'h5';
    }

    /**
     * 是否是手机
     * @return bool
     */
    public static function isMobile(): bool
    {
        $regex_match = "/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|"
            . "htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|"
            . "blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|"
            . "wellcom|bunjalloo|maui|symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|"
            . "^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|jig\s browser|hiptop|^ucweb|^benq|haier|^lct|"
            . "opera\s*mobi|opera\*mini|320x320|240x320|176x220)/i";
        return isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])
            or preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
    }

    /**
     * 发送HTTP请求方法，目前只支持CURL发送请求
     * @param string $url 请求URL
     * @param array $param GET参数数组
     * @param array $data POST的数据，GET请求时该参数无效
     * @param string $method 请求方法GET/POST
     * @return array          响应数据
     */
    public static function http(string $url, array $param = [], $data = '', string $method = 'GET'): array
    {
        $opts = array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        );

        /* 根据请求类型设置特定参数 */
        $opts[CURLOPT_URL] = $url . '?' . http_build_query($param);

        if (strtoupper($method) == 'POST') {
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $data;

            if (is_string($data)) { //发送JSON数据
                $opts[CURLOPT_HTTPHEADER] = array(
                    'Content-Type: application/json; charset=utf-8',
                    'Content-Length: ' . strlen($data),
                );
            }
        }

        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        //发生错误，抛出异常
        if ($error) throw new \Exception('请求发生错误：' . $error);

        return json_decode($data, true);
    }

    /**
     * 导出Word,html格式
     * @param string $content 内容
     * @param string $fileName 文件名
     */
    public static function exportWord(string $content, string $fileName = ''): void
    {
        if (empty($fileName)) {
            $fileName = date('YmdHis');
        }

        header("Content-type:application/msword");
        header("Content-Disposition:attachment; filename={$fileName}.doc");

        echo "<!DOCTYPE html>\n";
        echo "<html xmlns:v=\"urn:schemas-microsoft-com:vml\"";
        echo " xmlns:o=\"urn:schemas-microsoft-com:office:office\"";
        echo " xmlns:w=\"urn:schemas-microsoft-com:office:word\"";
        echo " xmlns:m=\"http://schemas.microsoft.com/office/2004/12/omml\"";
        echo " xmlns=\"http://www.w3.org/TR/REC-html40\">\n";
        echo "<head>\n";
        echo "<!--[if gte mso 9]>";
        echo "<xml>";
        echo "<w:WordDocument>";
        echo "<w:View>Print</w:View>";
        echo "<w:TrackMoves>false</w:TrackMoves>";
        echo "<w:TrackFormatting/>";
        echo "<w:ValidateAgainstSchemas/>";
        echo "<w:SaveIfXMLInvalid>false</w:SaveIfXMLInvalid>";
        echo "<w:IgnoreMixedContent>false</w:IgnoreMixedContent>";
        echo "<w:AlwaysShowPlaceholderText>false</w:AlwaysShowPlaceholderText>";
        echo "<w:DoNotPromoteQF/>";
        echo "<w:LidThemeOther>EN-US</w:LidThemeOther>";
        echo "<w:LidThemeAsian>ZH-CN</w:LidThemeAsian>";
        echo "<w:LidThemeComplexScript>X-NONE</w:LidThemeComplexScript>";
        echo "<w:Compatibility>";
        echo "<w:BreakWrappedTables/>";
        echo "<w:SnapToGridInCell/>";
        echo "<w:WrapTextWithPunct/>";
        echo "<w:UseAsianBreakRules/>";
        echo "<w:DontGrowAutofit/>";
        echo "<w:SplitPgBreakAndParaMark/>";
        echo "<w:DontVertAlignCellWithSp/>";
        echo "<w:DontBreakConstrainedForcedTables/>";
        echo "<w:DontVertAlignInTxbx/>";
        echo "<w:Word11KerningPairs/>";
        echo "<w:CachedColBalance/>";
        echo "<w:UseFELayout/>";
        echo "</w:Compatibility>";
        echo "<w:BrowserLevel>MicrosoftInternetExplorer4</w:BrowserLevel>";
        echo "<m:mathPr>";
        echo "<m:mathFont m:val=\"Cambria Math\"/>";
        echo "<m:brkBin m:val=\"before\"/>";
        echo "<m:brkBinSub m:val=\"--\"/>";
        echo "<m:smallFrac m:val=\"off\"/>";
        echo "<m:dispDef/>";
        echo "<m:lMargin m:val=\"0\"/>";
        echo "<m:rMargin m:val=\"0\"/>";
        echo "<m:defJc m:val=\"centerGroup\"/>";
        echo "<m:wrapIndent m:val=\"1440\"/>";
        echo "<m:intLim m:val=\"subSup\"/>";
        echo "<m:naryLim m:val=\"undOvr\"/>";
        echo "</m:mathPr>";
        echo "</w:WordDocument>";
        echo "</xml>";
        echo "<![endif]-->\n";
        echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>\n";
        echo "<title>{$fileName}</title>\n";
        echo "</head>\n";
        echo "<body>{$content}</body>\n";
        echo "</html>";

        exit();
    }
}