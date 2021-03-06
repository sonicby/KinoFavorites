KinoFavorites
=============

Сервис позволяет найти и добавить в базу любимых артистов, для удобного ведения списка фильмов, в которых они участвовали. Имеется возможность пометить фильм как "просмотренный" или спрятать его из общего списка.

###Скриншоты:
![Screenshot1](http://korphome.ru/kino_favorites/screenshots/Screenshot1.png "Screenshot1")
![Screenshot2](http://korphome.ru/kino_favorites/screenshots/Screenshot2.png "Screenshot2")

###Требования для установки:

* Веб-сервер (Apache, nginx, lighttpd)
* PHP (5.2 или выше) с поддержкой cURL и PDO
* MySQL

###Установка:

* Импортировать дамп базы kinofavorites.sql
* Перенести все файлы в папку на вашем сервере (например /path/to/folder/kinofavorites/)
* Внести изменения в config.php и указать данные для доступа к БД
* Добавить в cron engine.php

```
*/60 * * * * php -q /path/to/folder/kinofavorites/engine.php >> /path/to/log/kinofavorites_error.log 2>&1
```
###Настройки:

Так же, в php.ini (для CLI) необходимо изменить следующие параметры:

```
; увеличить максимальное вермя выполнения скрипта
max_execution_time = 300

; указать date.timezone
date.timezone = Europe/Moscow

; эту опцию желательно включить в php.ini как для CLI, так и для веб-сервера
allow_url_fopen = on
```
