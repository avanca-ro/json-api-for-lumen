<?php

namespace RealPage\JsonApi\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request as IlluminateRequest;
use Neomerx\JsonApi\Schema\Error;
use Neomerx\JsonApi\Schema\Link;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use RealPage\JsonApi\Authorization\RequestFailedAuthorization;
use RealPage\JsonApi\Validation\RequestFailedValidation;
use RealPage\JsonApi\Validation\ValidatesRequests;

class Request
{
    /** @var array */
    protected $json;

    /** @var IlluminateRequest */
    protected $request;

    /** @var Validator */
    protected $validator;

    public function __construct(IlluminateRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @throws RequestFailedValidation
     */
    public function validate()
    {
        /** @var ValidatesRequests $validator */
        $validator = $this->validator();

        if ($validator->isValid($this) === false) {
            throw new RequestFailedValidation($validator->errors());
        }
    }

    /**
     * Ensure that a requested operation is authorized.
     * If not, throw an exception.
     *
     * This requires a registered Policy.
     * If no policy is defined,
     * the framework will throw InvalidArgumentException.
     *
     * See also:
     *   https://laravel.com/docs/master/authorization
     *   http://jsonapi.org/format/#errors
     *
     * @param string $action Desired action; must match a policy method name.
     * @param mixed  $object Target object; class must match a policy.
     * @param array  $source Reference to source of error in request.
     *
     * @return bool  True on success; throws exception on failure.
     *
     * @throws RequestFailedAuthorization
     *
     * TODO: use a UUID for the source?
     */
    public function authorize(
        string $action,
        $object,
        array $source = null
    ) {
        if ($this->request()->user()->cant($action, $object)) {
            throw new RequestFailedAuthorization(
                new Error(
                    $id = null,
                    $link = new Link(true, 'https://tools.ietf.org/html/rfc7231#section-6.5.3', false),
                    null,
                    $status = '403',
                    $code = null,
                    $title = 'Forbidden',
                    $desc = 'Access is denied for one or more of the specified resources',
                    $source,
                    $hasMeta = false,
                    $meta = null
                )
            );
        }

        return true;
    }

    public function json(): array
    {
        if (!isset($this->json)) {
            $json = json_decode(
                $this->request()->getContent(),
                true, // associative array
                512, // depth
                JSON_UNESCAPED_SLASHES
            );

            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new JsonApiException(new MalformedRequest());
            }

            $this->json = $json;
        }

        return $this->json;
    }

    public function validator() : ValidatesRequests
    {
        return $this->validator;
    }

    public function setValidator(ValidatesRequests $validator)
    {
        $this->validator = $validator;
    }

    public function request(): IlluminateRequest
    {
        return $this->request;
    }
}
