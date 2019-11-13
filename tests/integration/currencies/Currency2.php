<?php

namespace Boomdraw\AutoFinder\Test\Integration\Currencies;

class Currency2 extends Driver {

    /**
     * @return array
     */
    public static function supported(): array
    {
        return [
            'UAH',
            'USD',
            'RUB'
        ];
    }

}