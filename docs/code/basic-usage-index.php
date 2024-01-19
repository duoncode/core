<?php

declare(strict_types=1);

require 'basic-usage-app.php';

$response = $app->run(
    $factory->serverRequest()->withUri(
        $factory->uri('https://example.com/test-string')
    )
);
assert((string)$response->getBody() === 'test-string');
