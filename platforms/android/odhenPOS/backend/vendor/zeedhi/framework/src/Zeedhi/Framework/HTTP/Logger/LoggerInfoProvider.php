<?php
namespace Zeedhi\Framework\HTTP\Logger;

use Zeedhi\Framework\HTTP\Request;

interface LoggerInfoProvider {

    public function getUserData(Request $request);

    public function getContextData(Request $request);

}