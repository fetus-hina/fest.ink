<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "team".
 *
 * @property integer $fest_id
 * @property integer $color_id
 * @property string $name
 * @property string $keyword
 *
 * @property Color $color
 * @property Fest $fest
 */
class Team extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'team';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fest_id', 'color_id', 'name', 'keyword'], 'required'],
            [['fest_id', 'color_id'], 'integer'],
            [['name', 'keyword'], 'string'],
            [['fest_id', 'color_id'], 'unique', 'targetAttribute' => ['fest_id', 'color_id'],
                'message' => 'The combination of Fest ID and Color ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fest_id' => 'Fest ID',
            'color_id' => 'Color ID',
            'name' => 'Name',
            'keyword' => 'Keyword',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColor()
    {
        return $this->hasOne(Color::className(), ['id' => 'color_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFest()
    {
        return $this->hasOne(Fest::className(), ['id' => 'fest_id']);
    }
}
