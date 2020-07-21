<?php

namespace RealPage\JsonApi;

use Neomerx\JsonApi\Schema\Link;
use Neomerx\JsonApi\Schema\Error;

class ErrorFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testUnsupportedMediaTypeErrorGeneration()
    {
        $link                 = new Link(true, 'http://jsonapi.org/format/#content-negotiation-clients', false);
        $title                = 'Unsupported Media Type';
        $detail               = 'Content-Type of a request containing JSON data must be application/vnd.api+json';
        $defaultError         = ErrorFactory::buildUnsupportedMediaType();
        $expectedDefaultError = new Error(null, $link, null, '415', null, $title, $detail, null, false, null);
        $this->assertEquals($expectedDefaultError, $defaultError);

        $customLink      = new Link(true, 'http://docs.myapp.com/errors#specific-error', false);
        $idError         = ErrorFactory::buildUnsupportedMediaType(1, $customLink, '12', null, true, ['this' => 'is meta']);
        $expectedIdError = new Error(1, $customLink, null, '415', '12', $title, $detail, null, true, ['this' => 'is meta']);
        $this->assertEquals($expectedIdError, $idError);
    }

    public function testUnacceptableErrorGeneration()
    {
        $link                 = new Link(true, 'http://jsonapi.org/format/#content-negotiation-clients', false);
        $title                = 'Not Acceptable';
        $detail               = 'Accept header must accept application/vnd.api+json at least once without parameters';
        $defaultError         = ErrorFactory::buildUnacceptable();
        $expectedDefaultError = new Error(null, $link, null, '406', null, $title, $detail, null, false, null);
        $this->assertEquals($expectedDefaultError, $defaultError);

        $customLink      = new Link(true, 'http://docs.myapp.com/errors#specific-error', false);
        $idError         = ErrorFactory::buildUnacceptable(1, $customLink, '12', null, true, ['this' => 'is meta']);
        $expectedIdError = new Error(1, $customLink, null, '406', '12', $title, $detail, null, true, ['this' => 'is meta']);
        $this->assertEquals($expectedIdError, $idError);
    }
}
