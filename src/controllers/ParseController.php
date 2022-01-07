<?php

/**
 * @package   yii2-datecontrol
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2021
 * @version   1.9.8
 */

namespace antonyz89\togglecolumn\controllers;

use antonyz89\togglecolumn\ToggleColumnAction;
use yii\web\Controller;

/**
 * ParseController class manages the actions for date conversion via ajax from display to save.
 */
class ParseController extends Controller
{
    /**
     * Convert display date for saving to model.
     *
     * @return array
     */
    public function actionSave()
    {
        $action = new ToggleColumnAction('save', $this);

        return $action->run();
    }
}
