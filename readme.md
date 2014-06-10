## Achievments

### Requirements

* PHP >= 5.4 + Mcrypt
* Mysql
* Conposer
* Git (для установки готового приложения)

Вы можете настроить Homestead -- виртуальную машину, которая позволит вам на любой системе (Windows, Mac, Linux) развернуть девелоперское окружение, 
включающее Ubuntu 14.04, Nginx, MySQL, PostgreSQL, Redis, Memcached и многое другое. Причём вам даже не придётся самим настраивать сервер, 
а добавление новых сайтов происходит добавлением двух строчек в конфигурационном файле. 
Подробнее см. https://github.com/boxfrommars/ach/blob/master/docs/homestead.md 

### Установка готового приложения

    xu@calypso:~$ git clone https://github.com/boxfrommars/achievments-laravel.git
    xu@calypso:~$ cd achievments-laravel/
    xu@calypso:~$ composer update
    xu@calypso:~$ chmod a+rw app/storage -R // папка для хранения логов, кеша и всего такого

    // создаём бд (если изменили здесь параметры бд, то меняем их в кофигурации в файле app/config/database.php)
    mysql> CREATE USER 'ach'@'localhost' IDENTIFIED BY 'ach';
    mysql> CREATE DATABASE ach;
    mysql> GRANT ALL PRIVILEGES ON ach . * TO 'ach'@'localhost';
    mysql> FLUSH PRIVILEGES;

    xu@calypso:~$ php artisan migrate
    xu@calypso:~$ php artisan db:seed // тестовые данные, чтобы обновить миграции и данные: php artisan migrate:refresh --seed

    xu@calypso:~$ php artisan serve --port 8444 // запускаем сервер

## Разработка приложения

Для начала создадим с помощью `composer` проект, и дадим серверу права на запись и чтение папки `app/storage`

```bash
xu@calypso:~$ composer create-project laravel/laravel ach --prefer-dist // создаём проект
xu@calypso:~$ cd ach // переходим в папку проекта
xu@calypso:~/ach$ composer update
xu@calypso:~/ach$ chmod a+rw app/storage -R // права на чтение и запись для сервера. можно (и для продакшна -- нужно) просто разрешить для группы вебсервера
```

Теперь для запуска сервера достаточно выполнить (если вы сами настроили apache или nginx или используете Homestead, запускать сервер не нужно)
    
```bash
xu@calypso:~/ach$ php artisan serve // дополнительный параметр --port для указания конкретного порта 
```

> php artisan -- это набор консольных комманд поставляющихся с laravel, облегчающих разработку на laravel, весь список доступных комманд можно посмотерть выполнив
> `php artisan list` или на [странице документации](http://laravel.com/docs/artisan)
    

### commit 4252a94 init

#### Настраиваем определение окружения разработчика http://laravel.com/docs/configuration#environment-configuration

По умолчанию окружение -- `production`, а для разработки нам понадобится использовать откружение `local`. из коробки единственная разница
между `local` и `production` только в том, что для локал выставлен параметр конфигурации `debag => true` и вынесен отдельный конфиг для подключения к бд, 
в котором сконфигурирован доступ к бд по умолчанию в homestead, но мы можем переписать для локального окружения любой параметр, также можно 
добавлять собственные окружения. Подробнее см. [документацию](http://laravel.com/docs/configuration#environment-configuration)

Для работы в локальном окружении добавить в файле `bootstrap/start.php` в массиве передаваемом в  `->detectEnvironment` имя своего компьютера (в Linux/Mac определяется командой `hostname`)

```php
$env = $app->detectEnvironment(array(
    'local' => array('your-machine-name'),
));
```

#### Настраиваем автодополнение https://github.com/barryvdh/laravel-ide-helper

Так как в laravel используются 'фасады' ([подробнее](http://laravel.com/docs/facades)), то в вашей ide не будет работать автодополнение, 
а это значит, что вас ждут вечные муки. Благо есть пакет, который решает эту проблему. установим его

```bash
xu@calypso:~/ach$ composer require barryvdh/laravel-ide-helper:1.* // добавляем пакет для генерации файлов для автодополнения
```

Теперь добавим в массив провайдеров в файле `app/config/app.php` следующую строчку

```php
'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider'
```
> Подробнее о сервис-провайдерах см. http://laravel.com/docs/ioc#service-providers


теперь мы можем генерировать файл-хелпер для автодополнения, с помощью команды `artisan`: `php artisan ide-helper:generate`

```bash
xu@calypso:~/ach$ php artisan clear-compiled
xu@calypso:~/ach$ php artisan ide-helper:generate // т.к. мы не описали соединение с бд, то выскочит ошибка Could not determine driver/connection for DB -- это нормально
xu@calypso:~/ach$ php artisan optimize
```

#### Настраиваем debugbar https://github.com/barryvdh/laravel-debugbar

```bash
xu@calypso:~/ach$ composer require barryvdh/laravel-debugbar:dev-master
```

Теперь добавим в массив провайдеров в файле `app/config/app.php` следующую строчку

```php
'Barryvdh\Debugbar\ServiceProvider',
```

Добавим ресурсы этого пакета (стили, js)

```bash
xu@calypso:~/ach$ php artisan debugbar:publish
```

В [документации](https://github.com/barryvdh/laravel-debugbar) к пакету автор отмечает, что ресурсы могут меняться и советует добавить в ваш `composer.json` в `post-update` следующую строчку: 

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

Теперь можно перегенерировать хелпер для автодополнения, сейчас ошибок не будет, а заодно хелпер сгенерируется с поддержкой автодополнения для фасадов связанных с БД, таких как Schema, Blueprint.
```bash
xu@calypso:~/ach$ php artisan clear-compiled
xu@calypso:~/ach$ php artisan ide-helper:generate
xu@calypso:~/ach$ php artisan optimize
```

### Commit 9aa9238 Настройка подключения к БД

#### Создаём миграцию для таблицы `users`

Для `users` по умолчанию уже идёт модель `User`, поэтому создавть вручную её не требуется

Создадим миграцию для создания таблицы users
```bash
xu@calypso:~/ach$ php artisan migrate:make create_users_table
```  

создался файл `ach/app/database/migrations/YYYY_MM_DD_SSZZZZ_create_users_table.php`, описываем в нём миграцию
```php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

    // то что происходит при 'накатывании' миграции, в нашем случае создание таблицы
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
            $table->timestamps(); // стандартные timestamps: created_at, updated_at
            $table->softDeletes(); // 'мягкое' удаление, колонка  deleted_at
        });
	}
	
    // то что происходит при 'откате' миграции, в нашем случае удаление таблицы
	public function down()
	{
        Schema::table('users', function(Blueprint $table)
        {
            $table->drop();
        });
	}
}
```
отметим использование 'мягкого' удаления, 
при удалении с помощью `$user->delete()` сама запись из таблицы не удаляется, а лишь помечается, как удалённая, при этом в исключается 
из результатов запросов вида `User:all()` и тому подобных. подробнее см. http://laravel.com/docs/eloquent#soft-deleting

вообще говоря миграции нужны не только для создания таблиц, но и для любых других действий с ними: например, для добавления/удаления колонок, 
изменения колонок и даже для добавления изменения данных. Подробнее см. http://laravel.com/docs/migrations и https://laracasts.com/index/migration

Применяем миграцию
```bash
xu@calypso:~/ach$ php artisan migrate
```  

#### Создаём модель и миграцию для таблицы `achievments`

Тут придётся создать модель вручную, то есть создать файл `app/models/Achievment.php` со следующим содержимым
```php
class Achievment extends Eloquent {
	protected $table = 'achievments'; // в данном случае не обязательно указывать таблицу, так как её имя -- множественное число от имени класса модели и магия laravel всё сделала бы за вас
}
```


Теперь создаём миграцию точно так же как и для `users`
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

для этого нам опять необходимо создать миграцию, которая создат нам таблицу для связей между нашими сущностями, заметим, что мы добавляем данные
в таблицу связи -- колонку `is_approved`, которая показывает, было ли одобрено достижение администратором, о работе с этими данными будет рассказано несколько ниже

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

Текущая структура БД: ![users and achievments](https://github.com/boxfrommars/ach/raw/master/docs/db/01.users_and_achievments.png)

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

Текущая структура БД: ![groups](https://github.com/boxfrommars/ach/raw/master/docs/db/02.groups.png)

Теперь мы можем перегенерировать хелпер-файл для поддержки автодополнения в созданных моделей, для этого используется команда `php artisan ide-helper:models`

```bash
xu@calypso:~/ach$ php artisan clear-compiled
xu@calypso:~/ach$ php artisan ide-helper:generate
xu@calypso:~/ach$ php artisan optimize
```

генератор спросит, добавлять ли докблок с методами и свойствами в каждый файл модели или описать модели в стандартном файле `_ide_helper.php`,
я предпочитаю добавлять в модели.


#### Записываем тестовые данные

Добавляем папку `publig/img/user` для хранения аватарок пользователей

Добавляем папку `publig/img/achievment` для хранения картинок достижений

Кладём в каждую из этих папок файл `.gitignore` (чтобы картинки не попадали в репозиторий, но сами папки создавались) со следующим содержимым
```
*
!.gitignore
```

Добавляем очень удобный пакет для генерации тестовых данных ([документация](https://github.com/fzaninotto/Faker))
```bash
xu@calypso:~/ach$ composer require fzaninotto/faker:1.4.*@dev
```

Создаём файл `app/database/seeds/AchievmentSeeder.php`
```php
<?php

class AchievmentSeeder extends Seeder
{
    /** @var \Faker\Generator */
    protected $_faker;

    public function __construct()
    {
        $this->_faker = Faker\Factory::create('ru_RU');
    }

    public function run()
    {
        $usersCount = 16;
        $achievmentsCount = 10;
        $beardFrequency = 4; // на сколько мальчишек один бородач
        $defaultPassword = '123123';

        // а вот и все группы
        $groupsData = array(
            array('title' => 'мальчишки', 'code' => 'male'),
            array('title' => 'девчонки', 'code' => 'female'),
            array('title' => 'разработчики', 'code' => 'developer'),
            array('title' => 'дизайнеры', 'code' => 'designer'),
            array('title' => 'менеджеры', 'code' => 'manager'),
            array('title' => 'бородачи', 'code' => 'beard'),
        );

        $userImageDirectory = 'public/img/user/';
        $achievmentImageDirectory = 'public/img/achievment/';

        // Удаляем предыдущие данные
        DB::table('user_groups')->delete();
        DB::table('achievment_groups')->delete();
        DB::table('user_achievments')->delete();
        DB::table('groups')->delete();
        DB::table('achievments')->delete();
        DB::table('users')->delete();

        $this->_cleanImageDirectory($userImageDirectory);
        $this->_cleanImageDirectory($achievmentImageDirectory);

        /** @var Group[] $groups */
        $groups = array();

        foreach ($groupsData as $group) {
            $groups[$group['code']] = Group::create(array(
                'title' => $group['title'],
                'description' => '',
                'code' => $group['code'],
            ));
        }

        /** @var Achievment[] $achievments */
        $achievments = array();

        for ($i = 0; $i < $achievmentsCount; $i++) {
            $achievment = Achievment::create(array(
                'depth' => $this->_faker->numberBetween(0, 100),
                'outlook' => $this->_faker->numberBetween(0, 100),
                'interaction' => $this->_faker->numberBetween(0, 100),

                'title' => $this->_faker->sentence(3),
                'description' => $this->_faker->paragraph(),
                'image' => $this->_faker->image($achievmentImageDirectory, 100, 100, 'abstract', false),
            ));

            $achievment->groups()->sync($this->_getRandomIds($groups));
            $achievments[] = $achievment;
        }

        // Добавляем администратора
        /** @var User $user */
        $userData = array(
            'name' => 'Dmitry Groza',
            'email' => 'boxfrommars@gmail.com',
            'password' => Hash::make($defaultPassword), // см. http://laravel.com/docs/security#storing-passwords
            'image' => $this->_faker->image($userImageDirectory, 100, 100, 'people', false),
        );
        $userGroupIds = array($groups['developer']->id, $groups['male']->id);
        $userAchievmentIds = $this->_getRandomIds($achievments, 4);

        $this->_createUser($userData, $userGroupIds, $userAchievmentIds);

        // Добавляем остальных тестовых пользователей
        for ($i = 0; $i < $usersCount; $i++) {

            $gender = $this->_faker->randomElement(array('male', 'female'));

            /** @var User $user */
            $userData = array(
                'name' => mb_convert_case($this->_faker->name($gender), MB_CASE_TITLE), // у фейкера нехорошие имена/фамилии, то с большой буквы, то с маленькой. приводим к нормальному виду
                'email' => $this->_faker->email,
                'password' => Hash::make($defaultPassword),
                'image' => $this->_faker->image($userImageDirectory, 100, 100, 'people', false),
            );

            $position = $this->_faker->randomElement(array('developer', 'manager', 'designer'));
            $userGroupIds = array($groups[$gender]->id, $groups[$position]->id);
            $userAchievmentIds = $this->_getRandomIds($achievments, 4);

            // добавляем немного бородачей
            if ($gender === 'male' && rand(1, $beardFrequency) === 1) {
                array_push($userGroupIds, $groups['beard']->id);
            }

            $this->_createUser($userData, $userGroupIds, $userAchievmentIds);
        }
    }

    /**
     * @param array $data данные, которые прямиком отправляются в User::create($data)
     * @param array $groupIds массив id групп
     * @param array $achievmentIds массив id достижений
     */
    protected function _createUser($data, $groupIds, $achievmentIds)
    {
        $user = User::create($data);
        $user->groups()->sync($groupIds);

        if (!empty($achievmentIds)) { // тут, в отличии от groups нужна проверка, т.к. array_fill вторым параметром  принимает только integer > 0
            $user->achievments()->sync(
                array_combine($achievmentIds, array_fill(0, count($achievmentIds), array('is_approved' => true)))
            );
        }
    }

    /**
     * @param string $directory директория для очищения
     *
     * очищаем директорию, при этом не удалянм в ней файл .gitignore
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
     * @param Eloquent[] $entities массив объектов со свойством id
     * @param integer    $maxCount максимальное число возвращаемых id
     * @return array массив id случайно выбранных объектов из списка
     */
    protected function _getRandomIds($entities, $maxCount = null)
    {
        if ($maxCount === null) {
            $maxCount = count($entities);
        }

        return array_map(
            function ($item) {
                return $item->id;
            },
            $this->_faker->randomElements($entities, rand(1, $maxCount))
        );
    }
}
```

а в файле `app/database/seeds/AchievmentSeeder.php` добавляем вызов генерации тестовых данных
```php
$this->call('AchievmentSeeder');
```

### Commit b9c4bc7 test data

#### Роутинг и контроллеры

На данный момент нужно реализовать следующие пути

 * `/` список достижений
 * `/users` список пользователей
 * `/users/{id}` страница пользователя, где id -- идентификатор пользователя
 * `/achievments` тоже список достижений (?)
 * `/achievments/{id}` страница достижения

в файле `app/routes.php` удаляем текущий роут для пути `/` и прописываем наши роуты

```php
Route::get('/', 'AchievmentController@getMain');

Route::get('users', 'AchievmentController@getUsers');
Route::get('users/{id}', 'AchievmentController@getUser');

Route::get('achievments', 'AchievmentController@getAchievments');
Route::get('achievments/{id}', 'AchievmentController@getAchievment');
```

Так как страниц не очень много, то все их заносим в один контроллер `AchievmentController`

Создаём файл `app/controllers/AchievmentController.php` со следующим содержимым
(если действие контроллера возвращает массив, то приложение возвращает ответ в формате `json` с соответствующим 
хедером `application/json`, при это eloquent модели тоже корректно преобразовываются, с исключенными `hidden` полями, 
которые мы установили в соответствующей модели, как,например, поле `password`)

```php
<?php

class AchievmentController extends BaseController
{

    public function getMain()
    {
        return ['url' => '/'];
    }

    public function getUsers()
    {
        $users = User::all();

        return $users;
    }

    public function getUser($id)
    {
        $user = User::find($id);
        if ($user === null) {
            App::abort(404, 'Page not found');
        }

        return $user;
    }

    public function getAchievments()
    {
        $achievments = Achievment::all();

        return $achievments;
    }

    public function getAchievment($id)
    {
        $achievment = Achievment::find($id);
        if ($achievment === null) {
            App::abort(404, 'Page not found');
        }

        return $achievment;
    }
}
```
Теперь можно открыть соответствующие страницы в браузере и убедиться, что всё работает.

### Commit 4ef320b routing & controllers

#### Добавляем виды

Сначала создадим общий лайаут `app/views/layout.blade.php`, в котором в месте, где будет выводиться контент вставляем `@yield('content')` (см. [документацию к шаблонизотру blade](http://laravel.com/docs/templates#blade-templating)). Заодно удалим ненужные нам `app/views/hello.php` и `app/views/email`. 

```php
...
<!-- Begin page content -->
<div class="container">
    @yield('content')
</div>
...
```

Теперь создадим отдельные страницы (и соответствующие папки) для каждого действия

`app/views/user/user_list.blade.php`

```php
@extends('layout')

@section('content')
    <h1>Пользователи</h1>
    @foreach ($users as $user)
    <div class="media">
        <div class="pull-left">
            <img class="img-thumbnail" src="/img/user/{{{ $user->image }}}" />
        </div>
        <div class="media-body">
            <h4 class="media-heading"><a href="/users/{{{ $user->id }}}">{{{ $user->name }}}</a></h4>
            <ul class="list-inline">
                @foreach ($user->achievments as $achievment)
                <li><a href="/achievments/{{{ $achievment->id }}}" title="{{{ $achievment->title }}}"><img class="img-thumbnail achievment-image-icon" src="/img/achievment/{{{ $achievment->image }}}" /></a></li>
                @endforeach
            </ul>

            <ul class="list-inline">
                @foreach ($user->groups as $group)
                <li><a class="label label-group label-{{{ $group->code }}}" href="/groups/{{{ $group->id }}}">{{{ $group->title }}}</a></li>
                @endforeach
            </ul>
        </div>
    </div>
    @endforeach
@stop
```

По аналогии создадим виды `app/views/user/user_show.blade.php`, `app/views/achievment/achievment_show.blade.php`, `app/views/achievment/achievment_list.blade.php` 

Изменим контроллер `app/controllers/AchievmentController.php`, чтобы он начал работать с созданными видами:

```php
<?php

class AchievmentController extends BaseController
{

    public function getMain()
    {
        return View::make('layout');
    }

    public function getUsers()
    {
        $users = User::all();

        return View::make('user.user_list', array('users' => $users));
    }

    public function getUser($id)
    {
        $user = User::find($id);
        if ($user === null) {
            App::abort(404, 'Page not found');
        }

        return View::make('user.user_show', array('user' => $user));
    }

    public function getAchievments()
    {
        $achievments = Achievment::all();

        return View::make('achievment.achievment_list', array('achievments' => $achievments));
    }

    public function getAchievment($id)
    {
        $achievment = Achievment::find($id);
        if ($achievment === null) {
            App::abort(404, 'Page not found');
        }

        return View::make('achievment.achievment_show', array('achievment' => $achievment));
    }
}
```

Теперь настало время заглянуть в наш debugbar.

А там мы увидим, что страница `/users` делает 35 (жуть) запросов (при 16 пользователях) к базе данных. 
Дело в том, что каждый раз, когда мы обращаемся к связанным сущностям eloquent-модели, выполняется запрос к базе, получающий эти связанные сущности.
Но это легко исправить, достаточно заменить:
* `User::all()` на `User::with('achievments', 'groups')->get()`
* `Achievment::all()` на `Achievment::with('users', 'groups')->get()`
* `Achievment::find($id)` на `Achievment::with('users', 'groups')->find($id)` (при запросе одной моделей, нет плюсов в использовании with -- и так и так выполняются три запроса, но это понадобится нам чуть ниже)
* `User::find($id)` на `User::with('achievments', 'groups')->find($id)`

и мы получим всего три запроса (для users: выборка всех пользователей, выборка всех групп этих пользователей и выборка всех достижений этих пользователей)

Теперь открываем страницу `/achievments/{id}` и видим, что даже после замены выполняются десятки запросов. Это понятно, мы подгрузили только 
связанных пользователей, но не их связанные группы и достижения, поэтому для каждого пользователя в списке, будет выполняться ещё по два запроса. 
И кажется, что вот тут-то всё кончено и придётся писать что-то громоздкое собственными руками. Но это не так :) В laravel и для этого есть немного магии, 
а именно вот такая конструкция:

```php
Achievment::with('users.groups', 'user.achievments', 'groups')->find($id)
```

Итого пять запросов независимо от количества пользователей, привязанных к данному достижению.

### Commit 48cec17

#### Аутентификация

По умолчанию с laravel уже идёт модель `User` (`app/models/User.php`), которая довольно просто используется для аутентификации с помощью  
Eloquent драйвера аутентификации. Также мы выше уже создали таблицу `users` со всеми необходимыми полями. Теперь для аутентификации пользователя,
нам достаточно в контроллере, в котором будет происходить логин, проверить (предварительно, конечно, полчив $email и $password из формы):

```php
if (Auth::attempt(array('email' => $email, 'password' => $password)))
{
    // успех
} else {
    // неудача
}
```
мы используем колонку `email` для аутентификации, но вы может использовать и другую колонку, например, `username`

Создадим контроллер, в котором опишем три действия:
 
 ```php
class AuthController extends BaseController
{
    public function getLogin()
    {
        return View::make('auth.login');
    }

    public function postLogin()
    {
        // валидатор проверяющий заполнены ли поля формы
        $validator = Validator::make(Input::all(), array(
            'email' => 'required',
            'password' => 'required'
        ));

        $credentials = array(
            'email' => Input::get('email'),
            'password' => Input::get('password'),
        );

        // если Auth::attempt вторым параметром принимает true, то приложение запоминает пользователя на неопределённое время
        // подробнее см. http://laravel.com/docs/security#authenticating-users
        $isRemember = Input::get('is_remember');

        if ($validator->passes() && Auth::attempt($credentials, $isRemember)) {
            // в случае успешной аутентификации редиректим на главную
            return Redirect::intended($path);
        } else {
            // в случае неуспешной -- редиректим назад на форму, заполняя поля введёнными данными, также записываем в flash-сообщение ошибки
            return Redirect::back()
                ->withInput()
                ->with('errors', array('Неправильный логин или пароль'));
        }
    }

    public function logout()
    {
        Auth::logout();

        return Redirect::to('/');
    }
}
 ```
`Input::all()`, `Input::get($fieldName)` введённые пользователем данные, см. http://laravel.com/docs/requests#basic-input
`Validator::make` -- валидатор для данных, см. http://laravel.com/docs/validation#basic-usage
`Redirect::back()`, `Redirect::to($path)`, `Redirect::intended($path)` -- редиректы, см. http://laravel.com/docs/responses#redirects, 
о `Redirect::intended($path)`будет написано чуть ниже 

Теперь создадим вид для страницы логина `auth/login.blade.php` (точно так же, как и раньше наследуемся от общего лайаута и переписываем
секцию content), подробнее о работе с формами см. http://laravel.com/docs/html 

```php
@extends("layout")

@section("content")

<h3>Вход</h3>

{{ Form::open(array('role' => 'form', 'class' => 'form-horizontal')) }}
<div class="form-group">
    {{ Form::label("email", "Email", array('class' => 'col-sm-2 control-label')) }}
    <div class="col-sm-4">
        {{ Form::text("email", Input::old("email"), array('class' => 'form-control')) }}
    </div>
</div>
<div class="form-group">
    {{ Form::label("password", "Пароль", array('class' => 'col-sm-2 control-label')) }}
    <div class="col-sm-4">
        {{ Form::password("password", array('class' => 'form-control')) }}
    </div>
</div>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-4">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="is_remember"> Запомнить меня
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-4">
        {{ Form::submit("Войти", array('class' => 'btn btn-default')) }}
    </div>
</div>
{{ Form::close() }}

@stop
```

И добавим в `app/routes.php`:

```php
Route::get('login', 'AuthController@getLogin');
Route::post('login', 'AuthController@postLogin');
Route::get('logout', 'AuthController@logout');
```

Также изменим наш общий лайаут, добавив функциональность для вывода любых ошибок переданных во флеш-сообщениях 
(см. http://laravel.com/docs/session#flash-data и http://laravel.com/docs/responses#redirects).
Для этого добавим 

```php
@if (Session::has('errors'))
    @foreach (Session::get('errors') as $error)
        <div class="alert alert-danger">{{ $error }} <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>
    @endforeach
@endif
```

Теперь можно проверять работу формы входа и страницы выхода. 
Добавим в лайаут ссылки на вход для гостей и на страницу профиля и на выход для вошедших пользователей
 
```php
<ul class="nav navbar-nav navbar-right">
    @if (Auth::check())
        <li><a href="/my">Мои успехи</a></li>
        <li><a href="/logout">Выйти</a></li>
    @else
        <li><a href="/login">Войти</a></li>
    @endif
</ul>
```

`Auth::check()` -- проверяет, прошёл ли человек аутентификацию

Теперь можно устроить страницу `/my` пользователя, воспользовавшись методом `Auth::user()`. Добавим соответствующий метод в `AchievmentController`

```php
public function getMy()
{
    /** @var User $user */
    $user = Auth::user();
    if (is_null($user)) App::abort(404, 'Page not found');

    // воспользуемся тем же видом, что и для страницы пользователя
    return View::make('user.user_show', array('user' => $user));
}
```

И добавим новый роут

```php
Route::get('my', array('before' => 'auth', 'uses' => 'AchievmentController@getMy'));
```

Тут мы воспользовались встроенным фильтром `auth` (находится в файле `app/filters.php`), который проверяет выполнил ли пользователь вход 
и, если нет, запишет в сессию, адрес текущей страницы и перенаправит пользователя на страницу входа. После входа пользователя перенаправит обратно на данный адрес.
 Для этого используется метод `Redirect::intended($path)` (см. `AuthController@postLogin`), который в случае существования в сессии intended страницы, 
 перенаправит на неё или на $path в другом случае.
