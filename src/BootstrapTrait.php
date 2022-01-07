<?php

namespace antonyz89\togglecolumn;

use Yii;
use yii\helpers\ArrayHelper;

trait BootstrapTrait {

    protected $_defaultBtnCss;
    protected $_bsVer;

    /**
     * Validate Bootstrap version
     * @param  int  $ver
     * @return bool
     * @throws Exception
     */
    public function isBs($ver)
    {
        return $this->getBsVer() === $ver;
    }

    /**
     * Get bootstrap version
     * @return int
     * @throws Exception
     */
    public function getBsVer()
    {
        if (empty($this->_bsVer)) {
            $this->configureBsVersion();
        }

        return $this->_bsVer;
    }

    /**
     * Configures the bootstrap version settings
     * @return int the bootstrap lib parsed version number (defaults to 3)
     * @throws Exception
     */
    protected function configureBsVersion()
    {
        $v = empty($this->bsVersion) ? ArrayHelper::getValue(Yii::$app->params, 'bsVersion', '3') :
        $this->bsVersion;
        $this->_bsVer = static::parseVer($v);

        return $this->_bsVer;
    }

    /**
     * Parses and returns the major BS version
     * @param  string  $ver
     * @return int
     */
    protected static function parseVer($ver)
    {
        $ver = substr(trim((string)$ver), 0, 1);

        return is_numeric($ver) ? (int)$ver : 3;
    }


    /**
     * Gets the default button CSS
     * @return string
     */
    public function getDefaultBtnCss()
    {
        return $this->_defaultBtnCss;
    }
}
