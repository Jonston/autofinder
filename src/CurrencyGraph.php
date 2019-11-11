<?php

namespace Boomdraw\AutoFinder;

class CurrencyGraph {
    private $map = [];

    public function connect(string $pointFrom, string $pointTo, int $weight)
    {
        $this->map[$pointFrom][$pointTo] = $weight;
    }

    public function getMap()
    {
        return $this->map;
    }
}