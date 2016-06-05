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
1. Install through composer
```
composer require zachleigh/laravel-property-bag
```
2. Register the service provider
In Laravel's config/app.php file, add the service provider to the array with the 'providers' key.
```
LaravelPropertyBag\ServiceProvider::class
```
3. Export the config file and migration
```
php artisan vendor:publish --provider="LaravelPropertyBag\ServiceProvider"
```
4. Run the migration
```
php artisan migrate
```
### Usage
1. Use the trait in the User model
```php
...
use LaravelPropertyBag\Settings\HasSettings;

class User extends Model
{
    use HasSettings;

    ...
}
```
2. Register your settings plus their allowed values and defaults.
In /config/laravel-property-bag.php
```php
'registered_user_settings' => collect([
    'example_setting' => [
        'allowed' => [true, false],
        'default' => true
    ]
])
```
3. Set the setting from the user model or from the global settings() helper
```php
$user->settings()->set('example_setting', false);
// or
settings()->set('example_setting', false);
```
4. Get the set value from the user model or from the global settings() helper
```php
$user->settings()->get('example_setting');
// or
settings('example_setting');
```
If the value has not been set, the registered default value will be returned.
