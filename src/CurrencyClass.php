<?php

namespace Boomdraw\AutoFinder;

class CurrencyClass
{
    private string $className;

    private integer $priority;

    private array $currencies = [];

    public function __construct(string $className, int $priority)
    {
        $this->className = $className;

        $this->priority = $priority;
    }

    public function hasCurrency($currency): bool
    {
        return array_key_exists($currency, $this->currencies);
    }

    public function setCurrency(string $currency, int $priority): void
    {
        $this->currencies[$currency] = $priority;
    }

    public function getCurrencyPriority(string $name): void
    {
        $this->hasCurrency($name) ? $this->currencies[$name] : null;
    }

    public function getCurrencies(): array
    {
        return $this->currencies;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

}
