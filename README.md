### Overview
This package makes the task of verifying eSewa payments easier.

### Installation
```
composer require nikhil-pandey/esewa
```

Or add the following to your `composer.json`

```json
"nikhil-pandey/esewa": "^1.0"
```

#### Service Provider
```php
'providers' => [
    NikhilPandey\Esewa\EsewaServiceProvider::class,
]
```
#### Facade
```php
'facades'   => [
    'Esewa'     => NikhilPandey\Esewa\Facades\Esewa::class,
]
```
#### Config File
You could easily deploy the config files by running
```php
php artisan vendor:publish
```

you should see the following 
```php
return [
    'debug' => true,
    'url' => 'https://esewa.com.np/epay/main',
    'debug_url' => 'https://dev.esewa.com.np/epay/main',
    'merchant_code' => '',
];
```
when `debug` is `true`, it verifies the data with test server of eSewa

change `debug` with `false` if you want to run in live mode

#### Uses

Suppose you have `Order` Model that stores your Order

Your order form will look like this
```html
<form action="{{ config('esewa.debug_url') }}" method="POST">
    <input value="{{ $order->total }}" name="tAmt" type="hidden">
    <input value="{{ $order->amount }}" name="amt" type="hidden">
    <input value="{{ $order->tax }}" name="txAmt" type="hidden">
    <input value="{{ $order->service_charge }}" name="psc" type="hidden">
    <input value="{{ $order->delivery_charge }}" name="pdc" type="hidden">
    <input value="{{ config('esewa.merchant_code') }}" name="scd" type="hidden">
    <input value="{{ $order->id }}" name="pid" type="hidden">
    <input value="{{ url('/success') }}" type="hidden" name="su">
    <input value="{{ url('/failure') }}" type="hidden" name="fu">
    <input value="Submit" type="submit">
</form>
```
The `tAmt`, `amt`, `scd`, `pid`, `su` and `fu` fields are required whereas `txAmt`, `psc` and `pdc` are optional.

Alternatively your form might look like
```html
<form action="{{ config('esewa.debug_url') }}" method="POST">
    <input value="{{ $order->total }}" name="tAmt" type="hidden">
    <input value="{{ $order->total }}" name="amt" type="hidden">
    <input value="{{ config('esewa.merchant_code') }}" name="scd" type="hidden">
    <input value="{{ $order->id }}" name="pid" type="hidden">
    <input value="{{ url('/success') }}" type="hidden" name="su">
    <input value="{{ url('/failure') }}" type="hidden" name="fu">
    <input value="Submit" type="submit">
</form>
```
Replace the `su` and `fu` with the success and failure callback url's

When the payment fails
```php
Route::get('failure', function () {
    // The user cancelled the payment
    // Show appropriate view/message
});
```

When the payment succeeds
```php
Route::get('success', function () {
    // Find the order from the order id
    $order = Order::find(request()->get('oid'));
    // Check the payment by providing the order id, amount and the reference id
    // Note: DONOT USE THE AMOUNT FROM THE REQUEST AS IT MIGHT HAVE BEEN ALTERED
    if (Esewa::with(['id' => $order->id, 'amount' => $order->total, 'reference_id' => request()->get('refId')])->isPaid()) {
        // Update the order
        // Show success message
    }

    // The payment was not completed. 
    // Show Error Message
});
```

Alternatively, if you have not registered the facade, you can also use
```php
app('esewa')->with(['id' => request()->get('refId'), 'amount' => 123, 'reference_id' => request()->get('refId')])->isPaid()
```

### License
This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
