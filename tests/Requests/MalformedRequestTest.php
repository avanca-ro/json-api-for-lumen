<?php

namespace RealPage\JsonApi\Requests;

use Neomerx\JsonApi\Contracts\Schema\ErrorInterface;

class MalformedRequestTest extends \PHPUnit\Framework\TestCase
{

    /** @var MalformedRequest */
    protected $error;

    public function setUp():void
    {
        parent::setUp();

        $this->error = new MalformedRequest();
    }

    /** @test */
    public function isJsonApiError()
    {
        $this->assertInstanceOf(ErrorInterface::class, $this->error);
    }

    /** @test */
    public function signifiesBadRequest()
    {
        $this->assertEquals(400, $this->error->getStatus());
        $this->assertEquals('Request json malformed', $this->error->getTitle());
        $this->assertEquals('The request json is malformed and could not be parsed.', $this->error->getDetail());
    }

    /** @test */
    public function omitsUnneededInformation()
    {
        $this->assertNull($this->error->getId());
        $this->assertNull($this->error->getLinks());
        $this->assertNull($this->error->getTypeLinks());
        $this->assertNull($this->error->getCode());
        $this->assertNull($this->error->getSource());
        $this->assertFalse($this->error->hasMeta());
        $this->assertNull($this->error->getMeta());
    }
}
