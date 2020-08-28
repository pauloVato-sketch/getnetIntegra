<?php
/**
 * @see       https://github.com/zendframework/zend-loader for the canonical source repository
 * @copyright Copyright (c) 2005-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-loader/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Loader\TestAsset;

/**
 * @group      Loader
 */
class SamplePlugin
{
    public $options;

    public function __construct($options = null)
    {
        $this->options = $options;
    }
}
