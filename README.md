# Json-Api For Lumen
Lumen helpers to help ease implementation of json-api standards.

[![Latest Stable Version](https://poser.pugx.org/realpage/json-api-for-lumen/v/stable)](https://packagist.org/packages/realpage/json-api-for-lumen) [![Total Downloads](https://poser.pugx.org/realpage/json-api-for-lumen/downloads)](https://packagist.org/packages/realpage/json-api-for-lumen) [![Latest Unstable Version](https://poser.pugx.org/realpage/json-api-for-lumen/v/unstable)](https://packagist.org/packages/realpage/json-api-for-lumen) [![License](https://poser.pugx.org/realpage/json-api-for-lumen/license)](https://packagist.org/packages/realpage/json-api-for-lumen) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/realpage/json-api-for-lumen/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/realpage/json-api-for-lumen/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/realpage/json-api-for-lumen/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/realpage/json-api-for-lumen/?branch=master)

## Install
Via Composer
``` bash
$ composer require realpage/json-api-for-lumen
```

### Lumen
You can register the service provider in `bootstrap/app.php`
``` php
$app->register(RealPage\JsonApi\Lumen\ServiceProvider::class);
```

## Usage

### Middleware

#### Enforcing Media Types

Fulfills the [server responsibilities](http://jsonapi.org/format/#content-negotiation) section of the json-api docs.  This should wrap all endpoints that are json-api spec compliant.

You can register the middleware in `bootstrap/app.php`
``` php
$app->routeMiddleware([
    'json-api.enforce-media-type' => RealPage\JsonApi\Middleware\EnforceMediaType::class,
]);
```

You can then use the middleware within your `routes.php` file
``` php
$app->get('quotes', ['middleware' => 'json-api.enforce-media-type', function () {
  //
}]);
```

### Validation

Use Laravel's validation capabilities to validate your requests and this package will handle converting failed validation to valid json-api responses.  

Add this entry to your exception handler

```php
use Illuminate\Http\Response;
use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;

// automatically encode exceptions as valid json-api json responses
if ($e instanceof JsonApiException) {
    return new Response(Encoder::instance()->encodeErrors($e->getErrors()), $e->getHttpCode(), [
            'Content-Type' => MediaTypeInterface::JSON_API_MEDIA_TYPE,
        ]);
}
```

Extending a few classes is all that's needed:

```php
use RealPage\JsonApi\Validation\ValidatesRequests;

class MyValidator extends ValidatesRequests
{
    public function rules() : array
    {
        return array_merge(parent::rules(), [
            'data.attributes.name' => 'required',
        ]);
    }
    
    public function messages() : array
    {
        return array_merge(parent::messages(), [
            'data.attributes.name.required' => 'A name is required',
        ]);
    }
}
```

```php
use RealPage\JsonApi\Requests\Request;
use RealPage\JsonApi\Validation\ValidatesRequests;

class MyRequest extends Request
{
    public function validator() : ValidatesRequests
    {
        return new MyValidator();
    }
}
```

In the controller you'd just call validate on the request:

```php
    public function store(MyRequest $request)
    {
        $request->validate();
        
        // do stuff
    }
```
## Encoder

Configures instances of `Neomerx\JsonApi\Encoder\Encoder` to encode your JSONAPI responses.

You can configure a default encoder like so in `config/json-api.php`:
``` php
<?php
return [
    'media-type' => 'application/vnd.api+json',
    'schemas' => [
        App\Models\MyModel::class => App\Schemas\MySchema::class,
    ],
    'jsonapi' => true,
    'encoder-options' => [
        'options' => JSON_PRETTY_PRINT,
    ],
    'meta' => [
        'apiVersion' => '1.1',
    ],
];
```

You can then retrieve the encoder in your Controller class:

``` php
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RealPage\JsonApi\EncoderService;
use App\Model\MyModel;

class MyController extends ApiController
{
    protected $status;
    protected $encoder;

    public function __construct(EncoderService $encoder)
    {
        $this->encoder = $encoder->getEncoder();
    }

    public function index(Request $request)
    {
        $data = [
            new MyModel(1, 'Some', "Attribute")
        ];

        return response($this->encoder->encodeData($data));
    }
}
```

Additional named encoders can be configured like:
``` php
<?php
return [
    'media-type' => 'application/vnd.api+json',
    'schemas' => [
        App\Models\MyModel::class => App\Schemas\MySchema::class,
    ],
    'jsonapi' => true,
    'encoder-options' => [
        'options' => JSON_PRETTY_PRINT,
    ],
    'meta' => [
        'apiVersion' => '1.1',
    ],
    'encoders' => [
        'custom' => [
            'jsonapi' => true,
            'encoder-options' => [
                'options' => JSON_PRETTY_PRINT,
            ],
            'meta' => [
                'apiVersion' => '2.0',
            ],
        ]
    ]
];
```

and then retrieved like:

```php
    $this->encoder = $encoder->getEncoder('custom');
```

## Change log
Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing
``` bash
$ composer test
```

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
