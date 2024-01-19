<?php

declare(strict_types=1);

namespace Conia\Core\Tests\Fixtures;

use Conia\Core\Factory\Nyholm;
use Conia\Core\Response;

class TestController
{
    public function textView(): Response
    {
        return Response::create(new Nyholm())->body('text');
    }
}
