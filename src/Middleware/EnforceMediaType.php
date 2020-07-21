<?php

namespace RealPage\JsonApi\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Schema\ErrorCollection;
use RealPage\JsonApi\ErrorFactory;
use RealPage\JsonApi\MediaTypeGuard;

class EnforceMediaType
{
    /**
     * Adds support for the Server Responsibilities section of content negotiation
     * spec for json-api.
     *
     * @see http://jsonapi.org/format/#content-negotiation
     */
    public function handle(Request $request, Closure $next, MediaTypeGuard $guard = null)
    {
        //
        $guard = $guard ?? app(MediaTypeGuard::class);

        if (!$guard->validateExistingContentType($request) || !$guard->hasCorrectHeadersForData($request)) {
            $errors = (new ErrorCollection())->add(ErrorFactory::buildUnsupportedMediaType());
            $encoder = Encoder::instance([])->withEncodeOptions(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            return new Response($encoder->encodeErrors($errors), 415, ['Content-Type' => $guard->getContentType()]);
        }

        if (!$guard->hasCorrectlySetAcceptHeader($request)) {
            $errors = (new ErrorCollection())->add(ErrorFactory::buildUnacceptable());
            $encoder = Encoder::instance([])->withEncodeOptions(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            return new Response($encoder->encodeErrors($errors), 406, ['Content-Type' => $guard->getContentType()]);
        }

        $response = $next($request);
        $response->header('Content-Type', $guard->getContentType());

        return $response;
    }
}
