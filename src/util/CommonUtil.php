<?php

namespace ffhome\common\util;

class CommonUtil
{
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
}