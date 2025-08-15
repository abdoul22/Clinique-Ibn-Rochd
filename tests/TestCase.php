<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Disable console output mocking globally for tests to avoid Mockery on OutputStyle.
     */
    public $mockConsoleOutput = false;

    protected function setUp(): void
    {
        parent::setUp();
        // Désactive le mock de sortie console pour éviter les prompts interceptés par Mockery
        $this->withoutMockingConsoleOutput();
    }

    /**
     * Options passed to migrate:fresh when using RefreshDatabase.
     */
    protected function migrateFreshUsing()
    {
        return [
            '--drop-views' => true,
            '--drop-types' => true,
            '--seed' => false,
            '--force' => true,
        ];
    }
}
