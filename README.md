yii2-toggle-column
============

<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=YATHVT293SXDL&source=url">
  <img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="Donate with PayPal" />
</a>

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist antonyz89/yii2-toggle-column dev-main
composer require antonyz89/yii2-toggle-column dev-main
```

or add

```
"antonyz89/yii2-toggle-column": "dev-main"
```

to the require section of your `composer.json` file.


USAGE
---------

⚠️ In some cases you may need to add `/toggle-column/parse/save` to your `urlManager`.

**common/config/main.php**

```php
use antonyz89\togglecolumn\Module as ToggleColumnModule;
use yii\i18n\PhpMessageSource;

return [
    // ...
    'modules' => [
        'toggle-column' => [
            'class' => ToggleColumnModule::class,
        ],
    ],
    'i18n' => [
        'translations' => [
            'tc' => [ // Toggle Column
                'class' => PhpMessageSource::class,
                'basePath' => '@antonyz89/togglecolumn/messages',
            ],
        ],
    ],
    // ...
];
```
After you downloaded, the last thing you need to do is updating your database schema by applying the migration:

```bash
$ php yii migrate/up --migrationPath=@antonyz89/togglecolumns/migrations
```
or copy the file `migrations/m220105_225647_create_toggle_column_table.php` to your `console/migrations` directory.

**Example**
---


```php
<?php

$columns = [
    'code',
    [
        'attribute' => 'partner_id',
        'value' => 'partner',
        'visible' => false // will not be displayed in ToggleColumn widget either
    ],
    [
        'attribute' => 'customer',
    ],
    [
        'attribute' => 'status',
        'value' => 'statusAsText'
    ],
];

?>

<?=
GridView::widget([
    // ...
    'toolbar' => [
        ToggleColumn::widget([
            'model' => Reserve::class,
            'columns' => $columns,
        ]),
    ],
    // ...
])
?>
```
