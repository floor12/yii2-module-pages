# yii2-module-pages

Модуль позволяет:
 - организовать древовидную структуру страниц
 - по-умолчанию страница содержит обычный текст, но может быть ссылаться на любые контроллеры
 - организовать sitemap.xml
 - для всех страниц организовать OpenGraph мета-теги
 - строить хлебные крошки с валидной микроразметкой
 - организовать редактирование структуры и контента страниц непосредственно на фронтенде в модальном окне


Установка
------------

#### Ставим модуль

Выполняем команду
```bash
$ composer require floor12/yii2-module-pages
```

иди добавляем в секцию "requred" файла composer.json
```json
"floor12/yii2-module-pages": "dev-master"
```


###Выполняем миграцию для созданию необходимых таблиц
```bash
$ ./yii migrate --migrationPath=@vendor/floor12/yii2-module-pages/src/migrations
```

###Добавляем модуль в конфиг приложения
```php  
'modules' => [
        'pages' => [
            'class' => 'floor12\pages\Module',
            'editRole' => '@',
        ],
    ]
    ...
```

Параметры:

1. `editRole` - роль пользователей, которым доступно управление. Можно использовать "@".

### Активируем компонент OpenGraph

```
   'components' => [
        'opengraph' => [
            'class' => 'floor12\opengraph\OpenGraph',
        ],
```

###Добавляем блок для роутинга

```
'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/<controller>/<action>' => '/<controller>/<action>',
                '/<module>/<controller>/<action>' => '<module>/<controller>/<action>',
                '/sitemap.xml' => '/site/sitemap',
                '/<path:[\w_\/-]+>' => '/pages/page/view',
            ],
        ],
```

Смысл в том, что стандартные роуты тоже необходимо внести в конфиг, иначе они перестанут работать, 
так как все будет перенаправляться на `/pages/page/view`, а этого нам не надо.

Использование
-----
@todo

