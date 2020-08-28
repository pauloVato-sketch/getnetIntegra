<?php
namespace Zeedhi\Framework\HTTP\Logger;

class Exception extends \Exception{

    const SKIP_EXCEPTION = 1;

    /**
     * @return static Skip current Request Exception.
     */
    public static function skipCurrentRequest() {
        return new static("Skip current request!", self::SKIP_EXCEPTION);
    }
}