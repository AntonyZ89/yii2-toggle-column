<?php

/**
 * @package   yii2-toggle-column
 * @author    Antony Gabriel <antonyz.dev@gmail.com>
 * @version   1.0.0
 */

namespace antonyz89\togglecolumn;

use yii\base\Module as BaseModule;

/**
 * Toggle Column module for Yii Framework 2.0.
 *
 * @author Antony Gabriel <antonyz.dev@gmail.com>
 * @version 1.0.0
 */
class Module extends BaseModule
{
    /**
     * Current module name.
     */
    const MODULE = 'toggle-column';

    /**
     * @var string|array the route/action to save the toggle column settings.
     */
    public $saveAction = ['/toggle-column/parse/save'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
}
