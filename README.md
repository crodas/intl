crodas/intl
===========

Easy internationalization support for PHP

It aims to be a drop-in replacement for `gettext`.

Setup
-----

It can be installed with composer

```bash
composer require crodas/intl:dev-master
```

Concepts
--------

`crodas/intl` at run time provides a few functions (`__`, `_e` and `_` *if gettext in not installed*) to translate text. It also provides a **generator** that walks throughtout the project and extract *all* texts it can find and generates a `template` file.

That `template` needs to be copied in order to create new `locales` or `languages`. You can run this process as many time as you'd like, the `template` file and *all* `locale` files are going to be updated (but we never override its content).

Whenever you update your locale files you would need to **compile** it in order to load efficiently from your PHP app.

TODO: *add demos*


Using
-----

In order to use `crodas/intl`, it needs to be initialized as follows:

```php
require "vendor/autoload.php";

crodas\Intl::init("/tmp/language.php", $locale);
```

It takes two arguments, the first is the compiled locale and the second is the language to use.

It is possible to switch to another locale at any time by doing:

```php
crodas\Intl::setLanguage($locale);
```

Then you can call to `__("Hello")` and `__("Hello %s, welcome")`, they will be replaced with the correct locale or the default content will be print if we can't the locale file or the sentence to translate, pretty much like `gettext` works.
