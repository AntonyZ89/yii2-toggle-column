<?php

namespace antonyz89\togglecolumn;

use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\Response;

class ToggleColumnAction extends Action
{
    /**
     * @var ActiveQuery
     */
    public $query;

    /**
     * @var string
     */
    public $modelClass = '\antonyz89\togglecolumn\models\ToggleColumn';

    /**
     * @var int
     */
    public $user_id;

    /**
     * @var string
     */
    public $table;

    /**
     * @var string[]
     */
    public $columns;

    /**
     * @var ActiveRecord
     */
    protected $_model;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->user_id = $this->user_id ?: Yii::$app->user->id;

        $this->table = $this->table ?: Yii::$app->request->post('table');

        $this->columns = $this->columns ?: Yii::$app->request->post('columns');

        $this->query = $this->query ?: $this->modelClass::find()->whereUser($this->user_id)->whereTable($this->table);

        $this->_model = $this->query->one();

        if (!$this->_model) {
            if(!isset($this->user_id)) {
                throw new InvalidConfigException('The "user_id" property must be set.');
            }

            if(!isset($this->table)) {
                throw new InvalidConfigException('The "table" property must be set.');
            }

            $this->_model = new $this->modelClass;
            $this->_model->user_id = $this->user_id;
            $this->_model->table = $this->table;
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $transaction = Yii::$app->db->beginTransaction();

        try {

            $this->_model->columns = json_encode($this->columns);

            if ($this->_model->save()) {
                $transaction->commit();
                return ['result' => true];
            }

            throw new Exception('Failed to save ToggleColumn. ' . json_encode($this->_model->errors));
        } catch (Exception $e) {
            $transaction->rollBack();
            return ['result' => false, 'error' => $e->getMessage()];
        }

        return ['result' => false];
    }
}
