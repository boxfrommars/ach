## Homestead

Для начала разберёмся с [Vagrant](http://www.vagrantup.com/):
Vagrant — утилита, позволяющая создавать виртуальную машину и настраивать её автоматически так, как вы это указали. 
Из коробки Vagrant работает с VirtualBox, хотя есть поддержка и других решений. Всё, что вам потребуется для того, 
чтобы развернуть окружение на компьютере — это выполнить одну команду: `vagrunt up`

Одно из главных понятий в Vagrant, это box. Box — это архив, который содержит в себе образ виртуальной машины и файл 
с настройками для самого Vagrant.

Подробнее о vagrant: http://habrahabr.ru/post/178797/

Homestead как раз и является таким боксом, предоставляющим нам установленные 

* Ubuntu 14.04
* PHP 5.5
* Nginx
* MySQL
* Postgres
* Node (+ Bower, Grunt и Gulp)
* Redis 
* Memcached
* Beanstalkd
* Laravel Envoy
* Fabric + HipChat Extension

## Установка Homestead

### Устанавливаем Vagrant и VirtualBox, добавляем бокс Homestead

https://www.virtualbox.org/wiki/Downloads
http://www.vagrantup.com/downloads.html

Устанавливаем по порядку, после установки вагранта установщик попросит перезагрузить систему. Во время перезагрузки
войдите в BIOS и проверьте, включена ли у вас Intel Virtualization Technology (VT-x, AMD-V), если нет, то включите

После загрузки в консоли выполняем (если вы работаете в Windows, то я советую воспользоваться git bash консолью, она нам ещё понадобится):

    vagrant box add laravel/homestead

Дальше клонируем репозиторий `homestead`. Документация советует клонировать в папку, где хранятся 
все ваши проекты. (например C:/Users/YourName/Workspace) 
клонируем:

    git clone https://github.com/laravel/homestead.git
    
открываем файл homestead/Homestead.yaml

```yaml
---
ip: "192.168.10.10" # ip вашей будущей виртуальной машины
memory: 2048        # количество выделяемой для неё памяти
cpus: 1             # количество используемых ею процессоров

authorize: /Users/me/.ssh/id_rsa.pub # путь к публичному ключу

keys:
    - /Users/me/.ssh/id_rsa # путь к ключу

folders:                        # папки которые будут синхронизироваться между гостевой и виртуальной машинами
    - map: /Users/me/Code       # Какая папка гостевой (в вашей текущей) машины будет синхронизироваться с виртуальной
      to: /home/vagrant/Code    # какой путь к этой папке будет в виртуальной системе 

sites:                                      # список сайтов, которые автоматически настроятся при инициализации (!) системы
    - map: homestead.app                    # адрес по которому будет доступен сайт
      to: /home/vagrant/Code/Laravel/public # директория, в которой содержится точка входа (index.php) 
```

если у вас нет ssh-ключей создайте их с помощью 
    
    ssh-keygen -t rsa -C "your@email.com"

Если вы работаете на Windows, то `ssh-keygen` доступна из Git Bash
после того как ключи сгенерируются нужно указать их в Homestead.yaml

Для Windows Homestead.yaml будет выглядеть примерно так:

```yaml
---
ip: "192.168.10.10"
memory: 2048
cpus: 1

authorize: C:\Users\YourName\.ssh\id_rsa.pub

keys:
    - C:\Users\YourName\.ssh\id_rsa

folders:
    - map: C:\Users\YourName\Workspace
      to: /home/vagrant/Workspace

sites:
    - map: test.dev
      to: /home/vagrant/Workspace/test.dev/public
    - map: ach.dev
      to: /home/vagrant/Workspace/ach.dev/public
```

Добавьте в файл `hosts` гостевой машины соответствующие строки 
(для Windows: `C:\Windows\System32\drivers\etc\hosts`, для Mac и Linux `/etc/hosts`)
 
```
127.0.0.1 test.dev
127.0.0.1 ach.dev
```

на этом настройка завершена, тут стоит отметить, что настраивать систему вам придётся один раз, 
в дальнейшей работе вам не придётся повторять эти шаги. 
 
запускаем, выполнив `vagrant up` в директории homestead

    vagrant up
    
После того, как vagrant инициализирует и запустит виртуальную машину, вы можете проверить её работу, 
создав файл C:\Users\YourName\Workspace\test.dev\public\index.php с содержимым `<?php phpinfo();` и перейдя на http://test.dev:8000

> Следующие порты перенаправляются к вашей виртуальной машине
> * SSH: 2222 -> 22
> * HTTP: 8000 -> 80
> * MySQL: 33060 -> 3306
> * Postgres: 54320 -> 5432
> то есть вы можете подключиться, например, к mysql 
> с клиентской машины так: `mysql -u homestead -p -P 33060 -h 127.0.0.1`, 
> логины и пароли для postgresql и mysql -- как vagrant/secret так и root/secret
> Если вы подключаетесь к бд изнутри вашей виртуальной машины, то используйте стандартные порты

Зайти на виртуальную машину можно выполнив:
    vagrant ssh
или
    ssh vagrant@127.0.0.1 -p 2222

> все команды vagrant необходимо выполнять из директории homestead

Для добавления новых сайтов есть два способа:
1. Добавить в Homestead.yaml новый сайт 
```yaml
sites:
    - map: test.dev
      to: /home/vagrant/Workspace/test.dev/public
    - map: new.dev
      to: /home/vagrant/Workspace/new.dev/public
    - map: ach.dev
      to: /home/vagrant/Workspace/ach.dev/public
```
и выполнить
```bash
vagrant provisio
```
2. Зайти на виртуальную машину и воспользоваться командой serve
```php
vagrant ssh
serve new.dev /home/vagrant/Workspace/new.dev/public
```

> После добавления любым из этих способов, не забудьте обновить файл `hosts`: `127.0.0.1 new.dev`
