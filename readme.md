## Achievments

Based on Laravel

## Changelog

```bash
xu@calypso:~$ composer create-project laravel/laravel ach --prefer-dist // создаём проект
xu@calypso:~$ cd ach // переходим в папку проекта
xu@calypso:~/ach$ composer update
xu@calypso:~/ach$ chmod a+rw app/storage -R // права на чтение для сервера. можно (и для продакшна -- нужно) просто разрешить для группы вебсервера
```

Теперь для запуска сервера достаточно выполнить 
    
```bash
xu@calypso:~/ach$ php artisan serve // дополнительный параметр --port для указания конкретного порта 
```

### commit 4252a94 init

#### Настраиваем определение окружения разработчика http://laravel.com/docs/configuration#environment-configuration

Next, we need to instruct the framework how to determine which environment it is running in. The default environment is always production. However, you may setup other environments within the bootstrap/start.php file at the root of your installation. In this file you will find an $app->detectEnvironment call. The array passed to this method is used to determine the current environment. You may add other environments and machine names to the array as needed.

```php
$env = $app->detectEnvironment(array(
    'local' => array('your-machine-name'),
));
```

In this example, 'local' is the name of the environment and 'your-machine-name' is the hostname of your server. On Linux and Mac, you may determine your hostname using the hostname terminal command.

#### Настраиваем автодополнение https://github.com/barryvdh/laravel-ide-helper

```bash
xu@calypso:~/ach$ composer require barryvdh/laravel-ide-helper:1.* // добавляем пакет для генерации файлов для автодополнения
```

After updating composer, add the `ServiceProvider` to the providers array in `app/config/app.php`

```php
'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider'
```

You can now re-generate the docs yourself (for future updates) in artisan

```bash
xu@calypso:~/ach$ php artisan clear-compiled
xu@calypso:~/ach$ php artisan ide-helper:generate // т.к. мы не описали соединение с бд, то выскочит ошибка Could not determine driver/connection for DB -- это нормально
xu@calypso:~/ach$ php artisan optimize
```

#### Настраиваем debugbar https://github.com/barryvdh/laravel-debugbar

```bash
xu@calypso:~/ach$ composer require barryvdh/laravel-debugbar:dev-master
```

After updating composer, add the ServiceProvider to the providers array in `app/config/app.php`

```php
'Barryvdh\Debugbar\ServiceProvider',
```

You need to publish the assets from this package.

```bash
xu@calypso:~/ach$ php artisan debugbar:publish
```

Note: The public assets can change overtime (because of upstream changes), it is recommended to re-publish them after update. You can also add the republish command in composer.json.

```php
"post-update-cmd": [
    "php artisan debugbar:publish"
],
```

### Commit 8cb95c8 ide helper, debugbar, local enviroment

#### Настраиваем базу данных

Создаём базу
```bash
mysql> CREATE USER 'ach'@'localhost' IDENTIFIED BY 'ach';
mysql> CREATE DATABASE ach;
mysql> GRANT ALL PRIVILEGES ON ach . * TO 'ach'@'localhost';
mysql> FLUSH PRIVILEGES;
```

Указываем в конфигурационном файле для нашего окружения `app/config/local/database.php` настройки подключения к базе данных
```php
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
```

Теперь можно перегенерировать хелпер для автодополнения
```bash
xu@calypso:~/ach$ php artisan clear-compiled
xu@calypso:~/ach$ php artisan ide-helper:generate
xu@calypso:~/ach$ php artisan optimize
```

### Commit 9aa9238 Настройка подключения к БД

#### Создаём миграцию для таблицы `users`

Для `users` по умолчанию уже идёт модель `User`, поэтому создавть вручную её не требуется
```bash
xu@calypso:~/ach$ php artisan migrate:make create_users_table
```  

создался файл `ach/app/database/migrations/YYYY_MM_DD_SSZZZZ_create_users_table.php`, описываем в нём миграцию
```php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	public function up()
	{
        Schema::create('users', function(Blueprint $table)
        {
            $table->string('name');
            $table->string('image')->nullable();
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
	}
	
	public function down()
	{
        Schema::table('users', function(Blueprint $table)
        {
            $table->drop();
        });
	}
}
```

Применяем миграцию
```bash
xu@calypso:~/ach$ php artisan migrate
```  

#### Создаём модель и миграцию для таблицы `achievments`

Создадим модель: файл `app/models/Achievment.php` со следующим содержимым
```php
class Achievment extends Eloquent {
	protected $table = 'achievments'; // в данном случае не обязательно точно указывать таблицу, так как её имя -- множественное число от имени класса модели
}
```

Теперь создаём миграцию
```bash
xu@calypso:~/ach$ php artisan migrate:make create_achievments_table
```  

создался файл `app/database/migrations/YYYY_MM_DD_SSZZZZ_create_achievments_table.php`, описываем в нём миграцию
```php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAchievmentsTable extends Migration {

	public function up()
	{
        Schema::create('achievments', function(Blueprint $table)
        {
            $table->increments('id');

            $table->integer('depth'); // глубина
            $table->integer('outlook'); // кругозор
            $table->integer('interaction'); // взаимодействие

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            $table->timestamps();
        });
	}

	public function down()
	{
        Schema::table('achievments', function(Blueprint $table)
        {
            $table->drop();
        });
	}
}
```

Применяем миграцию
```bash
xu@calypso:~/ach$ php artisan migrate
```  

#### Создаём связь многие ко многим для таблиц `users` и `achievments`

```bash
xu@calypso:~/ach$ php artisan migrate:make create_user_achievments_table
```  

создался файл `app/database/migrations/YYYY_MM_DD_SSZZZZ_create_user_achievments_table.php`, описываем в нём миграцию
```php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAchievmentsTable extends Migration {

	public function up()
	{
        Schema::create('user_achievments', function(Blueprint $table)
        {
            $table->integer('id_user')->unsigned();
            $table->integer('id_achievment')->unsigned();
            $table->boolean('is_approved')->default(false);

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_achievment')->references('id')->on('achievments');
        });
	}

	public function down()
	{
        Schema::table('user_achievments', function(Blueprint $table)
        {
            $table->drop();
        });
	}
}
```

Применяем миграцию
```bash
xu@calypso:~/ach$ php artisan migrate
```  

Добавляем в модель `Achievment` метод `->users()`
```php
// User >-< Achievment many to many relationship
public function users()
{
    return $this->belongsToMany('User', 'user_achievments', 'id_achievment', 'id_user');
}
```

А в модель `User` метод `->achievments()`
```php
// User >-< Achievment many to many relationship
public function achievments()
{
    return $this->belongsToMany('Achievment', 'user_achievments', 'id_user', 'id_achievment')->withPivot('is_approved');
}
```

Теперь мы можем получать связанные сущности, например:
```php
$user = User::find($id);
$achievments = $user->achievments; // все достижения данного пользователя
$achievment = $achievments[0];
$isApproved = $achievment->pivot->is_approved; // получаем данные из таблицы связи
```

или 
```php
$achievement = Achievment::find($id);
$users = $achievments->users; // все пользователи с данным достижением
```
Подробнее http://laravel.com/docs/eloquent#relationships

Текущая структура БД: docs/db/01.users_and_achievments.png

### Commit 8ecf5cc users and achievments: models, migrations and relationship

#### Создаём миграции, модели и связи для сущности `Group`

```bash
xu@calypso:~/ach$ php artisan migrate:make create_groups_table
```  

создался файл `app/database/migrations/YYYY_MM_DD_SSZZZZ_create_groups_table.php`, описываем в нём миграцию
```php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration {

	public function up()
	{
        Schema::create('groups', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('title');
            $table->string('description');
            $table->string('code');
            $table->string('image')->nullable();
            $table->timestamps();
        });
	}

	public function down()
	{
        Schema::table('groups', function(Blueprint $table)
        {
            $table->drop();
        });
	}
}
```

создаём модель `app/models/Group.php`
```php
class Group extends Eloquent {
}
```

Создаём связь многие ко многим между сущностями Group и User

```bash
xu@calypso:~/ach$ php artisan migrate:make create_user_groups_table
```  

создался файл `app/database/migrations/YYYY_MM_DD_SSZZZZ_create_user_groups_table.php`, описываем в нём миграцию

```php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGroupsTable extends Migration {

	public function up()
	{
        Schema::create('user_groups', function(Blueprint $table)
        {
            $table->integer('id_user')->unsigned();
            $table->integer('id_group')->unsigned();

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_group')->references('id')->on('groups');
        });
	}
	public function down()
	{
        Schema::table('user_groups', function(Blueprint $table)
        {
            $table->drop();
        });
	}
}
```

Применяем миграции
```bash
xu@calypso:~/ach$ php artisan migrate
```  

Добавляем в модель `Group` метод `->users()`
```php
// User >-< Achievment many to many relationship
public function users()
{
    return $this->belongsToMany('User', 'user_groups', 'id_group', 'id_user');
}
```

А в модель `User` метод `->groups()`
```php
// User >-< Group many to many relationship
public function groups()
{
    return $this->belongsToMany('Group', 'user_groups', 'id_user', 'id_group');
}
```

Создаём связь многие ко многим между сущностями Group и Achievment
```bash
xu@calypso:~/ach$ php artisan migrate:make create_achievment_groups_table
```  

создался файл `app/database/migrations/YYYY_MM_DD_SSZZZZ_create_achievment_groups_table.php`, описываем в нём миграцию

Добавляем в модель `Achievment` метод `->groups()`

```php
// Achievment >-< Group many to many relationship
public function groups()
{
    return $this->belongsToMany('Group', 'achievment_groups', 'id_achievment', 'id_group');
}
```

А в модель `Group` метод `->achievments()`
```php
// Group >-< Achievment many to many relationship
public function achievments()
{
    return $this->belongsToMany('Achievment', 'achievment_groups', 'id_group', 'id_achievment');
}
```

Текущая структура БД: docs/db/02.groups.png

#### Записываем тестовые данные

Добавляем папку `publig/img/user` для хранения аватарок пользователей

Добавляем папку `publig/img/achievment` для хранения картинок достижений

Кладём в каждую из этих папок файл `.gitignore` (чтобы картинки не попадали в репозиторий, но сами папки создавались) со следующим содержимым
```
*
!.gitignore
```

Добавляем пакет для генерации тестовых данных
```bash
xu@calypso:~/ach$ composer require fzaninotto/faker:1.4.*@dev
```

Создаём файл `app/database/seeds/AchievmentSeeder.php`
```php
class AchievmentSeeder extends Seeder
{
    /** @var \Faker\Generator */
    protected $_faker;

    public function __construct(){
        $this->_faker = Faker\Factory::create('ru_RU');
    }

    public function run()
    {
        // Удаляем предыдущие данные
        DB::table('user_groups')->delete();
        DB::table('achievment_groups')->delete();
        DB::table('user_achievments')->delete();
        DB::table('groups')->delete();
        DB::table('achievments')->delete();

        $userImageDirectory = 'public/img/user/';
        $achievmentImageDirectory = 'public/img/achievment/';

        $this->_cleanImageDirectory($userImageDirectory);
        $this->_cleanImageDirectory($achievmentImageDirectory);

        /** @var Group[] $groups */
        $groups = array();

        $groupsData = array(
            array('title' => 'мальчишки', 'code' => 'male'),
            array('title' => 'девчонки', 'code' => 'female'),
            array('title' => 'разработчики', 'code' => 'developer'),
            array('title' => 'дизайнеры', 'code' => 'designer'),
            array('title' => 'менеджеры', 'code' => 'manager'),
            array('title' => 'бородачи', 'code' => 'beard'),
        );

        foreach ($groupsData as $group) {
            $groups[$group['code']] = Group::create(array(
                'title' => $group['title'],
                'description' => '',
                'code' => $group['code'],
            ));
        }

        /** @var Achievment[] $achievments */
        $achievments = array();

        for ($i = 0; $i < 10; $i++) {
            $achievment = Achievment::create(array(
                'depth' => $this->_faker->numberBetween(0, 100),
                'outlook' => $this->_faker->numberBetween(0, 100),
                'interaction' => $this->_faker->numberBetween(0, 100),

                'title' => $this->_faker->sentence(3),
                'description' => $this->_faker->paragraph(),
                'image' => $this->_faker->image($achievmentImageDirectory, 100, 100, 'abstract', false),
            ));
            $this->_attachRandomGroups($achievment, $groups);
            $achievments[] = $achievment;
        }

        // Добавляем администратора

        /** @var User $user */
        $user = User::create(array(
            'name' => 'Dmitry Groza',
            'email' => 'boxfrommars@gmail.com',
            'password' => Hash::make('123123'),
            'image' => $this->_faker->image($userImageDirectory, 100, 100, 'people', false),
        ));

        $user->groups()->attach($groups['developer']->id);
        $user->groups()->attach($groups['male']->id);

        $achievmentIds = $this->_getRandomAchievmentIds($achievments);

        // чтобы добавить в таблицу связи данные (is_approved) нужно устроить такую конструкцию (idAch => array('is_approved' => true))
        $user->achievments()->sync(
            array_combine($achievmentIds, array_fill(0, count($achievmentIds), array('is_approved' => true)))
        );

        // Добавляем остальных тестовых пользователей
        for ($i = 0; $i < 16; $i++) {
            /** @var User $user */
            $user = User::create(array(
                'name' => $this->_faker->name,
                'email' => $this->_faker->email,
                'password' => Hash::make('123123'),
                'image' => $this->_faker->image($userImageDirectory, 100, 100, 'people', false),
            ));

            $achievmentIds = $this->_getRandomAchievmentIds($achievments);

            // чтобы добавить в таблицу связи данные (is_approved) нужно устроить такую конструкцию (idAch => array('is_approved' => true))
            $user->achievments()->sync(
                array_combine($achievmentIds, array_fill(0, count($achievmentIds), array('is_approved' => true)))
            );

            $this->_attachRandomGroups($user, $groups);
        }
    }

    /**
     * @param string $directory
     */
    protected function _cleanImageDirectory($directory)
    {
        if (File::isDirectory($directory)) {
            $items = new FilesystemIterator($directory);
            foreach ($items as $item) {
                if (!$item->isDir() && $item->getFilename() !== '.gitignore') {
                    File::delete($item->getRealPath());
                }
            }
        }
    }

    /**
     * @param Achievment[] $achievments
     * @return array
     */
    protected function _getRandomAchievmentIds($achievments) {
        return array_map(
            function ($item) {
                return $item->id;
            },
            $this->_faker->randomElements($achievments, rand(2, 6))
        );
    }

    /**
     * @param User|Achievment|\Illuminate\Database\Eloquent\Model $entity
     * @param Group[] $groups
     */
    protected function _attachRandomGroups($entity, $groups){
        $index = $this->_faker->randomNumber();

        $entity->groups()->attach(($index % 2 === 0) ? $groups['male']->id : $groups['female']->id);

        switch ($index % 3) {
            case 0:
                $entity->groups()->attach($groups['developer']->id);
                break;
            case 1:
                $entity->groups()->attach($groups['manager']->id);
                break;
            case 2:
                $entity->groups()->attach($groups['designer']->id);
                break;
        }

        if ($index % 6 === 0) {
            $entity->groups()->attach($groups['beard']->id);
        }
    }
}
```

а в файле `app/database/seeds/AchievmentSeeder.php` добавляем вызов генерации тестовых данных
```php
$this->call('AchievmentSeeder');
```

