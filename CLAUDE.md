# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Что это за проект

`floor12/yii2-module-pages` — Yii2-модуль (composer-пакет `type: yii2-extension`, не самостоятельное приложение). Организует древовидную структуру страниц сайта: sitemap.xml, breadcrumbs, OpenGraph-мета-теги, редактирование контента прямо на фронтенде в модальном окне. Устанавливается как зависимость в другое Yii2-приложение через `composer require`, поэтому в самом репозитории нет `index.php`, конфигов приложения или `runtime/`.

Нет тестов, CI-конфигурации и линтера в этом репозитории — только PHP source и статические ассеты. Установка/сборка выполняется исключительно в контексте приложения-хоста.

## Команды

- Установка миграций в приложении-хосте: `./yii migrate --migrationPath=@vendor/floor12/yii2-module-pages/src/migrations`
- Сборка SCSS → CSS (Sass, см. `src/assets/pages.scss` → `pages.css`/`pages.css.map`). Согласно глобальным правилам, Node-инструменты запускать только в Docker-контейнере, не на хосте.

## Архитектура

### Точка входа: `Module.php`

`floor12\pages\Module` — корень модуля. Ключевые конфигурируемые свойства, которые приложение-хост переопределяет в конфиге:
- `pageModel` — класс модели страницы (по умолчанию `models\Page`), можно подменить своим наследником.
- `userModel` — класс модели пользователя приложения-хоста (используется в FK-связях `creator`/`updator`).
- `editRole` — RBAC-роль (или `'@'` для любого авторизованного) для доступа к редактированию; проверяется через `Module::adminMode()`.
- `view` / `viewForm` — переопределяемые пути к view-файлам рендеринга страницы и формы редактирования.
- `layout` / `layoutAdmin` — layout для фронтенда и админки.
- `actionsIndex` / `actionsView` — расширение экшенов.

### Модель Page — "супер-страница" с полиморфным поведением

`models\Page` (ActiveRecord, таблица `page`) — не просто CRUD-модель. Ключевые механики:

- **Дерево страниц**: `parent_id`/`norder`, `path` — денормализованный полный URL-путь, пересчитывается рекурсивно во всех потомках при каждом сохранении (`logic\PageUpdate::updatePath()`).
- **Три режима поведения страницы** (проверяются в этом порядке в `PageController::actionView`):
  1. `link` заполнен → редирект на внешний/внутренний URL (`isLink()`).
  2. `index_action` заполнен ("Главный компонент", формат `Controller::action`) → вместо рендера `content` вызывается произвольный экшен произвольного контроллера приложения-хоста, страница используется как "роутер" на существующий функционал (листинги, формы и т.п.). Параметры экшена берутся через reflection/аннотации (`components\Annotations`, `getPageParams()`) и подставляются из `page_params` (JSON).
  3. Иначе — обычный рендер `content` через `Module::$view`.
  Аналогично `view_action` ("Дополнительный компонент") подключается для последнего сегмента URL, не совпавшего ни с одной страницей (паттерн вида `/раздел/сущность-42.html` — `раздел` это Page, `сущность-42` обрабатывает `view_action`).
- **История URL**: `PageUrl` — лог всех путей, когда-либо занимаемых страницей (пишется в `Page::afterSave()`). При 404 `PageController::checkUrlLogOrThrow()` ищет старый путь в этом логе и делает 301-редирект — не ломать это поведение при рефакторинге routing-логики.
- **Кэширование**: список страниц по `path`/`lang` кэшируется с `TagDependency` по тегу `Page::CACHE_TAG_NAME`; инвалидируется в `PageUpdate::execute()` при любом сохранении. Sitemap кэшируется отдельно в `Yii::$app->cache` под ключом `'sitemap'` на 300 сек (`components\SitemapWidget`).
- **Мультиязычность**: `lang` — часть уникальности `path`/`norder`, страницы разных языков — независимые деревья.
- **Инлайн-плейсхолдеры в content**: `PageController::parseWidgets()` постобрабатывает HTML контента, заменяя `{{map:KEY}}`, `{{googlemap:URL}}`, `{{openmap:ID}}`, `{{fontawesome:icon}}` на виджеты/iframe, плюс прогоняет через `ContentPicture` и `YoutubeProcessor`.

### Слой `logic/` — операции, не помещающиеся в ActiveRecord

Паттерн: класс с конструктором, принимающим модель+данные+контекст, и методом `execute()`. Используется вместо "толстых" контроллеров/моделей:
- `PageUpdate` — сохранение страницы (проставление `created`/`updated`/user_id, пересчёт `path` по дереву, инвалидация кэша).
- `PageOrderChanger` — перестановка `norder` (drag-n-drop / кнопки вверх-вниз), полный `reorder()` пересчёт после каждой операции.
- `PageBreadcrumbs` — рекурсивная сборка хлебных крошек от текущей страницы до корня с микроразметкой.

Эти классы создаются через `Yii::createObject(...)`, а не `new`, — при добавлении зависимостей используй DI-контейнер, а не хардкодь конструктор.

### Контроллеры

- `PageController` — публичная часть: `actionView` (роутинг страниц, см. выше), `actionMove`, `actionImageupload` (загрузка картинок Summernote), плюс `form`/`delete` экшены из пакета `floor12/yii2-editmodal` (модальное редактирование на фронтенде).
- `AdminController` — админка: `IndexAction`/`EditModalAction`/`DeleteAction` из `floor12/yii2-editmodal`, `actionSort` (bulk-обновление порядка по JSON из tree-grid), `actionMakeMeta`/`actionMakeContent` — интеграция с OpenAI через `components\GptHelper` (генерация мета-тегов и контента по промпту).

### Внешние зависимости пакета

Модуль сильно завязан на другие пакеты `floor12/*` (см. `composer.json`): `yii2-editmodal` (модальные CRUD-экшены), `yii2-metamaster` (OpenGraph/SEO мета-теги, регистрируется в `PageController::actionView`), `yii2-summernote` (WYSIWYG), `yii2-text-counter-widget`, `yii2-youtube-async-widget`, а также `leandrogehlen/yii2-treegrid` (древовидная админка) и `openai-php/client` (GPT-хелпер). При навигации по коду большая часть "магии" (виджеты, экшены) находится не в этом репозитории, а в этих зависимостях — искать их в `vendor/floor12/*`.

### Ассеты (`src/assets/`)

`PagesAsset` (AssetBundle) регистрирует `pages.css` + `autosubmit.js`/`pages.js`, зависит от `EditModalAsset` и `JuiAsset`. Исходник стилей — `pages.scss`, компилируется в `pages.css`/`pages.css.map` (sass-cache в `.sass-cache/`, не коммитить).
