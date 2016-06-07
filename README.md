# Laravel Property Bag   
[![Build Status](https://travis-ci.org/zachleigh/laravel-property-bag.svg?branch=master)](https://travis-ci.org/zachleigh/laravel-property-bag)
[![Latest Stable Version](https://poser.pugx.org/zachleigh/laravel-property-bag/version.svg)](//packagist.org/packages/zachleigh/laravel-property-bag) 
[![License](https://poser.pugx.org/zachleigh/laravel-property-bag/license.svg)](//packagist.org/packages/zachleigh/laravel-property-bag)  
##### Simple user settings for Laravel apps. 
  - Easily give your users settings
  - Simple to add additional settings as your app grows
  - Set default settings and limit setting values for security
  - Can be adapted to give other resources setting capability (to be improved in the future)
  - Fully configurable

### Contents
  - [About](#about)
  - [Install](#install)
  - [Usage](#usage)
  - [Advanced Configuration](#advanced-configuration)
  - [Contributing](#contributing)

### About
Laravel Property Bag gives your application resources savable, secure settings by using property bag database tables. A property bag is a single table with four columns: id, key, value, and resource_id. For example, a user settings property bag table would have id, key, value, and user_id columns. If the application has a user setting called 'email_frequency' with allowed values of either 'daily', 'weekly', or 'monthly' and the user with the id of 1 set the setting to 'daily', the database row would look like this: id, 'email_frequency', 'daily', 1.    
    
The benefit of using this kind of settings table, as opposed to say a json blob column on the user table, is that if in the future you decide to change a setting value, a simple database query can easily take care of it. In the previous example, if weekly emails are too much trouble and you want to change to bi-weekly, a single query could simply find all instances of 'email_frequency' that have a value of 'weekly' and change the value to 'bi-weekly'. 

### Install
##### 1. Install through composer     
```
composer require zachleigh/laravel-property-bag
```

##### 2. Register the service provider        
In Laravel's config/app.php file, add the service provider to the array with the 'providers' key.
```
LaravelPropertyBag\ServiceProvider::class
```

##### 3. Publish the migration      
```
php artisan vendor:publish --provider="LaravelPropertyBag\ServiceProvider"
```

##### 4. Publish the UserSettings directory to your app/ directory       
```
php artisan lpb:publish-user
```
This will create a UserSettings directory containing a UserPropertyBag model and a UserSettings class where you can configure how the package works.

##### 5. Run the migration      
```
php artisan migrate
```

### Usage
##### 1. Use the trait in the User model      
```php
...
use LaravelPropertyBag\Settings\HasSettings;

class User extends Model
{
    use HasSettings;

    ...
}
```

##### 2. Register your settings plus their allowed values and defaults     
After publishing the UserSettings directory (hopefully you did this above), register settings in the UserSettings class.
```php
protected $registeredSettings = [
    'example_setting' => [
        'allowed' => [true, false],
        'default' => false
    ]
];
```
Each setting must contain an array of allowed values and a default value.

##### 3. Set the setting from the user model or from the global settings() helper     
```php
$user->settings()->set(['example_setting' => false]);
// or
settings()->set(['example_setting' => false]);
```

Set multiple values at a time
```php
$user->settings()->set([
    'example_setting' => false,
    'another_setting' => 'grey'
]);
```

##### 4. Get the set value from the user model or from the global settings() helper     
```php
$value = $user->settings()->get('example_setting');
// or
$value = settings('example_setting');
```
If the value has not been set, the registered default value will be returned.

### Advanced Configuration
Laravel Property Bag gives you several ways to configure the package to fit your needs and wants.

###### I don't want to register settings as an array
Cool. I get it. Especially if you have dozens of settings, dealing with an array can be annoying. In UserSettings.php, add the setRegistered method.
```php
/**
 * Get the registered and default values from config or given array.
 *
 * @param array|null $registered
 *
 * @return Collection
 */
protected function setRegistered($registered)
{
    // Your code

    return $collection;
}
```
In this method, do whatever you want and return a collection of items that has the same structure as the registeredSettings array.
```php
'example_setting' => [
    'allowed' => [true, false],
    'default' => true
]
```

###### I don't want to call my table 'user_property_bag'
Before migrating, alter the migration and in UserPropertyBag.php, change the $table variable.
```php
/**
 * The table associated with the model.
 *
 * @var string
 */
protected $table = 'my_table_name';
```

### Contributing
Contributions are more than welcome. Fork, improve and make a pull request. For bugs, ideas for improvement or other, please create an [issue](https://github.com/zachleigh/laravel-property-bag/issues).
