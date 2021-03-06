# LESS Компилятор для Bitrix
![version](https://img.shields.io/badge/version-2.1.0-brightgreen.svg?style=flat-square "Version")
![MIT License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)

Простой и удобный компонент, реализующий компиляцию LESS файлов.

:exclamation: **Компонент не подключает CSS к шаблону, а только компилирует LESS файлы.** Это сделано специально для более гибкого управления подключением css-файлов.

## Преимущества
- Быстрая работа.
- Автоматическая компиляция только изменённых файлов.
- Генерация SourceMap.
- Минификация CSS-кода.
- Управление доступом к компиляции.

## Установка

### Шаг 1
#### a)
Компонент очень удобно устанавливать через composer:
```bash
composer require pafnuty/less-for-bitrix
```
#### b)
Но можно и вручную, для этого нужно положить файлы и папаки из репозитория в папку `/bitrix/modules/cn.less`. 

### Шаг 2
В админке перейти в раздел `/bitrix/admin/partner_modules.php` и выполнить установку решения **LESS Компилятор (cn.less)**.

## Использование
В нужном месте шаблона прописать вызов компонента:
```php
<?$APPLICATION->IncludeComponent(
    "codenails:cn.less", 
    "", 
    array(),
    false
);?>
```

Так же можно выбрать нужный компонент при редактировании страницы:

![cn.less](https://dl.dropboxusercontent.com/u/8142395/bitrix/cn.less.png "LESS Компилятор (cn.less)")

При необходимости можно настроить параметры.

По умолчанию компонент будет искать файл `SITE_TEMPLATE_PATH/less/template_styles.less` и положит скомпилированный `template_styles.css` в папку с текущим шаблоном сайта.

Не забывайте прописать в шаблон подключение CSS-файла, если настройки отличаются от стандартных:
```php
<?\Bitrix\Main\Page\Asset::getInstance()->addCss('/local/assets/css/compiled_file.css');?>
```

## Известные ошибки и недоработки
- При изменении параметров компонента нужно либо удалять папку `less_cache` и скомпилированные файлы (`.css` и `.map`), либо пересохранить один из less файлов, чтобы произошла перекомпляция.
- Для корректной работы SourseMap необходимо отключать минификацию CSS файла.

## Вопросы и поддержка
Если у вас возник вопрос, или есть пожелания к улучшению компонента — [воспользуйтесь формой](https://github.com/pafnuty/LessForBitrix/issues)

## Куда делась старая "примочка"?
- Живёт в ветке [old](https://github.com/pafnuty/LessForBitrix/tree/old) и её развитие не планируется.
