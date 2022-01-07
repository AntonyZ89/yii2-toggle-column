<?php

namespace antonyz89\togglecolumn;

use Yii;
use yii\base\InvalidConfigException;

trait ConfigTrait {

    /**
     * @var Module
     */
    protected $_module;

    /**
     * Initializes and validates the module.
     *
     * @param string $class the Module class name
     *
     * @return \yii\base\Module
     *
     * @throws InvalidConfigException
     */
    public function initModule($class)
    {
        $m = $class::MODULE;
        $module = $m ? static::getModule($m) : null;

        if ($module === null || !$module instanceof $class) {
            throw new InvalidConfigException("The '{$m}' module MUST be setup in your Yii configuration file and must be an instance of '{$class}'.");
        }

        $this->_module = $module;
    }

    /**
     * Gets the module instance by validating the module name. The check is first done for a submodule of the same name
     * and then the check is done for the module within the current Yii2 application.
     *
     * @param string $m the module identifier
     * @param string $class the module class name
     *
     * @throws InvalidConfigException
     *
     * @return yii\base\Module
     */
    public static function getModule($m, $class = '')
    {
        $app = Yii::$app;
        $mod = isset($app->controller) && $app->controller->module ? $app->controller->module : null;
        $module = null;
        if ($mod) {
            $module = $mod->id === $m ? $mod : $mod->getModule($m);
        }
        if (!$module) {
            $module = $app->getModule($m);
        }
        if ($module === null) {
            throw new InvalidConfigException("The '{$m}' module MUST be setup in your Yii configuration file.");
        }
        if (!empty($class) && !$module instanceof $class) {
            throw new InvalidConfigException("The '{$m}' module MUST be an instance of '{$class}'.");
        }
        return $module;
    }
}
