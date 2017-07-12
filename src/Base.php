<?php
namespace Yurun\Until\Lock;

abstract class Base
{
	/**
	 * 锁名称
	 * @var string
	 */
	public $name;

	/**
	 * 参数
	 * @var array
	 */
	public $params;

	/**
	 * 是否已加锁
	 * @var bool
	 */
	protected $isLocked = false;

	public function __construct($name, $params = array())
	{
		$this->name = $name;
		$this->params = $params;
	}

	/**
	 * 是否已加锁
	 * @return boolean
	 */
	public function isLocked()
	{
		return $this->isLocked;
	}

	/**
	 * 加锁
	 * @param callback $concurrentCallback 并发判断回调，如果不为null则在加锁成功后调用。用于判断是否已在之前的并发中处理过该任务。true:已处理，false:未处理
	 * @return bool
	 */
	public function lock($concurrentCallback = null)
	{
		if($this->isLocked)
		{
			throw new Exception('已经加锁', LockConst::EXCEPTION_ALREADY_LOCKED);
		}
		if($this->__lock())
		{
			$this->isLocked = true;
			if(null === $concurrentCallback)
			{
				return LockConst::LOCK_RESULT_SUCCESS;
			}
			else
			{
				if($concurrentCallback())
				{
					return LockConst::LOCK_RESULT_CONCURRENT_COMPLETE;
				}
				else
				{
					return LockConst::LOCK_RESULT_CONCURRENT_UNTREATED;
				}
			}
		}
		else
		{
			return LockConst::LOCK_RESULT_FAIL;
		}
	}

	/**
	 * 释放锁
	 * @return bool
	 */
	public function unlock()
	{
		if(!$this->isLocked)
		{
			throw new Exception('未加锁', LockConst::EXCEPTION_UNLOCKED);
		}
		if($this->__unlock())
		{
			$this->isLocked = false;
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 不阻塞加锁
	 * @return bool
	 */
	public function unblockLock()
	{
		if($this->isLocked)
		{
			throw new Exception('已经加锁', LockConst::EXCEPTION_ALREADY_LOCKED);
		}
		if($this->__unblockLock())
		{
			$this->isLocked = true;
			if(null === $concurrentCallback)
			{
				return LockConst::LOCK_RESULT_SUCCESS;
			}
			else
			{
				if($concurrentCallback())
				{
					return LockConst::LOCK_RESULT_CONCURRENT_COMPLETE;
				}
				else
				{
					return LockConst::LOCK_RESULT_CONCURRENT_UNTREATED;
				}
			}
		}
		else
		{
			return LockConst::LOCK_RESULT_FAIL;
		}
	}

	/**
	 * 加锁
	 * @return bool
	 */
	protected abstract function __lock();

	/**
	 * 释放锁
	 * @return bool
	 */
	protected abstract function __unlock();

	/**
	 * 不阻塞加锁
	 * @return bool
	 */
	protected abstract function __unblockLock();
}