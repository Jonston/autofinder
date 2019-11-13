<?php

namespace Boomdraw\AutoFinder\Test\Integration;

use Boomdraw\AutoFinder\Finder;
use Boomdraw\AutoFinder\FinderRefactor;
use Boomdraw\AutoFinder\Test\Integration\Currencies\Currency1;
use Boomdraw\AutoFinder\Test\Integration\Currencies\Currency2;
use Boomdraw\AutoFinder\Test\Integration\Currencies\Currency3;

class AutoFinderTest extends TestCase {

//    public function test_class()
//    {
//        config([
//            'app.currencies.priorities' => [
//                'UAH' => 1,
//                'USD' => 2
//            ]
//        ]);
//
//        config([
//            'app.currencies.classes' => [
//                [
//                    'priority'  => 3,
//                    'driver'    => Currency1::class
//                ],
//                [
//                    'priority'  => 1,
//                    'driver'    => Currency2::class
//                ],
//                [
//                    'priority'  => 1,
//                    'driver'    => Currency3::class
//                ]
//            ]
//        ]);
//
//        $finder = new FinderRefactor();
//
//        $route = $finder->find('EUR', 'RUB');
//
//        dd($route);
//
//
//    }

    public function test_find_route_in_single_class_hi_priority()
    {
        config([
            'app.currencies.priorities' => [
                'UAH' => 1,
                'USD' => 2
            ]
        ]);

        config([
            'app.currencies.classes' => [
                [
                    'priority'  => 3,
                    'driver'    => Currency1::class
                ],
                [
                    'priority'  => 1,
                    'driver'    => Currency2::class
                ],
                [
                    'priority'  => 1,
                    'driver'    => Currency3::class
                ]
            ]
        ]);

        $finder = new FinderRefactor();

        $this->assertEquals(Currency3::class, $finder->find('UAH', 'EUR'));
    }

    public function test_find_route_in_multiple_class_hi_priority()
    {
        config([
            'app.currencies.priorities' => [
                'UAH' => 1,
                'USD' => 2
            ]
        ]);

        config([
            'app.currencies.classes' => [
                [
                    'priority'  => 3,
                    'driver'    => Currency1::class
                ],
                [
                    'priority'  => 1,
                    'driver'    => Currency2::class
                ],
                [
                    'priority'  => 1,
                    'driver'    => Currency3::class
                ]
            ]
        ]);

        $finder = new FinderRefactor();

        $result = $finder->find('RUB', 'EUR');

        $this->assertEquals($result[0], Currency2::class);
        $this->assertEquals($result[1], Currency3::class);
    }

    public function test_find_route_in_multiple_class_hi_priority_given_currency_priority()
    {
        config([
            'app.currencies.priorities' => [
                'UAH' => 1,
                'USD' => 2
            ]
        ]);

        config([
            'app.currencies.classes' => [
                [
                    'priority'  => 3,
                    'driver'    => Currency1::class
                ],
                [
                    'priority'  => 1,
                    'driver'    => Currency2::class
                ],
                [
                    'priority'  => 1,
                    'driver'    => Currency3::class
                ]
            ]
        ]);

        $finder = new FinderRefactor();

        $result = $finder->find('EUR', 'RUB');

        $this->assertEquals($result[0], Currency3::class);
        $this->assertEquals($result[1], Currency2::class);
    }

    public function test_cannot_find_route()
    {
        config([
            'app.currencies.priorities' => [
                'UAH' => 1,
                'USD' => 2
            ]
        ]);

        config([
            'app.currencies.classes' => [
                [
                    'priority'  => 3,
                    'driver'    => Currency1::class
                ],
                [
                    'priority'  => 1,
                    'driver'    => Currency2::class
                ],
                [
                    'priority'  => 1,
                    'driver'    => Currency3::class
                ]
            ]
        ]);

        $finder = new FinderRefactor();

        $result = $finder->find('EUR', 'BYN');

        $this->assertNull($result);
    }

    public function test_find_route_preference_existing_priority_in_config()
    {
        config([
            'app.currencies.priorities' => [
                'USD' => 1
            ]
        ]);

        config([
            'app.currencies.classes' => [
                [
                    'priority'  => 1,
                    'driver'    => Currency1::class
                ],
                [
                    'priority'  => 1,
                    'driver'    => Currency2::class
                ],
                [
                    'priority'  => 1,
                    'driver'    => Currency3::class
                ]
            ]
        ]);

        $finder = new FinderRefactor();

        $result = $finder->find('EUR', 'RUB');

        $this->assertEquals($result[0], Currency1::class);
        $this->assertEquals($result[1], Currency2::class);
    }
}