# Laravel Property Bag   
[![Build Status](https://travis-ci.org/zachleigh/laravel-property-bag.svg?branch=master)](https://travis-ci.org/zachleigh/laravel-property-bag)
[![Latest Stable Version](https://poser.pugx.org/zachleigh/laravel-property-bag/version.svg)](//packagist.org/packages/zachleigh/laravel-property-bag) 
[![License](https://poser.pugx.org/zachleigh/laravel-property-bag/license.svg)](//packagist.org/packages/zachleigh/laravel-property-bag)  
##### Simple user settings for Laravel apps. 
  - Easily give your users settings
  - Simple to add additional settings as your app grows
  - Set default settings and limit setting values for security
  - Can be adapted to give other resources setting capability

### Install
##### Install through composer
```
composer require zachleigh/laravel-property-bag
```

##### Register the service provider
In Laravel's config/app.php file, add the service provider to the array with the 'providers' key.
```
LaravelPropertyBag\ServiceProvider::class
```

##### Export the config file and migration
```
php artisan vendor:publish --provider="LaravelPropertyBag\ServiceProvider"
```

##### Run the migration
```
php artisan migrate
```

### Usage
##### Use the trait in the User model
```php
...
use LaravelPropertyBag\Settings\HasSettings;

class User extends Model
{
    use HasSettings;

    ...
}
```

##### Register your settings plus their allowed values and defaults
In /config/laravel-property-bag.php, register settings under the registered_user_settings key.
```php
'registered_user_settings' => collect([
    'example_setting' => [
        'allowed' => [true, false],
        'default' => true
    ]
])
```

##### Set the setting from the user model or from the global settings() helper
```php
$user->settings()->set(['example_setting' => false]);
// or
settings()->set(['example_setting' => false]);
```

##### Set multiple values at a time
```
$user->settings()->set([
    'example_setting' => false,
    'another_setting' => 'grey'
]);
```

##### Get the set value from the user model or from the global settings() helper
```php
$value = $user->settings()->get('example_setting');
// or
$value = settings('example_setting');
```
If the value has not been set, the registered default value will be returned.
