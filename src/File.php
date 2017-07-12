<?php
namespace Yurun\Until\Lock;

class File extends Base
{
	public $filePath;
	private $fp;
	public function __construct($name, $filePath = null)
	{
		$this->name = $name;
		$this->filePath = null === $filePath ? sys_get_temp_dir() : $filePath;
		$this->fp = fopen($this->filePath . '/' . $name . '.lock', 'w+');
		if(false === $this->fp)
		{
			throw new Exception('加锁文件打开失败', LockConst::EXCEPTION_LOCKFILE_OPEN_FAIL);
		}
	}

	/**
	 * 加锁
	 * @return bool
	 */
	protected function __lock()
	{
		return flock($this->fp, LOCK_EX);
	}

	/**
	 * 释放锁
	 * @return bool
	 */
	protected function __unlock()
	{
		fclose($this->fp);
	}

	/**
	 * 不阻塞加锁
	 * @return bool
	 */
	protected function __unblockLock()
	{
		return flock($this->fp, LOCK_EX | LOCK_NB);
	}
}