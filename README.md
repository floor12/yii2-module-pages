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

### Активируем компонент MetaMaster

```
   'components' => [
        'metamaster' => [
            'class' => 'floor12\metamaster\MetaMaster',
            'siteName' => 'Your site name',
            'defaultImage' => '/design/export_logo.png',
        ],
```
`defaultImage` - путь к дефолтной картинке для Open Graph мета-тегов.
`siteName` - название сайта для Open Graph мета-тегов.

###Добавляем блок для роутинга

```
'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/<path:[\w_\/-]+>.html' => '/pages/page/view',
                '/sitemap.xml' => '/site/sitemap',
            ],
        ],
```

Использование
-----
@todo

REST API
-----

Модуль содержит контроллер `floor12\pages\controllers\ApiController`, позволяющий управлять страницами
и привязанными к ним файлами удалённо (например, из внешнего скрипта или AI-агента) без доступа к БД
напрямую и без привязки к реальному пользователю приложения-хоста.

### Настройка

Токены задаются в конфиге модуля. Значение по умолчанию - пустой массив, при котором API полностью
выключено (любой запрос вернёт 401).

```php
'modules' => [
    'pages' => [
        'class' => 'floor12\pages\Module',
        'editRole' => '@',
        'apiTokens' => array_filter(explode(',', getenv('PAGES_API_TOKENS') ?: '')),
        // 'apiUserId' => 1, // необязательно: id пользователя для create_user_id/update_user_id
    ],
],
```

Авторизация запроса - заголовок `Authorization: Bearer <token>`, токен должен совпадать с одним
из значений `apiTokens`.

### Эндпоинты

Базовый путь - `/pages/api`.

| Метод | Роут | Описание |
|---|---|---|
| GET | `/pages/api/structure` | языки, дерево страниц, зарегистрированные компоненты (`actionsIndex`/`actionsView`) |
| GET | `/pages/api/pages` | список страниц (query: `lang`, `parent_id`, `status`) |
| GET | `/pages/api/page?id=` | карточка страницы |
| POST | `/pages/api/page` | создание страницы (JSON body = поля модели `Page`) |
| POST | `/pages/api/page-update?id=` | обновление страницы (JSON body = изменяемые поля) |
| DELETE | `/pages/api/page?id=` | удаление страницы |
| POST | `/pages/api/page-move?id=&mode=0\|1` | изменение порядка (0 - вверх, 1 - вниз) |
| GET | `/pages/api/files?page_id=` | список файлов, привязанных к странице |
| POST | `/pages/api/file-upload?page_id=&attribute=banner\|images\|files` | загрузка файла (multipart, поле `file`) и привязка к странице |
| DELETE | `/pages/api/file?id=` | удаление файла |

### Примеры

```bash
TOKEN="your-token"
BASE="https://example.com/pages/api"

# структура сайта
curl -s -H "Authorization: Bearer $TOKEN" "$BASE/structure"

# карточка страницы
curl -s -H "Authorization: Bearer $TOKEN" "$BASE/page?id=12"

# создание страницы
curl -s -X POST -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"parent_id":0,"lang":"ru","key":"about","title_menu":"О нас","title_seo":"О компании","content":"<p>Текст</p>"}' \
  "$BASE/page"

# обновление контента
curl -s -X POST -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"content":"<p>Новый текст</p>"}' \
  "$BASE/page-update?id=12"

# удаление страницы
curl -s -X DELETE -H "Authorization: Bearer $TOKEN" "$BASE/page?id=12"

# загрузка изображения в галерею страницы
curl -s -X POST -H "Authorization: Bearer $TOKEN" \
  -F "file=@/path/to/image.jpg" \
  "$BASE/file-upload?page_id=12&attribute=images"
```

