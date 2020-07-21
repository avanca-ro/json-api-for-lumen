<?php

namespace RealPage\JsonApi\Validation;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;
use Neomerx\JsonApi\Schema\Error;
use Mockery;
use RealPage\JsonApi\Requests\Request;

class ValidatesRequestsTest extends \PHPUnit\Framework\TestCase
{
    const TYPE = 'my-type';

    /** @var ValidatesRequests */
    protected $validatesRequests;

    public function setUp(): void
    {
        parent::setUp();

        $this->validatesRequests = new class extends ValidatesRequests {
            public function type() : string
            {
                return ValidatesRequestsTest::TYPE;
            }
        };
    }

    /** @test */
    public function validates()
    {
        $json = [uniqid()];
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('json')->andReturn($json);

        $validator = Mockery::mock(Validator::class);
        $validator->shouldReceive('fails')->andReturnFalse();

        $validatorFactory = Mockery::mock(Factory::class);
        $validatorFactory->shouldReceive('make')->with(
            $json,
            $this->validatesRequests->rules(),
            $this->validatesRequests->messages()
        )->andReturn($validator);
        $this->validatesRequests->setValidatorFactory($validatorFactory);

        $this->validatesRequests->isValid($request);
        $this->assertEquals(0, $this->validatesRequests->errors()->count());
    }

    /** @test */
    public function failedValidationProvidesErrors()
    {
        $json = [uniqid()];
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('json')->andReturn($json);

        $laravelErrors = collect([
            'path.to.field' => [
                'field is invalid'
            ]
        ]);
        $validator = Mockery::mock(Validator::class);
        $validator->shouldReceive('fails')->andReturnTrue();
        $validator->shouldReceive('errors')->andReturn($laravelErrors);

        $validatorFactory = Mockery::mock(Factory::class);
        $validatorFactory->shouldReceive('make')->with(
            $json,
            $this->validatesRequests->rules(),
            $this->validatesRequests->messages()
        )->andReturn($validator);
        $this->validatesRequests->setValidatorFactory($validatorFactory);

        $this->validatesRequests->isValid($request);
        $this->assertGreaterThan(0, $this->validatesRequests->errors()->count());

        /** @var Error $error */
        $error = $this->validatesRequests->errors()->getArrayCopy()[0];

        $this->assertEquals(422, $error->getStatus());
        $this->assertEquals('Invalid Attribute', $error->getTitle());
        $this->assertEquals('field is invalid', $error->getDetail());
        $this->assertEquals([
            'pointer' => '/path/to/field/'
        ], $error->getSource());
    }

    /** @test */
    public function suppliesDefaultRules()
    {
        $this->assertEquals([
            'data'      => 'required',
            'data.type' => 'required|in:'.self::TYPE,
        ], $this->validatesRequests->rules());
    }

    /** @test */
    public function suppliesDefaultMessages()
    {
        $this->assertEquals([
            'data.required'      => 'Data is required in a valid json api format.',
            'data.type.required' => 'A valid resource type must be provided.',
            'data.type.in'       => 'The resource type provided does not match the expected type of "'.self::TYPE.'".',
        ], $this->validatesRequests->messages());
    }
}
