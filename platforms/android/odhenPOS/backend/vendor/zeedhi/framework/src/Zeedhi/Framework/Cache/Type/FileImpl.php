<?php
namespace Zeedhi\Framework\Cache\Type;

use Doctrine\Common\Cache\FileCache;
use Zeedhi\Framework\Cache\Cache;
use Zeedhi\Framework\Cache\Exception;

/**
 * Class FileImpl
 *
 * Class to provide an implementation of File cache driver
 *
 * @package Zeedhi\Framework\Cache\Type
 */
class FileImpl extends FileCache implements Cache {

	/**
	 * Constructor.
	 *
	 * @param string      $directory The cache directory.
	 * @param string|null $extension The cache file extension.
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($directory, $extension = '.cache') {
		parent::__construct($directory, $extension);
	}

	/**
	 * {@inheritdoc}
	 */
	public function save($key, $value, $lifeTime = 0) {
		$cacheFile = $this->getCacheFileName($key);
		$item = serialize(array(
			'value' => serialize($value),
			'lifeTime' => $lifeTime,
		));
		if (!file_put_contents($cacheFile, $item)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function fetch($key) {
		if ($data = $this->getData($key)) {
			return $data;
		}
		throw Exception::valueNotFound($key);
	}


	protected function getCacheFileName($key) {
		$hash = hash('sha256', $key);
		$path = $this->getDirectory() . DIRECTORY_SEPARATOR . $hash . $this->getExtension();
		return $path;
	}

	/**
	 * Get data value from file cache
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	protected function getData($key) {
		$cacheFile = $this->getCacheFileName($key);
		if (!file_exists($cacheFile)) {
			return false;
		}
		$data = unserialize(file_get_contents($cacheFile));
		if ($data['lifeTime'] != 0 && time() > $data['lifeTime']) {
			$this->delete($key);
			return false;
		}

		return unserialize($data['value']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete($key) {
		return unlink($this->getCacheFileName($key));
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doFetch($id) {}

	/**
	 * {@inheritdoc}
	 */
	protected function doContains($id) {}

	/**
	 * {@inheritdoc}
	 */
	protected function doSave($id, $data, $lifeTime = 0) {}

}