<?php

declare(strict_types=1);

use Conia\Cms\Config;
use Conia\Cms\Tests\Setup\TestCase;

uses(TestCase::class);

test('Init Conig', function () {
    $config = new Config('conia');

    expect($config->app())->toBe('conia');
});
