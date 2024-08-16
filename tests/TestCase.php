<?php

namespace Tests;

use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use  DatabaseMigrations;
    public function setUp()
    : void {

        parent::setUp();
        $this->faker = Factory::create();
        Artisan::call('migrate:refresh');
    }
}
