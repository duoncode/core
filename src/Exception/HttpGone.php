<?php

declare(strict_types=1);

namespace Duon\Core\Exception;

/** @psalm-api */
class HttpGone extends HttpError
{
	protected const int code = 410;
	protected const string message = 'Gone';
}