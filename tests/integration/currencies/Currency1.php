<?php

namespace Boomdraw\AutoFinder\Test\Integration\Currencies;

class Currency1 implements CurrencyInterface{

    public static function supported(): array
    {
        return [
            'UAH',
            'EUR'
        ];
    }

}