---
name: pages-cms
description: Удалённое управление контентом сайтов, построенных на floor12/yii2-module-pages (страницы, дерево разделов, файлы), через REST API модуля (controllers/ApiController) по Bearer-токену. Используй, когда пользователь просит отредактировать/создать/удалить страницу, посмотреть структуру сайта или загрузить файл на конкретном сайте (по имени сайта), не давая прямого доступа к серверу/БД.
---

# pages-cms

Скилл даёт curl-рецепты для REST API модуля `floor12/yii2-module-pages` (см. `src/controllers/ApiController.php` и `README.md` в этом репозитории — это канонический источник истины по эндпоинтам; если они когда-то разойдутся, править нужно оба места одним коммитом).

API даёт доступ только к сущностям модуля pages (страницы + привязанные файлы) по токену — не полный доступ к БД сайта.

## 1. Credentials сайта

Токены и базовые URL сайтов хранятся **локально, вне git**, в файле:

```
~/.claude/pages-api-sites.json
```

Формат:
```json
{
  "tensa-web": {"baseUrl": "https://tensa-web.example.com/pages/api", "token": "..."},
  "aratashenwinery": {"baseUrl": "https://aratashenwinery.example.com/pages/api", "token": "..."}
}
```

Если пользователь называет сайт, которого нет в этом файле — спроси baseUrl и токен и предложи добавить запись
(токен пользователь генерирует сам и прописывает в конфиге хоста, `modules.pages.apiTokens`, см. README модуля).
Не выводи содержимое токенов в чат без необходимости.

Перед запросами прочитай нужную запись, например:
```bash
jq -r '.["<site>"]' ~/.claude/pages-api-sites.json
```

## 2. Общие правила

- Все запросы — `Authorization: Bearer $TOKEN`.
- Тело запроса для create/update — строго JSON, заголовок `Content-Type: application/json`.
- Перед `page-update` сначала сделай `GET /page?id=`, покажи пользователю текущее состояние при неочевидных изменениях — обновление частичное (меняются только переданные ключи), но так проще не потерять контекст.
- Перед `DELETE /page` — всегда подтверди у пользователя, что именно эту страницу нужно удалить (токен даёт полный CRUD, удаление необратимо на уровне API).
- Ошибки возвращаются как HTTP-статус + JSON `{"name":..., "message":...}` (стандартный формат Yii2 `yii\web\HttpException`).

## 3. Эндпоинты

Базовый путь: `$BASE` (напр. `https://site.com/pages/api`).

| Метод | Путь | Назначение |
|---|---|---|
| GET | `$BASE/structure` | языки, дерево страниц, список компонентов (`index_actions`/`view_actions`) |
| GET | `$BASE/pages?lang=&parent_id=&status=` | плоский список страниц с фильтрами |
| GET | `$BASE/page?id=` | карточка страницы (все поля + `url`) |
| POST | `$BASE/page` | создать страницу (JSON body) |
| POST | `$BASE/page-update?id=` | обновить страницу (JSON body, частично) |
| DELETE | `$BASE/page?id=` | удалить страницу |
| POST | `$BASE/page-move?id=&mode=0\|1` | переставить среди соседей (0=вверх,1=вниз) |
| GET | `$BASE/files?page_id=` | файлы, привязанные к странице |
| POST | `$BASE/file-upload?page_id=&attribute=banner\|images\|files` | загрузить файл (multipart, поле `file`) и привязать |
| DELETE | `$BASE/file?id=` | удалить файл |

Поля модели `Page`, доступные в теле запроса `page`/`page-update`: `parent_id, lang, status, menu, key, title, title_menu,
title_seo, description_seo, keywords_seo, content, announce, link, index_action, view_action, page_params, layout,
use_purifier, menu_css_class`. (`id/path/created/updated/norder` — вычисляются модулем, не задаются напрямую.)

## 4. Готовые команды

```bash
TOKEN="..."; BASE="https://site.com/pages/api"

# структура сайта (языки + дерево + компоненты)
curl -s -H "Authorization: Bearer $TOKEN" "$BASE/structure" | jq

# список страниц конкретного раздела на конкретном языке
curl -s -H "Authorization: Bearer $TOKEN" "$BASE/pages?lang=ru&parent_id=3" | jq

# карточка страницы
curl -s -H "Authorization: Bearer $TOKEN" "$BASE/page?id=12" | jq

# создать страницу
curl -s -X POST -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"parent_id":0,"lang":"ru","key":"about","title_menu":"О нас","title_seo":"О компании","content":"<p>Текст</p>"}' \
  "$BASE/page" | jq

# обновить только контент
curl -s -X POST -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"content":"<p>Новый текст</p>"}' \
  "$BASE/page-update?id=12" | jq

# обновить SEO-мета
curl -s -X POST -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"title_seo":"Новый title","description_seo":"Новое описание"}' \
  "$BASE/page-update?id=12" | jq

# удалить страницу (подтверди у пользователя перед вызовом!)
curl -s -X DELETE -H "Authorization: Bearer $TOKEN" "$BASE/page?id=12" | jq

# переставить страницу вверх среди соседей
curl -s -X POST -H "Authorization: Bearer $TOKEN" "$BASE/page-move?id=12&mode=0" | jq

# файлы страницы
curl -s -H "Authorization: Bearer $TOKEN" "$BASE/files?page_id=12" | jq

# загрузить картинку в галерею страницы
curl -s -X POST -H "Authorization: Bearer $TOKEN" \
  -F "file=@/local/path/photo.jpg" \
  "$BASE/file-upload?page_id=12&attribute=images" | jq

# загрузить обложку страницы
curl -s -X POST -H "Authorization: Bearer $TOKEN" \
  -F "file=@/local/path/cover.jpg" \
  "$BASE/file-upload?page_id=12&attribute=banner" | jq

# удалить файл
curl -s -X DELETE -H "Authorization: Bearer $TOKEN" "$BASE/file?id=45" | jq
```
