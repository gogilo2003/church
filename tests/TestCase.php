<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (class_exists(\Illuminate\Support\Facades\ParallelTesting::class) && \Illuminate\Support\Facades\ParallelTesting::token()) {
            $db = config('database.connections.sqlite.database');
            if (is_string($db) && preg_match('/^(.*)\.sqlite_test_(\d+)$/', $db, $matches)) {
                $fixedDb = $matches[1] . '_test_' . $matches[2] . '.sqlite';
                config(['database.connections.sqlite.database' => $fixedDb]);
            }
        }
    }
}
