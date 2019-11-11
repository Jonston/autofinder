<?php

namespace Boomdraw\AutoFinder;

use Fisharebest\Algorithm\Dijkstra;

class Finder {

    private $graph;

    private $classes = [];

    public function __construct()
    {
        $this->graph = new CurrencyGraph();

        $this->setClasses();

        $this->setGraph();
    }

    /**
     * @return void
     */
    private function setClasses()
    {
        foreach (config('app.currencies.classes') as $classConfig){
            $driver = $classConfig['driver'];
            $priority = $classConfig['priority'];
            $currencies = $driver::supported();

            $class = new CurrencyClass($driver, $priority);

            foreach($currencies as $currency){
                $priority = config(
                    'app.currencies.priorities.' . $currency,
                    max(config('app.currencies.priorities', [1]))
                );

                $class->setCurrency(mb_convert_case($currency, MB_CASE_UPPER), $priority);
            }

            $this->classes[] = $class;
        }

        usort($this->classes, function($a, $b){
            return $a->getPriority() > $b->getPriority();
        });
    }

    /**
     * @return void
     */
    private function setGraph()
    {
        foreach($this->classes as $class){
            $neighbors = $this->getClassNeighbors($class);

            foreach($neighbors as $neighbor){
                foreach($this->getIntersectingCurrencies($class, $neighbor) as $currency => $priority)
                    $this->graph->connect($class->getClassName(), $neighbor->getClassName(), $priority);
            }
        }
    }

    /**
     * @param CurrencyClass $source
     * @param CurrencyClass $destination
     * @return array
     */
    private function getIntersectingCurrencies(CurrencyClass $source, CurrencyClass $destination)
    {
        return array_intersect_key($source->getCurrencies(), $destination->getCurrencies());
    }

    /**
     * @param CurrencyClass $source
     * @return array
     */
    private function getClassNeighbors(CurrencyClass $source)
    {
        return array_filter($this->classes, function($destination) use ($source){
            return $source !== $destination && $this->getIntersectingCurrencies($source, $destination);
        });
    }

    /**
     * @param string $currency
     * @return array
     */
    public function getClassesHasCurrency(string $currency)
    {
        return array_filter($this->classes, function($class) use ($currency){
            return $class->hasCurrency($currency);
        });
    }

    /**
     * @param string $from
     * @param string $to
     * @return array
     */
    public function findRoutesSingleClass(string $from, string $to)
    {
        $routes = [];

        foreach($this->classes as $class){
            if($class->hasCurrency($from) && $class->hasCurrency($to))
                $routes[] = $class->getClassName();
        }

        return $routes;
    }

    /**
     * @param string $from
     * @param string $to
     * @return void
     */
    public function findRoutesMultipleClass(string $from, string $to)
    {
        $algorithm = new Dijkstra($this->graph->getMap());

        foreach($this->getClassesHasCurrency($from) as $classFrom){
            foreach($this->getClassesHasCurrency($to) as $classTo){
                $route = $algorithm->shortestPaths($classFrom->getClassName(), $classTo->getClassName());

                if($route) $routes[] = $route[0];
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
        $from = mb_convert_case($from, MB_CASE_UPPER);
        $to = mb_convert_case($to, MB_CASE_UPPER);

        $routes = ($result = $this->findRoutesSingleClass($from, $to))
            ? $result
            : $this->findRoutesMultipleClass($from, $to);

        return $routes ? $routes[0] : null;
    }
}