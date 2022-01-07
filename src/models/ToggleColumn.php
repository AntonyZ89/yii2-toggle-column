<?php

namespace antonyz89\togglecolumn\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class ToggleColumn extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%toggle_column}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //columns
            ['columns', 'required'],
            ['columns', 'string'],
            //table
            ['table', 'required'],
            ['table', 'string', 'max' => 255],
            //user_id
            ['user_id', 'required'],
            ['user_id', 'integer'],
            //created_at
            ['created_at', 'integer'],
            //updated_at
            ['updated_at', 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     * @return ToggleColumnQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ToggleColumnQuery(get_called_class());
    }
}
