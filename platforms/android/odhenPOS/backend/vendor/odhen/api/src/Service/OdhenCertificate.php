<?php
namespace Odhen\API\Service;

use NFePHP\Common\Certificate;

class OdhenCertificate extends Certificate {
    // sobrecarga do método isExpired para não validação da data de validade do certificado
    public function isExpired(){
        return false;
    }
}
