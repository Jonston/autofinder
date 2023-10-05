<?php

namespace Boomdraw\AutoFinder\Test\Integration;

use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected $testModel;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.currencies.priorities', [
            'UAH' => 1,
            'USD' => 2
        ]);

        $app['config']->set('app.currencies.classes', [
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
            ],
            [
                'priority'  => 1,
                'driver'    => Currency4::class
            ]
        ]);
    }
}
