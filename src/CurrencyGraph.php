<?php

namespace Boomdraw\AutoFinder;

class CurrencyGraph
{
    private array $map = [];

    public function connect(string $pointFrom, string $pointTo, int $weight): void
    {
        $this->map[$pointFrom][$pointTo] = $weight;
    }

    public function getMap(): array
    {
        return $this->map;
    }
}
