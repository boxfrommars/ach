## Achievments

Based on Laravel

## Changelog

    xu@calypso:~$ composer create-project laravel/laravel ach --prefer-dist // создаём проект
    xu@calypso:~$ cd ach // переходим в папку проекта
    xu@calypso:~/ach$ composer update
    xu@calypso:~/ach$ chmod a+rw app/storage -R // права на чтение для сервера. можно (и для продакшна -- нужно) просто разрешить для группы вебсервера

Теперь для запуска сервера достаточно выполнить 
    
    xu@calypso:~/ach$ php artisan serve // дополнительный параметр --port для указания конкретного порта 

### commit 4252a94 init

#### Настраиваем [определение окружения разработчика](http://laravel.com/docs/configuration#environment-configuration)

Next, we need to instruct the framework how to determine which environment it is running in. The default environment is always production. However, you may setup other environments within the bootstrap/start.php file at the root of your installation. In this file you will find an $app->detectEnvironment call. The array passed to this method is used to determine the current environment. You may add other environments and machine names to the array as needed.
    
    $env = $app->detectEnvironment(array(
        'local' => array('your-machine-name'),
    ));
    
In this example, 'local' is the name of the environment and 'your-machine-name' is the hostname of your server. On Linux and Mac, you may determine your hostname using the hostname terminal command.

#### Настраиваем [автодополнение](https://github.com/barryvdh/laravel-ide-helper)

    xu@calypso:~/ach$ composer require barryvdh/laravel-ide-helper:1.* // добавляем пакет для генерации файлов для автодополнения

After updating composer, add the `ServiceProvider` to the providers array in `app/config/app.php`

    'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider'
    
You can now re-generate the docs yourself (for future updates) in artisan

    xu@calypso:~/ach$ php artisan clear-compiled
    xu@calypso:~/ach$ php artisan ide-helper:generate // т.к. мы не описали соединение с бд, то выскочит ошибка Could not determine driver/connection for DB -- это нормально
    xu@calypso:~/ach$ php artisan optimize

#### Настраиваем [debugbar](https://github.com/barryvdh/laravel-debugbar)

    xu@calypso:~/ach$ composer require barryvdh/laravel-debugbar:dev-master

After updating composer, add the ServiceProvider to the providers array in `app/config/app.php`

    'Barryvdh\Debugbar\ServiceProvider',

You need to publish the assets from this package.

    xu@calypso:~/ach$ php artisan debugbar:publish

Note: The public assets can change overtime (because of upstream changes), it is recommended to re-publish them after update. You can also add the republish command in composer.json.

    "post-update-cmd": [
        "php artisan debugbar:publish"
    ],
  
### Commit 8cb95c8 ide helper, debugbar, local enviroment

#### Настраиваем базу данных

Создаём базу

    mysql> CREATE USER 'ach'@'localhost' IDENTIFIED BY 'ach';
    mysql> CREATE DATABASE ach;
    mysql> GRANT ALL PRIVILEGES ON ach . * TO 'ach'@'localhost';
    mysql> FLUSH PRIVILEGES;
    
Указываем в конфигурационном файле для нашего окружения `app/config/local/database.php` настройки подключения к базе данных

    'mysql' => array(
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'ach',
        'username'  => 'ach',
        'password'  => 'ach',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ),
    
Теперь можно перегенерировать хелпер для автодополнения

    xu@calypso:~/ach$ php artisan clear-compiled
    xu@calypso:~/ach$ php artisan ide-helper:generate
    xu@calypso:~/ach$ php artisan optimize
    
### Commit 9aa9238 Настройка подключения к БД

    