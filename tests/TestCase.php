<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\File;

abstract class TestCase extends BaseTestCase
{
    private static ?string $compiledPath = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (self::$compiledPath === null) {
            self::$compiledPath = storage_path('framework/testing/views-tests-'.getmypid());
        }

        if (! File::exists(self::$compiledPath)) {
            File::makeDirectory(self::$compiledPath, 0755, true);
        }

        config()->set('view.compiled', self::$compiledPath);
    }
}
