<?php
declare (strict_types=1);

namespace ffhome\common\util;

/**
 * 使用文件阻塞功能实现同步处理
 * 创建对象就加锁，处理完同步事件后，调用unlock进行解锁
 */
class Lock
{
    private $fp = null;
    private $lock = false;

    /**
     * 构造方法，调用即加锁
     * @param string $filename 文件名称
     */
    public function __construct(string $filename)
    {
        $this->fp = fopen($filename, 'w');
        $this->lock = flock($this->fp, LOCK_EX);
    }

    /**
     * 解锁处理
     */
    public function unlock()
    {
        if ($this->lock) {
            flock($this->fp, LOCK_UN);
        }
        fclose($this->fp);
    }
}