<?php

namespace antonyz89\togglecolumn\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[ToggleColumn]].
 *
 * @see ToggleColumn
 */
class ToggleColumnQuery extends ActiveQuery
{

    /**
     * {@inheritdoc}
     * @return ToggleColumn[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ToggleColumn|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param int $user_id
     * @param string $operator default `=`
     * @return ToggleColumnQuery
     */
    public function whereUser($user_id, $operator = '=')
    {
        return $this->andWhere([
            $operator, sprintf('%s.user_id', ToggleColumn::tableName()), $user_id
        ]);
    }

    /**
     * @param int $table
     * @param string $operator default `=`
     * @return ToggleColumnQuery
     */
    public function whereTable($table, $operator = '=')
    {
        return $this->andWhere([
            $operator, sprintf('%s.table', ToggleColumn::tableName()), $table
        ]);
    }
}
