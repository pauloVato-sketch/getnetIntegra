<?php

namespace Odhen\API\Test;

class cURLRequestMock extends \Zeedhi\Framework\Remote\cURLRequest {

    public function request($requestPath, array $fields = Array()){
        // Retorna True quando e Mock, para que nao haja impressao
        return true;
    }
}