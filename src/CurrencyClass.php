<?php

namespace Boomdraw\AutoFinder;

class CurrencyClass {
    private $className;

    private $priority;

    private $currencies = [];

    public function __construct(string $className, int $priority)
    {
        $this->className = $className;

        $this->priority = $priority;
    }

    public function hasCurrency($currency)
    {
        return array_key_exists($currency, $this->currencies);
    }

    public function setCurrency(string $currency, int $priority)
    {
        $this->currencies[$currency] = $priority;
    }

    public function getCurrencyPriority(string $name)
    {
        $this->hasCurrency($name) ? $this->currencies[$name] : null;
    }

    public function getCurrencies()
    {
        return $this->currencies;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getPriority()
    {
        return $this->priority;
    }

}