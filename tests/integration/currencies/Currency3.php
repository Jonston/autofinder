<?php

namespace Boomdraw\AutoFinder\Test\Integration\Currencies;

class Currency3 implements CurrencyInterface{

    /**
     * @return array
     */
    public static function supported(): array
    {
        return [
            'UAH',
            'USD',
            'EUR'
        ];
    }

}