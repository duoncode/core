<?php

declare(strict_types=1);

namespace Duon\Core\Tests\Fixtures;

use Duon\Core\Factory\Nyholm;
use Duon\Core\Response;

class TestController
{
	public function textView(): Response
	{
		return Response::create(new Nyholm())->body('text');
	}
}