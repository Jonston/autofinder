<?php

namespace Boomdraw\AutoFinder\Test\Integration\Currencies;

class Currency1 extends Driver {

    public static function supported(): array
    {
        return [
            'UAH',
            'EUR'
        ];
    }

}