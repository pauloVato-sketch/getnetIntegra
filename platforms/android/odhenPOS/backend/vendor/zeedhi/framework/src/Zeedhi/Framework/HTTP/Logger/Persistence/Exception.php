<?php
namespace Zeedhi\Framework\HTTP\Logger\Persistence;

class Exception extends \Exception {

    public static function requestNotLogged() {
        return new static('Can\'t log response because request was not logged');
    }

}