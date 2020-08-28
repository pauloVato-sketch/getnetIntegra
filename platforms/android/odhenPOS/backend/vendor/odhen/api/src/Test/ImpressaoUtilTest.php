<?php

namespace Odhen\API\Test;

use Odhen\API\Lib\ImpressaoUtil;
use Odhen\API\Remote\Printer\Command;

class ImpressaoUtilTest extends ImpressaoUtil {

    public function requisicaoPonte($dadosImpressora, Command $comandos) {
    	return array(
            'error' => false
        );
    }

}