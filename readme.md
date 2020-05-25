#Rocketmail for backetfy

##Instalación

```bash
composer require rocketfy/rocketmail

php artisan vendor:publish --provider="rocketfy\rocketMail\rocketMailServiceProvider"

php artisan migrate 
```

Añadir comando al scheduler de laravel en App\Console\Kernel.php
```php
protected function schedule(Schedule $schedule)
{

    ...
    $schedule->command('rocketmail:send-newsletter')->everyMinute();
    ...
}
```