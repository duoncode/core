<?php

declare(strict_types=1);

namespace FiveOrbs\Core\Tests\Fixtures;

use FiveOrbs\Core\Factory\Nyholm;
use FiveOrbs\Core\Response;

class TestController
{
	public function textView(): Response
	{
		return Response::create(new Nyholm())->body('text');
	}
}
