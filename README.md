# `wp-scheduling` 
it's a package that provides a convenient way of working with a [WordPress Cron](https://developer.wordpress.org/plugins/cron/) functionality.

## Package Installation  
```composer require rumur/wp-scheduling```

### Themosis 2.x
```php console vendor:publish --provider='Rumur\WordPress\Scheduling\WordPressScheduleServiceProvider'```

### Sage 10.x
```wp acorn vendor:publish --provider='Rumur\WordPress\Scheduling\WordPressScheduleServiceProvider'```

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
    
    // It can be an either `handle` or `__invoke` method
    public function handle($args)
    {
        $id = $args['id'];
    }
}
```

```php
<?php

// You can add a class as a Job.
\Rumur\WordPress\Scheduling\Schedule::job(
    new App\Scheduling\HelloDolly('Hello Rudy, well, hello Harry')
)
// You can add args for the task, all these args will be injected to the `handle` method 
->with([ 
    'id' => 2020, 
    //...
])
    
// You can add callbacks that will be executed when the task successfully performed.
->onSuccess(static function() {
    // Do something when the task is run successfully.
})

// You can add callbacks that will be executed when the task encounters an error.
->onFailure(static function() {
    // Do something when the task is failed.
})

// To ping a url when the task is failed.
->pingOnFailure('https://domain.com/?ping=true&id=3790a0e1-3f51-4703-8962-8ed889e2cc7c&action=failed')

// To ping a url when the task is successfully performed.
->pingOnSuccess('https://domain.com/?ping=true&id=3790a0e1-3f51-4703-8962-8ed889e2cc7c&action=success')

// Register the recurrence for a task.
->runOnceInFiveMinutes();
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

// Register the recurrence for a task and returns the configured task. 
->runEveryThirtyMinutes();
```

### Task as a function.
In order to set a task as function, you need to implement that function first and as far as the `Schedule::call` takes any `callable`
instance you just call it.
```php
<?php
// functions.php

function my_own_task(array $args) {
    // Do what you need to do.
}
```

```php
<?php
// *.php

// You can add a function as a Job.
\Rumur\WordPress\Scheduling\Schedule::call('my_own_task')->onSuccess(static function ($task, $args) {
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

// Register the recurrence for a task and returns the configured task. 
->runEveryThirtyMinutes();
```
 
### [Available recurrence](#available-recurrence)
Note that these methods should be the last one in the chain, because it registers all options that was build for a task.
 
 | Recurrence                       | Description                                                              |
 |-------------------------------   |------------------------------------------------------------------------  |
 | `->runEveryMinute();`            | Register the task to run every minute                                    |
 | `->runEveryFiveMinutes();`       | Register the task to run every five minutes                              |
 | `->runEveryTenMinutes();`        | Register the task to run every ten minutes                               |
 | `->runEveryFifteenMinutes();`    | Register the task to run every fifteen minutes                           |
 | `->runEveryThirtyMinutes();`     | Register the task to run every thirty minutes                            |
 | `->runHourly();`                 | Register the task to run every hour                                      |
 | `->runDaily();`                  | Register the task to run every day                                       |
 | `->runWeekly();`                 | Register the task to run every week                                      |
 | `->runMonthly();`                | Register the task to run every month                                     |
 | `->runQuarterly();`              | Register the task to run every quarter                                   |
 | `->runYearly();`                 | Register the task to run every year                                      |
 | `->runOnceInMinute();`           | Register the task to run only once in minute                             |
 | `->runOnceInMinutes(45);`        | Register the task to run only once in 45 minutes                         |
 | `->runOnceInFiveMinutes();`      | Register the task to run only once in 5 minutes                          |
 | `->runOnceInTenMinutes();`       | Register the task to run only once in 10 minutes                         |
 | `->runOnceInFifteenMinutes();`   | Register the task to run only once in 15 minutes                         |
 | `->runOnceInThirtyMinutes();`    | Register the task to run only once in 30 minutes                         |
 | `->runOnceInHour();`             | Register the task to run only once in one hour                           |
 | `->runOnceInDay();`              | Register the task to run only once in one day                            |
 | `->runOnceInWeek();`             | Register the task to run only once in one week                           |
 | `->runOnceInMonth();`            | Register the task to run only once in one month                          |
 | `->runOnceInQuarter();`          | Register the task to run only once in a quarter                          |
 | `->runOnceInYear();`             | Register the task to run only once in a year                             |
 | `->runNow();`                    | Runs the task right now. The method mimics WordPress behavior, designed for a testing purpose. |
 
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

### How to set one task for more than one event?
Every chained method returns a `PendingTask` instance and this task might be assigned for a several recurrence.

```php
<?php
$pendingTask = \Rumur\WordPress\Scheduling\Schedule::job(
    new App\Scheduling\HelloDolly('Hello Rudy, well, hello Harry')
);

$pendingTask->runOnceInWeek();
$pendingTask->runEveryMinute();
$pendingTask->runOnceInDays(33);
$pendingTask->runOnceInWeeks(2);
```

### How to resign a job?
```php
<?php

use Rumur\WordPress\Scheduling;

$scheduledTask = Scheduling\Schedule::job(
    new App\Scheduling\HelloDolly('Hello Rudy, well, hello Harry')
)->runOnceInWeek();

Scheduling\Schedule::resign($scheduledTask);
```
 
 ## License
 This package is licensed under the MIT License - see the [LICENSE.md](https://github.com/rumur/wp-scheduling/blob/master/LICENSE) file for details.