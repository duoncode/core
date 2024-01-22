<?php

declare(strict_types=1);

namespace Conia\Core\Exception;

/** @psalm-api */
class HttpBadRequest extends HttpError
{
    protected const int code = 400;
    protected const string message = 'Bad Request';
}
