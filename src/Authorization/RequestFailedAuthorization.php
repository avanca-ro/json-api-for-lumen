<?php

namespace RealPage\JsonApi\Authorization;

use Neomerx\JsonApi\Exceptions\JsonApiException;

class RequestFailedAuthorization extends JsonApiException
{
    public function getHttpCode(): int
    {
        return 403;
    }
}
