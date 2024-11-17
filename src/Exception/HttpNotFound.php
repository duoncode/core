<?php

declare(strict_types=1);

namespace FiveOrbs\Core\Exception;

/** @psalm-api */
class HttpNotFound extends HttpError
{
	protected const int code = 404;
	protected const string message = 'Not Found';
}
