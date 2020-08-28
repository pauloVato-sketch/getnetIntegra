<?php

namespace Odhen\API\Lib;

class ImpressaoDelphi {

	const DLL_NAME = 'Ifm.rdmPrint';
    private $com = null;

    private function getCom($dllrdm){
        return new \COM($dllrdm);
    }

	public function criaCdsImpressoras($CDFILIAL, $CDLOJA){
        $COM = $this->getCom(self::DLL_NAME);
        $COM->Impressoras($CDFILIAL, $CDLOJA);
    }

    public function imprimeArquivo($fileName, $text) {
	    $COM = $this->getCom(self::DLL_NAME);
	    $COM->CriaArquivo($fileName, $text);
    }

}