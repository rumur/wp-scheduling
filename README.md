# `wp-scheduling` it's a package that provides a convenient way of working with a [WordPress Cron] (https://developer.wordpress.org/plugins/cron/) functionality.

## Package Installation  
```composer require rumur/wp-scheduling```  

### Minimum Requirements:
 - PHP: 7.2+
 - WordPress: 5.3+

### How to use it?

```php
<?php
// functions.php

// With this method the Scheduler will add intervals it uses.
\Rumur\WordPress\Scheduling\Schedule::registerIntoWordPress();
```

### Task as a `Class`.
In order to use a specific class as a Job, you can create any class you want, but this class must have `public` `handle` method   

```php
<?php

namespace App\Scheduling;

class HelloDolly
{
    protected $lyrics;

    public function __construct($lyrics)
    {
        $this->lyrics = $lyrics;
    }

    public function handle($args)
    {
        $id = $args['id'];
    }
}
```

```php
<?php

// You can add a class as a Job.
\Rumur\WordPress\Scheduling\Schedule::job(new App\Scheduling\HelloDolly('Lorem impsum'))
    // You can add args for the task, all these args will be injected to the `handle` method 
    ->with([ 
        'id' => 2020, 
        //...
    ])
    ->onSuccess(static function() {
        // Do something when the task is run successfully.
    })->onFailure(static function() {
        // Do something when the task is failed.
    })->onceInFiveMinutes()
       // To ping a url when the task is failed.
      ->pingOnFailure('https://domain.com/?ping=true&id=3790a0e1-3f51-4703-8962-8ed889e2cc7c&action=failed')
       // To ping a url when the task is successfully performed.
      ->pingOnSuccess('https://domain.com/?ping=true&id=3790a0e1-3f51-4703-8962-8ed889e2cc7c&action=success');
```

### Task as a `Closure`, the `call` method takes any `callable` instance.
```php
<?php
// You can add a Closure as a Job.
\Rumur\WordPress\Scheduling\Schedule::call(static function () {
    // Do something when the task is running.
})->onSuccess(static function ($task, $args) {
    // Do something when the task is run successfully.
})->onFailure(static function ($task, $args, $reason) {
    // Do something when the task is failed.
})
// You can add args for the task
->with([ 'id' => 2020, /*...*/ ])
// You can ping a url when the task is failed.
->pingOnFailure('https://domain.com/?ping=true&id=3790a0e1-3f51-4703-8962-8ed889e2cc7c&action=failed')
// You can ping a url when the task is successfully performed.
->pingOnSuccess('https://domain.com/?ping=true&id=3790a0e1-3f51-4703-8962-8ed889e2cc7c&action=success')
// Adds a recurrence into WordPress
->everyThirtyMinutes()
// Or you can run the task straight away, just to test it, if you need it.  
->now();
```
 
### [Available recurrence](#available-recurrence)
 
 | Method                        | Description                                                  |
 |----------------------------   |------------------------------------------------------------  |
 | `->everyMinute();`            | Run the task every minute                                    |
 | `->everyFiveMinutes();`       | Run the task every five minutes                              |
 | `->everyTenMinutes();`        | Run the task every ten minutes                               |
 | `->everyFifteenMinutes();`    | Run the task every fifteen minutes                           |
 | `->everyThirtyMinutes();`     | Run the task every thirty minutes                            |
 | `->hourly();`                 | Run the task every hour                                      |
 | `->daily();`                  | Run the task every day                                       |
 | `->weekly();`                 | Run the task every week                                      |
 | `->monthly();`                | Run the task every month                                     |
 | `->quarterly();`              | Run the task every quarter                                   |
 | `->yearly();`                 | Run the task every year                                      |
 | `->onceInMinute();`           | Run the task only once in minute                             |
 | `->onceInMinutes(45);`        | Run the task only once in 45 minutes                         |
 | `->onceInFiveMinutes();`      | Run the task only once in 5 minutes                          |
 | `->onceInTenMinutes();`       | Run the task only once in 10 minutes                         |
 | `->onceInFifteenMinutes();`   | Run the task only once in 15 minutes                         |
 | `->onceInThirtyMinutes();`    | Run the task only once in 30 minutes                         |
 | `->onceInHour();`             | Run the task only once in one hour                           |
 | `->onceInDay();`              | Run the task only once in one day                            |
 | `->onceInWeek();`             | Run the task only once in one week                           |
 | `->onceInMonth();`            | Run the task only once in one month                          |
 | `->onceInQuarter();`          | Run the task only once in a quarter                          |
 | `->onceInYear();`             | Run the task only once in a year                             |
 
 ### [Available methods](#available-methods)
 
 | Method                           | Description                                                       |
 |-----------------------------     |------------------------------------------------------------       |
 | `->onFailure(callable $thing);`  | Adds listeners for a task that will be run when it's failed.     |
 | `->onSuccess(callable $thing);`  | Adds listeners for a task that will be run when it's performed.   |
 | `->pingOnFailure(string $url);`  | Ping the url when the task is failed.                             |
 | `->pingOnSuccess(string $url);`  | Ping the url when the task is successfully performed.             |
 | `->with($data);`                 | The data for a task, that will be passed to `Closure` or `handle` method of the task. |
 
 ### How to add your own intervals?
 
 ```php
<?php
// functions.php

// Register the mandatory staff to WordPress.
add_action('init', static function() {
    // In Order to add your own intervals
    
    // The simple an straight way to do that it's just use a `addInterval` method.
    // Please Note that if you added an interval that already exists in a package,
    // it will be replaced by a new one during the register time,
    // however the system's ones that were added by a WordPress won't be touched and replaced. 
    \Rumur\WordPress\Scheduling\Schedule::addInterval('every-25-minutes', 25 * MINUTE_IN_SECONDS);
    
    // Or you can add in WordPress way as well 
    \Rumur\WordPress\Scheduling\Schedule::addInterval('every-45-minutes', [
        'interval' => 45 * MINUTE_IN_SECONDS,
        'display' => esc_html__('Every 45 Minutes'),
    ]);

    // With this method the Scheduler will add intervals it uses.
    \Rumur\WordPress\Scheduling\Schedule::registerIntoWordPress();
});
```
 
 ## License
 This package is licensed under the MIT License - see the [LICENSE.md](https://github.com/rumur/wp-scheduling/blob/master/LICENSE) file for details.