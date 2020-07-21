<?php

namespace RealPage\JsonApi;

use Neomerx\JsonApi\Schema\Link;
use Neomerx\JsonApi\Schema\Error;
use Neomerx\JsonApi\Contracts\Schema\LinkInterface;

class ErrorFactory
{
    public static function buildUnsupportedMediaType(
        $id = null,
        LinkInterface $aboutLink = null,
        $code = null,
        array $source = null,
        $hasMeta = false,
        $meta = null
    ): Error {
        return new Error(
            $id ?? null,
            $aboutLink ?? new Link(true, 'http://jsonapi.org/format/#content-negotiation-clients', false),
            null,
            '415',
            $code ?? null,
            'Unsupported Media Type',
            'Content-Type of a request containing JSON data must be application/vnd.api+json',
            $source,
            $hasMeta,
            $meta
        );
    }

    public static function buildUnacceptable(
        $id = null,
        LinkInterface $aboutLink = null,
        $code = null,
        array $source = null,
        $hasMeta = false,
        $meta = null
    ): Error {
        return new Error(
            $id ?? null,
            $aboutLink ?? new Link(true, 'http://jsonapi.org/format/#content-negotiation-clients', false),
            null,
            '406',
            $code ?? null,
            'Not Acceptable',
            'Accept header must accept application/vnd.api+json at least once without parameters',
            $source,
            $hasMeta,
            $meta
        );
    }
}
