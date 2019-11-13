<?php

namespace Boomdraw\AutoFinder;

use Taniko\Dijkstra\Graph;

class Finder {

    private $graph;

    private $classes = [];

    private $currencies = [];

    private $maxPriority;

    public function __construct()
    {
        $this->graph = new Graph();

        $this->init();
    }

    /**
     * @throws ErrorException
     *
     * @return void
     */
    protected function init(): void
    {
        foreach (config('app.currencies.priorities', []) as $currency => $priority){
            $this->currencies[$currency] = $priority;
        }

        $this->maxPriority = max(config('app.currencies.priorities', [0]));

        foreach (config('app.currencies.classes') as $classConfig){

            if( ! isset($classConfig['driver']))
                throw new ErrorException('Config must contain a driver param');

            $driver = $classConfig['driver'];
            $priority = $classConfig['priority'] ?? 1;

            $this->classes[$driver] = $priority;
        }
    }

    /**
     * @param bool $withPriorities
     * @return array
     */
    protected function getClasses(bool $withPriorities = false): array
    {
        return $withPriorities ? $this->classes : array_keys($this->classes);
    }

    /**
     * @param string $currency
     * @return int
     */
    protected function getCurrencyPriority(string $currency): int
    {
        return $this->currencies[$currency] ?? $this->maxPriority;
    }

    /**
     * @param string $class
     * @return mixed|null
     */
    protected function getClassPriority(string $class)
    {
        return $this->classes[$class] ?? null;
    }

    /**
     * @return void
     */
    protected function setGraph(): void
    {
        foreach($this->getClasses() as $class){
            $neighbors = $this->getClassNeighbors($class);

            foreach($neighbors as $neighbor){
                foreach($this->getIntersectingCurrencies($class, $neighbor) as $currency)
                    $this->graph->add(
                        $class . '_' . $currency,
                        $neighbor . '_' . $currency,
                        $this->getCurrencyPriority($currency)
                    );
            }
        }
    }

    /**
     * @param string $source
     * @param string $destination
     * @return array
     */
    protected function getIntersectingCurrencies(string $source, string $destination): array
    {
        return array_intersect($source::supported(), $destination::supported());
    }

    /**
     * @param string $source
     * @return array
     */
    protected function getClassNeighbors(string $source): array
    {
        return array_filter($this->getClasses(), function($destination) use ($source){
            return $source !== $destination && $this->getIntersectingCurrencies($source, $destination);
        });
    }

    /**
     * @param string $currency
     * @return array
     */
    protected function getClassesHasCurrency(string $currency): array
    {
        return array_filter($this->getClasses(), function($class) use ($currency){
            return $class::isSupported($currency);
        });
    }

    /**
     * @param string $from
     * @param string $to
     * @return array
     */
    protected function findRoutesSingleClass(string $from, string $to): array
    {
        $routes = [];

        foreach($this->getClasses() as $class){
            if($class::isSupported($from) && $class::isSupported($to))
                $routes[] = $class;
        }

        usort($routes, function($a, $b){
            return $this->getClassPriority($a) > $this->getClassPriority($b);
        });

        return $routes;
    }

    /**
     * @param string $from
     * @param string $to
     * @return array
     */
    protected function findRoutesMultipleClass(string $from, string $to): array
    {
        $this->setGraph();

        $routes = [];

        dd($this->graph->getNodes());

        foreach($this->getClassesHasCurrency($from) as $classFrom){
            foreach($this->getClassesHasCurrency($to) as $classTo){
                $route = $this->graph->search($classFrom . '_' . $from, $classTo . '_' . $to);

                if($route) {
                    $routes[] = [
                        'path' => $route,
                        'cost' => $this->graph->cost($route)
                    ];
                }
            }
        }

        usort($routes, function($a, $b){
            return count($a) > count($b);
        });

        return $routes;
    }


    /**
     * @param string $from
     * @param string $to
     * @return array
     */
    public function find(string $from, string $to)
    {
        $routes = ($result = $this->findRoutesSingleClass($from, $to))
            ? $result
            : $this->findRoutesMultipleClass($from, $to);

        return $routes ? $routes[0] : null;
    }
}