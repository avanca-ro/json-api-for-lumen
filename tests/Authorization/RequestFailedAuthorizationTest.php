<?php

namespace RealPage\JsonApi\Authorization;

use Neomerx\JsonApi\Schema\ErrorCollection;
use Neomerx\JsonApi\Exceptions\JsonApiException;

class RequestFailedAuthorizationTest extends \PHPUnit\Framework\TestCase
{

    /** @var ErrorCollection */
    protected $errors;

    /** @var RequestFailedAuthorization */
    protected $exception;

    public function setUp(): void
    {
        parent::setUp();

        $this->errors = new ErrorCollection();
        $this->exception = new RequestFailedAuthorization($this->errors);
    }

    /** @test */
    public function isJsonApiException()
    {
        $this->assertInstanceOf(JsonApiException::class, $this->exception);
    }

    /** @test */
    public function suppliesForbiddenResponseCode()
    {
        $this->assertEquals(403, $this->exception->getHttpCode());
    }
}
