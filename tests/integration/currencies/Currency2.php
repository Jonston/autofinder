<?php

namespace Boomdraw\AutoFinder\Test\Integration\Currencies;

class Currency2 implements CurrencyInterface{

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