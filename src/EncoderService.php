<?php
declare(strict_types=1);

namespace RealPage\JsonApi;

use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Encoder\Encoder;

/**
 * Class EncoderService
 */
class EncoderService
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $encoders = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getEncoder(string $name = 'default')
    {
        if (!isset($this->encoders[$name])) {
            if ($name === 'default') {
                $config = $this->config;
            } elseif (isset($this->config['encoders'][$name])) {
                $config = $this->config['encoders'][$name];
            } else {
                throw new \Exception(sprintf('No configuration found for %s "%s"', Encoder::class, $name));
            }

            $encoder_options = isset($config['encoder-options']) && is_array($config['encoder-options']) ?
                $config['encoder-options'] :
                [];
            $options = $this->getEncoderOptions($encoder_options);

            $encoder = Encoder::instance($this->config['schemas'])
                ->withEncodeOptions($options['options'])
                ->withEncodeDepth($options['depth']);

            if ($options['urlPrefix']) {
                $encoder->withUrlPrefix($options['urlPrefix']);
            }

            if (isset($config['jsonapi'])) {
                if (is_array($config['jsonapi'])) {
                    $encoder->withJsonApiVersion(EncoderInterface::JSON_API_VERSION);
                    $encoder->withJsonApiMeta($config['jsonapi']);
                } elseif ($config['jsonapi'] === true) {
                    $encoder->withJsonApiVersion(EncoderInterface::JSON_API_VERSION);
                }
            }
            if (isset($config['meta']) && is_array($config['meta'])) {
                $encoder->withMeta($config['meta']);
            }

            $this->encoders[$name] = $encoder;
        }
        return $this->encoders[$name];
    }

    protected function getEncoderOptions(array $config)
    {
        $options = isset($config['options']) && is_int($config['options']) ?
            $config['options'] :
            0;
        $urlPrefix = isset($config['urlPrefix']) && is_string($config['urlPrefix']) ?
            $config['urlPrefix'] :
            null;
        $depth = isset($config['depth']) && is_int($config['depth']) ?
            $config['depth'] :
            512;

        return [
            'options' => $options,
            'urlPrefix' => $urlPrefix,
            'depth' => $depth
        ];
    }
}
