<?php

namespace RealPage\JsonApi;

use Neomerx\JsonApi\Encoder\Encoder;

class EncoderServiceTest extends \PHPUnit\Framework\TestCase
{
    private $encoderService;

    public function setUp(): void
    {
        $config = [
            'schemas' => [],
            'encoders' => [
                'test-1' => [
                    'jsonapi' => true,
                    'meta' => [
                        'apiVersion' => '1.0',
                    ],
                    'encoder-options' => [
                        'options' => JSON_PRETTY_PRINT,
                        'urlPrefix' => '/',
                        'depth' => 512
                    ],
                ],
                'test-2' => [
                    'jsonapi' => [
                        'extensions' => 'bulk',
                    ],
                    'meta' => [
                        'apiVersion' => '1.0',
                    ],
                ],
            ]
        ];
        $this->encoderService = new EncoderService($config);
    }

    public function testGetDefaultEncoder()
    {
        $this->assertInstanceOf(Encoder::class, $this->encoderService->getEncoder());
    }

    public function testGetNamedEncoder()
    {
        $this->assertInstanceOf(Encoder::class, $this->encoderService->getEncoder('test-1'));
        $this->assertInstanceOf(Encoder::class, $this->encoderService->getEncoder('test-2'));
    }

    public function testGetUnconfiguredEncoder()
    {
        $this->expectException(\Exception::class);
        $this->encoderService->getEncoder('missing');
    }


    public function testEncoderIsSingleton()
    {
        $encoder = $this->encoderService->getEncoder();
        $this->assertSame($encoder, $this->encoderService->getEncoder());
    }

    public function testGetEncoderOptionsDefaults()
    {
        $method = $this->getMethod(EncoderService::class, 'getEncoderOptions');

        $service = new EncoderService([]);

        $encoderOptions = $method->invokeArgs($service, [[]]);

        $this->assertEquals(0, $encoderOptions['options']);
        $this->assertNull($encoderOptions['urlPrefix']);
        $this->assertEquals(512, $encoderOptions['depth']);
    }

    public function testGetEncoderOptions()
    {
        $method = $this->getMethod(EncoderService::class, 'getEncoderOptions');

        $service = new EncoderService([]);

        $configs = [
            [
                'options' => 0,
                'urlPrefix' => null,
                'depth' => 512
            ],
            [
                'options' => JSON_PRETTY_PRINT,
                'urlPrefix' => '/',
                'depth' => 1024
            ],
        ];

        foreach ($configs as $config) {
            $encoderOptions = $method->invokeArgs($service, [$config]);

            $this->assertEquals($config['options'], $encoderOptions['options']);
            $this->assertEquals($config['urlPrefix'], $encoderOptions['urlPrefix']);
            $this->assertEquals($config['depth'], $encoderOptions['depth']);
        }
    }

    public function testSetMetaAndJsonApiVersion()
    {
        $config = [
            'schemas' => [],
        ];
        $encoderService = new EncoderService($config);
        $encoder = $encoderService->getEncoder();
        $this->assertNull($this->getProperty($encoder, 'meta'));
        $this->assertNull($this->getProperty($encoder, 'jsonApiVersion'));
        $this->assertNull($this->getProperty($encoder, 'jsonApiMeta'));

        $config = [
            'schemas' => [],
            'jsonapi' => true,
            'meta' => [
                'apiVersion' => '1.0',
            ],
        ];
        $encoderService = new EncoderService($config);
        $encoder = $encoderService->getEncoder();
        $this->assertEquals('1.1', $this->getProperty($encoder, 'jsonApiVersion'));
        $this->assertNull($this->getProperty($encoder, 'jsonApiMeta'));
        $this->assertEquals($config['meta'], $this->getProperty($encoder, 'meta'));

        $config = [
            'schemas' => [],
            'jsonapi' => [
                'foo' => 'bar',
            ],
        ];
        $encoderService = new EncoderService($config);
        $encoder = $encoderService->getEncoder();
        $this->assertEquals('1.1', $this->getProperty($encoder, 'jsonApiVersion'));
        $this->assertEquals($config['jsonapi'], $this->getProperty($encoder, 'jsonApiMeta'));
    }

    protected static function getProperty($object, $name)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    protected static function getMethod($class, $name)
    {
        $class  = new \ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
