# Symfony custom requests

## What is it?
 
To help accomplish the "Thin controllers, fat models" ideal, this bundle allows you to create custom request objects that you can use for validating your input before it gets to the controller.

You can use these to make sure certain fields are set with certain dynamic values, or validate form data without having to add additional logic to your controller.

The aim is that your controller knows that it has the correct data and therefore can start working with it immediately, instead of having to double-check it's input is valid.

The new custom request object are defined via services, so can have any other services passed to them. This means your validation steps can use data-sources such as other APIs, databases, Elasticsearch, etc.

## How to use it

The first step is to update your `composer.json` with `dittto/symfony-custom-request`. You may need to also specify the an entry in the `repositories` for the github repository.

After you've added and installed this bundle, you'll need to add it to the `AppKernel.php` file:

```php
class AppKernel extends Kernel {
    public function registerBundles() {
        $bundles = [
            ...
            new \Dittto\CustomRequestBundle\DitttoCustomRequestBundle(),
```

Next we'll create our custom request. This is going to be a simple check that looks for a query string containing `token=allowed`. The test below is stored at `AppBundle/Request/TestRequest.php`.

```php
use Dittto\CustomRequestBundle\Request\AbstractRequestType;

class TestRequest extends AbstractRequestType {
    public function validate():bool {
        return $this->getOriginalRequest()->query->get('token') === 'allowed';
    }
}
```

Update the services for our new TestRequest. The `tag` is important as this enables us to know this is a custom request and can be used as a controller parameter.

```yaml
services:
    test_request:
        class: AppBundle\Request\TestRequest
        tags: [ { name: dittto.request } ]
```

Lastly, we need to tell our controller to use our custom request using it's parameters.

```php
class DefaultController extends Controller {
    public function indexAction(TestRequest $request):Response {
        ...
    }
}
```

## Filters

There is a filter chain available for altering how this code handles itself after the `validate()` method has been run. By default there is a filter in place that if a `GET` request fails, then a 400 exception is thrown.

You can add as many additional filters as you like to the chain to create automated responses based on certain request types.

New filters are added by creating a compatible filter (`RequestFilterInterface`) and adding a tag of `dittto.request_filter` when defining it as a service.

To make the filters run in a particular order, add a `slot` as a tag with a positive integer as it's value. These run in the order of lowest number first. If you assign 2 or more filters to the same slot, the first-assigned filter will take the slot and block any others from taking it's place.

### How to override default filters

Slots in local configs seem to always install before vendor-defined configs, so you can use the following to replace the default filter.

```yaml
services:
    override_exception_on_failed_get:
        class: Dittto\CustomRequestBundle\Request\Filter\NullFilterRequest
        tags: [ { name: dittto.request_filter, slot: 10 } ]
```
