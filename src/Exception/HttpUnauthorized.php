<?php

declare(strict_types=1);

namespace Duon\Core\Exception;

/** @api */
class HttpUnauthorized extends HttpError
{
	protected const int code = 401;
	protected const string message = 'Unauthorized';
}
