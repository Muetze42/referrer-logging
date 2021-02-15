#### For file method

```php
// config/filesystems.php
    'disks' => [
    //...
        'referrer-cache' => [
            'driver' => 'local',
            'root' => storage_path('app/referrer-cache'),
        ],
    //...
    ],
```
---

#### Publish the config:
`php artisan vendor:publish --provider="NormanHuth\ReferrerLogging\ReferrerLoggingServiceProvider" --tag=config`

---

#### Publish the migrations:
`php artisan vendor:publish --provider="NormanHuth\ReferrerLogging\ReferrerLoggingServiceProvider" --tag=migrations`

#### Add middleware to app/Http/Kernel.php for all routes
```php
    protected $middleware = [
        //..
        \NormanHuth\ReferrerLogging\Http\Middleware\ReferrerLog::class,
        //...
];
```
