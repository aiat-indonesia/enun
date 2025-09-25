<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Disable Scout during testing to prevent facade issues
        if ($this->app) {
            $this->app['config']->set('scout.driver', null);
        }
    }
}
