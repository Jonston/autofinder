<?php

namespace Boomdraw\AutoFinder;

use Taniko\Dijkstra\Graph;

class Finder {

    private $graph;

    private $currencies = [];

    private $classes = [];

    public function __construct()
    {
        $this->graph = new Graph();

        $currencies = [];

        foreach (config('app.currencies.priorities', []) as $currency => $priority){
            $currencies[$currency] = $priority;
        }

        $maxPriority = max($currencies ? $currencies : [1]);

        foreach (config('app.currencies.classes') as $classConfig){

            if( ! isset($classConfig['driver']))
                throw new ErrorException('Config must contain a driver param');

            $driver = $classConfig['driver'];
            $priority = $classConfig['priority'] ?? 1;

            $this->classes[$driver] = $priority;

            foreach($driver::supported() as $currency){
                $id = uniqid();

                $this->currencies[$id] = [
                    'id'        => $id,
                    'name'      => $currency,
                    'class'     => $driver,
                    'priority'  => $currencies[$currency] ?? $maxPriority
                ];
            }
        }
    }

    protected function getClassPriority(string $class)
    {
        return $this->classes[$class];
    }

    protected function getCurrency(string $id)
    {
        return $this->currencies[$id] ?? null;
    }

    public function getCurrenciesByName(string $name)
    {
        return array_filter($this->currencies, function($currency) use ($name){
            return $currency['name'] === $name;
        });
    }

    public function getCurrencySiblings(array $source)
    {
        return array_filter($this->currencies, function($destination) use ($source){
            return $source !== $destination && $source['class'] === $destination['class'];
        });
    }

    public function getCurrencyNeighbors(array $source)
    {
        return array_filter($this->currencies, function($destination) use ($source){
            return $source['class'] !== $destination['class'] && $source['name'] === $destination['name'];
        });
    }

    public function createGraph()
    {
        foreach($this->currencies as $source){
            $siblings = $this->getCurrencySiblings($source);
            $neighbors = $this->getCurrencyNeighbors($source);

            foreach(array_merge($siblings, $neighbors) as $destination){
                $this->graph->add($source['id'], $destination['id'], $destination['priority']);
            }
        }
    }

    protected function convertPath(array $path)
    {
        $route = [];

        foreach($path as $key => $point){
            $last = end($route);

            if($last !== $point['class'])
                $route[] = $point['class'];
        }

        return $route;
    }

    public function find(string $from, string $to)
    {
        $this->createGraph();

        $routes = [];

        foreach($this->getCurrenciesByName($from) as $currencyFrom){
            foreach($this->getCurrenciesByName($to) as $currencyTo){

                $route = $this->graph->search($currencyFrom['id'], $currencyTo['id']);

                $path = array_map(function($point){
                    return $this->getCurrency($point);
                }, $route);

                $routes[] = [
                    'from'  => $currencyFrom,
                    'to'    => $currencyTo,
                    'cost'  => $this->graph->cost($route),
                    'path'  => $this->convertPath($path)
                ];
            }
        }

        if( ! $routes) return null;

        $routes = collect($routes)->sort(function($a, $b){
            $aEdges = count($a['path']);
            $bEdges = count($b['path']);
            $aWeight = $a['cost'];
            $bWeight = $b['cost'];

            $aPriority = array_reduce($a['path'], function($total, $class){
                return $total + $this->getClassPriority($class);
            });

            $bPriority = array_reduce($b['path'], function($total, $class){
                return $total + $this->getClassPriority($class);
            });

            if($aEdges > $bEdges) return 1;
            if($aEdges < $bEdges) return -1;

            if($aPriority > $bPriority) return 1;
            if($aPriority < $bPriority) return -1;

            if($aWeight > $bWeight) return 1;
            if($aWeight < $bWeight) return -1;
        })->all();

        $route = current($routes);

        return (count($route['path']) > 1) ? $route['path'] : $route['path'][0];
    }




}