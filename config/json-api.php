<?php
return [
    'media-type' => 'application/vnd.api+json',

    // schemas are shared by all encoder instances
    'schemas' => [],

    // If jsonapi is set to true or is an array, withJsonApiVersion(EncoderInterface::JSON_API_VERSION) will be called.
    // Additionally, if jsonapi is an array, its contents will be passed as a parameter to withJsonApiMeta()
    'jsonapi' => true,

    // If meta is an array, it will be passed as $meta to $encoder->withMeta($meta).
    // 'meta' => [],

    // encoder-options are used when creating an Encoder instance
    // 'encoder-options' => [
    //     'options' => JSON_PRETTY_PRINT,
    //     'depth' => 512,
    //     'urlPrefix' => '',
    // ],

    // accept-header-policy configuration defines how to handle Accept header in requests.
    //
    // If accept-header-policy is set to 'default' or 'require', api will response with
    // 406 (Not Acceptable) if request has an Accept header, which does not match
    // configured media-type.
    //
    // If accept-header-policy is set to 'require', requests without an Accept header
    // will be responded by a 406 (Not Acceptable).
    //
    // If accept-header-policy is set to 'ignore', api will response all requests with a
    // json api document regardless of Accept header.
    'accept-header-policy' => 'require',
];
