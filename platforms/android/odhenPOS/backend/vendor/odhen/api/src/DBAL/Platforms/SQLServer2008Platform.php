<?php
namespace Odhen\API\DBAL\Platforms;

use Doctrine\DBAL\Platforms;

class SQLServer2008Platform extends Platforms\SQLServer2005Platform {

    /**
     * {@inheritDoc}
     */
    public function getDateTimeFormatString() {
        return 'd-m-Y H:i:s.000';
    }

    /**
     * {@inheritDoc}
     */
    public function getDateTimeTzFormatString() {
        return 'd-m-Y H:i:s.000 P';
    }

    /**
     * {@inheritDoc}
     */
    public function getDateFormatString() {
        return 'd-m-Y';
    }

}