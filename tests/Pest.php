<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| withoutVite() is applied here for every Feature test, not per-file —
| every page renders a layout with @vite(), which throws a "manifest not
| found" error in tests unless assets are built. This sidesteps that
| entirely rather than requiring `npm run build` before every test run.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function () {
        $this->withoutVite();
    })
    ->in('Feature');
