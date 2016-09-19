# Laravel Property Bag   
[![Build Status](https://travis-ci.org/zachleigh/laravel-property-bag.svg?branch=master)](https://travis-ci.org/zachleigh/laravel-property-bag)
[![Latest Stable Version](https://poser.pugx.org/zachleigh/laravel-property-bag/version.svg)](//packagist.org/packages/zachleigh/laravel-property-bag) 
[![License](https://poser.pugx.org/zachleigh/laravel-property-bag/license.svg)](//packagist.org/packages/zachleigh/laravel-property-bag)  
##### Simple settings for Laravel apps. 
  - Easily give multiple resources settings
  - Simple to add additional settings as your app grows
  - Set default settings and limit setting values for security
  - Fully configurable

### Contents
  - [Upgrade Information](#upgrade-information)
  - [About](#about)
  - [Install](#install)
  - [Usage](#usage)
  - [Advanced Configuration](#advanced-configuration)
  - [Contributing](#contributing)

### Upgrade Information
Version 1.0.0 brings major changes to the package that make it incompatible with previous versions. The package was essentially rewritten making upgrade from 0.9.7 to 1.0.0 difficult at best. 

### About
Laravel Property Bag gives your application resources savable, secure settings by using a single database property bag table. The benefit of using this kind of settings table, as opposed to say a json blob column on the resource table, is that if in the future you decide to change a setting value, a simple database query can easily take care of it.

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

##### 4. Create a new settings config file for your resource.      
```
php artisan pbag:make {resource}
``` 
{resource} should be the name of the model you wish to add settings to. For example:
```
php artisan pbag:make User
```
This will create a Settings directory containing a UserSettings class where you can configure your settings for the User class.

##### 5. Run the migration      
```
php artisan migrate --provider="LaravelPropertyBag\ServiceProvider"
```

### Usage
##### 1. Use the trait in the model.      
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
After publishing the UserSettings file (hopefully you did this above), register settings in the UserSettings class.
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
$user->setSettings(['example_setting' => false]);
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
$value = $user->settings('example_setting');
```
If the value has not been set, the registered default value will be returned. Note that default values are not stored in the database in order to limit database size.

### Methods

##### get($key)
Get value for given key.
```
$value = $model->settings()->get($key);
```

##### set($array)
Set array keys to associated values. Values may be of any type. Returns Settings.     
If a value is not registered in the allowed values array, a LaravelPropertyBag\Exceptions\InvalidSettingsValue will be thrown.
```
$model->settings()->set([
  'key1' => 'value1',
  'key2' => 'value2'
]);
```

##### getDefault($key)
Get default value for given key.
```
$default = $model->settings()->getDefault($key);
```

##### allDefaults()
Get all the default values for registered settings. Returns collection.
```
$defaults = $model->settings()->allDefaults();
```

##### getAllowed($key)
Get allowed values for given key. Returns collection.
```
$allowed = $model->settings()->getAllowed($key);
```

##### allAllowed()
Get all allowed values for registered settings. Returns collection.
```
$allowed = $model->settings()->allAllowed();
```

##### isDefault($key, $value)
Return true if given value is the default value for given key.
```
$boolean = $model->settings()->isDefault($key, $value);
```

##### isValid($key, $value)
Return true if given value is allowed for given key.
```
$boolean = $model->settings()->isValid($key, $value);
```

##### all()
Return all settings for model. Returns collection.
```
$allExceptDefault = $model->settings()->all();
```

### Advanced Configuration
Laravel Property Bag gives you several ways to configure the package to fit your needs and wants.

###### I don't want to register settings as an array
Cool. I get it. Especially if you have dozens of settings, dealing with an array can be annoying. In the model settings config file, add the registeredSettings method.
```php
/**
 * Return a collection of registered settings.
 *
 * @return Collection
 */
public function registeredSettings()
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

###### I want to use dynamic allowed and default values.
No problem. Like in the above section, create your own registeredSettings method in the settings config file and return a collection of registered settings.
```
/**
 * Return a collection of registered settings.
 *
 * @return Collection
 */
public function registeredSettings()
{
    $allGroups = Auth::user()->allGroupNames();

    return collect([
        'default_group' => [
            'allowed' => $allGroups,
            'default' => $allGroups[0]
        ]
    ]);
}
```
The allGroupNames function simply returns an array of group names:
```
/**
 * Get array of all group names.
 *
 * @return array
 */
public function allgroupNames()
{
    return $this->groups->pluck('name')->all();
}
```

### Contributing
Contributions are more than welcome. Fork, improve and make a pull request. For bugs, ideas for improvement or other, please create an [issue](https://github.com/zachleigh/laravel-property-bag/issues).
