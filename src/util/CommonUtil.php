<?php

namespace ffhome\common\util;

class CommonUtil
{
    /**
     * 密码加密算法
     * @param $value 需要加密的值
     * @return string
     */
    public static function password($value)
    {
        $value = 'ffhome_' . md5($value) . '_encrypt' . sha1($value);
        return md5($value);
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
    public static function getTree($list, $rootId = 0, $keyName = 'id', $parentKeyName = 'pid', $childName = 'child')
    {
        $root = [$keyName => $rootId];
        self::buildTree($list, $root, $keyName, $parentKeyName, $childName);
        return $root[$childName];
    }

    private static function buildTree($list, &$node, $keyName, $parentKeyName, $childName)
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
    public static function convertToArray($list, $val, $key = '')
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
    public static function getPrefixArray($list, $prefix)
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
     * @return mixed
     */
    public static function getRealIp()
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
     * 获取当前页面完整URL地址
     */
    public static function getUrl()
    {
        $sys_protocol = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocol . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
    }

    /**
     * 发送HTTP请求方法，目前只支持CURL发送请求
     * @param string $url 请求URL
     * @param array $param GET参数数组
     * @param array $data POST的数据，GET请求时该参数无效
     * @param string $method 请求方法GET/POST
     * @return array          响应数据
     */
    public static function http($url, $param = [], $data = '', $method = 'GET')
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
}